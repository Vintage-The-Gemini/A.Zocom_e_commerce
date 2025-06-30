<?php
// admin/products.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Products Management';

// Get active category from URL parameter
$active_category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $delete_query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
}

// Categories with icons and counts
$categories = [
    'Total Products' => ['boxes', 0],
    'Foot Protection' => ['shoe-prints', 0],
    'Head Protection' => ['hard-hat', 0],
    'Hand Protection' => ['hand-paper', 0],
    'Eye Protection' => ['glasses', 0],
    'Respiratory Protection' => ['head-side-mask', 0],
    'Fall Protection' => ['link', 0],
    'Body Protection' => ['tshirt', 0],
    'Fabrics' => ['scroll', 0]
];

// Get category counts
foreach ($categories as $category => $data) {
    if ($category === 'Total Products') {
        $count_query = "SELECT COUNT(*) as count FROM products";
        $result = $conn->query($count_query);
    } else {
        $count_query = "SELECT COUNT(*) as count FROM products WHERE category = ?";
        $stmt = $conn->prepare($count_query);
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    $categories[$category][1] = $result->fetch_assoc()['count'];
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3">
                <h1>Products Management</h1>
                <button type="button" class="btn btn-primary" id="addProductBtn">
                    <i class="fas fa-plus"></i> Add New Product
                </button>
            </div>

            <div class="row">
                <!-- Category Sidebar -->
                <div class="col-md-3">
                    <div class="category-sidebar card">
                        <div class="list-group list-group-flush">
                            <?php foreach ($categories as $category => $data):
                                $icon = $data[0];
                                $count = $data[1];
                                $category_url = $category === 'Total Products' ? 'all' : urlencode($category);
                                $is_active = ($active_category === $category_url) ||
                                    ($category === 'Total Products' && $active_category === 'all');
                            ?>
                                <a href="?category=<?php echo $category_url; ?>"
                                    class="list-group-item list-group-item-action <?php echo $is_active ? 'active' : ''; ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-<?php echo $icon; ?> me-2"></i>
                                            <?php echo $category; ?>
                                        </div>
                                        <span class="badge bg-<?php echo $is_active ? 'light text-dark' : 'primary'; ?> rounded-pill">
                                            <?php echo $count; ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 80px">Image</th>
                                            <th>Name</th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Description</th>
                                            <th style="width: 100px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM products";
                                        if ($active_category !== 'all') {
                                            $query .= " WHERE category = ?";
                                        }
                                        $query .= " ORDER BY category, product_name";

                                        $stmt = $conn->prepare($query);
                                        if ($active_category !== 'all') {
                                            $stmt->bind_param('s', $active_category);
                                        }
                                        $stmt->execute();
                                        $products = $stmt->get_result();

                                        while ($product = $products->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td>
                                                    <img src="../<?php echo htmlspecialchars($product['product_image']); ?>"
                                                        alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                                        class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">
                                                </td>
                                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                                <td><?php echo htmlspecialchars($product['product_brand']); ?></td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?php echo htmlspecialchars($product['category']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 200px;">
                                                        <?php echo htmlspecialchars($product['product_description']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                            data-id="<?php echo $product['product_id']; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                            <input type="hidden" name="product_id"
                                                                value="<?php echo $product['product_id']; ?>">
                                                            <button type="submit" name="delete_product"
                                                                class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="product_id" name="product_id">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="product_brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="product_brand" name="product_brand" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category => $data):
                                    if ($category !== 'Total Products'): ?>
                                        <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                <?php endif;
                                endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                            <div id="current_image" class="mt-2" style="display: none;">
                                <img src="" alt="Current product image" class="img-thumbnail" style="height: 100px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label">Description</label>
                        <textarea class="form-control" id="product_description" name="product_description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="links" class="form-label">Product Links</label>
                        <input type="text" class="form-control" id="links" name="links">
                        <small class="text-muted">Add relevant product links (optional)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .category-sidebar {
        position: sticky;
        top: 1rem;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }

    .list-group-item {
        border: none;
        padding: 0.8rem 1rem;
        transition: all 0.2s ease;
    }

    .list-group-item:hover {
        background-color: rgba(42, 82, 152, 0.1);
    }

    .list-group-item.active {
        background: var(--primary-gradient);
        border-color: transparent;
    }

    .badge {
        font-weight: 500;
        min-width: 30px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-group {
        gap: 0.25rem;
    }

    .card {
        border: none;
        box-shadow: var(--card-shadow);
    }

    @media (max-width: 768px) {
        .category-sidebar {
            position: static;
            margin-bottom: 1rem;
        }
    }
</style>

<script>
    // REPLACE the existing JavaScript in admin/products.php with this enhanced version

document.addEventListener('DOMContentLoaded', function() {
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    const productForm = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');

    // Enhanced toast function
    function showToast(message, type = 'success', duration = 5000) {
        // Create toast container if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const iconMap = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'danger': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center border-0 bg-${type === 'error' ? 'danger' : type} text-white`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${iconMap[type] || 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, { delay: duration });
        bsToast.show();

        // Remove toast element after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        });

        return bsToast;
    }

    // Form validation
    function validateForm() {
        const errors = [];
        
        // Required fields
        const requiredFields = [
            { id: 'product_name', name: 'Product Name', minLength: 3 },
            { id: 'product_brand', name: 'Brand', minLength: 2 },
            { id: 'category', name: 'Category' },
            { id: 'product_description', name: 'Description', minLength: 10 }
        ];

        requiredFields.forEach(field => {
            const element = document.getElementById(field.id);
            const value = element.value.trim();
            
            // Remove previous error styling
            element.classList.remove('is-invalid');
            
            if (!value) {
                errors.push(`${field.name} is required`);
                element.classList.add('is-invalid');
            } else if (field.minLength && value.length < field.minLength) {
                errors.push(`${field.name} must be at least ${field.minLength} characters long`);
                element.classList.add('is-invalid');
            }
        });

        // Image validation for new products
        const productId = document.getElementById('product_id').value;
        const imageFile = document.getElementById('product_image').files[0];
        const currentImageVisible = document.getElementById('current_image').style.display !== 'none';
        
        if (!productId && !imageFile && !currentImageVisible) {
            errors.push('Product image is required for new products');
            document.getElementById('product_image').classList.add('is-invalid');
        }

        if (imageFile) {
            // File type validation
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(imageFile.type)) {
                errors.push('Invalid image format. Use JPEG, PNG, GIF, or WebP');
                document.getElementById('product_image').classList.add('is-invalid');
            }

            // File size validation (5MB)
            if (imageFile.size > 5 * 1024 * 1024) {
                const sizeMB = (imageFile.size / 1024 / 1024).toFixed(2);
                errors.push(`Image too large (${sizeMB}MB). Maximum 5MB allowed`);
                document.getElementById('product_image').classList.add('is-invalid');
            }
        }

        return errors;
    }

    // Button loading state
    function setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Save Product';
        }
    }

    // Add new product
    document.getElementById('addProductBtn').addEventListener('click', function() {
        modalTitle.textContent = 'Add New Product';
        productForm.reset();
        document.getElementById('current_image').style.display = 'none';
        document.getElementById('product_id').value = '';
        
        // Clear validation states
        productForm.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        
        productModal.show();
    });

    // Edit product
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.id;
            modalTitle.textContent = 'Edit Product';

            try {
                showToast('Loading product data...', 'info', 2000);
                
                const response = await fetch(`get_product.php?id=${productId}`);
                
                if (!response.ok) {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const textResponse = await response.text();
                    console.error('Non-JSON response:', textResponse);
                    throw new Error('Server returned invalid response format');
                }

                const data = await response.json();

                if (data.success && data.product) {
                    const product = data.product;
                    
                    // Populate form fields
                    document.getElementById('product_id').value = product.product_id || '';
                    document.getElementById('product_name').value = product.product_name || '';
                    document.getElementById('product_brand').value = product.product_brand || '';
                    document.getElementById('category').value = product.category || '';
                    document.getElementById('product_description').value = product.product_description || '';
                    document.getElementById('links').value = product.links || '';

                    // Handle current image
                    const currentImageDiv = document.getElementById('current_image');
                    const currentImageImg = currentImageDiv.querySelector('img');
                    
                    if (product.product_image && product.product_image !== 'images/default-product.jpg') {
                        currentImageImg.src = '../' + product.product_image;
                        currentImageDiv.style.display = 'block';
                    } else {
                        currentImageDiv.style.display = 'none';
                    }

                    // Clear validation states
                    productForm.querySelectorAll('.is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                    });

                    productModal.show();
                } else {
                    throw new Error(data.message || 'Failed to load product data');
                }
            } catch (error) {
                console.error('Error loading product:', error);
                showToast(`Failed to load product: ${error.message}`, 'error');
            }
        });
    });

    // Form submission
    productForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Validate form
        const validationErrors = validateForm();
        if (validationErrors.length > 0) {
            validationErrors.forEach(error => showToast(error, 'error'));
            return;
        }

        const submitButton = this.querySelector('button[type="submit"]');
        setButtonLoading(submitButton, true);

        try {
            const formData = new FormData(this);
            
            // Debug: Log form data
            console.log('=== FORM SUBMISSION DEBUG ===');
            for (let [key, value] of formData.entries()) {
                if (value instanceof File) {
                    console.log(`${key}: File(${value.name}, ${value.size} bytes, ${value.type})`);
                } else {
                    console.log(`${key}: ${value}`);
                }
            }

            const response = await fetch('save_product.php', {
                method: 'POST',
                body: formData
            });

            console.log('Response status:', response.status);
            console.log('Response headers:', [...response.headers.entries()]);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const textResponse = await response.text();
                console.error('=== NON-JSON RESPONSE ===');
                console.error('Content-Type:', contentType);
                console.error('Response body:', textResponse);
                throw new Error('Server returned invalid response format. Check server logs.');
            }

            const result = await response.json();
            console.log('=== SERVER RESPONSE ===', result);

            if (result.success) {
                productModal.hide();
                showToast(result.message, 'success');
                
                // Show debug info if available
                if (result.debug && result.debug.length > 0) {
                    console.log('=== DEBUG INFO ===');
                    result.debug.forEach(debug => console.log('Debug:', debug));
                }
                
                // Reload page after delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } else {
                throw new Error(result.message || 'Unknown server error occurred');
            }

        } catch (error) {
            console.error('=== SUBMISSION ERROR ===', error);
            
            let errorMessage = 'An unexpected error occurred. Please try again.';
            
            if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error. Please check your connection and try again.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server response error. Please check server logs and try again.';
            } else if (error.message.includes('HTTP 500')) {
                errorMessage = 'Server error (500). Please check server logs and database connection.';
            } else if (error.message) {
                errorMessage = error.message;
            }
            
            showToast(errorMessage, 'error', 8000);
            
        } finally {
            setButtonLoading(submitButton, false);
        }
    });

    // Image preview
    document.getElementById('product_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const currentImageDiv = document.getElementById('current_image');
        const currentImageImg = currentImageDiv.querySelector('img');
        
        if (file) {
            // Client-side validation
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showToast('Invalid file type. Please select JPEG, PNG, GIF, or WebP image.', 'error');
                this.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                showToast(`File too large (${sizeMB}MB). Maximum 5MB allowed.`, 'error');
                this.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                currentImageImg.src = e.target.result;
                currentImageDiv.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Clear validation on input
    productForm.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    // Modal cleanup
    document.getElementById('productModal').addEventListener('hidden.bs.modal', function() {
        productForm.reset();
        document.getElementById('current_image').style.display = 'none';
        productForm.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
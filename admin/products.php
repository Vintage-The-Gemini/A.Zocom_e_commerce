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
    document.addEventListener('DOMContentLoaded', function() {
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        const productForm = document.getElementById('productForm');
        const modalTitle = document.getElementById('modalTitle');

        // Open modal for new product
        document.getElementById('addProductBtn').addEventListener('click', function() {
            modalTitle.textContent = 'Add New Product';
            productForm.reset();
            document.getElementById('current_image').style.display = 'none';
            document.getElementById('product_id').value = '';
            productModal.show();
        });

        // Open modal for edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async function() {
                const productId = this.dataset.id;
                modalTitle.textContent = 'Edit Product';

                try {
                    const response = await fetch(`get_product.php?id=${productId}`);
                    const data = await response.json();

                    if (data.success) {
                        const product = data.product;
                        document.getElementById('product_id').value = product.product_id;
                        document.getElementById('product_name').value = product.product_name;
                        document.getElementById('product_brand').value = product.product_brand;
                        document.getElementById('category').value = product.category;
                        document.getElementById('product_description').value = product.product_description;
                        document.getElementById('links').value = product.links;

                        // Show current image
                        const currentImage = document.querySelector('#current_image img');
                        currentImage.src = '../' + product.product_image;
                        document.getElementById('current_image').style.display = 'block';

                        productModal.show();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error loading product data');
                }
            });
        });

        // Handle form submission
        productForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('save_product.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    productModal.hide();
                    // Reload the page to show updated data
                    window.location.reload();
                } else {
                    alert(result.message || 'Error saving product');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error saving product');
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
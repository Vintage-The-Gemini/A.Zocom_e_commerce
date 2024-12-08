<?php
session_start();

function getCategoryIcon($category)
{
    $icons = [
        'Foot Protection' => 'fa-shoe-prints',
        'Head Protection' => 'fa-hard-hat',
        'Hand Protection' => 'fa-hand-paper',
        'Eye Protection' => 'fa-glasses',
        'Respiratory Protection' => 'fa-head-side-mask',
        'Ear Protection' => 'fa-headphones',
        'Fall Protection' => 'fa-link',
        'Body Protection' => 'fa-tshirt',
        'Fabrics' => 'fa-scroll'
    ];

    return isset($icons[$category]) ? $icons[$category] : 'fa-box';
}
require_once './server/connection.php';

// Check admin authentication
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Get all counts for dashboard stats
$total_products_query = "SELECT COUNT(*) as count FROM products";
$total_blogs_query = "SELECT COUNT(*) as count FROM blogs";
$published_blogs_query = "SELECT COUNT(*) as count FROM blogs WHERE status = 'published'";
$orders_query = "SELECT COUNT(*) as count FROM orders"; // If you have an orders table

$total_products = $conn->query($total_products_query)->fetch_assoc()['count'];
$total_blogs = $conn->query($total_blogs_query)->fetch_assoc()['count'];
$published_blogs = $conn->query($published_blogs_query)->fetch_assoc()['count'];
$total_orders = 0; // Set to 0 or get from orders table if exists

// Get product counts for each category
$category_counts = [];
$categories = [
    'Foot Protection',
    'Head Protection',
    'Hand Protection',
    'Eye Protection',
    'Respiratory Protection',
    'Ear Protection',
    'Fall Protection',
    'Body Protection',
    'Fabrics'
];

foreach ($categories as $category) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE LOWER(category) = LOWER(?)");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $category_counts[$category] = $count;
}

// Fetch products with filter
$category_filter = isset($_GET['category']) ? $_GET['category'] : null;
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM products WHERE 1=1";
if ($category_filter) {
    $query .= " AND category = ?";
}
if ($search_term) {
    $query .= " AND (product_name LIKE ? OR product_brand LIKE ?)";
}
$query .= " ORDER BY product_id DESC";

$stmt = $conn->prepare($query);

if ($category_filter && $search_term) {
    $search_param = "%$search_term%";
    $stmt->bind_param("sss", $category_filter, $search_param, $search_param);
} elseif ($category_filter) {
    $stmt->bind_param("s", $category_filter);
} elseif ($search_term) {
    $search_param = "%$search_term%";
    $stmt->bind_param("ss", $search_param, $search_param);
}

$stmt->execute();
$products = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zocom Limited</title>

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <div class="toast-container"></div>

    <!-- Top Navigation -->
    <nav class="admin-nav">
        <div class="nav-brand">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <img src="images/zocom no bg logo.png" alt="Zocom Logo" class="nav-logo">
            <span>Admin Panel</span>
        </div>
        <div class="nav-menu">
            <a href="admin.php" class="nav-link active">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="admin-orders.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="admin-blogs.php" class="nav-link">
                <i class="fas fa-blog"></i> Blogs
            </a>
        </div>
        <div class="nav-actions">
            <div class="search-container">
                <input type="text" id="searchProducts" class="search-input"
                    placeholder="Search products..." value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="search-btn" onclick="searchProducts()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="admin-settings.php">
                            <i class="fas fa-cog"></i> Settings
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-content">
                <div class="category-header">
                    Product Categories
                </div>
                <ul class="category-list">
                    <li>
                        <a href="admin.php" class="<?php echo !$category_filter ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i>
                            All Products
                            <span class="badge"><?php echo array_sum($category_counts); ?></span>
                        </a>
                    </li>
                    <?php foreach ($categories as $category):
                        $category_slug = strtolower(str_replace(' ', '-', $category));
                        $count = $category_counts[$category] ?? 0;
                    ?>
                        <li>
                            <a href="admin.php?category=<?php echo urlencode($category); ?>"
                                class="<?php echo $category_filter === $category ? 'active' : ''; ?>">
                                <i class="fas <?php echo getCategoryIcon($category); ?>"></i>
                                <span class="category-name"><?php echo $category; ?></span>
                                <span class="badge"><?php echo $count; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Dashboard Stats -->
            <!-- Replace the current stats section with this -->
            <div class="stats-dashboard">
                <div class="stats-grid">
                    <div class="stats-card products">
                        <div class="stats-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?php echo $total_products; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>

                    <div class="stats-card blogs">
                        <div class="stats-icon">
                            <i class="fas fa-blog"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?php echo $total_blogs; ?></h3>
                            <p>Total Blogs</p>
                        </div>
                    </div>

                    <div class="stats-card orders">
                        <div class="stats-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?php echo $total_orders; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="content-section">
                <div class="content-header">
                    <h1><?php echo $category_filter ? $category_filter : 'All Products'; ?></h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Add New Product
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products->num_rows > 0): ?>
                                <?php while ($product = $products->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $product['product_id']; ?></td>
                                        <td>
                                            <img src="<?php echo $product['product_image']; ?>"
                                                alt="<?php echo $product['product_name']; ?>"
                                                class="product-thumbnail">
                                        </td>
                                        <td><?php echo $product['product_name']; ?></td>
                                        <td><?php echo $product['product_brand']; ?></td>
                                        <td><?php echo $product['category']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1"
                                                onclick="viewProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary me-1"
                                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="deleteProduct(<?php echo $product['product_id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <input type="text" name="product_brand" class="form-control" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price (KSH)</label>
                                <input type="number" name="product_price" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                            <div class="image-preview mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="product_description" class="form-control" rows="4"></textarea>
                            <small class="text-muted">Separate different points with semicolons (;)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Additional Links</label>
                            <textarea name="links" class="form-control" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <!-- Similar structure to Add Product Modal but with pre-filled values -->
    </div>

    <!-- View Product Modal -->
    <div class="modal fade" id="viewProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        function searchProducts() {
            const searchTerm = document.getElementById('searchProducts').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('search', searchTerm);
            window.location.href = currentUrl.toString();
        }

        // Handle search on enter key
        document.getElementById('searchProducts').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });

        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.admin-sidebar').classList.toggle('collapsed');
            document.querySelector('.admin-main').classList.toggle('expanded');
        });

        // Image preview functionality
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const preview = this.parentElement.querySelector('.image-preview');
                if (preview && this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>

    <script src="assets/js/admin.js"></script>
</body>

</html>
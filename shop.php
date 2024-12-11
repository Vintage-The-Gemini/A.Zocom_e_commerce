<?php
session_start();
require_once 'server/connection.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get cart count
$cartCount = count($_SESSION['cart']);

function ensureConnection()
{
    global $conn;
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_errno) {
        $config = require 'config.php';  // Move credentials to separate config file
        $conn = mysqli_connect(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass'],
            $config['db_name']
        ) or die("Couldn't connect to the database");
        $conn->set_charset("utf8mb4");
    }
    return $conn;
}

// Get database connection
$conn = ensureConnection();

// Get header images with error handling
try {
    $header_images_query = "SELECT product_image FROM products ORDER BY RAND() LIMIT 6";
    $header_images = $conn->query($header_images_query);

    if (!$header_images) {
        error_log("Header images query failed: " . $conn->error);
        $header_images = null;
    }
} catch (Exception $e) {
    error_log("Error fetching header images: " . $e->getMessage());
    $header_images = null;
}

// After securing the header images, get the featured products
require_once 'server/get_featured_products.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Shop for quality safety equipment at Zocom Limited. Wide range of protective gear including foot, head, hand protection and more.">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>Shop - Zocom Limited</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/shop.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <!-- Mobile Category Menu Toggle -->
    <!-- Update the toggle button -->
    <button id="categoryMenuToggle" class="category-menu-toggle">
        <i class="fas fa-bars"></i>
        <span>Categories</span>
    </button>

    <!-- Mobile Category Drawer - Updated Structure -->
    <div class="drawer-overlay"></div>
    <div class="mobile-category-drawer">
        <div class="drawer-header">
            <h3>Categories</h3>
            <button type="button" class="drawer-close">&times;</button>
        </div>
        <div class="drawer-content">
            <ul class="shop__category-list">
                <!-- Categories will be cloned here -->
            </ul>
        </div>
    </div>

    <main class="shop">
        <!-- Products Header -->
        <!-- Replace the existing shop header with this -->
        <header class="shop__header">
            <!-- Background Pattern -->
            <div class="header-pattern"></div>

            <!-- Safety Icons Overlay -->
            <div class="header-icons-overlay">
                <i class="fas fa-hard-hat"></i>
                <i class="fas fa-glasses"></i>
                <i class="fas fa-shoe-prints"></i>
                <i class="fas fa-hand-paper"></i>
                <i class="fas fa-headphones"></i>
                <i class="fas fa-mask"></i>
            </div>

            <!-- Header Content -->
            <div class="shop__header-content">
                <h1 class="shop__title">Safety Equipment Shop</h1>
                <p class="shop__subtitle">Quality Protection for Every Workplace - Your Trusted Source for Professional Safety Gear</p>
            </div>
        </header>

        <!-- Search Section - Now between hero and products -->
        <div class="shop__search-section">
            <div class="shop__search">
                <input type="text" id="searchInput" class="shop__search-input" placeholder="Search for safety equipment...">
                <button class="shop__search-btn">
                    <i class="fas fa-search"></i>
                    <span>Search Products</span>
                </button>
            </div>
        </div>



        <!-- Rest of your content -->

        <div class="shop__container">
            <!-- Sidebar -->
            <aside class="shop__sidebar">
                <div class="shop__categories">
                    <h3 class="shop__sidebar-title">Categories</h3>
                    <ul class="shop__category-list">
                        <?php
                        $categories = [
                            'Foot Protection' => 'shoe-prints',
                            'Head Protection' => 'hard-hat',
                            'Hand Protection' => 'hand-paper',
                            'Eye Protection' => 'glasses',
                            'Respiratory Protection' => 'head-side-mask',
                            'Ear Protection' => 'headphones',
                            'Fall Protection' => 'link',
                            'Body Protection' => 'tshirt',
                            'Fabrics' => 'scroll'
                        ];

                        foreach ($categories as $category => $icon):
                            $categoryId = strtolower(str_replace(' ', '-', $category));
                        ?>
                            <li>
                                <a href="#<?php echo $categoryId; ?>" class="shop__category-link <?php echo $category === 'Foot Protection' ? 'active' : ''; ?>">
                                    <i class="fas fa-<?php echo $icon; ?>"></i>
                                    <span><?php echo $category; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>

            <!-- Products Grid -->
            <section class="shop__content">
                <?php foreach ($categories as $category => $icon):
                    $categoryId = strtolower(str_replace(' ', '-', $category));
                    $variable_name = strtolower(str_replace(' ', '_', $category));
                    $products = ${$variable_name};
                ?>
                    <div class="shop__grid <?php echo $category === 'Foot Protection' ? 'active' : ''; ?>"
                        id="<?php echo $categoryId; ?>"
                        data-category="<?php echo $category; ?>">
                        <?php if ($products && $products->num_rows > 0):
                            while ($product = $products->fetch_assoc()): ?>
                                <article class="shop__card" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                                    <div class="shop__card-image">
                                        <img src="<?php echo htmlspecialchars($product['product_image']); ?>"
                                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                            loading="lazy"
                                            onclick="showProductModal(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                    </div>
                                    <div class="shop__card-content">
                                        <h3 class="shop__card-title"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                        <span class="shop__card-brand"><?php echo htmlspecialchars($product['product_brand']); ?></span>
                                    </div>
                                    <div class="shop__card-actions">
                                        <button type="button" class="shop__card-btn"
                                            onclick="addToCart(event, <?php echo htmlspecialchars(json_encode($product)); ?>)">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                        <a href="https://wa.me/+254782540742?text=<?php echo urlencode("I'm interested in " . $product['product_name']); ?>"
                                            class="shop__card-whatsapp" target="_blank">
                                            <i class="fab fa-whatsapp"></i> Order on WhatsApp
                                        </a>
                                    </div>
                                </article>
                            <?php endwhile;
                        else: ?>
                            <div class="shop__no-products">
                                <p>No products found in <?php echo $category; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
        </div>
    </main>

    <!-- Product Modal -->
    <!-- Product Modal -->
    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div id="productModalContent">
                        <!-- Product content template -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="modal-image-container">
                                    <img src="" alt="" class="modal-product-image">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal-product-details">
                                    <h3 class="modal-product-title"></h3>
                                    <p class="modal-product-brand"></p>
                                    <div class="product-description">
                                        <h4>Product Details:</h4>
                                        <ul class="product-description-list">
                                            <!-- Description points will be inserted here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <div class="modal-footer-actions">
                        <button type="button" class="shop__card-btn" id="modalAddToCart" data-product="">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <a href="#" class="shop__card-whatsapp" id="modalWhatsApp" target="_blank">
                            <i class="fab fa-whatsapp"></i> Order on WhatsApp
                        </a>
                        <button type="button" class="shop__card-btn" data-bs-dismiss="modal" style="background: #f4f7fa; color: #555;">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Toasts will be dynamically inserted here -->
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">

    </div>


    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script src="assets/js/cart.js" defer></script>
    <script src="assets/js/shop.js" defer></script>

</body>

</html>
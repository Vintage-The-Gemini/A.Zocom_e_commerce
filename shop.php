<?php
session_start();
require_once 'server/connection.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get cart count
$cartCount = count($_SESSION['cart']);

// Ensure database connection is active
function ensureConnection()
{
    global $conn;
    if (!$conn || !($conn instanceof mysqli) || $conn->connect_errno) {
        $conn = mysqli_connect("localhost", "root", "", "zocom")
            or die("Couldn't connect to the database");
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
    <button id="categoryMenuToggle" class="category-menu-toggle d-md-none">
        <i class="fas fa-bars"></i> Categories
    </button>

    <main class="shop">
        <!-- Products Header -->
        <!-- Replace the existing shop header with this -->
        <header class="shop__header">
            <!-- Background Product Images Grid -->
            <div class="header-images-grid">
                <?php while ($img = $header_images->fetch_assoc()): ?>
                    <div class="header-image">
                        <img src="<?php echo htmlspecialchars($img['product_image']); ?>" alt="Safety Equipment">
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Safety Icons Overlay -->
            <div class="header-icons-overlay">
                <i class="fas fa-hard-hat"></i>
                <i class="fas fa-glasses"></i>
                <i class="fas fa-shoe-prints"></i>
                <i class="fas fa-hand-paper"></i>
                <i class="fas fa-headphones"></i>
                <i class="fas fa-mask"></i>
            </div>

            <!-- Dark Overlay -->
            <div class="header-overlay"></div>

            <!-- Header Content -->
            <div class="shop__header-content">
                <div class="shop__header-text">
                    <h1 class="shop__title">Safety Equipment Shop</h1>
                    <p class="shop__subtitle">Quality Protection for Every Workplace</p>
                </div>

                <div class="shop__search-wrapper">
                    <div class="shop__search">
                        <input type="text"
                            id="searchInput"
                            class="shop__search-input"
                            placeholder="Search for safety equipment...">
                        <button class="shop__search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </header>

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
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="productModalContent">
                    <!-- Content will be dynamically inserted -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="shop__card-btn" id="modalAddToCart">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <a href="#" class="shop__card-whatsapp" id="modalWhatsAppBtn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Order via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">

    </div>


    <?php include 'components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/cart.js"></script>

    <script>
        // Add this to show notification when WhatsApp order button is clicked
        function showOrderNotification() {
            const toast = document.createElement('div');
            toast.className = 'custom-toast success-toast';
            toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>Order initiated successfully! Opening WhatsApp...</span>
        </div>
    `;

            document.querySelector('.toast-container').appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Update your orderViaWhatsApp function to include notification
        function orderViaWhatsApp(product) {
            showOrderNotification();
            // Rest of your existing orderViaWhatsApp code...
        }
    </script>

    <script>
        // Initialize all functionality when document is ready
        document.addEventListener('DOMContentLoaded', function() {
            initializeMobileMenu();
            initializeCategorySwitch();
            initializeSearch();
            initializeModal();
        });

        // Mobile menu initialization
        function initializeMobileMenu() {
            const categoryToggle = document.getElementById('categoryMenuToggle');
            const sidebar = document.querySelector('.shop__sidebar');

            if (categoryToggle && sidebar) {
                categoryToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    this.classList.toggle('active');
                });
            }
        }

        // Category switching
        function initializeCategorySwitch() {
            document.querySelectorAll('.shop__category-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    switchCategory(this);
                });
            });
        }

        function switchCategory(link) {
            // Remove active class from all links
            document.querySelectorAll('.shop__category-link').forEach(l =>
                l.classList.remove('active'));

            // Add active class to clicked link
            link.classList.add('active');

            // Switch grid display
            const targetId = link.getAttribute('href').substring(1);
            document.querySelectorAll('.shop__grid').forEach(grid => {
                grid.classList.remove('active');
                if (grid.id === targetId) {
                    grid.classList.add('active');
                }
            });

            // Close mobile menu if open
            if (window.innerWidth <= 768) {
                document.querySelector('.shop__sidebar').classList.remove('active');
                document.getElementById('categoryMenuToggle').classList.remove('active');
            }
        }

        // Search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    performSearch(searchTerm);
                }, 300));
            }
        }

        function performSearch(searchTerm) {
            document.querySelectorAll('.shop__card').forEach(card => {
                const cardContent = card.textContent.toLowerCase();
                card.style.display = cardContent.includes(searchTerm) ? 'block' : 'none';
            });

            // Update no results messages
            document.querySelectorAll('.shop__grid').forEach(grid => {
                const visibleCards = grid.querySelectorAll('.shop__card[style="display: block"]').length;
                const noProducts = grid.querySelector('.shop__no-products');
                if (noProducts) {
                    noProducts.style.display = visibleCards === 0 ? 'block' : 'none';
                }
            });
        }

        // Debounce function for search
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Modal functionality
        function initializeModal() {
            const productModal = document.getElementById('productModal');
            if (!productModal) return;

            // Clean up modal on hide
            productModal.addEventListener('hidden.bs.modal', function() {
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            });

            // Prevent modal from closing when clicking inside
            productModal.querySelector('.modal-content').addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        function showProductModal(product) {
            const modalContent = document.getElementById('productModalContent');
            if (!modalContent) return;

            // Generate description HTML
            let descriptionHtml = '';
            if (product.product_description) {
                const points = product.product_description.split(';');
                descriptionHtml = points.length > 1 ?
                    '<ul class="product-description-list">' +
                    points.map(point => `<li>${point.trim()}</li>`).join('') +
                    '</ul>' :
                    `<p>${product.product_description}</p>`;
            }

            // Update modal content
            modalContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="${product.product_image}" 
                             alt="${product.product_name}" 
                             class="modal-product-image">
                    </div>
                    <div class="col-md-6">
                        <div class="modal-product-details">
                            <h3 class="modal-product-title">${product.product_name}</h3>
                            <p class="modal-product-brand">${product.product_brand}</p>
                            <div class="modal-product-description">
                                ${descriptionHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Setup modal buttons
            const modalAddToCartBtn = document.getElementById('modalAddToCart');
            const modalWhatsAppBtn = document.getElementById('modalWhatsAppBtn');

            if (modalAddToCartBtn) {
                modalAddToCartBtn.onclick = (e) => addToCart(e, product);
            }

            if (modalWhatsAppBtn) {
                modalWhatsAppBtn.href = `https://wa.me/+254782540742?text=${encodeURIComponent(
                    `I'm interested in ${product.product_name} (${product.product_brand})`
                )}`;
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            modal.show();
        }
    </script>

    <script>
        // Add this to your existing JavaScript in shop.php
        function orderViaWhatsApp(product) {
            // Record the order
            fetch('server/record_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    products: `Product: ${product.product_name}\nBrand: ${product.product_brand}`,
                    from_cart: false
                })
            });

            // Open WhatsApp
            const message = encodeURIComponent(
                `I'm interested in:\n\nProduct: ${product.product_name}\nBrand: ${product.product_brand}`
            );
            window.open(`https://wa.me/+254782540742?text=${message}`, '_blank');
        }
    </script>
</body>

</html>
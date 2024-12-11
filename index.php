<?php
session_start();
require_once 'server/connection.php';

// Fetch one featured product from each category
$categories = [
    'Foot Protection',
    'Head Protection',
    'Hand Protection',
    'Eye Protection',
    'Respiratory Protection',
    'Fall Protection',
    'Body Protection'
];

$featured_products = [];
foreach ($categories as $category) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY RAND() LIMIT 1");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $featured_products[$category] = $result->fetch_assoc();
    }
}
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
    <title>Zocom Limited</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/footer.css">

    <!-- Add these in your HTML head section -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <main class="main">
        <!-- Hero Section -->
        <section class="hero" id="hero">
            <div class="hero__container">
                <div class="hero__left">
                    <h1 class="hero__title">
                        Your Safety <span>is Our Main Concern</span>
                    </h1>
                    <p class="hero__description">
                        We are leading suppliers of safety gear in Kenya, offering a wide range of products including safety shoes/boots, helmets, safety gloves, spectacles & goggles, overalls, spray suits, gumboots and body harnesses.
                        We work with the best safety wear brands in the world and assure you top-notch quality. Keep your workers safe with Zocom Limited!
                    </p>
                    <img src="images/safety 2.png" alt="Safety Equipment" class="hero__mobile-image">
                    <a href="#popular" class="hero__button">See Our Products</a>
                </div>
                <div class="hero__right">
                    <img src="images/safety hero.png" alt="Safety Hero Image" class="hero__image">
                </div>
            </div>
        </section>

        <!-- Popular Products Section -->
        <section class="popular" id="popular">
            <div class="popular__container">
                <div class="popular__header">
                    <h2 class="popular__title">Popular Products</h2>
                    <p class="popular__description">Find our best safety products faster</p>
                </div>

                <div class="popular__grid">
                    <?php foreach ($featured_products as $category => $product): ?>
                        <article class="popular__card">
                            <div class="popular__card-image">
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>"
                                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    loading="lazy">
                                <div class="popular__category"><?php echo htmlspecialchars($category); ?></div>
                            </div>
                            <div class="popular__text">
                                <h3 class="popular__name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="popular__brand"><?php echo htmlspecialchars($product['product_brand']); ?></p>
                                <p class="popular__description-text"><?php
                                                                        echo htmlspecialchars(substr($product['product_description'], 0, 100)) . '...';
                                                                        ?></p>
                                <a href="shop.php#<?php echo strtolower(str_replace(' ', '-', $category)); ?>"
                                    class="popular__button">
                                    <span>View Category</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <a href="shop.php" class="popular__more-button">View All Products</a>
            </div>
        </section>
        <!-- 
        <section class="offers">
            <div class="offers-background"></div>
            <div class="container">
                <h2 class="gradient-text">Special Offers</h2>
                <div class="offers-grid">
                    <div class="offer-item">
                        <img src="offers/banner 1.1.jpg" alt="Offer 1">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.2.jpg" alt="Offer 2">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.4.jpg" alt="Offer 3">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.9.jpg" alt="Offer 4">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.6.jpg" alt="Offer 5">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.7.jpg" alt="Offer 6">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 2.8.jpg" alt="Offer 7">
                    </div>
                    <div class="offer-item">
                        <img src="offers/banner 1.1.jpg" alt="Offer 8">
                    </div>
                </div>
            </div>
        </section> -->
        <section id="new-testimonials" class="py-5">
            <div class="container my-5">
                <h2 class="text-center mb-4 gradient-text">What Our Clients Say</h2>
                <div class="row g-4 justify-content-center">
                    <!-- Testimonial Card 1 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/Nairobi_Hospital_Logo.png" alt="The Nairobi Hospital" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">The Nairobi Hospital</h5>
                                    <small class="text-muted">Procurement & Stores Manager</small>
                                </div>
                            </div>
                            <p>"We confirm that Zocom Limited has supplied safety shoes & nurses' shoes to the hospital. Any assistance accorded to them will be highly appreciated."</p>
                        </div>
                    </div>
                    <!-- Testimonial Card 2 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/TOTAL ENERGIES LOGO.svg" alt="Total Kenya PLC" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">Total Kenya PLC</h5>
                                    <small class="text-muted">Supply Chain Manager</small>
                                </div>
                            </div>
                            <p>"Zocom Ltd has been our vendor for protective clothing and uniforms for the last ten years. We recommend them as a supplier subject to your own due diligence."</p>
                        </div>
                    </div>
                    <!-- Testimonial Card 3 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/Kenya power logo.png" alt="Kenya Power" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">Kenya Power</h5>
                                    <small class="text-muted">Supply Chain Manager</small>
                                </div>
                            </div>
                            <p>"Zocom Ltd has supplied personal protective clothing to us since 2008. Their performance has been good and satisfactory. We recommend them for consideration in any tender."</p>
                        </div>
                    </div>
                    <!-- Testimonial Card 4 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/agrochemical foods logo.png" alt="Agro-Chemical and Food Company Limited" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">Agro-Chemical and Food Company Limited</h5>
                                    <small class="text-muted">Procurement Officer</small>
                                </div>
                            </div>
                            <p>"Zocom Ltd has been supplying Personal Protective Equipment to us for over ten years. Their performance has been satisfactory, and we recommend them for your needs."</p>
                        </div>
                    </div>
                    <!-- Testimonial Card 5 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/kengen logo.png" alt="KenGen" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">KenGen</h5>
                                    <small class="text-muted">Supply Chain Director</small>
                                </div>
                            </div>
                            <p>"Zocom Limited has been our supplier for staff uniforms and protective wear. They are reliable, professional, and responsible, delivering within the specified time."</p>
                        </div>
                    </div>
                    <!-- Testimonial Card 6 -->
                    <div class="col-md-4">
                        <div class="neumorphic-card p-4 h-100">
                            <div class="d-flex align-items-center mb-3">
                                <img src="images/KNH LOGO.png" alt="Kenyatta National Hospital" class="me-3" width="60" height="60">
                                <div>
                                    <h5 class="mb-0">Kenyatta National Hospital</h5>
                                    <small class="text-muted">Chief Executive Officer</small>
                                </div>
                            </div>
                            <p>"We confirm that Zocom Limited has been our supplier for protective clothing and uniforms. We recommend them for their excellent service and reliability."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php include 'components/footer.php'; ?>

    </main>
    <!-- Rest of your content -->

    <script>
        // Toggle mobile menu
        document.querySelector('.navbar__toggle').addEventListener('click', function() {
            document.querySelector('.navbar__menu').classList.toggle('show');
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.navbar__toggle');
            const menu = document.querySelector('.navbar__menu');

            menuToggle.addEventListener('click', function() {
                menu.classList.toggle('show');
                menuToggle.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !menuToggle.contains(e.target)) {
                    menu.classList.remove('show');
                    menuToggle.classList.remove('active');
                }
            });

            // Close menu when window resized to desktop size
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    menu.classList.remove('show');
                    menuToggle.classList.remove('active');
                }
            });
        });
    </script>

</body>

</html>
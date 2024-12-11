<?php
session_start();
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
    <link rel="stylesheet" href="assets/css/about.css">

    <!-- Add these in your HTML head section -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <!-- Continuing from your base template -->
    <main class="about-page">
        <!-- Hero Section -->
        <!-- Replace the existing hero section with this -->
        <section class="about-hero">
            <div class="container">
                <div class="hero-content">
                    <div class="geometric-shapes">
                        <div class="shape shape-1"></div>
                        <div class="shape shape-2"></div>
                        <div class="shape shape-3"></div>
                        <div class="shape shape-4"></div>
                    </div>
                    <h1 class="gradient-title">About Zocom Limited</h1>
                    <p>Your trusted partner in safety solutions since 1995</p>
                </div>
            </div>
        </section>

        <!-- Replace the image section with this new stats section -->
        <div class="stats-section">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="stat-number">8+</div>
                    <div class="stat-label">Global Partners</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Safety Products</div>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <!-- Replace the existing about-content section with this -->
        <section class="about-content">
            <div class="container">
                <div class="about-wrapper">
                    <!-- Introduction Card -->
                    <div class="about-intro">
                        <div class="about-header">
                            <span class="about-subtitle">Our Story</span>
                            <h2>Know More About Us</h2>
                            <div class="header-line"></div>
                        </div>

                    </div>

                    <!-- Timeline Cards -->
                    <div class="about-timeline">
                        <div class="timeline-card">
                            <div class="year">1995</div>
                            <div class="content">
                                <h3>The Beginning</h3>
                                <p>Zocom Ltd was established to deal with general supplies, marking the start of our journey in the safety industry.</p>
                            </div>
                        </div>

                        <div class="timeline-card">
                            <div class="year">2001</div>
                            <div class="content">
                                <h3>Specialization</h3>
                                <p>Recognizing an urgent need in occupational health and safety, we dedicated ourselves to providing professional safety solutions.</p>
                            </div>
                        </div>

                        <div class="timeline-card">
                            <div class="year">Present</div>
                            <div class="content">
                                <h3>Growth & Excellence</h3>
                                <p>Today, we've expanded our operations with more staff and enhanced systems, becoming a leader in safety equipment distribution.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mission Statement -->
                    <div class="mission-statement">
                        <div class="mission-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="mission-content">
                            <h3>Our Mission</h3>
                            <p>To provide top-quality safety solutions while maintaining the highest standards of professionalism and customer service in the industry.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Partners Section -->
        <section class="partners-section">
            <div class="container">
                <h2>Our Partners</h2>
                <p class="section-subtitle">We work with the best safety wear brands in the world</p>

                <div class="partners-grid">
                    <a href="https://www.safetyjogger.com/" target="_blank" class="partner-card">
                        <img src="logos/SAFETY JOGGER LOGO.png" alt="Safety Jogger">
                    </a>

                    <a href="https://www.deltaplus.eu/en-US/" target="_blank" class="partner-card">
                        <img src="logos/DELTA PLUS LOGO.svg" alt="Delta Plus">
                    </a>

                    <a href="https://www.wondergrip.com/en/" target="_blank" class="partner-card">
                        <img src="logos/wondergrip logo 4.jpg" alt="Wonder Grip">
                    </a>

                    <a href="https://www.bova.co.za" target="_blank" class="partner-card">
                        <img src="logos/bova_logo.png" alt="Bova">
                    </a>

                    <a href="goliath.co.uk" target="_blank" class="partner-card">
                        <img src="logos/GOLIATH 4.jpg" alt="Goliath">
                    </a>

                    <a href="https://www.leoworkwear.com/" target="_blank" class="partner-card">
                        <img src="logos/leo_workwear_logo.png" alt="Leo Workwear">
                    </a>

                    <a href="https://www.xmtextiles.com/" target="_blank" class="partner-card">
                        <img src="logos/xm_textiles_logo.png" alt="XM Textiles">
                    </a>

                    <a href="https://superhousegroup.com/" target="_blank" class="partner-card">
                        <img src="logos/super_house_logo.png" alt="Super House">
                    </a>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <div class="container">
                <h2>Our Core Values</h2>
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Quality Assurance</h3>
                        <p>We maintain the highest standards in safety equipment and protective wear.</p>
                    </div>

                    <div class="value-card">
                        <i class="fas fa-handshake"></i>
                        <h3>Professional Service</h3>
                        <p>Dedicated to providing prompt and professional service to all our clients.</p>
                    </div>

                    <div class="value-card">
                        <i class="fas fa-certificate"></i>
                        <h3>Certified Products</h3>
                        <p>All our products meet international safety standards and certifications.</p>
                    </div>

                    <div class="value-card">
                        <i class="fas fa-users"></i>
                        <h3>Customer Focus</h3>
                        <p>We prioritize our customers' needs and satisfaction in everything we do.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'components/footer.php'; ?>

    <script>
        // Add this at the bottom of your page before </body>

        document.addEventListener('DOMContentLoaded', function() {
            const parallaxImage = document.querySelector('.parallax-image');
            if (!parallaxImage) return;

            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const rate = scrolled * 0.3;

                // Only apply transform if the element is in viewport
                const rect = parallaxImage.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    parallaxImage.style.transform = `translate3d(0, ${rate}px, 0)`;
                }
            });
        });
    </script>
</body>

</html>
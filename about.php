<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>


    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-K9SX33NJ');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Favicon -->
    <link rel="icon" href="images/zocom no bg logo.png" type="image/png">
    <link rel="apple-touch-icon" href="images/zocom no bg logo.png">

    <!-- Primary Meta Tags -->
    <meta name="title" content="About Us - Zocom Limited">
    <meta name="description" content="Learn about Zocom Limited, your trusted partner in safety solutions since 1995. Leading supplier of quality safety equipment in Kenya.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.zocomlimited.co.ke/about">
    <meta property="og:title" content="About Zocom Limited - Your Safety Partner">
    <meta property="og:description" content="Your trusted partner in safety solutions since 1995. Learn about our commitment to workplace safety and quality protection equipment.">
    <meta property="og:image" content="images/zocom no bg logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.zocomlimited.co.ke/about">
    <meta property="twitter:title" content="About Zocom Limited - Your Safety Partner">
    <meta property="twitter:description" content="Your trusted partner in safety solutions since 1995. Learn about our commitment to workplace safety and quality protection equipment.">
    <meta property="twitter:image" content="images/zocom no bg logo.png">

    <title>About Us - Zocom Limited</title>

    <!-- Your existing CSS links -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/about.css">
</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K9SX33NJ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
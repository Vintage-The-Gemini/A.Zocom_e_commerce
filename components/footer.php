<?php
// components/footer.php
?>
<link rel="stylesheet" href="assets/css/footer.css">
<footer class="footer">
    <div class="footer__container">
        <div class="footer__content">
            <div class="footer__brand">
                <img src="images/zocom no bg logo.png" alt="Zocom Limited" class="logo">
            </div>

            <div class="footer__data">
                <div class="footer__section">
                    <h3 class="footer__title">About</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="about.php" class="footer__link">About Us</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Company</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="about.php" class="footer__link">Why Choose Us</a>
                        </li>
                        <li>
                            <a href="about.php" class="footer__link">Our Partners</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Products</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="shop.php" class="footer__link">Most Popular</a>
                        </li>
                        <li>
                            <a href="#popular" class="footer__link">New Arrivals</a>
                        </li>
                        <li>
                            <a href="shop.php" class="footer__link">Safety Products</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Follow Us</h3>
                    <div class="footer__social">
                        <a href="https://www.facebook.com/zocomltd/"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="https://www.instagram.com/zocomlimited/"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/zocom-limited/?viewAsMember=true"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCMXidOGnoybsb0PkQAalfhA"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="https://x.com/ZocomLimited"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="Twitter">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <a href="https://www.tiktok.com/@zocomltd"
                            target="_blank"
                            class="footer__social-link"
                            aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer__group">
                <div class="footer__terms">
                    <a href="#">Terms & Agreements</a>
                    <a href="#">Privacy Policy</a>
                </div>
                <span class="footer__copy">
                    &copy; <?php echo date('Y'); ?> Zocom Limited. All rights reserved
                </span>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if (basename($_SERVER['PHP_SELF']) === 'shop.php'): ?>
    <!-- Cart JS only on shop page -->
    <script src="assets/js/cart.js"></script>
<?php endif; ?>

<!-- Common JS -->
<script src="assets/js/navbar.js"></script>
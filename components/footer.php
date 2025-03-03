<?php
// Ensure proper asset loading with root-relative paths
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="/assets/css/footer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<footer class="footer">
    <div class="footer__container">
        <div class="footer__content">
            <div class="footer__brand">
                <img src="/images/zocom no bg logo.png" alt="Zocom Limited" class="logo">
            </div>

            <div class="footer__data">
                <div class="footer__section">
                    <h3 class="footer__title">About</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="/about" class="footer__link">About Us</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Company</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="/about#why-choose-us" class="footer__link">Why Choose Us</a>
                        </li>
                        <li>
                            <a href="/about#partners" class="footer__link">Our Partners</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Products</h3>
                    <ul class="footer__links">
                        <li>
                            <a href="/shop" class="footer__link">Most Popular</a>
                        </li>
                        <li>
                            <a href="/shop#new-arrivals" class="footer__link">New Arrivals</a>
                        </li>
                        <li>
                            <a href="/shop#safety-products" class="footer__link">Safety Products</a>
                        </li>
                    </ul>
                </div>

                <div class="footer__section">
                    <h3 class="footer__title">Follow Us</h3>
                    <div class="footer__social">
                        <a href="https://www.facebook.com/zocomltd/"
                            target="_blank"
                            class="footer__social-link facebook"
                            aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/zocomlimited/"
                            target="_blank"
                            class="footer__social-link instagram"
                            aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/zocom-limited/?viewAsMember=true"
                            target="_blank"
                            class="footer__social-link linkedin"
                            aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCMXidOGnoybsb0PkQAalfhA"
                            target="_blank"
                            class="footer__social-link youtube"
                            aria-label="YouTube">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                        <a href="https://x.com/ZocomLimited"
                            target="_blank"
                            class="footer__social-link x-twitter"
                            aria-label="X (Twitter)">
                            <i class="fa-brands fa-x-twitter"></i>
                        </a>
                        <a href="https://www.tiktok.com/@zocomltd"
                            target="_blank"
                            class="footer__social-link tiktok"
                            aria-label="TikTok">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer__group">
                <div class="footer__terms">
                    <a href="/terms">Terms & Agreements</a>
                    <a href="/privacy">Privacy Policy</a>
                </div>
                <span class="footer__copy">
                    &copy; <?php echo date('Y'); ?> Zocom Limited. All rights reserved
                </span>
            </div>
        </div>
    </div>
</footer>

<!-- Load scripts based on page needs -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($current_page === 'shop'): ?>
    <script src="/assets/js/cart.js"></script>
<?php endif; ?>

<script src="/assets/js/navbar.js"></script>
<?php
function isActiveLink($page)
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}
?>

<nav class="navbar">
    <div class="navbar__container">
        <a href="index.php" class="navbar__brand">
            <img src="images/zocom no bg logo.png" alt="Zocom Logo" class="navbar__logo">
        </a>


        <button class="navbar__toggle" id="navbarToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="navbar__menu">
            <ul class="navbar__nav">
                <li><a href="index.php" class="navbar__link <?php echo isActiveLink('index.php'); ?>">Home</a></li>
                <li><a href="shop.php" class="navbar__link <?php echo isActiveLink('shop.php'); ?>">Shop</a></li>
                <li><a href="about.php" class="navbar__link <?php echo isActiveLink('about.php'); ?>">About</a></li>
                <li><a href="contacts.php" class="navbar__link <?php echo isActiveLink('contacts.php'); ?>">Contact</a></li>
                <li><a href="blogs.php" class="navbar__link <?php echo isActiveLink('blogs.php'); ?>">Blogs</a></li>
            </ul>

            <div class="navbar__cart">
                <a href="cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                </a>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the toggle button and menu
            const toggleButton = document.getElementById('navbarToggle');
            const menu = document.querySelector('.navbar__menu');

            // Toggle menu when button is clicked
            toggleButton.addEventListener('click', function() {
                // Toggle active class on button and menu
                this.classList.toggle('active');
                menu.classList.toggle('show');
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideMenu = menu.contains(event.target);
                const isClickOnToggle = toggleButton.contains(event.target);

                if (!isClickInsideMenu && !isClickOnToggle && menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    toggleButton.classList.remove('active');
                }
            });

            // Close menu when window is resized to larger screen
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    menu.classList.remove('show');
                    toggleButton.classList.remove('active');
                }
            });
        });
    </script>

</nav>
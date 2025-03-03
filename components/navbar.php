<?php
// Get current page for active state
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');
?>

<nav class="navbar">
    <div class="navbar__container">
        <a href="index" class="navbar__brand">
            <img src="images/zocom no bg logo.png" alt="Zocom Logo" class="navbar__logo">
        </a>

        <div class="navbar__menu">
            <ul class="navbar__nav">
                <li><a href="index.php" class="navbar__link <?php echo ($current_page == 'index') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="shop.php" class="navbar__link <?php echo ($current_page == 'shop') ? 'active' : ''; ?>">Shop</a></li>
                <li><a href="about.php" class="navbar__link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="blogs.php" class="navbar__link <?php echo ($current_page == 'blogs') ? 'active' : ''; ?>">Blog</a></li>
                <li><a href="contacts.php" class="navbar__link <?php echo ($current_page == 'contacts') ? 'active' : ''; ?>">Contact</a></li>
            </ul>

            <a href="cart.php" class="navbar__cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
            </a>
        </div>

        <button id="navbarToggle" class="navbar__toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>
<?php
// admin/includes/sidebar.php

// Get the current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-3 col-lg-2 admin-sidebar">
    <div class="sidebar-brand">
        <h4>Zocom Admin</h4>
        <p class="mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
    </div>

    <nav class="nav flex-column">
        <a href="index.php" class="nav-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="products.php" class="nav-link <?php echo $current_page === 'products.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="blogs.php" class="nav-link <?php echo $current_page === 'blogs.php' ? 'active' : ''; ?>">
            <i class="fas fa-blog"></i> Blogs
        </a>
        <a href="orders.php" class="nav-link <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="users.php" class="nav-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <a href="change_password.php" class="nav-link">
            <i class="fas fa-key"></i> Change Password
        </a>
    </nav>
</div>
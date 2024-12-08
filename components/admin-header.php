<nav class="admin-nav">
    <div class="nav-brand">
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <img src="images/zocom no bg logo.png" alt="Zocom Logo" class="nav-logo">
        <span>Admin Panel</span>
    </div>
    <div class="nav-menu">
        <a href="admin.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>">
            <i class="fas fa-box"></i> Products
        </a>
        <a href="admin-orders.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'admin-orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="admin-blogs.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'admin-blogs.php' ? 'active' : ''; ?>">
            <i class="fas fa-blog"></i> Blogs
        </a>
    </div>
    <div class="nav-actions">
        <div class="search-container">
            <input type="text" id="searchInput" class="search-input" placeholder="Search...">
            <button class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle" type="button" id="settingsDropdown" data-bs-toggle="dropdown">
                <i class="fas fa-cog"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="admin-settings.php">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
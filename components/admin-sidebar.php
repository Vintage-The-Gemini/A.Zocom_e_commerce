<aside class="admin-sidebar">
    <div class="sidebar-content">
        <div class="category-header">
            Dashboard
        </div>
        <ul class="category-list">
            <li>
                <a href="admin.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    Products
                </a>
            </li>
        </ul>

        <div class="category-header mt-4">
            Content Management
        </div>
        <ul class="category-list">
            <li>
                <a href="admin-blogs.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'admin-blogs.php' ? 'active' : ''; ?>">
                    <i class="fas fa-blog"></i>
                    Blogs
                    <span class="badge bg-primary"><?php echo $blog_count; ?></span>
                </a>
            </li>
        </ul>

        <div class="category-header mt-4">
            Settings
        </div>
        <ul class="category-list">
            <li>
                <a href="#" onclick="showWhatsAppOrders()">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp Orders
                </a>
            </li>
            <li>
                <a href="#" onclick="showSettings()">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>
</aside>
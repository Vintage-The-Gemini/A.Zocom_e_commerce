<?php
session_start();
require_once '../server/connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch statistics
// Total Products
$products_query = "SELECT COUNT(*) as total FROM products";
$result = $conn->query($products_query);
$total_products = $result->fetch_assoc()['total'];

// New Orders (orders with 'new' status)
$orders_query = "SELECT COUNT(*) as total FROM orders WHERE status = 'new'";
$result = $conn->query($orders_query);
$new_orders = $result->fetch_assoc()['total'];

// Published Blogs
$blogs_query = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
$result = $conn->query($blogs_query);
$published_blogs = $result->fetch_assoc()['total'];

// Active Admin Users
$users_query = "SELECT COUNT(*) as total FROM admin_users";
$result = $conn->query($users_query);
$active_users = $result->fetch_assoc()['total'];

// Fetch admin info
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT username FROM admin_users WHERE id = ?";
$stmt = $conn->prepare($admin_query);
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Zocom Limited</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2a5298, #76060d, #1e3c72);
            --primary-color: #2a5298;
            --accent-color: #ff0081;
            --dark-blue: #1e3c72;
            --light-bg: #f4f7fa;
            --card-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            --transition: all 0.2s ease;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            background: var(--primary-gradient);
            min-height: 100vh;
            color: white;
            padding-top: 2rem;
        }

        .sidebar-brand {
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1.5rem;
            transition: var(--transition);
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }

        /* Main Content Styles */
        .main-content {
            background: var(--light-bg);
            min-height: 100vh;
            padding: 2rem;
        }

        .content-header {
            margin-bottom: 2rem;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 1rem;
        }

        .stats-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--dark-blue);
        }

        .stats-card p {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                width: 250px;
                z-index: 1000;
                transition: var(--transition);
            }

            .admin-sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-toggle {
                display: block !important;
            }
        }

        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar">
                <div class="sidebar-brand">
                    <h4>Zocom Admin</h4>
                    <p class="mb-0">Welcome, <?php echo htmlspecialchars($admin['username']); ?></p>
                </div>

                <nav class="nav flex-column">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="products.php" class="nav-link">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="blogs.php" class="nav-link">
                        <i class="fas fa-blog"></i> Blogs
                    </a>
                    <a href="orders.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="content-header">
                    <h1>Dashboard Overview</h1>
                </div>

                <!-- Stats Cards -->
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <h3>Products</h3>
                            <p class="mb-0">Total: <?php echo $total_products; ?></p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>Orders</h3>
                            <p class="mb-0">New: <?php echo $new_orders; ?></p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-blog"></i>
                            </div>
                            <h3>Blogs</h3>
                            <p class="mb-0">Published: <?php echo $published_blogs; ?></p>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="stats-card">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Users</h3>
                            <p class="mb-0">Active: <?php echo $active_users; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <p>Welcome to your admin dashboard. From here you can:</p>
                                <ul>
                                    <li>Manage products in your catalog</li>
                                    <li>Handle new orders and update order status</li>
                                    <li>Create and edit blog posts</li>
                                    <li>Manage admin users</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.admin-sidebar');
            const toggleBtn = document.querySelector('.mobile-toggle');

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>
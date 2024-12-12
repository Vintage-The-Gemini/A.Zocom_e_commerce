<?php
// admin/includes/header.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Zocom Admin</title>

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

        /* Mobile Responsive */
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
        }
    </style>
</head>

<body>
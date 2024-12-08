<?php
session_start();
require_once './server/connection.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Fetch orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_orders_query = "SELECT COUNT(*) as count FROM orders";
$total_orders_result = $conn->query($total_orders_query);

if ($total_orders_result) {
    $total_orders = $total_orders_result->fetch_assoc()['count'];
    $total_pages = ceil($total_orders / $per_page);

    // Fetch orders
    $query = "SELECT * FROM orders ORDER BY order_date DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $per_page, $offset);
    $stmt->execute();
    $orders = $stmt->get_result();
} else {
    $total_orders = 0;
    $total_pages = 0;
    $orders = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Zocom Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>

<body>
    <nav class="admin-nav">
        <div class="nav-brand">
            <button id="sidebarToggle" class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <img src="images/zocom no bg logo.png" alt="Zocom Logo" class="nav-logo">
            <span>Admin Panel</span>
        </div>
        <div class="nav-menu">
            <a href="admin.php" class="nav-link">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="admin-orders.php" class="nav-link active">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="admin-blogs.php" class="nav-link">
                <i class="fas fa-blog"></i> Blogs
            </a>
        </div>
        <div class="nav-actions">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-content">
                <div class="category-header">
                    Order Status
                </div>
                <ul class="category-list">
                    <li>
                        <a href="?status=all" class="active">
                            <i class="fas fa-list"></i>
                            All Orders
                            <span class="badge bg-primary"><?php echo $total_orders; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="?status=new">
                            <i class="fas fa-star"></i>
                            New Orders
                        </a>
                    </li>
                    <li>
                        <a href="?status=processing">
                            <i class="fas fa-clock"></i>
                            Processing
                        </a>
                    </li>
                    <li>
                        <a href="?status=completed">
                            <i class="fas fa-check"></i>
                            Completed
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="admin-main">
            <div class="stats-dashboard">
                <div class="stats-grid">
                    <div class="stats-card">
                        <div class="stats-icon products">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?php echo $total_orders; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <!-- Add more stats cards if needed -->
                </div>
            </div>

            <div class="content-section">
                <div class="content-header">
                    <h1>Order Management</h1>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Products</th>
                                <th>WhatsApp</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders && $orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>
                                            <?php
                                            $products = nl2br(htmlspecialchars($order['product_list']));
                                            echo substr($products, 0, 100) . (strlen($products) > 100 ? '...' : '');
                                            ?>
                                        </td>
                                        <td>
                                            <a href="https://wa.me/<?php echo $order['whatsapp_number']; ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-success">
                                                <i class="fab fa-whatsapp"></i> Chat
                                            </a>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <select class="form-select form-select-sm status-select"
                                                onchange="updateOrderStatus(<?php echo $order['order_id']; ?>, this.value)">
                                                <option value="new" <?php echo $order['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" onclick="viewOrder(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteOrder(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No orders found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Orders pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update order status
        function updateOrderStatus(orderId, status) {
            fetch('server/order_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'update_status',
                        order_id: orderId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Order status updated successfully');
                    } else {
                        showToast('Failed to update status', 'error');
                    }
                });
        }

        // View order details
        function viewOrder(orderId) {
            fetch(`server/order_handler.php?action=get_order&id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modalBody = document.querySelector('#viewOrderModal .modal-body');
                        modalBody.innerHTML = `
                            <div class="order-details">
                                <p><strong>Order ID:</strong> #${data.order.order_id}</p>
                                <p><strong>Date:</strong> ${new Date(data.order.order_date).toLocaleDateString()}</p>
                                <p><strong>WhatsApp:</strong> ${data.order.whatsapp_number}</p>
                                <p><strong>Status:</strong> ${data.order.status}</p>
                                <hr>
                                <h6>Products:</h6>
                                <pre>${data.order.product_list}</pre>
                                ${data.order.notes ? `<hr><h6>Notes:</h6><p>${data.order.notes}</p>` : ''}
                            </div>
                        `;
                        new bootstrap.Modal(document.getElementById('viewOrderModal')).show();
                    }
                });
        }

        // Delete order
        function deleteOrder(orderId) {
            if (!confirm('Are you sure you want to delete this order?')) return;

            fetch('server/order_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        order_id: orderId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Order deleted successfully');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast('Failed to delete order', 'error');
                    }
                });
        }

        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}-toast`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;

            document.querySelector('.toast-container').appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>

</html>
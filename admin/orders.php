<?php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get order statistics
$stats_query = "SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_orders,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
    SUM(total_amount) as total_revenue
FROM orders";
// Get order statistics with error handling
$stats_query = "SELECT 
    COUNT(*) as total_orders,
    COALESCE(SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END), 0) as new_orders,
    COALESCE(SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END), 0) as processing_orders,
    COALESCE(SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END), 0) as shipped_orders,
    COALESCE(SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END), 0) as delivered_orders,
    COALESCE(SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END), 0) as pending_payments,
    COALESCE(SUM(total_amount), 0) as total_revenue
FROM orders";

$stats_result = $conn->query($stats_query);
if ($stats_result === false) {
    // Handle query error
    die("Error fetching statistics: " . $conn->error);
}
$stats = $stats_result->fetch_assoc();

// Set default values if no orders exist
if (!$stats) {
    $stats = [
        'total_orders' => 0,
        'new_orders' => 0,
        'processing_orders' => 0,
        'shipped_orders' => 0,
        'delivered_orders' => 0,
        'pending_payments' => 0,
        'total_revenue' => 0
    ];
}

// Get all orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build the query
$where_clauses = [];
if ($status_filter) {
    $where_clauses[] = "status = '$status_filter'";
}
if ($search) {
    $where_clauses[] = "(order_number LIKE '%$search%' OR customer_name LIKE '%$search%' OR customer_email LIKE '%$search%')";
}

$where_sql = $where_clauses ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$orders_query = "SELECT * FROM orders $where_sql ORDER BY created_at DESC LIMIT $offset, $limit";
$orders = $conn->query($orders_query);

// Get total orders count for pagination
$total_query = "SELECT COUNT(*) as count FROM orders $where_sql";
$total_orders = $conn->query($total_query)->fetch_assoc()['count'];
$total_pages = ceil($total_orders / $limit);

$page_title = "Orders Management";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                <h1>Orders Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" id="exportOrdersBtn">
                        <i class="fas fa-download"></i> Export Orders
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Total Orders</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['total_orders']; ?></h3>
                                </div>
                                <i class="fas fa-shopping-cart fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">New Orders</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['new_orders']; ?></h3>
                                </div>
                                <i class="fas fa-clock fa-2x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Total Revenue</h6>
                                    <h3 class="card-title mb-0">$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                                </div>
                                <i class="fas fa-dollar-sign fa-2x text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Pending Payments</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['pending_payments']; ?></h3>
                                </div>
                                <i class="fas fa-hourglass-half fa-2x text-danger opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3" method="GET">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Order Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="search" class="form-label">Search Orders</label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="<?php echo htmlspecialchars($search); ?>"
                                placeholder="Search by order number, customer name, or email">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($orders && $orders->num_rows > 0): ?>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                </div>
                                            </td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo match ($order['status']) {
                                                                            'new' => 'info',
                                                                            'processing' => 'primary',
                                                                            'shipped' => 'warning',
                                                                            'delivered' => 'success',
                                                                            'cancelled' => 'danger',
                                                                            default => 'secondary'
                                                                        };
                                                                        ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo match ($order['payment_status']) {
                                                                            'paid' => 'success',
                                                                            'refunded' => 'warning',
                                                                            default => 'danger'
                                                                        };
                                                                        ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-info btn-view"
                                                        data-id="<?php echo $order['id']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                        data-id="<?php echo $order['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($order['status'] !== 'delivered'): ?>
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="<?php echo $order['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Orders pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Order View Modal -->
<div class="modal fade" id="orderViewModal" tabindex="-1" aria-labelledby="orderViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderViewModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary print-order">
                    <i class="fas fa-print"></i> Print Order
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Order Edit Modal -->
<div class="modal fade" id="orderEditModal" tabindex="-1" aria-labelledby="orderEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderEditModalLabel">Edit Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="orderEditForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_order_id" name="order_id">

                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Order Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="new">New</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="edit_payment_status" name="payment_status" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderViewModal = new bootstrap.Modal(document.getElementById('orderViewModal'));
        const orderEditModal = new bootstrap.Modal(document.getElementById('orderEditModal'));

        // View Order
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.dataset.id;
                try {
                    const response = await fetch(`get_order.php?id=${orderId}`);
                    const data = await response.json();

                    if (data.success) {
                        const modalBody = document.querySelector('#orderViewModal .modal-body');
                        modalBody.innerHTML = generateOrderDetails(data.order);
                        orderViewModal.show();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('Error loading order details', 'error');
                }
            });
        });

        // Edit Order
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.dataset.id;
                try {
                    const response = await fetch(`get_order.php?id=${orderId}`);
                    const data = await response.json();

                    if (data.success) {
                        document.getElementById('edit_order_id').value = data.order.id;
                        document.getElementById('edit_status').value = data.order.status;
                        document.getElementById('edit_payment_status').value = data.order.payment_status;
                        document.getElementById('edit_notes').value = data.order.notes;
                        orderEditModal.show();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('Error loading order data', 'error');
                }
            });
        });

        // Delete Order
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Are you sure you want to delete this order?')) {
                    const orderId = this.dataset.id;
                    try {
                        const response = await fetch('delete_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                order_id: orderId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showToast('Order deleted successfully', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message, 'error');
                        }
                    } catch (error) {
                        showToast('Error deleting order', 'error');
                    }
                }
            });
        });

        // Save Order Changes
        document.getElementById('orderEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('update_order.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    orderEditModal.hide();
                    showToast('Order updated successfully', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.message || 'Error updating order', 'error');
                }
            } catch (error) {
                showToast('Error updating order', 'error');
            }
        });

        // Export Orders
        document.getElementById('exportOrdersBtn').addEventListener('click', function() {
            window.location.href = 'export_orders.php';
        });

        // Print Order
        document.querySelector('.print-order').addEventListener('click', function() {
            const printContent = document.querySelector('#orderViewModal .modal-body').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
            <html>
                <head>
                    <title>Print Order</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { padding: 20px; }
                        @media print {
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent}
                    <button onclick="window.print()" class="btn btn-primary mt-3 no-print">Print</button>
                </body>
            </html>
        `);
        });

        function generateOrderDetails(order) {
            return `
            <div class="order-details">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p><strong>Order Number:</strong> ${order.order_number}</p>
                        <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
                        <p><strong>Status:</strong> ${order.status}</p>
                        <p><strong>Payment Status:</strong> ${order.payment_status}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <p><strong>Name:</strong> ${order.customer_name}</p>
                        <p><strong>Email:</strong> ${order.customer_email}</p>
                        <p><strong>Phone:</strong> ${order.customer_phone || 'N/A'}</p>
                    </div>
                </div>
                <div class="mb-3">
                    <h6>Shipping Address</h6>
                    <p>${order.shipping_address}</p>
                </div>
                <div class="mb-3">
                    <h6>Order Items</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${generateOrderItemsRows(order.items)}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Amount:</th>
                                <th>$${order.total_amount}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                ${order.notes ? `
                    <div class="mb-3">
                        <h6>Notes</h6>
                        <p>${order.notes}</p>
                    </div>
                ` : ''}
            </div>
        `;
        }

        function generateOrderItemsRows(items) {
            return items.map(item => `
            <tr>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>$${item.price}</td>
                <td>$${(item.quantity * item.price).toFixed(2)}</td>
            </tr>
        `).join('');
        }

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast align-items-center border-0 bg-${type} text-white fade show`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, {
                delay: 3000
            });
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
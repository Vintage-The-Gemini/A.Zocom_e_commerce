<?php
session_start();
require_once 'server/connection.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Zocom Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/cart.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <main class="cart-page">
        <div class="cart-container">
            <div class="cart-header">
                <h1>Shopping Cart</h1>
                <p class="cart-count"><?php echo count($_SESSION['cart']); ?> items</p>
            </div>

            <?php if (empty($_SESSION['cart'])): ?>
                <div class="cart-empty">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Your cart is empty</p>
                    <a href="shop.php" class="btn-shop">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-items">
                    <?php foreach ($_SESSION['cart'] as $productId => $item): ?>
                        <div class="cart-item" data-id="<?php echo $productId; ?>">
                            <div class="item-image">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                    alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                <div class="item-actions">
                                    <div class="quantity-controls">
                                        <button class="qty-btn minus" onclick="updateQuantity(<?php echo $productId; ?>, -1)">-</button>
                                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                                        <button class="qty-btn plus" onclick="updateQuantity(<?php echo $productId; ?>, 1)">+</button>
                                    </div>
                                    <button class="btn btn-danger btn-sm remove-btn" onclick="removeFromCart(<?php echo $productId; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-actions">
                    <button class="btn-clear" onclick="clearCart()">Clear Cart</button>
                    <button class="btn-whatsapp" onclick="orderViaWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Order via WhatsApp
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showOrderNotification() {
            const toast = document.createElement('div');
            toast.className = 'custom-toast success-toast';
            toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-check-circle"></i>
            <span>Cart order initiated! Opening WhatsApp...</span>
        </div>
    `;

            document.querySelector('.toast-container').appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Update your orderViaWhatsApp function
        function orderViaWhatsApp() {
            showOrderNotification();
            // Rest of your existing orderViaWhatsApp code...
        }
    </script>


    <script src="assets/js/navbar.js"></script>
    <script src="assets/js/cart.js"></script>
</body>

</html>
<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    try {
        $product_list = $data['products'];
        $whatsapp_number = "+254782540742"; // Your business WhatsApp number

        $stmt = $conn->prepare("INSERT INTO orders (product_list, whatsapp_number, status) VALUES (?, ?, 'new')");
        $stmt->bind_param("ss", $product_list, $whatsapp_number);

        if ($stmt->execute()) {
            // Clear cart if order was from cart page
            if ($data['from_cart']) {
                $_SESSION['cart'] = [];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Order recorded successfully'
            ]);
        } else {
            throw new Exception("Failed to record order");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

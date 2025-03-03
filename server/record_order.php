<?php
// server/record_order.php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Get JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Default response
$response = [
    'success' => false,
    'message' => 'An error occurred while recording the order'
];

try {
    // Validate input
    if (!isset($data['products'])) {
        throw new Exception('No products provided');
    }

    // Get current timestamp
    $timestamp = date('Y-m-d H:i:s');

    // Get additional data
    $from_cart = isset($data['from_cart']) ? 1 : 0;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $products = $data['products'];

    // Generate a random order reference
    $order_ref = 'WA-' . strtoupper(substr(md5(uniqid()), 0, 8));

    // Store in database if available
    if ($conn) {
        // Check if we have a whatsapp_requests table, create if not
        $check_table = "SHOW TABLES LIKE 'whatsapp_requests'";
        $table_exists = $conn->query($check_table);

        if ($table_exists->num_rows == 0) {
            // Create the table if it doesn't exist
            $create_table = "CREATE TABLE whatsapp_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_ref VARCHAR(20) NOT NULL,
                user_id INT NULL,
                products TEXT NOT NULL,
                from_cart TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->query($create_table);
        }

        // Prepare and execute the insert statement
        $stmt = $conn->prepare("INSERT INTO whatsapp_requests (order_ref, user_id, products, from_cart) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $order_ref, $user_id, $products, $from_cart);

        if ($stmt->execute()) {
            $response = [
                'success' => true,
                'message' => 'Order request recorded successfully',
                'order_ref' => $order_ref
            ];
        } else {
            throw new Exception('Database error: ' . $stmt->error);
        }
    } else {
        // No database connection, but we'll still respond with success
        // since the WhatsApp functionality should still work
        $response = [
            'success' => true,
            'message' => 'Proceeding with WhatsApp order',
            'order_ref' => $order_ref
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];

    // Log the error
    error_log('WhatsApp order error: ' . $e->getMessage());
}

// Return the response
echo json_encode($response);

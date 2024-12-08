<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'update_status':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
            $stmt->bind_param("si", $data['status'], $data['order_id']);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Error updating order status");
            }
            break;

        case 'get_order':
            $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();

            if ($order) {
                echo json_encode(['success' => true, 'order' => $order]);
            } else {
                throw new Exception("Order not found");
            }
            break;

        case 'delete':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->bind_param("i", $data['order_id']);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Error deleting order");
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

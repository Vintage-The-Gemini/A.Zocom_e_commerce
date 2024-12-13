<?php
session_start();
require_once '../server/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    if (!isset($_GET['id'])) {
        throw new Exception('User ID required');
    }

    $user_id = $_GET['id'];
    $query = "SELECT id, username, email, role_id, status FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        throw new Exception('User not found');
    }
} catch (Exception $e) {
    error_log("Error in get_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

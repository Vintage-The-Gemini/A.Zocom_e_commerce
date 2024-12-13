<?php
// admin/delete_user.php
session_start();
require_once '../server/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['user_id'])) {
        throw new Exception('User ID required');
    }

    // Prevent deleting own account
    if ($data['user_id'] == $_SESSION['admin_id']) {
        throw new Exception('Cannot delete your own account');
    }

    // Check if user exists
    $check_stmt = $conn->prepare("SELECT id FROM admin_users WHERE id = ?");
    $check_stmt->bind_param('i', $data['user_id']);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows === 0) {
        throw new Exception('User not found');
    }

    // Delete user
    $stmt = $conn->prepare("DELETE FROM admin_users WHERE id = ?");
    $stmt->bind_param('i', $data['user_id']);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch (Exception $e) {
    error_log("Error in delete_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

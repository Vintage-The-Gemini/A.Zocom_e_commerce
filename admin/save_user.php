<?php
// admin/save_user.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $user_id = $_POST['user_id'] ?? null;
    $is_edit = !empty($user_id);

    // Validate required fields
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['role_id'])) {
        throw new Exception('Username, email and role are required');
    }

    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Check if username/email already exists
    $check_query = "SELECT id FROM admin_users WHERE (username = ? OR email = ?) AND id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('ssi', $_POST['username'], $_POST['email'], $user_id ?: 0);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        throw new Exception('Username or email already exists');
    }

    if ($is_edit) {
        $query = "UPDATE admin_users SET 
                  username = ?, 
                  email = ?,
                  role_id = ?,
                  status = ?";

        $params = [
            $_POST['username'],
            $_POST['email'],
            $_POST['role_id'],
            $_POST['status']
        ];
        $types = "ssis";

        // Add password to update if provided
        if (!empty($_POST['password'])) {
            $query .= ", password = ?";
            $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $types .= "s";
        }

        $query .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
    } else {
        // New user requires password
        if (empty($_POST['password'])) {
            throw new Exception('Password is required for new users');
        }

        $query = "INSERT INTO admin_users (username, email, password, role_id, status) 
                  VALUES (?, ?, ?, ?, ?)";

        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssis",
            $_POST['username'],
            $_POST['email'],
            $password_hash,
            $_POST['role_id'],
            $_POST['status']
        );
    }

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = $is_edit ? 'User updated successfully' : 'User created successfully';
    } else {
        throw new Exception("Database error");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

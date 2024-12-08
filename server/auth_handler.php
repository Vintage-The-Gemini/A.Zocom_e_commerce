<?php
session_start();
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'All fields are required';
        header('Location: ../login.php');
        exit();
    }

    // Check if input is email or username
    $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $sql = "SELECT * FROM admin_users WHERE $field = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = 'Database error';
        header('Location: ../login.php');
        exit();
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];

        // Update last login timestamp if you want
        // $stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
        // $stmt->bind_param('i', $user['id']);
        // $stmt->execute();

        header('Location: ../admin.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid credentials';
        header('Location: ../login.php');
        exit();
    }
}

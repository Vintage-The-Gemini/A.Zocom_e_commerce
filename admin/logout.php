<?php
// admin/logout.php
session_start();

// Record logout time if needed
if (isset($_SESSION['admin_id'])) {
    require_once '../server/connection.php';

    // Optional: Record logout in activity log
    $stmt = $conn->prepare("INSERT INTO user_activity_log (user_id, activity_type, details) VALUES (?, 'logout', 'User logged out')");
    $stmt->bind_param('i', $_SESSION['admin_id']);
    $stmt->execute();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with a logged out message
header('Location: login.php?logout=success');
exit();

<?php
require_once 'server/connection.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Blog ID is required']);
    exit;
}

$blog_id = mysqli_real_escape_string($conn, $_GET['id']);
$query = "SELECT * FROM blogs WHERE id = '$blog_id' AND status = 'published'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$blog = mysqli_fetch_assoc($result);
if (!$blog) {
    echo json_encode(['success' => false, 'message' => 'Blog not found']);
    exit;
}

echo json_encode(['success' => true, 'blog' => $blog]);

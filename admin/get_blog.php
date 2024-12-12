
<?php
// admin/get_blog.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Blog ID required']);
    exit();
}

$blog_id = $_GET['id'];
$query = "SELECT * FROM blogs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $blog_id);
$stmt->execute();
$result = $stmt->get_result();
$blog = $result->fetch_assoc();

if ($blog) {
    echo json_encode(['success' => true, 'blog' => $blog]);
} else {
    echo json_encode(['success' => false, 'message' => 'Blog not found']);
}

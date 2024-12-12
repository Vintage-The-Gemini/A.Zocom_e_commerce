<?php
// admin/delete_blog.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['blog_id'])) {
    echo json_encode(['success' => false, 'message' => 'Blog ID required']);
    exit();
}

try {
    // Get the blog image path before deletion
    $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
    $stmt->bind_param('i', $data['blog_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    // Delete the blog post
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param('i', $data['blog_id']);

    if ($stmt->execute()) {
        // Delete associated image if it exists
        if (!empty($blog['featured_image'])) {
            $image_path = "../" . $blog['featured_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        echo json_encode(['success' => true, 'message' => 'Blog post deleted successfully']);
    } else {
        throw new Exception("Error deleting blog post");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

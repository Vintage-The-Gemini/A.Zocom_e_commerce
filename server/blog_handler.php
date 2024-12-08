<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Handle image upload
function handleImageUpload($file)
{
    $target_dir = "../uploads/blogs/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filename = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "uploads/blogs/" . $filename;
    }
    throw new Exception('Failed to upload image');
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'add':
            // Handle image upload if provided
            $image_path = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $image_path = handleImageUpload($_FILES['featured_image']);
            }

            $stmt = $conn->prepare("INSERT INTO blogs (title, excerpt, content, category, author, featured_image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "sssssss",
                $_POST['title'],
                $_POST['excerpt'],
                $_POST['content'],
                $_POST['category'],
                $_POST['author'],
                $image_path,
                $_POST['status']
            );

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Blog post added successfully']);
            } else {
                throw new Exception("Error adding blog post");
            }
            break;

        case 'update':
            $set_fields = [
                "title = ?",
                "excerpt = ?",
                "content = ?",
                "category = ?",
                "author = ?",
                "status = ?"
            ];
            $params = [
                $_POST['title'],
                $_POST['excerpt'],
                $_POST['content'],
                $_POST['category'],
                $_POST['author'],
                $_POST['status']
            ];
            $types = "ssssss";

            // Handle image update if new image provided
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $image_path = handleImageUpload($_FILES['featured_image']);
                $set_fields[] = "featured_image = ?";
                $params[] = $image_path;
                $types .= "s";
            }

            $params[] = $_POST['blog_id'];
            $types .= "i";

            $query = "UPDATE blogs SET " . implode(", ", $set_fields) . " WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Blog post updated successfully']);
            } else {
                throw new Exception("Error updating blog post");
            }
            break;

        case 'delete':
            $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
            $stmt->bind_param("i", $_POST['id']);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Blog post deleted successfully']);
            } else {
                throw new Exception("Error deleting blog post");
            }
            break;

        case 'get':
            $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->bind_param("i", $_GET['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $blog = $result->fetch_assoc();

            if ($blog) {
                echo json_encode(['success' => true, 'blog' => $blog]);
            } else {
                throw new Exception("Blog post not found");
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

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
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    if (!getimagesize($file["tmp_name"])) {
        throw new Exception('File is not an image');
    }

    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        throw new Exception('File is too large');
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        throw new Exception('Only JPG, JPEG, PNG & GIF files are allowed');
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "uploads/blogs/" . $filename;
    }
    throw new Exception('Failed to upload image');
}

try {
    // Handle both POST and GET requests
    $action = '';
    $input = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if the request is multipart/form-data or JSON
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $input = $_POST;
        } else {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
            $action = $input['action'] ?? '';
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        $input = $_GET;
    }

    switch ($action) {
        case 'add':
            // Handle image upload if provided
            $image_path = null;
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $image_path = handleImageUpload($_FILES['featured_image']);
            }

            $stmt = $conn->prepare("INSERT INTO blogs (title, excerpt, content, category, author, featured_image, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

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
                echo json_encode([
                    'success' => true,
                    'message' => 'Blog post added successfully',
                    'blog_id' => $stmt->insert_id
                ]);
            } else {
                throw new Exception("Error adding blog post: " . $stmt->error);
            }
            break;

        case 'update':
            $set_fields = [
                "title = ?",
                "excerpt = ?",
                "content = ?",
                "category = ?",
                "author = ?",
                "status = ?",
                "updated_at = NOW()"
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
                echo json_encode([
                    'success' => true,
                    'message' => 'Blog post updated successfully'
                ]);
            } else {
                throw new Exception("Error updating blog post: " . $stmt->error);
            }
            break;

        case 'delete':
            // First get the image path to delete the file
            $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
            $blog_id = isset($_POST['id']) ? $_POST['id'] : $input['id'];
            $stmt->bind_param("i", $blog_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $blog = $result->fetch_assoc();

            // Delete the blog post
            $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
            $stmt->bind_param("i", $blog_id);

            if ($stmt->execute()) {
                // Delete the image file if it exists
                if ($blog && $blog['featured_image']) {
                    $image_path = "../" . $blog['featured_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                echo json_encode([
                    'success' => true,
                    'message' => 'Blog post deleted successfully'
                ]);
            } else {
                throw new Exception("Error deleting blog post: " . $stmt->error);
            }
            break;

        case 'get':
            $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->bind_param("i", $input['id']);
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
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

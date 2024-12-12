<?php
// admin/save_blog.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $blog_id = $_POST['blog_id'] ?? null;
    $is_edit = !empty($blog_id);

    // Handle featured image upload
    $image_path = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['size'] > 0) {
        $target_dir = "../uploads/blogs/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["featured_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // If editing, remove old image
        if ($is_edit) {
            $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
            $stmt->bind_param('i', $blog_id);
            $stmt->execute();
            $old_image = $stmt->get_result()->fetch_assoc()['featured_image'];
            if ($old_image && file_exists("../" . $old_image)) {
                unlink("../" . $old_image);
            }
        }

        if (move_uploaded_file($_FILES["featured_image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/blogs/" . $new_filename;
        } else {
            throw new Exception("Error uploading image");
        }
    }

    if ($is_edit) {
        // If editing and no new image uploaded, keep existing image
        if (empty($image_path)) {
            $stmt = $conn->prepare("SELECT featured_image FROM blogs WHERE id = ?");
            $stmt->bind_param('i', $blog_id);
            $stmt->execute();
            $current_blog = $stmt->get_result()->fetch_assoc();
            $image_path = $current_blog['featured_image'];
        }

        $query = "UPDATE blogs SET 
                  title = ?, 
                  excerpt = ?,
                  content = ?,
                  featured_image = ?,
                  author = ?,
                  category = ?,
                  status = ?,
                  updated_at = CURRENT_TIMESTAMP
                  WHERE id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssssssi",
            $_POST['title'],
            $_POST['excerpt'],
            $_POST['content'],
            $image_path,
            $_POST['author'],
            $_POST['category'],
            $_POST['status'],
            $blog_id
        );
    } else {
        $query = "INSERT INTO blogs (
                    title, 
                    excerpt,
                    content,
                    featured_image,
                    author,
                    category,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssssss",
            $_POST['title'],
            $_POST['excerpt'],
            $_POST['content'],
            $image_path,
            $_POST['author'],
            $_POST['category'],
            $_POST['status']
        );
    }

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = $is_edit ? 'Blog post updated successfully' : 'Blog post created successfully';
    } else {
        throw new Exception("Error saving blog post");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

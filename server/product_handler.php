<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

function handleImageUpload($file, $category)
{
    $target_dir = "../uploads/" . strtolower(str_replace(' ', '_', $category)) . "/";

    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filename = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return "uploads/" . strtolower(str_replace(' ', '_', $category)) . "/" . $filename;
    }
    throw new Exception('Failed to upload image');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];

    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                // Handle image upload
                if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== 0) {
                    throw new Exception('Invalid image upload');
                }

                $image_path = handleImageUpload($_FILES['product_image'], $_POST['category']);

                // Insert product with all fields including links
                $stmt = $conn->prepare("INSERT INTO products (product_name, product_brand, category, product_description, links, product_price, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "sssssds",
                    $_POST['product_name'],
                    $_POST['product_brand'],
                    $_POST['category'],
                    $_POST['product_description'],
                    $_POST['links'],
                    $_POST['product_price'],
                    $image_path
                );

                if (!$stmt->execute()) {
                    throw new Exception("Error executing query: " . $stmt->error);
                }

                $response = [
                    'success' => true,
                    'message' => 'Product added successfully',
                    'product_id' => $conn->insert_id
                ];
                break;

            case 'edit':
                $image_path = null;
                $params = [];
                $types = "";

                // Base SQL for update
                $sql_parts = [
                    "product_name = ?",
                    "product_brand = ?",
                    "category = ?",
                    "product_description = ?",
                    "links = ?",
                    "product_price = ?"
                ];

                // Add base parameters
                $params = [
                    $_POST['product_name'],
                    $_POST['product_brand'],
                    $_POST['category'],
                    $_POST['product_description'],
                    $_POST['links'],
                    $_POST['product_price']
                ];
                $types = "sssssd";

                // Handle image upload if new image is provided
                if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
                    $image_path = handleImageUpload($_FILES['product_image'], $_POST['category']);
                    $sql_parts[] = "product_image = ?";
                    $params[] = $image_path;
                    $types .= "s";
                }

                // Add product_id to parameters
                $params[] = $_POST['product_id'];
                $types .= "i";

                // Construct final SQL
                $sql = "UPDATE products SET " . implode(", ", $sql_parts) . " WHERE product_id = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);

                if (!$stmt->execute()) {
                    throw new Exception("Error updating product: " . $stmt->error);
                }

                $response = ['success' => true, 'message' => 'Product updated successfully'];
                break;

            case 'delete':
                // Get the product image path before deleting
                $stmt = $conn->prepare("SELECT product_image FROM products WHERE product_id = ?");
                $stmt->bind_param("i", $_POST['product_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();

                // Delete the product
                $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->bind_param("i", $_POST['product_id']);

                if (!$stmt->execute()) {
                    throw new Exception("Error deleting product: " . $stmt->error);
                }

                // Delete the image file if it exists
                if ($product && $product['product_image']) {
                    $image_path = "../" . $product['product_image'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }

                $response = ['success' => true, 'message' => 'Product deleted successfully'];
                break;

            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'error' => $e->getMessage()];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle GET requests for retrieving products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->prepare("SELECT * FROM products ORDER BY product_id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'products' => $products]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

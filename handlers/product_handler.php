<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

function handleImageUpload($file, $category)
{
    $folder_name = str_replace(' ', '_', $category);
    $target_dir = "../" . $folder_name . "/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filename = time() . '_' . basename($file["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $folder_name . "/" . $filename;
    }

    throw new Exception('Failed to upload file');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false];

    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                if (empty($_FILES['product_image']['name'])) {
                    throw new Exception('Product image is required');
                }

                $image_path = handleImageUpload($_FILES['product_image'], $_POST['category']);

                $stmt = $conn->prepare("INSERT INTO products (product_name, product_brand, category, product_price, product_image, product_description) VALUES (?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "sssdss",
                    $_POST['product_name'],
                    $_POST['product_brand'],
                    $_POST['category'],
                    $_POST['product_price'],
                    $image_path,
                    $_POST['product_description']
                );

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Product added successfully'];
                } else {
                    throw new Exception($stmt->error);
                }
                break;

            case 'delete':
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['productId'])) {
                    throw new Exception('Product ID is required');
                }

                $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
                $stmt->bind_param("i", $data['productId']);

                if ($stmt->execute()) {
                    $response = ['success' => true, 'message' => 'Product deleted successfully'];
                } else {
                    throw new Exception($stmt->error);
                }
                break;
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'error' => $e->getMessage()];
    }

    echo json_encode($response);
}

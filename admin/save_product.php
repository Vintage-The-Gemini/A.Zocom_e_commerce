<?php
// admin/save_product.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    $product_id = $_POST['product_id'] ?? null;
    $is_edit = !empty($product_id);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['size'] > 0) {
        $target_dir = "../images/products/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_path = "images/products/" . $new_filename;
        } else {
            throw new Exception("Error uploading file");
        }
    }

    if ($is_edit) {
        // If editing and no new image uploaded, keep existing image
        if (empty($image_path)) {
            $stmt = $conn->prepare("SELECT product_image FROM products WHERE product_id = ?");
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_product = $result->fetch_assoc();
            $image_path = $current_product['product_image'];
        }

        $query = "UPDATE products SET 
                  product_name = ?, 
                  product_brand = ?,
                  category = ?,
                  product_description = ?,
                  links = ?,
                  product_image = ?
                  WHERE product_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssi",
            $_POST['product_name'],
            $_POST['product_brand'],
            $_POST['category'],
            $_POST['product_description'],
            $_POST['links'],
            $image_path,
            $product_id
        );
    } else {
        // For new products, use default image if none uploaded
        if (empty($image_path)) {
            $image_path = "images/default-product.jpg";
        }

        $query = "INSERT INTO products (
                    product_name, 
                    product_brand, 
                    category,
                    product_description,
                    links,
                    product_image
                ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssss",
            $_POST['product_name'],
            $_POST['product_brand'],
            $_POST['category'],
            $_POST['product_description'],
            $_POST['links'],
            $image_path
        );
    }

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = $is_edit ? 'Product updated successfully' : 'Product added successfully';
    } else {
        throw new Exception("Error executing query: " . $stmt->error);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

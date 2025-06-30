<?php
// admin/save_product.php - Enhanced version with comprehensive error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production
ini_set('log_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Start output buffering to prevent any accidental output
ob_start();

session_start();

$response = ['success' => false, 'message' => '', 'debug' => []];

try {
    // Include database connection with error handling
    if (!file_exists('../server/connection.php')) {
        throw new Exception('Database connection file not found');
    }
    
    require_once '../server/connection.php';

    // Check if database connection exists
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }

    // Check admin authentication
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access - Please log in');
    }

    // Validate required POST data
    $required_fields = ['product_name', 'product_brand', 'category', 'product_description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field '{$field}' is required");
        }
    }

    $product_id = !empty($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $is_edit = !empty($product_id);
    
    $response['debug'][] = "Operation: " . ($is_edit ? 'Edit' : 'Create');
    $response['debug'][] = "Product ID: " . ($product_id ?: 'New');

    // Handle image upload with comprehensive error checking
    $image_path = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        
        // Check for upload errors
        if ($_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File too large (php.ini limit)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
                UPLOAD_ERR_PARTIAL => 'File upload incomplete',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            
            $error_message = $upload_errors[$_FILES['product_image']['error']] ?? 'Unknown upload error';
            throw new Exception("Image upload failed: " . $error_message);
        }

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_extension = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', $allowed_types));
        }

        // Check file size (5MB limit)
        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size too large. Maximum 5MB allowed");
        }

        // Create target directory with proper permissions
        $target_dir = "../images/products/";
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }

        if (!is_writable($target_dir)) {
            throw new Exception("Upload directory is not writable");
        }

        // Generate unique filename
        $new_filename = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_path = "images/products/" . $new_filename;
            $response['debug'][] = "Image uploaded: " . $new_filename;
        } else {
            throw new Exception("Failed to move uploaded file");
        }
    }

    // Database operations
    if ($is_edit) {
        // Verify product exists
        $check_stmt = $conn->prepare("SELECT product_id, product_image FROM products WHERE product_id = ?");
        if (!$check_stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $check_stmt->bind_param('i', $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Product not found for editing");
        }
        
        $current_product = $result->fetch_assoc();
        
        // If no new image uploaded, keep existing image
        if (empty($image_path)) {
            $image_path = $current_product['product_image'];
        } else {
            // Delete old image if a new one was uploaded
            $old_image_path = "../" . $current_product['product_image'];
            if (file_exists($old_image_path) && $current_product['product_image'] !== 'images/default-product.jpg') {
                unlink($old_image_path);
                $response['debug'][] = "Old image deleted";
            }
        }

        // Update query
        $query = "UPDATE products SET 
                  product_name = ?, 
                  product_brand = ?,
                  category = ?,
                  product_description = ?,
                  links = ?,
                  product_image = ?
                  WHERE product_id = ?";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Update prepare error: " . $conn->error);
        }

        $links = $_POST['links'] ?? '';
        $stmt->bind_param(
            "ssssssi",
            $_POST['product_name'],
            $_POST['product_brand'],
            $_POST['category'],
            $_POST['product_description'],
            $links,
            $image_path,
            $product_id
        );

    } else {
        // Insert new product
        
        // Use default image if none uploaded
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
        if (!$stmt) {
            throw new Exception("Insert prepare error: " . $conn->error);
        }

        $links = $_POST['links'] ?? '';
        $stmt->bind_param(
            "ssssss",
            $_POST['product_name'],
            $_POST['product_brand'],
            $_POST['category'],
            $_POST['product_description'],
            $links,
            $image_path
        );
    }

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Database execution error: " . $stmt->error);
    }

    $response['success'] = true;
    $response['message'] = $is_edit ? 'Product updated successfully' : 'Product added successfully';
    
    if (!$is_edit) {
        $response['product_id'] = $conn->insert_id;
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    $response['debug'][] = "Error: " . $e->getMessage();
    
    // Log the error for debugging
    error_log("Product save error: " . $e->getMessage() . " | POST data: " . json_encode($_POST));
    
} catch (Error $e) {
    $response['message'] = "System error occurred";
    $response['debug'][] = "Fatal error: " . $e->getMessage();
    
    // Log fatal errors
    error_log("Product save fatal error: " . $e->getMessage());
}

// Clean output buffer and send JSON response
ob_end_clean();
echo json_encode($response);
exit();
?>
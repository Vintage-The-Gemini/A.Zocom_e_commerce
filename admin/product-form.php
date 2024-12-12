<?php
// admin/product-form.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$product = null;
$is_edit = false;

// Check if editing existing product
if (isset($_GET['id'])) {
    $is_edit = true;
    $product_id = $_GET['id'];
    $query = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        header('Location: products.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $product_brand = $_POST['product_brand'];
    $category = $_POST['category'];
    $product_description = $_POST['product_description'];
    $links = $_POST['links'];

    if ($is_edit) {
        // Handle image update
        $image_path = $product['product_image']; // Keep existing image by default
        if (isset($_FILES['product_image']) && $_FILES['product_image']['size'] > 0) {
            $target_dir = "../images/products/";
            $image_path = $target_dir . basename($_FILES["product_image"]["name"]);
            move_uploaded_file($_FILES["product_image"]["tmp_name"], $image_path);
        }

        // Update existing product
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
            'ssssssi',
            $product_name,
            $product_brand,
            $category,
            $product_description,
            $links,
            $image_path,
            $product_id
        );
    } else {
        // Handle new image upload
        $target_dir = "../images/products/";
        $image_path = $target_dir . basename($_FILES["product_image"]["name"]);
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $image_path);

        // Insert new product
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
            'ssssss',
            $product_name,
            $product_brand,
            $category,
            $product_description,
            $links,
            $image_path
        );
    }

    if ($stmt->execute()) {
        header('Location: products.php');
        exit();
    }
}

// Get categories for dropdown
$categories = [
    'Foot Protection',
    'Head Protection',
    'Hand Protection',
    'Eye Protection',
    'Respiratory Protection',
    'Fall Protection',
    'Body Protection',
    'Fabrics'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product - Zocom Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Include the same styles as before */
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: var(--card-shadow);
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: contain;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 col-lg-10 ms-sm-auto main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?php echo $is_edit ? 'Edit' : 'Add New'; ?> Product</h1>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>

                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name"
                                value="<?php echo $product ? htmlspecialchars($product['product_name']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="product_brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="product_brand" name="product_brand"
                                value="<?php echo $product ? htmlspecialchars($product['product_brand']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>"
                                        <?php echo ($product && $product['category'] == $cat) ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="product_description" class="form-label">Description</label>
                            <textarea class="form-control" id="product_description" name="product_description"
                                rows="4" required><?php echo $product ? htmlspecialchars($product['product_description']) : ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="links" class="form-label">Product Links</label>
                            <input type="text" class="form-control" id="links" name="links"
                                value="<?php echo $product ? htmlspecialchars($product['links']) : ''; ?>">
                            <small class="text-muted">Add relevant product links (optional)</small>
                        </div>

                        <div class="mb-3">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="product_image" name="product_image"
                                accept="image/*" <?php echo $is_edit ? '' : 'required'; ?>>
                            <?php if ($product && $product['product_image']): ?>
                                <img src="<?php echo htmlspecialchars($product['product_image']); ?>"
                                    alt="Current product image" class="preview-image">
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            <?php echo $is_edit ? 'Update' : 'Add'; ?> Product
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
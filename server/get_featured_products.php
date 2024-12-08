<?php
include('connection.php');

function getProductsByCategory($conn, $category)
{
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return null;
    }

    $stmt->bind_param("s", $category);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return null;
    }

    return $stmt->get_result();
}

$categories = [
    'Foot protection',
    'Eye protection',
    'Head protection',
    'Hand protection',
    'Fall protection',
    'Respiratory protection',
    'Ear protection',
    'Body protection',
    'Fabrics'
];

foreach ($categories as $category) {
    $variable_name = strtolower(str_replace(' ', '_', $category));
    ${$variable_name} = getProductsByCategory($conn, $category);
}

$conn->close();

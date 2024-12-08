<?php
require_once 'server/connection.php';

// First create the table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql)) {
    echo "Table admin_users created or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Now create the admin user
$username = "admin";
$email = "admin@zocom.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);

// Check if admin exists
$check = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");

if ($check->num_rows > 0) {
    // Update existing admin
    $sql = "UPDATE admin_users SET password = ? WHERE username = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $password);
    if ($stmt->execute()) {
        echo "Admin password updated successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error updating admin: " . $conn->error . "<br>";
    }
} else {
    // Create new admin
    $sql = "INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $password);
    if ($stmt->execute()) {
        echo "Admin user created successfully<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error creating admin: " . $conn->error . "<br>";
    }
}

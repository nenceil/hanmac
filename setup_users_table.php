<?php
include 'includes/db.php';

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table users created successfully<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Insert default admin user if not exists
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Default password: admin123

$check = $conn->query("SELECT * FROM users WHERE username = '$username'");
if ($check->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        echo "Default admin user created (admin/admin123)<br>";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
} else {
    echo "Admin user already exists<br>";
}

$conn->close();
?>

<?php
include 'includes/db.php';

// Add image column to categories if it doesn't exist
$sql = "SHOW COLUMNS FROM categories LIKE 'image'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE categories ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER display_order";
    if ($conn->query($sql)) {
        echo "Column 'image' added to 'categories' table.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "Column 'image' already exists in 'categories' table.<br>";
}

echo "Database maintenance complete.";
?>

<?php
include 'includes/db.php';

// Add image columns
$alter_sql = "ALTER TABLE products 
ADD COLUMN image2 VARCHAR(255) AFTER image,
ADD COLUMN image3 VARCHAR(255) AFTER image2,
ADD COLUMN image4 VARCHAR(255) AFTER image3,
ADD COLUMN image5 VARCHAR(255) AFTER image4";

if ($conn->query($alter_sql) === TRUE) {
    echo "Table 'products' updated successfully to support 5 images.";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>

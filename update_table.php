<?php
include 'includes/db.php';

$res = $conn->query("SHOW COLUMNS FROM products LIKE 'specifications'");
if ($res->num_rows == 0) {
    $sql = "ALTER TABLE products ADD COLUMN specifications TEXT AFTER spec_ip_grade";
    if ($conn->query($sql) === TRUE) {
        echo "Column specifications added successfully\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
} else {
    echo "Column specifications already exists\n";
}

$conn->close();
?>

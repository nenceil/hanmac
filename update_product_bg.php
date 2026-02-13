<?php
include 'includes/db.php';

$sql = "UPDATE products SET bg_color = '#ffffff'";
if ($conn->query($sql) === TRUE) {
    echo "All products background color updated to #ffffff.<br>";
} else {
    echo "Error updating records: " . $conn->error . "<br>";
}

echo "Database cleanup complete.";
?>

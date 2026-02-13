<?php
include 'includes/db.php';

$username = 'admin';
$newPassword = password_hash('gdi2026', PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
$stmt->bind_param("ss", $newPassword, $username);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Password for user 'admin' has been successfully updated to 'gdi2026'.<br>";
    } else {
        echo "User 'admin' not found or password already set to this value.<br>";
    }
} else {
    echo "Error updating password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

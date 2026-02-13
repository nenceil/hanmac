<?php
include 'includes/db.php';

$res = $conn->query("SELECT id, specifications FROM products");

while ($row = $res->fetch_assoc()) {
    $specs = [];
    if (!empty($row['specifications'])) {
        $specs = json_decode($row['specifications'], true);
    }

    // Check if "Kode" already exists
    $hasKode = false;
    foreach ($specs as $s) {
        if ($s['name'] === 'Kode') {
            $hasKode = true;
            break;
        }
    }

    if (!$hasKode) {
        // Prepend "Kode" to the beginning of the array
        array_unshift($specs, ['name' => 'Kode', 'value' => '']);
        
        $json = json_encode($specs);
        $stmt = $conn->prepare("UPDATE products SET specifications = ? WHERE id = ?");
        $stmt->bind_param("si", $json, $row['id']);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Standard 'Kode' specification added to all products successfully.<br>";
echo "Database maintenance complete.";
?>

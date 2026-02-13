<?php
include 'includes/db.php';

$res = $conn->query("SELECT id, specifications FROM products");

$count = 0;
while ($row = $res->fetch_assoc()) {
    $specs = [];
    if (!empty($row['specifications'])) {
        $specs = json_decode($row['specifications'], true);
    }

    $changed = false;
    foreach ($specs as &$s) {
        if ($s['name'] === 'Kode') {
            $s['name'] = 'Code';
            $changed = true;
        }
    }

    if ($changed) {
        $json = json_encode($specs);
        $stmt = $conn->prepare("UPDATE products SET specifications = ? WHERE id = ?");
        $stmt->bind_param("si", $json, $row['id']);
        $stmt->execute();
        $stmt->close();
        $count++;
    }
}

echo "$count products updated from 'Kode' to 'Code'.<br>";
echo "Database maintenance complete.";
?>

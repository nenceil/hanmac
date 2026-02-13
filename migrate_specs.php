<?php
include 'includes/db.php';

$res = $conn->query("SELECT id, spec_color, spec_size, spec_hole_size, spec_material, spec_fitting, spec_ip_grade, specifications FROM products");

while ($row = $res->fetch_assoc()) {
    $specs = [];
    if (!empty($row['specifications'])) {
        $specs = json_decode($row['specifications'], true);
    }

    $mapping = [
        'Color' => $row['spec_color'],
        'Size' => $row['spec_size'],
        'Hole Size' => $row['spec_hole_size'],
        'Material' => $row['spec_material'],
        'Fitting' => $row['spec_fitting'],
        'IP Grade' => $row['spec_ip_grade']
    ];

    $changed = false;
    foreach ($mapping as $name => $value) {
        if (!empty($value)) {
            // Check if already in specs
            $exists = false;
            foreach ($specs as $s) {
                if ($s['name'] === $name) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $specs[] = ['name' => $name, 'value' => $value];
                $changed = true;
            }
        }
    }

    if ($changed) {
        $json = json_encode($specs);
        $stmt = $conn->prepare("UPDATE products SET specifications = ? WHERE id = ?");
        $stmt->bind_param("si", $json, $row['id']);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Data migration completed successfully.\n";
$conn->close();
?>

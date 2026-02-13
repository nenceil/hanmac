<?php
include 'includes/db.php';

// Drop table if exists to start fresh (optional, good for dev)
$conn->query("DROP TABLE IF EXISTS products");

// Create table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    image VARCHAR(255),
    description TEXT,
    featured BOOLEAN DEFAULT FALSE,
    bg_color VARCHAR(50),
    spec_color VARCHAR(100),
    spec_size VARCHAR(100),
    spec_hole_size VARCHAR(100),
    spec_material VARCHAR(100),
    spec_fitting VARCHAR(100),
    spec_ip_grade VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table products created successfully<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Prepare statement
$stmt = $conn->prepare("INSERT INTO products (slug, name, category, image, description, featured, bg_color, spec_color, spec_size, spec_hole_size, spec_material, spec_fitting, spec_ip_grade) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssisssssss", $slug, $name, $category, $image, $description, $featured, $bg_color, $spec_color, $spec_size, $spec_hole_size, $spec_material, $spec_fitting, $spec_ip_grade);

$products = [
    [
        'aura-pendant', 'Aura Pendant Light', 'LIGHTING', 'assets/images/feat_pendant.png', 'An elegant pendant light that brings warmth to any room with its modern design.', 1, '#f5f5f5', 
        'Sand Black, Sand White', '30 x 40cm', '20cm', 'Aluminium', 'MR16', 'IP20'
    ],
    [
        'lumina-desk', 'Lumina Desk Lamp', 'LAMPS', 'assets/images/feat_desk.png', 'Perfect for your workspace, this desk lamp offers adjustable brightness and sleek style.', 1, NULL, 
        'Matte Black', '15 x 35cm', 'N/A', 'Steel', 'E27', 'IP20'
    ],
    [
        'arc-floor', 'Arc Floor Stand', 'FLOOR', 'assets/images/feat_floor.png', 'A statement piece for your living room, providing focused lighting with a dramatic arc.', 1, NULL, 
        'Chrome', '180cm Height', 'N/A', 'Stainless Steel', 'E27', 'IP20'
    ],
    [
        'halo-wall', 'Halo Wall Sconce', 'WALL', 'assets/images/feat_sconce.png', 'Minimalist wall sconce that creates a soft halo effect, ideal for hallways and bedrooms.', 1, NULL, 
        'Gold / Black', '25cm Diameter', 'N/A', 'Brass', 'LED Integrated', 'IP20'
    ],
    [
        'sphere-pendant', 'Sphere Pendant', 'LIGHTING', 'assets/images/cat_ceiling_lamp.png', 'Geometric perfection in a hanging light, suitable for modern dining areas.', 1, NULL, 
        'White Glass', '40cm Diameter', 'N/A', 'Glass & Metal', 'E27', 'IP20'
    ],
    [
        'studio-lamp', 'Studio Lamp', 'LAMPS', 'assets/images/cat_table_lamp.png', 'Professional grade lighting for your studio or reading nook.', 1, NULL, 
        'Industrial Grey', '20 x 40cm', 'N/A', 'Metal', 'E27', 'IP20'
    ],
    [
        'garden-wall', 'Garden Wall Light', 'OUTDOOR', 'assets/images/cat_wall_lamp.png', 'Durable and weather-resistant lighting to illuminate your outdoor spaces.', 0, NULL, 
        'Graphite', '15 x 25cm', 'N/A', 'Aluminium', 'LED', 'IP65'
    ],
    [
        'minimal-floor', 'Minimal Floor Lamp', 'FLOOR', 'assets/images/cat_floor_lamp.png', 'Sleek and unobtrusive, adding light without taking up visual space.', 0, NULL, 
        'Black', '160cm Height', 'N/A', 'Aluminium', 'LED', 'IP20'
    ]
];

foreach ($products as $p) {
    $slug = $p[0];
    $name = $p[1];
    $category = $p[2];
    $image = $p[3];
    $description = $p[4];
    $featured = $p[5];
    $bg_color = $p[6];
    $spec_color = $p[7];
    $spec_size = $p[8];
    $spec_hole_size = $p[9];
    $spec_material = $p[10];
    $spec_fitting = $p[11];
    $spec_ip_grade = $p[12];
    
    if (!$stmt->execute()) {
        echo "Error inserting $slug: " . $stmt->error . "<br>";
    } else {
        echo "Inserted $slug<br>";
    }
}

echo "Database setup complete.";
$stmt->close();
$conn->close();
?>

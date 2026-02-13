<?php
include_once 'includes/db.php';
include_once 'includes/lang_load.php';

$slug = isset($_GET['id']) ? $_GET['id'] : '';
$product = null;

if ($slug) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

if (!$product) {
    // Redirect or show error if product not found
    header("Location: catalog.php");
    exit;
}

$productName = ($current_lang == 'en' && !empty($product['name_en'])) ? $product['name_en'] : $product['name'];
$pageTitle = $productName . ' - ' . __('all_products') . ' - Hanmac Lighting';
// $currentPage can be left unset or set to 'product'
include 'includes/header.php';
?>

    <main class="product-detail-page">
        <div class="product-detail-container">
            <!-- Breadcrumb Navigation removed -->

            <!-- Product Title on Top -->
            <div class="product-title-container">
                <?php
                $productCode = 'N/A';
                if (!empty($product['specifications'])) {
                    $specs_data = json_decode($product['specifications'], true);
                    
                    if (!empty($specs_data)) {
                        // Check if new structure (variants): first item has 'specs' key
                        if (isset($specs_data[0]['specs'])) {
                            $codes = [];
                            // Loop through ALL variants to collect codes
                            foreach ($specs_data as $variant) {
                                if (isset($variant['specs'])) {
                                    foreach ($variant['specs'] as $s) {
                                        if (isset($s['name']) && $s['name'] === 'Code' && !empty($s['value'])) {
                                            $codes[] = $s['value'];
                                            break; // Found code for this variant
                                        }
                                    }
                                }
                            }
                            if (!empty($codes)) {
                                $productCode = implode(' | ', $codes);
                            }
                        } else {
                            // Legacy flat structure
                            foreach ($specs_data as $s) {
                                if (isset($s['name']) && $s['name'] === 'Code' && !empty($s['value'])) {
                                    $productCode = $s['value'];
                                    break;
                                }
                            }
                        }
                    }
                }
                ?>
                <h1 class="product-title-large">
                    <span style="color: #999; font-weight: 400;"><?php echo htmlspecialchars($productCode); ?></span>
                    <?php echo htmlspecialchars($productName); ?>
                </h1>
            </div>

            <!-- Left Side: Images -->
            <div class="product-media">
                <?php
                // Collect all valid images
                $images = [];
                if (!empty($product['image'])) $images[] = $product['image'];
                if (!empty($product['image2'])) $images[] = $product['image2'];
                if (!empty($product['image3'])) $images[] = $product['image3'];
                if (!empty($product['image4'])) $images[] = $product['image4'];
                if (!empty($product['image5'])) $images[] = $product['image5'];
                ?>
                
                <div class="main-image-frame" style="background-color: <?php echo $product['bg_color'] ? $product['bg_color'] : '#f9f9f9'; ?>;">
                    <?php if (count($images) > 1): ?>
                        <button class="slider-btn prev-btn" onclick="moveSlider(-1)">&#10094;</button>
                    <?php endif; ?>
                    
                    <img src="<?php echo $images[0]; ?>" alt="<?php echo $product['name']; ?>" id="mainImage" data-index="0">
                    
                    <?php if (count($images) > 1): ?>
                        <button class="slider-btn next-btn" onclick="moveSlider(1)">&#10095;</button>
                    <?php endif; ?>
                </div>

                <!-- Thumbnails -->
                <?php if (count($images) > 0): ?>
                <div class="image-thumbnails">
                    <?php foreach ($images as $index => $img): ?>
                    <div class="thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo $img; ?>', <?php echo $index; ?>)">
                        <img src="<?php echo $img; ?>" alt="View <?php echo $index + 1; ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- Pass images to JS for the global slider functions -->
                <script>
                    window.productImages = <?php echo json_encode($images); ?>;
                </script>
            </div>

            <!-- Right Side: Info -->
            <div class="product-info-panel">
                <!-- Title moved above -->


                <div class="tech-specs-card">
                    <h2 class="specs-heading"><?php echo __('detail_specifications'); ?></h2>

                    <?php
                    $raw_specs = ($product && !empty($product['specifications'])) ? json_decode($product['specifications'], true) : [];
                    $variants = [];

                    // Check if new structure (array of variants)
                    $is_new_structure = !empty($raw_specs) && isset($raw_specs[0]['specs']);

                    if ($is_new_structure) {
                        $variants = $raw_specs;
                    } else {
                        // Legacy: treat as single variant
                        if (!empty($raw_specs)) {
                             $variants[] = [
                                'name' => 'General',
                                'specs' => $raw_specs
                            ];
                        }
                    }
                    ?>

                    <?php if (count($variants) > 0): ?>
                        <?php if (count($variants) > 1): ?>
                        <div class="variant-tabs">
                            <?php foreach ($variants as $index => $variant): ?>
                                <button class="variant-tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                                        onclick="switchVariant(<?php echo $index; ?>)">
                                    <?php echo htmlspecialchars($variant['name']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="specs-content">
                            <?php foreach ($variants as $index => $variant): ?>
                                <div id="variant-panel-<?php echo $index; ?>" 
                                     class="variant-panel <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <div class="specs-table">
                                        <?php if (!empty($variant['specs'])): 
                                            foreach ($variant['specs'] as $spec): ?>
                                            <div class="spec-row">
                                                <span class="spec-label"><?php echo htmlspecialchars($spec['name']); ?></span>
                                                <span class="spec-value"><?php echo htmlspecialchars($spec['value']); ?></span>
                                            </div>
                                        <?php endforeach; endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No specifications available.</p>
                    <?php endif; ?>
                </div>

                <style>
                    .variant-tabs {
                        display: flex;
                        gap: 10px;
                        margin-bottom: 20px;
                        flex-wrap: wrap;
                    }
                    .variant-tab {
                        background: none;
                        border: 1px solid #333;
                        color: #333;
                        padding: 10px 20px;
                        cursor: pointer;
                        font-family: inherit;
                        font-size: 0.9rem;
                        transition: all 0.3s ease;
                    }
                    .variant-tab.active {
                        background: #1a1a1a;
                        color: #fff;
                        border-color: #1a1a1a;
                    }
                    .variant-panel {
                        display: none;
                        animation: fadeIn 0.3s ease-in-out;
                    }
                    .variant-panel.active {
                        display: block;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(5px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                </style>    

                <script>
                function switchVariant(index) {
                    // Update tabs
                    document.querySelectorAll('.variant-tab').forEach((tab, i) => {
                        if (i === index) tab.classList.add('active');
                        else tab.classList.remove('active');
                    });
                    
                    // Update panels
                    document.querySelectorAll('.variant-panel').forEach((panel, i) => {
                         if (i === index) panel.classList.add('active');
                        else panel.classList.remove('active');
                    });
                }
                </script>
                
                <div style="margin-top: 2rem;">
                    <a href="catalog.php" class="back-link">← <?php echo __('view_all_products'); ?></a>
                </div>
            </div>
        </div>
    </main>

<?php include 'includes/footer.php'; ?>

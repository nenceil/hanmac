<?php
include 'auth.php';
include_once '../includes/db.php';
// Ensure language is loaded
include_once '../includes/lang_load.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$product = null;

// Fetch categories for the dropdown
$categories_res = $conn->query("SELECT * FROM categories ORDER BY display_order ASC");
$category_list = [];
while ($cat = $categories_res->fetch_assoc()) {
    $category_list[] = $cat;
}

if ($id) {
    // Fetch existing product data
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}

// Function to handle image upload
function handleImageUpload($fieldName) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/';
        $fileName = time() . '_' . basename($_FILES[$fieldName]['name']); // Add timestamp to avoid collisions
        $targetFile = $uploadDir . $fileName;
        
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
            // Apply Watermark
            applyWatermark($targetFile);
            return 'assets/images/' . $fileName;
        }
    }
    return null;
}

function applyWatermark($targetFile) {
    $watermarkPath = '../assets/images/watermark.png';
    if (!file_exists($watermarkPath)) return;

    $info = getimagesize($targetFile);
    $type = $info[2];
    
    switch ($type) {
        case IMAGETYPE_JPEG: $targetImg = imagecreatefromjpeg($targetFile); break;
        case IMAGETYPE_PNG: $targetImg = imagecreatefrompng($targetFile); break;
        case IMAGETYPE_GIF: $targetImg = imagecreatefromgif($targetFile); break;
        default: return;
    }

    $watermarkImg = imagecreatefrompng($watermarkPath);
    if (!$watermarkImg) {
        imagedestroy($targetImg);
        return;
    }

    $imgW = imagesx($targetImg);
    $imgH = imagesy($targetImg);
    
    // Original Watermark Dimensions
    $wmW_orig = imagesx($watermarkImg);
    $wmH_orig = imagesy($watermarkImg);

    // Resize Watermark (50% smaller)
    $wmW = $wmW_orig * 0.5;
    $wmH = $wmH_orig * 0.5;

    $resizedWatermark = imagecreatetruecolor($wmW, $wmH);
    imagealphablending($resizedWatermark, false);
    imagesavealpha($resizedWatermark, true);
    
    // Preserve transparency from original
    $transparent = imagecolorallocatealpha($resizedWatermark, 0, 0, 0, 127);
    imagefill($resizedWatermark, 0, 0, $transparent);

    imagecopyresampled($resizedWatermark, $watermarkImg, 0, 0, 0, 0, $wmW, $wmH, $wmW_orig, $wmH_orig);

    // Center watermark
    $dstX = ($imgW - $wmW) / 2;
    $dstY = ($imgH - $wmH) / 2;

    // Apply with 10% opacity
    imagecopymerge_alpha($targetImg, $resizedWatermark, $dstX, $dstY, 0, 0, $wmW, $wmH, 10);

    // Save
    switch ($type) {
        case IMAGETYPE_JPEG: imagejpeg($targetImg, $targetFile, 90); break;
        case IMAGETYPE_PNG: imagepng($targetImg, $targetFile); break;
        case IMAGETYPE_GIF: imagegif($targetImg, $targetFile); break;
    }

    imagedestroy($targetImg);
    imagedestroy($watermarkImg);
    imagedestroy($resizedWatermark);
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
    $cut = imagecreatetruecolor($src_w, $src_h); 
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
    imagedestroy($cut);
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slug = $_POST['slug'];
    $name = $_POST['name'];
    $name_en = $_POST['name_en'];
    $category = $_POST['category'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    $bg_color = '#ffffff'; // Default to white as per user request

    // Handle Dynamic Specifications (Variants)
    $final_specs = [];
    
    if (isset($_POST['variants']) && is_array($_POST['variants'])) {
        foreach ($_POST['variants'] as $variant) {
            $variantName = $variant['name'] ?? 'Default';
            $variantSpecs = [];
            
            if (isset($variant['specs']) && is_array($variant['specs'])) {
                foreach ($variant['specs'] as $spec) {
                    if (!empty($spec['name'])) {
                        $variantSpecs[] = [
                            'name' => $spec['name'],
                            'value' => $spec['value']
                        ];
                    }
                }
            }
            
            $final_specs[] = [
                'name' => $variantName,
                'specs' => $variantSpecs
            ];
        }
    } 
    // Fallback for old simple specs if variants not used (or legacy)
    elseif (isset($_POST['spec_names']) && isset($_POST['spec_values'])) {
        $simpleSpecs = [];
        for ($i = 0; $i < count($_POST['spec_names']); $i++) {
            if (!empty($_POST['spec_names'][$i])) {
                $simpleSpecs[] = [
                    'name' => $_POST['spec_names'][$i],
                    'value' => $_POST['spec_values'][$i]
                ];
            }
        }
        // Save as single default variant to maintain consistent structure
        $final_specs[] = [
            'name' => 'Default',
            'specs' => $simpleSpecs
        ];
    }

    $specifications_json = json_encode($final_specs);
    
    // Handle 5 images
    $images = [];
    $imageFields = ['image', 'image2', 'image3', 'image4', 'image5'];
    
    foreach ($imageFields as $field) {
        $uploadedPath = handleImageUpload($field);
        if (!$uploadedPath && isset($_POST['current_' . $field])) {
            $images[$field] = $_POST['current_' . $field];
        } else {
            $images[$field] = $uploadedPath;
        }
    }

    if ($id) {
        $sql = "UPDATE products SET slug=?, name=?, name_en=?, category=?, image=?, image2=?, image3=?, image4=?, image5=?, featured=?, bg_color=?, specifications=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssissi", $slug, $name, $name_en, $category, $images['image'], $images['image2'], $images['image3'], $images['image4'], $images['image5'], $featured, $bg_color, $specifications_json, $id);
    } else {
        $sql = "INSERT INTO products (slug, name, name_en, category, image, image2, image3, image4, image5, featured, bg_color, specifications) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssiss", $slug, $name, $name_en, $category, $images['image'], $images['image2'], $images['image3'], $images['image4'], $images['image5'], $featured, $bg_color, $specifications_json);
    }

    if ($stmt->execute()) {
        header("Location: products.php?msg=" . ($id ? "updated" : "created"));
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? __('admin_edit_product') : __('admin_add_product'); ?> - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container" style="max-width: 800px;">
    <a href="products.php" class="back-link">← <?php echo __('admin_back_to_products'); ?></a>
    <h1 style="margin-top: 10px;"><?php echo $id ? __('admin_edit_product') : __('admin_add_product'); ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name (Indonesian)</label>
            <input type="text" name="name" value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Name (English)</label>
            <input type="text" name="name_en" value="<?php echo $product ? htmlspecialchars($product['name_en']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label><?php echo __('admin_slug'); ?></label>
            <input type="text" name="slug" value="<?php echo $product ? htmlspecialchars($product['slug']) : ''; ?>" required placeholder="<?php echo __('admin_slug_placeholder'); ?>">
        </div>

        <div class="form-group">
            <label><?php echo __('admin_category'); ?></label>
            <select name="category">
                <?php foreach ($category_list as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php if($product && $product['category'] == $cat['name']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Image 1 -->
        <div class="form-group">
            <label><?php echo __('admin_image_main'); ?></label>
            <?php if ($product && $product['image']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $product['image']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image" value="<?php echo $product['image']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image">
        </div>

        <!-- Image 2 -->
        <div class="form-group">
            <label><?php echo __('admin_image_x'); ?> 2</label>
            <?php if ($product && isset($product['image2']) && $product['image2']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $product['image2']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image2" value="<?php echo $product['image2']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image2">
        </div>

        <!-- Image 3 -->
        <div class="form-group">
            <label><?php echo __('admin_image_x'); ?> 3</label>
            <?php if ($product && isset($product['image3']) && $product['image3']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $product['image3']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image3" value="<?php echo $product['image3']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image3">
        </div>

        <!-- Image 4 -->
        <div class="form-group">
            <label><?php echo __('admin_image_x'); ?> 4</label>
            <?php if ($product && isset($product['image4']) && $product['image4']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $product['image4']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image4" value="<?php echo $product['image4']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image4">
        </div>

        <!-- Image 5 -->
        <div class="form-group">
            <label><?php echo __('admin_image_x'); ?> 5</label>
            <?php if ($product && isset($product['image5']) && $product['image5']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $product['image5']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image5" value="<?php echo $product['image5']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image5">
        </div>

        <div class="form-group">
            <label><?php echo __('admin_featured_product'); ?></label>
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="featured" id="featured" <?php if($product && $product['featured']) echo 'checked'; ?> style="width: auto;">
                <label for="featured" style="margin: 0; font-weight: 500; font-size: 0.85rem; color: #666;"><?php echo __('admin_featured_desc'); ?></label>
            </div>
        </div>
        

        <hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">
        <h3 style="font-weight: 700; font-size: 1.1rem; margin-bottom: 20px;">Product Variants & Specifications</h3>
        
        <div id="variants-container">
            <?php
            $variants = [];
            $raw_specs = ($product && !empty($product['specifications'])) ? json_decode($product['specifications'], true) : [];
            
            // Check data structure: if first item has 'specs' key, it's new structure. Else legacy.
            $is_new_structure = !empty($raw_specs) && isset($raw_specs[0]['specs']);
            
            if ($is_new_structure) {
                $variants = $raw_specs;
            } else {
                // Legacy or Empty
                $legacy_specs = $raw_specs;
                
                // Ensure 'Code' exists for legacy/default
                $code_found = false;
                foreach ($legacy_specs as $s) {
                    if (isset($s['name']) && $s['name'] === 'Code') {
                        $code_found = true; break;
                    }
                }
                if (!$code_found && !empty($legacy_specs)) { // Only add if we have something, or if it's a new product
                   // actually, let's just respect what's there. if empty, we create default.
                }
                
                if (empty($legacy_specs) && !$id) {
                     $legacy_specs = [
                        ['name' => 'Code', 'value' => 'N/A'],
                        ['name' => 'Color', 'value' => '']
                    ];
                } elseif (empty($legacy_specs)) {
                     $legacy_specs = [['name' => 'Code', 'value' => 'N/A']];
                }

                $variants[] = [
                    'name' => 'Default',
                    'specs' => $legacy_specs
                ];
            }
            
            foreach ($variants as $vIndex => $variant):
            ?>
            <div class="variant-box" style="background: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
                    <div style="flex-grow: 1; margin-right: 15px;">
                        <label style="font-weight: 600; font-size: 0.9rem;">Variant Name</label>
                        <input type="text" name="variants[<?php echo $vIndex; ?>][name]" value="<?php echo htmlspecialchars($variant['name']); ?>" placeholder="e.g. 3000K, Black, etc.">
                    </div>
                    <button type="button" class="btn btn-danger" onclick="removeVariant(this)" style="height: fit-content; margin-top: 18px;">Delete Variant</button>
                </div>
                
                <div class="specs-list">
                    <?php if (!empty($variant['specs'])): 
                        foreach ($variant['specs'] as $sIndex => $spec): ?>
                        <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                            <input type="text" name="variants[<?php echo $vIndex; ?>][specs][<?php echo $sIndex; ?>][name]" value="<?php echo htmlspecialchars($spec['name']); ?>" placeholder="Spec Name">
                            <input type="text" name="variants[<?php echo $vIndex; ?>][specs][<?php echo $sIndex; ?>][value]" value="<?php echo htmlspecialchars($spec['value']); ?>" placeholder="Value">
                            <button type="button" class="btn btn-danger" style="padding: 8px 15px;" onclick="removeSpec(this)">×</button>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addSpec(this, <?php echo $vIndex; ?>)">+ Add Spec</button>
            </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-primary" style="margin-bottom: 20px;" onclick="addVariant()">+ Add New Variant</button>

        <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px; display: flex; gap: 12px; justify-content: space-between;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 40px;"><?php echo __('admin_save_product'); ?></button>
            <?php if ($id): ?>
                <a href="products.php?delete=<?php echo $id; ?>" class="btn btn-danger" style="padding: 12px 20px;" onclick="return confirm('<?php echo __('admin_confirm_delete'); ?>')"><?php echo __('admin_delete_product'); ?></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
let variantCount = <?php echo count($variants); ?>;

function addVariant() {
    const container = document.getElementById('variants-container');
    const index = variantCount++;
    
    const div = document.createElement('div');
    div.className = 'variant-box';
    div.style = 'background: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px;';
    div.innerHTML = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; align-items: center;">
            <div style="flex-grow: 1; margin-right: 15px;">
                <label style="font-weight: 600; font-size: 0.9rem;">Variant Name</label>
                <input type="text" name="variants[${index}][name]" value="New Variant" placeholder="e.g. 3000K, Black, etc.">
            </div>
            <button type="button" class="btn btn-danger" onclick="removeVariant(this)" style="height: fit-content; margin-top: 18px;">Delete Variant</button>
        </div>
        <div class="specs-list">
            <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="variants[${index}][specs][0][name]" value="Code" placeholder="Spec Name">
                <input type="text" name="variants[${index}][specs][0][value]" value="" placeholder="Value">
                <button type="button" class="btn btn-danger" style="padding: 8px 15px;" onclick="removeSpec(this)">×</button>
            </div>
            <div class="spec-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <input type="text" name="variants[${index}][specs][1][name]" value="Power" placeholder="Spec Name">
                <input type="text" name="variants[${index}][specs][1][value]" value="" placeholder="Value">
                <button type="button" class="btn btn-danger" style="padding: 8px 15px;" onclick="removeSpec(this)">×</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addSpec(this, ${index})">+ Add Spec</button>
    `;
    container.appendChild(div);
}

function removeVariant(btn) {
    if(confirm('Delete this variant?')) {
        btn.closest('.variant-box').remove();
    }
}

function addSpec(btn, variantIndex) {
    const list = btn.previousElementSibling;
    // Count existing rows to generate index (approximate, uniqueness within form submission relies on just array push if we didn't use explicit indexes, but PHP handles array keys loosely if they are numeric. 
    // However, to be safe with HTML inputs, we can just use a large random number or timestamp if strict indexing isn't required by PHP side as long as it iterates. 
    // Actually PHP processes variants[i][specs][] automatically if we omit key ?? No, we defined variants[i][specs][j].
    // Let's use Date.now() + random for spec index to avoid collision 
    const specIndex = Date.now() + Math.floor(Math.random() * 1000); 

    const row = document.createElement('div');
    row.className = 'spec-row';
    row.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
    row.innerHTML = `
        <input type="text" name="variants[${variantIndex}][specs][${specIndex}][name]" placeholder="Spec Name">
        <input type="text" name="variants[${variantIndex}][specs][${specIndex}][value]" placeholder="Value">
        <button type="button" class="btn btn-danger" style="padding: 8px 15px;" onclick="removeSpec(this)">×</button>
    `;
    list.appendChild(row);
}

function removeSpec(btn) {
    btn.parentElement.remove();
}


document.addEventListener('DOMContentLoaded', function() {
    let cropper;
    let currentInput;
    let originalFileName = 'cropped_image.jpg'; // Default fallback
    const modal = document.getElementById('crop-modal');
    const imageToCrop = document.getElementById('image-to-crop');
    const cancelBtn = document.getElementById('cancel-crop');
    const saveBtn = document.getElementById('save-crop');

    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                if (!file.type.startsWith('image/')) return;

                originalFileName = file.name; // Capture original name
                currentInput = this;
                const reader = new FileReader();
                reader.onload = function(evt) {
                    imageToCrop.src = evt.target.result;
                    modal.style.display = 'flex';
                    
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                    });
                };
                reader.readAsDataURL(file);
                this.value = ''; // Temporarily clear
            }
        });
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        if (cropper) cropper.destroy();
        currentInput.value = ''; 
    });

    saveBtn.addEventListener('click', function() {
        if (cropper) {
            cropper.getCroppedCanvas().toBlob((blob) => {
                const file = new File([blob], originalFileName, { type: "image/jpeg" });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                currentInput.files = dataTransfer.files;
                
                modal.style.display = 'none';
                cropper.destroy();
            }, 'image/jpeg');
        }
    });
});
</script>

<div id="crop-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:20px; border-radius:8px; width:90%; max-width:600px; height: 90%; max-height:600px; display: flex; flex-direction: column;">
        <div style="flex-grow: 1; overflow: hidden; position: relative; background: #333; display: flex; align-items: center; justify-content: center;">
            <img id="image-to-crop" style="max-width:100%; max-height: 100%; display: block;" src="">
        </div>
        <div style="margin-top:20px; text-align:right;">
             <button type="button" class="btn btn-secondary" id="cancel-crop" style="margin-right: 10px;">Cancel</button>
             <button type="button" class="btn btn-primary" id="save-crop">Crop & Save</button>
        </div>
    </div>
</div>

</body>
</html>

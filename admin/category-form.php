<?php
include 'auth.php';
include_once '../includes/db.php';
// Ensure language is loaded
include_once '../includes/lang_load.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$category = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $category = $stmt->get_result()->fetch_assoc();
}

// Function to handle image upload
function handleImageUpload($fieldName) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/';
        $fileName = 'cat_' . time() . '_' . basename($_FILES[$fieldName]['name']);
        $targetFile = $uploadDir . $fileName;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
            return 'assets/images/' . $fileName;
        }
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $name_en = $_POST['name_en'];
    $display_order = intval($_POST['display_order']);
    
    // Handle image
    $imagePath = handleImageUpload('image');
    if (!$imagePath && isset($_POST['current_image'])) {
        $imagePath = $_POST['current_image'];
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, name_en=?, display_order=?, image=? WHERE id=?");
        $stmt->bind_param("ssisi", $name, $name_en, $display_order, $imagePath, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, name_en, display_order, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $name, $name_en, $display_order, $imagePath);
    }

    if ($stmt->execute()) {
        header("Location: categories.php");
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
    <title><?php echo $id ? __('admin_edit_category') : __('admin_add_category'); ?> - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container" style="max-width: 600px;">
    <a href="categories.php" class="back-link">← <?php echo __('admin_back_to_categories'); ?></a>
    <h1 style="margin-top: 10px;"><?php echo $id ? __('admin_edit_category') : __('admin_add_category'); ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name (Indonesian)</label>
            <input type="text" name="name" value="<?php echo $category ? htmlspecialchars($category['name']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Name (English)</label>
            <input type="text" name="name_en" value="<?php echo $category ? htmlspecialchars($category['name_en']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label><?php echo __('admin_category_image'); ?></label>
            <?php if ($category && $category['image']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="../<?php echo $category['image']; ?>" width="100" style="border-radius: 4px; border: 1px solid #ddd;">
                    <input type="hidden" name="current_image" value="<?php echo $category['image']; ?>">
                </div>
            <?php endif; ?>
            <input type="file" name="image">
        </div>
        <div class="form-group">
            <label><?php echo __('admin_display_order'); ?></label>
            <input type="number" name="display_order" value="<?php echo $category ? $category['display_order'] : '0'; ?>">
            <small style="color: #888; display: block; margin-top: 5px;"><?php echo __('admin_display_order_desc'); ?></small>
        </div>
        <div style="margin-top: 40px; border-top: 1px solid #eee; padding-top: 30px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 40px;"><?php echo __('admin_save_category'); ?></button>
            <?php if ($id): ?>
                <a href="categories.php?delete=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('<?php echo __('admin_confirm_delete_cat'); ?>')" style="float: right;"><?php echo __('admin_delete_product'); ?></a>
            <?php endif; ?>
        </div>
    </form>
</div>


<script>
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

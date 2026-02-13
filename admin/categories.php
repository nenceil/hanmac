<?php
include 'auth.php';
include_once '../includes/db.php';
// Ensure language is loaded
include_once '../includes/lang_load.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: categories.php?msg=deleted");
    exit;
}

$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $msg = __('admin_msg_cat_deleted');
}

$result = $conn->query("SELECT * FROM categories ORDER BY display_order ASC");
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_manage_categories'); ?> - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container">
    <div class="dashboard-header">
        <div>
            <a href="index.php" class="back-link">← <?php echo __('admin_dashboard'); ?></a>
            <h1><?php echo __('admin_manage_categories'); ?></h1>
        </div>
        <div class="header-actions">
            <a href="category-form.php" class="btn btn-primary">+ <?php echo __('admin_add_category'); ?></a>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_category_id'); ?></th>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;" width="100"><?php echo __('admin_category_image_th'); ?></th>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_category_info'); ?></th>
                <th style="text-align: right; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_category_actions'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="<?php echo __('admin_category_id'); ?>">#<?php echo $row['id']; ?></td>
                <td data-label="<?php echo __('admin_category_image_th'); ?>">
                    <?php if($row['image']): ?>
                        <img src="../<?php echo htmlspecialchars($row['image']); ?>" class="thumbnail-img" alt="">
                    <?php else: ?>
                        <div class="thumbnail-img" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #ccc; font-size: 0.7rem;"><?php echo __('admin_no_img'); ?></div>
                    <?php endif; ?>
                </td>
                <td data-label="<?php echo __('admin_category_info'); ?>">
                    <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
                    <div class="product-category">ENGLISH: <?php echo htmlspecialchars($row['name_en']); ?></div>
                </td>
                <td data-label="<?php echo __('admin_category_actions'); ?>">
                    <div class="action-buttons">
                        <a href="category-form.php?id=<?php echo $row['id']; ?>" class="btn btn-success"><?php echo __('admin_edit'); ?></a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info"><?php echo __('admin_no_categories_found'); ?></div>
    <?php endif; ?>
</div>

</body>
</html>

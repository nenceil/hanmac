<?php
include 'auth.php';
include_once '../includes/db.php';
// Ensure language is loaded
include_once '../includes/lang_load.php';

$msg = '';
$msg_type = '';

// DELETE Logic
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $del_id);
        if ($stmt->execute()) {
            $msg = __('admin_msg_deleted');
            $msg_type = "success";
        } else {
            $msg = __('admin_msg_delete_error') . $stmt->error;
            $msg_type = "danger";
        }
    }
}

// REDIRECT messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'created') {
        $msg = __('admin_msg_created');
        $msg_type = "success";
    } elseif ($_GET['msg'] == 'updated') {
        $msg = __('admin_msg_updated');
        $msg_type = "success";
    }
}

// Fetch categories for filter
$cats = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = [];
while($c = $cats->fetch_assoc()) {
    $categories[] = $c['category'];
}

// Handle Filtering
$whereClause = "";
$selectedCategory = "";
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $selectedCategory = $_GET['category'];
    $whereClause = "WHERE category = '" . $conn->real_escape_string($selectedCategory) . "'";
}

$sql = "SELECT * FROM products $whereClause ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_manage_products'); ?> - Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
            min-width: 200px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

    <div class="container">
        <div class="dashboard-header">
            <div>
                <a href="index.php" class="back-link">← <?php echo __('admin_dashboard'); ?></a>
                <h1><?php echo __('admin_manage_products'); ?></h1>
            </div>
            <div class="header-actions">
                <a href="product-form.php" class="btn btn-primary">+ <?php echo __('admin_add_product'); ?></a>
            </div>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg_type; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- Filter Section -->
        <div class="filter-container">
            <span style="font-weight: 600; font-size: 0.9rem; color: #555;"><?php echo __('admin_filter_category'); ?>:</span>
            <form action="" method="GET" style="margin: 0;">
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value=""><?php echo __('admin_all_categories'); ?></option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php if($selectedCategory == $cat) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_product_id'); ?></th>
                    <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;" width="100"><?php echo __('admin_product_image'); ?></th>
                    <th style="text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_product_info'); ?></th>
                    <th style="text-align: right; padding: 15px; border-bottom: 2px solid #eee; font-size: 0.85rem; color: #999; text-transform: uppercase;"><?php echo __('admin_product_actions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="<?php echo __('admin_product_id'); ?>">#<?php echo $row['id']; ?></td>
                    <td data-label="<?php echo __('admin_product_image'); ?>">
                        <?php if($row['image']): ?>
                            <img src="../<?php echo htmlspecialchars($row['image']); ?>" class="thumbnail-img" alt="">
                        <?php else: ?>
                            <div class="thumbnail-img" style="display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #ccc; font-size: 0.7rem;"><?php echo __('admin_no_img'); ?></div>
                        <?php endif; ?>
                    </td>
                    <td data-label="<?php echo __('admin_product_info'); ?>">
                        <div class="product-name"><?php echo htmlspecialchars($row['name']); ?></div>
                        <div class="product-category"><?php echo htmlspecialchars($row['category']); ?></div>
                    </td>
                    <td data-label="<?php echo __('admin_product_actions'); ?>">
                        <div class="action-buttons">
                            <a href="product-form.php?id=<?php echo $row['id']; ?>" class="btn btn-success"><?php echo __('admin_edit'); ?></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info"><?php echo __('admin_no_products_found'); ?></div>
        <?php endif; ?>
    </div>

</body>
</html>

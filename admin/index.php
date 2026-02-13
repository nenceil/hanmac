<?php
include 'auth.php';
// Ensure language is loaded
include_once '../includes/lang_load.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_dashboard'); ?> - Hanmac Lighting</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include 'header.php'; ?>

<div class="container portal-container" style="max-width: 1000px; margin: 10px auto 40px auto; padding: 40px;">
    <div class="welcome-header">
        <h1><?php echo __('admin_welcome'); ?>, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
        <p><?php echo __('admin_welcome_sub'); ?></p>
    </div>

    <div class="portal-grid">
        <a href="products.php" class="portal-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            </div>
            <h2><?php echo strtoupper(__('admin_manage_products')); ?></h2>
            <p><?php echo __('admin_products_desc'); ?></p>
        </a>

        <a href="categories.php" class="portal-card">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
            </div>
            <h2><?php echo strtoupper(__('admin_manage_categories')); ?></h2>
            <p><?php echo __('admin_categories_desc'); ?></p>
        </a>
    </div>
</div>

</body>
</html>

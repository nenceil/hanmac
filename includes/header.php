<?php
include_once 'lang_load.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Hanmac Lighting'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="slider.css?v=<?php echo time(); ?>">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
</head>

<body>
    <header class="main-header">
        <div class="logo-container">
            <a href="index.php">
                <img src="assets/images/logo_hanmac.png" alt="Hanmac Logo" class="main-logo">
            </a>
        </div>

        <!-- Desktop Nav -->
        <div class="main-nav">
            <a href="index.php" class="<?php echo (isset($currentPage) && $currentPage == 'home') ? 'active' : ''; ?>"><?php echo __('home'); ?></a>
            <a href="index.php#catalog"><?php echo __('categories'); ?></a>
            <a href="catalog.php" class="<?php echo (isset($currentPage) && $currentPage == 'catalog') ? 'active' : ''; ?>"><?php echo __('all_products'); ?></a>
        </div>

        <div class="header-utilities">
            <form action="catalog.php" method="GET" class="search-box">
                <input type="text" name="q" placeholder="<?php echo __('search_placeholder'); ?>" class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit" class="search-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
            <div class="lang-selector">
                <a href="change_lang.php?lang=id" class="lang-option <?php echo $current_lang == 'id' ? 'active' : ''; ?>">ID</a>
                <span class="lang-divider">|</span>
                <a href="change_lang.php?lang=en" class="lang-option <?php echo $current_lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>

        <!-- Hamburger Button -->
        <button class="hamburger-btn" aria-label="Menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </header>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay">
    <div class="mobile-menu-content">
        <div class="mobile-menu-header">
            <span class="mobile-menu-title">MENU</span>
            <button class="close-menu-btn" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <nav class="mobile-nav-links">
            <a href="index.php"><?php echo __('home'); ?></a>
            <a href="index.php#catalog"><?php echo __('categories'); ?></a>
            <a href="catalog.php"><?php echo __('all_products'); ?></a>
        </nav>
        <div class="mobile-utilities">
            <form action="catalog.php" method="GET" class="search-box mobile-search">
                <input type="text" name="q" placeholder="<?php echo __('search_placeholder'); ?>" class="search-input" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="submit" class="search-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
            <div class="lang-selector mobile-lang">
                <a href="change_lang.php?lang=id" class="lang-option <?php echo $current_lang == 'id' ? 'active' : ''; ?>">ID</a>
                <span class="lang-divider">|</span>
                <a href="change_lang.php?lang=en" class="lang-option <?php echo $current_lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
        </div>
    </div>
    </div>

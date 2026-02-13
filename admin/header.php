<?php
// Ensure language is loaded for the admin panel
include_once '../includes/lang_load.php';
?>
<header class="admin-header">
    <div class="admin-header-container">
        <!-- Logo stays centered -->
        <div class="admin-logo-centered">
            <a href="index.php">
                <img src="../assets/images/logo_hanmac.png" alt="Hanmac Logo">
            </a>
        </div>
        
        <!-- Hamburger on the Right -->
        <button class="admin-hamburger" id="adminHamburger">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>
</header>

<!-- Admin Sidebar Menu (Aligned Right) -->
<div class="admin-sidebar-overlay" id="adminSidebar">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <span class="sidebar-title">NAVIGATION</span>
            <button class="close-sidebar" id="closeSidebar">&times;</button>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                <?php echo __('admin_dashboard'); ?>
            </a>
            <a href="products.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                <?php echo __('admin_manage_products'); ?>
            </a>
            <a href="categories.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <?php echo __('admin_manage_categories'); ?>
            </a>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
            <a href="../index.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <?php echo __('admin_view_public'); ?>
            </a>
            <a href="logout.php" class="nav-item logout" style="margin-top: 5px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <?php echo __('admin_logout'); ?>
            </a>

            <!-- Language Selector in Sidebar -->
            <div style="margin-top: 30px; padding: 0 15px; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #999; letter-spacing: 1px;">LANGUAGE:</span>
                <div class="admin-lang-switcher" style="display: flex; gap: 8px;">
                    <a href="../change_lang.php?lang=id" style="text-decoration: none; font-size: 0.85rem; font-weight: 700; color: <?php echo $current_lang == 'id' ? '#1a1a1a' : '#ccc'; ?>;">ID</a>
                    <span style="color: #eee;">|</span>
                    <a href="../change_lang.php?lang=en" style="text-decoration: none; font-size: 0.85rem; font-weight: 700; color: <?php echo $current_lang == 'en' ? '#1a1a1a' : '#ccc'; ?>;">EN</a>
                </div>
            </div>
        </nav>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('adminHamburger');
    const sidebar = document.getElementById('adminSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (hamburger && sidebar && closeBtn) {
        hamburger.addEventListener('click', function() {
            sidebar.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        closeBtn.addEventListener('click', function() {
            sidebar.classList.remove('active');
            document.body.style.overflow = 'auto';
        });

        sidebar.addEventListener('click', function(e) {
            if (e.target === sidebar) {
                sidebar.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    }
});
</script>

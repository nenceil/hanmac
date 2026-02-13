<?php
$pageTitle = 'All Categories - Hanmac Lighting';
$currentPage = 'categories';
include_once 'includes/db.php';
include 'includes/header.php';
?>

<main>
    <section class="category-section" style="padding-top: 120px;">
        <div class="category-header">
            <h2><?php echo __('categories'); ?></h2>
        </div>
        <div class="category-grid">
            <?php
            $cat_res = $conn->query("SELECT * FROM categories ORDER BY display_order ASC");
            if ($cat_res->num_rows > 0) {
                while($cat = $cat_res->fetch_assoc()):
                    $catTitle = ($current_lang == 'en' && !empty($cat['name_en'])) ? $cat['name_en'] : $cat['name'];
                    $catImg = !empty($cat['image']) ? $cat['image'] : 'assets/images/cat_floor_lamp.png';
            ?>
            <a href="catalog.php?cat=<?php echo urlencode($cat['name']); ?>" class="category-item">
                <div class="cat-image">
                    <img src="<?php echo $catImg; ?>" alt="<?php echo htmlspecialchars($catTitle); ?>">
                    <span><?php echo htmlspecialchars($catTitle); ?></span>
                </div>
            </a>
            <?php 
                endwhile; 
            } else {
                echo '<p>' . __('admin_no_categories_found') . '</p>';
            }
            ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<?php
$pageTitle = 'Hanmac Lighting - Modern Lighting Solutions';
$currentPage = 'home';
include_once 'includes/db.php';
include 'includes/header.php';
?>

    <main>
        <section class="hero-section">
            <div class="hero-content">
                <h1><?php echo __('hero_title'); ?></h1>
                <p class="hero-subtext"><?php echo __('hero_subtitle'); ?></p>
                <a href="#catalog" class="btn btn-outline"><?php echo __('hero_button'); ?></a>
            </div>
        </section>

        <section id="catalog" class="category-section">
            <div class="category-header">
                <h2><?php echo __('categories'); ?></h2>
                <a href="catalog.php" class="see-all-link"><?php echo __('see_all'); ?> <span class="arrow">↗</span></a>
            </div>
            <div class="category-grid">
                <?php
                $cat_res = $conn->query("SELECT * FROM categories ORDER BY display_order ASC LIMIT 8");
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
                <?php endwhile; ?>
            </div>
        </section>

        <section class="featured-section">
            <div class="section-title">
                <h2><?php echo __('featured_products'); ?></h2>
            </div>
            <div class="featured-grid">
                <?php
                $sql = "SELECT * FROM products WHERE featured = 1 LIMIT 12";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $productName = ($current_lang == 'en' && !empty($row['name_en'])) ? $row['name_en'] : $row['name'];
                        ?>
                        <a href="product-detail.php?id=<?php echo $row['slug']; ?>" class="product-card">
                            <!-- <div class="product-msg-badge">NEW</div> --> 
                             <div class="product-image">
                                <img src="<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($productName); ?>">
                            </div>
                            <div class="product-details">
                                <h4><?php echo htmlspecialchars($productName); ?></h4>
                            </div>
                        </a>
                        <?php
                    }
                } else {
                    echo "<p>" . __('no_products') . "</p>";
                }
                ?>
            </div>

            <div class="view-all-container">
                <a href="catalog.php" class="btn btn-outline btn-dark"><?php echo __('view_all_products'); ?></a>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>

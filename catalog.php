<?php
$pageTitle = 'Catalog - Hanmac Lighting';
$currentPage = 'catalog';
include_once 'includes/db.php';
include 'includes/header.php';
?>

    <main class="catalog-page">
        <section class="section-title-area">
            <h1><?php echo __('catalog_title'); ?></h1>
            <div class="filter-tabs">
                <a href="catalog.php" class="<?php echo !isset($_GET['cat']) ? 'tab-active' : ''; ?>">All</a>
                <?php
                $cat_res = $conn->query("SELECT * FROM categories ORDER BY display_order ASC");
                while($cat = $cat_res->fetch_assoc()):
                    $catTitle = ($current_lang == 'en' && !empty($cat['name_en'])) ? $cat['name_en'] : $cat['name'];
                    $isActive = (isset($_GET['cat']) && $_GET['cat'] == $cat['name']) ? 'tab-active' : '';
                ?>
                <a href="catalog.php?cat=<?php echo urlencode($cat['name']); ?>" class="<?php echo $isActive; ?>"><?php echo htmlspecialchars($catTitle); ?></a>
                <?php endwhile; ?>
            </div>
        </section>

        <section class="catalog-grid">
            <?php
            $category_filter = isset($_GET['cat']) ? $_GET['cat'] : null;
            $search_query = isset($_GET['q']) ? $_GET['q'] : null;

            $sql = "SELECT * FROM products WHERE 1=1";
            
            if ($category_filter) {
                $sql .= " AND category = '" . $conn->real_escape_string($category_filter) . "'";
            }
            
            if ($search_query) {
                $q = $conn->real_escape_string($search_query);
                $sql .= " AND (name LIKE '%$q%' OR name_en LIKE '%$q%' OR category LIKE '%$q%' OR specifications LIKE '%$q%')";
            }

            $sql .= " ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $productName = ($current_lang == 'en' && !empty($row['name_en'])) ? $row['name_en'] : $row['name'];
                    ?>
                    <a href="product-detail.php?id=<?php echo $row['slug']; ?>" class="catalog-item">
                        <div class="cat-product-image">
                            <img src="<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($productName); ?>">
                        </div>
                        <div class="cat-product-info">
                            <h3><?php echo htmlspecialchars($productName); ?></h3>
                        </div>
                    </a>
                    <?php
                }
            } else {
                echo "<div class='no-results'><p>" . __('no_products') . "</p></div>";
            }
            ?>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>

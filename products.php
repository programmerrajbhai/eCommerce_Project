<?php
// products.php
require_once 'includes/header.php';

// ডেটাবেস থেকে সব প্রোডাক্ট নিয়ে আসা
// যদি নির্দিষ্ট ক্যাটাগরি দেখতে চায়, সেটার জন্য লজিক রাখা হলো
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->execute([$category]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$all_products = $stmt->fetchAll();

// ডেটাবেস থেকে সব ইউনিক ক্যাটাগরি নিয়ে আসা (ফিল্টার করার জন্য)
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $cat_stmt->fetchAll();
?>

<style>
/* =========================================
   শপ পেজ স্টাইল (Shop Page Styles)
========================================= */
.shop-header {
    padding: 120px 8% 40px;
    text-align: center;
}

.shop-header h1 {
    font-size: 45px;
    text-transform: uppercase;
}

/* ক্যাটাগরি ফিল্টার বাটন */
.category-filters {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 14px;
    background: var(--glass-bg);
    border: var(--glass-border);
    color: var(--text-light);
    cursor: pointer;
    transition: 0.3s;
}

.filter-btn:hover, .filter-btn.active {
    background: var(--accent-orange);
    box-shadow: var(--accent-glow);
    border-color: var(--accent-orange);
}
</style>

<section class="shop-header">
    <h1>ALL <span class="highlight">PRODUCTS</span></h1>
    <p style="color: #aaa; margin-top: 10px;">Explore our premium collection of digital & physical assets.</p>
</section>

<section class="trending-products" style="padding-top: 0;">
    
    <div class="category-filters">
        <a href="products.php" class="filter-btn <?= ($category == '') ? 'active' : '' ?>">All Products</a>
        <?php foreach($categories as $cat): ?>
            <?php if(!empty($cat['category'])): ?>
                <a href="products.php?category=<?= urlencode($cat['category']) ?>" 
                   class="filter-btn <?= ($category == $cat['category']) ? 'active' : '' ?>">
                    <?= $cat['category'] ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <div class="product-grid">
        <?php if(count($all_products) > 0): ?>
            <?php foreach($all_products as $product): ?>
                <?php 
                    // ইমেজ সোর্স ঠিক করা
                    $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image']; 
                ?>
                <div class="product-card glass-panel">
                    <div class="card-image-wrapper">
                        <a href="product_details.php?id=<?= $product['id'] ?>">
                            <img src="<?= $img_src ?>" alt="<?= $product['title'] ?>" class="card-image">
                        </a>
                    </div>
                    <div class="card-info">
                        <a href="product_details.php?id=<?= $product['id'] ?>">
                            <h3 style="transition: 0.3s;"><?= $product['title'] ?></h3>
                        </a>
                        <p class="price">৳<?= number_format($product['price'], 2) ?></p>
                        <button class="btn btn-glass add-to-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <h3 style="color: #aaa;">No products found in this category!</h3>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
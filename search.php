<?php
// search.php
require_once 'includes/header.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if ($search_query) {
    // টাইটেল বা ক্যাটাগরির সাথে মিল রেখে প্রোডাক্ট খোঁজা
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE ? OR category LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search_query%", "%$search_query%"]);
    $products = $stmt->fetchAll();
}
?>

<section class="shop-header" style="padding: 120px 8% 40px; text-align: center;">
    <h1>SEARCH <span class="highlight">RESULTS</span></h1>
    <p style="color: #aaa; margin-top: 10px;">Showing results for: <b>"<?= htmlspecialchars($search_query) ?>"</b></p>
</section>

<section class="trending-products" style="padding-top: 0; min-height: 50vh;">
    <div class="product-grid">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <?php 
                    $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image']; 
                ?>
                <div class="product-card glass-panel">
                    <div class="card-image-wrapper">
                        <a href="product_details.php?id=<?= $product['id'] ?>">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="card-image">
                        </a>
                    </div>
                    <div class="card-info">
                        <a href="product_details.php?id=<?= $product['id'] ?>">
                            <h3 style="transition: 0.3s;"><?= htmlspecialchars($product['title']) ?></h3>
                        </a>
                        <p class="price">৳<?= number_format($product['price'], 2) ?></p>
                        <button class="btn btn-glass add-to-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 50px; color: #aaa; margin-bottom: 20px;"></i>
                <h3 style="color: #aaa;">No products found matching your search!</h3>
                <a href="products.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">Browse All Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
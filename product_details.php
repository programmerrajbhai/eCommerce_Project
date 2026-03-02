<?php
// product_details.php
require_once 'includes/header.php';

// URL থেকে প্রোডাক্ট আইডি নেওয়া (যেমন: product_details.php?id=1)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ডেটাবেস থেকে প্রোডাক্টের তথ্য নিয়ে আসা
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// যদি প্রোডাক্ট না পাওয়া যায়
if (!$product) {
    echo "<div style='text-align:center; padding: 150px 20px; color:white; min-height: 60vh;'>
            <h2 style='margin-bottom: 20px;'>Product not found!</h2>
            <a href='index.php' class='btn btn-primary'>Go Back to Shop</a>
          </div>";
    require_once 'includes/footer.php';
    exit;
}

// ইমেজ সোর্স ঠিক করা (ডেমো লিংক বা লোকাল ফোল্ডারের জন্য)
$img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image'];
?>

<section class="product-details-section">
    <div class="details-container glass-panel">
        
        <div class="details-image-wrapper">
            <div class="glow-circle"></div>
            <img src="<?= $img_src ?>" alt="<?= $product['title'] ?>" class="details-image">
        </div>

        <div class="details-info">
            <h1><?= $product['title'] ?></h1>
            <span class="category-badge"><?= $product['category'] ?></span>
            
            <h2 class="price">৳ <?= number_format($product['price'], 2) ?></h2>
            
            <p class="description"><?= $product['description'] ?></p>
            
            <p class="stock-info">
                <?php if($product['stock'] > 0): ?>
                    <i class="fa-solid fa-circle-check" style="color: #2ecc71;"></i> In Stock (<?= $product['stock'] ?> available)
                <?php else: ?>
                    <i class="fa-solid fa-circle-xmark" style="color: #e74c3c;"></i> Out of Stock
                <?php endif; ?>
            </p>

            <div class="action-buttons">
                <?php if($product['stock'] > 0): ?>
                    <button class="btn btn-primary" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                <?php endif; ?>
                <button class="btn btn-glass">
                    <i class="fa-regular fa-heart"></i> Wishlist
                </button>
            </div>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
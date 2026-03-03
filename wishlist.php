<?php
// wishlist.php
require_once 'includes/header.php';

// সেশনে উইশলিস্ট অ্যারে তৈরি করা (যদি আগে থেকে না থাকে)
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// =========================================
// ১. উইশলিস্টে প্রোডাক্ট অ্যাড করার লজিক
// =========================================
if (isset($_GET['add'])) {
    $add_id = (int)$_GET['add'];
    
    // প্রোডাক্টটি আগে থেকে না থাকলে অ্যাড করবে
    if (!in_array($add_id, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $add_id;
    }
    // অ্যাড হওয়ার পর URL ক্লিন করার জন্য রিলোড
    echo "<script>window.location.href='wishlist.php';</script>";
    exit;
}

// =========================================
// ২. উইশলিস্ট থেকে প্রোডাক্ট রিমুভ করার লজিক
// =========================================
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    
    // প্রোডাক্টটি খুঁজে বের করে ডিলিট করা
    if (($key = array_search($remove_id, $_SESSION['wishlist'])) !== false) {
        unset($_SESSION['wishlist'][$key]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // ইনডেক্স ঠিক করা
    }
    echo "<script>window.location.href='wishlist.php';</script>";
    exit;
}

// =========================================
// ৩. ডেটাবেস থেকে উইশলিস্টের প্রোডাক্টগুলো নিয়ে আসা
// =========================================
$wishlist_products = [];
if (count($_SESSION['wishlist']) > 0) {
    // ?, ?, ? ডাইনামিক প্লেসহোল্ডার তৈরি করা
    $in_query = implode(',', array_fill(0, count($_SESSION['wishlist']), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in_query) ORDER BY id DESC");
    $stmt->execute($_SESSION['wishlist']);
    $wishlist_products = $stmt->fetchAll();
}
?>

<section class="shop-header" style="padding: 120px 8% 40px; text-align: center;">
    <h1>MY <span class="highlight">WISHLIST</span></h1>
    <p style="color: #aaa; margin-top: 10px;">Your favorite products saved for later.</p>
</section>

<section class="trending-products" style="padding-top: 0; min-height: 50vh;">
    <div class="product-grid">
        <?php if(count($wishlist_products) > 0): ?>
            <?php foreach($wishlist_products as $product): ?>
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
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button class="btn btn-primary" style="flex: 1; padding: 10px;" onclick="addToCart(<?= $product['id'] ?>)">
                                <i class="fa-solid fa-cart-plus"></i> Add
                            </button>
                            <a href="wishlist.php?remove=<?= $product['id'] ?>" class="btn btn-glass" style="flex: 1; text-align: center; padding: 10px; color: #ff4757; border-color: #ff4757;">
                                <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                <i class="fa-regular fa-heart" style="font-size: 50px; color: #aaa; margin-bottom: 20px;"></i>
                <h3 style="color: #aaa;">Your wishlist is currently empty!</h3>
                <a href="products.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">Explore Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
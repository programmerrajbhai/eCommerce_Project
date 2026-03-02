<?php
// index.php
require_once 'includes/header.php';

// ডেটাবেস থেকে লেটেস্ট ৪টি প্রোডাক্ট আনার কোড
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$products = $stmt->fetchAll();

// ডেটাবেস থেকে ক্যাটাগরিগুলো আনার কোড (যাতে ডাইনামিক ক্যাটাগরি তৈরি হয়)
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category != '' LIMIT 6");
$categories = $cat_stmt->fetchAll();
?>

<section class="banner-section">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            
            <div class="swiper-slide">
                <a href="products.php?category=Electronics">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?q=80&w=2070&auto=format&fit=crop" alt="Sale Banner 1">
                    <div class="banner-overlay">
                        <h2>MEGA ELECTRONICS SALE</h2>
                        <p>Up to 50% Off on Top Gadgets</p>
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="products.php?category=Shoes">
                    <img src="https://images.unsplash.com/photo-1555529771-835f59fc5efe?q=80&w=2070&auto=format&fit=crop" alt="Sale Banner 2">
                    <div class="banner-overlay">
                        <h2>NEW SNEAKER ARRIVALS</h2>
                        <p>Step Up Your Style Game</p>
                    </div>
                </a>
            </div>

            <div class="swiper-slide">
                <a href="products.php">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop" alt="Sale Banner 3">
                    <div class="banner-overlay">
                        <h2>EXCLUSIVE COLLECTION</h2>
                        <p>Shop the Latest Trends Now</p>
                    </div>
                </a>
            </div>

        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="categories-section">
    <h2 class="section-title" style="font-size: 24px; margin-bottom: 30px;">SHOP BY <span class="highlight">CATEGORY</span></h2>
    
    <div class="category-row">
        <?php if(count($categories) > 0): ?>
            <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?= urlencode($cat['category']) ?>" class="category-box glass-panel">
                    <i class="fa-solid fa-tags" style="font-size: 24px; color: var(--accent-orange); margin-bottom: 10px;"></i>
                    <h4><?= htmlspecialchars($cat['category']) ?></h4>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <a href="products.php" class="category-box glass-panel">
                <i class="fa-solid fa-shoe-prints"></i>
                <h4>Shoes</h4>
            </a>
            <a href="products.php" class="category-box glass-panel">
                <i class="fa-solid fa-laptop"></i>
                <h4>Electronics</h4>
            </a>
            <a href="products.php" class="category-box glass-panel">
                <i class="fa-solid fa-shirt"></i>
                <h4>Clothing</h4>
            </a>
            <a href="products.php" class="category-box glass-panel">
                <i class="fa-solid fa-headphones"></i>
                <h4>Accessories</h4>
            </a>
        <?php endif; ?>
    </div>
</section>

<section id="shop" class="trending-products">
    <h2 class="section-title">TRENDING <span class="highlight">NOW</span></h2>
    
    <div class="product-grid">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <?php 
                    $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image']; 
                ?>
                <div class="product-card glass-panel">
                    <div class="card-image-wrapper">
                        <img src="<?= $img_src ?>" alt="<?= $product['title'] ?>" class="card-image">
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
            <p style="text-align: center; color: white;">No products found in database!</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
<?php
// index.php
require_once 'includes/header.php';

// ডেটাবেস থেকে সব প্রোডাক্ট র‍্যান্ডমলি (Randomly) আনার কোড
$stmt = $pdo->query("SELECT * FROM products ORDER BY RAND()");
$products = $stmt->fetchAll();

// ডেটাবেস থেকে ক্যাটাগরিগুলো আনার কোড
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category != '' LIMIT 6");
$categories = $cat_stmt->fetchAll();
?>

<style>
/* =========================================
   হোমপেজ স্পেশাল ক্লিন ডিজাইন (Clean UI)
========================================= */
/* ব্যানার ওভারলে টেক্সট ডিজাইন */
.banner-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 60px 40px 40px;
    background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
    color: #fff;
    pointer-events: none;
}
.banner-overlay h2 { font-size: 38px; font-weight: 800; margin-bottom: 8px; text-shadow: 2px 2px 10px rgba(0,0,0,0.5); }
.banner-overlay p { font-size: 18px; color: var(--accent-orange); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

/* ক্যাটাগরি সেকশন */
.category-row {
    display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; padding: 10px 0;
}
.category-box {
    width: 160px; padding: 25px 15px; text-align: center; border-radius: 16px;
    background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.4s ease; text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.category-box i { font-size: 32px; color: #aaa; margin-bottom: 15px; transition: 0.4s; }
.category-box h4 { font-size: 15px; color: #fff; font-weight: 600; transition: 0.4s; }
.category-box:hover { transform: translateY(-8px); border-color: var(--accent-orange); background: rgba(255, 115, 0, 0.05); box-shadow: 0 10px 20px rgba(0,0,0,0.3); }
.category-box:hover i { color: var(--accent-orange); transform: scale(1.1); }
.category-box:hover h4 { color: var(--accent-orange); }

/* প্রোডাক্ট কার্ড ক্লিনআপ */
.product-card { border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; }
.card-image-wrapper { height: 220px; padding: 20px; background: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; }
.card-image-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.5s ease; }
.product-card:hover .card-image-wrapper img { transform: scale(1.08); }

.card-info { padding: 20px; text-align: center; }
.card-info h3 { font-size: 16px; margin-bottom: 10px; color: #fff; font-weight: 600; }
.card-info a { text-decoration: none; }
.card-info a:hover h3 { color: var(--accent-orange); }
.card-info .price { font-size: 20px; color: var(--accent-orange); font-weight: 800; margin-bottom: 15px; }

/* অ্যাড টু কার্ট বাটন */
.add-to-cart-btn {
    width: 100%; padding: 12px; font-size: 14px; font-weight: bold; border-radius: 8px;
    background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s;
}
.add-to-cart-btn:hover { background: var(--accent-orange); border-color: var(--accent-orange); color: #fff; box-shadow: 0 5px 15px rgba(255,115,0,0.3); }

/* মোবাইল রেসপন্সিভ ব্যানার টেক্সট */
@media (max-width: 768px) {
    .banner-overlay h2 { font-size: 24px; }
    .banner-overlay p { font-size: 14px; }
    .category-box { width: 130px; padding: 15px; }
    .category-box i { font-size: 24px; }
    .card-image-wrapper { height: 180px; }
}
</style>

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

<section class="categories-section" id="categories">
    <h2 class="section-title" style="font-size: 28px; margin-bottom: 40px; text-align: center;">SHOP BY <span class="highlight">CATEGORY</span></h2>
    
    <div class="category-row">
        <?php if(count($categories) > 0): ?>
            <?php foreach($categories as $cat): ?>
                <a href="products.php?category=<?= urlencode($cat['category']) ?>" class="category-box">
                    <i class="fa-solid fa-tags"></i>
                    <h4><?= htmlspecialchars($cat['category']) ?></h4>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <a href="products.php" class="category-box">
                <i class="fa-solid fa-shoe-prints"></i>
                <h4>Shoes</h4>
            </a>
            <a href="products.php" class="category-box">
                <i class="fa-solid fa-laptop"></i>
                <h4>Electronics</h4>
            </a>
            <a href="products.php" class="category-box">
                <i class="fa-solid fa-shirt"></i>
                <h4>Clothing</h4>
            </a>
            <a href="products.php" class="category-box">
                <i class="fa-solid fa-headphones"></i>
                <h4>Accessories</h4>
            </a>
        <?php endif; ?>
    </div>
</section>

<section id="shop" class="trending-products">
    <h2 class="section-title" style="font-size: 28px; margin-bottom: 40px; text-align: center;">TRENDING <span class="highlight">NOW</span></h2>
    
    <div class="product-grid">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <?php 
                    $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image']; 
                ?>
                <div class="product-card glass-panel">
                    <div class="card-image-wrapper">
                        <a href="product_details.php?id=<?= $product['id'] ?>" style="display: block; width: 100%; height: 100%;">
                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        </a>
                    </div>
                    
                    <div class="card-info">
                        <a href="product_details.php?id=<?= $product['id'] ?>">
                            <h3><?= htmlspecialchars($product['title']) ?></h3>
                        </a>
                        <p class="price">৳<?= number_format($product['price'], 2) ?></p>
                        
                        <button class="add-to-cart-btn" onclick="addToCart(<?= $product['id'] ?>)">
                            <i class="fa-solid fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; color: #aaa; grid-column: 1 / -1; padding: 50px;">No products found in database!</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
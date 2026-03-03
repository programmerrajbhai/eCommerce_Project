<?php
// product_details.php
require_once 'includes/header.php';

// URL থেকে প্রোডাক্ট আইডি নেওয়া 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ডেটাবেস থেকে প্রোডাক্টের তথ্য নিয়ে আসা
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// যদি প্রোডাক্ট না পাওয়া যায়
if (!$product) {
    echo "<div style='text-align:center; padding: 150px 20px; color:white; min-height: 60vh;'>
            <h2 style='margin-bottom: 20px;'>Product not found!</h2>
            <a href='index.php' class='btn btn-primary'>Go Back to Shop</a>
          </div>";
    require_once 'includes/footer.php';
    exit;
}

// মেইন ইমেজ সোর্স ঠিক করা
$main_img = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image'];

// গ্যালারি ইমেজ প্রসেসিং (ডাবল ইমেজ ফিক্স)
$gallery_images = [$main_img]; // প্রথমেই মেইন ইমেজ রাখলাম

if (!empty($product['gallery'])) {
    $gallery_links = explode(',', $product['gallery']);
    foreach ($gallery_links as $link) {
        $link = trim($link);
        if (!empty($link)) {
            $img_url = filter_var($link, FILTER_VALIDATE_URL) ? $link : 'assets/images/products/'.$link;
            // যদি ইমেজটি আগে থেকে অ্যারেতে না থাকে, তবেই যোগ করবে
            if (!in_array($img_url, $gallery_images)) {
                $gallery_images[] = $img_url;
            }
        }
    }
}

// =========================================
// রিলেটেড প্রোডাক্ট (Related Products) খোঁজার লজিক
// =========================================
// বর্তমান প্রোডাক্টের ক্যাটাগরি অনুযায়ী অন্য ৪টি প্রোডাক্ট আনা হবে (বর্তমান প্রোডাক্টটি বাদ দিয়ে)
$stmt_related = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? ORDER BY RAND() LIMIT 4");
$stmt_related->execute([$product['category'], $id]);
$related_products = $stmt_related->fetchAll();

?>

<style>
/* =========================================
   প্রোডাক্ট গ্যালারি স্লাইডার ডিজাইন (HD Fix)
========================================= */
.gallery-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    max-width: 500px;
}

/* মেইন বড় ছবি */
.main-img-container {
    width: 100%;
    height: 400px;
    background: rgba(255,255,255,0.03);
    border-radius: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: var(--glass-border);
    position: relative;
    padding: 20px; 
}

/* পেছনের গ্লো ইফেক্ট */
.main-img-container::before {
    content: '';
    position: absolute;
    width: 200px;
    height: 200px;
    background: var(--accent-orange);
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.15; 
    z-index: 0;
}

/* 🚀 HD ইমেজ ফিক্স */
.main-img-container img {
    max-width: 100% !important;
    max-height: 100% !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain !important; 
    z-index: 1;
    animation: none !important; 
    transform: none !important; 
    filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)) !important;
    image-rendering: high-quality;
}

/* নিচে ছোট থাম্বনেইল ছবিগুলো */
.thumb-slider {
    width: 100%;
    height: 80px;
}

.thumb-slide {
    width: 80px !important;
    height: 80px !important; 
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.3s;
    background: rgba(255,255,255,0.05);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 5px; 
}

/* থাম্বনেইল ইমেজ ফিক্স */
.thumb-slide img {
    max-width: 100% !important;
    max-height: 100% !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain !important; 
    opacity: 0.5;
    transition: 0.3s;
    animation: none !important;
    transform: none !important;
}

/* অ্যাকটিভ থাম্বনেইলের স্টাইল */
.thumb-slide.swiper-slide-thumb-active {
    border-color: var(--accent-orange);
    background: rgba(255, 115, 0, 0.1);
}
.thumb-slide.swiper-slide-thumb-active img, .thumb-slide:hover img {
    opacity: 1;
}

/* ডিসক্রিপশন টেক্সট ফিক্স */
.details-info .description {
    color: #cccccc;
    line-height: 1.8;
    margin-bottom: 25px;
    font-size: 15px;
    white-space: pre-wrap; 
}

/* রিলেটেড প্রোডাক্ট কার্ড ডিজাইন (হোমপেজের মতো ক্লিন) */
.product-card { border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; }
.card-image-wrapper { height: 220px; padding: 20px; background: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; overflow: hidden; }
.card-image-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; transition: transform 0.5s ease; }
.product-card:hover .card-image-wrapper img { transform: scale(1.08); }
.card-info { padding: 20px; text-align: center; }
.card-info h3 { font-size: 16px; margin-bottom: 10px; color: #fff; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.card-info a { text-decoration: none; }
.card-info a:hover h3 { color: var(--accent-orange); }
.card-info .price { font-size: 20px; color: var(--accent-orange); font-weight: 800; margin-bottom: 15px; }
.add-to-cart-btn { width: 100%; padding: 12px; font-size: 14px; font-weight: bold; border-radius: 8px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); transition: 0.3s; cursor: pointer; }
.add-to-cart-btn:hover { background: var(--accent-orange); border-color: var(--accent-orange); color: #fff; box-shadow: 0 5px 15px rgba(255,115,0,0.3); }
</style>

<section class="product-details-section">
    <div class="details-container glass-panel">
        
        <div class="gallery-wrapper">
            <div class="swiper mainProductSwiper">
                <div class="swiper-wrapper">
                    <?php foreach($gallery_images as $img): ?>
                        <div class="swiper-slide main-img-container">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if(count($gallery_images) > 1): ?>
                <div class="swiper thumbProductSwiper">
                    <div class="swiper-wrapper">
                        <?php foreach($gallery_images as $img): ?>
                            <div class="swiper-slide thumb-slide">
                                <img src="<?= $img ?>" alt="thumb">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="details-info">
            <h1><?= htmlspecialchars($product['title']) ?></h1>
            <span class="category-badge"><?= htmlspecialchars($product['category']) ?></span>
            
            <h2 class="price">৳ <?= number_format($product['price'], 2) ?></h2>
            
            <div class="description"><?= htmlspecialchars($product['description']) ?></div>
            
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
                <a href="wishlist.php?add=<?= $product['id'] ?>" class="btn btn-glass" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-heart" style="color: #ff4757;"></i> Save
                </a>
            </div>
        </div>

    </div>
</section>

<?php if(count($related_products) > 0): ?>
<section class="trending-products" style="padding-top: 20px;">
    <h2 class="section-title" style="font-size: 26px; margin-bottom: 30px; text-align: center;">RELATED <span class="highlight">PRODUCTS</span></h2>
    
    <div class="product-grid">
        <?php foreach($related_products as $rel_product): ?>
            <?php 
                $rel_img_src = filter_var($rel_product['image'], FILTER_VALIDATE_URL) ? $rel_product['image'] : 'assets/images/products/'.$rel_product['image']; 
            ?>
            <div class="product-card glass-panel">
                <div class="card-image-wrapper">
                    <a href="product_details.php?id=<?= $rel_product['id'] ?>" style="display: block; width: 100%; height: 100%;">
                        <img src="<?= $rel_img_src ?>" alt="<?= htmlspecialchars($rel_product['title']) ?>">
                    </a>
                </div>
                
                <div class="card-info">
                    <a href="product_details.php?id=<?= $rel_product['id'] ?>">
                        <h3><?= htmlspecialchars($rel_product['title']) ?></h3>
                    </a>
                    <p class="price">৳<?= number_format($rel_product['price'], 2) ?></p>
                    
                    <button class="add-to-cart-btn" onclick="addToCart(<?= $rel_product['id'] ?>)">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var thumbSwiper = new Swiper(".thumbProductSwiper", {
        spaceBetween: 15,
        slidesPerView: 5,
        freeMode: true,
        watchSlidesProgress: true,
    });
    
    var mainSwiper = new Swiper(".mainProductSwiper", {
        spaceBetween: 10,
        effect: "fade", 
        fadeEffect: { crossFade: true }, 
        thumbs: {
            swiper: thumbSwiper, 
        },
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
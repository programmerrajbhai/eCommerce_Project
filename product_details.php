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
    padding: 20px; /* ভেতরে একটু গ্যাপ রাখার জন্য */
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
    opacity: 0.2; /* গ্লো একটু কমানো হলো যাতে ছবি ক্লিয়ার থাকে */
    z-index: 0;
}

/* 🚀 HD ইমেজ ফিক্স: ঘোলা ও বাঁকা হওয়া বন্ধ করা হলো */
.main-img-container img {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    object-fit: contain; /* ছবি যাতে কেটে না যায় */
    z-index: 1;
    animation: none !important; /* আগের বাঁকা অ্যানিমেশন বন্ধ */
    transform: none !important; /* রোটেশন বন্ধ */
    filter: none !important; /* ড্রপ-শ্যাডো বন্ধ যাতে ডার্ক ইমেজে সমস্যা না হয় */
}

/* নিচে ছোট থাম্বনেইল ছবিগুলো */
.thumb-slider {
    width: 100%;
    height: 80px;
}

.thumb-slide {
    width: 80px !important;
    height: 80px !important; /* একদম স্কয়ার সাইজ */
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.3s;
    background: rgba(255,255,255,0.05);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 4px; /* বর্ডারের ভেতরে হালকা গ্যাপ */
}

/* থাম্বনেইল ইমেজ ফিক্স */
.thumb-slide img {
    width: 100% !important;
    height: 100% !important;
    max-width: none !important;
    max-height: none !important;
    object-fit: cover; /* বক্স পুরোপুরি ফিল করার জন্য */
    border-radius: 5px;
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

/* ডিসক্রিপশন টেক্সট ফিক্স (যাতে লাইনগুলো গায়ে গায়ে না লেগে থাকে) */
.details-info .description {
    color: #cccccc;
    line-height: 1.8;
    margin-bottom: 25px;
    font-size: 15px;
    white-space: pre-wrap; /* ইউজারের দেওয়া স্পেস ও এন্টার ঠিক রাখার জন্য */
}
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
                <button class="btn btn-glass">
                    <i class="fa-regular fa-heart"></i> Wishlist
                </button>
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var thumbSwiper = new Swiper(".thumbProductSwiper", {
        spaceBetween: 15,
        slidesPerView: 5, /* ৫টি থাম্বনেইল একসাথে দেখাবে */
        freeMode: true,
        watchSlidesProgress: true,
    });
    
    var mainSwiper = new Swiper(".mainProductSwiper", {
        spaceBetween: 10,
        effect: "fade", 
        fadeEffect: { crossFade: true }, // এটি ছবির ওভারল্যাপিং বন্ধ করবে
        thumbs: {
            swiper: thumbSwiper, 
        },
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
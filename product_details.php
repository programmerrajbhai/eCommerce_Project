<?php
// product_details.php
require_once 'includes/header.php';

// URL থেকে প্রোডাক্ট আইডি নেওয়া (যেমন: product_details.php?id=1)
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

// গ্যালারি ইমেজগুলোকে কমা দিয়ে ভাগ করে অ্যারে বানানো
$gallery_images = [];
if (!empty($product['gallery'])) {
    $gallery_links = explode(',', $product['gallery']);
    foreach ($gallery_links as $link) {
        $link = trim($link); // লিংকের দুপাশের স্পেস রিমুভ করা
        if (!empty($link)) {
            $gallery_images[] = filter_var($link, FILTER_VALIDATE_URL) ? $link : 'assets/images/products/'.$link;
        }
    }
}
// মেইন ইমেজটাকেও গ্যালারির প্রথমে রাখা হলো
array_unshift($gallery_images, $main_img);
?>

<style>
/* =========================================
   প্রোডাক্ট গ্যালারি স্লাইডার ডিজাইন
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
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: var(--glass-border);
    overflow: hidden;
}

.main-img-container img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    transition: 0.5s ease;
}

/* নিচে ছোট থাম্বনেইল ছবিগুলো */
.thumb-slider {
    width: 100%;
    height: 80px;
}

.thumb-slide {
    width: 80px !important;
    height: 100%;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: 0.3s;
    background: rgba(255,255,255,0.05);
    display: flex;
    justify-content: center;
    align-items: center;
}

.thumb-slide img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    opacity: 0.6;
    transition: 0.3s;
}

/* অ্যাকটিভ থাম্বনেইলের স্টাইল */
.thumb-slide.swiper-slide-thumb-active {
    border-color: var(--accent-orange);
    background: rgba(255, 115, 0, 0.1);
}
.thumb-slide.swiper-slide-thumb-active img, .thumb-slide:hover img {
    opacity: 1;
    transform: scale(1.1);
}
</style>

<section class="product-details-section">
    <div class="details-container glass-panel">
        
        <div class="gallery-wrapper">
            <div class="swiper mainProductSwiper">
                <div class="swiper-wrapper">
                    <?php foreach($gallery_images as $img): ?>
                        <div class="swiper-slide main-img-container">
                            <img src="<?= $img ?>" alt="<?= $product['title'] ?>">
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
            <h1><?= $product['title'] ?></h1>
            <span class="category-badge"><?= $product['category'] ?></span>
            
            <h2 class="price">৳ <?= number_format($product['price'], 2) ?></h2>
            
            <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            
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
    // থাম্বনেইল স্লাইডার ইনিশিয়ালাইজ
    var thumbSwiper = new Swiper(".thumbProductSwiper", {
        spaceBetween: 15,
        slidesPerView: "auto", // কন্টেন্ট অনুযায়ী সাইজ নিবে
        freeMode: true,
        watchSlidesProgress: true,
    });
    
    // মেইন ইমেজ স্লাইডার ইনিশিয়ালাইজ (থাম্বনেইলের সাথে কানেক্ট করা)
    var mainSwiper = new Swiper(".mainProductSwiper", {
        spaceBetween: 10,
        effect: "fade", // ছবি চেঞ্জ হওয়ার সময় ফেড ইফেক্ট
        thumbs: {
            swiper: thumbSwiper, // এই মেইন স্লাইডারকে থাম্বনেইলের সাথে লিংক করে দেওয়া হলো
        },
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
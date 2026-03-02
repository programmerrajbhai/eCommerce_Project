<?php
// index.php
require_once 'includes/header.php';

// ডেটাবেস থেকে লেটেস্ট ৪টি প্রোডাক্ট আনার কোড
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$products = $stmt->fetchAll();

// ডেটাবেস থেকে ক্যাটাগরিগুলো আনার কোড (যাতে ডাইনামিক ক্যাটাগরি তৈরি হয়)
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category != '' LIMIT 6");
$categories = $cat_stmt->fetchAll();
?>


<section class="ecommerce-slider">
    <div class="slides-container">
        
        <div class="slide active">
            <div class="slide-text">
                <h3>NEW ARRIVALS</h3>
                <h1>PREMIUM <span class="highlight">SNEAKERS</span></h1>
                <p>Up to 40% OFF on the latest collection. Step up your style game today!</p>
                <a href="products.php?category=Shoes" class="btn btn-primary">Shop Collection <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="slide-image">
                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80" alt="Premium Sneaker">
            </div>
        </div>

        <div class="slide">
            <div class="slide-text">
                <h3>SMART GADGETS</h3>
                <h1>NEXT GEN <span class="highlight">WATCHES</span></h1>
                <p>Track your fitness with our premium smartwatches. Stay connected always.</p>
                <a href="products.php?category=Electronics" class="btn btn-primary">View Gadgets <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="slide-image">
                <img src="https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=600&q=80" alt="Smart Watch">
            </div>
        </div>

        <div class="slide">
            <div class="slide-text">
                <h3>TOP QUALITY</h3>
                <h1>WIRELESS <span class="highlight">AUDIO</span></h1>
                <p>Experience pure sound with our noise-cancelling wireless headphones.</p>
                <a href="products.php?category=Accessories" class="btn btn-primary">Discover Audio <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="slide-image">
                <img src="https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=600&q=80" alt="Headphones" style="border-radius: 50%;">
            </div>
        </div>

    </div>
    
    <button class="slider-btn prev" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="slider-btn next" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>

    <div class="slider-dots">
        <span class="dot active" onclick="currentSlide(0)"></span>
        <span class="dot" onclick="currentSlide(1)"></span>
        <span class="dot" onclick="currentSlide(2)"></span>
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
<?php
// index.php
require_once 'includes/header.php';

// ডেটাবেস থেকে লেটেস্ট ৪টি প্রোডাক্ট আনার কোড (বর্তমানে ডেটাবেস ফাঁকা থাকলে ডেমো দেখাবে)
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$products = $stmt->fetchAll();
?>

<section class="hero-section">
    <div class="hero-content">
        <h1>WE DELIVER <br><span class="highlight">PREMIUM</span> <br>PRODUCTS.</h1>
        <p>Transforming your shopping experience with high-end digital & physical assets. We specialize in top-tier quality.</p>
        
        <div class="hero-buttons">
            <a href="#shop" class="btn btn-primary">Shop Now <i class="fa-solid fa-arrow-right"></i></a>
            <a href="#" class="btn btn-glass">View Portfolio</a>
        </div>
    </div>

    <div class="hero-image-wrapper">
        <div class="glow-shape">
            <span>R.</span>
        </div>
        <img src="https://pngimg.com/d/sneakers_PNG2.png" alt="Premium Sneaker" class="floating-product">
    </div>
</section>

<section id="shop" class="trending-products">
    <h2 class="section-title">TRENDING <span class="highlight">NOW</span></h2>
    
    <div class="product-grid">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card glass-panel">
                    <div class="card-image-wrapper">
                        <img src="assets/images/products/<?= $product['image'] ?>" alt="<?= $product['title'] ?>" class="card-image">
                    </div>
                    <div class="card-info">
                        <h3><?= $product['title'] ?></h3>
                        <p class="price">৳<?= number_format($product['price'], 2) ?></p>
                        <button class="btn btn-glass add-to-cart-btn">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="product-card glass-panel">
                <div class="card-image-wrapper">
                    <img src="https://pngimg.com/d/sneakers_PNG2.png" alt="Sneaker" class="card-image">
                </div>
                <div class="card-info">
                    <h3>Premium Jordan</h3>
                    <p class="price">৳ 3,500</p>
                    <button class="btn btn-glass add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card glass-panel">
                <div class="card-image-wrapper">
                    <img src="https://freepngimg.com/thumb/watch/22425-2-luxury-watch-transparent-image.png" alt="Watch" class="card-image">
                </div>
                <div class="card-info">
                    <h3>Smart Watch Pro</h3>
                    <p class="price">৳ 4,200</p>
                    <button class="btn btn-glass add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card glass-panel">
                <div class="card-image-wrapper">
                    <img src="https://pngimg.com/d/headphones_PNG101979.png" alt="Headphone" class="card-image">
                </div>
                <div class="card-info">
                    <h3>Wireless Headphone</h3>
                    <p class="price">৳ 2,100</p>
                    <button class="btn btn-glass add-to-cart-btn">Add to Cart</button>
                </div>
            </div>
            
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
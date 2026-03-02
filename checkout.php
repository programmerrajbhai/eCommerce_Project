<?php
// checkout.php
require_once 'includes/header.php';

// কার্ট ফাঁকা থাকলে চেকআউটে ঢুকতে দেবে না, হোমপেজে পাঠিয়ে দেবে
if (empty($_SESSION['cart'])) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

// ডেটাবেস থেকে শিপিং সেটিংস নিয়ে আসা (Area and Cost)
$stmt = $pdo->query("SELECT * FROM shipping_settings");
$shipping_areas = $stmt->fetchAll();

$message = '';

// যদি ফর্ম সাবমিট করা হয় (অর্ডার প্লেস করা হয়)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // নতুন ইউজার হলে অ্যাকাউন্ট তৈরির জন্য
    $address = $_POST['address'];
    $area_cost = (float)$_POST['shipping_area'];
    $payment_method = $_POST['payment_method'];

    try {
        $pdo->beginTransaction(); // ট্রানজেকশন শুরু (যাতে কোনো ভুল হলে ডেটাবেসে অর্ধেক ডেটা সেভ না হয়)

        // ১. চেক করা ইউজার আগে থেকে আছে কিনা, না থাকলে নতুন তৈরি করা
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $user_id = $user['id'];
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            $user_id = $pdo->lastInsertId();
        }

        // ২. কার্টের মোট দাম হিসাব করা
        $cart_total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_total += $item['price'] * $item['quantity'];
        }
        $grand_total = $cart_total + $area_cost;

        // ৩. Order টেবিলে ডেটা ইনসার্ট করা
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_cost, payment_method, shipping_address, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $grand_total, $area_cost, $payment_method, $address]);
        $order_id = $pdo->lastInsertId();

        // ৪. Order Items টেবিলে প্রোডাক্টগুলো ইনসার্ট করা
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $item) {
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        $pdo->commit(); // সব ঠিক থাকলে ডেটাবেসে ফাইনাল সেভ করা

        // কার্ট ক্লিয়ার করা এবং সাকসেস মেসেজ দেখানো
        unset($_SESSION['cart']);
        $message = "Order placed successfully! Your Order ID is: #RC-" . str_pad($order_id, 4, '0', STR_PAD_LEFT);

    } catch (Exception $e) {
        $pdo->rollBack(); // এরর হলে কোনো ডেটা সেভ হবে না
        $message = "Error placing order: " . $e->getMessage();
    }
}
?>

<section class="checkout-section">
    <?php if ($message): ?>
        <div class="glass-panel" style="max-width: 600px; margin: 100px auto; padding: 40px; text-align: center;">
            <i class="fa-solid fa-circle-check" style="font-size: 60px; color: #2ecc71; margin-bottom: 20px;"></i>
            <h2><?= $message ?></h2>
            <p style="margin: 20px 0; color: #aaa;">Thank you for shopping with us!</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <h2 class="section-title" style="margin-top: 50px;">SECURE <span class="highlight">CHECKOUT</span></h2>
        
        <form action="checkout.php" method="POST" class="checkout-container">
            
            <div class="checkout-form glass-panel">
                <h3 style="margin-bottom: 20px; border-bottom: var(--glass-border); padding-bottom: 10px;">Billing Details</h3>
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="For order updates & account">
                </div>

                <div class="form-group">
                    <label>Account Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Create a password for your account">
                </div>

                <div class="form-group">
                    <label>Delivery Address</label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="House, Street, City..."></textarea>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                    </select>
                </div>
            </div>

            <div class="order-summary glass-panel">
                <h3 style="margin-bottom: 20px; border-bottom: var(--glass-border); padding-bottom: 10px;">Order Summary</h3>
                
                <div class="summary-items">
                    <?php 
                    $subtotal = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $subtotal += $item['price'] * $item['quantity'];
                    ?>
                        <div class="s-item" style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px;">
                            <span><?= $item['title'] ?> <b>(x<?= $item['quantity'] ?>)</b></span>
                            <span>৳ <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr style="border: var(--glass-border); margin: 20px 0;">

                <div class="form-group">
                    <label>Shipping Area</label>
                    <select name="shipping_area" id="shipping_area" class="form-control" onchange="calculateTotal()" required>
                        <option value="" disabled selected>Select Delivery Area</option>
                        <?php foreach($shipping_areas as $area): ?>
                            <option value="<?= $area['cost'] ?>"><?= $area['area_name'] ?> - ৳ <?= $area['cost'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="totals" style="margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #aaa;">
                        <span>Subtotal:</span>
                        <span id="subtotal_display" data-subtotal="<?= $subtotal ?>">৳ <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #aaa;">
                        <span>Shipping:</span>
                        <span id="shipping_display">৳ 0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 22px; font-weight: bold; color: var(--accent-orange); margin-top: 15px; border-top: var(--glass-border); padding-top: 15px;">
                        <span>Total:</span>
                        <span id="grand_total_display">৳ <?= number_format($subtotal, 2) ?></span>
                    </div>
                </div>

                <button type="submit" name="place_order" class="btn btn-primary" style="width: 100%; margin-top: 30px; font-size: 16px;">
                    Confirm Order <i class="fa-solid fa-check-circle"></i>
                </button>
            </div>
            
        </form>
    <?php endif; ?>
</section>

<script>
function calculateTotal() {
    let subtotal = parseFloat(document.getElementById('subtotal_display').getAttribute('data-subtotal'));
    let shippingSelect = document.getElementById('shipping_area');
    let shippingCost = parseFloat(shippingSelect.value);
    
    if(!isNaN(shippingCost)) {
        let grandTotal = subtotal + shippingCost;
        
        document.getElementById('shipping_display').innerText = '৳ ' + shippingCost.toFixed(2);
        document.getElementById('grand_total_display').innerText = '৳ ' + grandTotal.toFixed(2);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
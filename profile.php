<?php
// profile.php
require_once 'includes/header.php';

// লগআউট লজিক
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    echo "<script>window.location.href='profile.php';</script>";
    exit;
}

$error = '';

// লগইন লজিক
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        echo "<script>window.location.href='profile.php';</script>";
        exit;
    } else {
        $error = "Invalid Email or Password!";
    }
}

// যদি লগইন করা থাকে, তাহলে অর্ডার হিস্ট্রি নিয়ে আসা হবে
$orders = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
}
?>

<style>
    .auth-container { max-width: 400px; margin: 120px auto 80px; padding: 40px; text-align: center; }
    .auth-container h2 { margin-bottom: 20px; }
    .form-control { width: 100%; padding: 14px; margin-bottom: 15px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; outline: none; }
    .form-control:focus { border-color: var(--accent-orange); }
    
    .profile-dashboard { max-width: 1000px; margin: 120px auto 80px; padding: 40px; }
    .order-table { width: 100%; border-collapse: collapse; margin-top: 20px; text-align: left; }
    .order-table th, .order-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .order-table th { color: var(--accent-orange); text-transform: uppercase; font-size: 14px; }
    .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
</style>

<?php if (!isset($_SESSION['user_id'])): ?>
    <section class="auth-container glass-panel">
        <h2>USER <span class="highlight">LOGIN</span></h2>
        <p style="color: #aaa; margin-bottom: 30px; font-size: 14px;">Enter the email and password you used during checkout.</p>
        
        <?php if($error): ?>
            <p style="color: #ff4757; margin-bottom: 15px;"><?= $error ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Login to Account</button>
        </form>
    </section>

<?php else: ?>
    <section class="profile-dashboard glass-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 30px;">
            <div>
                <h2>Welcome, <span class="highlight"><?= htmlspecialchars($_SESSION['user_name']) ?></span>!</h2>
                <p style="color: #aaa;">Here you can track your recent orders.</p>
            </div>
            <a href="profile.php?action=logout" class="btn btn-glass" style="color: #ff4757; border-color: #ff4757;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>

        <h3><i class="fa-solid fa-box-open" style="color: var(--accent-orange);"></i> My Order History</h3>
        
        <div style="overflow-x: auto;">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td style="font-weight: bold;">#RC-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            <td style="color: var(--accent-orange); font-weight: bold;">৳ <?= number_format($order['total_amount'], 2) ?></td>
                            <td style="text-transform: uppercase; font-size: 13px;"><?= str_replace('_', ' ', $order['payment_method']) ?></td>
                            <td>
                                <?php
                                    $color = '#aaa';
                                    if($order['status'] == 'pending') $color = '#f1c40f';
                                    if($order['status'] == 'processing') $color = '#3498db';
                                    if($order['status'] == 'shipped') $color = '#9b59b6';
                                    if($order['status'] == 'delivered') $color = '#2ecc71';
                                    if($order['status'] == 'cancelled') $color = '#e74c3c';
                                ?>
                                <span class="badge" style="color: <?= $color ?>; border: 1px solid <?= $color ?>;">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if(count($orders) == 0): ?>
                        <tr><td colspan="5" style="text-align: center; color: #aaa; padding: 30px;">You haven't placed any orders yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
<?php
// admin/index.php
require_once 'includes/header.php';

// ১. ডেটাবেস থেকে পরিসংখ্যান (Statistics) নিয়ে আসা
$total_orders = $pdo->query("SELECT count(*) FROM orders")->fetchColumn();
$pending_orders = $pdo->query("SELECT count(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$total_revenue = $pdo->query("SELECT sum(total_amount) FROM orders WHERE status = 'delivered'")->fetchColumn();
$total_products = $pdo->query("SELECT count(*) FROM products")->fetchColumn();

// যদি কোনো রেভিনিউ না থাকে তাহলে 0 দেখাবে
$total_revenue = $total_revenue ? $total_revenue : 0;

// ২. ড্যাশবোর্ডে দেখানোর জন্য সাম্প্রতিক ৫টি অর্ডার নিয়ে আসা
$recent_orders = $pdo->query("
    SELECT orders.*, users.name 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.id DESC LIMIT 5
")->fetchAll();
?>

<h1 style="margin-bottom: 30px; font-weight: 800;">DASHBOARD <span style="color: var(--accent-orange);">OVERVIEW</span></h1>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px;">
    
    <div class="glass-card" style="border-left: 4px solid var(--accent-orange);">
        <h3 style="color: #aaa; font-size: 14px; margin-bottom: 10px;">Total Revenue</h3>
        <h2 style="font-size: 28px;">৳ <?= number_format($total_revenue, 2) ?></h2>
    </div>
    
    <div class="glass-card" style="border-left: 4px solid #3498db;">
        <h3 style="color: #aaa; font-size: 14px; margin-bottom: 10px;">Total Orders</h3>
        <h2 style="font-size: 28px;"><?= $total_orders ?></h2>
    </div>

    <div class="glass-card" style="border-left: 4px solid #f1c40f;">
        <h3 style="color: #aaa; font-size: 14px; margin-bottom: 10px;">Pending Orders</h3>
        <h2 style="font-size: 28px; color: #f1c40f;"><?= $pending_orders ?></h2>
    </div>

    <div class="glass-card" style="border-left: 4px solid #2ecc71;">
        <h3 style="color: #aaa; font-size: 14px; margin-bottom: 10px;">Total Products</h3>
        <h2 style="font-size: 28px; color: #2ecc71;"><?= $total_products ?></h2>
    </div>

</div>

<div class="glass-card">
    <h3 style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
        <i class="fa-solid fa-clock" style="color: var(--accent-orange);"></i> Recent Orders
    </h3>
    
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td style="font-weight: bold;">#RC-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($order['name']) ?></td>
                        <td style="color: var(--accent-orange); font-weight: bold;">৳ <?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <?php
                                // স্ট্যাটাস অনুযায়ী কালার সেট করা
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
                
                <?php if(count($recent_orders) == 0): ?>
                    <tr><td colspan="4" style="text-align: center; color: #aaa; padding: 20px;">No recent orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> </body>
</html>
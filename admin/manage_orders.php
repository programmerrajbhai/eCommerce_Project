<?php
// admin/manage_orders.php
require_once 'includes/header.php';

// স্ট্যাটাস আপডেট করার লজিক
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    
    echo "<script>alert('Order Status Updated Successfully!'); window.location.href='manage_orders.php';</script>";
}

// ডেটাবেস থেকে সব অর্ডার নিয়ে আসা (ইউজারের নাম সহ)
$stmt = $pdo->query("
    SELECT orders.*, users.name, users.email 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.id DESC
");
$orders = $stmt->fetchAll();
?>

<h1 style="margin-bottom: 30px; font-weight: 800;">MANAGE <span style="color: var(--accent-orange);">ORDERS</span></h1>

<div class="glass-card">
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Date</th>
                    <th>Current Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td style="font-weight: bold;">#RC-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td><?= $order['name'] ?><br><small style="color: #aaa;"><?= $order['email'] ?></small></td>
                        <td style="color: var(--accent-orange); font-weight: bold;">৳ <?= number_format($order['total_amount'], 2) ?></td>
                        <td style="text-transform: uppercase;"><?= str_replace('_', ' ', $order['payment_method']) ?></td>
                        <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                        
                        <td>
                            <?php
                                $color = '#aaa';
                                if($order['status'] == 'pending') $color = '#f1c40f';
                                if($order['status'] == 'processing') $color = '#3498db'; // Confirm/Approved
                                if($order['status'] == 'shipped') $color = '#9b59b6';
                                if($order['status'] == 'delivered') $color = '#2ecc71';
                                if($order['status'] == 'cancelled') $color = '#e74c3c'; // Reject
                            ?>
                            <span class="badge" style="color: <?= $color ?>; border: 1px solid <?= $color ?>;">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        
                        <td>
                            <form action="" method="POST" style="display: flex; gap: 10px; align-items: center;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Approved</option>
                                    <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Reject</option>
                                </select>
                                <button type="submit" name="update_status" class="btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($orders) == 0): ?>
                    <tr><td colspan="7" style="text-align: center; color: #aaa;">No orders found!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> </body>
</html>
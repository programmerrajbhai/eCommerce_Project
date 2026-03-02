<?php
// cart_logic.php
session_start();
require_once 'includes/db.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Add to Cart Logic
    if ($action == 'add') {
        $product_id = $_POST['product_id'];
        
        // প্রোডাক্ট ডেটাবেস থেকে খোঁজা
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] += 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // image url চেক করা হচ্ছে (ডেমো লিংকের জন্য)
                $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : 'assets/images/products/'.$product['image'];
                
                $_SESSION['cart'][] = [
                    'id' => $product['id'],
                    'title' => $product['title'],
                    'price' => $product['price'],
                    'image' => $img_src,
                    'quantity' => 1
                ];
            }
            echo json_encode(['status' => 'success']);
            exit;
        }
    }

    // Fetch Cart Logic (কার্টের ডেটা ফ্রন্টএন্ডে পাঠানো)
    if ($action == 'fetch') {
        $html = '';
        $total = 0;
        $count = 0;

        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            foreach ($_SESSION['cart'] as $index => $item) {
                $total += $item['price'] * $item['quantity'];
                $count += $item['quantity'];
                $html .= '
                <div class="cart-item">
                    <img src="'.$item['image'].'" alt="Product">
                    <div class="item-details">
                        <h4>'.$item['title'].'</h4>
                        <p>৳ '.number_format($item['price'], 2).' x '.$item['quantity'].'</p>
                    </div>
                    <button class="remove-item" onclick="removeFromCart('.$index.')"><i class="fa-solid fa-trash"></i></button>
                </div>';
            }
        } else {
            $html = '<p style="text-align:center; margin-top:20px; color:#aaa;">Your cart is empty.</p>';
        }

        echo json_encode(['html' => $html, 'total' => number_format($total, 2), 'count' => $count]);
        exit;
    }

    // Remove from Cart Logic
    if ($action == 'remove') {
        $index = $_POST['index'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // অ্যারে রি-ইনডেক্স
        }
        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
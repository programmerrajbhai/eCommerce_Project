<?php
// includes/footer.php
?>
    <footer style="padding: 40px 8%; text-align: center; margin-top: 50px;">
        <div class="glass-panel" style="padding: 20px;">
            <p>&copy; 2026 RAJ STORE. All Rights Reserved.</p>
        </div>
    </footer>

    <div class="cart-overlay" id="cart-overlay" onclick="toggleCart()"></div>
    
    <div class="offcanvas-cart glass-panel" id="offcanvas-cart">
        <div class="cart-header">
            <h2>Your Cart <span class="highlight">.</span></h2>
            <button class="close-cart" onclick="toggleCart()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div class="cart-items" id="cart-items-container">
            <div class="cart-item">
                <img src="https://pngimg.com/d/sneakers_PNG2.png" alt="Product">
                <div class="item-details">
                    <h4>Premium Jordan</h4>
                    <p>৳ 3,500 x 1</p>
                </div>
                <button class="remove-item"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        
        <div class="cart-footer">
            <h3>Total: <span class="highlight">৳ 3,500</span></h3>
            <a href="checkout.php" class="btn btn-primary" style="display: block; width: 100%; text-align: center; margin-top: 20px;">Proceed to Checkout</a>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
<?php
// includes/header.php

// সেশন স্টার্ট করা হলো (কার্ট এবং ইউজার লগইন ট্র্যাক করার জন্য)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ডেটাবেস কানেকশন যুক্ত করা হলো
require_once 'db.php';

// কার্টে কয়টি আইটেম আছে তা গোনার লজিক (ভবিষ্যতে ডাইনামিক করা হবে)
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raj eCommerce | Premium Store</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

</head>
<body>

<header id="navbar">
    <a href="index.php" class="logo">
        <div class="logo-dot"></div>
        RAJ <span>STORE</span>
    </a>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Shop</a></li>
        <li><a href="index.php#categories">Categories</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>

    <div class="nav-icons" style="display: flex; align-items: center; gap: 15px;">
        
        <form action="search.php" method="GET" style="display: flex; align-items: center; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 2px 12px; transition: 0.3s;">
            <input type="text" name="q" placeholder="Search..." style="background: transparent; border: none; color: #fff; outline: none; padding: 5px; width: 130px; font-size: 13px;" required>
            <button type="submit" style="background: transparent; border: none; color: var(--accent-orange); cursor: pointer; font-size: 14px;"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>

        <a href="wishlist.php"><i class="fa-regular fa-heart"></i></a>
        
        <div class="cart-icon-wrapper" onclick="toggleCart()">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="cart-badge" id="cart-badge-count"><?php echo $cart_count; ?></span>
        </div>

        <a href="profile.php"><i class="fa-regular fa-user"></i></a>

        <i class="fa-solid fa-bars mobile-menu-btn" onclick="toggleMobileMenu()"></i>
    </div>
   
</header>

<script>
    // স্টিকি হেডার স্ক্রিপ্ট
    window.addEventListener("scroll", function() {
        var header = document.querySelector("header");
        header.classList.toggle("scrolled", window.scrollY > 50);
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
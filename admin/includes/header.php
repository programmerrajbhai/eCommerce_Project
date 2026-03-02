<?php
// admin/includes/header.php
session_start();

// লগইন চেক (লগইন না থাকলে login.php তে পাঠিয়ে দেবে)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// মেইন ডেটাবেস কানেক্ট করা হলো (../ দিয়ে এক ফোল্ডার পেছনে যাওয়া হয়েছে)
require_once '../includes/db.php';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Raj Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-dark: #0a0a0c; --panel-bg: rgba(255, 255, 255, 0.05); --accent-orange: #ff7300; --text-light: #ffffff; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { display: flex; background-color: var(--bg-dark); color: var(--text-light); min-height: 100vh; }
        
        .sidebar { width: 250px; background: rgba(10, 10, 12, 0.9); border-right: 1px solid rgba(255,255,255,0.1); padding: 30px 20px; position: fixed; height: 100vh; }
        .sidebar h2 { color: var(--accent-orange); margin-bottom: 40px; font-weight: 800; text-align: center; }
        .sidebar a { display: block; padding: 12px 15px; color: #aaa; text-decoration: none; border-radius: 10px; margin-bottom: 10px; transition: 0.3s; font-size: 15px; }
        .sidebar a:hover, .sidebar a.active { background: var(--accent-orange); color: #fff; box-shadow: 0 0 15px rgba(255,115,0,0.4); }
        .sidebar i { width: 25px; }

        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); }
        .glass-card { background: var(--panel-bg); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 25px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        th { color: var(--accent-orange); text-transform: uppercase; font-size: 13px; }
        tr:hover { background: rgba(255,255,255,0.02); }

        .btn-sm { padding: 6px 12px; background: var(--accent-orange); color: #fff; border: none; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .btn-sm:hover { opacity: 0.8; }
        select.form-control { background: rgba(0,0,0,0.5); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 8px; border-radius: 5px; outline: none; }
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>RAJ ADMIN</h2>
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a href="manage_orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_orders.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-cart-shopping"></i> Manage Orders
        </a>
        
        <a href="manage_products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-box-open"></i> Manage Products
        </a>
        
        <a href="../index.php" target="_blank" style="margin-top: 30px;">
            <i class="fa-solid fa-globe"></i> View Website
        </a>
        <a href="logout.php" style="color: #ff4757; margin-top: 10px;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>

    <div class="main-content">
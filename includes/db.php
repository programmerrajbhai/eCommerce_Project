<?php
// includes/db.php

$host = 'localhost';
$dbname = 'raj_ecommerce';
$username = 'root'; // XAMPP এর ডিফল্ট ইউজারনেম
$password = '';     // XAMPP এর ডিফল্ট পাসওয়ার্ড ফাঁকা থাকে

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // এরর হ্যান্ডেলিং এবং সিকিউরিটি অন করা হলো
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
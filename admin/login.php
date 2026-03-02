<?php
// admin/login.php
session_start();

// যদি আগে থেকেই লগইন করা থাকে, ড্যাশবোর্ডে পাঠিয়ে দেবে
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 하드কোডেড অ্যাডমিন ডিটেইলস (Hardcoded Admin Credentials)
    $admin_email = "admin@raj.com";
    $admin_pass = "admin123";

    if ($email === $admin_email && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Raj Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #0a0a0c; color: #fff; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: rgba(255, 255, 255, 0.05); padding: 40px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); width: 100%; max-width: 400px; text-align: center; }
        .login-box h2 { color: #ff7300; margin-bottom: 20px; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 15px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; outline: none; box-sizing: border-box;}
        .form-control:focus { border-color: #ff7300; }
        .btn { width: 100%; padding: 12px; background: #ff7300; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn:hover { background: #e66800; box-shadow: 0 0 15px rgba(255, 115, 0, 0.5); }
        .error { color: #ff4757; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>RAJ ADMIN</h2>
        <p style="color: #aaa; margin-bottom: 20px; font-size: 14px;">Please login to continue</p>
        
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="email" name="email" class="form-control" placeholder="Admin Email" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn">LOGIN</button>
        </form>
    </div>
</body>
</html>
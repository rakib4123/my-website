<?php
require_once "config.php";

$cart_count = 0;
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $result = $mysqli->query("SELECT SUM(quantity) as count FROM cart WHERE user_id = " . (int)$user_id);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cart_count = $row['count'] ? (int)$row['count'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electro Beats BD - Modern Tech Accessories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-background text-text-primary">
    <header class="bg-surface/80 backdrop-blur-lg sticky top-0 z-50 border-b border-gray-200">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php">
                <img src="logo.jpg" alt="Electro Beats BD Logo" class="h-12 w-auto">
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                <a href="products.php" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors">Shop</a>
                <a href="about.php" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors">About</a>
                <a href="contact.php" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
            </div>

            <div class="flex items-center space-x-6">
                <a href="cart.php" class="relative text-gray-700 hover:text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-blue-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                
                <?php if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                    <a href="account.php" class="text-gray-700 hover:text-blue-600">
                       <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </a>
                    <a href="logout.php" class="text-sm font-semibold text-gray-700 hover:text-blue-600">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="font-semibold text-gray-700 hover:text-blue-600 transition-colors text-sm">Login</a>
                    <a href="register.php" class="btn btn-primary !py-2 !px-4 text-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <main>
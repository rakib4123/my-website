<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
// Security check: If admin is not logged in, redirect to login page
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once "../config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'Admin Panel'; ?> - Electro Beats BD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">
    <div class="w-64 bg-gray-800 text-white p-6 flex flex-col">
        <div class="mb-8">
            <a href="index.php" class="text-2xl font-bold">Admin Panel</a>
        </div>
        <nav class="space-y-3">
            <a href="index.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Dashboard</span></a>
            <a href="products.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Manage Products</span></a>
            <a href="orders.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Manage Orders</span></a>
            <a href="customers.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Manage Customers</span></a>
            <a href="manage_slider.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Manage Homepage Slider</span></a>
            <a href="manage_popup.php" class="flex items-center space-x-2 text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-3 rounded-lg"><span>Manage Popup</span></a>
        </nav>
        <a href="logout.php" class="block mt-auto text-gray-300 hover:bg-gray-700 hover:text-white font-semibold py-2 px-4 rounded-lg border border-gray-700 text-center">Logout</a>
    </div>
    <div class="flex-1 p-10">
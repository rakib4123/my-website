<?php 
$page_title = "Admin Dashboard";
include 'header.php'; 

// Fetch stats for the dashboard
$total_orders = $mysqli->query("SELECT COUNT(id) as count FROM orders")->fetch_assoc()['count'];
$total_customers = $mysqli->query("SELECT COUNT(id) as count FROM users")->fetch_assoc()['count'];
$total_products = $mysqli->query("SELECT COUNT(id) as count FROM products")->fetch_assoc()['count'];
$pending_orders = $mysqli->query("SELECT COUNT(id) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];
?>

<h2 class="text-4xl font-extrabold text-gray-800 mb-8">Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-500">Total Orders</h3>
        <p class="text-4xl font-bold mt-2 text-gray-800"><?php echo $total_orders; ?></p>
    </div>
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-500">Pending Orders</h3>
        <p class="text-4xl font-bold mt-2 text-yellow-500"><?php echo $pending_orders; ?></p>
    </div>
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-500">Total Customers</h3>
        <p class="text-4xl font-bold mt-2 text-gray-800"><?php echo $total_customers; ?></p>
    </div>
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-500">Total Products</h3>
        <p class="text-4xl font-bold mt-2 text-gray-800"><?php echo $total_products; ?></p>
    </div>
</div>

<p class="text-gray-600">Welcome to your admin dashboard. Use the sidebar to manage your store.</p>

<?php include 'footer.php'; ?>
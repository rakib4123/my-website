<?php
include 'header.php';
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){ header("location: login.php"); exit; }
$username = htmlspecialchars($_SESSION["username"]);
?>
<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-10 animate-on-scroll">My Account</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-1 animate-on-scroll">
            <div class="card p-6">
                <h3 class="text-xl font-bold mb-4">Hello, <?php echo $username; ?>!</h3>
                <ul class="space-y-3 text-gray-600">
                    <li><a href="account.php" class="font-bold text-blue-600 block">Dashboard</a></li>
                    <li><a href="order-history.php" class="hover:text-blue-600 block">Order History</a></li>
                    <li><a href="manage-addresses.php" class="hover:text-blue-600 block">Manage Addresses</a></li>
                    <li><a href="logout.php" class="hover:text-blue-600 block pt-3 border-t">Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="md:col-span-3 space-y-8">
            <div class="card p-6 animate-on-scroll">
                 <h3 class="text-xl font-bold mb-4">Dashboard</h3>
                 <p class="text-gray-600">From your account dashboard you can view your recent orders and edit your account details.</p>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
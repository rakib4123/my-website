<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$orders = [];

// Fetch all orders for the current user
$sql = "SELECT id, total_price, order_date, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    $stmt->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-white mb-10 animate-on-scroll">Your Order History</h1>

    <?php if (!empty($orders)): ?>
        <div class="space-y-8 animate-on-scroll">
            <?php foreach ($orders as $order): ?>
                <div class="card p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-700 pb-4 mb-4">
                        <div>
                            <p class="text-xl font-bold">Order #<?php echo $order['id']; ?></p>
                            <p class="text-gray-400">Date: <?php echo date("F j, Y", strtotime($order['order_date'])); ?></p>
                        </div>
                        <div class="mt-2 sm:mt-0 text-right">
                            <p class="text-lg font-semibold">Total: ৳<?php echo number_format($order['total_price'], 2); ?></p>
                            <span class="text-sm font-medium px-3 py-1 rounded-full 
                                <?php 
                                    switch ($order['status']) {
                                        case 'Shipped': echo 'bg-green-500/20 text-green-400'; break;
                                        case 'Processing': echo 'bg-yellow-500/20 text-yellow-400'; break;
                                        default: echo 'bg-gray-500/20 text-gray-400'; break;
                                    }
                                ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-2 text-white">Items in this order:</h4>
                        <ul class="space-y-2">
                            <?php
                            // Fetch items for this specific order
                            $sql_items = "SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
                            if ($stmt_items = $mysqli->prepare($sql_items)) {
                                $stmt_items->bind_param("i", $order['id']);
                                $stmt_items->execute();
                                $result_items = $stmt_items->get_result();
                                while ($item = $result_items->fetch_assoc()) {
                                    echo '<li class="text-gray-300 flex justify-between">
                                            <span>' . htmlspecialchars($item['name']) . ' (x' . $item['quantity'] . ')</span>
                                            <span>৳' . number_format($item['price'] * $item['quantity'], 2) . '</span>
                                          </li>';
                                }
                                $stmt_items->close();
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center card p-12 animate-on-scroll">
            <h2 class="text-2xl font-semibold mb-4 text-white">You have no order history.</h2>
            <p class="text-gray-400 mb-6">All your past orders will be displayed here.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
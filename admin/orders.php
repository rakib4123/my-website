<?php 
$page_title = "Manage Orders";
include 'header.php';

$feedback_message = '';

// Handle order status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = trim($_POST['status']);
    
    $sql_update = "UPDATE orders SET status = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql_update)) {
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $feedback_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">Order #' . $order_id . ' status updated.</div>';
        }
        $stmt->close();
    }
}

// Fetch all orders with user information
$orders = [];
$sql = "SELECT o.id, o.total_price, o.order_date, o.status, o.shipping_address, u.username 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC";
$result = $mysqli->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<h2 class="text-4xl font-extrabold text-gray-800 mb-8">Manage Orders</h2>
<?php echo $feedback_message; ?>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-600">Order ID</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Customer</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Date</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Total</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if(!empty($orders)): ?>
                    <?php foreach($orders as $order): ?>
                        <tr>
                            <td class="p-4 font-semibold">#<?php echo $order['id']; ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($order['username']); ?></td>
                            <td class="p-4 text-gray-500"><?php echo date("M d, Y", strtotime($order['order_date'])); ?></td>
                            <td class="p-4">৳<?php echo number_format($order['total_price'], 2); ?></td>
                            <td class="p-4">
                                <span class="text-sm font-medium px-3 py-1 rounded-full <?php 
                                    if ($order['status'] == 'Shipped' || $order['status'] == 'Completed') echo 'bg-green-100 text-green-800';
                                    elseif ($order['status'] == 'Processing') echo 'bg-yellow-100 text-yellow-800';
                                    elseif ($order['status'] == 'Cancelled') echo 'bg-red-100 text-red-800';
                                    else echo 'bg-gray-100 text-gray-800';
                                ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <form action="orders.php" method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="form-input !py-1 !px-2 !w-32">
                                        <option value="Pending" <?php if($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                        <option value="Shipped" <?php if($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="Completed" <?php if($order['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                        <option value="Cancelled" <?php if($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary !py-1 !px-3 text-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">No orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
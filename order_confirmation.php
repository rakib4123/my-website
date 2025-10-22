<?php
include 'header.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) { header("location: login.php"); exit; }
if (!isset($_GET['id']) || empty($_GET['id'])) { header("location: account.php"); exit; }

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['id'];
$order = null;
$order_items = [];

// Fetch the main order details, ensuring it belongs to the logged-in user for security
$sql_order = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
if ($stmt_order = $mysqli->prepare($sql_order)) {
    $stmt_order->bind_param("ii", $order_id, $user_id);
    $stmt_order->execute();
    $result = $stmt_order->get_result();
    if ($result->num_rows == 1) {
        $order = $result->fetch_assoc();
    } else {
        header("location: order-history.php");
        exit;
    }
    $stmt_order->close();
}

// Fetch the items for this order
$sql_items = "SELECT p.name, p.thumbnail_image, oi.quantity, oi.price 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
if ($stmt_items = $mysqli->prepare($sql_items)) {
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    while ($item = $result_items->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt_items->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <div class="max-w-3xl mx-auto card p-8 animate-on-scroll">
        <div class="text-center border-b pb-6 mb-6">
            <h1 class="text-3xl font-extrabold text-green-600">Thank You For Your Order!</h1>
            <p class="text-gray-600 mt-2">Your bill and order confirmation.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-2">Order Details</h2>
                <p class="text-gray-600"><strong>Order #:</strong> <?php echo $order['id']; ?></p>
                <p class="text-gray-600"><strong>Order Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['order_date'])); ?></p>
                <p class="text-gray-600"><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?></p>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800 mb-2">Shipping Address</h2>
                <div class="text-gray-600 whitespace-pre-line"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4 pt-6 border-t">Items Ordered</h2>
            <div class="space-y-4">
                <?php foreach($order_items as $item): ?>
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo htmlspecialchars($item['thumbnail_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-16 h-16 object-contain rounded-lg border p-1">
                            <div>
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo $item['quantity']; ?> x ৳<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-800">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t space-y-2">
             <div class="flex justify-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span>৳<?php echo number_format($order['total_price'] - $order['delivery_charge'], 2); ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Delivery Charge:</span>
                        <span>৳<?php echo number_format($order['delivery_charge'], 2); ?></span>
                    </div>
                    <div class="flex justify-between font-bold text-xl text-gray-800 mt-2 pt-2 border-t">
                        <span>Grand Total:</span>
                        <span>৳<?php echo number_format($order['total_price'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-8 pt-6 border-t">
            <a href="products.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
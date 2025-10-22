<?php
// --- ALL PHP LOGIC IS NOW AT THE TOP, BEFORE ANY HTML ---

require_once "config.php";

// Security Check: If user is not logged in, redirect them immediately.
// This must happen before any HTML is sent.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// --- Now we can proceed with fetching the cart data ---
$user_id = $_SESSION['id'];
$cart_items = [];
$total_price = 0;

$sql = "SELECT p.id, p.name, p.price, p.thumbnail_image, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";

if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $cart_items[] = $row;
            $total_price += $row['price'] * $row['quantity'];
        }
    }
    $stmt->close();
}

// --- NOW, WE START THE HTML OUTPUT ---
include 'header.php';
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-10 animate-on-scroll">Your Cart</h1>
    <?php if (!empty($cart_items)): ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-on-scroll">
            <div class="lg:col-span-2 card p-6">
                <div class="space-y-6">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex justify-between items-center border-b pb-4 last:border-b-0">
                        <div class="flex items-center space-x-4">
                            <img src="<?php echo htmlspecialchars($item['thumbnail_image']); ?>" class="w-24 h-24 object-contain rounded-lg">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                <form action="remove_from_cart.php" method="POST" class="mt-1">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:underline font-semibold text-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                        <p class="font-semibold text-lg">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="lg:col-span-1">
                <div class="card p-6">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-4">Order Summary</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>৳<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="flex justify-between font-bold text-xl border-t pt-4 mt-4">
                            <span>Total</span>
                            <span>৳<?php echo number_format($total_price, 2); ?></span>
                        </div>
                    </div>
                    <a href="checkout.php" class="w-full text-center mt-6 btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center card p-12 animate-on-scroll">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Your cart is empty.</h2>
            <a href="products.php" class="btn btn-primary mt-4">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
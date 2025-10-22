<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$cart_items = [];
$subtotal = 0;

// Fetch cart items for calculation and display
$sql_cart = "SELECT p.id, p.name, p.price, p.thumbnail_image, c.quantity 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
if ($stmt_cart = $mysqli->prepare($sql_cart)) {
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();
    if ($result_cart->num_rows > 0) {
        while ($row = $result_cart->fetch_assoc()) {
            $cart_items[] = $row;
            $subtotal += $row['price'] * $row['quantity'];
        }
    } else {
        header("location: products.php"); // Redirect if cart is empty
        exit;
    }
    $stmt_cart->close();
}

// Fetch user's saved addresses
$addresses = [];
$sql_addr = "SELECT * FROM user_addresses WHERE user_id = ?";
if ($stmt_addr = $mysqli->prepare($sql_addr)) {
    $stmt_addr->bind_param("i", $user_id);
    $stmt_addr->execute();
    $result_addr = $stmt_addr->get_result();
    while ($row = $result_addr->fetch_assoc()) {
        $addresses[] = $row;
    }
    $stmt_addr->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-10 animate-on-scroll">Checkout</h1>

    <form action="process_order.php" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="card p-8 animate-on-scroll">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Shipping Information</h2>
                <?php if (!empty($addresses)): ?>
                <div class="mb-6">
                    <label for="saved_address" class="block text-sm font-medium text-gray-700 mb-1">Use a Saved Address</label>
                    <select id="saved_address" class="form-input">
                        <option value="">-- Or enter a new address below --</option>
                        <?php foreach($addresses as $addr): ?>
                            <option value='<?php echo json_encode($addr); ?>'><?php echo htmlspecialchars($addr['full_name'] . ', ' . $addr['address_line1']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <div class="space-y-4">
                    <div><label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label><input type="text" id="full_name" name="full_name" class="mt-1 form-input" required></div>
                    <div><label for="phone" class="block text-sm font-medium text-gray-700">Contact Number</label><input type="tel" id="phone" name="phone" class="mt-1 form-input" required></div>
                    <div><label for="address_line1" class="block text-sm font-medium text-gray-700">Street Address</label><input type="text" id="address_line1" name="address_line1" class="mt-1 form-input" required></div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label for="city" class="block text-sm font-medium text-gray-700">City</label><input type="text" id="city" name="city" class="mt-1 form-input" required></div>
                        <div><label for="state" class="block text-sm font-medium text-gray-700">State / Division</label><input type="text" id="state" name="state" class="mt-1 form-input" required></div>
                    </div>
                </div>
            </div>
            <div class="card p-8 animate-on-scroll">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Delivery & Payment</h2>
                <div class="mb-6"><h3 class="text-lg font-semibold text-gray-700 mb-3">Delivery Location</h3><div class="space-y-3"><label class="flex items-center p-4 border rounded-lg cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500"><input type="radio" name="delivery_option" value="inside_dhaka" class="h-5 w-5 text-blue-600" checked><span class="ml-4 font-semibold">Inside Dhaka (৳70)</span></label><label class="flex items-center p-4 border rounded-lg cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500"><input type="radio" name="delivery_option" value="outside_dhaka" class="h-5 w-5 text-blue-600"><span class="ml-4 font-semibold">Outside Dhaka (৳130)</span></label></div></div>
                <div><h3 class="text-lg font-semibold text-gray-700 mb-3">Payment Method</h3><div class="space-y-3"><label class="flex items-center p-4 border rounded-lg cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500"><input type="radio" name="payment_method" value="Cash on Delivery" class="h-5 w-5 text-blue-600" checked><span class="ml-4 font-semibold">Cash on Delivery</span></label></div></div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="card p-8 sticky top-28 animate-on-scroll">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Order Summary</h2>
                <div class="space-y-4 mb-4 border-b pb-4">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                            <span class="text-gray-600">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span id="subtotal_span">৳<?php echo number_format($subtotal, 2); ?></span></div>
                    <div class="flex justify-between text-gray-600"><span>Delivery Charge</span><span id="delivery_charge_span">৳70.00</span></div>
                    <div class="flex justify-between font-bold text-xl text-gray-800 border-t pt-4 mt-4"><span>Total</span><span id="total_price_span">৳<?php echo number_format($subtotal + 70, 2); ?></span></div>
                </div>
                <button type="submit" class="w-full btn btn-primary mt-8">Place Order</button>
            </div>
        </div>
    </form>
</div>

<script>
    // JS for auto-filling saved address
    document.getElementById('saved_address')?.addEventListener('change', function() { /* ... */ });
    // JS for dynamically updating total price
    const subtotal = <?php echo $subtotal; ?>;
    const deliveryRadios = document.querySelectorAll('input[name="delivery_option"]');
    const deliveryChargeSpan = document.getElementById('delivery_charge_span');
    const totalPriceSpan = document.getElementById('total_price_span');
    const charges = { inside_dhaka: 70, outside_dhaka: 130 };
    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const charge = charges[this.value];
            const total = subtotal + charge;
            deliveryChargeSpan.textContent = '৳' + charge.toFixed(2);
            totalPriceSpan.textContent = '৳' + total.toFixed(2);
        });
    });
</script>
<?php include 'footer.php'; ?>
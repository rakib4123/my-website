<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$payment_methods = [];
$feedback_message = '';

// --- Handle Add/Delete Requests ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle DELETE request
    if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['method_id'])) {
        $method_id_to_delete = (int)$_POST['method_id'];
        $sql_delete = "DELETE FROM user_payment_methods WHERE id = ? AND user_id = ?";
        if ($stmt = $mysqli->prepare($sql_delete)) {
            $stmt->bind_param("ii", $method_id_to_delete, $user_id);
            if ($stmt->execute()) {
                $feedback_message = '<div class="bg-green-500/20 text-green-400 p-4 rounded-lg mb-6">Payment method removed.</div>';
            }
            $stmt->close();
        }
    }
    // Handle ADD request
    elseif (isset($_POST['action']) && $_POST['action'] == 'add') {
        $card_holder_name = trim($_POST['card_holder_name']);
        $card_number = trim($_POST['card_number']);
        $expiry_date = trim($_POST['expiry_date']);

        if (!empty($card_holder_name) && !empty($card_number) && !empty($expiry_date)) {
            $masked_number = '**** **** **** ' . substr($card_number, -4);
            
            $sql_insert = "INSERT INTO user_payment_methods (user_id, card_number, card_holder_name, expiry_date) VALUES (?, ?, ?, ?)";
            if ($stmt = $mysqli->prepare($sql_insert)) {
                $stmt->bind_param("isss", $user_id, $masked_number, $card_holder_name, $expiry_date);
                if ($stmt->execute()) {
                    $feedback_message = '<div class="bg-green-500/20 text-green-400 p-4 rounded-lg mb-6">Payment method added.</div>';
                }
                $stmt->close();
            }
        }
    }
}

// Fetch all payment methods for the current user
$sql_fetch = "SELECT id, card_number, card_holder_name, expiry_date FROM user_payment_methods WHERE user_id = ?";
if ($stmt = $mysqli->prepare($sql_fetch)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $payment_methods[] = $row;
    }
    $stmt->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-white mb-10 animate-on-scroll">Manage Payment Methods</h1>
    <?php echo $feedback_message; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-2xl font-bold text-white animate-on-scroll">Your Saved Cards</h2>
            <?php if (!empty($payment_methods)): ?>
                <?php foreach ($payment_methods as $method): ?>
                    <div class="card p-6 flex justify-between items-center animate-on-scroll">
                        <div class="flex items-center space-x-4">
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                            <div>
                                <p class="font-bold text-lg text-white"><?php echo htmlspecialchars($method['card_number']); ?></p>
                                <p class="text-gray-400">Expires: <?php echo htmlspecialchars($method['expiry_date']); ?></p>
                            </div>
                        </div>
                        <div>
                            <form action="manage-payments.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="method_id" value="<?php echo $method['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-400 font-semibold text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card p-8 text-center animate-on-scroll">
                    <p class="text-gray-400">You have no saved payment methods.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-1">
            <div class="card p-6 animate-on-scroll">
                <h2 class="text-2xl font-bold text-white mb-6">Add a New Card</h2>
                <form action="manage-payments.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    <div>
                        <label for="card_holder_name" class="block text-sm font-medium text-gray-300 mb-1">Cardholder Name</label>
                        <input type="text" name="card_holder_name" class="form-input" required>
                    </div>
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-300 mb-1">Card Number</label>
                        <input type="text" name="card_number" placeholder="xxxx xxxx xxxx xxxx" class="form-input" required>
                    </div>
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-300 mb-1">Expiry Date (MM/YYYY)</label>
                        <input type="text" name="expiry_date" placeholder="MM/YYYY" class="form-input" required>
                    </div>
                    <button type="submit" class="w-full btn btn-primary mt-4">Save Payment Method</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
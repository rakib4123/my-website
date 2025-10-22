<?php
include 'header.php';

// Redirect if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$addresses = [];
$feedback_message = '';

// --- Handle Add/Delete Address ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Handle DELETE request ---
    if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['address_id'])) {
        $address_id_to_delete = (int)$_POST['address_id'];
        $sql_delete = "DELETE FROM user_addresses WHERE id = ? AND user_id = ?";
        if ($stmt = $mysqli->prepare($sql_delete)) {
            $stmt->bind_param("ii", $address_id_to_delete, $user_id);
            if ($stmt->execute()) {
                $feedback_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">Address removed successfully.</div>';
            }
            $stmt->close();
        }
    }
    // --- Handle ADD request ---
    elseif (isset($_POST['action']) && $_POST['action'] == 'add') {
        // Sanitize and prepare all the new, separate fields
        $full_name = trim($_POST['full_name']);
        $address_line1 = trim($_POST['address_line1']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $zip_code = trim($_POST['zip_code']);
        $country = trim($_POST['country']);
        $phone_number = trim($_POST['phone_number']);

        $sql_insert = "INSERT INTO user_addresses (user_id, full_name, address_line1, city, state, zip_code, country, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql_insert)) {
            $stmt->bind_param("isssssss", $user_id, $full_name, $address_line1, $city, $state, $zip_code, $country, $phone_number);
            if ($stmt->execute()) {
                $feedback_message = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">Address added successfully.</div>';
            }
            $stmt->close();
        }
    }
}

// Fetch all addresses for the current user to display
$sql_fetch = "SELECT * FROM user_addresses WHERE user_id = ?";
if ($stmt = $mysqli->prepare($sql_fetch)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }
    $stmt->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <h1 class="text-4xl font-extrabold text-center text-gray-900 mb-10 animate-on-scroll">Manage Addresses</h1>

    <?php echo $feedback_message; // Display success/error messages ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Display Existing Addresses -->
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-2xl font-bold text-gray-800 animate-on-scroll">Your Saved Addresses</h2>
            <?php if (!empty($addresses)): ?>
                <?php foreach ($addresses as $address): ?>
                    <div class="card p-6 flex justify-between items-start animate-on-scroll">
                        <div>
                            <p class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($address['full_name']); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($address['address_line1']); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['state']); ?> <?php echo htmlspecialchars($address['zip_code']); ?></p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($address['country']); ?></p>
                            <p class="text-gray-600 mt-2">Contact: <?php echo htmlspecialchars($address['phone_number']); ?></p>
                        </div>
                        <div>
                            <form action="manage-addresses.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card p-8 text-center animate-on-scroll">
                    <p class="text-gray-500">You have no saved addresses.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add New Address Form -->
        <div class="lg:col-span-1">
            <div class="card p-6 animate-on-scroll">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Add a New Address</h2>
                <form action="manage-addresses.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="full_name" class="mt-1 form-input" required>
                    </div>
                     <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                        <input type="tel" name="phone_number" class="mt-1 form-input" required>
                    </div>
                    <div>
                        <label for="address_line1" class="block text-sm font-medium text-gray-700">Street Address</label>
                        <input type="text" name="address_line1" class="mt-1 form-input" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" name="city" class="mt-1 form-input" required>
                        </div>
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">State / Division</label>
                            <input type="text" name="state" class="mt-1 form-input" required>
                        </div>
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700">ZIP / Postal Code</label>
                            <input type="text" name="zip_code" class="mt-1 form-input" required>
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <input type="text" name="country" class="mt-1 form-input" value="Bangladesh" required>
                        </div>
                    </div>
                    <button type="submit" class="w-full btn btn-primary mt-4">Save Address</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>


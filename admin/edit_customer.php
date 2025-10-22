<?php
$page_title = "Edit Customer";
include 'header.php';

$customer = null;
$customer_id = null;
$feedback_message = '';

// Check for a valid customer ID in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $customer_id = (int)$_GET['id'];
} else {
    header("location: customers.php");
    exit;
}

// Handle form submission to UPDATE the customer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Basic validation
    if (!empty($username) && !empty($email)) {
        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssi", $username, $email, $customer_id);
            if ($stmt->execute()) {
                $feedback_message = '<div class="bg-green-100 border-green-400 text-green-700 p-3 rounded-lg mb-6">Customer details updated successfully!</div>';
            } else {
                $feedback_message = '<div class="bg-red-100 border-red-400 text-red-700 p-3 rounded-lg mb-6">Error updating customer.</div>';
            }
            $stmt->close();
        }
    }
}

// Fetch current customer data to pre-fill the form
$sql_fetch = "SELECT id, username, email FROM users WHERE id = ?";
if ($stmt_fetch = $mysqli->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $customer_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($result->num_rows == 1) {
        $customer = $result->fetch_assoc();
    } else {
        header("location: customers.php");
        exit;
    }
    $stmt_fetch->close();
}
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800">Edit Customer</h2>
    <a href="customers.php" class="text-blue-600 hover:underline font-semibold">&larr; Back to Customer List</a>
</div>

<?php echo $feedback_message; ?>

<?php if ($customer): ?>
<div class="card p-8">
    <form action="edit_customer.php?id=<?php echo $customer_id; ?>" method="post" class="space-y-6">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" class="mt-1 form-input" value="<?php echo htmlspecialchars($customer['username']); ?>" required>
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" name="email" class="mt-1 form-input" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
        </div>
        <div class="flex space-x-4 pt-4 border-t">
            <button type="submit" class="btn btn-primary">Update Customer</button>
            <a href="customers.php" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
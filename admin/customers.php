<?php 
$page_title = "Manage Customers";
include 'header.php';

// Fetch all users
$customers = [];
$sql = "SELECT id, username, email, created_at FROM users ORDER BY created_at DESC";
$result = $mysqli->query($sql);
if ($result) {
    while($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800">Manage Customers</h2>
</div>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-600">Customer ID</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Username</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Email</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Date Registered</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(!empty($customers)): ?>
                    <?php foreach($customers as $customer): ?>
                        <tr>
                            <td class="p-4 font-semibold">#<?php echo $customer['id']; ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td class="p-4 text-gray-500"><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td class="p-4 text-gray-500"><?php echo date("M d, Y", strtotime($customer['created_at'])); ?></td>
                            <td class="p-4 flex items-center space-x-4">
                                <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="text-blue-600 hover:underline font-semibold">Edit</a>
                                <form action="delete_customer.php" method="POST" onsubmit="return confirm('WARNING: Deleting a customer will also delete all their associated orders. Are you sure?');">
                                    <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
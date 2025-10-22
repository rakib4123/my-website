<?php 
$page_title = "Manage Products";
include 'header.php';

$products = [];
$result = $mysqli->query("SELECT * FROM products ORDER BY id DESC");
if ($result) { while($row = $result->fetch_assoc()) { $products[] = $row; } }
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800">Manage Products</h2>
    <a href="add_product.php" class="btn btn-primary">Add New Product</a>
</div>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-4 text-left font-semibold">Image</th>
                    <th class="p-4 text-left font-semibold">Name</th>
                    <th class="p-4 text-left font-semibold">Price</th>
                    <th class="p-4 text-left font-semibold">Stock</th>
                    <th class="p-4 text-left font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(!empty($products)): ?>
                    <?php foreach($products as $product): ?>
                        <tr>
                            <td class="p-4"><img src="../<?php echo htmlspecialchars($product['thumbnail_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-16 h-16 object-cover rounded-md"></td>
                            <td class="p-4 font-semibold text-gray-800"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="p-4 text-gray-600">৳<?php echo number_format($product['price'], 2); ?></td>
                            <td class="p-4 text-gray-600"><?php echo $product['stock']; ?></td>
                            <td class="p-4 flex items-center space-x-4">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:underline font-semibold">Edit</a>
                                <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">No products have been added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php
// --- ALL PHP LOGIC AT THE TOP, BEFORE ANY HTML OUTPUT ---
$page_title = "Edit Product";
include 'header.php'; // Includes security check and DB config

$product = null;
$product_id = null;
$feedback_message = '';

// Check for a valid product ID in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];
} else {
    header("location: products.php"); // Redirect if no ID is provided
    exit;
}

// --- HANDLE FORM SUBMISSION TO UPDATE THE PRODUCT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];
    $current_image = $_POST['current_image'];
    $thumbnail_path = $current_image;

    // --- Handle New Image Upload (if one is provided) ---
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . '_' . basename($_FILES["new_image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file)) {
            $thumbnail_path = "uploads/" . $image_name;
            // Delete old image if it exists and is different from the new one
            if (!empty($current_image) && file_exists("../" . $current_image) && $current_image !== $thumbnail_path) {
                unlink("../" . $current_image);
            }
        }
    }

    // --- Database Update Logic ---
    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, stock = ?, thumbnail_image = ? WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssdsisi", $name, $description, $price, $category, $stock, $thumbnail_path, $product_id);
        if ($stmt->execute()) {
            $feedback_message = '<div class="bg-green-100 border-green-400 text-green-700 p-3 rounded-lg mb-6">Product updated successfully!</div>';
        } else {
            $feedback_message = '<div class="bg-red-100 border-red-400 text-red-700 p-3 rounded-lg mb-6">Error updating product.</div>';
        }
        $stmt->close();
    }
}

// --- Fetch current product data to pre-fill the form ---
$sql_fetch = "SELECT * FROM products WHERE id = ?";
if ($stmt_fetch = $mysqli->prepare($sql_fetch)) {
    $stmt_fetch->bind_param("i", $product_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        header("location: products.php"); // Redirect if product not found
        exit;
    }
    $stmt_fetch->close();
}
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800">Edit Product</h2>
    <a href="products.php" class="text-blue-600 hover:underline font-semibold">&larr; Back to Product List</a>
</div>

<?php echo $feedback_message; ?>

<?php if ($product): ?>
<div class="card p-8">
    <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" class="mt-1 form-input" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="5" class="mt-1 form-input"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Price (৳)</label>
                <input type="number" name="price" step="0.01" class="mt-1 form-input" value="<?php echo $product['price']; ?>" required>
            </div>
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <input type="text" name="category" class="mt-1 form-input" value="<?php echo htmlspecialchars($product['category']); ?>" required>
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                <input type="number" name="stock" class="mt-1 form-input" value="<?php echo $product['stock']; ?>" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Current Thumbnail Image</label>
            <img src="../<?php echo htmlspecialchars($product['thumbnail_image']); ?>" class="w-32 h-32 object-cover rounded-lg mb-4 border p-1">
            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['thumbnail_image']); ?>">
            
            <label for="new_image" class="block text-sm font-medium text-gray-700">Upload New Thumbnail (Optional)</label>
            <input type="file" name="new_image" class="mt-1 form-input !p-2">
        </div>
        <div class="flex space-x-4 pt-4 border-t">
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="products.php" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
<?php
include 'header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];
$product = null;

// Fetch main product details
$sql = "SELECT * FROM products WHERE id = ?";
if ($stmt = $mysqli->prepare($sql)) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
    } else {
        header("location: products.php");
        exit;
    }
    $stmt->close();
}
?>

<div class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
        <div class="animate-on-scroll">
            <div class="card p-4">
                <img src="<?php echo htmlspecialchars($product['thumbnail_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-auto rounded-lg object-contain aspect-square">
            </div>
        </div>

        <div class="animate-on-scroll">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-3xl text-gray-800 mb-6">৳<?php echo number_format($product['price'], 2); ?></p>
            <div class="text-gray-600 leading-relaxed mb-8">
                <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            </div>

            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="flex items-center space-x-4 mb-6">
                    <label for="quantity" class="font-semibold text-gray-700">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-input w-24 text-center">
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <button type="submit" name="add_to_cart" class="w-full btn btn-primary text-lg">Add to Cart</button>
                    <button type="submit" name="buy_now" class="w-full btn btn-secondary bg-gray-800 hover:bg-black text-white text-lg">Buy Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php 
include 'header.php';

$search_query = "";
$search_results = [];

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    
    // Prepare a statement to search name and description
    $sql = "SELECT id, name, price, thumbnail_image FROM products WHERE name LIKE ? OR description LIKE ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $param_query = "%" . $search_query . "%";
        $stmt->bind_param("ss", $param_query, $param_query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $search_results[] = $row;
            }
        }
        $stmt->close();
    }
}
?>

<div class="container mx-auto px-6 py-12">
    <div class="text-center animate-on-scroll">
        <h1 class="text-4xl font-extrabold text-white">Search Results</h1>
        <?php if (!empty($search_query)): ?>
            <p class="mt-4 text-lg text-gray-400">You searched for: "<strong><?php echo htmlspecialchars($search_query); ?></strong>"</p>
        <?php endif; ?>
    </div>

    <div class="mt-12">
        <?php if (!empty($search_results)): ?>
            <p class="mb-6 text-gray-300 animate-on-scroll">Found <?php echo count($search_results); ?> matching products.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($search_results as $product): ?>
                    <div class="card text-center animate-on-scroll">
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="block">
                            <img src="<?php echo htmlspecialchars($product['thumbnail_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-56 object-cover">
                            <div class="p-6">
                                <h3 class="font-semibold text-lg text-white"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="text-gray-400 mt-2">৳<?php echo number_format($product['price'], 2); ?></p>
                            </div>
                        </a>
                        <div class="p-4 border-t border-gray-700">
                            <form action="add_to_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full btn btn-primary py-2">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center card p-12 animate-on-scroll">
                <p class="text-gray-400 text-xl">No products found matching your search.</p>
                <a href="products.php" class="mt-6 inline-block btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
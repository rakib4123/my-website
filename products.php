<?php 
include 'header.php'; 

// --- UPDATED FILTER LOGIC ---
$category_filter = $_GET['category'] ?? null;
$min_price = $_GET['min_price'] ?? null;
$max_price = $_GET['max_price'] ?? null;

$sql = "SELECT id, name, price, thumbnail_image FROM products";
$conditions = [];
$params = [];
$types = '';

if ($category_filter) { $conditions[] = "category = ?"; $params[] = $category_filter; $types .= 's'; }
if (is_numeric($min_price)) { $conditions[] = "price >= ?"; $params[] = $min_price; $types .= 'd'; }
if (is_numeric($max_price)) { $conditions[] = "price <= ?"; $params[] = $max_price; $types .= 'd'; }

if (!empty($conditions)) { $sql .= " WHERE " . implode(" AND ", $conditions); }
$sql .= " ORDER BY name ASC";

$stmt = $mysqli->prepare($sql);

// This is the more compatible way to bind a dynamic number of parameters
if (!empty($params)) {
    $bind_names = [$types];
    foreach ($params as $key => $value) {
        $bind_names[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}

$stmt->execute();
$result = $stmt->get_result();
// --- END OF UPDATED LOGIC ---

$categories_result = $mysqli->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
?>

<div class="container mx-auto px-6 py-12">
    <div class="text-center mb-12 animate-on-scroll">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">
            <?php echo $category_filter ? htmlspecialchars($category_filter) : 'All Products'; ?>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="lg:col-span-1 animate-on-scroll">
            <div class="card p-6 sticky top-28">
                <h3 class="font-bold text-xl mb-4 border-b pb-2">Categories</h3>
                <ul class="space-y-2">
                    <li><a href="products.php" class="text-gray-600 hover:text-blue-600 <?php echo !$category_filter ? 'font-bold text-blue-600' : ''; ?>">All Products</a></li>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <li><a href="?category=<?php echo urlencode($category['category']); ?>" class="text-gray-600 hover:text-blue-600 <?php echo ($category_filter == $category['category']) ? 'font-bold text-blue-600' : ''; ?>"><?php echo htmlspecialchars($category['category']); ?></a></li>
                    <?php endwhile; ?>
                </ul>

                <h3 class="font-bold text-xl mt-8 mb-4 border-b pb-2">Filter by Price (৳)</h3>
                <form action="products.php" method="GET">
                    <?php if ($category_filter): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>"><?php endif; ?>
                    <div class="flex items-center space-x-2">
                        <input type="number" name="min_price" placeholder="Min" class="form-input w-full text-sm" value="<?php echo htmlspecialchars($min_price ?? ''); ?>">
                        <span class="text-gray-500">-</span>
                        <input type="number" name="max_price" placeholder="Max" class="form-input w-full text-sm" value="<?php echo htmlspecialchars($max_price ?? ''); ?>">
                    </div>
                    <button type="submit" class="w-full mt-4 btn btn-primary">Apply Filter</button>
                </form>
            </div>
        </aside>

        <main class="lg:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                <?php
                if ($result && $result->num_rows > 0) {
                    while($product = $result->fetch_assoc()) {
                        echo '<div class="card flex flex-col justify-between animate-on-scroll">';
                        echo '  <div><a href="product-detail.php?id=' . $product['id'] . '" class="block p-4"><img src="' . htmlspecialchars($product['thumbnail_image']) . '" alt="' . htmlspecialchars($product['name']) . '" class="w-full h-48 object-contain"></a><div class="p-4 text-center"><h3 class="font-semibold text-lg text-gray-800 h-14">' . htmlspecialchars($product['name']) . '</h3><p class="text-gray-500 mt-2">৳' . number_format($product['price'], 2) . '</p></div></div>';
                        echo '  <div class="p-4 border-t border-gray-200"><form action="add_to_cart.php" method="POST" class="space-y-2"><input type="hidden" name="product_id" value="' . $product['id'] . '"><input type="hidden" name="quantity" value="1"><button type="submit" name="add_to_cart" class="w-full btn btn-primary py-2 text-sm">Add to Cart</button><button type="submit" name="buy_now" class="w-full btn bg-gray-700 hover:bg-gray-800 text-white py-2 text-sm">Buy Now</button></form></div>';
                        echo '</div>';
                    }
                } else {
                    echo "<div class='sm:col-span-2 xl:col-span-3 card p-12 text-center'><p class='text-gray-500'>No products found matching your criteria.</p></div>";
                }
                $stmt->close();
                ?>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>
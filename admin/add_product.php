<?php 
$page_title = "Add Product";
include 'header.php';
$feedback_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];
    $thumbnail_path = '';
    $gallery_files = [];

    // --- Main Thumbnail Upload ---
    if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $image_name = time() . '_thumb_' . basename($_FILES["thumbnail_image"]["name"]);
        $target_file = $target_dir . $image_name;
        if (move_uploaded_file($_FILES["thumbnail_image"]["tmp_name"], $target_file)) {
            $thumbnail_path = "uploads/" . $image_name;
        }
    }

    // --- Gallery Image & Video Upload ---
    if (isset($_FILES['gallery_media']) && !empty(array_filter($_FILES['gallery_media']['name']))) {
        $target_dir = "../uploads/";
        foreach ($_FILES['gallery_media']['name'] as $key => $name_val) {
            if ($_FILES['gallery_media']['error'][$key] == 0) {
                $file_name = time() . '_' . basename($_FILES["gallery_media"]["name"][$key]);
                $target_file = $target_dir . $file_name;
                
                // Determine if the file is an image or video
                $file_type = mime_content_type($_FILES["gallery_media"]["tmp_name"][$key]);
                $media_type = (strpos($file_type, 'video') !== false) ? 'video' : 'image';

                if (move_uploaded_file($_FILES["gallery_media"]["tmp_name"][$key], $target_file)) {
                    $gallery_files[] = ['type' => $media_type, 'path' => "uploads/" . $file_name];
                }
            }
        }
    }

    if (empty($thumbnail_path)) {
        $feedback_message = '<div class="bg-red-100 p-3 rounded-lg text-red-700">Thumbnail image is required.</div>';
    } else {
        // Insert main product details
        $sql = "INSERT INTO products (name, description, price, category, stock, thumbnail_image) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssdsis", $name, $description, $price, $category, $stock, $thumbnail_path);
            if ($stmt->execute()) {
                $product_id = $stmt->insert_id;
                
                // Now, insert gallery media files
                if (!empty($gallery_files)) {
                    $sql_gallery = "INSERT INTO product_media (product_id, media_type, media_path) VALUES (?, ?, ?)";
                    if ($stmt_gallery = $mysqli->prepare($sql_gallery)) {
                        foreach ($gallery_files as $file) {
                            $stmt_gallery->bind_param("iss", $product_id, $file['type'], $file['path']);
                            $stmt_gallery->execute();
                        }
                        $stmt_gallery->close();
                    }
                }
                header("location: products.php");
                exit;
            }
            $stmt->close();
        }
    }
}
?>

<h2 class="text-4xl font-extrabold text-gray-800 mb-8">Add New Product</h2>
<?php echo $feedback_message; ?>

<div class="card p-8">
    <form action="add_product.php" method="post" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" class="mt-1 form-input" required>
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 form-input"></textarea>
        </div>
        <div>
            <label for="thumbnail_image" class="block text-sm font-medium text-gray-700">Main Thumbnail Image</label>
            <input type="file" name="thumbnail_image" class="mt-1 form-input !p-2" required accept="image/*">
        </div>
        <div>
            <label for="gallery_media" class="block text-sm font-medium text-gray-700">Gallery Photos & Videos (select multiple)</label>
            <input type="file" name="gallery_media[]" class="mt-1 form-input !p-2" multiple accept="image/*,video/mp4">
        </div>

        <div class="flex space-x-4 pt-4 border-t">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="products.php" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
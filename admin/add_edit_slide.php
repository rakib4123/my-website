<?php
// --- ALL PHP LOGIC IS NOW AT THE TOP, BEFORE ANY HTML ---

// Start session and check for admin login
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database configuration
require_once "../config.php";

// Initialize variables
$slide = ['type' => 'image', 'heading' => '', 'subheading' => '', 'button_text' => 'Shop Now', 'button_link' => 'products.php', 'is_active' => 1, 'sort_order' => 0, 'media_path' => ''];
$edit_mode = false;
$feedback_message = '';
$page_title = "Add New Slide";

// Check if we are in edit mode (ID is in the URL)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $edit_mode = true;
    $slide_id = (int)$_GET['id'];
    $page_title = "Edit Slide";
    
    $result = $mysqli->query("SELECT * FROM hero_slider WHERE id = $slide_id");
    if($result && $result->num_rows == 1) {
        $slide = $result->fetch_assoc();
    } else {
        // Slide not found, redirect back
        header("location: manage_slider.php");
        exit;
    }
}

// --- HANDLE FORM SUBMISSION (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $type = $_POST['type'];
    $heading = trim($_POST['heading']);
    $subheading = trim($_POST['subheading']);
    $button_text = trim($_POST['button_text']);
    $button_link = trim($_POST['button_link']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    $media_path = $_POST['current_media_path'] ?? '';

    // Handle file upload
    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . '_' . basename($_FILES["media_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
            $media_path = "uploads/" . $file_name;
        }
    }

    if ($edit_mode) {
        // Update existing slide in the database
        $sql = "UPDATE hero_slider SET type=?, media_path=?, heading=?, subheading=?, button_text=?, button_link=?, is_active=?, sort_order=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssssssiii", $type, $media_path, $heading, $subheading, $button_text, $button_link, $is_active, $sort_order, $slide_id);
    } else {
        // Insert new slide into the database
        $sql = "INSERT INTO hero_slider (type, media_path, heading, subheading, button_text, button_link, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssssssii", $type, $media_path, $heading, $subheading, $button_text, $button_link, $is_active, $sort_order);
    }
    
    // Execute the query and redirect
    if ($stmt->execute()) {
        header("location: manage_slider.php"); // This redirect will now work correctly
        exit;
    } else {
        $feedback_message = '<div class="bg-red-100 border-red-400 text-red-700 p-3 rounded-lg">Error: ' . $stmt->error . '</div>';
    }
}

// --- START HTML OUTPUT ---
include 'header.php';
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800"><?php echo $page_title; ?></h2>
    <a href="manage_slider.php" class="text-blue-600 hover:underline font-semibold">&larr; Back to Slider List</a>
</div>

<?php echo $feedback_message; ?>

<div class="card p-8">
    <form action="add_edit_slide.php<?php if($edit_mode) echo '?id='.$slide_id; ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="current_media_path" value="<?php echo htmlspecialchars($slide['media_path']); ?>">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Slide Type</label>
            <select name="type" class="form-input">
                <option value="image" <?php if($slide['type'] == 'image') echo 'selected'; ?>>Image</option>
                <option value="video" <?php if($slide['type'] == 'video') echo 'selected'; ?>>Video</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Media File (Image or Video)</label>
            <?php if($edit_mode && !empty($slide['media_path'])) echo '<p class="text-xs text-gray-500 mb-2">Current file: '.htmlspecialchars($slide['media_path']).'. Uploading a new file will replace it.</p>'; ?>
            <input type="file" name="media_file" class="form-input !p-2">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Heading</label>
            <input type="text" name="heading" class="form-input" value="<?php echo htmlspecialchars($slide['heading']); ?>" required>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subheading</label>
            <textarea name="subheading" rows="3" class="form-input"><?php echo htmlspecialchars($slide['subheading']); ?></textarea>
        </div>
        
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                <input type="text" name="button_text" class="form-input" value="<?php echo htmlspecialchars($slide['button_text']); ?>" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Button Link</label>
                <input type="text" name="button_link" class="form-input" value="<?php echo htmlspecialchars($slide['button_link']); ?>" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-6">
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" class="form-input" value="<?php echo $slide['sort_order']; ?>">
            </div>
            <div>
                <label class="flex items-center space-x-2 mt-8">
                    <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded" <?php if($slide['is_active']) echo 'checked'; ?>>
                    <span class="text-gray-700">Is Active?</span>
                </label>
            </div>
        </div>

        <div class="flex space-x-4 border-t pt-6">
            <button type="submit" class="btn btn-primary">Save Slide</button>
            <a href="manage_slider.php" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
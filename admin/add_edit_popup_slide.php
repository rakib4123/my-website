<?php
$page_title = "Add/Edit Popup Slide";
include 'header.php';

// Initialize variables
$slide = ['heading' => '', 'subheading' => '', 'button_text' => 'Learn More', 'button_link' => '#', 'is_active' => 1, 'sort_order' => 0, 'media_path' => ''];
$edit_mode = false;
$feedback_message = '';

// Check if we are in "Edit" mode
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $edit_mode = true;
    $slide_id = (int)$_GET['id'];
    $page_title = "Edit Popup Slide";
    
    // Fetch existing slide data from the `popup_slides` table
    $result = $mysqli->query("SELECT * FROM popup_slides WHERE id = $slide_id");
    if($result && $result->num_rows == 1) {
        $slide = $result->fetch_assoc();
    } else {
        header("location: manage_popup.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $heading = trim($_POST['heading']);
    $subheading = trim($_POST['subheading']);
    $button_text = trim($_POST['button_text']);
    $button_link = trim($_POST['button_link']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    $media_path = $_POST['current_media_path'] ?? '';

    if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . '_popup_' . basename($_FILES["media_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
            $media_path = "uploads/" . $file_name;
        }
    }

    if ($edit_mode) {
        // CORRECTED: Updates the `popup_slides` table
        $sql = "UPDATE popup_slides SET media_path=?, heading=?, subheading=?, button_text=?, button_link=?, is_active=?, sort_order=? WHERE id=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssiii", $media_path, $heading, $subheading, $button_text, $button_link, $is_active, $sort_order, $slide_id);
    } else {
        // CORRECTED: Inserts into the `popup_slides` table
        $sql = "INSERT INTO popup_slides (media_path, heading, subheading, button_text, button_link, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssii", $media_path, $heading, $subheading, $button_text, $button_link, $is_active, $sort_order);
    }
    
    if ($stmt->execute()) {
        header("location: manage_popup.php");
        exit;
    } else {
        $feedback_message = '<div class="bg-red-100 p-3 rounded-lg text-red-700">Error: ' . $stmt->error . '</div>';
    }
}
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800"><?php echo $page_title; ?></h2>
    <a href="manage_popup.php" class="text-blue-600 hover:underline font-semibold">&larr; Back to Popup List</a>
</div>

<?php echo $feedback_message; ?>

<div class="card p-8">
    <form action="add_edit_popup_slide.php<?php if($edit_mode) echo '?id='.$slide_id; ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="current_media_path" value="<?php echo htmlspecialchars($slide['media_path']); ?>">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Media File (Image)</label>
            <?php if($edit_mode && !empty($slide['media_path'])) echo '<p class="text-xs text-gray-500 mb-2">Current file: '.htmlspecialchars($slide['media_path']).'</p>'; ?>
            <input type="file" name="media_file" class="form-input !p-2" accept="image/*" <?php if(!$edit_mode) echo 'required'; ?>>
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
            <a href="manage_popup.php" class="btn bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
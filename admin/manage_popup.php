<?php 
$page_title = "Manage Popup Slider";
include 'header.php';

// Handle slide deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_slide_id'])) {
    $slide_id = (int)$_POST['delete_slide_id'];
    $sql_delete = "DELETE FROM popup_slides WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql_delete)) {
        $stmt->bind_param("i", $slide_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="bg-green-100 p-3 rounded-lg text-green-700 mb-6">Slide deleted successfully.</div>';
    }
}

// Fetch all slides
$slides = [];
$result = $mysqli->query("SELECT * FROM popup_slides ORDER BY sort_order ASC");
if ($result) {
    while($row = $result->fetch_assoc()) {
        $slides[] = $row;
    }
}
?>

<div class="flex justify-between items-center mb-8">
    <h2 class="text-4xl font-extrabold text-gray-800">Manage Popup Slider</h2>
    <a href="add_edit_popup_slide.php" class="btn btn-primary">Add New Popup Slide</a>
</div>

<div class="card p-0">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-600">Order</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Media</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Heading</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Status</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(!empty($slides)): ?>
                    <?php foreach($slides as $slide): ?>
                        <tr>
                            <td class="p-4 font-semibold"><?php echo $slide['sort_order']; ?></td>
                            <td class="p-4"><img src="../<?php echo htmlspecialchars($slide['media_path']); ?>" class="w-24 h-16 object-cover rounded-md"></td>
                            <td class="p-4 font-semibold"><?php echo htmlspecialchars($slide['heading']); ?></td>
                            <td class="p-4"><?php echo $slide['is_active'] ? '<span class="text-green-600 font-semibold">Active</span>' : '<span class="text-gray-500">Inactive</span>'; ?></td>
                            <td class="p-4 flex items-center space-x-4">
                                <a href="add_edit_popup_slide.php?id=<?php echo $slide['id']; ?>" class="text-blue-600 hover:underline font-semibold">Edit</a>
                                <form action="manage_popup.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="delete_slide_id" value="<?php echo $slide['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">No popup slides have been added yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
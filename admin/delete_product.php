<?php
// Start the session and include the config file
require_once "../config.php";

// Security check: Ensure an admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the request is a POST and a product ID is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id_to_delete = (int)$_POST['product_id'];

    // --- Step 1: Delete the product's image file from the server ---
    
    // First, get the image path from the database
    $sql_get_image = "SELECT thumbnail_image FROM products WHERE id = ?";
    if($stmt_img = $mysqli->prepare($sql_get_image)){
        $stmt_img->bind_param("i", $product_id_to_delete);
        $stmt_img->execute();
        $stmt_img->bind_result($image_path_to_delete);
        
        // If a path is found, and the file exists, delete it
        if($stmt_img->fetch() && !empty($image_path_to_delete) && file_exists("../" . $image_path_to_delete)){
            unlink("../" . $image_path_to_delete);
        }
        $stmt_img->close();
    }
    
    // --- Step 2: Delete the product record from the database ---
    $sql_delete = "DELETE FROM products WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql_delete)) {
        $stmt->bind_param("i", $product_id_to_delete);
        $stmt->execute();
        $stmt->close();
    }
}

// Redirect back to the main products page
header("location: products.php");
exit;
?>
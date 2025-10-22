<?php
require_once "config.php";

// Security Check: User must be logged in to add items to the cart.
// If not logged in, redirect them to the login page.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check that the form was submitted with the required data
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        // Check if the product is already in the user's cart
        $sql_check = "SELECT id FROM cart WHERE user_id = ? AND product_id = ?";
        if ($stmt_check = $mysqli->prepare($sql_check)) {
            $stmt_check->bind_param("ii", $user_id, $product_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            
            if ($stmt_check->num_rows > 0) {
                // If it exists, update the quantity
                $sql_update = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
                if ($stmt_update = $mysqli->prepare($sql_update)) {
                    $stmt_update->bind_param("iii", $quantity, $user_id, $product_id);
                    $stmt_update->execute();
                }
            } else {
                // If it does not exist, insert a new record
                $sql_insert = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                if ($stmt_insert = $mysqli->prepare($sql_insert)) {
                    $stmt_insert->bind_param("iii", $user_id, $product_id, $quantity);
                    $stmt_insert->execute();
                }
            }
            $stmt_check->close();
        }
    }

    // --- NEW LOGIC: Check which button was clicked ---
    if (isset($_POST['buy_now'])) {
        // If "Buy Now" was clicked, redirect straight to checkout
        header("location: checkout.php");
    } else {
        // Otherwise (if "Add to Cart" was clicked), redirect to the cart page
        header("location: cart.php");
    }
    exit;

} else {
    // If the script was accessed improperly, just go to the shop page
    header("location: products.php");
    exit;
}
?>
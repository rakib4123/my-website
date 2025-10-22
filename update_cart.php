<?php
require_once "config.php";

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['id'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity <= 0) {
        // If quantity is 0 or less, remove the item
        $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Otherwise, update the quantity to the new value
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}
header("location: cart.php");
exit;
?>
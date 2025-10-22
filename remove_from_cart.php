<?php
require_once "config.php";
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) { header("location: login.php"); exit; }

if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['id'];
    $product_id = (int)$_POST['product_id'];
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }
}
header("location: cart.php");
exit;
?>
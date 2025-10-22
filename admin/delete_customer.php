<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}
require_once "../config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['customer_id'])) {
    $customer_id_to_delete = (int)$_POST['customer_id'];
    
    // Note: Due to "ON DELETE CASCADE" in the database setup, deleting a user
    // will also automatically delete their associated orders, cart items, addresses, etc.
    $sql_delete = "DELETE FROM users WHERE id = ?";
    if ($stmt = $mysqli->prepare($sql_delete)) {
        $stmt->bind_param("i", $customer_id_to_delete);
        $stmt->execute();
        $stmt->close();
    }
}
// Redirect back to the main customers page
header("location: customers.php");
exit;
?>
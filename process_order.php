<?php
require_once "config.php";

// Redirect if user is not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Check if the form was submitted correctly with the necessary data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['full_name'], $_POST['delivery_option'])) {
    
    // --- 1. GATHER AND SANITIZE DATA ---
    // Combine all address fields into a single, formatted string
    $shipping_address = 
        htmlspecialchars(trim($_POST['full_name'])) . "\n" .
        "Phone: " . htmlspecialchars(trim($_POST['phone'])) . "\n" .
        htmlspecialchars(trim($_POST['address_line1'])) . "\n" .
        htmlspecialchars(trim($_POST['city'])) . ", " . htmlspecialchars(trim($_POST['state']));
    
    $delivery_option = $_POST['delivery_option'];
    $payment_method = 'Cash on Delivery'; // Or get from form if you have more options

    // --- 2. CALCULATE DELIVERY CHARGE ---
    $delivery_charge = ($delivery_option === 'outside_dhaka') ? 130.00 : 70.00;

    // --- 3. START DATABASE TRANSACTION ---
    $mysqli->begin_transaction();

    try {
        // --- 4. FETCH CART ITEMS AND CALCULATE SUBTOTAL ---
        $cart_items = [];
        $subtotal = 0;
        $sql_cart = "SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
        if ($stmt_cart = $mysqli->prepare($sql_cart)) {
            $stmt_cart->bind_param("i", $user_id);
            $stmt_cart->execute();
            $result_cart = $stmt_cart->get_result();
            if ($result_cart->num_rows > 0) {
                while ($row = $result_cart->fetch_assoc()) {
                    $cart_items[] = $row;
                    $subtotal += $row['price'] * $row['quantity'];
                }
            } else { throw new Exception("Your cart is empty."); }
            $stmt_cart->close();
        }

        // --- 5. CALCULATE FINAL TOTAL PRICE ---
        $total_price = $subtotal + $delivery_charge;

        // --- 6. INSERT THE MAIN ORDER RECORD ---
        $sql_order = "INSERT INTO orders (user_id, total_price, delivery_charge, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
        $stmt_order = $mysqli->prepare($sql_order);
        $stmt_order->bind_param("iddss", $user_id, $total_price, $delivery_charge, $shipping_address, $payment_method);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        // --- 7. INSERT EACH ITEM FROM THE ORDER ---
        $sql_items = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_items = $mysqli->prepare($sql_items);
        foreach ($cart_items as $item) {
            $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_items->execute();
        }
        $stmt_items->close();
        
        // --- 8. CLEAR THE USER'S CART ---
        $sql_clear_cart = "DELETE FROM cart WHERE user_id = ?";
        $stmt_clear_cart = $mysqli->prepare($sql_clear_cart);
        $stmt_clear_cart->bind_param("i", $user_id);
        $stmt_clear_cart->execute();
        $stmt_clear_cart->close();

        // --- 9. COMMIT AND REDIRECT ---
        $mysqli->commit();
        header("location: order_confirmation.php?id=" . $order_id);
        exit();

    } catch (Exception $e) {
        $mysqli->rollback();
        die("Error processing order. Please try again. Details: " . $e->getMessage());
    }
} else {
    header("location: checkout.php");
    exit;
}
?>
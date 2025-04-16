<?php
session_start();
require 'db_connection.php'; // Make sure to use your connection settings

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $order_content = $_POST['order_content'];
    $total_price = $_POST['total_price'];
    $order_address = $_POST['order_address'];

    // Insert into user_order table
    $stmt = $conn->prepare("INSERT INTO user_order (user_id, user_name, menu_order_id, order_time) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("isi", $user_id, $user_name, $order_content); // Adjust types as needed

    if ($stmt->execute()) {
        // Clear from menu_orders
        $deleteStmt = $conn->prepare("DELETE FROM menu_orders WHERE id = ?");
        $deleteStmt->bind_param("i", $order_content); // Assuming order_content is the menu_order_id
        $deleteStmt->execute();

        echo "<script>alert('Order Confirmed! Thank you, $user_name'); window.location.href='homepage.php';</script>";
    } else {
        echo "Order failed: " . $conn->error;
    }
}
?>

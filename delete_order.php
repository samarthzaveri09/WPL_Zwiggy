<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = $_SESSION['order_content']; // Assuming this is menu_order_id

    $stmt = $conn->prepare("DELETE FROM menu_orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        unset($_SESSION['order_content'], $_SESSION['order_price'], $_SESSION['order_address']);
        echo "<script>alert('Order deleted.'); window.location.href='homepage.php';</script>";
    } else {
        echo "Error deleting order: " . $conn->error;
    }
}
?>

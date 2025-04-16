<?php
ni_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

$order_content = $_SESSION['order_content'];
$order_price = $_SESSION['order_price'];
$order_address = $_SESSION['order_address'];

$platform_fee = 5 + $order_price;
$gst = 0.18 * $order_price;
$total = $order_price + $platform_fee + $gst;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <style>
        body {
            background-color: #f5f0fa;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .cart-container {
            width: 60%;
            margin: 50px auto;
            background: #e6d9f5;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 15px rgba(138, 43, 226, 0.3);
        }
        h2 {
            text-align: center;
            color: #4b0082;
        }
        .cart-details {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #6a0dad;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #5a009d;
        }
        .total {
            font-weight: bold;
            margin-top: 20px;
        }
        .delete-btn {
            background-color: crimson;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2>Hello, <?php echo $user_name; ?>! Here's your Cart</h2>
    <div class="cart-details">
        <p><strong>Order Content:</strong> <?php echo $order_content; ?></p>
        <p><strong>Order Price:</strong> ₹<?php echo number_format($order_price, 2); ?></p>
        <p><strong>Platform Fee (5%):</strong> ₹<?php echo number_format($platform_fee, 2); ?></p>
        <p><strong>GST (18%):</strong> ₹<?php echo number_format($gst, 2); ?></p>
        <p class="total"><strong>Total Amount:</strong> ₹<?php echo number_format($total, 2); ?></p>
        <p><strong>Delivery Address:</strong> <?php echo $order_address; ?></p>
    </div>

    <form method="post" action="confirm_order.php">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="user_name" value="<?php echo $user_name; ?>">
        <input type="hidden" name="order_content" value="<?php echo $order_content; ?>">
        <input type="hidden" name="total_price" value="<?php echo $total; ?>">
        <input type="hidden" name="order_address" value="<?php echo $order_address; ?>">
        <button class="btn" type="submit" name="confirm_order">Complete Order</button>
    </form>

    <form method="post" action="delete_order.php">
        <button class="btn delete-btn" type="submit" name="delete_order">Delete Order</button>
    </form>
</div>

</body>
</html>

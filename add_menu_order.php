<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$rest_id = $_POST['rest_id'];
$rest_name = $_POST['rest_name'];
$order_contents = $_POST['order_contents'];
$order_price = $_POST['order_price'];
$order_location = $_POST['order_location'];

// Insert into the menu_orders table
$sql = "INSERT INTO menu_orders (rest_id, rest_name, order_contents, order_price, order_location, is_available) 
        VALUES ('$rest_id', '$rest_name', '$order_contents', '$order_price', '$order_location', TRUE)";

if ($conn->query($sql) === TRUE) {
    echo "New order posted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

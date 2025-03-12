<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

// Create table if not exists
$tableQuery = "CREATE TABLE IF NOT EXISTS foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    cuisine VARCHAR(50) NOT NULL
)";
$conn->query($tableQuery);

// Insert data into table
if (isset($_POST['submit'])) {
    $food_name = $_POST['food_name'];
    $price = $_POST['price'];
    $cuisine = $_POST['cuisine'];

    $insertQuery = "INSERT INTO foods (food_name, price, cuisine) VALUES ('$food_name', '$price', '$cuisine')";

    if ($conn->query($insertQuery) === TRUE) {
        echo "<p>Food item added successfully!</p>";
    } else {
        echo "Error: " . $insertQuery . "<br>" . $conn->error;
    }
}

// Update functionality
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $food_name = $_POST['food_name'];
    $price = $_POST['price'];
    $cuisine = $_POST['cuisine'];

    $updateQuery = "UPDATE foods SET food_name='$food_name', price='$price', cuisine='$cuisine' WHERE id='$id'";
    if ($conn->query($updateQuery) === TRUE) {
        echo "<p>Food item updated successfully!</p>";
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Delete functionality
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $deleteQuery = "DELETE FROM foods WHERE id='$id'";

    if ($conn->query($deleteQuery) === TRUE) {
        echo "<p>Food item deleted successfully!</p>";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Display all food items
$result = $conn->query("SELECT * FROM foods");
if ($result->num_rows > 0) {
    echo "<h3>Food Menu</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Food Name</th><th>Price</th><th>Cuisine</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['food_name']}</td>
                <td>{$row['price']}</td>
                <td>{$row['cuisine']}</td>
                <td>
                    <form method='POST' action='zwiggy.php' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='text' name='food_name' value='{$row['food_name']}' required>
                        <input type='number' name='price' value='{$row['price']}' step='0.01' required>
                        <input type='text' name='cuisine' value='{$row['cuisine']}' required>
                        <input type='submit' name='update' value='Update'>
                    </form>
                    <form method='POST' action='zwiggy.php' style='display:inline;'>
                        <input type='hidden' name='id' value='{$row['id']}'>
                        <input type='submit' name='delete' value='Delete'>
                    </form>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No food items found.</p>";
}

$conn->close();
?>

HTML

<!DOCTYPE html>
<html>
<head>
    <title>Zwiggy Food App</title>
</head>
<body>
    <h2>Food Entry Form - Zwiggy</h2>
    <form method="POST" action="zwiggy.php">
        Food Name: <input type="text" name="food_name" required><br>
        Price: <input type="number" name="price" step="0.01" required><br>
        Cuisine: <input type="text" name="cuisine" required><br>
        <input type="submit" name="submit" value="Add Food">
    </form>
</body>
</html>

<?php
// Database credentials
$servername = "localhost";
$username = "root"; // MySQL username
$password = ""; // MySQL password
$dbname = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Database connection error
}

// Initialize error message variable
$deleteError = $deleteSuccess = "";

// Check if the delete form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the restaurant name to delete
    $restaurant_name_to_delete = $_POST['restaurant_name_to_delete'];

    // Validate the name input
    if (empty($restaurant_name_to_delete)) {
        $deleteError = "Restaurant name is required to delete.";
    } else {
        // Prepare and bind the SQL statement to delete the restaurant by name
        $stmt = $conn->prepare("DELETE FROM restaurant_info WHERE name = ?");
        $stmt->bind_param("s", $restaurant_name_to_delete);

        // Execute the statement and check if it was successful
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $deleteSuccess = "Restaurant deleted successfully.";
            } else {
                $deleteError = "Restaurant not found.";
            }
        } else {
            $deleteError = "Error: Could not delete restaurant.";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Restaurant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #d32f2f;
        }
        .error {
            color: red;
            font-size: 12px;
        }
        .success {
            color: green;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Delete Restaurant</h2>
    <form method="POST" action="">
        <label for="restaurant_name_to_delete">Restaurant Name to Delete:</label>
        <input type="text" id="restaurant_name_to_delete" name="restaurant_name_to_delete" required>
        <span class="error"><?php echo $deleteError; ?></span>
        <span class="success"><?php echo $deleteSuccess; ?></span>
        <button type="submit" name="submit_delete">Delete Restaurant</button>
    </form>
</div>

</body>
</html>
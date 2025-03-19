<?php
$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Database connection error
}

// Initialize error message variables
$nameError = $cuisineError = $ratingError = $locationError = $contactError = $timeError = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $name = $_POST['name'];
    $cuisine = $_POST['cuisine'];
    $rating = $_POST['rating'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'];

    // Error handling: Validate input fields
    $valid = true; // Assume the form is valid initially

    // Check for empty fields and store error messages
    if (empty($name)) {
        $nameError = "Restaurant Name is required.";
        $valid = false;
    }
    if (empty($cuisine)) {
        $cuisineError = "Cuisine type is required.";
        $valid = false;
    }
    if (empty($rating) || $rating < 0 || $rating > 5) {
        $ratingError = "Rating must be between 0 and 5.";
        $valid = false;
    }
    if (empty($location)) {
        $locationError = "Location is required.";
        $valid = false;
    }
    if (empty($contact) || !preg_match("/^\d{10}$/", $contact)) {
        $contactError = "Contact must be a 10-digit number.";
        $valid = false;
    }
    if (empty($opening_time) || empty($closing_time)) {
        $timeError = "Opening and Closing times are required.";
        $valid = false;
    }

    // If no errors, proceed with inserting data into the database
    if ($valid) {
        // Prepare and bind the SQL statement to insert data
        $stmt = $conn->prepare("INSERT INTO restaurant_info (name, cuisine, rating, location, contact, opening_time, closing_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsdss", $name, $cuisine, $rating, $location, $contact, $opening_time, $closing_time);

        // Execute the statement and check if it was successful
        if ($stmt->execute()) {
            echo "<script>alert('Restaurant information added successfully.');</script>";
        } else {
            echo "<script>alert('Error: Could not add restaurant information.');</script>";
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
    <title>Add Restaurant</title>
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
        input[type="text"], input[type="number"], input[type="time"], textarea {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Restaurant Information</h2>
    <form method="POST" action="">
        <label for="name">Restaurant Name:</label>
        <input type="text" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
        <span class="error"><?php echo $nameError; ?></span>

        <label for="cuisine">Cuisine:</label>
        <input type="text" id="cuisine" name="cuisine" value="<?php echo isset($cuisine) ? $cuisine : ''; ?>" required>
        <span class="error"><?php echo $cuisineError; ?></span>

        <label for="rating">Rating:</label>
        <input type="number" id="rating" name="rating" value="<?php echo isset($rating) ? $rating : ''; ?>" min="0" max="5" step="0.1" required>
        <span class="error"><?php echo $ratingError; ?></span>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo isset($location) ? $location : ''; ?>" required>
        <span class="error"><?php echo $locationError; ?></span>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?php echo isset($contact) ? $contact : ''; ?>" required>
        <span class="error"><?php echo $contactError; ?></span>

        <label for="opening_time">Opening Time:</label>
        <input type="time" id="opening_time" name="opening_time" value="<?php echo isset($opening_time) ? $opening_time : ''; ?>" required>
        
        <label for="closing_time">Closing Time:</label>
        <input type="time" id="closing_time" name="closing_time" value="<?php echo isset($closing_time) ? $closing_time : ''; ?>" required>
        <span class="error"><?php echo $timeError; ?></span>

        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for restaurant registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rest_name = mysqli_real_escape_string($conn, $_POST['rest_name']);
    $rest_cuisine = mysqli_real_escape_string($conn, $_POST['rest_cuisine']);
    $rest_address = mysqli_real_escape_string($conn, $_POST['rest_address']);
    $rest_contact = $_POST['rest_contact']; // Get raw contact number
    $password = mysqli_real_escape_string($conn, $_POST['password']); // From form field
    
    // Validate contact number for int(10) field
    if (!is_numeric($rest_contact)) {
        echo "Error: Contact number must contain only digits.";
        echo "<br><a href='restaurant_add_form.html'>Go back</a>";
        exit();
    }
    
    // Convert to integer and check range
    $rest_contact_int = (int)$rest_contact;
    if ($rest_contact_int <= 0 || $rest_contact_int > 9999999999) { // Max value for int(10)
        echo "Error: Contact number is out of valid range.";
        echo "<br><a href='restaurant_add_form.html'>Go back</a>";
        exit();
    }
    
    // Use rest_password as the column name
    $sql = "INSERT INTO restaurants (rest_name, rest_cuisine, rest_address, rest_contact, rest_password)
            VALUES ('$rest_name', '$rest_cuisine', '$rest_address', $rest_contact_int, '$password')";

    if (mysqli_query($conn, $sql)) {
        // Start session and set restaurant data
        session_start();
        $_SESSION['rest_id'] = mysqli_insert_id($conn);
        $_SESSION['rest_name'] = $rest_name;
        $_SESSION['rest_contact'] = $rest_contact_int;
        
        // Redirect to restaurant landing page after successful registration
        header("Location: restaurant_landing.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
        echo "<br><a href='restaurant_add_form.html'>Go back</a>";
    }
}
?>
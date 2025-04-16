<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Direct DB connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted values
    $rest_contact = $_POST['rest_contact'];
    $password = $_POST['password']; // From form field

    // Validate contact is numeric
    if (!is_numeric($rest_contact)) {
        echo "<script>alert('Contact number must contain only digits.');</script>";
        echo "<script>window.location.href='restaurant_add_form.html';</script>";
        exit();
    }
    
    // Convert to integer
    $rest_contact = (int)$rest_contact;

    // Query to get restaurant details
    $sql = "SELECT * FROM restaurants WHERE rest_contact = $rest_contact";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if password matches (using the correct column name)
        if ($password == $row['rest_password']) {
            // Set all necessary session variables
            $_SESSION['rest_id'] = $row['rest_id'];
            $_SESSION['rest_name'] = $row['rest_name'];
            $_SESSION['rest_contact'] = $row['rest_contact'];
            
            // Redirect to restaurant landing page
            header("Location: restaurant_landing.php");
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
            echo "<script>window.location.href='restaurant_add_form.html';</script>";
        }
    } else {
        echo "<script>alert('Restaurant not found. Please check your contact number.');</script>";
        echo "<script>window.location.href='restaurant_add_form.html';</script>";
    }
}
?>
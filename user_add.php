<?php
// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// DB Config
$host = "localhost";
$db = "zwiggy_db";
$user = "root";
$pass = "";

// Connect
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session for storing username
session_start();

// On form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $name = $_POST["user_name"];
    $email = $_POST["user_email"];
    $password = $_POST["user_password"];
    $contact = $_POST["user_contact"];
    $address = $_POST["user_address"];

    // Prepare & insert into the database
    $sql = "INSERT INTO users (user_name, user_email, user_password, user_contact, user_address) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $name, $email, $password, $contact, $address);

        if ($stmt->execute()) {
            // Store the username in a session variable after successful insert
            $_SESSION['username'] = $name;

            // Redirect to the homepage (you can change the destination to homepage.php if needed)
            header("Location: homepage.php");
            exit();
        } else {
            echo "Execution error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Query error: " . $conn->error;
    }
}

$conn->close();
?>

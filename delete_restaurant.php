<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if rest_id is set and numeric
    if (isset($_POST['rest_id']) && is_numeric($_POST['rest_id'])) {
        $rest_id = $_POST['rest_id'];

        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM restaurants WHERE id = ?");
        $stmt->bind_param("i", $rest_id);

        if ($stmt->execute()) {
            echo "Restaurant deleted successfully.";
        } else {
            echo "Error deleting restaurant: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid or missing restaurant ID.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>

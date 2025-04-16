<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for storing restaurant details
session_start();

// Check if user is logged in
if (!isset($_SESSION['rest_id']) || !isset($_SESSION['rest_name'])) {
    // Redirect to login page if not logged in
    header("Location: restaurant_add_form.html");
    exit();
}

// Get restaurant details from session
$rest_id = $_SESSION['rest_id'];
$rest_name = $_SESSION['rest_name'];

// DB connection for additional operations if needed
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Restaurant Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #orderForm {
      display: none;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Restaurant: <?php echo htmlspecialchars($rest_name); ?></a>
    <div class="ml-auto">
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container my-4">
  <!-- Post New Order Button -->
  <button class="btn btn-primary mb-3" onclick="toggleOrderForm()">Post New Order</button>

  <!-- Order Form -->
  <form id="orderForm" action="add_menu_order.php" method="POST" class="border p-3 mb-3">
    <input type="hidden" name="rest_id" value="<?php echo $rest_id; ?>">
    <input type="hidden" name="rest_name" value="<?php echo $rest_name; ?>">

    <div class="mb-3">
      <label for="order_contents" class="form-label">Order Contents</label>
      <textarea name="order_contents" id="order_contents" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
      <label for="order_price" class="form-label">Order Price (₹)</label>
      <input type="number" name="order_price" class="form-control" required step="0.01">
    </div>

    <div class="mb-3">
      <label for="order_location" class="form-label">Order Location</label>
      <input type="text" name="order_location" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success">Submit Order</button>
  </form>

  <!-- Update / Delete Restaurant -->
  <div class="d-flex gap-3">
    <form action="update_restaurant.php" method="POST">
      <input type="hidden" name="rest_id" value="<?php echo $rest_id; ?>">
      <button type="submit" class="btn btn-warning">Update Restaurant</button>
    </form>

    <form action="delete_restaurant.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this restaurant?');">
      <input type="hidden" name="rest_id" value="<?php echo $rest_id; ?>">
      <button type="submit" class="btn btn-danger">Delete Restaurant</button>
    </form>
  </div>
</div>

<script>
  function toggleOrderForm() {
    const form = document.getElementById("orderForm");
    form.style.display = form.style.display === "none" ? "block" : "none";
  }
</script>

</body>
</html>
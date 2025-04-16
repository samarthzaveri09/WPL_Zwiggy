<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch restaurants
$sql_restaurants = "SELECT rest_name, rest_cuisine FROM restaurants";
$result_restaurants = $conn->query($sql_restaurants);

// Query to fetch available dishes
$sql_dishes = "SELECT m.order_contents, m.order_price, r.rest_name 
               FROM menu_orders m
               JOIN restaurants r ON m.rest_id = r.rest_id
               WHERE m.is_available = TRUE";
$result_dishes = $conn->query($sql_dishes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zwiggy Food Delivery</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <div class="top-banner-left">
            <a href="#" style="color: #4e1d9e; text-decoration: none; font-weight: bold;">ZWIGGY125FIRST</a>
        </div>
        <div class="top-banner-right">
            <div style="display: flex; align-items: center; gap: 5px;">
                <span>🏫</span>
                <span>K.J Somaiya School of Engineering</span>
            </div>
            <a href="#" style="color: #4e1d9e; text-decoration: none;">Change Location</a>
        </div>
    </div>

    <header class="header">
        <div class="logo">
            <img src="https://placehold.co/100x30?text=Zwiggy" alt="Zwiggy">
        </div>
        <nav class="nav-links">
            <a href="index.php" class="nav-link">Home</a>
            <a href="WPL/cart.php" class="nav-link">Cart</a>
            <!-- Check if the username session is set and display it -->
            <?php if (isset($_SESSION['username'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="logout.php" class="signup-btn">Logout</a>
            <?php else: ?>
                <a href="WPL/user_add_form.html" class="signup-btn">Sign Up/Sign In</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <p>Order Restaurant food, takeaways and groceries.</p>
        <h1>Feast Your Senses, Fast and Fresh</h1>
        <p>Enter a postcode to see what we deliver!</p>
        <div class="search-bar">
            <input type="text" placeholder="e.g. EC4R 3TE">
            <button>Search</button>
        </div>
    </section>

    <!-- Featured Image Section (Replacing Categories) -->
    <section class="featured-image">
        <h2>Featured Indian Dish</h2>
        <img src="cholle-bhature.jpg" alt="Indian food plate with puri and chickpea curry" 
             style="width: 150%; max-width: 600px; height: 50%; border-radius: 10px;" 
             onerror="this.src='https://placehold.co/600x400?text=Indian+Cuisine'">
    </section>

    <!-- Restaurants Section - Integrated -->
    <section class="restaurants">
        <h2>Popular Restaurants</h2>
        <div class="restaurant-cards">
            <?php
            if ($result_restaurants->num_rows > 0) {
                // Output data of each row
                while($row = $result_restaurants->fetch_assoc()) {
                    echo '<div class="restaurant-card">';
                    // Create placeholder with restaurant name
                    echo '<img src="https://placehold.co/150x80?text=' . urlencode($row['rest_name']) . '" alt="' . htmlspecialchars($row['rest_name']) . '">';
                    echo '<div class="restaurant-name">' . htmlspecialchars($row['rest_name']) . '</div>';
                    echo '<div class="restaurant-cuisine">' . htmlspecialchars($row['rest_cuisine']) . '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No restaurants found</p>";
            }
            ?>
        </div>
    </section>

    <!-- Available Dishes Section - Updated to pull from database -->
    <section class="available-dishes">
        <h2>Available Dishes</h2>
        <div class="dishes-container">
            <?php
            if ($result_dishes->num_rows > 0) {
                // Output data of each row
                while($dish = $result_dishes->fetch_assoc()) {
                    echo '<div class="dish-card">';
                    echo '<div class="dish-content">';
                    echo '<h3>' . htmlspecialchars($dish['order_contents']) . '</h3>';
                    // You could add a description column to your database if needed
                    echo '<p>Delicious dish available now.</p>';
                    echo '</div>';
                    echo '<div class="dish-location">Location: ' . htmlspecialchars($dish['rest_name']) . '</div>';
                    echo '<div class="dish-footer">';
                    echo '<span class="dish-price">₹' . htmlspecialchars($dish['order_price']) . '</span>';
                    echo '<button class="add-to-cart">Add to Cart</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No available dishes found</p>";
            }
            ?>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat-item">
            <span class="stat-number">546+</span>
            <span class="stat-label">Registered Sellers</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">789,900+</span>
            <span class="stat-label">Orders Delivered</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">690+</span>
            <span class="stat-label">Restaurants Partnered</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">17,457+</span>
            <span class="stat-label">Food Items</span>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="social-icons">
            <a href="#"><span>f</span></a>
            <a href="#"><span>t</span></a>
            <a href="#"><span>in</span></a>
            <a href="#"><span>ig</span></a>
            <a href="#"><span>sc</span></a>
        </div>

        <div class="footer-bottom">
            <span>Zwiggy Copyright 2024. All Rights Reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms</a>
                <a href="#">Pricing</a>
                <a href="#">Accessibility</a>
            </div>
        </div>
    </footer>

    <script>
        // JavaScript for interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchButton = document.querySelector('.search-bar button');
            const searchInput = document.querySelector('.search-bar input');
            
            searchButton.addEventListener('click', function() {
                alert('Searching for restaurants near: ' + searchInput.value);
            });
            
            // Restaurant selection
            const restaurantCards = document.querySelectorAll('.restaurant-card');
            restaurantCards.forEach(card => {
                card.addEventListener('click', function() {
                    const restaurant = this.querySelector('.restaurant-name').textContent;
                    alert('You selected restaurant: ' + restaurant);
                });
            });
            
            // Add to cart functionality
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const dishName = this.closest('.dish-card').querySelector('h3').textContent;
                    alert(dishName + ' added to cart!');
                });
            });
        });
    </script>

<?php
// Close connection
$conn->close();
?>
</body>
</html>
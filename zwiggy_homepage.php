<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully or already exists";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($dbname);

// Create restaurant_info table if not exists
$sql = "CREATE TABLE IF NOT EXISTS restaurant_info (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    cuisine VARCHAR(50) NOT NULL,
    rating DECIMAL(3,1) NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    opening_time TIME NOT NULL,
    closing_time TIME NOT NULL
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating table: " . $conn->error;
}

// Create foods table if not exists
$sql = "CREATE TABLE IF NOT EXISTS foods (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    food_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    cuisine VARCHAR(50) NOT NULL,
    restaurant_id INT(11),
    FOREIGN KEY (restaurant_id) REFERENCES restaurant_info(id)
)";

if ($conn->query($sql) !== TRUE) {
    echo "Error creating foods table: " . $conn->error;
}

// Fetch top restaurants
$topRestaurants = [];
$sql = "SELECT * FROM restaurant_info ORDER BY rating DESC LIMIT 6";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $topRestaurants[] = $row;
    }
}

// Close the connection
$conn->close();

// Session handling
session_start();
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zwiggy - Food Delivery App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Header Styles */
        header {
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .logo span {
            color: #6a1b9a;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
        }

        .nav-links a:hover {
            color: #6a1b9a;
        }

        .user-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn {
            background-color: transparent;
            color: #6a1b9a;
            border: 1px solid #6a1b9a !important;
        }

        .signup-btn {
            background-color: #6a1b9a;
            color: #fff;
        }

        /* Hero Section */
        .hero {
            background-color: #9c27b0;
            color: white;
            padding: 30px 0;
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .hero h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        .search-bar {
            width: 80%;
            max-width: 600px;
            display: flex;
            margin-top: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 5px 0 0 5px;
        }

        .search-bar button {
            padding: 12px 25px;
            background-color: #6a1b9a;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        /* Featured Categories */
        .featured-categories {
            padding: 30px 0;
            background-color: #fff;
        }

        .section-title {
            margin-bottom: 20px;
            color: #333;
            font-size: 20px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .category-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .category-card h3 {
            padding: 15px;
            text-align: center;
            font-size: 16px;
            color: #333;
        }

        /* Restaurants Section */
        .restaurants {
            padding: 30px 0;
        }

        .restaurant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .restaurant-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .restaurant-card:hover {
            transform: translateY(-5px);
        }

        .restaurant-card img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }

        .restaurant-card .info {
            padding: 10px;
        }

        .restaurant-card h3 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .restaurant-card p {
            font-size: 12px;
            color: #666;
        }

        .rating {
            color: #ff9800;
            font-size: 14px;
        }

        /* App Promotion */
        .app-promo {
            background-color: #6a1b9a;
            color: white;
            padding: 30px 0;
            margin: 30px 0;
        }

        .app-promo-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .app-promo-text {
            flex: 1;
        }

        .app-promo h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        .app-promo p {
            margin-bottom: 20px;
        }

        .app-buttons {
            display: flex;
            gap: 15px;
        }

        .app-buttons img {
            height: 40px;
            cursor: pointer;
        }

        .app-promo-image {
            flex: 1;
            text-align: right;
        }

        .app-promo-image img {
            max-width: 100%;
            height: auto;
        }

        /* Features Section */
        .features {
            padding: 30px 0;
            background-color: #fff;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .feature-card i {
            font-size: 30px;
            color: #6a1b9a;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
        }

        .feature-card p {
            color: #666;
            font-size: 14px;
        }

        /* Stats Section */
        .stats {
            padding: 30px 0;
            background-color: #f9f9f9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 24px;
            color: #6a1b9a;
            margin-bottom: 5px;
        }

        .stat-item p {
            color: #666;
            font-size: 14px;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: #fff;
            padding: 30px 0;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .footer-column h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #ddd;
            text-decoration: none;
        }

        .footer-column ul li a:hover {
            color: #fff;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-links a {
            color: #fff;
            text-decoration: none;
            font-size: 20px;
        }

        .footer-bottom {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
        }

        /* Login Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 500px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-submit {
            background-color: #6a1b9a;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        .switch-form {
            text-align: center;
            margin-top: 15px;
        }

        .switch-form a {
            color: #6a1b9a;
            text-decoration: none;
        }

        /* Admin Panel Link */
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #6a1b9a;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            z-index: 100;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .app-promo-content {
                flex-direction: column;
                text-align: center;
            }

            .app-promo-image {
                margin-top: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .app-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .search-bar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-content">
            <div class="logo">
                <span>Z</span>wiggy
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="zwiggy_search.php">Food Search</a></li>
                <li><a href="#">Offers</a></li>
                <li><a href="#">Help</a></li>
            </ul>
            <div class="user-actions">
                <?php if ($loggedIn): ?>
                    <span>Welcome, <?php echo htmlspecialchars($userName); ?></span>
                    <a href="logout.php" style="text-decoration: none; margin-left: 10px;">Logout</a>
                <?php else: ?>
                    <button class="login-btn" id="loginBtn">Login</button>
                    <button class="signup-btn" id="signupBtn">Sign Up</button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-content">
            <h1>Feast Your Senses, Fast and Fresh</h1>
            <div class="search-bar">
                <input type="text" placeholder="Enter your delivery location">
                <button id="searchBtn">Search</button>
            </div>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="featured-categories">
        <div class="container">
            <h2 class="section-title">Popular Categories</h2>
            <div class="category-grid">
                <div class="category-card">
                    <img src="https://via.placeholder.com/400x320?text=Pizza" alt="Pizza">
                    <h3>Pizza</h3>
                </div>
                <div class="category-card">
                    <img src="https://via.placeholder.com/400x320?text=Burgers" alt="Burgers">
                    <h3>Burgers</h3>
                </div>
                <div class="category-card">
                    <img src="https://via.placeholder.com/400x320?text=Chinese" alt="Chinese">
                    <h3>Chinese</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Restaurants Section -->
    <section class="restaurants">
        <div class="container">
            <h2 class="section-title">Top Restaurants</h2>
            <div class="restaurant-grid">
                <?php if (count($topRestaurants) > 0): ?>
                    <?php foreach ($topRestaurants as $restaurant): ?>
                        <div class="restaurant-card">
                            <img src="https://via.placeholder.com/400x320?text=<?php echo htmlspecialchars($restaurant['name']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                            <div class="info">
                                <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                                <p><?php echo htmlspecialchars($restaurant['cuisine']); ?></p>
                                <p class="rating">
                                    <?php 
                                        $rating = $restaurant['rating'];
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i - 0.5 <= $rating) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                    ?>
                                    <?php echo $rating; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default restaurants if no data from database -->
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=McDonald's" alt="McDonald's">
                        <div class="info">
                            <h3>McDonald's</h3>
                            <p>Fast Food</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                4.0
                            </p>
                        </div>
                    </div>
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=Pizza+Hut" alt="Pizza Hut">
                        <div class="info">
                            <h3>Pizza Hut</h3>
                            <p>Pizzas</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                4.2
                            </p>
                        </div>
                    </div>
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=KFC" alt="KFC">
                        <div class="info">
                            <h3>KFC</h3>
                            <p>Chicken</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <i class="far fa-star"></i>
                                3.5
                            </p>
                        </div>
                    </div>
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=Taco+Bell" alt="Taco Bell">
                        <div class="info">
                            <h3>Taco Bell</h3>
                            <p>Mexican</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                4.1
                            </p>
                        </div>
                    </div>
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=Domino's" alt="Domino's">
                        <div class="info">
                            <h3>Domino's</h3>
                            <p>Pizza</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                4.3
                            </p>
                        </div>
                    </div>
                    <div class="restaurant-card">
                        <img src="https://via.placeholder.com/400x320?text=Subway" alt="Subway">
                        <div class="info">
                            <h3>Subway</h3>
                            <p>Sandwiches</p>
                            <p class="rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                4.6
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- App Promotion -->
    <section class="app-promo">
        <div class="container app-promo-content">
            <div class="app-promo-text">
                <h2>Try it now! Download the Zwiggy app</h2>
                <p>Enjoy exclusive offers and faster delivery with our mobile app.</p>
                <div class="app-buttons">
                    <img src="https://via.placeholder.com/135x40?text=App+Store" alt="App Store">
                    <img src="https://via.placeholder.com/135x40?text=Google+Play" alt="Google Play">
                </div>
            </div>
            <div class="app-promo-image">
                <img src="https://via.placeholder.com/300x200?text=Zwiggy+App" alt="Zwiggy App">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose Zwiggy?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-clock"></i>
                    <h3>Quick Delivery</h3>
                    <p>Food delivered in 30 minutes or less</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-utensils"></i>
                    <h3>Wide Selection</h3>
                    <p>Thousands of restaurants to choose from</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-tag"></i>
                    <h3>Best Offers</h3>
                    <p>Exclusive deals and discounts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>5M+</h3>
                    <p>Happy Customers</p>
                </div>
                <div class="stat-item">
                    <h3>10,000+</h3>
                    <p>Restaurants</p>
                </div>
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Cities Covered</p>
                </div>
                <div class="stat-item">
                    <h3>15,000+</h3>
                    <p>Delivery Partners</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>About Us</h3>
                    <ul>
                        <li><a href="#">About Zwiggy</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Investor Relations</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="#">Help & Support</a></li>
                        <li><a href="#">Partner with us</a></li>
                        <li><a href="#">Ride with us</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                    <p>Download the App</p>
                    <div class="app-buttons">
                        <img src="https://via.placeholder.com/100x30?text=App+Store" alt="App Store" style="height: 30px;">
                        <img src="https://via.placeholder.com/100x30?text=Google+Play" alt="Google Play" style="height: 30px;">
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Zwiggy. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Admin Link (only visible in development) -->
    <a href="admin.php" class="admin-link">Admin Panel</a>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Login</h2>
            <form id="loginForm" action="login_process.php" method="post">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="form-submit">Login</button>
            </form>
            <div class="switch-form">
                <p>Don't have an account? <a href="#" id="switchToSignup">Sign up</a></p>
            </div>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create Account</h2>
            <form id="signupForm" action="signup_process.php" method="post">
                <div class="form-group">
                    <label for="signup-name">Full Name</label>
                    <input type="text" id="signup-name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="signup-email">Email</label>
                    <input type="email" id="signup-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="signup-phone">Phone</label>
                    <input type="tel" id="signup-phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="signup-password">Password</label>
                    <input type="password" id="signup-password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="signup-address">Address</label>
                    <input type="text" id="signup-address" name="address" required>
                </div>
                <button type="submit" class="form-submit">Sign Up</button>
            </form>
            <div class="switch-form">
                <p>Already have an account? <a href="#" id="switchToLogin">Login</a></p>
            </div>
        </div>
    </div>

    <script>
        // Modal handling
        const loginModal = document.getElementById("loginModal");
        const signupModal = document.getElementById("signupModal");
        const loginBtn = document.getElementById("loginBtn");
        const signupBtn = document.getElementById("signupBtn");
        const closeBtns = document.getElementsByClassName("close");
        const switchToSignup = document.getElementById("switchToSignup");
        const switchToLogin = document.getElementById("switchToLogin");

        // Open login modal
        loginBtn.onclick = function() {
            loginModal.style.display = "block
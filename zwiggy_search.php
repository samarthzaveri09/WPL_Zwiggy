<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "zwiggy_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search variables
$food_name_search = '';
$min_price = '';
$max_price = '';
$cuisine_search = '';
$search_results = null;

// Process search form submission
if (isset($_POST['search'])) {
    $food_name_search = $_POST['food_name_search'] ?? '';
    $min_price = $_POST['min_price'] ?? '';
    $max_price = $_POST['max_price'] ?? '';
    $cuisine_search = $_POST['cuisine_search'] ?? '';
    
    // Build search query
    $query = "SELECT * FROM foods WHERE 1=1";
    
    if (!empty($food_name_search)) {
        $query .= " AND food_name LIKE '%" . $conn->real_escape_string($food_name_search) . "%'";
    }
    
    if (!empty($min_price)) {
        $query .= " AND price >= " . $conn->real_escape_string($min_price);
    }
    
    if (!empty($max_price)) {
        $query .= " AND price <= " . $conn->real_escape_string($max_price);
    }
    
    if (!empty($cuisine_search)) {
        $query .= " AND cuisine LIKE '%" . $conn->real_escape_string($cuisine_search) . "%'";
    }
    
    // Execute search query
    $search_results = $conn->query($query);
}

// Get all available cuisines for dropdown
$cuisines = array();
$cuisine_query = "SELECT DISTINCT cuisine FROM foods ORDER BY cuisine";
$cuisine_result = $conn->query($cuisine_query);
if ($cuisine_result->num_rows > 0) {
    while ($row = $cuisine_result->fetch_assoc()) {
        $cuisines[] = $row['cuisine'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Zwiggy Food Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .search-form {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .search-form div {
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .buttons {
            margin-top: 15px;
        }
        .buttons a, .buttons input[type="submit"] {
            padding: 8px 15px;
            text-decoration: none;
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .buttons a {
            background-color: #2196F3;
        }
        .no-results {
            color: #ff0000;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h2>Search Food Items - Zwiggy</h2>
    
    <div class="search-form">
        <form method="POST" action="search.php">
            <div>
                <label for="food_name_search">Food Name:</label>
                <input type="text" id="food_name_search" name="food_name_search" value="<?php echo htmlspecialchars($food_name_search); ?>">
            </div>
            
            <div>
                <label for="min_price">Min Price:</label>
                <input type="number" id="min_price" name="min_price" step="0.01" value="<?php echo htmlspecialchars($min_price); ?>">
                
                <label for="max_price">Max Price:</label>
                <input type="number" id="max_price" name="max_price" step="0.01" value="<?php echo htmlspecialchars($max_price); ?>">
            </div>
            
            <div>
                <label for="cuisine_search">Cuisine:</label>
                <select id="cuisine_search" name="cuisine_search">
                    <option value="">All Cuisines</option>
                    <?php foreach ($cuisines as $cuisine): ?>
                        <option value="<?php echo htmlspecialchars($cuisine); ?>" <?php if($cuisine_search == $cuisine) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cuisine); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="buttons">
                <input type="submit" name="search" value="Search">
                <a href="search.php">Clear Search</a>
                <a href="zwiggy.php">Back to Food Menu</a>
            </div>
        </form>
    </div>
    
    <?php if ($search_results): ?>
        <?php if ($search_results->num_rows > 0): ?>
            <h3>Search Results (<?php echo $search_results->num_rows; ?> items found)</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Cuisine</th>
                </tr>
                <?php while ($row = $search_results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($row['cuisine']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-results">No items match your search criteria.</p>
        <?php endif; ?>
    <?php endif; ?>
    
</body>
</html>

<?php
$conn->close();
?>
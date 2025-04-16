<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Restaurant</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      display: flex;
      height: 100vh;
      background-color: #1a1a1a;
    }

    .container {
      display: flex;
      width: 100%;
      max-width: 900px;
      margin: auto;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .left-panel {
      flex: 1;
      background-color: #d9b3ff;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }

    .logo {
      font-size: 3.5rem;
      font-family: 'Brush Script MT', cursive;
      color: #000;
      margin-bottom: 1rem;
    }

    .icon {
      background-color: rgba(255, 255, 255, 0.7);
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .cutlery {
      font-size: 1.5rem;
      color: #4b0082;
    }

    .right-panel {
      flex: 1;
      background-color: #f2efe4;
      padding: 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    h2 {
      font-size: 1.8rem;
      margin-bottom: 2rem;
      color: #333;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 1rem;
    }

    input[type="text"] {
      width: 100%;
      padding: 0.8rem;
      border: none;
      border-radius: 4px;
      background-color: #fff;
      margin-bottom: 1rem;
    }

    .btn {
      width: 100%;
      padding: 0.8rem;
      background-color: #4b0082;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 1rem;
    }

  </style>
</head>
<body>
  <div class="container">
    <div class="left-panel">
      <div class="logo">Zwiggy</div>
      <div class="icon">
        <span class="cutlery">🍴</span>
      </div>
    </div>
    <div class="right-panel">
      <h2>Update Restaurant Details</h2>
      <form action="update_restaurant.php" method="POST">
        <input type="hidden" name="rest_id" value="<?php echo htmlspecialchars($rest_id); ?>">

        <div class="form-group">
          <input type="text" name="rest_name" placeholder="Restaurant Name" value="<?php echo htmlspecialchars($rest_name ?? ''); ?>" required>
        </div>

        <div class="form-group">
          <input type="text" name="rest_address" placeholder="Address" value="<?php echo htmlspecialchars($rest_address ?? ''); ?>" required>
        </div>

        <div class="form-group">
          <input type="text" name="rest_cuisine" placeholder="Cuisine" value="<?php echo htmlspecialchars($rest_cuisine ?? ''); ?>" required>
        </div>

        <div class="form-group">
          <input type="text" name="rest_contact" placeholder="Contact Number" value="<?php echo htmlspecialchars($rest_contact ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn">Update Restaurant</button>
      </form>
    </div>
  </div>
</body>
</html>

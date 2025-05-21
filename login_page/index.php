<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Log in</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <!-- Left side (Login form) -->
        <div class="login-section">
            <div class="login-box">
                <h1>Login</h1>
                <form method="POST" action="login.php">
                    <div class="input-group">
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit">Log in</button>
                </form>
            </div>
        </div>

        <!-- Right side (Description section) -->
        <div class="welcome-section">
            <div class="welcome-box">
                <img src="../images/CEC1.png" alt="School Logo" class="logo">
                <h2>Welcome to Cebu Eastern College <br> Inventory Management System - School Materials</h2>
                <p>Welcome to Cebu Eastern College's Inventory Management System for School Materials! This platform is designed specifically for our dedicated staffs to easily manage and track educational resources.</p>
                <br>
                <p>Log in to start managing.</p>
            </div>
        </div>
    </div>
</body>
</html>

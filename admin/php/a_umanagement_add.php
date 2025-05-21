<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Input validation (optional)
    if (empty($username) || empty($password) || empty($role)) {
        echo "All fields are required.";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL query to insert data into the users table
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement, bind password as a string
        $stmt->bind_param("ssi", $username, $hashed_password, $role);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect back to the user page with a success message
            echo "<script>alert('User added successfully!'); window.location.href = 'a_umanagement.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Add User</title>
        <link rel="stylesheet" href="../css/a_umanagement_add.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/modal.css">

        <script>
                // Show the logout confirmation modal
                function confirmLogout() {
                    document.getElementById('logoutModal').style.display = 'block';
                }

                // Close the logout confirmation modal
                function closeLogoutModal() {
                    document.getElementById('logoutModal').style.display = 'none';
                }
            </script>
    </head>
    <body>
        <div class="dashboard-container">
            <!-- Sidebar Section -->
            <aside class="sidebar">
            <h2>INVENTORY MANAGEMENT</h2>
            <p class="greeting">Welcome, <b>Super Admin</b>!</p>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="a_dashboard.php"><i class="material-icons">home</i>Dashboard</a></li>
                    <li><a href="a_product.php"><i class="material-icons">inventory</i>Product</a></li>
                    <li><a href="a_product_new.php"><i class="material-icons">add_circle</i>Insert Product</a></li>
                    <li><a href="a_category.php"><i class="material-icons">category</i>Category</a></li>
                    <li><a href="a_department.php"><i class="material-icons">apartment</i>Department</a></li>
                    <li><a href="a_umanagement.php"><i class="material-icons">people</i>User Management</a></li>
                    <li><a href="a_order.php"><i class="material-icons">shopping_basket</i>Order</a></li>
                </ul>
            </nav>
            </aside>

            <!-- Main Content Section -->
            <main class="main-content">
                <!-- Top Header Section -->
                <header class="top-header">
                    <button class="logout-btn" onclick="confirmLogout()">
                        <i class="material-icons">logout</i>Log out
                    </button>
                </header>

                <div class="container">
                    <div class="header">
                        <span class="material-icons back-arrow" onclick="window.history.back();">arrow_back</span>
                        <h2>Add User</h2>
                    </div>

                    <!-- Add User Form -->
                    <form class="user-form" method="POST" action="">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select name="role" id="role" required>
                                <option value="">Select Role</option>
                                <?php
                                include 'db_conn.php';
                                
                                $query = "SELECT * FROM roles";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['user_role']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-add">Add</button>
                    </form>
                </div>

                <!-- Logout Confirmation Modal -->
                <div id="logoutModal" class="modal">
                    <div class="modal-content">
                        <h3>Exit Management?</h3>
                        <form method="POST" action="logout.php">
                            <button type="submit" class="btn-confirm">Log out</button>
                            <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>

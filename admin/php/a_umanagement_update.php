<?php
include 'db_conn.php';

// Initialize variables
$user_id = $_GET['id']; // Assume user ID is passed via GET request
$username = $password = $role = "";

// Fetch the existing user details if a valid ID is passed
if (isset($user_id)) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user data and assign to variables
        $user = $result->fetch_assoc();
        $username = $user['username'];
        // Password will not be fetched/displayed for security reasons
        $role = $user['role'];
    } else {
        echo "User not found.";
        exit;
    }
}

// Handle form submission to update the user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $updated_username = $_POST['username'];
    $updated_password = $_POST['password'];
    $updated_role = $_POST['role'];

    // Hash the password if it is provided
    if (!empty($updated_password)) {
        $hashed_password = password_hash($updated_password, PASSWORD_DEFAULT);
    } else {
        // Keep the existing password if the password field is left empty
        $hashed_password = $password;
    }

    // Update user in the database
    $update_query = "UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssii", $updated_username, $hashed_password, $updated_role, $user_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('User updated successfully!');
                window.location.href = 'a_umanagement.php';
              </script>";
        exit;
    } else {
        echo "<script>alert('Error updating user: " . $stmt->error . "');</script>";
    }    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Update User</title>
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
                    <h2>Update User</h2>
                </div>

                <!-- Update User Form -->
                <form class="user-form" method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" required>
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
                                    echo "<option value='" . $row['id'] . "' " . ($row['id'] == $role ? "selected" : "") . ">" . htmlspecialchars($row['user_role']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-add">Update</button>
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

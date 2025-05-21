<?php
include 'db_conn.php';

$department_name = "";
$error = "";

// Check if an ID is provided in the query string (for fetching department details)
if (isset($_GET['id'])) {
    $department_id = $_GET['id'];

    // Fetch the existing department from the database
    $stmt = $conn->prepare("SELECT department_name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if department exists
    if ($result->num_rows > 0) {
        $department = $result->fetch_assoc();
        $department_name = $department['department_name'];
    } else {
        echo "<script>alert('Department not found!'); window.location.href = 'a_department.php';</script>";
        exit;
    }

    $stmt->close();
}

// Handle form submission for updating the department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the updated department name from the form
    $department_name = trim($_POST['department_name']);

    // Validate input
    if (empty($department_name)) {
        $error = "Department name cannot be empty!";
    } else {
        // Update the department in the database
        $stmt = $conn->prepare("UPDATE departments SET department_name = ? WHERE id = ?");
        $stmt->bind_param("si", $department_name, $department_id);

        if ($stmt->execute()) {
            echo "<script>alert('Department successfully updated!'); window.location.href = 'a_department.php';</script>";
        } else {
            $error = "Error updating department: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Update Department</title>
    <link rel="stylesheet" href="../css/a_department_add.css">
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
            <p class="greeting">Welcome, <b>Admin</b>!</p>
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
                    <h2>Update Department</h2>
                </div>

                <!-- Update Department Form -->
                <form class="department-form" method="POST" action="">
                    <div class="form-group">
                        <label for="department_name">Department Name:</label>
                        <input type="text" name="department_name" id="department_name" value="<?php echo htmlspecialchars($department_name); ?>" required>
                    </div>
                    <button type="submit" class="btn-add">Update</button>
                </form>
            </div>
        </main>

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
    </div>
</body>
</html>

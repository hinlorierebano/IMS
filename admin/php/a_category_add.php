<?php
include 'db_conn.php';

$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get category name from form input
    $category_name = trim($_POST["category_name"]);

    // Validate input
    if (empty($category_name)) {
        $error = "Category name cannot be empty!";
    } else {
        // Prepare SQL to insert new category
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            // Alert success and redirect
            echo "<script>alert('Category successfully added!'); window.location.href = 'a_category.php';</script>";
        } else {
            echo "<script>alert('Error adding category');</script>";
        }

        // Close the statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Add Category</title>
        <link rel="stylesheet" href="../css/a_category_add.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="../css/modal.css">

        <script>
            // Auto capitalize the first letter
            function capitalizeFirstLetter(input) {
                let value = input.value;
                input.value = value.charAt(0).toUpperCase() + value.slice(1);
            }
            
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
                        <h2>Add Category</h2>
                    </div>

                    <!-- Add Category Form -->
                    <form class="category-form" method="POST" action="">
                        <div class="form-group">
                            <label for="category_name">Category Name:</label>
                            <input type="text" name="category_name" id="category_name" required oninput="capitalizeFirstLetter(this)">
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

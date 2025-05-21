<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $product_type = $_POST['product_type']; // Get the selected type
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

    if (empty($product_name) || empty($product_type) || empty($quantity) || empty($category)) {
        echo "All fields are required.";
        exit();
    }

    $sql = "INSERT INTO products (product_name, product_type, quantity, category_id) VALUES (?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssii", $product_name, $product_type, $quantity, $category);
        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!'); window.location.href = 'a_product.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Add Product</title>
        <link rel="stylesheet" href="../css/a_product_add.css">
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
                <!-- Top Header with Logout Button -->
                <header class="top-header">
                    <button class="logout-btn" onclick="confirmLogout()">
                        <i class="material-icons">logout</i>Log out
                    </button>
                </header>

                <div class="container">
                    <div class="header">
                        <span class="material-icons back-arrow" onclick="window.history.back();">arrow_back</span>
                        <h2>Add Product</h2>
                    </div>

                    <!-- Add Product Form -->
                    <form class="product-form" method="POST" action="">
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" name="product_name" id="product_name" required oninput="capitalizeFirstLetter(this)">
                        </div>

                        <div class="form-group">
                            <label for="product_type">Product Type:</label>
                            <select name="product_type" id="product_type" required>
                                <option value="">Select Type</option>
                                <option value="Ream">Ream</option>
                                <option value="Box">Box</option>
                                <option value="Pack">Pack</option>
                                <option value="Individual">Individual</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">No. of Item on Hand:</label>
                            <input type="number" name="quantity" id="quantity" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select name="category" id="category" required>
                                <option value="">Select Category</option>
                                <?php
                                // Fetch categories from database
                                include 'db_conn.php';
                                $query = "SELECT * FROM categories";
                                $result = $conn->query($query);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>";
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

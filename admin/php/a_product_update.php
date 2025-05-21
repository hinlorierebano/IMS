<?php
include 'db_conn.php';

// Initialize variables
$product_id = $_GET['id'];
$product_name = $product_type = $quantity = $category_id = "";

// Fetch the existing product details if a valid ID is passed
if (isset($product_id)) {
    $query = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch product data and assign to variables
        $product = $result->fetch_assoc();
        $product_name = $product['product_name'];
        $product_type = $product['product_type'];
        $quantity = $product['quantity'];
        $category_id = $product['category_id'];
    } else {
        echo "Product not found.";
        exit;
    }
}

// Handle form submission to update the product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $updated_product_name = $_POST['product_name'];
    $updated_product_type = $_POST['product_type'];
    $updated_quantity = $_POST['quantity'];
    $updated_category_id = $_POST['category'];

    // Update product in the database
    $update_query = "UPDATE products SET product_name = ?, product_type = ?, quantity = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssiii", $updated_product_name, $updated_product_type, $updated_quantity, $updated_category_id, $product_id);

    if ($stmt->execute()) {
        // JavaScript alert for success
        echo "<script>alert('Product updated successfully!'); window.location.href = 'a_product.php';</script>";
    } else {
        // Error alert in case of failure
        echo "<script>alert('Error updating product: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Update Product</title>
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
                <!-- Top Header Section -->
                <header class="top-header">
                    <button class="logout-btn" onclick="confirmLogout()">
                        <i class="material-icons">logout</i>Log out
                    </button>
                </header>

                <div class="container">
                    <div class="header">
                        <span class="material-icons back-arrow" onclick="window.history.back();">arrow_back</span>
                        <h2>Update Product</h2>
                    </div>

                    <!-- Update Product Form -->
                    <form class="product-form" method="POST" action="">
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="product_type">Product Type:</label>
                            <select name="product_type" id="product_type" required>
                                <option value="">Select Type</option>
                                <option value="Ream" <?php echo ($product_type == "Ream") ? "selected" : ""; ?>>Ream</option>
                                <option value="Box" <?php echo ($product_type == "Box") ? "selected" : ""; ?>>Box</option>
                                <option value="Pack" <?php echo ($product_type == "Pack") ? "selected" : ""; ?>>Pack</option>
                                <option value="Individual" <?php echo ($product_type == "Individual") ? "selected" : ""; ?>>Individual</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">No. of Item on Hand:</label>
                            <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select name="category" id="category" required>
                                <option value="">Select Category</option>
                                <?php
                                // Fetch categories from the database
                                $category_query = "SELECT * FROM categories";
                                $category_result = $conn->query($category_query);

                                if ($category_result->num_rows > 0) {
                                    while ($row = $category_result->fetch_assoc()) {
                                        $selected = ($row['id'] == $category_id) ? "selected" : "";
                                        echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['category_name']) . "</option>";
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

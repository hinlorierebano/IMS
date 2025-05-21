<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_name'];
    $new_quantity = $_POST['quantity'];

    // Fetch the current quantity of the product
    $query = "SELECT quantity FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $existing_quantity = $row['quantity'];
        $updated_quantity = $existing_quantity + $new_quantity;

        // Update the product's total quantity in the products table
        $updateQuery = "UPDATE products SET quantity = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $updated_quantity, $product_id);
        $updateStmt->execute();

        // Log the update in the product_updates table
        $logQuery = "INSERT INTO product_updates (product_id, quantity) VALUES (?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("ii", $product_id, $new_quantity);
        $logStmt->execute();

        echo "<script>
                alert('Quantity updated successfully!');
                window.location.href = 'a_product_new.php';
              </script>";
    } else {
        echo "<script>
                alert('Product not found.');
                window.location.href = 'a_product_new.php';
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Add Product Quantity</title>
        <link rel="stylesheet" href="../css/a_product_add.css">
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
                <!-- Top Header with Logout Button -->
                <header class="top-header">
                    <button class="logout-btn" onclick="confirmLogout()">
                        <i class="material-icons">logout</i>Log out
                    </button>
                </header>

                <div class="container">
                    <div class="header">
                        <span class="material-icons back-arrow" onclick="window.history.back();">arrow_back</span>
                        <h2>Add Quantity to Product</h2>
                    </div>

                    <!-- Add Product Form -->
                    <form class="product-form" method="POST" action="">
                        <div class="form-group">
                            <label for="product_name">Product Name:</label>
                            <select name="product_name" id="product_name" required>
                                <option value="">Select Product</option>
                                <?php
                                // Fetch only active (non-archived) products from the database
                                $productQuery = "SELECT id, product_name FROM products WHERE deleted_at IS NULL";
                                $productResult = $conn->query($productQuery);
                                if ($productResult->num_rows > 0) {
                                    while ($productRow = $productResult->fetch_assoc()) {
                                        echo "<option value='" . $productRow['id'] . "'>" . $productRow['product_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="quantity">No. of Item on Hand:</label>
                            <input type="number" name="quantity" id="quantity" required>
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

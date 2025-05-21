<?php
include 'db_conn.php';

// Initialize variables
$order_id = $_GET['id'];
$product_id = $quantity = $department_id = "";

// Fetch the existing order details if a valid ID is passed
if (isset($order_id)) {
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch order data and assign to variables
        $order = $result->fetch_assoc();
        $product_id = $order['product_id'];
        $quantity = $order['quantity'];
        $department_id = $order['department_id'];
    } else {
        echo "Order not found.";
        exit;
    }
}

// Handle form submission to update the order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $updated_product_id = $_POST['product_id'];
    $updated_quantity = $_POST['quantity'];
    $updated_department_id = $_POST['department'];

    // Check if the ordered quantity exceeds available product quantity
    $product_query = "SELECT quantity FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $updated_product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();
    
    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        if ($updated_quantity > $product['quantity']) {
            // Show alert if quantity exceeds available stock
            echo "<script>alert('Order quantity exceeds available stock.'); window.history.back();</script>";
            exit;
        }
    }

    // Update the order in the database
    $update_query = "UPDATE orders SET product_id = ?, quantity = ?, department_id = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("iisi", $updated_product_id, $updated_quantity, $updated_department_id, $order_id);

    if ($stmt->execute()) {
        // Success alert
        echo "<script>alert('Order updated successfully!'); window.location.href = 'a_order.php';</script>";
    } else {
        // Error alert
        echo "<script>alert('Error updating order: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Update Order</title>
    <link rel="stylesheet" href="../css/a_order_add.css">
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

        <main class="main-content">
            <header class="top-header">
                <button class="logout-btn" onclick="confirmLogout()">
                    <i class="material-icons">logout</i>Log out
                </button>
            </header>

            <div class="container">
                <div class="header">
                    <span class="material-icons back-arrow" onclick="window.history.back();">arrow_back</span>
                    <h2>Update Order</h2>
                </div>

                <!-- Update Order Form -->
                <form class="order-form" method="POST" action="">
                    <div class="form-group">
                        <label for="product_id">Product Name:</label>
                        <select name="product_id" id="product_id" required>
                            <option value="">Select Product</option>
                            <?php
                            $query = "SELECT * FROM products";
                            $result = $conn->query($query);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($row['id'] == $product_id) ? "selected" : "";
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['product_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="department">Department:</label>
                        <select name="department" id="department" required>
                            <option value="">Select Department</option>
                            <?php
                            $query = "SELECT * FROM departments";
                            $result = $conn->query($query);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($row['id'] == $department_id) ? "selected" : "";
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['department_name'] . "</option>";
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

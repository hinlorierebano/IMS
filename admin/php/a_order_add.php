<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $department = $_POST['department'];

    // Input validation (optional)
    if (empty($product_id) || empty($quantity) || empty($department)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Fetch the available quantity for the selected product
    $productQuery = "SELECT quantity FROM products WHERE id = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($available_quantity);
    $stmt->fetch();
    $stmt->close();

    // Check if the ordered quantity exceeds the available quantity
    if ($quantity > $available_quantity) {
        echo "<script>alert('The ordered quantity exceeds the available stock. Available quantity: $available_quantity'); window.history.back();</script>";
        exit();
    }

    // Proceed to insert the order if the quantity is valid
    $sql = "INSERT INTO orders (product_id, quantity, department_id, order_date) VALUES (?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement
        $stmt->bind_param("iis", $product_id, $quantity, $department);

        // Execute the statement
        if ($stmt->execute()) {
            // Show success message and redirect
            echo "<script>alert('Order added successfully!'); window.location.href = 'a_order.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
        }

        // Close statement
        $stmt->close();
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>";
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
    <title>Inventory Management | Add Order</title>
    <link rel="stylesheet" href="../css/a_order_add.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/modal.css">

    <script>
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

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
                    <h2>Add Order</h2>
                </div>

                <!-- Add Order Form -->
                <form class="order-form" method="POST" action="">
                    <div class="form-group">
                        <label for="product_id">Product Name:</label>
                        <select name="product_id" id="product_name" required>
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
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" required>
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
                                    echo "<option value='" . $row['id'] . "'>" . $row['department_name'] . "</option>";
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

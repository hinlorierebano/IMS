<?php
include 'db_conn.php';

$query = "SELECT pu.id, p.product_name, pu.quantity, pu.date_added, c.category_name
        FROM product_updates pu
        JOIN products p ON pu.product_id = p.id
        JOIN categories c ON p.category_id = c.id
        ORDER BY pu.date_added DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Product</title>
    <link rel="stylesheet" href="../css/a_product.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/modal.css">

    <script>
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        function confirmDelete(productId) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('productToDelete').value = productId;
        }

        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
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
                    <div class="left-section">
                        <h2>Inserted Products</h2>
                    </div>

                    <div class="right-section">
                        <button class="add-item" onclick="window.location.href='a_product_new_add.php'">Insert Item</button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity Added</th>
                                <th>Category</th>
                                <th>Date Added</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                                        <td>" . htmlspecialchars($row['quantity']) . "</td>
                                        <td>" . htmlspecialchars($row['category_name']) . "</td>
                                        <td>" . htmlspecialchars($row['date_added']) . "</td>
                                        <td>
                                            <span class='material-icons icon delete' title='Delete Record' onclick='confirmDelete(" . $row['id'] . ")'>delete</span>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No products found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h3>Delete this record?</h3>
                    <form method="POST" action="a_product_new_delete.php">
                        <input type="hidden" name="product_id" id="productToDelete" value="">
                        <button type="submit" class="btn-confirm">Confirm</button>
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    </form>
                </div>
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

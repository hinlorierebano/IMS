<?php
include 'db_conn.php';

// Fetch soft-deleted products (deleted within the last 30 days)
$query = "SELECT products.*, categories.category_name 
          FROM products 
          JOIN categories ON products.category_id = categories.id 
          WHERE products.deleted_at IS NOT NULL 
          AND products.deleted_at >= NOW() - INTERVAL 30 DAY
          ORDER BY products.deleted_at DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Archive</title>
    <link rel="stylesheet" href="../css/a_product.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="../css/modal.css">

    <script>
        // Show modals dynamically
        function showModal(action, productId) {
            if (action === 'restore') {
                document.getElementById('restoreModal').style.display = 'block';
                document.getElementById('restoreProductId').value = productId;
            } else if (action === 'delete') {
                document.getElementById('deleteModal').style.display = 'block';
                document.getElementById('deleteProductId').value = productId;
            }
        }

        // Close all modals
        function closeModal() {
            document.getElementById('restoreModal').style.display = 'none';
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Show logout confirmation modal
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        // Close logout modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Searchbar function
        function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.product-table tbody tr');

            rows.forEach(row => {
                const productName = row.querySelector('td').textContent.toLowerCase();
                row.style.display = productName.includes(searchTerm) ? '' : 'none';
            });
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
                        <h2>Recently Deleted Products</h2>
                    </div>

                    <div class="right-section">
                        <!-- Search Bar-->
                        <input type="text" id="searchInput" class="search-field" placeholder="Search products..." onkeyup="searchProducts()">
                    </div>
                </div>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Product Type</th>
                            <th>Stock Quantity</th>
                            <th>Category</th>
                            <th>Date Deleted</th>
                            <th style="width: 15%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                                    <td>" . htmlspecialchars($row['product_type']) . "</td>
                                    <td>" . htmlspecialchars($row['quantity']) . "</td>
                                    <td>" . htmlspecialchars($row['category_name']) . "</td>
                                    <td>" . htmlspecialchars($row['deleted_at']) . "</td>
                                    <td>
                                        <span class='material-icons icon restore' title='Restore Product' onclick='showModal(\"restore\", " . $row['id'] . ")'>restore</span>
                                        <span class='material-icons icon delete' title='Delete Product' onclick='showModal(\"delete\", " . $row['id'] . ")'>delete</span>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No archived products found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Restore Confirmation Modal -->
            <div id="restoreModal" class="modal">
                <div class="modal-content">
                    <h3>Restore this product?</h3>
                    <form method="POST" action="a_product_restore.php">
                        <input type="hidden" name="product_id" id="restoreProductId">
                        <button type="submit" class="btn-confirm">Confirm</button>
                        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h3>Permanently delete this product?</h3>
                    <form method="POST" action="a_product_delete_permanent.php">
                        <input type="hidden" name="product_id" id="deleteProductId">
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

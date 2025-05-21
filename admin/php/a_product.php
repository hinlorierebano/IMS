<?php
include 'db_conn.php';

// Fetch categories from the database for the dropdown
$categoriesQuery = "SELECT * FROM categories";
$categoriesResult = $conn->query($categoriesQuery);

// Fetch products with category name from the database
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'DESC' : 'ASC';

if ($categoryFilter) {
    // If a category is selected, filter the products based on the category and exclude archived products
    $query = "SELECT products.*, categories.category_name 
              FROM products 
              JOIN categories ON products.category_id = categories.id 
              WHERE products.category_id = ? AND products.deleted_at IS NULL
              ORDER BY quantity $sortOrder";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $categoryFilter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Fetch all products with category names and exclude archived products
    $query = "SELECT products.*, categories.category_name 
              FROM products 
              JOIN categories ON products.category_id = categories.id 
              WHERE products.deleted_at IS NULL
              ORDER BY quantity $sortOrder";
    $result = $conn->query($query);
}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <script>
        // Show the logout confirmation modal
        function confirmLogout() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        // Close the logout confirmation modal
        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        // Handle category change to filter products
        function filterByCategory() {
            var category = document.getElementById('categoryFilter').value;
            window.location.href = "a_product.php?category=" + category;
        }

        let productIdToDelete = null; // Variable to store product ID for deletion

        // Show the delete confirmation modal for product deletion
        function confirmDelete(productId) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('productToDelete').value = productId; // Set the product ID to the hidden input
        }

        // Close the delete confirmation modal
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Sort the items
        function sortItems() {
            const sortOrder = document.getElementById('sortItems').value;
            const categoryId = document.getElementById('categoryFilter').value;
            const url = new URL(window.location.href);

            // Add sort order and category as URL parameters
            if (sortOrder) {
                url.searchParams.set('sort', sortOrder);
            } else {
                url.searchParams.delete('sort');
            }
            
            if (categoryId) {
                url.searchParams.set('category', categoryId);
            } else {
                url.searchParams.delete('category');
            }

            // Redirect to the new URL to reload with sorting
            window.location.href = url.toString();
        }

        //Searchbar function
        function searchProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.product-table tbody tr');

        rows.forEach(row => {
            const productName = row.querySelector('td').textContent.toLowerCase();
            if (productName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                }
            });
        }
        function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Title
        doc.setFontSize(16);
        doc.text("Product Inventory", 14, 15);

        // Get table data
        const table = document.querySelector(".product-table");
        const headers = [];
        const data = [];

        // Extract table headers
        table.querySelectorAll("thead th").forEach(th => headers.push(th.innerText));

        // Extract table rows
        table.querySelectorAll("tbody tr").forEach(tr => {
            const rowData = [];
            tr.querySelectorAll("td").forEach(td => rowData.push(td.innerText));
            data.push(rowData);
        });

        // Generate PDF table
        doc.autoTable({
            head: [headers],
            body: data,
            startY: 25,
        });

        // Save the PDF
        doc.save("Product_Inventory.pdf");
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
            <!-- Logout Button-->
                <button class="export-pdf logout-btn" onclick="generatePDF()" style="margin-right: 10px;">
                    <i class="material-icons">print</i>
                </button>
            
                <button class="logout-btn" onclick="confirmLogout()">
                    <i class="material-icons">logout</i>Log out
                </button>

            </header>

            <div class="container">
                <div class="header">
                    <div class="left-section">
                        <h2>Products</h2>
                    </div>
                    
                    <div class="right-section">
                        <a href="a_product_archive.php"><span class="material-icons icon archive" title="View Archived Products">archive</span></a>
                        <input type="text" id="searchInput" class="search-field" placeholder="Search products..." onkeyup="searchProducts()">
                        <select id="categoryFilter" class="filter" onchange="filterByCategory()">
                            <option value="">All Categories</option>
                            <?php
                            // Populate the category dropdown from the database
                            if ($categoriesResult->num_rows > 0) {
                                while ($category = $categoriesResult->fetch_assoc()) {
                                    $selected = ($categoryFilter == $category['id']) ? 'selected' : '';
                                    echo "<option value='" . $category['id'] . "' $selected>" . htmlspecialchars($category['category_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <select id="sortItems" class="filter" onchange="sortItems()">
                            <option value="">Sort No. of Items</option>
                            <option value="asc">Ascending</option>
                            <option value="desc">Descending</option>
                        </select>

                        <button class="add-item" onclick="window.location.href='a_product_add.php'">Add Item</button>
                    </div>
                </div>
                
                <!-- Product Table -->
                <div class="table-container">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Product Type</th>
                                <th>Stock Quantity</th>
                                <th>Category</th>
                                <th>Date Added</th>
                                <th style="width: 15%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Check if there are results
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Check if the quantity is low (e.g., less than 10)
                                    $lowStockClass = ($row['quantity'] < 30) ? 'low-stock' : '';

                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                                        <td>" . htmlspecialchars($row['product_type']) . "</td>
                                        <td class='$lowStockClass'>" . htmlspecialchars($row['quantity']) . "</td>
                                        <td>" . htmlspecialchars($row['category_name']) . "</td>
                                        <td>" . htmlspecialchars($row['date_added']) . "</td>
                                        <td>
                                            <a href='a_product_update.php?id=" . $row['id'] . "'>
                                                <span class='material-icons icon edit' title='Edit Product'>edit</span></a>
                                            <span class='material-icons icon delete' title='Delete Product' onclick='confirmDelete(" . $row['id'] . ")'>delete</span>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No products found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h3>Delete this product?</h3>
                    <form method="POST" action="a_product_delete.php">
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

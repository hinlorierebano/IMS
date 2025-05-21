<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Management | Department</title>
        <link rel="stylesheet" href="../css/a_department.css">
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

            // Show the delete confirmation modal for department deletion
            function confirmDelete(departmentId) {
                document.getElementById('deleteModal').style.display = 'block';
                document.getElementById('departmentToDelete').value = departmentId;
            }

            // Close the delete confirmation modal
            function closeModal() {
                document.getElementById('deleteModal').style.display = 'none';
            }

            function searchDepartment() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toLowerCase();
            const table = document.querySelector("table tbody");
            const rows = table.getElementsByTagName("tr");

                for (let i = 0; i < rows.length; i++) {
                    const departmentCell = rows[i].getElementsByTagName("td")[0];
                    if (departmentCell) {
                        const departmentName = departmentCell.textContent || departmentCell.innerText;
                        rows[i].style.display = departmentName.toLowerCase().includes(filter) ? "" : "none";
                    }
                }
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
                    <button class="logout-btn" onclick="confirmLogout()">
                        <i class="material-icons">logout</i>Log out
                    </button>
                </header>

                <!-- Department Table -->
                <div class="container">
                    <div class="header">
                        <div class="left-section">
                            <h2>Departments</h2>
                        </div>

                        <div class="right-section">
                            <!-- Search Bar-->
                            <input type="text" id="searchInput" class="search-field" placeholder="Search department..." onkeyup="searchDepartment()">
                            <button class="add-department" onclick="window.location.href='a_department_add.php'">Add Department</button>
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Department Name</th>
                                    <th style="width: 15%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'db_conn.php';
                                
                                $query = "SELECT * FROM departments";
                                $result = $conn->query($query);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($row['department_name']) . "</td>
                                            <td>
                                                <a href='a_department_update.php?id=" . $row['id'] . "'>
                                                    <span class='material-icons icon edit' title='Edit Department'>edit</span></a>
                                                <span class='material-icons icon delete' title='Delete Department' onclick='confirmDelete(" . $row['id'] . ")'>delete</span>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No departments found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="deleteModal" class="modal">
                    <div class="modal-content">
                        <h3>Delete this department?</h3>
                        <form method="POST" action="a_department_delete.php">
                            <input type="hidden" name="department_id" id="departmentToDelete">
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

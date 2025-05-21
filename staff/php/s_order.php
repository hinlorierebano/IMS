<?php
include 'db_conn.php';

// Fetch orders with department and product details from the database
$departmentFilter = isset($_GET['department']) ? $_GET['department'] : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'DESC'; // Default to newest first

// Modify query based on filters and sort order
$query = "SELECT orders.*, departments.department_name, products.product_name 
          FROM orders 
          JOIN departments ON orders.department_id = departments.id 
          JOIN products ON orders.product_id = products.id 
          WHERE 1=1";

// Add department filter if selected
if ($departmentFilter) {
    $query .= " AND departments.id = ?";
}

// Add status filter if selected
if ($statusFilter) {
    $query .= " AND orders.status = ?";
}

// Add sorting by order date
$query .= " ORDER BY orders.order_date $sortOrder";

// Prepare and execute the query
$stmt = $conn->prepare($query);

if ($departmentFilter && $statusFilter) {
    $stmt->bind_param('is', $departmentFilter, $statusFilter);
} elseif ($departmentFilter) {
    $stmt->bind_param('i', $departmentFilter);
} elseif ($statusFilter) {
    $stmt->bind_param('s', $statusFilter);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if the query was successful
if (!$result) {
    die('Error in SQL query: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Order</title>
    <link rel="stylesheet" href="../css/s_order.css">
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

        // Handle filter changes
        function filterOrders() {
            var department = document.getElementById('departmentFilter').value;
            var status = document.getElementById('statusFilter').value;
            var sortOrder = document.getElementById('sortOrder').value; // Get sort order
            window.location.href = "s_order.php?department=" + department + "&status=" + status + "&sortOrder=" + sortOrder;
        }

        let orderIdToDelete = null; // Variable to store order ID for deletion
        let orderIdToUpdate = null;   // Variable to store order ID for status update

        // Show the delete confirmation modal
        function confirmDelete(orderId) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('orderToDelete').value = orderId;
        }

        // Close any modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Show the status update modal
        function changeStatus(orderId) {
            document.getElementById('statusModal').style.display = 'block';
            document.getElementById('orderToUpdate').value = orderId;
        }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Section -->
        <aside class="sidebar">
            <h2>INVENTORY MANAGEMENT</h2>
            <p class="greeting">Welcome, <b>Staff</b>!</p>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="s_dashboard.php"><i class="material-icons">home</i>Dashboard</a></li>
                    <li><a href="s_product.php"><i class="material-icons">inventory</i>Product</a></li>
                    <li><a href="s_order.php"><i class="material-icons">shopping_basket</i>Order</a></li>
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
                    <div class="left-section">
                        <h2>Orders</h2>
                    </div>

                    <div class="right-section">
                        <!-- Department Filter -->
                        <select id="departmentFilter" class="filter" onchange="filterOrders()">
                            <option value="">All Departments</option>
                            <?php
                            $departmentsResult = $conn->query("SELECT * FROM departments");
                            if ($departmentsResult->num_rows > 0) {
                                while ($department = $departmentsResult->fetch_assoc()) {
                                    $selected = ($departmentFilter == $department['id']) ? 'selected' : '';
                                    echo "<option value='" . $department['id'] . "' $selected>" . htmlspecialchars($department['department_name']) . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <!-- Sort Order Filter -->
                        <select id="sortOrder" class="filter" onchange="filterOrders()">
                            <option value="DESC" <?= isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'DESC' ? 'selected' : '' ?>>Newest Orders</option>
                            <option value="ASC" <?= isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'ASC' ? 'selected' : '' ?>>Oldest Orders</option>
                        </select>

                        <!-- Status Filter -->
                        <select id="statusFilter" class="filter" onchange="filterOrders()">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?= $statusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Claimed" <?= $statusFilter == 'Claimed' ? 'selected' : '' ?>>Claimed</option>
                        </select>

                        <button class="add-order" onclick="window.location.href='s_order_add.php'">Add Order</button>
                    </div>
                </div>

                <!-- Order Table -->
                <div class="table-container">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Department</th>
                                <th>Date Ordered</th>
                                <th>Status</th>
                                <th style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    // Assign a class based on status
                                    $statusClass = ($row['status'] == 'Claimed') ? 'claimed' : 'pending';
                                    
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                                        <td>" . htmlspecialchars($row['quantity']) . "</td>
                                        <td>" . htmlspecialchars($row['department_name']) . "</td>
                                        <td>" . htmlspecialchars($row['order_date']) . "</td>
                                        <td class='$statusClass'>" . htmlspecialchars($row['status']) . "</td>
                                        <td>";
                                        
                                    // Display buttons only if status is "Pending"
                                    if ($row['status'] == 'Pending') {
                                        echo "<a href='a_order_update.php?id=" . $row['id'] . "'>
                                            <span class='material-icons icon edit' title='Edit Order'>edit</span></a>
                                            <span class='material-icons icon delete' title='Delete Order' onclick='confirmDelete(" . $row['id'] . ")'>delete</span>";
                                    }
                                    
                                    echo "<span class='material-icons icon check' title='Change Status' onclick='changeStatus(" . $row['id'] . ")'>check_circle</span>";
                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No orders found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="modal">
                <div class="modal-content">
                    <h3>Delete this order?</h3>
                    <form method="POST" action="s_order_delete.php">
                        <input type="hidden" name="order_id" id="orderToDelete" value="">
                        <button type="submit" class="btn-confirm">Confirm</button>
                        <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    </form>
                </div>
            </div>

            <!-- Status Update Modal -->
            <div id="statusModal" class="modal">
                <div class="modal-content">
                    <h3>Update Order Status</h3>
                    <form method="POST" action="s_order_status_update.php">
                        <input type="hidden" name="order_id" id="orderToUpdate" value="">
                        <div class="update-status">
                            <label for="newStatus">Select Status:</label>
                            <select class="status" name="new_status" id="newStatus" required>
                                <option value="Pending">Pending</option>
                                <option value="Claimed">Claimed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-confirm">Confirm</button>
                        <button type="button" class="btn-cancel" onclick="closeModal('statusModal')">Cancel</button>
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

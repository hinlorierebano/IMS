<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management | Dashboard</title>
    <link rel="stylesheet" href="../css/a_dashboard.css">
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
          <!-- Top Header Section -->
          <header class="top-header">
              <button class="logout-btn" onclick="confirmLogout()">
                <i class="material-icons">logout</i>Log out
              </button>
          </header>

          <!-- Dashboard Cards -->
          <section class="dashboard-cards">
            <?php
            include 'db_conn.php';

            // Get total users
            $users_result = $conn->query("SELECT COUNT(*) AS total FROM users");
            $users_total = $users_result->fetch_assoc()['total'];

            // Get total items (non-arachived)
            $items_result = $conn->query("SELECT COUNT(*) AS total FROM products WHERE deleted_at IS NULL");
            $items_total = $items_result->fetch_assoc()['total'];

            // Get total categories
            $categories_result = $conn->query("SELECT COUNT(*) AS total FROM categories");
            $categories_total = $categories_result->fetch_assoc()['total'];

            // Get total departments
            $departments_result = $conn->query("SELECT COUNT(*) AS total FROM departments");
            $departments_total = $departments_result->fetch_assoc()['total'];

            // Get total orders
            $orders_result = $conn->query("SELECT COUNT(*) AS total FROM orders");
            $orders_total = $orders_result->fetch_assoc()['total'];

            // Get low stock items
            $low_stock_result = $conn->query("SELECT COUNT(*) AS total FROM products WHERE quantity < 100");
            $low_stock_total = $low_stock_result->fetch_assoc()['total'];

            // Get recently added items
            $recent_items_result = $conn->query("SELECT COUNT(*) AS total FROM products WHERE date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $recent_items_total = $recent_items_result->fetch_assoc()['total'];
            ?>

            <div class="card">
              <h3>Users</h3>
              <div class="total-number"><?php echo $users_total; ?></div>
            </div>
            <div class="card">
              <h3>Items</h3>
              <div class="total-number"><?php echo $items_total; ?></div>
            </div>
            <div class="card">
              <h3>Categories</h3>
              <div class="total-number"><?php echo $categories_total; ?></div>
            </div>
            <div class="card">
              <h3>Departments</h3>
              <div class="total-number"><?php echo $departments_total; ?></div>
            </div>
            <div class="card">
              <h3>Orders</h3>
              <div class="total-number"><?php echo $orders_total; ?></div>
            </div>
            <div class="card">
              <h3>Low Stock Items</h3>
              <div class="total-number"><?php echo $low_stock_total; ?></div>
            </div>
            <div class="card">
              <h3>Recently Added Items</h3>
              <div class="total-number"><?php echo $recent_items_total; ?></div>
            </div>
          </section>

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

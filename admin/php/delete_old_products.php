<?php
include 'db_conn.php';

// Permanently delete products that were archived more than 30 days ago
$query = "DELETE FROM products WHERE deleted_at < NOW() - INTERVAL 30 DAY";
$conn->query($query);

$conn->close();
?>

<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Permanently delete the product from the database
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product successfully deleted!'); window.location.href = 'a_product_archive.php';</script>";
    } else {
        echo "<script>alert('Error deleting product');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

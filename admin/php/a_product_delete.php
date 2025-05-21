<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    // Soft delete: set the deleted_at timestamp
    $query = "UPDATE products SET deleted_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        echo "<script>alert('Product successfully deleted!'); window.location.href = 'a_product.php';</script>";
    } else {
        echo "<script>alert('Error deleting product');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    
    // Restore product by setting deleted_at to NULL
    $query = "UPDATE products SET deleted_at = NULL WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        echo "<script>alert('Product successfully restored!'); window.location.href = 'a_product_archive.php';</script>";
    } else {
        echo "<script>alert('Failed to restore product');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

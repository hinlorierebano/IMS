<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);

    // Delete the record from product_updates where id matches the provided ID
    $deleteQuery = "DELETE FROM product_updates WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        echo "<script>alert('Product update successfully deleted!'); window.location.href = 'a_product_new.php';</script>";
    } else {
        echo "<script>alert('Error deleting product update.');</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>

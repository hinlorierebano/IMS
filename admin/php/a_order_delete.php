<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];

    // Prepare the DELETE statement to remove the order from the database
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order successfully deleted!'); window.location.href = 'a_order.php';</script>";
    } else {
        echo "<script>alert('Error deleting order');</script>";
    }

    $stmt->close();
}
$conn->close();
?>

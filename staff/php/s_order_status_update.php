<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    // Fetch the order details (product_id and quantity)
    $orderQuery = "SELECT product_id, quantity, status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $orderResult = $stmt->get_result();
    $order = $orderResult->fetch_assoc();
    $product_id = $order['product_id'];
    $order_quantity = $order['quantity'];
    $current_status = $order['status'];

    // Only proceed if the status is actually being changed
    if ($current_status != $new_status) {
        // If changing status to Claimed, deduct the ordered quantity from the product stock
        if ($new_status == 'Claimed') {
            $updateProductQuery = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
            $stmt = $conn->prepare($updateProductQuery);
            $stmt->bind_param("ii", $order_quantity, $product_id);
            $stmt->execute();
        }

        // If changing status to Pending, restore the quantity back to the product stock
        if ($new_status == 'Pending' && $current_status == 'Claimed') {
            $restoreProductQuery = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
            $stmt = $conn->prepare($restoreProductQuery);
            $stmt->bind_param("ii", $order_quantity, $product_id);
            $stmt->execute();
        }

        // Now, update the order status
        $statusQuery = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($statusQuery);
        $stmt->bind_param("si", $new_status, $order_id);

        if ($stmt->execute()) {
            // Redirect with success message
            header("Location: s_order.php?message=Order status updated successfully");
            exit();
        } else {
            echo "Error updating status: " . $conn->error;
        }
    } else {
        // If status hasn't changed, just redirect without doing anything
        header("Location: s_order.php");
    }

    $stmt->close();
}
$conn->close();
?>

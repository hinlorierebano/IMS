<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the category ID from the POST request
    $category_id = $_POST['category_id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);

    if ($stmt->execute()) {
        echo "<script>alert('Category successfully deleted!'); window.location.href = 'a_category.php';</script>";
    } else {
        echo "<script>alert('Error deleting category');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the department ID from the POST request
    $department_id = $_POST['department_id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $department_id);

    if ($stmt->execute()) {
        echo "<script>alert('Department successfully deleted!'); window.location.href = 'a_department.php';</script>";
    } else {
        echo "<script>alert('Error deleting department');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

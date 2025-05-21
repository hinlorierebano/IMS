<?php
include 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user ID from the POST request
    $user_id = $_POST['user_id'];

    // Prepare and execute the delete statement
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id); // Corrected to use $user_id

    if ($stmt->execute()) {
        echo "<script>alert('User successfully deleted!'); window.location.href = 'a_umanagement.php';</script>";
    } else {
        echo "<script>alert('Error deleting user: " . $stmt->error . "');</script>"; // Display error details
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Invalid request method.');</script>";
}
?>

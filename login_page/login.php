<?php
session_start();
include "db_conn.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if (empty($user) || empty($pass)) {
        // Check if fields are empty
        echo "<script>alert('Username or Password required'); window.location.href='index.php';</script>";
        exit();
    }

    // Query to get user by username
    $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];  // Fetch the password from the database

        // Check if stored password is hashed
        if (password_needs_rehash($stored_password, PASSWORD_DEFAULT)) {
            // Password is not hashed, so directly compare the plaintext password
            if ($pass === $stored_password) {
                // Login successful, set session variables
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == '2') {
                    echo "<script>alert('Login successful'); window.location.href='../admin/php/a_landingpage.php';</script>";
                } elseif ($row['role'] == '3') {
                    echo "<script>alert('Login successful'); window.location.href='../staff/php/s_landingpage.php';</script>";
                } else {
                    echo "<script>alert('Invalid Username or Password'); window.location.href='index.php';</script>";
                }
            } else {
                echo "<script>alert('Incorrect Username or Password'); window.location.href='index.php';</script>";
            }
        } else {
            // Password is hashed, use password_verify
            if (password_verify($pass, $stored_password)) {
                // Login successful, set session variables
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == '2') {
                    echo "<script>alert('Login successful'); window.location.href='../admin/php/a_landingpage.php';</script>";
                } elseif ($row['role'] == '3') {
                    echo "<script>alert('Login successful'); window.location.href='../staff/php/s_landingpage.php';</script>";
                } else {
                    echo "<script>alert('Invalid Username or Password'); window.location.href='index.php';</script>";
                }
            } else {
                echo "<script>alert('Incorrect Username or Password'); window.location.href='index.php';</script>";
            }
        }
    } else {
        // User not found
        echo "<script>alert('Invalid Username or Password'); window.location.href='index.php';</script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

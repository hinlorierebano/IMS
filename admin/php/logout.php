<?php
session_start();

// Destroy session and clear session data
session_unset();
session_destroy();

// Prevent back button after logout by setting cache-control headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Correct path for the login page
header("Location: ../../login_page/index.php");
exit();
?>

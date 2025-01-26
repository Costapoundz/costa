<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect the user to the login page or homepage
header("Location: login.html"); // Replace 'login.php' with your desired location
exit;
?>

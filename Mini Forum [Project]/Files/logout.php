<?php
session_start(); // Start the session to access session variables

// Destroy the session to log the user out
session_unset();  // Remove all session variables
session_destroy(); // Destroy the session itself

// Redirect the user to the login page (or any page you want)
header("Location: login.php");
exit;
?>

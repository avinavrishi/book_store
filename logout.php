<?php
// Start the session to access session variables
session_start();

// Destroy the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect the user to the login page
header("Location: login.html");
exit();
?>

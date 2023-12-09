<?php
// Start the session (if it's not already started)
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: /hello/login.html");
exit;
?>

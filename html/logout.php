<?php
// Start session
session_start();

// Destroy the session to log the user out
session_unset();
session_destroy();

// Redirect to the home page
header("Location: home.html");
exit();
?>

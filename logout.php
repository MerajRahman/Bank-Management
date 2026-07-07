<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session

// Redirect back to the public home page
header("Location: home.php");
exit();
?>
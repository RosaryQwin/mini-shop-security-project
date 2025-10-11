<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect back to main shop
header("Location: ../index.php");
exit;
?>
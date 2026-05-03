<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Check if user ID is set in session
if (!isset($_SESSION['u_id'])) {
   header('Location: login.php');
   exit();
}

?>
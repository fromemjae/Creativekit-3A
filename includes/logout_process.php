<?php
// includes/logout_process.php
// ============================================================
// CUSTOMER LOGOUT PROCESSOR
// ============================================================

// 1. Activate session tracking so we can clear it
session_start();

// 2. Clear out all session data 
session_unset();
session_destroy();

// 3. Redirect the customer back to the home page
// (Note: '../index.php' jumps up one folder out of the includes directory)
header('Location: ../index.php');
exit();
?>
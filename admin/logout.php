<?php
// ============================================================
// ADMIN LOGOUT
// admin/logout.php
// ============================================================
session_start();
session_unset();
session_destroy();
header('Location: login.php');
exit;
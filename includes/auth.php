<?php
// includes/auth.php
// FIX: Removed absolute paths. Uses relative paths to prevent routing breaks.

function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php'); 
        exit;
    }
}

function requireSuperAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php'); 
        exit;
    }
    if ($_SESSION['admin_role'] !== 'superadmin') {
        header('Location: dashboard.php?error=unauthorized'); 
        exit;
    }
}

function isSuperAdmin() {
    return isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin';
}

function adminName() {
    return isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin';
}

function adminRole() {
    return isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : '';
}
?>
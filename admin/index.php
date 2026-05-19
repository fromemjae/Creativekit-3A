
<!-- INJECTED ADMIN CSS -->
<link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
<?php
// admin/index.php — redirect to dashboard or login
session_start();
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
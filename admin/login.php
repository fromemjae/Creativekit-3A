<?php
// admin/login.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// ALL BACKEND LOGIC HANDLED HERE BEFORE HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FIX: Removed the non-existent 'POST_email' key
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password fields.';
    } else {
        $query = "SELECT * FROM admins WHERE email = ? LIMIT 1";
        $stmt  = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin  = mysqli_fetch_assoc($result);

            if ($admin) {
                // Strictly tests against the hash defined in your creativekit3a.sql
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id']   = $admin['id'];
                    $_SESSION['admin_name'] = $admin['first_name'];
                    $_SESSION['admin_role'] = $admin['role'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Sign In | CreativeKit3A</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>

  <div class="login-container">
    <div class="login-logo">
      <div class="logo-icon"><i class="fas fa-shield-alt"></i></div>
      <h1>CreativeKit3A</h1>
    </div>

    <h2>Sign in to continue</h2>

    <?php if ($error): ?>
    <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-wrap">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="admin@example.com" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>
      </div>
      <button type="submit" class="btn-login">Secure Login</button>
    </form>
  </div>

</body>
</html>
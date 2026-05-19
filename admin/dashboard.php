<?php
// ============================================================
// ADMIN DASHBOARD
// admin/dashboard.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Get stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM messages WHERE is_read = 0"))['c'];
$total_admins   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM admins"))['c'];
/* TO THIS: */
$total_quotes   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM quote_requests"))['c'];
// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT o.*, u.first_name, u.last_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CreativeKit3A Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-brand">
    CreativeKit3A
    <span>Admin Panel</span>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>

    <div class="nav-section">Manage</div>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    
    <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
    
    
    <a href="admin_quotes.php"><i class="fas fa-comments-dollar"></i> Quotes
      <?php if ($total_messages > 0): ?>
        <span class="admin-elem-37"><?= $total_messages ?></span>
      <?php endif; ?>
    </a>

    <?php if (isSuperAdmin()): ?>
    <div class="nav-section">Super Admin</div>
    <a href="admins.php" class="superadmin-only"><i class="fas fa-user-shield"></i> Manage Admins</a>
    <?php endif; ?>

    <div class="nav-section">Account</div>
    <a href="/CREATIVEKIT3A-WEBSITE/" target="_blank"><i class="fas fa-globe"></i> View Website</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
  <div class="sidebar-footer">
    Logged in as<br>
    <strong class="admin-elem-38"><?= adminName() ?></strong>
    <span class="role-badge"><?= isSuperAdmin() ? 'Super Admin' : 'Admin' ?></span>
  </div>
</aside>

<main class="main">
  <div class="page-header">
    <h2>Dashboard <?= isset($_GET['error']) ? '<span class="admin-elem-39">⚠️ Unauthorized access</span>' : '' ?></h2>
    <p>Welcome back, <?= adminName() ?>! Here's what's happening today.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="fas fa-box"></i></div>
      <div class="stat-info"><h3><?= $total_products ?></h3><p>Total Products</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green"><i class="fas fa-users"></i></div>
      <div class="stat-info"><h3><?= $total_users ?></h3><p>Registered Users</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange"><i class="fas fa-shopping-bag"></i></div>
      <div class="stat-info"><h3><?= $total_orders ?></h3><p>Total Orders</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon red"><i class="fas fa-envelope"></i></div>
      <div class="stat-info"><h3><?= $total_messages ?></h3><p>Unread Messages</p></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon red"><i class="fas fa-comments-dollar"></i></div>
      <div class="stat-info"><h3><?= $total_quotes ?></h3><p>Total Quotes</p></div>
    </div>
    <?php if (isSuperAdmin()): ?>
    <div class="stat-card">
      <div class="stat-icon purple"><i class="fas fa-user-shield"></i></div>
      <div class="stat-info"><h3><?= $total_admins ?></h3><p>Total Admins</p></div>
    </div>
    <?php endif; ?>
  </div>
  

  <div class="card">
    <div class="card-header">
      <h3>Recent Orders</h3>
      <a href="orders.php">View All <i class="fas fa-arrow-right"></i></a>
    </div>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
        <tr>
          <td>#<?= $order['id'] ?></td>
          <td><?= $order['first_name'] ? htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) : 'Guest' ?></td>
          <td>₱<?= number_format($order['total_amount'], 2) ?></td>
          <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($recent_orders) === 0): ?>
        <tr><td colspan="5" class="admin-elem-40">No orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

</body>
</html>
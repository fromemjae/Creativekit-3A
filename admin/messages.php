<?php
// ============================================================
// ADMIN MESSAGES / INQUIRIES
// admin/messages.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// DELETE message
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM messages WHERE id = $id");
    $success = 'Message deleted.';
}

// MARK as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE id = $id");
}

// VIEW single message
$view = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE id = $id");
    $view = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM messages WHERE id = $id"));
}

$messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC");
$unread   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM messages WHERE is_read = 0"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Messages | CreativeKit3A Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-brand">CreativeKit3A <span>Admin Panel</span></div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <div class="nav-section">Manage</div>
    <a href="products.php"><i class="fas fa-box"></i> Products</a>
    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
    <a href="admin_quotes.php"><i class="fas fa-comments-dollar"></i> Quotes
      <?php if ($unread > 0): ?><span class="unread-badge"><?= $unread ?></span><?php endif; ?>
    </a>
    <?php if (isSuperAdmin()): ?>
    <div class="nav-section">Super Admin</div>
    <a href="admins.php"><i class="fas fa-user-shield"></i> Manage Admins</a>
    <?php endif; ?>
    <div class="nav-section">Account</div>
    <a href="/CREATIVEKIT3A-WEBSITE/" target="_blank"><i class="fas fa-globe"></i> View Website</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
  <div class="sidebar-footer">Logged in as <strong class="admin-elem-p2-1"><?= adminName() ?></strong></div>
</aside>

<main class="main">
  <div class="page-header">
    <h2>Messages & Inquiries</h2>
  </div>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <!-- VIEW SINGLE MESSAGE -->
  <?php if ($view): ?>
  <div class="msg-view">
    <h3><?= htmlspecialchars($view['subject'] ?: '(No Subject)') ?></h3>
    <div class="meta">
      <span><i class="fas fa-user"></i> <?= htmlspecialchars($view['name']) ?></span>
      <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($view['email']) ?></span>
      <?php if ($view['phone']): ?><span><i class="fas fa-phone"></i> <?= htmlspecialchars($view['phone']) ?></span><?php endif; ?>
      <span><i class="fas fa-clock"></i> <?= date('M d, Y h:i A', strtotime($view['created_at'])) ?></span>
    </div>
    <div class="body"><?= htmlspecialchars($view['message']) ?></div>
    <div class="admin-elem-p2-2">
      <a href="mailto:<?= htmlspecialchars($view['email']) ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
      <a href="messages.php" class="btn admin-elem-p2-3">← Back to All</a>
      <a href="?delete=<?= $view['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i> Delete</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- MESSAGES TABLE -->
  <div class="card">
    <div class="card-header">
      <h3>All Messages (<?= mysqli_num_rows($messages) ?>) — <?= $unread ?> unread</h3>
    </div>
    <table>
      <thead>
        <tr><th>From</th><th>Subject</th><th>Preview</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php
        mysqli_data_seek($messages, 0);
        while ($m = mysqli_fetch_assoc($messages)):
        ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread' ?>">
          <td>
            <div class="admin-elem-p2-4"><?= htmlspecialchars($m['name']) ?></div>
            <div class="admin-elem-p2-5"><?= htmlspecialchars($m['email']) ?></div>
          </td>
          <td><?= htmlspecialchars($m['subject'] ?: '(No Subject)') ?></td>
          <td class="msg-preview"><?= htmlspecialchars($m['message']) ?></td>
          <td><span class="badge badge-<?= $m['is_read'] ? 'read' : 'unread' ?>"><?= $m['is_read'] ? 'Read' : 'Unread' ?></span></td>
          <td class="admin-elem-p2-6"><?= date('M d, Y', strtotime($m['created_at'])) ?></td>
          <td class="admin-elem-p2-6">
            <a href="?view=<?= $m['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
            <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($messages) === 0): ?>
        <tr><td colspan="6" class="admin-elem-p2-7">No messages yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
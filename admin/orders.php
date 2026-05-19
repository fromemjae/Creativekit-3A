<?php
// ============================================================
// ADMIN ORDERS
// admin/orders.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// UPDATE order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $status   = $_POST['status'] ?? 'pending';
    $allowed  = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
        $success = 'Order status updated.';
    }
}

// DELETE order
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM orders WHERE id = $id");
    $success = 'Order deleted.';
}

// VIEW single order
$view_order = $order_items = null;
if (isset($_GET['view'])) {
    $id = (int)$_GET['view'];
    $view_order = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT o.*, u.first_name, u.last_name, u.email, u.phone
         FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = $id"));
    $order_items = mysqli_query($conn,
        "SELECT oi.*, p.name as product_name, p.image
         FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $id");
}

// Status filter
$status_filter = $_GET['status'] ?? '';
$where = $status_filter
    ? "WHERE o.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'"
    : '';
$orders = mysqli_query($conn,
    "SELECT o.*, u.first_name, u.last_name
     FROM orders o LEFT JOIN users u ON o.user_id = u.id
     $where ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders | CreativeKit3A Admin</title>
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
    <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="users.php"><i class="fas fa-users"></i> Users</a>
    <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
    <a href="admin_quotes.php"><i class="fas fa-comments-dollar"></i> Quotes</a>
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
    <h2>Orders</h2>
    <div class="filters">
      <a href="orders.php"           class="<?= !$status_filter              ? 'active':'' ?>">All</a>
      <a href="?status=pending"      class="<?= $status_filter==='pending'    ? 'active':'' ?>">Pending</a>
      <a href="?status=processing"   class="<?= $status_filter==='processing' ? 'active':'' ?>">Processing</a>
      <a href="?status=shipped"      class="<?= $status_filter==='shipped'    ? 'active':'' ?>">Shipped</a>
      <a href="?status=delivered"    class="<?= $status_filter==='delivered'  ? 'active':'' ?>">Delivered</a>
      <a href="?status=cancelled"    class="<?= $status_filter==='cancelled'  ? 'active':'' ?>">Cancelled</a>
    </div>
  </div>

  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

  <!-- ORDER DETAIL -->
  <?php if ($view_order): ?>
  <div class="order-detail">
    <h3>Order #<?= $view_order['id'] ?> Details</h3>
    <div class="detail-grid">
      <div class="detail-item">
        <label>Customer</label>
        <span><?= $view_order['first_name'] ? htmlspecialchars($view_order['first_name'].' '.$view_order['last_name']) : 'Guest' ?></span>
      </div>
      <div class="detail-item"><label>Email</label><span><?= htmlspecialchars($view_order['email'] ?? '—') ?></span></div>
      <div class="detail-item"><label>Phone</label><span><?= htmlspecialchars($view_order['phone'] ?? '—') ?></span></div>
      <div class="detail-item">
        <label>Total Amount</label>
        <span class="admin-elem-p2-8">₱<?= number_format($view_order['total_amount'],2) ?></span>
      </div>
      <div class="detail-item"><label>Date</label><span><?= date('M d, Y h:i A', strtotime($view_order['created_at'])) ?></span></div>
      <div class="detail-item">
        <label>Update Status</label>
        <form method="POST" class="status-form">
          <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">
          <select name="status">
            <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
            <option value="<?= $s ?>" <?= $view_order['status']===$s ? 'selected':'' ?>><?= ucfirst($s) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save</button>
        </form>
      </div>
    </div>

    <?php if ($view_order['notes']): ?>
    <p class="admin-elem-p2-9"><strong>Notes:</strong> <?= htmlspecialchars($view_order['notes']) ?></p>
    <?php endif; ?>

    <h4 class="admin-elem-p2-10">Items Ordered</h4>
    <?php if ($order_items && mysqli_num_rows($order_items) > 0): ?>
      <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
      <div class="item-row">
        <?php if ($item['image']): ?>
          <img src="<?= htmlspecialchars($item['image']) ?>" class="item-img" alt="">
        <?php endif; ?>
        <div class="item-name"><?= htmlspecialchars($item['product_name'] ?? 'Deleted Product') ?></div>
        <div class="item-qty">x<?= $item['quantity'] ?> &nbsp; ₱<?= number_format($item['price'],2) ?></div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="admin-elem-p2-11">No items recorded for this order.</p>
    <?php endif; ?>

    <div class="admin-elem-p2-12">
      <a href="orders.php" class="btn admin-elem-p2-3"><i class="fas fa-arrow-left"></i> Back</a>
      <a href="?delete=<?= $view_order['id'] ?>" class="btn btn-danger"
         onclick="return confirm('Delete order #<?= $view_order['id'] ?>?')"><i class="fas fa-trash"></i> Delete Order</a>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDERS TABLE -->
  <div class="card">
    <div class="card-header"><h3>All Orders (<?= mysqli_num_rows($orders) ?>)</h3></div>
    <table>
      <thead>
        <tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php while ($o = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td><strong>#<?= $o['id'] ?></strong></td>
          <td><?= $o['first_name'] ? htmlspecialchars($o['first_name'].' '.$o['last_name']) : 'Guest' ?></td>
          <td>₱<?= number_format($o['total_amount'],2) ?></td>
          <td><span class="badge badge-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
          <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
          <td class="admin-elem-p2-6">
            <a href="?view=<?= $o['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> View</a>
            <a href="?delete=<?= $o['id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete order #<?= $o['id'] ?>?')"><i class="fas fa-trash"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($orders) === 0): ?>
        <tr><td colspan="6" class="admin-elem-p2-7">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
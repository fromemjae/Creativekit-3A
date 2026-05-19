<?php 
$pageTitle = "Track Your Order | CreativeKit 3A";
include 'includes/header.php'; 
require_once 'includes/db.php';

$orderFound = false;
$status = "";
$errorMsg = "";

// COMMENT: Process tracking search request when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id']) && isset($_GET['email'])) {
    $orderId = intval($_GET['order_id']);
    $email = trim($_GET['email']);

    if ($orderId > 0 && !empty($email)) {
        /* COMMENT: Query looks up the order status matching both the Order ID and customer Email */
        $query = "SELECT o.id, o.status, o.total_amount, o.created_at, q.email AS quote_email 
                  FROM orders o
                  LEFT JOIN quote_requests q ON o.quote_id = q.id
                  WHERE o.id = ? AND q.email = ? LIMIT 1";
                  
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'is', $orderId, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $order = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if ($order) {
                $orderFound = true;
                $status = strtolower($order['status']); // pending, processing, shipped, delivered, cancelled
            } else {
                $errorMsg = "⚠️ No matching order found for that Order ID and Email combination.";
            }
        }
    } else {
        $errorMsg = "⚠️ Please fill in both the Order ID and Email fields.";
    }
}
?>

<main class="tracker-main">
  <div class="container tracker-container">
    
    <div class="tracker-header">
      <h2>Track Your Corporate Order</h2>
      <p>Enter your Order Number and the Email used during your quote submission to check fulfillment milestones.</p>
    </div>

    <div class="tracker-card">
      <form method="GET" action="track_order.php" class="tracker-form">
        <div class="tracker-group">
          <label class="tracker-label">Order Number / ID</label>
          <input type="number" name="order_id" class="tracker-input" required placeholder="e.g. 1005" value="<?= isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '' ?>">
        </div>
        <div class="tracker-group">
          <label class="tracker-label">Email Address</label>
          <input type="email" name="email" class="tracker-input" required placeholder="client@company.com" value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>">
        </div>
        <button type="submit" class="tracker-btn">Search Order Status</button>
      </form>
    </div>

    <?php if (!empty($errorMsg)): ?>
      <div class="tracker-alert error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <?php if ($orderFound): ?>
      <div class="tracker-card timeline-card">
        <h3>Order Status: <span class="status-text"><?= ucfirst($status) ?></span></h3>
        
        <div class="tracker-timeline">
          <div class="timeline-step <?= ($status === 'processing' || $status === 'shipped' || $status === 'delivered') ? 'completed' : '' ?>">
            <div class="step-icon"><i class="fas fa-box-open"></i></div>
            <p class="step-title">Processing</p>
            <span class="step-desc">Giveaways in production</span>
          </div>

          <div class="timeline-step <?= ($status === 'shipped' || $status === 'delivered') ? 'completed' : ($status === 'processing' ? 'active' : '') ?>">
            <div class="step-icon"><i class="fas fa-truck"></i></div>
            <p class="step-title">Shipped</p>
            <span class="step-desc">In transit / Out for handover</span>
          </div>

          <div class="timeline-step <?= ($status === 'delivered') ? 'completed' : ($status === 'shipped' ? 'active' : '') ?>">
            <div class="step-icon"><i class="fas fa-check-circle"></i></div>
            <p class="step-title">Delivered</p>
            <span class="step-desc">Handover complete</span>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php include 'includes/footer.php'; ?>
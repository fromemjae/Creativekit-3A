<?php
// ============================================================
// SIMPLIFIED ADMIN CONTROL DESK & STRIPE LINK MANAGEMENT PANEL
// admin/admin_quotes.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php'; // Siguraduhing tama ang path patungo sa iyong database connection

// Temporary success/error tracking variables for your local presentation
$msgSuccess = "";
$msgError = "";

// 1. PROCESS ADMIN ACTION: APPROVE WITH STRIPE LINK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_approve'])) {
    
    $quoteId = isset($_POST['quote_id']) ? (int)$_POST['quote_id'] : 0;
    $calculatedPrice = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0.00;
    $manuallyPastedLink = isset($_POST['payment_url']) ? trim($_POST['payment_url']) : '';

    if (empty($manuallyPastedLink) || $calculatedPrice <= 0) {
        $msgError = "❌ Please enter a valid total amount and paste the Stripe Payment Link.";
    } else {
        // GINAMIT ANG TAMA MONG COLUMN NAME: 'status' imbes na 'quote_status'
        /* */
        $updateSql = "UPDATE quote_requests SET total_amount = ?, payment_url = ?, status = 'approved' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateSql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'dsi', $calculatedPrice, $manuallyPastedLink, $quoteId);
            if (mysqli_stmt_execute($stmt)) {
                $msgSuccess = "🎉 Quote #$quoteId successfully approved! Stripe link sent to user portal.";
            } else {
                $msgError = "❌ Database update failure.";
            }
            mysqli_stmt_close($stmt);
        }
        /* */
    }
}

// 2. PROCESS ADMIN ACTION: MANUAL OVERRIDE MARK AS PAID
if (isset($_GET['mark_paid'])) {
    $quoteId = (int)$_GET['mark_paid'];
    
    // Admin checks their Stripe Phone App or Browser Dashboard, verifies money arrived, and triggers this override:
    /* */
    $paidSql = "UPDATE quote_requests SET payment_status = 'paid' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $paidSql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $quoteId);
        if (mysqli_stmt_execute($stmt)) {
            $msgSuccess = "💰 Quote #$quoteId updated to PAID status successfully!";
            
            // ============================================================
            // CHANGED HERE: Fetch quote details first to capture the total_amount and user_id for the new order record.
            // ============================================================
            $fetchSql = "SELECT * FROM quote_requests WHERE id = ? LIMIT 1";
            $fetchStmt = mysqli_prepare($conn, $fetchSql);
            if ($fetchStmt) {
                mysqli_stmt_bind_param($fetchStmt, 'i', $quoteId);
                mysqli_stmt_execute($fetchStmt);
                $result = mysqli_stmt_get_result($fetchStmt);
                $quoteData = mysqli_fetch_assoc($result);
                mysqli_stmt_close($fetchStmt);
                
                if ($quoteData) {
                    $customerId = isset($quoteData['user_id']) ? $quoteData['user_id'] : null;
                    $calculatedPrice = $quoteData['total_amount'];
                    
                    // ============================================================
                    // CHANGED HERE: Automatically migrate it into the master orders table for fulfillment tracking.
                    // ============================================================
                    $insertOrderSql = "INSERT INTO orders (user_id, quote_id, total_amount, status, created_at) VALUES (?, ?, ?, 'processing', NOW())";
                    $stmtOrder = mysqli_prepare($conn, $insertOrderSql);
                    if ($stmtOrder) {
                        mysqli_stmt_bind_param($stmtOrder, 'iid', $customerId, $quoteId, $calculatedPrice);
                        mysqli_stmt_execute($stmtOrder);
                        mysqli_stmt_close($stmtOrder);
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    /* */
}

// Fetch all logged quotes out of your database to draw on screen
$allQuotesQuery = mysqli_query($conn, "SELECT * FROM quote_requests ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel | Corporate Quote Desk</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="panel-container">
    <div class="admin-elem-1">
      <a href="dashboard.php" class="admin-elem-2">
        <i class="fas fa-arrow-left"></i> Return to Main Admin Dashboard
      </a>
    </div>
  <h2><i class="fas fa-tasks admin-elem-3"></i> Corporate Quote Management Panel (Stripe Flow)</h2>
  <p class="admin-elem-4">Review customer custom specifications, calculate costs, paste manual Stripe Test Links, and handle final order auditing overrides.</p>

  <?php if (!empty($msgSuccess)): ?><div class="admin-elem-5"><?= $msgSuccess ?></div><?php endif; ?>
  <?php if (!empty($msgError)): ?><div class="admin-elem-6"><?= $msgError ?></div><?php endif; ?>

  <table class="quote-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer &amp; Product Details</th>
        <th>Customization Surcharge Notes</th>
        <th>Pricing &amp; Stripe Setup Link</th>
        <th>Status Tracking</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($allQuotesQuery && mysqli_num_rows($allQuotesQuery) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($allQuotesQuery)): ?>
          <?php 
             // Pagtitiyak na laging may default values kahit blangko ang database
             $dbStatus = isset($row['status']) ? $row['status'] : 'pending';
             $dbPaymentStatus = isset($row['payment_status']) ? $row['payment_status'] : 'unpaid';
          ?>
          <tr>
            <td><strong>#<?= $row['id'] ?></strong></td>
            <td>
              <strong>Name:</strong> <?= htmlspecialchars($row['full_name'] ?? $row['customer_name'] ?? 'N/A') ?><br>
              <strong>Email:</strong> <?= htmlspecialchars($row['email'] ?? $row['customer_email'] ?? 'N/A') ?><br>
              <strong>Product:</strong> <span class="admin-elem-7"><?= htmlspecialchars($row['item_type'] ?? $row['product_name'] ?? 'N/A') ?> (<?= htmlspecialchars($row['quantity'] ?? '0') ?> units)</span>
            </td>
            <td>
              <small class="admin-elem-8"><?= htmlspecialchars($row['custom_notes'] ?? $row['customization_request'] ?? 'No custom parameters specified.') ?></small>
            </td>
            <td>
              <?php if ($dbStatus === 'pending'): ?>
                <form method="POST" action="">
                  <input type="hidden" name="quote_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="action_approve" value="1">
                  
                  <input type="number" name="total_amount" step="0.01" placeholder="Enter Total Amount (₱)" class="input-field" required>
                  <input type="url" name="payment_url" placeholder="Paste Stripe Link URL here..." class="input-field" required>
                  
                  <button type="submit" class="btn-submit"><i class="fas fa-check"></i> Approve &amp; Send Link</button>
                </form>
              <?php else: ?>
                <strong class="admin-elem-9">Total: ₱<?= number_format((float)($row['total_amount'] ?? 0), 2) ?></strong><br>
                <?php if (!empty($row['payment_url'])): ?>
                    <a href="<?= htmlspecialchars($row['payment_url']) ?>" target="_blank" style="color: #ff6b00; font-size: 0.82rem; word-break: break-all; font-weight: 600;"><i class="fas fa-external-link-alt"></i> View Stripe Checkout Page</a>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td>
              <span class="status-badge status-<?= htmlspecialchars($dbStatus) ?>"><?= htmlspecialchars($dbStatus) ?></span>
              <br><br>
              <strong>Payment:</strong> 
              <span style="font-weight: 700; color: <?= $dbPaymentStatus === 'paid' ? '#10b981' : '#dc2626' ?>;">
                <?= strtoupper(htmlspecialchars($dbPaymentStatus)) ?>
              </span>
              
              <?php if ($dbStatus === 'approved' && $dbPaymentStatus === 'unpaid'): ?>
                <br><br>
                <a href="?mark_paid=<?= $row['id'] ?>" class="btn-paid" onclick="return confirm('Verify that payment has cleared inside your external Stripe window dashboard before continuing?');"><i class="fas fa-cash-register"></i> Mark as Paid</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="admin-elem-10">No corporate quote records found under database arrays.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
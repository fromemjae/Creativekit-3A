<?php
// ============================================================
// ADMINS MANAGEMENT PANEL
// admin/admins.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Protect the page - only admins allowed
requireAdmin();

$feedbackMsg = "";

// ============================================================
// LOUD UPDATE COMMENTS: SECURE SUPER-ADMIN DELETION HANDLER
// Validates that only 'superadmin' roles can execute the drop statement.
// Fixed the self-deletion check to read from your active login session keys.
// ============================================================
/* */
if (isset($_GET['delete_id'])) {
    if (isSuperAdmin()) {
        $deleteId = intval($_GET['delete_id']);
        $currentLoggedInAdminId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
        
        // Anti-Lockout Safety Lock: Prevent a Super-Admin from deleting themselves
        if ($deleteId === $currentLoggedInAdminId) {
            // FIXED: Combined duplicate class attributes and cleaned unescaped double quotes
            $feedbackMsg = "<div class='lm-msg lm-msg--error admin-elem-35'>⚠️ Security Exception: You cannot delete your own active administrative session!</div>";
        } else {
            $delQuery = "DELETE FROM admins WHERE id = ?";
            $stmt = mysqli_prepare($conn, $delQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $deleteId);
                if (mysqli_stmt_execute($stmt)) {
                    // FIXED: Combined duplicate class attributes and cleaned unescaped double quotes
                    $feedbackMsg = "<div class='lm-msg lm-msg--success admin-elem-36'>🎉 Success: Administrator account has been permanently removed from the system database.</div>";
                } else {
                    // FIXED: Combined duplicate class attributes and cleaned unescaped double quotes
                    $feedbackMsg = "<div class='lm-msg lm-msg--error admin-elem-35'>⚠️ Database Error: Failed to execute removal routine.</div>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        // FIXED: Combined duplicate class attributes and cleaned unescaped double quotes
        $feedbackMsg = "<div class='lm-msg lm-msg--error admin-elem-35'>⚠️ Access Denied: Regular administrators do not have authorization to remove accounts. Only a Super-Admin can perform this.</div>";
    }
}

// Fetch all recorded administrator rows
$all_admins = mysqli_query($conn, "SELECT id, first_name, last_name, email, role, status, created_at FROM admins ORDER BY id DESC");
/* */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Administrators | CreativeKit 3A</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../style.css">

<link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body class="admin-elem-11">

  <div class="container admin-elem-12">
    
    <div class="admin-elem-13">
      <a href="dashboard.php" class="admin-elem-14">
        <i class="fas fa-arrow-left"></i> Back to Main Dashboard
      </a>
    </div>

    <div class="admin-elem-15">
      <h2 class="admin-elem-16">Manage System Administrators</h2>
      <p class="admin-elem-17">Review, track, and audit all corporate dashboard management profiles saved inside XAMPP.</p>
    </div>

    <?php if (!empty($feedbackMsg)) echo $feedbackMsg; ?>

    <div class="card admin-elem-18">
      <div class="card-header admin-elem-19">
        <h3 class="admin-elem-20">
          <i class="fas fa-users-shield admin-elem-21"></i> Registered Administrator Profiles
        </h3>
      </div>
      
      <div class="admin-elem-22">
        <table class="admin-elem-23">
          <thead>
            <tr class="admin-elem-24">
              <th class="admin-elem-25">Admin ID</th>
              <th class="admin-elem-25">Full Name</th>
              <th class="admin-elem-25">Email Address</th>
              <th class="admin-elem-25">Account Privilege</th>
              <th class="admin-elem-25">Status</th>
              <th class="admin-elem-25">Date Created</th>
              
              <th class="admin-elem-26">Actions</th>
              
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($all_admins) > 0): ?>
              <?php while ($admin = mysqli_fetch_assoc($all_admins)): ?>
                <tr class="admin-elem-27">
                  <td class="admin-elem-28">
                    #<?php echo $admin['id']; ?>
                  </td>
                  <td class="admin-elem-29">
                    <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>
                  </td>
                  <td class="admin-elem-30">
                    <?php echo htmlspecialchars($admin['email']); ?>
                  </td>
                  <td class="admin-elem-25">
                    <span style="padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: <?php echo ($admin['role'] === 'superadmin') ? '#fff0e5; color: var(--orange-primary);' : '#eef2f7; color: #475569;'; ?>">
                      <?php echo htmlspecialchars($admin['role']); ?>
                    </span>
                  </td>
                  <td class="admin-elem-25">
                    <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; color: <?php echo ($admin['status'] === 'active') ? '#16a34a;' : '#dc2626;'; ?>">
                      <span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo ($admin['status'] === 'active') ? '#16a34a;' : '#dc2626;'; ?>"></span>
                      <?php echo ucfirst(htmlspecialchars($admin['status'])); ?>
                    </span>
                  </td>
                  <td class="admin-elem-30">
                    <?php echo date('M d, Y', strtotime($admin['created_at'])); ?>
                  </td>

                  
                  <td class="admin-elem-31">
                    <?php if (isSuperAdmin()): ?>
                      <?php 
                        $currentLoggedInId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
                        if (intval($admin['id']) !== $currentLoggedInId): 
                      ?>
                        <a href="admins.php?delete_id=<?php echo $admin['id']; ?>" 
                           onclick="return confirm('Are you absolutely certain you want to permanently revoke all admin privileges for <?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?>? This action drops their account data immediately.');"
                           style="color: #dc2626; background: #fee2e2; padding: 6px 12px; border-radius: 4px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 0.8rem; border: 1px solid #fca5a5; transition: all 0.2s;">
                          <i class="fas fa-user-minus"></i> Remove
                        </a>
                      <?php else: ?>
                        <span class="admin-elem-32"><i class="fas fa-check-circle"></i> You (Active)</span>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="admin-elem-33"><i class="fas fa-lock"></i> Restricted</span>
                    <?php endif; ?>
                  </td>
                 
                  </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="admin-elem-34">
                  No active administrator registry records located inside MySQL database.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</body>
</html>
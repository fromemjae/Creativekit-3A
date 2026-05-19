<?php

if(session_status() === PHP_SESSION_NONE){
  session_set_cookie_params([
    'path' => '/',
    'secure' => false,
    'httponly' => 'true',
    'samesite' => 'Lax',

  ]);
  session_start();

}

if (!isset($pageTitle)) {
    $pageTitle = "Corporate Giveaways | CreativeKit3A";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Customized Silicone Wristbands &amp; Baller IDs Philippines | Pinoyballers">
  <title><?php echo $pageTitle; ?></title>


  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  
</head>

<body>

  
  <!-- ============================================================
       TOP BAR
       ============================================================ -->
       
  <div class="top-bar">
    <div class="container" style="height: 41px;">

      
      <?php if (isset($_SESSION['admin_id'])): ?>
      <div class="admin-auth-group">
         <span onclick="toggleSidebarMenu()" class="admin-status-span">
           <i class="fas fa-user-shield"></i><?= isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin' ? 'Super Admin' : 'Admin'; ?>: Control Mode Active <i class="fas fa-caret-down" style="font-size: 0.75rem;"></i>
         </span>
         <a href="includes/logout_process.php" class="logout-btn">Sign Out
         </a>
      </div>

  <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'customer'): ?>
      <div  class="customer-auth-group">
      <span onclick="toggleSidebarMenu()" class="customer-status-span">
          <i class="fas fa-user"></i> Hi, <?= htmlspecialchars($_SESSION['user_name']) ?> <i class="fas fa-caret-down" style="font-size: 0.75rem;"></i>
      </span>
      <a href="includes/logout_process.php" class="logout-btn">Sign Out
      </a>
      </div>

  <?php else: ?>
    <div class="guest-auth-group">
      <button class="login-btn" onclick="openModal('login-modal')">Sign In</button>
      <button class="register-btn" onclick="openModal('register-modal');">Register</button>
    </div>
  <?php endif; ?>
  <div class="top-bar-contact">
        <a href="mailto:salesandmarketing@creativekit3a.com"><i class="fas fa-envelope icon"></i> salesandmarketing@creativekit3a.com</a>
        <a href="tel:+639177142774"><i class="fas fa-mobile-alt icon"></i> +63 917 714 2774</a>
        <a href="tel:+639339927410"><i class="fas fa-phone icon"></i> +63 933 992 7410</a>
      </div>
</li>
      
      </div>
    </div>

  
  <!-- ============================================================
       MAIN NAVBAR
       ============================================================ -->
  <nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="navbar-inner">

      <!-- Hamburger (mobile) -->
      <button class="hamburger" aria-label="Open menu" aria-expanded="false" aria-controls="sidenav">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <!-- Logo -->
      <!-- AFTER -->
<a href="index.php" class="navbar-logo">
  <img src="2.png" alt="CreativeKit3A Logo" class="logo-img">
  <span class="logo-text">CreativeKit3A</span>
</a>

      <!-- Search -->
      <div class="navbar-search" role="search">
        <input type="search" placeholder="What are you looking for?" aria-label="Search products">
        <i class="fas fa-search search-icon" aria-hidden="true"></i>
      </div>

      <!-- Right side -->
      <div class="navbar-right">
        <nav class="navbar-links" aria-label="Secondary navigation">
          <a href="about.php">About</a>
          <a href="accessories.php">Category</a>
          <a href="includes/footer.php">Contact Us</a>
        </nav>

        <button class="rfq-btn" aria-label="Request a Quote" onclick="window.location.href='quote.php'">Request a Quote</button>
       
        
        <a href="/cart/" class="navbar-cart" aria-label="Shopping cart">
          <i class="fas fa-shopping-cart" aria-hidden="true"></i>
          <span>Cart</span>
          <span class="cart-count" aria-live="polite">0</span>
        </a>

      </div>

    </div>
  </nav>

  <!-- ============================================================
       MEGA MENU BAR
       ============================================================ -->
  <div class="mega-menu-bar" role="navigation" aria-label="Category navigation">
    <ul class="mega-menu-nav">

      <?php
      require_once __DIR__ . '/db.php';
      $parentCatsQuery = mysqli_query($conn, "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY id ASC");
      while ($parent = mysqli_fetch_assoc($parentCatsQuery)):
      ?>
        <li class="mega-menu-item">
          <a href="accessories.php?cat=<?= $parent['id'] ?>"><?= htmlspecialchars($parent['name']) ?> <i class="fas fa-chevron-down chevron" aria-hidden="true"></i></a>
          <div class="mega-dropdown">
            <?php
            $subCatsQuery = mysqli_query($conn, "SELECT * FROM categories WHERE parent_id = {$parent['id']} ORDER BY name ASC");
            while ($sub = mysqli_fetch_assoc($subCatsQuery)):
            ?>
              <div class="mega-dropdown-col">
                <h4><?= htmlspecialchars($sub['name']) ?></h4>
                <?php
                $childCatsQuery = mysqli_query($conn, "SELECT * FROM categories WHERE parent_id = {$sub['id']} ORDER BY name ASC");
                while ($child = mysqli_fetch_assoc($childCatsQuery)):
                ?>
                  <a href="accessories.php?cat=<?= $child['id'] ?>"><?= htmlspecialchars($child['name']) ?></a>
                <?php endwhile; ?>
              </div>
            <?php endwhile; ?>
          </div>
        </li>
      <?php endwhile; ?>
      

    </ul>
  </div>

  <!-- Mobile Sidenav Overlay -->
  <div class="sidenav-overlay" aria-hidden="true"></div>

  <!-- Mobile Sidenav -->
  <aside class="sidenav" id="sidenav" role="complementary" aria-label="Dynamic Sidenav Layout Container">
    <div class="sidenav-header">
      <div>
        <?php if (isset($_SESSION['admin_id'])): ?>
          <span class="sidenav-admin-lbl"><i class="fas fa-user-shield"></i> <?= isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin' ? 'Super Admin Account' : 'Admin Account'; ?></span>
          <span class="sidenav-admin-name"><?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : (isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'System Administrator'); ?></span>
        <?php elseif (isset($_SESSION['user_id'])): ?>
          <span class="sidenav-cust-lbl">Welcome, Customer</span>
          <span class="sidenav-cust-name"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></span>
        <?php else: ?>
          <span class="sidenav-guest-name">CreativeKit3A Menu</span>
        <?php endif; ?>
      </div>
      <button class="sidenav-close" aria-label="Close menu" onclick="toggleSidebarMenu()">&times;</button>
    </div>
    
    <div class="sidenav-body">
      
      <?php if (isset($_SESSION['admin_id'])): ?>
        <p class="sidenav-section-title sec-exec"></i> Executive Controls</p>
        <a href="admin/dashboard.php" class="sidenav-admin-dash"><i class="fas fa-chart-line"></i> Open Admin Dashboard</a>
      <?php endif; ?>

      <p class="sidenav-section-title sec-cat">Product Categories</p>
      <div class="sidenav-cat-box">
        <?php
        if ($parentCatsQuery) {
            mysqli_data_seek($parentCatsQuery, 0);
            while ($parent = mysqli_fetch_assoc($parentCatsQuery)):
            ?>
              <a href="accessories.php?cat=<?= $parent['slug'] ?>" class="sidenav-cat-link"><i class="fas fa-angle-right" style="color: #ff6b00; margin-right: 5px;"></i> <?= htmlspecialchars($parent['name']) ?></a>
            <?php 
            endwhile;
        }
        ?>
      </div>

      <p class="sidenav-section-title sec-nav">Navigation Menu</p>
      <a href="about.php" class="sidenav-nav-link"><i class="fas fa-info-circle" style="width: 16px; color: #64748b;"></i> About Us</a>
      <a href="accessories.php" class="sidenav-nav-link"><i class="fas fa-th-large" style="width: 16px; color: #64748b;"></i> View Full Category</a>
      <a href="/contact-us/" class="sidenav-nav-link"><i class="fas fa-paper-plane" style="width: 16px; color: #64748b;"></i> Contact Us</a>

      <p class="sidenav-section-title sec-help">Corporate Help Desk</p>
      <div class="sidenav-help-box">
        <div class="sidenav-help-row"><i class="fas fa-envelope" style="color: #ff6b00; width:14px;"></i> <a href="mailto:salesandmarketing@creativekit3a.com" style="color: inherit; text-decoration: none;">salesandmarketing@creativekit3a.com</a></div>
        <div class="sidenav-help-row"><i class="fas fa-mobile-alt" style="color: #ff6b00; width:14px;"></i> <a href="tel:+639177142774" style="color: inherit; text-decoration: none;">+63 917 714 2774</a></div>
        <div class="sidenav-help-row"><i class="fas fa-phone" style="color: #ff6b00; width:14px;"></i> <a href="tel:+639339927410" style="color: inherit; text-decoration: none;">+63 933 992 7410</a></div>
      </div>

      <?php if (isset($_SESSION['admin_id']) || isset($_SESSION['user_id'])): ?>
        <a href="includes/logout_process.php" class="sidenav-logout-link"><i class="fas fa-sign-out-alt"></i> Account Sign Out</a>
      <?php endif; ?>
    </div>
  </aside>

  <script>
    function toggleSidebarMenu() {
      const sidebar = document.getElementById('sidenav');
      const overlay = document.querySelector('.sidenav-overlay');
      if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      }
    }
  </script>
  
  

<div id="forgot-modal" class="forgot-form">
  <div class="forgot-form-header">
    <h3>Reset Password</h3>
    <button type="button" class="lm-close" onclick="closeModal('forgot-modal')">&times;</button>
  </div>
  <div class="forgot-form-body">
    <div id="lm-forgot-error" class="lm-msg lm-msg--error" hidden></div>
    <div id="lm-forgot-success" class="lm-msg lm-msg--success" hidden></div>

    <p class="forgot-desc" style="font-size: 0.85rem; margin-bottom: 20px; color: #666; line-height: 1.45;">
      Enter your registered email address below, and we will send you instructions to reset your password.
    </p>

    <div class="forgot-group" style="margin-bottom: 20px;">
      <label class="forgot-label">Email Address</label>
      <div class="forgot-input-wrap">
        <i class="fas fa-envelope"></i>
        <input type="email" id="forgot-forgot-email" class="forgot-input" placeholder="yourname@example.com">
      </div>
    </div>

    <button type="button" id="forgot-btn-form" class="forgot-btn" onclick="submitForgotPassword()">
      <i class="fas fa-paper-plane"></i> Send Reset Link
    </button>

    <p class="forgot-switch" style="margin-top: 20px; text-align: center; font-size: 0.88rem;">
      Remembered your password? 
      <a href="#" onclick="closeModal('forgot-modal'); openModal('login-modal'); return false;" style="color: var(--orange-primary, #FF6B00); font-weight:600;">Sign in here</a>
    </p>
  </div>
</div>
  




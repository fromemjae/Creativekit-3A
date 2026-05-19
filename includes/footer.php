<footer class="site-footer" role="contentinfo">
    <div class="footer-main">
      <div class="container">
        <div class="footer-grid">

          <div class="footer-brand">
            <a href="/" aria-label="Pinoyballers home"><span class="logo-text">CreativeKit3A</span></a>
            <p class="tagline">VAT registered company<br>2779B R. FERNANDEZ ST. CORNER BENITA ST. GAGALANGIN, TONDO, MANILA, Manila, Philippines, 1013</p>
            <div class="footer-social" aria-label="Social media links">
              <a href="https://www.facebook.com/profile.php?id=100063787431490" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
              </div>
          </div>

          <div class="footer-col">
            <h5>Contact Information</h5>
            <div class="footer-contact-item">
              <i class="fas fa-envelope icon" aria-hidden="true"></i>
              <a href="mailto:salesandmarketing@creativekit3a.com">salesandmarketing@creativekit3a.com</a>
            </div>
            <div class="footer-contact-item">
              <i class="fas fa-mobile-alt icon" aria-hidden="true"></i>
              <a href="tel:+639177142774">+63 917 714 2774</a>
            </div>
            <div class="footer-contact-item">
              <i class="fas fa-phone icon" aria-hidden="true"></i>
              <a href="tel:+639339927410">+63 933 992 7410</a>
            </div>
          </div>

         
          <div class="footer-col">
            <h5>Customer Help</h5>
            <ul>
              <li><a href="/about.php">About</a></li>
              <li><a href="/accessories-gadgets/index2.html">Category</a></li>
              <li><a href="/contact-us/">Contact Us</a></li>
              </ul>
          </div>

        </div>
      </div>
    </div>

    <hr class="footer-divider">

    <div class="container">
      <div class="footer-bottom">
        <p>&copy; Copyright 2026. CreativeKit3A.shop All Rights Reserved.</p>
        <div class="footer-payments" aria-label="Accepted payment methods">
          <img src="https://.com/wp-content/uploads/2024/04/mastercard.png" alt="Mastercard">
          <img src="https://.com/wp-content/uploads/2024/04/visa.png" alt="Visa">
          <img src="https://.com/wp-content/uploads/2024/04/paypal-new.png" alt="PayPal">
          <img src="https://.com/wp-content/uploads/2024/04/BPI.png" alt="BPI">
          <img src="https://.com/wp-content/uploads/2024/04/paymongo_gcash.png" alt="GCash">
          <img src="https://.com/wp-content/uploads/2024/04/paymongo_paymaya.png" alt="Maya">
        </div>
      </div>
    </div>
  </footer>


<div class="lm-overlay" id="lm-overlay" onclick="closeAllModals()" aria-hidden="true"></div>

<div class="lm-modal" id="login-modal" role="dialog" aria-modal="true" aria-labelledby="login-title">
  <div class="lm-card">
    <button class="lm-close" onclick="closeModal('login-modal'); document.getElementById('panel-forgot').style.display='none'; document.getElementById('panel-login').style.display='block';" aria-label="Close">×</button>

    <div class="lm-header">
      <span class="lm-brand">CreativeKit3A</span>
      <p class="lm-tagline">Sign in to your account</p>
    </div>

    <div class="lm-panel" id="panel-login">
      <div class="lm-field">
        <label for="lm-login-email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="lm-login-email" placeholder="you@email.com" required>
      </div>

      <div class="lm-field">
        <label for="lm-login-password"><i class="fas fa-lock"></i> Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-login-password" placeholder="••••••••" required>
          <button type="button" class="lm-pw-toggle" onclick="togglePw('lm-login-password', this)"><i class="fas fa-eye"></i></button>
          
        </div>
      </div>

      <div class="lm-extras">
        <label class="lm-remember"><input type="checkbox"> Remember me</label>
        
        <a href="#" class="lm-forgot" onclick="document.getElementById('panel-login').style.display='none'; document.getElementById('panel-forgot').style.display='block'; return false;">Forgot password?</a>
      </div>

      <div class="lm-msg lm-msg--error" id="lm-login-error" hidden></div>

      <button class="btn btn-primary lm-submit" onclick="submitLogin()">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </button>

      <p class="lm-switch">
        Don't have an account? 
        <a href="#" onclick="closeModal('login-modal'); openModal('register-modal'); return false;">Register here</a>
      </p>
    </div>

    <div class="lm-panel" id="panel-forgot" style="display: none;">
      <p style="font-size: 0.85rem; margin-bottom: 15px; color: #666; line-height: 1.45;">
        Enter your registered email address below, and we will send you instructions to reset your password.
      </p>

      <div class="lm-field">
        <label for="lm-forgot-email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="lm-forgot-email" placeholder="you@email.com" required>
      </div>

      <div class="lm-msg lm-msg--error" id="lm-forgot-error" hidden></div>
      <div class="lm-msg lm-msg--success" id="lm-forgot-success" hidden></div>

      <button type="button" class="btn btn-primary lm-submit" id="lm-forgot-btn" onclick="submitForgotPassword(); return false;">
        <i class="fas fa-paper-plane"></i> Send Reset Link
      </button>

      <p class="lm-switch">
        Remembered your password? 
        <a href="#" onclick="document.getElementById('panel-forgot').style.display='none'; document.getElementById('panel-login').style.display='block'; return false;">Sign in here</a>
      </p>
    </div>

  </div>
</div>

<div class="lm-modal" id="register-modal" role="dialog" aria-modal="true" aria-labelledby="register-title">
  <div class="lm-card">
    <button class="lm-close" onclick="closeModal('register-modal')" aria-label="Close">×</button>

    <div class="lm-header">
      <span class="lm-brand">CreativeKit3A</span>
      <p class="lm-tagline">Create your account</p>
    </div>

   <div class="lm-panel" id="panel-register" role="tabpanel" aria-labelledby="tab-register" hidden>
      
      <div class="lm-field">
        <label for="lm-reg-firstname"><i class="fas fa-user"></i> First Name</label>
        <input type="text" id="lm-reg-firstname" placeholder="First Name" required>
      </div>

      <div class="lm-field">
        <label for="lm-reg-lastname"><i class="fas fa-user"></i> Last Name</label>
        <input type="text" id="lm-reg-lastname" placeholder="Last Name" required>
      </div>

      <div class="lm-field">
        <label for="lm-reg-email"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" id="lm-reg-email" placeholder="you@email.com" required>
      </div>

      <div class="lm-field">
        <label for="lm-reg-phone"><i class="fas fa-phone"></i> Phone Number</label>
        <input type="text" id="lm-reg-phone" placeholder="0917XXXXXXX">
      </div>

      <div class="lm-field">
        <label for="lm-reg-password"><i class="fas fa-lock"></i> Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-reg-password" placeholder="At least 8 characters" required>
          <button type="button" class="lm-pw-toggle" onclick="togglePw('lm-reg-password', this)"><i class="fas fa-eye"></i></button>
        </div>
      </div>

      <div class="lm-field">
        <label for="lm-reg-confirm"><i class="fas fa-lock"></i> Confirm Password</label>
        <div class="lm-pw-wrap">
          <input type="password" id="lm-reg-confirm" placeholder="Repeat your password" required>
          <button type="button" class="lm-pw-toggle" onclick="togglePw('lm-reg-confirm', this)"><i class="fas fa-eye"></i></button>
        </div>
      </div>

      <div class="lm-terms">
        <label><input type="checkbox" id="lm-reg-terms" required> I agree to the <a href="about.php">Terms & Conditions</a></label>
      </div>

      <div class="lm-msg lm-msg--error" id="lm-reg-error" hidden></div>
      <div class="lm-msg lm-msg--success" id="lm-reg-success" hidden></div>

      <button type="button" class="btn btn-primary lm-submit" id="lm-reg-btn" onclick="submitRegister(); return false;">
        <i class="fas fa-user-plus"></i> Create Account
      </button>

      <p class="lm-switch">
        Already have an account? 
        <a href="#" onclick="closeModal('register-modal'); openModal('login-modal'); switchTab('login'); return false;">Sign in here</a>
      </p>
    </div>
  </div>
</div>
  <script src="/CREATIVEKIT3A-WEBSITE/main.js"></script>



</body>
</html>
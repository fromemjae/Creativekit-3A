/* ==========================================
   auth.js — CreativeKit3A
   Custom auth using public.users (no Supabase Auth)
   ========================================== */

'use strict';

/* ------------------------------------------
   SESSION HELPERS
   ------------------------------------------ */

/**
 * Save user to sessionStorage
 */
function setSession(user) {
  // Never store the password hash on the client
  const { password, ...safeUser } = user;
  sessionStorage.setItem('ck_user', JSON.stringify(safeUser));
}

/**
 * Get current logged-in user (or null)
 */
function getSession() {
  try {
    const raw = sessionStorage.getItem('ck_user');
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

/**
 * Clear session (logout)
 */
function clearSession() {
  sessionStorage.removeItem('ck_user');
}

/**
 * Check if someone is logged in
 */
function isLoggedIn() {
  return getSession() !== null;
}

/* ------------------------------------------
   REGISTER
   ------------------------------------------ */

/**
 * Handles user registration — inserts into public.users
 * Password is hashed server-side via the DB trigger (pgcrypto)
 * @param {Event} event
 */
async function handleRegister(event) {
  event.preventDefault();

  const firstNameEl = document.getElementById('reg-firstname');
  const lastNameEl  = document.getElementById('reg-lastname');
  const emailEl     = document.getElementById('reg-email');
  const passwordEl  = document.getElementById('reg-password');
  const phoneEl     = document.getElementById('reg-phone');
  const errorEl     = document.getElementById('lm-register-error');
  const btnEl       = event.target.querySelector('button[type="submit"]');

  const firstName = firstNameEl?.value.trim();
  const lastName  = lastNameEl?.value.trim();
  const email     = emailEl?.value.trim();
  const password  = passwordEl?.value;
  const phone     = phoneEl?.value.trim() || null;

  // Clear previous errors
  if (errorEl) { errorEl.hidden = true; errorEl.textContent = ''; }

  // Basic client-side validation
  if (!firstName || !lastName || !email || !password) {
    showAuthError(errorEl, 'Please fill in all required fields.');
    return;
  }

  if (password.length < 6) {
    showAuthError(errorEl, 'Password must be at least 6 characters.');
    return;
  }

  // Disable button while processing
  if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Registering...'; }

  try {
    // Check if email already exists
    const { data: existing, error: checkError } = await supabase
      .from('users')
      .select('id')
      .eq('email', email)
      .maybeSingle();

    if (checkError) throw checkError;

    if (existing) {
      showAuthError(errorEl, 'An account with this email already exists.');
      return;
    }

    // Insert new user — password hashed by DB trigger automatically
    const { error: insertError } = await supabase
      .from('users')
      .insert({
        first_name: firstName,
        last_name:  lastName,
        email:      email,
        password:   password,
        phone:      phone
      });

    if (insertError) throw insertError;

    // Success — redirect to login
    alert('Registration successful! You can now sign in.');
    window.location.href = 'login.html';

  } catch (err) {
    console.error('[Register Error]', err.message);
    showAuthError(errorEl, 'Registration failed: ' + err.message);
  } finally {
    if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Register'; }
  }
}

/* ------------------------------------------
   SIGN IN
   ------------------------------------------ */

/**
 * Handles user login — verifies against public.users via RPC
 * @param {Event} event
 */
async function handleSignIn(event) {
  event.preventDefault();

  const emailEl    = document.getElementById('login-email');
  const passwordEl = document.getElementById('login-password');
  const errorEl    = document.getElementById('lm-login-error');
  const btnEl      = event.target.querySelector('button[type="submit"]');

  const email    = emailEl?.value.trim();
  const password = passwordEl?.value;

  // Clear previous errors
  if (errorEl) { errorEl.hidden = true; errorEl.textContent = ''; }

  if (!email || !password) {
    showAuthError(errorEl, 'Please enter your email and password.');
    return;
  }

  if (btnEl) { btnEl.disabled = true; btnEl.textContent = 'Signing in...'; }

  try {
    // Use the login_user RPC which does crypt() comparison server-side
    const { data, error } = await supabase.rpc('login_user', {
      p_email:    email,
      p_password: password
    });

    if (error) throw error;

    if (!data || data.length === 0) {
      showAuthError(errorEl, 'Invalid email or password.');
      return;
    }

    const user = data[0];

    // Save session (password hash stripped inside setSession)
    setSession(user);

    // Update UI then redirect
    updateAuthUI();
    window.location.href = 'index.html';

  } catch (err) {
    console.error('[Sign In Error]', err.message);
    showAuthError(errorEl, 'Sign in failed: ' + err.message);
  } finally {
    if (btnEl) { btnEl.disabled = false; btnEl.textContent = 'Sign In'; }
  }
}

/* ------------------------------------------
   SIGN OUT
   ------------------------------------------ */

/**
 * Logs the user out and redirects to login page
 */
function handleSignOut() {
  clearSession();
  updateAuthUI();
  window.location.href = 'login.html';
}

/* ------------------------------------------
   UI UPDATER
   ------------------------------------------ */

/**
 * Updates nav/header UI based on login state.
 * Looks for:
 *   #auth-guest     — shown when logged out
 *   #auth-customer  — shown when logged in as customer
 *   #user-display-name — filled with user's first name
 */
function updateAuthUI() {
  const user         = getSession();
  const guestGroup   = document.getElementById('auth-guest');
  const customerGroup = document.getElementById('auth-customer');
  const nameEl       = document.getElementById('user-display-name');

  if (!user) {
    if (guestGroup)    guestGroup.hidden = false;
    if (customerGroup) customerGroup.hidden = true;
  } else {
    if (guestGroup)    guestGroup.hidden = true;
    if (customerGroup) customerGroup.hidden = false;
    if (nameEl)        nameEl.textContent = user.first_name || 'Customer';
  }
}

/* ------------------------------------------
   ROUTE GUARD
   ------------------------------------------ */

/**
 * Call this on any page that requires login.
 * Redirects to login.html if no session exists.
 * Usage: requireAuth();
 */
function requireAuth() {
  if (!isLoggedIn()) {
    window.location.href = 'login.html';
  }
}

/* ------------------------------------------
   ERROR DISPLAY HELPER
   ------------------------------------------ */

/**
 * Shows an error message in the given element
 * @param {HTMLElement|null} el
 * @param {string} message
 */
function showAuthError(el, message) {
  if (!el) { alert(message); return; }
  el.textContent = message;
  el.hidden = false;
}

/* ------------------------------------------
   INIT ON PAGE LOAD
   ------------------------------------------ */

document.addEventListener('DOMContentLoaded', updateAuthUI);
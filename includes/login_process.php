<?php
// includes/login_process.php
// ============================================================
// DUAL-ROLE AUTHENTICATION PROCESSOR (ADMINS & CUSTOMERS)
// ============================================================
header('Content-Type: application/json');

// 1. Pull in your existing database connection
require_once __DIR__ . '/db.php';

// Enforce consistent global cookie scoping parameters to prevent spontaneous logouts
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}

// 2. Grab raw JSON input sent by main.js fetch()
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format received.']);
    exit();
}

// 3. Extract credentials safely
$email    = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// 4. Input validation
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please enter both email and password fields.']);
    exit();
}

// ============================================================
// LOUD UPDATE COMMENTS: PHASE 1 - SCAN THE ADMINS TABLE FIRST
// Intercepts administrative attempts before shifting to customers
// ============================================================
/* */
$adminQuery = "SELECT * FROM admins WHERE email = ? LIMIT 1";
$adminStmt  = mysqli_prepare($conn, $adminQuery);

if ($adminStmt) {
    mysqli_stmt_bind_param($adminStmt, 's', $email);
    mysqli_stmt_execute($adminStmt);
    $adminResult = mysqli_stmt_get_result($adminStmt);
    $admin       = mysqli_fetch_assoc($adminResult);
    mysqli_stmt_close($adminStmt);

    if ($admin) {
        // Check if admin status is inactive
        if (isset($admin['status']) && $admin['status'] === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'This administrative profile has been deactivated.']);
            exit();
        }

        // Verify administrative password
        if (password_verify($password, $admin['password'])) {
            // Set administrative session tokens needed by header.php and auth protection layers
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['first_name'];
            $_SESSION['admin_role'] = isset($admin['role']) ? $admin['role'] : 'admin';
            
            // Unset customer keys to avoid session role bleeding
            unset($_SESSION['user_id']);
            unset($_SESSION['user_role']);

            echo json_encode(['success' => true, 'message' => 'Administrative clearance granted! Accessing system control panels...']);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect password specified for this administrative account.']);
            exit();
        }
    }
}
/* */


// ============================================================
// PHASE 2 - FALLBACK TO STANDARD CUSTOMER VERIFICATION
// ============================================================
$query = "SELECT * FROM users WHERE email = ? LIMIT 1";
$stmt  = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user) {
        // COMMENT: Check if user status is banned before letting them in
        if ($user['status'] === 'banned') {
            echo json_encode(['success' => false, 'message' => 'Your account has been suspended. Contact support.']);
            exit();
        }

        // COMMENT: Your register_process.php script hashes passwords using PASSWORD_BCRYPT.
        // Therefore, we MUST use password_verify here to mathematically match them.
        if (password_verify($password, $user['password'])) {
            
            // COMMENT: Set customer session variables
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];
            $_SESSION['user_role'] = 'customer'; 
            
            // Unset administrative attributes to keep environment definitions separate
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_role']);

            echo json_encode(['success' => true, 'message' => 'Login successful! Redirecting...']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No account registry match found with that email identifier.']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database authentication pipeline transactional query error.']);
}
?>
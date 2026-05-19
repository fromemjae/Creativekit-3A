<?php
// ============================================================
// AJAX REGISTER PROCESSOR
// includes/register_process.php
// ============================================================
header('Content-Type: application/json');

// 1. Pull in database connection
require_once __DIR__ . '/db.php';

// 2. Grab raw JSON input sent by main.js fetch()
$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format received.']);
    exit();
}

// 3. Extract and sanitize variables
$first_name = isset($data['first_name']) ? trim($data['first_name']) : '';
$last_name  = isset($data['last_name']) ? trim($data['last_name']) : '';
$email      = isset($data['email']) ? trim($data['email']) : '';
$phone      = isset($data['phone']) ? trim($data['phone']) : null;
$password   = isset($data['password']) ? $data['password'] : '';

// 4. Server-side validation fallback
if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit();
}

// 5. Check if email already exists in your 'users' table
$check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($check_stmt, 's', $email);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']);
    mysqli_stmt_close($check_stmt);
    exit();
}
mysqli_stmt_close($check_stmt);

// 6. Securely hash password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// 7. Insert new user matching your creativekit3a.sql configuration
// (Note: 'status' automatically defaults to 'active' inside your SQL definition, so we omit it safely here)
$insert_stmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");

if ($insert_stmt) {
    mysqli_stmt_bind_param($insert_stmt, 'sssss', $first_name, $last_name, $email, $phone, $hashed_password);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during insertion: ' . mysqli_stmt_error($insert_stmt)]);
    }
    mysqli_stmt_close($insert_stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database preparation failed: ' . mysqli_error($conn)]);
}
exit();
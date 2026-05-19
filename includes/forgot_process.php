<?php
// includes/forgot_process.php
// ============================================================
// SIMULATED FORGOT PASSWORD PROCESSING ENGINE
// ============================================================
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data format received.']);
    exit();
}

$email = isset($data['email']) ? trim($data['email']) : '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
    exit();
}

// Check if the customer email account exists inside the database table
$query = "SELECT first_name FROM users WHERE email = ? LIMIT 1";
$stmt  = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user   = mysqli_fetch_assoc($result);

    if ($user) {
        // Match found! Return success notification string
        echo json_encode([
            'success' => true, 
            'message' => 'A password reset link has been sent to your email box! (Simulated System Live)'
        ]);
    } else {
        // No match found
        echo json_encode([
            'success' => false, 
            'message' => 'This email address is not registered in our records.'
        ]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database operation error occurred.']);
}
?>
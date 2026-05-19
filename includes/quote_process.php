<?php
// includes/quote_process.php
// ============================================================
// LOUD UPDATE COMMENTS: ENTIRE NEW FILE ADDED
// AJAX QUOTE REQUEST ENGINE AND CONTROLLER
// Handles capture, parameter escaping, tracking logged-in user context, 
// and inserting entries directly into MySQL.
// ============================================================
header('Content-Type: application/json');

// Pull in database context safely
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Intercept data streams
$inputContent = file_get_contents('php://input');
$parsedData = json_decode($inputContent, true);

if (!$parsedData) {
    echo json_encode(['success' => false, 'message' => 'Invalid or malformed data context structure.']);
    exit();
}

// Extract variables safely
$fullName   = isset($parsedData['full_name']) ? trim($parsedData['full_name']) : '';
$email      = isset($parsedData['email']) ? trim($parsedData['email']) : '';
$phone      = isset($parsedData['phone']) ? trim($parsedData['phone']) : null;
$itemType   = isset($parsedData['item_type']) ? trim($parsedData['item_type']) : '';
$quantity   = isset($parsedData['quantity']) ? intval($parsedData['quantity']) : 0;
$deadline   = isset($parsedData['deadline']) && !empty($parsedData['deadline']) ? $parsedData['deadline'] : null;
$customNotes = isset($parsedData['custom_notes']) ? trim($parsedData['custom_notes']) : null;

// Determine if a customer user account session is active
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

// Validation rules matching data policies
if (empty($fullName) || empty($email) || empty($itemType) || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please provide all required parameters marked with asterisks (*)']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please fill out a valid email address path structure.']);
    exit();
}

// Bind request items into the tracking database
$insertQuery = "INSERT INTO quote_requests (user_id, full_name, email, phone, item_type, quantity, deadline, custom_notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
$statement = mysqli_prepare($conn, $insertQuery);

if ($statement) {
    mysqli_stmt_bind_param($statement, 'isssssss', $userId, $fullName, $email, $phone, $itemType, $quantity, $deadline, $customNotes);
    
    if (mysqli_stmt_execute($statement)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Your custom layout quotation proposal parameter sets were transmitted to our tracking dashboard. We will notify you shortly!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database persistence fault encountered. Please try again later.']);
    }
    mysqli_stmt_close($statement);
} else {
    echo json_encode(['success' => false, 'message' => 'Internal prepared data framework allocation error.']);
}

mysqli_close($conn);
?>
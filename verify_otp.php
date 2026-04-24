<?php
session_start();

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$entered_otp = trim($_POST['otp'] ?? '');

// Check OTP exists in session
if (empty($_SESSION['otp']) || empty($_SESSION['otp_time'])) {
    echo json_encode(['status' => 'error', 'message' => 'No OTP found. Please request a new one.']);
    exit;
}

// Check OTP has not expired (5 minutes = 300 seconds)
if ((time() - $_SESSION['otp_time']) > 300) {
    // Clear expired OTP
    unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']);
    echo json_encode(['status' => 'error', 'message' => 'OTP has expired. Please request a new one.']);
    exit;
}

// Check OTP matches
if ($entered_otp !== (string)$_SESSION['otp']) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect OTP. Please try again.']);
    exit;
}

// OTP is correct — mark as verified in session
$_SESSION['otp_verified'] = true;

// Store donor details in session so payment_verify.php reads from session, not POST
// These must have been set before this step (in donate.php flow)
// We just confirm the flag here; actual donor data is stored on the client
// and will be passed at form submit — see payment_verify.php for session check.

// Clear OTP from session (one-time use)
unset($_SESSION['otp'], $_SESSION['otp_time']);

echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully.']);
exit;
?>
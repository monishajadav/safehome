<?php
session_start();
include('includes/db_connect.php');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: donate.php');
    exit;
}

// --- Security check: OTP must have been verified this session ---
if (empty($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header('Location: donate.php?error=otp_required');
    exit;
}

// Read submitted data from POST
$payment_id     = trim($_POST['razorpay_payment_id'] ?? '');
$full_name      = trim($_POST['full_name']           ?? '');
$email          = trim($_POST['email']               ?? '');
$phone          = trim($_POST['phone']               ?? '');
$amount         = floatval($_POST['amount']          ?? 0);
$donation_type  = trim($_POST['donation_type']       ?? '');
$payment_method = trim($_POST['payment_method']      ?? '');
$message        = trim($_POST['message']             ?? '');

// Basic validation
if (!$payment_id || !$full_name || !$email || $amount < 1) {
    header('Location: donate.php?error=invalid');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: donate.php?error=invalid');
    exit;
}

// Save to donations table
// bind_param format: s=string, d=double — 8 params, 8 placeholders
$stmt = $conn->prepare("
    INSERT INTO donations 
        (payment_id, full_name, email, phone, amount, donation_type, payment_method, message, status, created_at)
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())
");
$stmt->bind_param(
    "ssssdsss",           // 8 types: 4 strings, 1 double, 3 strings
    $payment_id,
    $full_name,
    $email,
    $phone,
    $amount,
    $donation_type,
    $payment_method,
    $message
);

if ($stmt->execute()) {
    $donation_id = $conn->insert_id;

    // Store in session for thank-you page
    $_SESSION['last_donation'] = [
        'id'             => $donation_id,
        'payment_id'     => $payment_id,
        'name'           => $full_name,
        'email'          => $email,
        'amount'         => $amount,
        'donation_type'  => $donation_type,
        'payment_method' => $payment_method,
    ];

    // Clear OTP verified flag so it cannot be reused
    unset($_SESSION['otp_verified'], $_SESSION['otp_email']);

    $stmt->close();
    $conn->close();
    header('Location: thank_you.php');
    exit;

} else {
    error_log("Donation DB insert failed: " . $stmt->error);
    $stmt->close();
    $conn->close();
    header('Location: donate.php?error=db_failed');
    exit;
}
?>
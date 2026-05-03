
<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Adjust this path to match your project ---
// If PHPMailer is in src/ folder:
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';
// If PHPMailer is installed via Composer, replace above 3 lines with:
// require 'vendor/autoload.php';

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
    exit;
}

// Generate 6-digit OTP and store in session
$otp = rand(100000, 999999);
$_SESSION['otp']        = $otp;
$_SESSION['otp_email']  = $email;
$_SESSION['otp_time']   = time();          // used to check 5-min expiry
$_SESSION['otp_verified'] = false;         // reset any previous verification

// --- Gmail credentials ---
define('GMAIL_ADDRESS',  'moni25jadav@gmail.com');
define('GMAIL_APP_PASS', 'eeji lbkc vaze nvbk');        // App Password — NO spaces

$mail = new PHPMailer(true);

try {
    // SMTP config
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = GMAIL_ADDRESS;
    $mail->Password   = GMAIL_APP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender & recipient
    $mail->setFrom(GMAIL_ADDRESS, 'Safe & Home Foundation');
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP - Safe & Home Foundation';
    $mail->Body    = "
        <div style='font-family:Arial,sans-serif;max-width:420px;margin:auto;border:1px solid #e0e0e0;border-radius:15px;overflow:hidden;'>
            <div style='background:linear-gradient(135deg,#198754,#20c997);padding:24px;text-align:center;color:white;'>
                <h2 style='margin:0;font-size:1.4rem;'>Safe &amp; Home Foundation</h2>
                <p style='margin:6px 0 0;opacity:0.9;font-size:0.9rem;'>Donation OTP Verification</p>
            </div>
            <div style='padding:32px;text-align:center;'>
                <p style='color:#444;font-size:0.95rem;'>Your One-Time Password is:</p>
                <div style='font-size:2.8rem;font-weight:900;color:#198754;letter-spacing:12px;margin:20px 0;'>$otp</div>
                <p style='color:#888;font-size:0.82rem;'>Valid for <strong>5 minutes</strong>. Do not share this OTP with anyone.</p>
                <hr style='border:none;border-top:1px solid #eee;margin:20px 0;'>
                <p style='color:#aaa;font-size:0.78rem;'>If you did not request this, please ignore this email.</p>
            </div>
        </div>
    ";

    $mail->send();
    echo json_encode(['status' => 'sent']);

} catch (Exception $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Could not send OTP. Please try again. (' . $mail->ErrorInfo . ')'
    ]);
}
?>
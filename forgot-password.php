<?php
session_start();
include('includes/db_connect.php');

$error   = "";
$show_otp = false;

// ── STEP 1: VALIDATE USER & STORE IN SESSION ─────────────────────────────────
if(isset($_POST['submit'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));

    if(empty($email) || empty($username)) {
        $error = "Please enter both email and username.";
    } else {
        $sql    = "SELECT * FROM users WHERE email='$email' AND username='$username'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) == 1) {
            // Store for OTP step
            $_SESSION['forgot_email']    = $email;
            $_SESSION['forgot_username'] = $username;
            $_SESSION['otp_verified']    = false;
            $show_otp = true;
        } else {
            $error = "No account found with that email and username.";
        }
    }
}

// ── STEP 2: AFTER OTP VERIFIED — GENERATE RESET TOKEN ────────────────────────
if(isset($_POST['do_reset'])) {
    if(!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        $error = "Please verify your OTP first.";
    } else {
        $email    = mysqli_real_escape_string($conn, $_SESSION['forgot_email']);
        $username = mysqli_real_escape_string($conn, $_SESSION['forgot_username']);
        $token    = bin2hex(random_bytes(32));
        $expiry   = date('Y-m-d H:i:s', strtotime('+2 hours'));

        $upd = "UPDATE users SET reset_token='$token', reset_expiry='$expiry'
                WHERE email='$email' AND username='$username'";

        if(mysqli_query($conn, $upd)) {
            unset($_SESSION['forgot_email'], $_SESSION['forgot_username'],
                  $_SESSION['otp_verified'], $_SESSION['otp'],
                  $_SESSION['otp_email'],    $_SESSION['otp_time']);
            header("Location: reset-password.php?token=$token");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Safe & Home Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh; display:flex; align-items:center; justify-content:center;
        }
        .forgot-card {
            background: rgba(255,255,255,0.95); border-radius:20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3); max-width:450px;
            animation: fadeIn 0.8s ease-in;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(30px);} to{opacity:1;transform:translateY(0);} }
        .forgot-icon { font-size:4rem; color:#667eea; }
        .input-group-text { background:#667eea; color:white; border:none; }
        .form-control:focus { border-color:#667eea; box-shadow:0 0 0 0.2rem rgba(102,126,234,0.25); }
        .btn-primary { background:linear-gradient(135deg,#667eea,#764ba2); border:none; font-weight:bold; transition:all 0.3s; }
        .btn-primary:hover { transform:translateY(-2px); }
        .back-link { color:#667eea; font-weight:600; text-decoration:none; }

        /* OTP */
        .otp-box { width:48px; height:54px; font-size:1.4rem; font-weight:700; text-align:center; border:2px solid #667eea; border-radius:10px; }
        .otp-box:focus { border-color:#764ba2; box-shadow:0 0 0 0.2rem rgba(102,126,234,0.25); outline:none; }
    </style>
</head>
<body>
<div class="forgot-card card p-5 w-100">
    <div class="text-center mb-4">
        <i class="bi bi-key forgot-icon"></i>
        <h2 class="mt-3 fw-bold" style="color:#343a40;">Forgot Password?</h2>
        <p class="text-muted">
            <?php echo $show_otp ? 'Verify your email to continue' : 'Enter your email and username'; ?>
        </p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(!$show_otp): ?>
    <!-- ── STEP 1: EMAIL + USERNAME FORM ── -->
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" name="email"
                    placeholder="Enter your email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="username"
                    placeholder="Enter your username"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
        </div>
        <button type="submit" name="submit" class="btn btn-primary w-100 py-2">
            <i class="bi bi-arrow-right-circle me-1"></i> Continue
        </button>
    </form>

    <?php else: ?>
    <!-- ── STEP 2: OTP VERIFICATION ── -->
    <div class="text-center mb-3">
        <p class="text-muted" style="font-size:0.9rem;">
            Send OTP to <strong><?php echo htmlspecialchars($_SESSION['forgot_email']); ?></strong>
        </p>
    </div>

    <div id="otp-error" class="alert alert-danger d-none"></div>
    <div id="otp-success" class="alert alert-success d-none"></div>

    <!-- Send OTP button -->
    <div id="send-otp-section" class="text-center mb-3">
        <button type="button" class="btn btn-primary w-100 py-2" id="sendOtpBtn" onclick="sendForgotOTP()">
            <i class="bi bi-send-fill me-1"></i> Send OTP to Email
        </button>
    </div>

    <!-- OTP boxes -->
    <div id="otp-input-section" style="display:none;">
        <label class="form-label fw-bold">Enter 6-digit OTP</label>
        <div class="d-flex justify-content-center gap-2 mb-3">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,1)" onkeydown="movePrev(event,this,0)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,2)" onkeydown="movePrev(event,this,1)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,3)" onkeydown="movePrev(event,this,2)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,4)" onkeydown="movePrev(event,this,3)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,5)" onkeydown="movePrev(event,this,4)">
            <input type="text" class="otp-box" maxlength="1" onkeydown="movePrev(event,this,5)">
        </div>
        <div class="text-center mb-3 text-muted" style="font-size:0.88rem;">
            OTP expires in: <strong id="fp-timer" style="color:#667eea;">05:00</strong>
        </div>
        <button type="button" class="btn btn-primary w-100 py-2 mb-2" onclick="verifyForgotOTP()">
            <i class="bi bi-shield-check-fill me-1"></i> Verify OTP
        </button>
        <button type="button" class="btn btn-outline-secondary w-100 py-2" id="resendBtn" onclick="sendForgotOTP()" disabled>
            <i class="bi bi-arrow-clockwise me-1"></i> Resend OTP
        </button>
    </div>

    <!-- Hidden form submitted after OTP verified -->
    <form id="doResetForm" method="POST">
        <input type="hidden" name="do_reset" value="1">
    </form>
    <?php endif; ?>

    <div class="mt-4 text-center">
        <a href="login.php" class="back-link"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function moveNext(input, nextIndex) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if(input.value.length === 1 && nextIndex < 6)
        document.querySelectorAll('.otp-box')[nextIndex].focus();
}
function movePrev(event, input, prevIndex) {
    if(event.key === 'Backspace' && input.value === '' && prevIndex >= 0)
        document.querySelectorAll('.otp-box')[prevIndex].focus();
}

var fpTimer = null;
function startTimer(seconds) {
    clearInterval(fpTimer);
    var remaining = seconds;
    var el = document.getElementById('fp-timer');
    var resendBtn = document.getElementById('resendBtn');
    if(resendBtn) resendBtn.disabled = true;

    fpTimer = setInterval(function() {
        remaining--;
        var m = Math.floor(remaining/60);
        var s = remaining % 60;
        if(el) el.textContent = (m<10?'0':'')+m+':'+(s<10?'0':'')+s;
        if(remaining <= 0) {
            clearInterval(fpTimer);
            if(el) { el.textContent = 'Expired'; el.style.color = '#dc3545'; }
            if(resendBtn) resendBtn.disabled = false;
        }
    }, 1000);
}

function sendForgotOTP() {
    var email = '<?php echo isset($_SESSION["forgot_email"]) ? addslashes($_SESSION["forgot_email"]) : ""; ?>';
    var errBox = document.getElementById('otp-error');
    var sucBox = document.getElementById('otp-success');
    var sendBtn = document.getElementById('sendOtpBtn');

    errBox.classList.add('d-none');
    sucBox.classList.add('d-none');
    if(sendBtn) { sendBtn.disabled = true; sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...'; }

    fetch('send_otp.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'email=' + encodeURIComponent(email)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'sent') {
            sucBox.classList.remove('d-none');
            sucBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>OTP sent to ' + email;
            document.getElementById('send-otp-section').style.display = 'none';
            document.getElementById('otp-input-section').style.display = 'block';
            document.querySelectorAll('.otp-box').forEach(b => b.value = '');
            document.querySelectorAll('.otp-box')[0].focus();
            startTimer(300);
        } else {
            errBox.classList.remove('d-none');
            errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>' + (data.message || 'Failed to send OTP.');
            if(sendBtn) { sendBtn.disabled = false; sendBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Send OTP to Email'; }
        }
    })
    .catch(() => {
        errBox.classList.remove('d-none');
        errBox.innerHTML = 'Network error. Please try again.';
        if(sendBtn) { sendBtn.disabled = false; sendBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Send OTP to Email'; }
    });
}

function verifyForgotOTP() {
    var boxes = document.querySelectorAll('.otp-box');
    var otp = '';
    boxes.forEach(b => otp += b.value.trim());
    var errBox = document.getElementById('otp-error');
    var sucBox = document.getElementById('otp-success');

    errBox.classList.add('d-none');
    sucBox.classList.add('d-none');

    if(otp.length < 6) {
        errBox.classList.remove('d-none');
        errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>Please enter the complete 6-digit OTP.';
        return;
    }

    fetch('verify_otp.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'otp=' + encodeURIComponent(otp)
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            sucBox.classList.remove('d-none');
            sucBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>OTP Verified! Redirecting to reset password...';
            clearInterval(fpTimer);
            setTimeout(() => document.getElementById('doResetForm').submit(), 1200);
        } else {
            errBox.classList.remove('d-none');
            errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>' + (data.message || 'Invalid OTP.');
        }
    })
    .catch(() => {
        errBox.classList.remove('d-none');
        errBox.innerHTML = 'Network error. Please try again.';
    });
}
</script>
</body>
</html>
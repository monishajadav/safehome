<!-- //User fills form
//↓
//Validate inputs
↓
Store details temporarily in Session
↓
Send OTP to email
↓
Verify OTP
↓
If OTP correct → Insert user into database
↓
Redirect to login// -->
<?php
session_start();
//store data temporarily
include('includes/db_connect.php');

$success = "";//---|___to display success and error message
$error   = "";//---|

//step-2 AFTER OTP VERIFIED — SAVE TO DB 
if(isset($_POST['create_account'])) {
    if(!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        $error = "Please verify your OTP first.";
    } else {
        $username  = mysqli_real_escape_string($conn, trim($_SESSION['reg_username']));
        $email     = mysqli_real_escape_string($conn, trim($_SESSION['reg_email']));//to prevent from sql injection
        $phone     = mysqli_real_escape_string($conn, trim($_SESSION['reg_phone']));
        $hashed_pw = $_SESSION['reg_password'];

        $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email'");//check wheter user already exists
        if(mysqli_num_rows($check) > 0) {
            $error = "Username or email already registered.";
        } else {
            $sql = "INSERT INTO users (username, email, password, phone) VALUES ('$username','$email','$hashed_pw','$phone')";
            if(mysqli_query($conn, $sql)) {
                // Clear session data
                unset($_SESSION['reg_username'], $_SESSION['reg_email'], $_SESSION['reg_phone'],
                      $_SESSION['reg_password'], $_SESSION['otp_verified'], $_SESSION['otp'],
                      $_SESSION['otp_email'], $_SESSION['otp_time']);
                $redirect = isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : '';
                header("Location: login.php$redirect");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// ── STEP 1: VALIDATE FORM & STORE IN SESSION ─────────────────────────────────
if(isset($_POST['submit'])) {
    $username        = trim($_POST['username']);
    $email           = trim($_POST['email']);
    $phone           = trim($_POST['phone']);
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if(empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = "All fields are required.";
    } elseif($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $esc_user  = mysqli_real_escape_string($conn, $username);
        $esc_email = mysqli_real_escape_string($conn, $email);

        $check_u = mysqli_query($conn, "SELECT id FROM users WHERE username='$esc_user'");
        $check_e = mysqli_query($conn, "SELECT id FROM users WHERE email='$esc_email'");

        if(mysqli_num_rows($check_u) > 0) {
            $error = "Username already exists.";
        } elseif(mysqli_num_rows($check_e) > 0) {
            $error = "Email already registered.";
        } else {
            // Store in session — don't save to DB yet
            $_SESSION['reg_username'] = $username;
            $_SESSION['reg_email']    = $email;
            $_SESSION['reg_phone']    = $phone;
            $_SESSION['reg_password'] = password_hash($password, PASSWORD_DEFAULT);//encrpt data
            $_SESSION['otp_verified'] = false;
            // Show OTP step
            $show_otp = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Safe & Home Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body { height:100%; margin:0; padding:0; overflow-x:hidden; }
        body {
            background-image: url("./images/register.png");
            background-repeat: no-repeat; background-position: center;
            background-size: cover; background-attachment: fixed;
            min-height: 100vh; display:flex; align-items:center; justify-content:center;
        }
        .register-card {
            background-color: rgba(0,0,0,0.45); border-radius:12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5); padding:30px;
            animation: fadeIn 0.8s ease-in;
        }
        @keyframes fadeIn { from{opacity:0;transform:translateY(30px);} to{opacity:1;transform:translateY(0);} }
        .form-container h2 { color:white; font-weight:900; text-shadow:2px 2px 6px black; }
        .form-container .form-label { color:#F5F5F5; font-weight:700; text-shadow:1px 1px 3px #000; }
        .form-container a { color:#ADFF2F; font-weight:700; text-shadow:1px 1px 2px #000; }
        .register-icon { font-size:3rem; color:white; text-shadow:2px 2px 5px black; }
        .input-group-text { background-color:#28a745; color:white; border:none; }
        .form-control:focus { border-color:#28a745; box-shadow:0 0 0 0.2rem rgba(40,167,69,0.25); }
        .btn-success { background:linear-gradient(135deg,#28a745,#20c997); border:none; font-weight:bold; transition:all 0.3s; }
        .btn-success:hover { transform:translateY(-2px); box-shadow:0 5px 15px rgba(40,167,69,0.4); }

        /* OTP styles */
        .otp-box { width:48px; height:54px; font-size:1.4rem; font-weight:700; text-align:center; border:2px solid #28a745; border-radius:10px; }
        .otp-box:focus { border-color:#20c997; box-shadow:0 0 0 0.2rem rgba(40,167,69,0.25); outline:none; }
        .timer { font-size:0.9rem; color:#aaa; }
        .timer strong { color:#28a745; }
    </style>
</head>
<body>
<div class="register-card card p-4 w-100 form-container" style="max-width:440px;">
    <div class="text-center mb-4">
        <i class="bi bi-person-plus register-icon"></i>
        <h2 class="fw-bold mt-2">Create Account</h2>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ── REGISTRATION FORM ── -->
    <?php if(!isset($show_otp)): ?>
    <form id="registrationform" novalidate
          action="register.php<?php echo isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : ''; ?>"
          method="POST">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Enter username" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                <input type="email" class="form-control" name="email" placeholder="Enter email" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Phone <span class="text-muted fw-normal">(optional)</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="tel" class="form-control" name="phone" placeholder="+91 XXXXX XXXXX">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="registerPassword" name="password" placeholder="Min 6 characters" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" class="form-control" id="registerConfirmPassword" name="confirmPassword" placeholder="Confirm password" required>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-success w-100 py-2 mt-2">
            <i class="bi bi-arrow-right-circle me-1"></i> Continue
        </button>
    </form>

    <div class="mt-3 text-center">
        <a href="login.php<?php echo isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : ''; ?>">
            Already have an account? <span style="color:#4CAF50;font-weight:700;">Login</span>
        </a>
    </div>

    <?php else: ?>
    <!-- ── OTP VERIFICATION STEP ── -->
    <div class="text-center mb-3">
        <div style="width:70px;height:70px;background:linear-gradient(135deg,#28a745,#20c997);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <i class="bi bi-envelope-check-fill text-white" style="font-size:1.8rem;"></i>
        </div>
        <h6 class="text-white fw-bold">Verify Your Email</h6>
        <p class="text-white-50" style="font-size:0.85rem;">
            We'll send a 6-digit OTP to<br>
            <strong class="text-white"><?php echo htmlspecialchars($_SESSION['reg_email']); ?></strong>
        </p>
    </div>

    <div id="otp-error" class="alert alert-danger d-none"></div>
    <div id="otp-success" class="alert alert-success d-none"></div>

    <!-- Send OTP button (shown first) -->
    <div id="send-otp-section" class="text-center mb-3">
        <button type="button" class="btn btn-success w-100 py-2" id="sendOtpBtn" onclick="sendRegOTP()">
            <i class="bi bi-send-fill me-1"></i> Send OTP to Email
        </button>
    </div>

    <!-- OTP input boxes (hidden until OTP sent) -->
    <div id="otp-input-section" style="display:none;">
        <label class="form-label text-white fw-bold">Enter 6-digit OTP</label>
        <div class="d-flex justify-content-center gap-2 mb-3">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,1)" onkeydown="movePrev(event,this,0)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,2)" onkeydown="movePrev(event,this,1)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,3)" onkeydown="movePrev(event,this,2)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,4)" onkeydown="movePrev(event,this,3)">
            <input type="text" class="otp-box" maxlength="1" oninput="moveNext(this,5)" onkeydown="movePrev(event,this,4)">
            <input type="text" class="otp-box" maxlength="1" onkeydown="movePrev(event,this,5)">
        </div>
        <div class="text-center mb-3 timer">
            OTP expires in: <strong id="reg-timer">05:00</strong>
        </div>
        <button type="button" class="btn btn-success w-100 py-2 mb-2" onclick="verifyRegOTP()">
            <i class="bi bi-shield-check-fill me-1"></i> Verify OTP
        </button>
        <button type="button" class="btn btn-outline-light w-100 py-2" id="resendBtn" onclick="sendRegOTP()" disabled>
            <i class="bi bi-arrow-clockwise me-1"></i> Resend OTP
        </button>
    </div>

    <!-- Hidden form — submitted after OTP verified -->
    <form id="createAccountForm" method="POST"
          action="register.php<?php echo isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : ''; ?>">
        <input type="hidden" name="create_account" value="1">
    </form>

    <div class="mt-3 text-center">
        <a href="register.php" style="color:#ADFF2F;">← Start over</a>
    </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── OTP BOX NAVIGATION ──
function moveNext(input, nextIndex) {
    input.value = input.value.replace(/[^0-9]/g, '');
    if(input.value.length === 1 && nextIndex < 6)
        document.querySelectorAll('.otp-box')[nextIndex].focus();
}
function movePrev(event, input, prevIndex) {
    if(event.key === 'Backspace' && input.value === '' && prevIndex >= 0)
        document.querySelectorAll('.otp-box')[prevIndex].focus();
}

// ── TIMER ──
var regTimerInterval = null;
function startRegTimer(seconds) {
    clearInterval(regTimerInterval);
    var remaining = seconds;
    var timerEl = document.getElementById('reg-timer');
    var resendBtn = document.getElementById('resendBtn');
    if(resendBtn) resendBtn.disabled = true;

    regTimerInterval = setInterval(function() {
        remaining--;
        var m = Math.floor(remaining/60);
        var s = remaining % 60;
        if(timerEl) timerEl.textContent = (m<10?'0':'')+m+':'+(s<10?'0':'')+s;
        if(remaining <= 0) {
            clearInterval(regTimerInterval);
            if(timerEl) { timerEl.textContent = 'Expired'; timerEl.style.color = '#dc3545'; }
            if(resendBtn) resendBtn.disabled = false;
        }
    }, 1000);
}

// ── SEND OTP ──
function sendRegOTP() {
    var email = '<?php echo isset($_SESSION["reg_email"]) ? addslashes($_SESSION["reg_email"]) : ""; ?>';
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
            startRegTimer(300);
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

// ── VERIFY OTP ──
function verifyRegOTP() {
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
            sucBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Email verified! Creating your account...';
            clearInterval(regTimerInterval);
            setTimeout(() => document.getElementById('createAccountForm').submit(), 1200);
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
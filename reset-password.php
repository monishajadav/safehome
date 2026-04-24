<?php
include('includes/db_connect.php');

$success = "";
$error = "";
$valid_token = false;

// Check if token is provided
if(isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Verify token exists (removed expiry check for now to make testing easier)
    $sql = "SELECT * FROM users WHERE reset_token='$token'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if token is expired
        if(strtotime($user['reset_expiry']) > time()) {
            $valid_token = true;
        } else {
            $error = "This reset link has expired. It was valid until " . date('M d, Y H:i', strtotime($user['reset_expiry'])) . ". Please request a new one from the <a href='forgot-password.php'>Forgot Password</a> page.";
        }
    } else {
        $error = "Invalid reset link. Please request a new one.";
    }
} else {
    $error = "No reset token provided.";
}

// Handle password reset form submission
if(isset($_POST['submit']) && $valid_token) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    
    if(empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif(strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $update_sql = "UPDATE users SET password='$hashed_password', reset_token=NULL, reset_expiry=NULL WHERE reset_token='$token'";
        
        if(mysqli_query($conn, $update_sql)) {
            $success = "Password reset successful! You can now login with your new password.";
            $valid_token = false; // Hide form after success
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Safe & Home Foundation</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", sans-serif;
        }

        .reset-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            max-width: 450px;
            animation: fadeIn 0.8s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reset-icon {
            font-size: 4rem;
            color: #28a745;
        }

        h2 {
            color: #343a40;
            font-weight: bold;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .input-group-text {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
        }

        .back-link {
            color: #28a745;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            color: #20c997;
        }
    </style>
</head>
<body>
    <div class="reset-card card p-5 w-100">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock reset-icon"></i>
            <h2 class="mt-3">Reset Password</h2>
            <p class="text-muted">Enter your new password</p>
        </div>

        <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <div class="text-center mt-3">
                <a href="login.php" class="btn btn-success">Go to Login</a>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($valid_token && !$success): ?>
        <form method="POST" action="" id="resetForm">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                </div>
                <div class="invalid-feedback" id="passwordMismatch" style="display: none;">
                    Passwords do not match!
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-success w-100 py-2">
                <i class="bi bi-check-circle"></i> Reset Password
            </button>
        </form>
        <?php endif; ?>

        <div class="mt-4 text-center">
            <a href="forgot-password.php" class="back-link">
                <i class="bi bi-arrow-left"></i> Request New Reset Link
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password match validation
        const form = document.getElementById('resetForm');
        if(form) {
            const newPass = document.getElementById('new_password');
            const confirmPass = document.getElementById('confirm_password');
            const mismatchMsg = document.getElementById('passwordMismatch');

            confirmPass.addEventListener('input', function() {
                if(confirmPass.value !== newPass.value) {
                    mismatchMsg.style.display = 'block';
                    confirmPass.setCustomValidity("Passwords don't match");
                } else {
                    mismatchMsg.style.display = 'none';
                    confirmPass.setCustomValidity("");
                }
            });
        }
    </script>
</body>
</html>
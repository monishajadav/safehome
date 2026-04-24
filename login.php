<?php
session_start();
include('includes/db_connect.php');

$error = ""; //empty variable to store error message

//check login form is submitted
if (isset($_POST['submit'])) {
  //username as input,trim removes space,my_sqli_escape_string-protect from sql injection
  $username = mysqli_real_escape_string($conn, trim($_POST['username']));
  $password = $_POST['password'];

  // Validation
  if (empty($username) || empty($password)) {
    $error = "Please fill in all fields.";
  } else {
    // Check if user exists (can login with username OR email)
    $sql = "SELECT * FROM users WHERE username='$username' OR email='$username'";
    //run sql query
    $result = mysqli_query($conn, $sql);
    //check weather the  user is exist 
    if (mysqli_num_rows($result) == 1) {
      //fetch user data as array
      $user = mysqli_fetch_assoc($result);

      // Verify password
      if (password_verify($password, $user['password'])) {
        // Login successful - create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;

        // Redirect to index
        if (isset($_GET['redirect'])) {
          header("Location: " . $_GET['redirect']);
        } else {
          header("Location: index.php");
        }
        exit(); //stop
      } else {
        $error = "Incorrect password. Please try again.";
      }
    } else {
      $error = "User not found. Please check your username/email.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- //support all charecter -->
  <meta charset="UTF-8" />
  <!-- //make responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Safe & Home Foundation</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
    crossorigin="anonymous" />

  <!-- Bootstrap Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    rel="stylesheet" />

  <style>
    body {
      background-image: url("./images/login.png");
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* Card styling */
    .login-card {
      background-color: rgba(0, 0, 0, 0.4);
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
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

    /* Form label and heading colors */
    .form-container h2 {
      color: white;
      font-weight: 900;
      text-shadow: 2px 2px 6px black;
    }

    .form-container .form-label {
      color: #f5f5f5;
      font-weight: 700 !important;
      text-shadow: 1px 1px 3px #000;
    }

    /* Input styling */
    .form-control {
      background-color: rgba(255, 255, 255, 0.9);
      border: none;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      background-color: rgba(255, 255, 255, 1);
      box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }

    .input-group-text {
      background-color: #28a745;
      color: white;
      border: none;
    }

    /* Register link styling */
    .form-container a {
      color: #adff2f;
      font-weight: 700 !important;
      text-shadow: 1px 1px 2px #000;
    }

    .form-container .fw-semibold {
      color: #4caf50 !important;
      text-shadow: 1px 1px 2px #000;
    }

    .brand-icon {
      font-size: 3rem;
      color: white;
      text-shadow: 2px 2px 5px black;
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      border: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
    }
  </style>
</head>

<body>
  <!-- Center the login form on screen-->
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <!-- login card-->
    <div class="login-card card -p-5 w-100 form-container" style="max-width: 420px;">
      <div class="text-center mb-4">
        <!-- icon--> <i class="bi bi-person brand-icon"></i>
        <h2 class="fw-bold mt-2">Sign In</h2>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : ''; ?>">
        <div class="mb-3">
          <label for="loginEmail" class="form-label">Email or Username</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input
              type="text"
              class="form-control"
              id="loginEmail"
              placeholder="Enter email or username"
              name="username"
              required />
          </div>
        </div>

        <div class="mb-3">
          <label for="loginPassword" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input
              type="password"
              class="form-control"
              id="loginPassword"
              placeholder="Password"
              name="password"
              required />
          </div>
        </div>
        <div class="mt-3">
          <a href="forgot-password.php" class="text-decoration-none" style="color: #adff2f;">
            Forgot Password?
          </a>
        </div>


        <button type="submit" name="submit" class="btn btn-success w-100 py-2 mt-2">
          Login
        </button>
      </form>

      <div class="mt-3 text-center">
        <a href="register.php" class="text-decoration-none">
          Don't have an account?
          <span class="fw-semibold">Register</span>
        </a>
      </div>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
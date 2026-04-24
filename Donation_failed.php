<?php
session_start();
$error_msg = $_SESSION['donation_error'] ?? 'Payment could not be completed. Please try again.';
unset($_SESSION['donation_error']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment Failed | Safe & Home Foundation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(135deg, #fce4ec, #ffcdd2); min-height: 100vh; display: flex; align-items: center; }
    .card { border-radius: 25px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.12); }
    .x-circle { width: 100px; height: 100px; background: linear-gradient(135deg, #c62828, #e53935); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .btn-retry { background: linear-gradient(135deg, #198754, #20c997); color: white; border: none; border-radius: 50px; padding: 12px 40px; font-weight: 700; }
    .btn-retry:hover { color: white; transform: translateY(-2px); }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-5 text-center">
          <div class="x-circle">
            <i class="bi bi-x-lg text-white" style="font-size: 3rem;"></i>
          </div>
          <h2 class="fw-bold" style="color:#c62828;">Payment Failed</h2>
          <p class="text-muted mb-4"><?php echo htmlspecialchars($error_msg); ?></p>
          <a href="donate.php" class="btn btn-retry">
            <i class="bi bi-arrow-clockwise me-2"></i>Try Again
          </a>
          <a href="contact.php" class="btn btn-outline-secondary mt-3" style="border-radius:50px;">
            <i class="bi bi-envelope me-2"></i>Contact Support
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php
// thank_you.php
session_start();

$name   = htmlspecialchars($_GET['name']   ?? 'Donor');
$amount = htmlspecialchars($_GET['amount'] ?? '0');
$dtype  = htmlspecialchars($_GET['dtype']  ?? 'General Fund');
$pay_id = htmlspecialchars($_GET['id']     ?? '');

$formatted_amount = '₹' . number_format((float)$amount, 2);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thank You | Safe & Home Foundation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 20px;
    }
    .thank-card {
      background: white;
      border-radius: 30px;
      padding: 60px 50px;
      max-width: 600px;
      width: 100%;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0,0,0,0.12);
      animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    @keyframes popIn {
      from { opacity: 0; transform: scale(0.85); }
      to   { opacity: 1; transform: scale(1); }
    }
    .check-circle {
      width: 110px;
      height: 110px;
      background: linear-gradient(135deg, #198754, #20c997);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 30px;
      box-shadow: 0 10px 30px rgba(25,135,84,0.35);
      animation: bounceIn 0.8s 0.2s both;
    }
    @keyframes bounceIn {
      0%   { transform: scale(0); opacity: 0; }
      60%  { transform: scale(1.15); }
      100% { transform: scale(1); opacity: 1; }
    }
    .check-circle i { color: white; font-size: 3rem; }
    h1 { font-size: 2.5rem; font-weight: 900; color: #198754; margin-bottom: 10px; }
    .donor-name { font-size: 1.2rem; color: #555; margin-bottom: 30px; }
    .amount-badge {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      font-size: 2rem;
      font-weight: 800;
      padding: 15px 40px;
      border-radius: 50px;
      display: inline-block;
      margin-bottom: 30px;
      box-shadow: 0 8px 25px rgba(25,135,84,0.3);
    }
    .detail-box {
      background: #f0fdf4;
      border: 1px solid #d1fae5;
      border-radius: 15px;
      padding: 20px 30px;
      margin-bottom: 30px;
      text-align: left;
    }
    .detail-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px dashed #d1fae5;
      font-size: 0.9rem;
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: #666; font-weight: 500; }
    .detail-value { color: #198754; font-weight: 700; }
    .quote-box {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      border-left: 4px solid #198754;
      border-radius: 0 15px 15px 0;
      padding: 20px 25px;
      margin-bottom: 35px;
      font-style: italic;
      color: #444;
    }
    .btn-home {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 14px 40px;
      font-size: 1rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-block;
      margin: 5px;
      transition: all 0.3s;
      box-shadow: 0 5px 20px rgba(25,135,84,0.3);
    }
    .btn-home:hover { transform: translateY(-3px); color: white; box-shadow: 0 10px 30px rgba(25,135,84,0.4); }
    .btn-outline-green {
      background: white;
      color: #198754;
      border: 2px solid #198754;
      border-radius: 50px;
      padding: 14px 40px;
      font-size: 1rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-block;
      margin: 5px;
      transition: all 0.3s;
    }
    .btn-outline-green:hover { background: #198754; color: white; transform: translateY(-3px); }
    .confetti-emoji { font-size: 2rem; animation: spin 2s linear infinite; display: inline-block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    @media (max-width: 576px) {
      .thank-card { padding: 40px 25px; }
      h1 { font-size: 1.8rem; }
      .amount-badge { font-size: 1.5rem; padding: 12px 30px; }
    }
  </style>
</head>
<body>

<div class="thank-card">

  <!-- Success Icon -->
  <div class="check-circle">
    <i class="bi bi-check-lg"></i>
  </div>

  <!-- Headline -->
  <h1>Thank You! <span class="confetti-emoji">🎉</span></h1>
  <p class="donor-name">Dear <strong><?php echo $name; ?></strong>, your donation has been received!</p>

  <!-- Amount -->
  <div class="amount-badge">
    <?php echo $formatted_amount; ?> Donated
  </div>

  <!-- Details -->
  <div class="detail-box">
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-heart-fill me-2 text-success"></i>Cause</span>
      <span class="detail-value"><?php echo $dtype; ?></span>
    </div>
    <?php if ($pay_id): ?>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-receipt me-2 text-success"></i>Payment ID</span>
      <span class="detail-value" style="font-size:0.8rem;"><?php echo $pay_id; ?></span>
    </div>
    <?php endif; ?>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-calendar-check me-2 text-success"></i>Date</span>
      <span class="detail-value"><?php echo date('d M Y, h:i A'); ?></span>
    </div>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-shield-check me-2 text-success"></i>Status</span>
      <span class="detail-value">✅ Confirmed</span>
    </div>
  </div>

  <!-- Inspirational quote -->
  <div class="quote-box">
    <i class="bi bi-quote me-2 text-success fs-4"></i>
    "No act of kindness, no matter how small, is ever wasted."
    <br><small style="color:#666;">— Aesop</small>
  </div>

  <!-- Action Buttons -->
  <div class="mb-3">
    <a href="index.php" class="btn-home"><i class="bi bi-house-fill me-2"></i>Back to Home</a>
    <a href="donate.php" class="btn-outline-green"><i class="bi bi-heart-fill me-2"></i>Donate Again</a>
  </div>

  <p style="color:#aaa; font-size:0.82rem; margin-top:20px;">
    A confirmation will be sent to your email. Thank you for supporting Safe & Home Foundation.
  </p>

</div>

</body>
</html>
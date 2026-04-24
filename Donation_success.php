<?php
session_start();

if (!isset($_SESSION['donation_success'])) {
    header("Location: donate.php");
    exit();
}

$data       = $_SESSION['donation_success'];
$name       = htmlspecialchars($data['name']);
$email      = htmlspecialchars($data['email'] ?? '');
$amount     = number_format((float)$data['amount'], 2);
$dtype      = htmlspecialchars($data['dtype']);
$payment_id = htmlspecialchars($data['payment_id']);

unset($_SESSION['donation_success']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment Successful | Safe & Home Foundation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    body { background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 40px 20px; overflow-x: hidden; }
    .success-card { background: white; border-radius: 30px; padding: 60px 50px; box-shadow: 0 20px 60px rgba(0,0,0,0.12); max-width: 580px; width: 100%; text-align: center; animation: popIn 0.6s cubic-bezier(0.175,0.885,0.32,1.275); position: relative; z-index: 1; }
    @keyframes popIn { from { opacity:0; transform:scale(0.8); } to { opacity:1; transform:scale(1); } }
    .check-circle { width:100px; height:100px; border-radius:50%; background:linear-gradient(135deg,#198754,#20c997); display:flex; align-items:center; justify-content:center; margin:0 auto 30px; box-shadow:0 10px 30px rgba(25,135,84,0.4); animation:checkPop 0.8s ease 0.3s both; }
    @keyframes checkPop { from{transform:scale(0);} to{transform:scale(1);} }
    .check-circle i { font-size:3rem; color:white; }
    h2 { font-size:2rem; font-weight:800; color:#198754; margin-bottom:8px; }
    .subtitle { color:#666; font-size:1rem; margin-bottom:30px; }
    .detail-box { background:#f8fffe; border:1px solid #c8e6c9; border-radius:16px; padding:24px; margin-bottom:20px; text-align:left; }
    .detail-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px dashed #e0f2e9; font-size:0.93rem; }
    .detail-row:last-child { border-bottom:none; font-weight:700; color:#198754; font-size:1.05rem; }
    .detail-label { color:#666; font-weight:500; }
    .detail-value { font-weight:600; color:#2d2d2d; }
    .amount-val { color:#198754; font-size:1.3rem; font-weight:800; }
    .payment-id-box { background:#e8f5e9; border-radius:10px; padding:12px 16px; font-size:0.8rem; color:#555; word-break:break-all; margin-bottom:24px; }
    .payment-id-box strong { color:#198754; display:block; margin-bottom:4px; }
    .confetti-msg { background:linear-gradient(135deg,#198754,#20c997); color:white; border-radius:14px; padding:20px; margin-bottom:24px; font-size:0.95rem; line-height:1.6; }
    .btn-home { background:linear-gradient(135deg,#198754,#20c997); color:white; border:none; border-radius:50px; padding:14px 40px; font-weight:700; font-size:1rem; text-decoration:none; display:inline-block; transition:all 0.3s; margin:6px; }
    .btn-home:hover { transform:translateY(-3px); box-shadow:0 10px 25px rgba(25,135,84,0.4); color:white; }
    .btn-donate-again { background:transparent; color:#198754; border:2px solid #198754; border-radius:50px; padding:13px 35px; font-weight:700; font-size:1rem; text-decoration:none; display:inline-block; transition:all 0.3s; margin:6px; }
    .btn-donate-again:hover { background:#198754; color:white; }
    .hearts { position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:0; overflow:hidden; }
    .heart { position:absolute; animation:floatUp 4s ease-in infinite; opacity:0; }
    @keyframes floatUp { 0%{opacity:1;transform:translateY(100vh) scale(1);} 100%{opacity:0;transform:translateY(-10vh) scale(0.5);} }
    @media(max-width:576px){ .success-card{padding:40px 20px;} h2{font-size:1.6rem;} }
  </style>
</head>
<body>

<div class="hearts" id="hearts"></div>

<div class="success-card">
  <div class="check-circle">
    <i class="bi bi-check-lg"></i>
  </div>

  <h2>🎉 Thank You, <?php echo $name; ?>!</h2>
  <p class="subtitle">Your donation was received successfully. You are making a real difference!</p>

  <div class="detail-box">
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-person-fill me-2 text-success"></i>Donor Name</span>
      <span class="detail-value"><?php echo $name; ?></span>
    </div>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-envelope-fill me-2 text-success"></i>Email</span>
      <span class="detail-value"><?php echo $email; ?></span>
    </div>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-heart-fill me-2 text-success"></i>Donated For</span>
      <span class="detail-value"><?php echo $dtype; ?></span>
    </div>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-calendar-check me-2 text-success"></i>Date & Time</span>
      <span class="detail-value"><?php echo date('d M Y, h:i A'); ?></span>
    </div>
    <div class="detail-row">
      <span class="detail-label"><i class="bi bi-currency-rupee me-2 text-success"></i>Amount Donated</span>
      <span class="detail-value amount-val">₹<?php echo $amount; ?></span>
    </div>
  </div>

  <div class="payment-id-box">
    <strong><i class="bi bi-shield-check-fill me-1"></i>Payment Reference ID:</strong>
    <?php echo $payment_id; ?>
  </div>

  <div class="confetti-msg">
    <i class="bi bi-stars me-2"></i>
    <strong>Your generosity matters!</strong><br>
    Your donation of <strong>₹<?php echo $amount; ?></strong> will directly support
    orphan children and elderly people at Safe & Home Foundation.
    Thank you for spreading love and hope! ❤️
  </div>

  <div>
    <a href="index.php" class="btn-home"><i class="bi bi-house-fill me-2"></i>Back to Home</a>
    <a href="donate.php" class="btn-donate-again"><i class="bi bi-heart-fill me-2"></i>Donate Again</a>
  </div>
</div>

<script>
const heartsContainer = document.getElementById('hearts');
const emojis = ['❤️','🧡','💛','💚','💙','💜','🤍'];
for (let i = 0; i < 20; i++) {
  const heart = document.createElement('div');
  heart.classList.add('heart');
  heart.textContent = emojis[Math.floor(Math.random() * emojis.length)];
  heart.style.left = Math.random() * 100 + 'vw';
  heart.style.animationDelay = Math.random() * 4 + 's';
  heart.style.animationDuration = (3 + Math.random() * 3) + 's';
  heart.style.fontSize = (1 + Math.random() * 1.5) + 'rem';
  heartsContainer.appendChild(heart);
}
</script>
</body>
</html>
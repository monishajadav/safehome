<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username  = $is_logged_in ? $_SESSION['username'] : '';
$user_email = $is_logged_in ? ($_SESSION['email']   ?? '') : '';
$user_phone = $is_logged_in ? ($_SESSION['phone']   ?? '') : '';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Donate | Safe & Home Foundation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      overflow-x: hidden;
    }

    .navbar {
      padding: 0.75rem 0;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .navbar-brand img {
      height: 80px;
    }

    .nav-link {
      font-size: 0.95rem;
      font-weight: 500;
      padding: 0.5rem 1.3rem !important;
      transition: color 0.2s;
    }

    .nav-link:hover {
      color: #ffc107 !important;
    }

    .btn {
      border-radius: 50px;
      padding: 12px 35px;
      font-weight: 600;
      transition: 0.3s ease;
    }

    .btn-success {
      background: #1f7a4c;
      border: none;
    }

    .btn-success:hover {
      background: #14532d;
      transform: translateY(-3px);
    }

    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("./images/donation.jpg");
      min-height: 90vh;
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      display: flex;
      align-items: center;
      color: #fff;
      text-align: center;
    }

    .hero h1 {
      font-size: 3.8rem;
      font-weight: 900;
      text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.5);
      animation: fadeInUp 1s ease-out;
    }

    .hero p {
      font-size: 1.4rem;
      text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
      animation: fadeInUp 1.3s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .section-green {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      padding: 80px 0;
    }

    .section-title {
      font-size: 2.3rem;
      font-weight: 800;
      color: #198754;
      position: relative;
      display: inline-block;
      margin-bottom: 2.5rem;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 70px;
      height: 4px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 2px;
    }

    .amount-btn {
      background: white;
      border: 2px solid #198754;
      color: #198754;
      border-radius: 50px;
      padding: 10px 25px;
      font-weight: 700;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .amount-btn:hover,
    .amount-btn.active {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
    }

    .impact-card {
      background: white;
      border-radius: 20px;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      height: 100%;
    }

    .impact-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(25, 135, 84, 0.2);
    }

    .impact-icon {
      width: 75px;
      height: 75px;
      border-radius: 50%;
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 2rem;
      color: #198754;
      transition: all 0.3s ease;
    }

    .impact-card:hover .impact-icon {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
    }

    .impact-card h5 {
      font-weight: 700;
      color: #198754;
    }

    .impact-amount {
      font-size: 1.5rem;
      font-weight: 800;
      color: #198754;
    }

    .form-card {
      background: white;
      border-radius: 25px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
    }

    .form-control,
    .form-select {
      border-radius: 10px;
      border: 2px solid #e0e0e0;
      padding: 12px 15px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
    }

    .form-label {
      font-weight: 600;
      color: #444;
      margin-bottom: 8px;
    }

    .input-group-text {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      border: none;
      border-radius: 10px 0 0 10px;
    }

    .btn-submit {
      background: linear-gradient(135deg, #198754, #20c997);
      border: none;
      border-radius: 50px;
      padding: 15px 50px;
      font-size: 1.1rem;
      font-weight: 700;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 5px 20px rgba(25, 135, 84, 0.4);
      width: 100%;
    }

    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(25, 135, 84, 0.5);
      color: white;
    }

    .cta-section {
      background: linear-gradient(135deg, #198754, #20c997);
      padding: 80px 0;
      text-align: center;
      color: white;
    }

    .btn-cta {
      background: white;
      color: #198754;
      border: none;
      border-radius: 50px;
      padding: 15px 40px;
      font-weight: 700;
      font-size: 1.1rem;
      transition: all 0.3s ease;
    }

    .btn-cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      color: #198754;
    }

    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.8s ease;
    }

    .fade-in.show {
      opacity: 1;
      transform: translateY(0);
    }

    footer {
      background: #198754;
      color: white;
    }

    footer a {
      transition: all 0.3s ease;
    }

    footer a:hover {
      color: #ffd700 !important;
    }

    /* STEPS */
    .payment-steps {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 40px;
    }

    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
      max-width: 140px;
    }

    .step-circle {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: #e0e0e0;
      color: #999;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
      z-index: 1;
      transition: all 0.3s;
    }

    .step-item.active .step-circle {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      box-shadow: 0 4px 15px rgba(25, 135, 84, 0.4);
    }

    .step-item.done .step-circle {
      background: #198754;
      color: white;
    }

    .step-label {
      font-size: 0.75rem;
      font-weight: 600;
      color: #999;
      margin-top: 6px;
      text-align: center;
    }

    .step-item.active .step-label,
    .step-item.done .step-label {
      color: #198754;
    }

    .step-line {
      height: 3px;
      flex: 1;
      background: #e0e0e0;
      margin-top: -22px;
    }

    .step-line.done {
      background: #198754;
    }

    /* REVIEW */
    .review-box {
      background: #f8f9fa;
      border-radius: 15px;
      padding: 25px;
      border: 1px solid #e0e0e0;
      margin-bottom: 20px;
    }

    .review-row {
      display: flex;
      justify-content: space-between;
      padding: 10px 0;
      border-bottom: 1px solid #e9ecef;
      font-size: 0.95rem;
    }

    .review-row:last-child {
      border-bottom: none;
      font-weight: 700;
      color: #198754;
      font-size: 1.1rem;
    }

    .review-label {
      color: #666;
    }

    /* PAYMENT MODAL */
    .pay-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.6);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(4px);
    }

    .pay-overlay.open {
      display: flex;
    }

    .pay-modal {
      background: #fff;
      border-radius: 20px;
      width: 100%;
      max-width: 440px;
      margin: 20px;
      overflow: hidden;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
      animation: slideUp 0.4s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .pay-header {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      padding: 20px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .pay-header-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .pay-logo {
      width: 40px;
      height: 40px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pay-logo i {
      color: #198754;
      font-size: 1.2rem;
    }

    .pay-header h6 {
      margin: 0;
      font-weight: 700;
      font-size: 1rem;
    }

    .pay-header small {
      opacity: 0.85;
      font-size: 0.8rem;
    }

    .pay-close {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }

    .pay-close:hover {
      background: rgba(255, 255, 255, 0.35);
    }

    .pay-amount-bar {
      background: #f0fdf4;
      border-bottom: 1px solid #d1fae5;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .pay-amount-bar span {
      color: #555;
      font-size: 0.88rem;
    }

    .pay-amount-bar strong {
      color: #198754;
      font-size: 1.3rem;
      font-weight: 800;
    }

    .pay-tabs {
      display: flex;
      border-bottom: 2px solid #eee;
    }

    .pay-tab {
      flex: 1;
      padding: 12px 8px;
      text-align: center;
      font-size: 0.82rem;
      font-weight: 600;
      color: #888;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
      transition: all 0.2s;
    }

    .pay-tab.active {
      color: #198754;
      border-bottom-color: #198754;
    }

    .pay-tab i {
      display: block;
      font-size: 1.1rem;
      margin-bottom: 3px;
    }

    .pay-body {
      padding: 24px;
      min-height: 200px;
    }

    .upi-input-wrap {
      position: relative;
      margin-bottom: 16px;
    }

    .upi-input-wrap input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 0.95rem;
      font-family: 'Poppins', sans-serif;
      transition: border-color 0.2s;
    }

    .upi-input-wrap input:focus {
      outline: none;
      border-color: #198754;
    }

    .upi-apps {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .upi-app {
      background: #f8f9fa;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 8px 14px;
      font-size: 0.82rem;
      font-weight: 600;
      color: #444;
      cursor: pointer;
      transition: all 0.2s;
    }

    .upi-app:hover,
    .upi-app.selected {
      border-color: #198754;
      color: #198754;
      background: #f0fdf4;
    }

    .card-preview {
      background: linear-gradient(135deg, #198754, #20c997);
      border-radius: 14px;
      padding: 20px;
      color: white;
      margin-bottom: 16px;
      position: relative;
      overflow: hidden;
    }

    .card-preview::before {
      content: '';
      position: absolute;
      top: -30px;
      right: -30px;
      width: 120px;
      height: 120px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
    }

    .card-preview::after {
      content: '';
      position: absolute;
      bottom: -40px;
      left: -20px;
      width: 100px;
      height: 100px;
      background: rgba(255, 255, 255, 0.07);
      border-radius: 50%;
    }

    .card-chip {
      width: 34px;
      height: 26px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 5px;
      margin-bottom: 16px;
    }

    .card-num {
      font-size: 1.05rem;
      letter-spacing: 3px;
      font-weight: 600;
      margin-bottom: 12px;
    }

    .card-bottom {
      display: flex;
      justify-content: space-between;
      font-size: 0.8rem;
      opacity: 0.85;
    }

    .pay-input {
      width: 100%;
      padding: 11px 14px;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      font-size: 0.9rem;
      margin-bottom: 12px;
      font-family: 'Poppins', sans-serif;
      transition: border-color 0.2s;
    }

    .pay-input:focus {
      outline: none;
      border-color: #198754;
    }

    .pay-input-row {
      display: flex;
      gap: 10px;
    }

    .pay-input-row .pay-input {
      flex: 1;
    }

    .bank-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 16px;
    }

    .bank-btn {
      background: #f8f9fa;
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 12px 10px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.82rem;
      font-weight: 600;
      color: #444;
    }

    .bank-btn:hover,
    .bank-btn.selected {
      border-color: #198754;
      color: #198754;
      background: #f0fdf4;
    }

    .bank-btn i {
      display: block;
      font-size: 1.4rem;
      margin-bottom: 4px;
    }

    .pay-btn {
      width: 100%;
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 14px;
      font-size: 1rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
      font-family: 'Poppins', sans-serif;
      box-shadow: 0 4px 15px rgba(25, 135, 84, 0.35);
    }

    .pay-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(25, 135, 84, 0.45);
    }

    .processing-screen {
      display: none;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
      text-align: center;
    }

    .processing-screen.show {
      display: flex;
    }

    .spinner {
      width: 60px;
      height: 60px;
      border: 5px solid #e0e0e0;
      border-top-color: #198754;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin-bottom: 20px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .pay-footer {
      padding: 12px 24px;
      background: #f8f9fa;
      border-top: 1px solid #eee;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .pay-footer span {
      font-size: 0.78rem;
      color: #999;
    }

    .pay-footer i {
      color: #198754;
    }

    @media (max-width: 768px) {
      .hero h1 {
        font-size: 2.5rem;
      }

      .form-card {
        padding: 25px 15px;
      }
    }
  </style>
</head>

<body>

  <?php include "./includes/navbar.php" ?>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <h1>Make a Difference Today</h1>
      <p class="mt-3 px-md-5">"Your generosity can change a life.<br>Every rupee donated brings hope to someone in need."</p>
      <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
        <a href="#donate-form" class="btn btn-success btn-lg px-5"><i class="bi bi-heart-fill"></i> Donate Now</a>
        <a href="#impact" class="btn btn-outline-light btn-lg px-5"><i class="bi bi-arrow-down-circle"></i> See Impact</a>
      </div>
    </div>
  </section>

  <!-- IMPACT -->
  <section class="section-green" id="impact">
    <div class="container text-center">
      <h2 class="section-title">Your Donation Impact</h2>
      <p class="lead mb-5" style="color:#555;">See how your contribution makes a real difference</p>
      <div class="row g-4">
        <div class="col-md-3 col-sm-6 fade-in">
          <div class="impact-card">
            <div class="impact-icon"><i class="bi bi-basket-fill"></i></div>
            <div class="impact-amount">₹500</div>
            <h5 class="mt-2">Feeds a Child</h5>
            <p style="color:#666; font-size:0.9rem;">Nutritious meals for an orphaned child for one week.</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 fade-in">
          <div class="impact-card">
            <div class="impact-icon"><i class="bi bi-hospital-fill"></i></div>
            <div class="impact-amount">₹1,000</div>
            <h5 class="mt-2">Medical Care</h5>
            <p style="color:#666; font-size:0.9rem;">Basic medical checkup and medicines for an elder.</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 fade-in">
          <div class="impact-card">
            <div class="impact-icon"><i class="bi bi-book-fill"></i></div>
            <div class="impact-amount">₹2,500</div>
            <h5 class="mt-2">Education</h5>
            <p style="color:#666; font-size:0.9rem;">School supplies and books for a child for one month.</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 fade-in">
          <div class="impact-card">
            <div class="impact-icon"><i class="bi bi-house-heart-fill"></i></div>
            <div class="impact-amount">₹5,000</div>
            <h5 class="mt-2">Shelter Support</h5>
            <p style="color:#666; font-size:0.9rem;">Shelter maintenance for elders for one month.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- DONATION FORM -->
  <section class="section-green" id="donate-form">
    <div class="container">
      <h2 class="section-title text-center">Make Your Donation</h2>
      <p class="text-center mb-5" style="color:#555; font-size:1.05rem;">Every contribution, big or small, makes a difference.</p>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="form-card">

            <!-- PROGRESS STEPS -->
            <div class="payment-steps">
              <div class="step-item active" id="step1-ind">
                <div class="step-circle">1</div>
                <div class="step-label">Your Details</div>
              </div>
              <div class="step-line" id="line1"></div>
              <div class="step-item" id="step2-ind">
                <div class="step-circle">2</div>
                <div class="step-label">Review</div>
              </div>
              <div class="step-line" id="line2"></div>
              <div class="step-item" id="step3-ind">
                <div class="step-circle">3</div>
                <div class="step-label">Verify OTP</div>
              </div>
              <div class="step-line" id="line3"></div>
              <div class="step-item" id="step4-ind">
                <div class="step-circle"><i class="bi bi-lock-fill" style="font-size:0.85rem;"></i></div>
                <div class="step-label">Pay Securely</div>
              </div>
            </div>

            <!-- STEP 1: FORM -->
            <div id="step1">
              <div class="mb-4 text-center">
                <h6 class="fw-bold mb-3" style="color:#444;">Quick Select Amount:</h6>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                  <button type="button" class="amount-btn" onclick="setAmount(500,this)">₹500</button>
                  <button type="button" class="amount-btn" onclick="setAmount(1000,this)">₹1,000</button>
                  <button type="button" class="amount-btn" onclick="setAmount(2500,this)">₹2,500</button>
                  <button type="button" class="amount-btn" onclick="setAmount(5000,this)">₹5,000</button>
                  <button type="button" class="amount-btn" onclick="setAmount(10000,this)">₹10,000</button>
                </div>
              </div>

              <div id="form-error" class="alert alert-danger d-none"></div>

              <div class="row g-4">
                <div class="col-md-6">
                  <label class="form-label">Full Name *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <input type="text" id="full_name" class="form-control" placeholder="Your full name"
                      value="<?php echo htmlspecialchars($username); ?>">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email Address *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" id="email" class="form-control" placeholder="Your email"
                      value="<?php echo htmlspecialchars($user_email); ?>">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone Number *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                    <input type="tel" id="phone" class="form-control" placeholder="Your phone number"
                      value="<?php echo htmlspecialchars($user_phone); ?>">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Donation Amount (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-currency-rupee"></i></span>
                    <input type="number" id="amountInput" class="form-control" placeholder="Enter amount" min="1">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Donate For *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-heart-fill"></i></span>
                    <select id="donation_type" class="form-select">
                      <option value="">Select cause</option>
                      <option value="Elder Care">Elder Care</option>
                      <option value="Child Welfare">Child Welfare</option>
                      <option value="Education">Education</option>
                      <option value="Healthcare">Healthcare</option>
                      <option value="Food & Nutrition">Food & Nutrition</option>
                      <option value="General Fund">General Fund</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Payment Method *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-credit-card-fill"></i></span>
                    <select id="payment_method" class="form-select">
                      <option value="">Select method</option>
                      <option value="UPI">UPI</option>
                      <option value="Net Banking">Net Banking</option>
                      <option value="Credit Card">Credit Card</option>
                      <option value="Debit Card">Debit Card</option>
                    </select>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label">Message (Optional)</label>
                  <textarea id="message" class="form-control" rows="3" placeholder="Leave a message of hope..."></textarea>
                </div>
                <div class="col-12 text-center mt-3">
                  <button type="button" class="btn-submit btn" onclick="goToReview()">
                    <i class="bi bi-arrow-right-circle-fill me-2"></i>Proceed to Review
                  </button>
                </div>
              </div>
            </div>

            <!-- STEP 2: REVIEW -->
            <div id="step2" class="d-none">
              <h5 class="fw-bold mb-4 text-center" style="color:#198754;">
                <i class="bi bi-clipboard-check-fill me-2"></i>Review Your Donation
              </h5>
              <div class="review-box">
                <div class="review-row"><span class="review-label">Full Name</span><span id="r-name"></span></div>
                <div class="review-row"><span class="review-label">Email</span><span id="r-email"></span></div>
                <div class="review-row"><span class="review-label">Phone</span><span id="r-phone"></span></div>
                <div class="review-row"><span class="review-label">Donating For</span><span id="r-type"></span></div>
                <div class="review-row"><span class="review-label">Payment Method</span><span id="r-method"></span></div>
                <div class="review-row"><span class="review-label">Message</span><span id="r-message" style="font-style:italic;max-width:200px;text-align:right;"></span></div>
                <div class="review-row"><span class="review-label">💰 Total Amount</span><span id="r-amount"></span></div>
              </div>
              <div class="alert border-0 rounded-3 mb-4" style="background:#e8f5e9;">
                <i class="bi bi-shield-check-fill text-success me-2"></i>
                <strong>Secure Payment:</strong> Your payment is processed safely. No card details stored on our server.
              </div>
              <div class="d-flex gap-3">
                <button type="button" class="btn btn-outline-secondary w-50 py-3" onclick="goBack()">
                  <i class="bi bi-arrow-left-circle me-2"></i>Go Back
                </button>
                <button type="button" class="btn-submit btn w-50" onclick="goToOTP()">
                  <i class="bi bi-phone-fill me-2"></i>Verify with OTP
                </button>
              </div>
            </div>

<!-- STEP 3: OTP VERIFICATION -->
<div id="step3" class="d-none">

  <div class="text-center mb-4">
    <div style="width:80px;height:80px;background:linear-gradient(135deg,#e8f5e9,#c8e6c9);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <i class="bi bi-phone-fill text-success" style="font-size:2rem;"></i>
    </div>
    <h5 class="fw-bold" style="color:#198754;">Verify Your Email Address</h5>
    <p class="text-muted" style="font-size:0.9rem;">We will send an OTP to your email to confirm your identity</p>
  </div>

  <div id="otp-error" class="alert alert-danger d-none"></div>
  <div id="otp-success" class="alert alert-success d-none"></div>

  <div id="phone-section">
    <label class="form-label fw-bold">Confirm Your Email Address</label>
    <div class="input-group mb-2">
      <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
      <input type="email" id="otp-phone" class="form-control" placeholder="Enter your email address">
      <button class="btn btn-success px-4" type="button" id="send-otp-btn" onclick="sendOTP()">
        <i class="bi bi-send-fill me-1"></i>Send OTP
      </button>
    </div>
    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>OTP will be sent to your email</small>
  </div>

  <!-- OTP INPUT (hidden initially) -->
  <div id="otp-section" class="d-none mt-4">
    <label class="form-label fw-bold">Enter OTP</label>
    <p class="text-muted" style="font-size:0.88rem;" id="otp-sent-msg"></p>

    <div class="d-flex justify-content-center gap-2 mb-3" id="otp-boxes">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" oninput="moveToNext(this,1)" onkeydown="moveToPrev(event,this,0)">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" oninput="moveToNext(this,2)" onkeydown="moveToPrev(event,this,1)">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" oninput="moveToNext(this,3)" onkeydown="moveToPrev(event,this,2)">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" oninput="moveToNext(this,4)" onkeydown="moveToPrev(event,this,3)">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" oninput="moveToNext(this,5)" onkeydown="moveToPrev(event,this,4)">
      <input type="text" class="form-control text-center fw-bold fs-5 otp-box" maxlength="1" style="width:50px;height:55px;" onkeydown="moveToPrev(event,this,5)">
    </div>

    <div class="text-center mb-3">
      <span class="text-muted" style="font-size:0.88rem;">
        <i class="bi bi-clock me-1"></i>OTP expires in: <strong id="timer" style="color:#198754;">05:00</strong>
      </span>
    </div>

    <div class="d-flex gap-3">
      <button type="button" class="btn btn-outline-secondary w-50 py-3" id="resend-btn" onclick="resendOTP()" disabled>
        <i class="bi bi-arrow-clockwise me-2"></i>Resend OTP
      </button>
      <button type="button" class="btn-submit btn w-50" onclick="verifyOTP()">
        <i class="bi bi-shield-check-fill me-2"></i>Verify & Pay
      </button>
    </div>
  </div>

  <div class="mt-3">
    <button type="button" class="btn btn-outline-secondary w-100" onclick="goBackToReview()">
      <i class="bi bi-arrow-left-circle me-2"></i>Go Back
    </button>
  </div>

</div>  <!-- end step3 -->

            </div>
          </div>
        </div>
      </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container">
      <h2 style="font-size:2.5rem; font-weight:800;">Together We Can Do More</h2>
      <p class="lead mt-3 mb-5 opacity-90">Share our mission with your friends and family.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="volunteer.php" class="btn-cta btn"><i class="bi bi-people-fill text-success me-2"></i>Volunteer With Us</a>
        <a href="contact.php" class="btn btn-outline-light btn-lg px-5" style="border-radius:50px;font-weight:700;"><i class="bi bi-envelope-fill me-2"></i>Contact Us</a>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
          <h5 class="fw-bold"><i class="bi bi-house-heart-fill"></i> Safe & Home Foundation</h5>
          <p class="mb-0">Caring for all generations with love</p>
        </div>
        <div class="col-md-4 text-center mb-3 mb-md-0">
          <h5 class="fw-bold">Quick Links</h5>
          <a href="aboutus.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> About Us</a>
          <a href="contact.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> Contact</a>
          <a href="donate.php" class="text-white d-block"><i class="bi bi-arrow-right"></i> Donate</a>
        </div>
        <div class="col-md-4 text-center text-md-end">
          <h5 class="fw-bold">Follow Us</h5>
          <a href="#" class="text-white me-3 fs-4"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-3 fs-4"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white fs-4"><i class="bi bi-twitter"></i></a>
        </div>
      </div>
      <hr class="mt-4 opacity-25">
      <p class="text-center mb-0">© 2025 Safe & Home Foundation | All Rights Reserved | Made with <i class="bi bi-heart-fill text-danger"></i> for a better tomorrow</p>
    </div>
  </footer>

  <!-- PAYMENT MODAL -->
  <div class="pay-overlay" id="payOverlay">
    <div class="pay-modal">
      <div class="pay-header">
        <div class="pay-header-left">
          <div class="pay-logo"><i class="bi bi-house-heart-fill"></i></div>
          <div>
            <h6>Safe & Home Foundation</h6>
            <small id="modal-amount-label">Amount: ₹0</small>
          </div>
        </div>
        <button class="pay-close" onclick="closePayModal()">✕</button>
      </div>
      <div class="pay-amount-bar">
        <span><i class="bi bi-shield-lock-fill text-success me-1"></i>Secure Payment</span>
        <strong id="modal-amount-display">₹0</strong>
      </div>
      <div id="pay-ui">
        <div class="pay-tabs">
          <div class="pay-tab active" id="tab-upi" onclick="switchTab('upi')"><i class="bi bi-phone-fill"></i>UPI</div>
          <div class="pay-tab" id="tab-card" onclick="switchTab('card')"><i class="bi bi-credit-card-fill"></i>Card</div>
          <div class="pay-tab" id="tab-bank" onclick="switchTab('bank')"><i class="bi bi-bank2"></i>Netbanking</div>
        </div>

        <!-- UPI -->
        <div class="pay-body" id="body-upi">
          <p style="font-size:0.85rem;color:#666;margin-bottom:12px;">Select UPI app or enter UPI ID</p>
          <div class="upi-apps">
            <div class="upi-app" onclick="selectUpiApp(this,'GPay')">📱 GPay</div>
            <div class="upi-app" onclick="selectUpiApp(this,'PhonePe')">💜 PhonePe</div>
            <div class="upi-app" onclick="selectUpiApp(this,'Paytm')">🔵 Paytm</div>
            <div class="upi-app" onclick="selectUpiApp(this,'BHIM')">🇮🇳 BHIM</div>
          </div>
          <div class="upi-input-wrap">
            <input type="text" id="upi-id" placeholder="Enter UPI ID (e.g. name@upi)" />
          </div>
          <button class="pay-btn" onclick="processPayment()">
            <i class="bi bi-lock-fill me-2"></i>Pay <span id="upi-pay-amt"></span>
          </button>
        </div>

        <!-- CARD -->
        <div class="pay-body d-none" id="body-card">
          <div class="card-preview">
            <div class="card-chip"></div>
            <div class="card-num" id="card-preview-num">•••• •••• •••• ••••</div>
            <div class="card-bottom">
              <span id="card-preview-name">CARD HOLDER</span>
              <span id="card-preview-exp">MM/YY</span>
            </div>
          </div>
          <input type="text" class="pay-input" id="card-num" placeholder="Card Number" maxlength="19" oninput="formatCard(this)" />
          <input type="text" class="pay-input" id="card-name" placeholder="Name on Card" oninput="document.getElementById('card-preview-name').textContent=this.value.toUpperCase()||'CARD HOLDER'" />
          <div class="pay-input-row">
            <input type="text" class="pay-input" id="card-exp" placeholder="MM/YY" maxlength="5" oninput="formatExp(this)" />
            <input type="text" class="pay-input" id="card-cvv" placeholder="CVV" maxlength="3" />
          </div>
          <button class="pay-btn" onclick="processPayment()">
            <i class="bi bi-lock-fill me-2"></i>Pay <span id="card-pay-amt"></span>
          </button>
        </div>

        <!-- NETBANKING -->
        <div class="pay-body d-none" id="body-bank">
          <p style="font-size:0.85rem;color:#666;margin-bottom:12px;">Select your bank</p>
          <div class="bank-grid">
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>SBI</div>
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>HDFC</div>
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>ICICI</div>
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>Axis</div>
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>Canara</div>
            <div class="bank-btn" onclick="selectBank(this)"><i class="bi bi-building"></i>Bank of Baroda</div>
          </div>
          <button class="pay-btn" onclick="processPayment()">
            <i class="bi bi-lock-fill me-2"></i>Pay <span id="bank-pay-amt"></span>
          </button>
        </div>
      </div>

      <!-- Processing -->
      <div class="processing-screen" id="processing-screen">
        <div class="spinner"></div>
        <h6 style="color:#198754;font-weight:700;">Processing Payment...</h6>
        <p style="color:#888;font-size:0.88rem;">Please wait, do not refresh the page</p>
      </div>

      <div class="pay-footer">
        <i class="bi bi-shield-lock-fill"></i>
        <span>256-bit SSL Encrypted &nbsp;|&nbsp; Safe & Home Foundation</span>
      </div>
    </div>
  </div>

  <!-- Hidden POST form -->
  <form id="payForm" method="POST" action="payment_verify.php" style="display:none;">
    <input type="hidden" name="razorpay_payment_id" id="hid_pay_id">
    <input type="hidden" name="full_name" id="hid_name">
    <input type="hidden" name="email" id="hid_email">
    <input type="hidden" name="phone" id="hid_phone">
    <input type="hidden" name="amount" id="hid_amount">
    <input type="hidden" name="donation_type" id="hid_dtype">
    <input type="hidden" name="payment_method" id="hid_dmethod">
    <input type="hidden" name="message" id="hid_msg">
  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  // ===================== AMOUNT BUTTONS =====================
  <script>
    function setAmount(amount, btn) {
      document.getElementById('amountInput').value = amount;
      document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }

    // ===================== FADE IN =====================
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) e.target.classList.add('show');
      });
    }, {
      threshold: 0.1
    });
    document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

    // ===================== STEP 1 → REVIEW =====================
    function goToReview() {
      const name = document.getElementById('full_name').value.trim();
      const email = document.getElementById('email').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const amount = document.getElementById('amountInput').value.trim();
      const dtype = document.getElementById('donation_type').value;
      const dmethod = document.getElementById('payment_method').value;
      const msg = document.getElementById('message').value.trim();
      const errBox = document.getElementById('form-error');

      if (!name || !email || !phone || !amount || !dtype || !dmethod) {
        errBox.classList.remove('d-none');
        errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Please fill in all required fields.';
        window.scrollTo({
          top: errBox.offsetTop - 100,
          behavior: 'smooth'
        });
        return;
      }
      if (isNaN(amount) || amount <= 0) {
        errBox.classList.remove('d-none');
        errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Please enter a valid amount.';
        return;
      }
      errBox.classList.add('d-none');

      document.getElementById('r-name').textContent = name;
      document.getElementById('r-email').textContent = email;
      document.getElementById('r-phone').textContent = phone;
      document.getElementById('r-type').textContent = dtype;
      document.getElementById('r-method').textContent = dmethod;
      document.getElementById('r-message').textContent = msg || '—';
      document.getElementById('r-amount').textContent = '₹' + parseFloat(amount).toLocaleString('en-IN');

      document.getElementById('step1').classList.add('d-none');
      document.getElementById('step2').classList.remove('d-none');
      document.getElementById('step1-ind').classList.replace('active', 'done');
      document.getElementById('line1').classList.add('done');
      document.getElementById('step2-ind').classList.add('active');
      window.scrollTo({
        top: document.getElementById('donate-form').offsetTop - 80,
        behavior: 'smooth'
      });
    }

    function goBack() {
      document.getElementById('step2').classList.add('d-none');
      document.getElementById('step1').classList.remove('d-none');
      document.getElementById('step1-ind').classList.remove('done');
      document.getElementById('step1-ind').classList.add('active');
      document.getElementById('line1').classList.remove('done');
      document.getElementById('step2-ind').classList.remove('active');
    }

    // ===================== STEP 2 → OTP =====================
    function goToOTP() {
      // Pre-fill email from donation form
      var email = document.getElementById('email').value.trim();
      document.getElementById('otp-phone').value = email;

      document.getElementById('step2').classList.add('d-none');
      document.getElementById('step3').classList.remove('d-none');
      document.getElementById('step2-ind').classList.replace('active', 'done');
      document.getElementById('line2').classList.add('done');
      document.getElementById('step3-ind').classList.add('active');
      window.scrollTo({
        top: document.getElementById('donate-form').offsetTop - 80,
        behavior: 'smooth'
      });
    }

    function goBackToReview() {
      document.getElementById('step3').classList.add('d-none');
      document.getElementById('step2').classList.remove('d-none');
      document.getElementById('step3-ind').classList.remove('active');
      document.getElementById('line2').classList.remove('done');
      document.getElementById('step2-ind').classList.replace('done', 'active');
      clearInterval(timerInterval);
      document.getElementById('otp-section').classList.add('d-none');
      document.getElementById('otp-error').classList.add('d-none');
      document.getElementById('otp-success').classList.add('d-none');
    }

    // ===================== EMAIL OTP =====================
    var timerInterval = null;

    function sendOTP() {
      var email = document.getElementById('otp-phone').value.trim();
      var errBox = document.getElementById('otp-error');
      var sucBox = document.getElementById('otp-success');
      var btn = document.getElementById('send-otp-btn');

      errBox.classList.add('d-none');
      sucBox.classList.add('d-none');

      // Basic email check
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errBox.classList.remove('d-none');
        errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Enter a valid email address.';
        return;
      }

      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

      fetch('send_otp.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'email=' + encodeURIComponent(email)
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'sent') {
            sucBox.classList.remove('d-none');
            sucBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>OTP sent to ' + email;
            document.getElementById('otp-sent-msg').textContent = 'OTP sent to ' + email + '. Enter below:';
            document.getElementById('otp-section').classList.remove('d-none');
            document.querySelectorAll('.otp-box').forEach(b => b.value = '');
            document.querySelectorAll('.otp-box')[0].focus();
            startTimer(300);
          } else {
            errBox.classList.remove('d-none');
            errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (data.message || 'Failed to send OTP.');
          }
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill me-1"></i>Resend';
        })
        .catch(() => {
          errBox.classList.remove('d-none');
          errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Network error. Please try again.';
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-send-fill me-1"></i>Send OTP';
        });
    }

    function resendOTP() {
      document.getElementById('otp-section').classList.add('d-none');
      document.getElementById('otp-success').classList.add('d-none');
      document.getElementById('otp-error').classList.add('d-none');
      clearInterval(timerInterval);
      document.getElementById('resend-btn').disabled = true;
      sendOTP();
    }

    function startTimer(seconds) {
      clearInterval(timerInterval);
      var remaining = seconds;
      var timerEl = document.getElementById('timer');
      var resendBtn = document.getElementById('resend-btn');
      resendBtn.disabled = true;

      timerInterval = setInterval(function() {
        remaining--;
        var mins = Math.floor(remaining / 60);
        var secs = remaining % 60;
        timerEl.textContent = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
        timerEl.style.color = '#198754';
        if (remaining <= 0) {
          clearInterval(timerInterval);
          timerEl.textContent = 'Expired';
          timerEl.style.color = '#dc3545';
          resendBtn.disabled = false;
        }
      }, 1000);
    }

    // OTP box navigation
    function moveToNext(input, nextIndex) {
      input.value = input.value.replace(/[^0-9]/g, '');
      if (input.value.length === 1 && nextIndex < 6) {
        document.querySelectorAll('.otp-box')[nextIndex].focus();
      }
    }

    function moveToPrev(event, input, prevIndex) {
      if (event.key === 'Backspace' && input.value === '' && prevIndex >= 0) {
        document.querySelectorAll('.otp-box')[prevIndex].focus();
      }
    }

    // ===================== VERIFY OTP =====================
    function verifyOTP() {
      var boxes = document.querySelectorAll('.otp-box');
      var otp = '';
      boxes.forEach(b => otp += b.value.trim());
      var errBox = document.getElementById('otp-error');
      var sucBox = document.getElementById('otp-success');

      errBox.classList.add('d-none');
      sucBox.classList.add('d-none');

      if (otp.length < 6) {
        errBox.classList.remove('d-none');
        errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>Please enter the complete 6-digit OTP.';
        return;
      }

      fetch('verify_otp.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'otp=' + encodeURIComponent(otp)
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            sucBox.classList.remove('d-none');
            sucBox.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>OTP Verified! Opening payment...';
            clearInterval(timerInterval);

            document.getElementById('step3-ind').classList.replace('active', 'done');
            document.getElementById('line3').classList.add('done');
            document.getElementById('step4-ind').classList.add('active');

            setTimeout(function() {
              document.getElementById('step3').classList.add('d-none');
              openPayModal();
            }, 1000);
          } else {
            errBox.classList.remove('d-none');
            errBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (data.message || 'Invalid OTP.');
            var otpBoxes = document.getElementById('otp-boxes');
            otpBoxes.style.transform = 'translateX(-8px)';
            setTimeout(function() {
              otpBoxes.style.transform = 'translateX(8px)';
            }, 100);
            setTimeout(function() {
              otpBoxes.style.transform = 'translateX(0)';
            }, 200);
          }
        });
    }

    // ===================== PAYMENT MODAL =====================
    function openPayModal() {
      const amount = parseFloat(document.getElementById('amountInput').value.trim());
      const fmtAmt = '₹' + amount.toLocaleString('en-IN');
      document.getElementById('modal-amount-label').textContent = 'Amount: ' + fmtAmt;
      document.getElementById('modal-amount-display').textContent = fmtAmt;
      document.getElementById('upi-pay-amt').textContent = fmtAmt;
      document.getElementById('card-pay-amt').textContent = fmtAmt;
      document.getElementById('bank-pay-amt').textContent = fmtAmt;
      document.getElementById('pay-ui').style.display = '';
      document.getElementById('processing-screen').classList.remove('show');
      switchTab('upi');
      document.getElementById('payOverlay').classList.add('open');
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    }

    function closePayModal() {
      document.getElementById('payOverlay').classList.remove('open');
      document.getElementById('step3-ind').classList.remove('done');
      document.getElementById('step3-ind').classList.add('active');
      document.getElementById('line3').classList.remove('done');
      document.getElementById('step4-ind').classList.remove('active');
      document.getElementById('step3').classList.remove('d-none');
    }

    function switchTab(tab) {
      ['upi', 'card', 'bank'].forEach(t => {
        document.getElementById('body-' + t).classList.add('d-none');
        document.getElementById('tab-' + t).classList.remove('active');
      });
      document.getElementById('body-' + tab).classList.remove('d-none');
      document.getElementById('tab-' + tab).classList.add('active');
    }

    function selectUpiApp(el, app) {
      document.querySelectorAll('.upi-app').forEach(a => a.classList.remove('selected'));
      el.classList.add('selected');
      document.getElementById('upi-id').value = '';
      document.getElementById('upi-id').placeholder = 'Using ' + app;
    }

    function selectBank(el) {
      document.querySelectorAll('.bank-btn').forEach(b => b.classList.remove('selected'));
      el.classList.add('selected');
    }

    function formatCard(input) {
      let v = input.value.replace(/\D/g, '').substring(0, 16);
      input.value = v.replace(/(.{4})/g, '$1 ').trim();
      const display = v.padEnd(16, '•').replace(/(.{4})/g, '$1 ').trim();
      document.getElementById('card-preview-num').textContent = display;
    }

    function formatExp(input) {
      let v = input.value.replace(/\D/g, '');
      if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2, 4);
      input.value = v;
      document.getElementById('card-preview-exp').textContent = v || 'MM/YY';
    }

    function processPayment() {
      document.getElementById('pay-ui').style.display = 'none';
      document.getElementById('processing-screen').classList.add('show');
      const fakeId = 'PAY_' + Date.now() + '_' + Math.random().toString(36).substring(2, 8).toUpperCase();
      setTimeout(function() {
        document.getElementById('hid_pay_id').value = fakeId;
        document.getElementById('hid_name').value = document.getElementById('full_name').value;
        document.getElementById('hid_email').value = document.getElementById('email').value;
        document.getElementById('hid_phone').value = document.getElementById('phone').value;
        document.getElementById('hid_amount').value = document.getElementById('amountInput').value;
        document.getElementById('hid_dtype').value = document.getElementById('donation_type').value;
        document.getElementById('hid_dmethod').value = document.getElementById('payment_method').value;
        document.getElementById('hid_msg').value = document.getElementById('message').value;
        document.getElementById('payForm').submit();
      }, 2500);
    }
  </script>
</body>

</html>
<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

// Get all active terms sections
$result = mysqli_query($conn, "SELECT * FROM terms_conditions WHERE is_active=1 ORDER BY section_order ASC");
$total = mysqli_num_rows($result);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Terms & Conditions | Safe & Home Foundation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; background: #f8f9fa; }

    .navbar { padding: 0.75rem 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .navbar-brand img { height: 80px; }
    .nav-link { font-size: 0.95rem; font-weight: 500; padding: 0.5rem 1.3rem !important; transition: color 0.2s; letter-spacing: 0.3px; }
    .nav-link:hover { color: #ffc107 !important; }
    .dropdown-menu { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; }
    .dropdown-item:hover { background: #f8f9fa; color: #198754; }
    .btn-warning { background: #ffc107; border: none; font-weight: 600; }
    .btn-outline-light { border-width: 2px; font-weight: 600; }
    .btn-outline-light:hover { background: white; color: #198754; }

    /* HERO */
    
    .hero {
  background: url("./images/login.png") center/cover no-repeat;
  min-height: 90vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  color: white;
}
    .hero h1 { font-size: 3rem; font-weight: 900; animation: fadeInUp 1s ease-out; }
    .hero p { font-size: 1.1rem; animation: fadeInUp 1.3s ease-out; opacity: 0.9; }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* CONTENT SECTION */
    .terms-container {
      max-width: 1000px;
      margin: -60px auto 50px;
      background: white;
      border-radius: 25px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.1);
      position: relative;
    }

    /* TOP GRADIENT BAR */
    .terms-container::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 6px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 25px 25px 0 0;
    }

    .last-updated {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      color: #198754;
      padding: 12px 20px;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 30px;
    }

    .terms-section {
      margin-bottom: 35px;
      padding-bottom: 25px;
      border-bottom: 2px solid #f0f0f0;
      animation: fadeInUp 0.8s ease-out backwards;
    }

    .terms-section:last-child {
      border-bottom: none;
    }

    .terms-section:nth-child(1) { animation-delay: 0.1s; }
    .terms-section:nth-child(2) { animation-delay: 0.2s; }
    .terms-section:nth-child(3) { animation-delay: 0.3s; }
    .terms-section:nth-child(4) { animation-delay: 0.4s; }
    .terms-section:nth-child(5) { animation-delay: 0.5s; }

    .section-number {
      display: inline-block;
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      border-radius: 50%;
      text-align: center;
      line-height: 40px;
      font-weight: 700;
      margin-right: 15px;
      font-size: 1rem;
    }

    .section-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #198754;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }

    .section-content {
      font-size: 1rem;
      line-height: 1.8;
      color: #555;
      text-align: justify;
    }

    /* SIDEBAR TOC */
    .toc-card {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      border-radius: 15px;
      padding: 25px;
      position: sticky;
      top: 100px;
    }

    .toc-card h5 {
      font-weight: 800;
      color: #198754;
      margin-bottom: 20px;
      font-size: 1.1rem;
    }

    .toc-item {
      padding: 8px 12px;
      margin-bottom: 8px;
      background: white;
      border-radius: 8px;
      color: #555;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
      font-size: 0.9rem;
    }

    .toc-item:hover {
      background: #198754;
      color: white;
      transform: translateX(5px);
    }

    /* IMPORTANT NOTICE BOX */
    .notice-box {
      background: #fff3cd;
      border-left: 5px solid #ffc107;
      padding: 20px;
      border-radius: 10px;
      margin: 30px 0;
    }

    .notice-box i {
      font-size: 1.5rem;
      color: #ffc107;
      margin-right: 10px;
    }

    .notice-box p {
      margin: 0;
      color: #856404;
      font-weight: 600;
    }

    /* CTA */
    .cta-section {
      background: linear-gradient(135deg, #198754, #20c997);
      padding: 50px 0;
      text-align: center;
      color: white;
      margin-top: 50px;
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
      text-decoration: none;
      display: inline-block;
    }

    .btn-cta:hover {
      transform: translateY(-3px);
      color: #198754;
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    footer { background: #198754; color: white; }
    footer a { transition: all 0.3s ease; }
    footer a:hover { color: #ffd700 !important; }

    @media (max-width: 991px) {
      .navbar-brand img { height: 60px; }
      .terms-container { padding: 30px 20px; }
      .hero h1 { font-size: 2rem; }
    }
    /* navbar */
.navbar { 
  padding: 0.75rem 0;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.navbar-brand img { 
  height: 80px;
}

.nav-link { font-size: 0.95rem; font-weight: 500; padding: 0.5rem 1.3rem !important; transition: color 0.2s; letter-spacing: 0.3px; }



.nav-link:hover { 
  color: #ffc107 !important;
}

/* BUTTONS */
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

.btn-outline-success {
  border: 2px solid #1f7a4c;
  color: #1f7a4c;
}

.btn-outline-success:hover {
  background: #1f7a4c;
  color: white;
}
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php include "./includes/navbar.php" ?>
<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1><i class="bi bi-file-text me-3"></i>Terms & Conditions</h1>
    <p class="mt-3">Please read these terms carefully before using our services</p>
  </div>
</section>

<!-- CONTENT -->
<section style="padding: 80px 0;">
  <div class="container">
    <div class="row g-5">

      <!-- MAIN CONTENT -->
      <div class="col-lg-8">
        <div class="terms-container">

          <!-- LAST UPDATED -->
          <div class="last-updated">
            <i class="bi bi-clock-history me-2"></i>
            Last Updated: <?php echo date('F d, Y'); ?>
          </div>

          <!-- IMPORTANT NOTICE -->
          <div class="notice-box">
            <div class="d-flex align-items-start">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <p>
                By accessing this website and using our services, you acknowledge that you have read, understood, 
                and agree to be bound by these Terms and Conditions.
              </p>
            </div>
          </div>

          <!-- SECTIONS -->
          <?php if($total > 0): ?>
            <?php $counter = 1; ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <div class="terms-section" id="section<?php echo $counter; ?>">
                <h3 class="section-title">
                  <span class="section-number"><?php echo $counter; ?></span>
                  <?php echo htmlspecialchars($row['section_title']); ?>
                </h3>
                <div class="section-content">
                  <?php echo nl2br(htmlspecialchars($row['section_content'])); ?>
                </div>
              </div>
              <?php $counter++; ?>
            <?php endwhile; ?>
          <?php else: ?>
            <p class="text-muted">Terms and conditions content is being updated. Please check back soon.</p>
          <?php endif; ?>

          <!-- ACCEPTANCE -->
          <div class="notice-box" style="background: #d1e7dd; border-left-color: #198754;">
            <div class="d-flex align-items-start">
              <i class="bi bi-check-circle-fill text-success"></i>
              <p style="color: #0f5132;">
                By continuing to use our website and services, you acknowledge your acceptance of these Terms and Conditions. 
                If you do not agree, please discontinue use immediately.
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- SIDEBAR TOC -->
      <div class="col-lg-4">
        <div class="toc-card">
          <h5><i class="bi bi-list-ul me-2"></i>Quick Navigation</h5>
          <?php
          // Re-query for TOC
          $result2 = mysqli_query($conn, "SELECT * FROM terms_conditions WHERE is_active=1 ORDER BY section_order ASC");
          $toc_counter = 1;
          while($toc = mysqli_fetch_assoc($result2)):
          ?>
            <a href="#section<?php echo $toc_counter; ?>" class="toc-item">
              <?php echo $toc_counter; ?>. <?php echo htmlspecialchars($toc['section_title']); ?>
            </a>
            <?php $toc_counter++; ?>
          <?php endwhile; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2 style="font-size:2rem; font-weight:800;">Have Questions?</h2>
    <p class="lead mt-3 mb-4 opacity-90">
      If you have any concerns about our Terms & Conditions, feel free to reach out!
    </p>
    <a href="contact.php" class="btn-cta">
      <i class="bi bi-envelope-fill me-2"></i> Contact Us
    </a>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-success text-white py-4">
  <div class="container">
    <div class="row">
      <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
        <h5 class="fw-bold"><i class="bi bi-house-heart-fill"></i> Safe & Home Foundation</h5>
        <p class="mb-0">Caring for all generations with love</p>
      </div>
      <div class="col-md-4 text-center mb-3 mb-md-0">
        <h5 class="fw-bold">Quick Links</h5>
        <a href="aboutus.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> About Us</a>
        <a href="terms.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> Terms & Conditions</a>
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
    <p class="text-center mb-0">
      © 2025 Safe & Home Foundation | All Rights Reserved |
      Made with <i class="bi bi-heart-fill text-danger"></i> for a better tomorrow
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Smooth scroll for TOC links
document.querySelectorAll('.toc-item').forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if(target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});
</script>
</body>
</html>

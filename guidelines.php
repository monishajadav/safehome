<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

// Filter by category
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
if(!empty($category)) {
    $sql = "SELECT * FROM guidelines WHERE is_active=1 AND category='$category' ORDER BY display_order ASC";
} else {
    $sql = "SELECT * FROM guidelines WHERE is_active=1 ORDER BY display_order ASC";
}
$result = mysqli_query($conn, $sql);
$total = mysqli_num_rows($result);

// Get categories with counts
$categories = mysqli_query($conn, "SELECT category, COUNT(*) as count FROM guidelines WHERE is_active=1 GROUP BY category ORDER BY category ASC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Guidelines | Safe & Home Foundation</title>
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
      background: url("./images/internship.jpg") center/cover no-repeat;
      min-height: 80vh; display:flex; align-items:center;
      justify-content:center; text-align:center; position:relative; color:white;
    }
    .hero h1 { font-size: 3rem; font-weight: 900; animation: fadeInUp 1s ease-out; }
    .hero p { font-size: 1.1rem; animation: fadeInUp 1.3s ease-out; opacity: 0.9; }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* MAIN CONTAINER */
    .guidelines-container {
      max-width: 1200px;
      margin: -60px auto 50px;
      background: white;
      border-radius: 25px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.1);
      position: relative;
    }

    /* TOP GRADIENT BAR */
    .guidelines-container::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 6px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 25px 25px 0 0;
    }

    /* CATEGORY FILTER */
    .category-filters {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 40px;
      justify-content: center;
    }
    .category-filter {
      background: #f8f9fa;
      border: 2px solid #e0e0e0;
      border-radius: 50px;
      padding: 8px 20px;
      font-weight: 600;
      color: #555;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .category-filter:hover, .category-filter.active {
      background: linear-gradient(135deg, #198754, #20c997);
      border-color: #198754;
      color: white;
    }

    /* GUIDELINE CARD */
    .guideline-card {
      background: #f8fff8;
      border-left: 5px solid #198754;
      border-radius: 15px;
      padding: 25px;
      margin-bottom: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease-out backwards;
    }
    .guideline-card:hover {
      transform: translateX(10px);
      box-shadow: 0 10px 25px rgba(25,135,84,0.15);
    }

    .guideline-card:nth-child(1) { animation-delay: 0.1s; }
    .guideline-card:nth-child(2) { animation-delay: 0.2s; }
    .guideline-card:nth-child(3) { animation-delay: 0.3s; }
    .guideline-card:nth-child(4) { animation-delay: 0.4s; }
    .guideline-card:nth-child(5) { animation-delay: 0.5s; }

    .guideline-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #198754, #20c997);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: white;
      float: left;
      margin-right: 20px;
    }

    .guideline-header {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .guideline-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: #198754;
      margin: 0;
    }

    .guideline-content {
      font-size: 1rem;
      line-height: 1.8;
      color: #555;
      clear: both;
    }

    .category-badge {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      color: #198754;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 700;
      margin-left: auto;
    }

    /* IMPORTANT NOTICE */
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

    /* SIDEBAR */
    .sidebar-card {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      border-radius: 15px;
      padding: 25px;
      position: sticky;
      top: 100px;
    }
    .sidebar-card h5 {
      font-weight: 800;
      color: #198754;
      margin-bottom: 20px;
      font-size: 1.1rem;
    }
    .sidebar-item {
      padding: 10px 15px;
      margin-bottom: 10px;
      background: white;
      border-radius: 8px;
      color: #555;
      text-decoration: none;
      display: block;
      transition: all 0.3s ease;
      font-size: 0.9rem;
    }
    .sidebar-item:hover {
      background: #198754;
      color: white;
      transform: translateX(5px);
    }
    .sidebar-item i {
      margin-right: 8px;
    }

    /* EMPTY STATE */
    .empty-state {
      text-align: center;
      padding: 60px 30px;
      background: #f8fff8;
      border-radius: 15px;
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
      .guidelines-container { padding: 30px 20px; margin: 20px; }
      .hero h1 { font-size: 2rem; }
      .guideline-icon { width: 50px; height: 50px; font-size: 1.5rem; margin-bottom: 15px; float: none; }
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
    <h1><i class="bi bi-book me-3"></i>Community Guidelines</h1>
    <p class="mt-3">Important guidelines for volunteers, visitors, donors, and community members</p>
  </div>
</section>

<!-- CONTENT -->
<section style="padding: 80px 0;">
  <div class="container">
    <div class="row g-5">

      <!-- MAIN CONTENT -->
      <div class="col-lg-8">
        <div class="guidelines-container">

          <!-- IMPORTANT NOTICE -->
          <div class="notice-box">
            <div class="d-flex align-items-start">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <p>
                These guidelines are mandatory for everyone involved with Safe & Home Foundation. 
                Failure to comply may result in termination of volunteer status or access privileges.
              </p>
            </div>
          </div>

          <!-- CATEGORY FILTERS -->
          <div class="category-filters">
            <a href="guidelines.php" class="category-filter <?php echo empty($category) ? 'active' : ''; ?>">
              <i class="bi bi-grid-fill me-1"></i> All Guidelines
            </a>
            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
              <a href="guidelines.php?category=<?php echo urlencode($cat['category']); ?>" 
                 class="category-filter <?php echo $category == $cat['category'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['category']); ?> (<?php echo $cat['count']; ?>)
              </a>
            <?php endwhile; ?>
          </div>

          <!-- GUIDELINES LIST -->
          <?php if($total > 0): ?>
            <?php
            $current_category = '';
            mysqli_data_seek($result, 0); // Reset pointer
            while($row = mysqli_fetch_assoc($result)):
              // Show category header when category changes
              if($current_category != $row['category']):
                $current_category = $row['category'];
                if($row != mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM guidelines WHERE is_active=1 ORDER BY display_order ASC LIMIT 1"))):
                  echo '<hr class="my-4">';
                endif;
            ?>
                <h3 style="color:#198754; font-weight:800; margin:30px 0 20px;">
                  <i class="bi bi-bookmark-fill me-2"></i>
                  <?php echo htmlspecialchars($row['category']); ?> Guidelines
                </h3>
              <?php endif; ?>

              <!-- GUIDELINE CARD -->
              <div class="guideline-card">
                <div class="guideline-header">
                  <div class="guideline-icon">
                    <i class="<?php echo htmlspecialchars($row['icon']); ?>"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h4 class="guideline-title"><?php echo htmlspecialchars($row['guideline_title']); ?></h4>
                  </div>
                  <span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                </div>
                <div class="guideline-content">
                  <?php echo nl2br(htmlspecialchars($row['guideline_content'])); ?>
                </div>
              </div>

            <?php endwhile; ?>

          <?php else: ?>
            <div class="empty-state">
              <i class="bi bi-book fs-1 text-success opacity-50 d-block mb-4"></i>
              <h4 class="text-success fw-bold">No Guidelines Found</h4>
              <p class="text-muted">Guidelines for this category will be added soon.</p>
            </div>
          <?php endif; ?>

          <!-- COMPLIANCE NOTICE -->
          <div class="notice-box" style="background: #d1e7dd; border-left-color: #198754;">
            <div class="d-flex align-items-start">
              <i class="bi bi-check-circle-fill text-success"></i>
              <p style="color: #0f5132;">
                By participating in any Safe & Home Foundation activities, you acknowledge that you have read, 
                understood, and agree to follow these guidelines.
              </p>
            </div>
          </div>

        </div>
      </div>

      <!-- SIDEBAR -->
      <div class="col-lg-4">
        <div class="sidebar-card">
          <h5><i class="bi bi-list-ul me-2"></i>Quick Links</h5>
          <a href="volunteer.php" class="sidebar-item">
            <i class="bi bi-people-fill"></i> Become a Volunteer
          </a>
          <a href="donate.php" class="sidebar-item">
            <i class="bi bi-heart-fill"></i> Make a Donation
          </a>
          <a href="internship.php" class="sidebar-item">
            <i class="bi bi-briefcase-fill"></i> Apply for Internship
          </a>
          <a href="wellwisher.php" class="sidebar-item">
            <i class="bi bi-chat-heart-fill"></i> Share Your Message
          </a>
          <a href="contact.php" class="sidebar-item">
            <i class="bi bi-envelope-fill"></i> Contact Us
          </a>
          <a href="terms.php" class="sidebar-item">
            <i class="bi bi-file-text-fill"></i> Terms & Conditions
          </a>
        </div>

        <!-- EMERGENCY CONTACTS -->
        <div class="sidebar-card mt-4" style="background: #fce4ec;">
          <h5 style="color: #c62828;"><i class="bi bi-telephone-fill me-2"></i>Emergency Contacts</h5>
          <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
            <p class="mb-1" style="font-weight: 700; color: #333;">Emergency Services</p>
            <p class="mb-0" style="color: #666;">📞 112</p>
          </div>
          <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px;">
            <p class="mb-1" style="font-weight: 700; color: #333;">Foundation Office</p>
            <p class="mb-0" style="color: #666;">📞 +91 98765 43210</p>
          </div>
          <div style="background: white; padding: 15px; border-radius: 8px;">
            <p class="mb-1" style="font-weight: 700; color: #333;">Director</p>
            <p class="mb-0" style="color: #666;">📞 +91 91234 56789</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2 style="font-size:2rem; font-weight:800;">Questions About Guidelines?</h2>
    <p class="lead mt-3 mb-4 opacity-90">
      Our team is here to help clarify any doubts you may have!
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
        <a href="guidelines.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> Guidelines</a>
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
</body>
</html>
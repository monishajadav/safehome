<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

// Filter by category
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
if(!empty($category)) {
    $sql = "SELECT * FROM gallery WHERE category='$category' ORDER BY uploaded_at DESC";
} else {
    $sql = "SELECT * FROM gallery ORDER BY uploaded_at DESC";
}
$result = mysqli_query($conn, $sql);
$total = mysqli_num_rows($result);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gallery | Safe & Home Foundation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

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



    /* HERO */
   .hero {
  background: 
    linear-gradient(135deg, rgba(25, 135, 84, 0.3) 0%, rgba(32, 201, 151, 0.3) 100%),
    url("./images/gallery.jpg");
  background-size: cover;
  background-position: center;
  min-height: 90vh;
  display: flex;
  align-items: center;
  color: #fff;
  text-align: center;
}
    .hero h1 { font-size: 3.5rem; font-weight: 900; animation: fadeInUp 1s ease-out; }
    .hero p { font-size: 1.3rem; animation: fadeInUp 1.3s ease-out; opacity: 0.9; }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .section-green {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      padding: 80px 0;
    }

    .section-title {
      font-size: 2.3rem; font-weight: 800; color: #198754;
      position: relative; display: inline-block; margin-bottom: 2.5rem;
    }
    .section-title::after {
      content: ''; position: absolute; bottom: -10px; left: 50%;
      transform: translateX(-50%); width: 70px; height: 4px;
      background: linear-gradient(90deg, #198754, #20c997); border-radius: 2px;
    }

    /* FILTER TABS */
    .filter-tabs { display: flex; gap: 10px; justify-content: center; margin-bottom: 40px; flex-wrap: wrap; }
    .filter-tab {
      background: white; border: 2px solid #e0e0e0; border-radius: 50px;
      padding: 8px 25px; font-weight: 600; color: #555; cursor: pointer;
      transition: all 0.3s ease; text-decoration: none; font-size: 0.9rem;
    }
    .filter-tab:hover, .filter-tab.active {
      background: linear-gradient(135deg, #198754, #20c997);
      border-color: #198754; color: white;
    }

    /* GALLERY CARDS */
    .gallery-card {
      background: white; border-radius: 20px;
      overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease; height: 100%;
    }
    .gallery-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 50px rgba(25,135,84,0.2);
    }
    .gallery-img-wrap {
      position: relative; overflow: hidden; height: 220px;
    }
    .gallery-img-wrap img {
      width: 100%; height: 100%; object-fit: cover;
      transition: transform 0.5s ease;
    }
    .gallery-card:hover .gallery-img-wrap img {
      transform: scale(1.1);
    }
    .gallery-overlay {
      position: absolute; top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(25,135,84,0.85);
      display: flex; align-items: center; justify-content: center;
      opacity: 0; transition: all 0.3s ease;
    }
    .gallery-card:hover .gallery-overlay { opacity: 1; }
    .gallery-overlay i { font-size: 2.5rem; color: white; }

    .category-badge {
      position: absolute; top: 15px; left: 15px;
      background: linear-gradient(135deg, #198754, #20c997);
      color: white; padding: 4px 12px; border-radius: 20px;
      font-size: 0.75rem; font-weight: 700;
    }

    .gallery-body { padding: 20px; }
    .gallery-body h5 { font-weight: 700; color: #333; margin-bottom: 8px; font-size: 1rem; }
    .gallery-body p { color: #666; font-size: 0.88rem; line-height: 1.6; margin-bottom: 10px; }
    .gallery-date { font-size: 0.78rem; color: #aaa; }

    /* LIGHTBOX MODAL */
    .modal-content { border-radius: 20px; overflow: hidden; border: none; }
    .modal-header {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white; border: none; padding: 20px 25px;
    }
    .modal-body { padding: 0; }
    .modal-body img { width: 100%; max-height: 500px; object-fit: contain; background: #000; }
    .modal-footer { padding: 20px 25px; border-top: 1px solid #f0f0f0; }

    /* EMPTY STATE */
    .empty-state {
      background: white; border-radius: 20px; padding: 60px 30px;
      text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    /* CTA */
    .cta-section {
      background: linear-gradient(135deg, #198754, #20c997);
      padding: 60px 0; text-align: center; color: white;
    }
    .btn-cta {
      background: white; color: #198754; border: none; border-radius: 50px;
      padding: 15px 40px; font-weight: 700; font-size: 1.1rem; transition: all 0.3s ease;
      text-decoration: none; display: inline-block;
    }
    .btn-cta:hover { transform: translateY(-3px); color: #198754; }

    .fade-in { opacity: 0; transform: translateY(20px); transition: all 0.8s ease; }
    .fade-in.show { opacity: 1; transform: translateY(0); }

   /* ================= FOOTER ================= */

.footer {
  background: linear-gradient(135deg, #14532d 0%, #1e8449 50%, #1f7a4c 100%);
  color: #fff;
  padding: 60px 20px 20px;
  font-family: 'Poppins', sans-serif;
  margin-top: 50px;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-box h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
  font-weight: 800;
  color: #f4d03f;
}

.footer-box h3 {
  font-size: 1.2rem;
  margin-bottom: 20px;
  font-weight: 700;
  color: #f4d03f;
  border-bottom: 2px solid rgba(244, 208, 63, 0.3);
  padding-bottom: 10px;
}

.footer-box p {
  font-size: 0.95rem;
  line-height: 1.8;
  margin-bottom: 10px;
  color: rgba(255, 255, 255, 0.9);
}

.footer-box ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-box ul li {
  margin: 12px 0;
}

.footer-box ul li a {
  color: #fff;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-block;
  font-size: 0.95rem;
}

.footer-box ul li a:hover {
  color: #f4d03f;
  transform: translateX(8px);
}

/* Donate Button */
.donate-btn {
  display: inline-block;
  margin-top: 15px;
  padding: 12px 30px;
  background: linear-gradient(135deg, #f4d03f, #f1c40f);
  color: #000;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(244, 208, 63, 0.3);
}

.donate-btn:hover {
  background: linear-gradient(135deg, #f1c40f, #f39c12);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(244, 208, 63, 0.5);
  color: #000;
}

/* Social Icons */
.social-icons {
  text-align: center;
  margin: 40px 0 30px;
  padding: 30px 0;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.social-icons a {
  margin: 0 12px;
  font-size: 28px;
  text-decoration: none;
  color: white;
  transition: all 0.3s ease;
  display: inline-block;
}

.social-icons a:hover {
  color: #f4d03f;
  transform: scale(1.2) translateY(-5px);
}

/* Footer Bottom */
.footer-bottom {
  text-align: center;
  padding-top: 20px;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.8);
}

.footer-bottom a {
  color: #f4d03f;
  text-decoration: none;
  transition: all 0.3s ease;
  margin: 0 8px;
}

.footer-bottom a:hover {
  color: #fff;
  text-decoration: underline;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: 1fr;
    gap: 30px;
  }

  .footer-box {
    text-align: center;
  }

  .footer-box ul li a:hover {
    transform: translateX(0);
    color: #f4d03f;
  }

  .social-icons a {
    font-size: 24px;
    margin: 0 8px;
  }
}

    @media (max-width: 991px) { .navbar-brand img { height: 60px; } }
    @media (max-width: 768px) { .hero h1 { font-size: 2.2rem; } }
  </style>
</head>
<body>

<!-- NAVBAR -->
<?php include "./includes/navbar.php" ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1><i class="bi bi-images me-3"></i>Our Gallery</h1>
    <p class="mt-3">Moments of love, care and hope captured forever.<br>
    Every picture tells a story of change.</p>
  </div>
</section>

<!-- GALLERY SECTION -->
<section class="section-green">
  <div class="container">
    <h2 class="section-title text-center">Photo Gallery</h2>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
      <a href="gallery.php" class="filter-tab <?php echo empty($category) ? 'active' : ''; ?>">
        <i class="bi bi-grid-fill me-1"></i> All
      </a>
      <a href="gallery.php?category=Elder Care" class="filter-tab <?php echo $category=='Elder Care' ? 'active' : ''; ?>">
        <i class="bi bi-heart-fill me-1"></i> Elder Care
      </a>
      <a href="gallery.php?category=Child Welfare" class="filter-tab <?php echo $category=='Child Welfare' ? 'active' : ''; ?>">
        <i class="bi bi-emoji-smile-fill me-1"></i> Child Welfare
      </a>
      <a href="gallery.php?category=Events" class="filter-tab <?php echo $category=='Events' ? 'active' : ''; ?>">
        <i class="bi bi-calendar-event-fill me-1"></i> Events
      </a>
      <a href="gallery.php?category=Volunteer" class="filter-tab <?php echo $category=='Volunteer' ? 'active' : ''; ?>">
        <i class="bi bi-people-fill me-1"></i> Volunteer
      </a>
      <a href="gallery.php?category=General" class="filter-tab <?php echo $category=='General' ? 'active' : ''; ?>">
        <i class="bi bi-star-fill me-1"></i> General
      </a>
    </div>

    <?php if($total > 0): ?>
      <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-lg-4 col-md-6 fade-in">
            <div class="gallery-card">

              <!-- IMAGE -->
              <div class="gallery-img-wrap">
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                     alt="<?php echo htmlspecialchars($row['title']); ?>">
                <span class="category-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                <!-- OVERLAY - click to open modal -->
                <div class="gallery-overlay"
                     data-bs-toggle="modal"
                     data-bs-target="#imgModal<?php echo $row['id']; ?>"
                     style="cursor:pointer;">
                  <i class="bi bi-zoom-in"></i>
                </div>
              </div>

              <!-- BODY -->
              <div class="gallery-body">
                <h5><?php echo htmlspecialchars($row['title']); ?></h5>
                <?php if(!empty($row['description'])): ?>
                  <p><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : ''); ?></p>
                <?php endif; ?>
                <div class="gallery-date">
                  <i class="bi bi-calendar me-1"></i>
                  <?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?>
                </div>
              </div>

            </div>
          </div>

          <!-- LIGHTBOX MODAL -->
          <div class="modal fade" id="imgModal<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">
                    <i class="bi bi-image me-2"></i>
                    <?php echo htmlspecialchars($row['title']); ?>
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                       alt="<?php echo htmlspecialchars($row['title']); ?>">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                  <div>
                    <span class="badge bg-success me-2"><?php echo htmlspecialchars($row['category']); ?></span>
                    <small class="text-muted">
                      <i class="bi bi-calendar me-1"></i>
                      <?php echo date('F d, Y', strtotime($row['uploaded_at'])); ?>
                    </small>
                  </div>
                  <?php if(!empty($row['description'])): ?>
                    <p class="mb-0 text-muted" style="font-size:0.9rem;">
                      <?php echo htmlspecialchars($row['description']); ?>
                    </p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

        <?php endwhile; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-images fs-1 text-success opacity-50 d-block mb-4"></i>
        <h4 class="text-success fw-bold">No Photos Yet!</h4>
        <p class="text-muted">Check back soon for photos from our events and activities.</p>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2 style="font-size:2rem; font-weight:800;">Want to Be Part of Our Story?</h2>
    <p class="lead mt-3 mb-4 opacity-90">Join us as a volunteer and create memories that matter!</p>
    <a href="volunteer.php" class="btn-cta">
      <i class="bi bi-people-fill me-2"></i> Join as Volunteer
    </a>
  </div>
</section>

<!-- FOOTER -->
<?php include "./includes/footer.php" ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fadeElements = document.querySelectorAll(".fade-in");
const observer = new IntersectionObserver(
  (entries) => { entries.forEach((entry) => { if (entry.isIntersecting) entry.target.classList.add("show"); }); },
  { threshold: 0.1 }
);
fadeElements.forEach((el) => observer.observe(el));
</script>
</body>
</html>
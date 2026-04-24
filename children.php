<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Child Welfare | Safe & Home Foundation</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

  <style>
    * {
      font-family: 'Poppins', sans-serif;
    }

    html { 
      scroll-behavior: smooth; 
    }

body {
  background: linear-gradient(-45deg, #ffeaa7, #fab1a0, #74b9ff, #a29bfe);
  background-size: 400% 400%;
  animation: gradientBG 15s ease infinite;
  overflow-x: hidden;
  margin: 0;
  padding: 0;
}

    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
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

    /* HERO SECTION */
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
        url("./images/children.jpg");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: white;
      text-align: center;
      padding: 150px 20px;
      min-height:90vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      text-shadow: 3px 3px 15px rgba(0,0,0,0.5);
      animation: fadeInUp 1s ease-out;
    }

    .hero h2 {
      font-size: 1.5rem;
      font-weight: 400;
      color: #f1f1f1;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
      animation: fadeInUp 1.2s ease-out;
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

    /* CONTENT SECTIONS */
    section:not(.hero):not(.footer) {
      padding: 80px 0;
    }

    .content-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.2);
      margin-bottom: 30px;
      animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .section-title {
      color: #198754;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 30px;
      font-size: 2.5rem;
      position: relative;
      display: inline-block;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 2px;
    }

    p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #333;
    }

    /* HOW WE HELP LIST */
    .help-list {
      display: inline-block;
      text-align: left;
      margin: 30px auto;
      background: white;
      padding: 30px 50px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .help-list li {
      font-size: 1.1rem;
      margin-bottom: 15px;
      color: #333;
      line-height: 1.8;
    }

    .help-list li::marker {
      color: #198754;
      font-weight: bold;
    }

    /* STATS SECTION */
    .stats-section {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 60px 0;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .stat-item {
      text-align: center;
      padding: 20px;
      color: white;
    }

    .stat-item i {
      font-size: 3.5rem;
      margin-bottom: 15px;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    }

    .stat-item h3 {
      font-size: 3rem;
      font-weight: 800;
      margin-bottom: 10px;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    }

    .stat-item p {
      font-size: 1.1rem;
      margin-bottom: 0;
      color: white;
      text-shadow: 1px 1px 5px rgba(0,0,0,0.3);
    }

    /* CTA SECTION */
    .cta-section {
      padding: 80px 0;
      text-align: center;
    }

    .cta-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 60px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.2);
    }

    .btn-donate {
      background: linear-gradient(135deg, #198754 0%, #20c997 100%);
      border: none;
      color: white;
      padding: 15px 50px;
      font-size: 1.2rem;
      font-weight: 700;
      border-radius: 50px;
      transition: all 0.3s ease;
      box-shadow: 0 5px 20px rgba(25,135,84,0.4);
    }

    .btn-donate:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(25,135,84,0.5);
      color: white;
    }

    .highlight-text {
      color: #198754;
      font-weight: 700;
      font-style: italic;
    }

    /* FOOTER */
    footer {
      background: rgba(25,135,84,0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }

    footer a {
      transition: all 0.3s ease;
    }

    footer a:hover {
      color: #ffd700 !important;
      transform: translateX(5px);
    }

    /* ANIMATION ON SCROLL */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: all 1s ease;
    }

    .fade-in.show {
      opacity: 1;
      transform: translateY(0);
    }

    /* RESPONSIVE */
    @media (max-width: 991px) {
      .navbar-brand img {
        height: 60px;
      }
      
      .hero h1 { font-size: 2.5rem; }
      .hero h2 { font-size: 1.2rem; }
      .section-title { font-size: 2rem; }
      .content-card { padding: 30px; }
    }

    @media (max-width: 768px) {
      .hero h1 { font-size: 2rem; }
      .stat-item h3 { font-size: 2rem; }
    }
  
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <?php include "./includes/navbar.php" ?>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <h1>CARE FOR CHILDREN</h1>
      <h2 class="mt-3">"Every child deserves love, education, and a place to call home"</h2>
       <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
      <a href="donate.php" class="btn btn-success btn-lg px-5">
        <i class="bi bi-heart-fill"></i> Donate Now
      </a>
    </div>
    </div>
  </section>

  <!-- ABOUT SECTION -->
  <section class="fade-in">
    <div class="container">
      <div class="content-card text-center">
        <h2 class="section-title">Our Promise to Children</h2>
        <p>
          At Safe & Home Foundation, we work tirelessly to provide shelter, education, and emotional 
          support to orphaned and abandoned children. Our mission is to ensure every child grows up 
          with hope, dignity, and opportunities for a brighter future.
        </p>
        <p>
          Through the kindness of our donors and volunteers, we aim to create a safe, loving, and 
          nurturing environment that helps each child dream beyond their circumstances.
        </p>
      </div>
    </div>
  </section>

  <!-- HOW WE HELP -->
  <section class="fade-in">
    <div class="container">
      <div class="content-card text-center">
        <h2 class="section-title">How We Help</h2>
        <ol class="help-list">
          <li><i class="bi bi-house-heart-fill text-success"></i> Provide safe shelter and nutritious meals</li>
          <li><i class="bi bi-book-fill text-success"></i> Support education and school materials</li>
          <li><i class="bi bi-heart-pulse-fill text-success"></i> Offer healthcare and emotional counseling</li>
          <li><i class="bi bi-star-fill text-success"></i> Organize extracurricular and skill-building activities</li>
        </ol>
      </div>
    </div>
  </section>

  <!-- STATS SECTION -->
  <section class="stats-section fade-in">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-6">
          <div class="stat-item">
            <i class="bi bi-people-fill"></i>
            <h3>500+</h3>
            <p>Children Helped</p>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="stat-item">
            <i class="bi bi-book-fill"></i>
            <h3>100%</h3>
            <p>School Enrollment</p>
          </div>
        </div>
        <div class="col-md-4 col-6">
          <div class="stat-item">
            <i class="bi bi-heart-fill"></i>
            <h3>50+</h3>
            <p>Volunteers</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- IMPACT SECTION -->
  <section class="fade-in">
    <div class="container">
      <div class="content-card text-center">
        <h2 class="section-title">Smiles of Hope</h2>
        <p>
          With your support, more than <span class="highlight-text">500 children</span> now have access 
          to education, healthy food, and a caring environment. Together, we're giving them a chance to 
          live their dreams.
        </p>
        <p class="mt-4 fs-5 fst-italic">
          <i class="bi bi-quote"></i>
          Every child has the potential to change the world. We just need to give them the chance.
          <i class="bi bi-quote"></i>
        </p>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section fade-in">
    <div class="container">
      <div class="cta-card">
        <h2 class="section-title">You Can Make a Difference</h2>
        <h4 class="mb-4">Your contribution can light up a child's world</h4>
        <a href="donate.php" class="btn btn-donate">
          <i class="bi bi-heart-fill"></i> Donate Now
        </a>
        <p class="mt-4 fs-5 fst-italic highlight-text">
          "A small act of kindness can change a life forever"
        </p>
      </div>
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
          <a href="contact.php" class="text-white d-block mb-1"><i class="bi bi-arrow-right"></i> Contact</a>
          <a href="donate.php" class="text-white d-block"><i class="bi bi-arrow-right"></i> Donate</a>
          <a href="guidelines.php" class="text-white d-block"><i class="bi bi-arrow-right"></i>Guidelines</a>
        </div>
        <div class="col-md-4 text-center text-md-end">
          <h5 class="fw-bold">Follow Us</h5>
          <a href="#" class="text-white me-3 fs-4"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-white me-3 fs-4"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-white fs-4"><i class="bi bi-twitter"></i></a>
        </div>
      </div>
      <hr class="bg-white mt-4 opacity-25">
      <p class="text-center mb-0">© 2025 Safe & Home Foundation | All Rights Reserved | Made with <i class="bi bi-heart-fill"></i> for a better tomorrow</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Fade-in animation on scroll
    const fadeElements = document.querySelectorAll(".fade-in");
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) entry.target.classList.add("show");
        });
      },
      { threshold: 0.2 }
    );
    fadeElements.forEach((el) => observer.observe(el));
  </script>
</body>
</html>
<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us | Safe & Home Foundation</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="css/common.css" rel="stylesheet" />
  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }
    /* HERO */
    .hero {
      background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                  url("./images/aboout.jpg");
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
      text-shadow: 3px 3px 8px rgba(0,0,0,0.5);
      animation: fadeInUp 1s ease-out;
    }
    .hero p {
      font-size: 1.4rem;
      font-weight: 400;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
      animation: fadeInUp 1.3s ease-out;
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* ALL SECTIONS - SAME GRADIENT */
    .section-green {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      padding: 80px 0;
    }

    /* SECTION TITLE */
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
      left: 0;
      width: 70px;
      height: 4px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 2px;
    }
    .section-title.center::after {
      left: 50%;
      transform: translateX(-50%);
    }

    /* ABOUT CARD */
    .about-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
    }
    .about-card:hover {
      transform: translateY(-5px);
    }

    /* HIGHLIGHT BOX */
    .highlight-box {
      background: linear-gradient(135deg, #198754, #20c997);
      border-radius: 20px;
      padding: 35px;
      color: white;
    }
    .highlight-box p {
      font-size: 1.2rem;
      line-height: 1.9;
      margin: 0;
    }

    /* QUOTE BOX */
    .quote-box {
      background: white;
      border-left: 6px solid #198754;
      border-radius: 0 15px 15px 0;
      padding: 25px 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.06);
      font-size: 1.2rem;
      font-style: italic;
      color: #198754;
      line-height: 1.8;
    }

    /* VALUES CARDS */
    .value-card {
      background: white;
      border-radius: 20px;
      padding: 35px 25px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      height: 100%;
    }
    .value-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(25,135,84,0.2);
    }
    .value-icon {
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
    .value-card:hover .value-icon {
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      transform: rotate(10deg);
    }
    .value-card h5 {
      font-weight: 700;
      color: #198754;
      margin-bottom: 10px;
    }
    .value-card p {
      color: #666;
      font-size: 0.95rem;
      margin: 0;
    }

    /* STATS SECTION */
    .stat-box {
      background: white;
      border-radius: 20px;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }
    .stat-box:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(25,135,84,0.2);
    }
    .stat-number {
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, #198754, #20c997);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    .stat-label {
      font-size: 1rem;
      color: #666;
      font-weight: 500;
    }

    /* TEAM CARDS */
    .team-card {
      background: white;
      border-radius: 20px;
      padding: 35px 25px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      height: 100%;
    }
    .team-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(25,135,84,0.25);
    }
    .team-avatar {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      background: linear-gradient(135deg, #198754, #20c997);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 2.5rem;
      color: white;
      font-weight: 700;
    }
    .team-card h5 {
      font-weight: 700;
      color: #198754;
      margin-bottom: 5px;
    }
    .team-card p {
      color: #888;
      font-size: 0.9rem;
      margin: 0;
    }
    .team-card .role {
      background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
      color: #198754;
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-block;
      margin-top: 10px;
    }

    /* CTA SECTION */
    .cta-section {
      background: linear-gradient(135deg, #198754, #20c997);
      padding: 80px 0;
      text-align: center;
      color: white;
    }
    .cta-section h2 {
      font-size: 2.5rem;
      font-weight: 800;
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
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .btn-cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      color: #198754;
    }
    /* FOOTER */
    footer {
      background: #198754;
      color: white;
      padding: 40px 0 20px;
    }
    footer a { transition: all 0.3s ease; }
    footer a:hover { color: #ffd700 !important; }

    /* RESPONSIVE */
    @media (max-width: 991px) {
      .navbar-brand img { height: 60px; }
    }
    @media (max-width: 768px) {
      .hero h1 { font-size: 2.5rem; }
      .hero p { font-size: 1.1rem; }
      .section-title { font-size: 1.8rem; }
    }
    /* Section Background */
.section-green {
  padding: 80px 0;
  background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
}

/* Title */
.section-title {
  font-weight: 700;
  margin-bottom: 15px;
}

.subtitle {
  color: #555;
  font-size: 16px;
}

/* Card Design */
.team-card {
  background: #f5f5f5;
  padding: 40px 25px;
  border-radius: 20px;
  transition: 0.3s ease;
  box-shadow: 0 8px 20px rgba(0,0,0,0.05);
}

.team-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.1);
}

/* Avatar Circle */
.team-avatar {
  width: 90px;
  height: 90px;
  margin: 0 auto 20px;
  background: linear-gradient(135deg, #1f7a4c, #2ecc71);
  color: white;
  font-size: 36px;
  font-weight: bold;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Name */
.team-card h5 {
  font-weight: 600;
  margin-bottom: 5px;
  color: #1f7a4c;
}

/* Role Text */
.team-card p {
  color: #777;
  font-size: 14px;
}

/* Badge */
.badge-role {
  display: inline-block;
  background: #d4edda;
  color: #155724;
  padding: 6px 15px;
  border-radius: 20px;
  font-size: 13px;
  margin-top: 10px;
  margin-bottom: 15px;
}

/* Portfolio Button */
.portfolio-btn {
  display: inline-block;
  padding: 10px 25px;
  background: #1f7a4c;
  color: white;
  border-radius: 25px;
  text-decoration: none;
  transition: 0.3s;
}

.portfolio-btn:hover {
  background: #14532d;
  color: white;
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
/* footer */
.footer {
  background-color: #1e8449;
  color: #fff;
  padding: 40px 20px 10px;
  font-family: Arial, sans-serif;
}

.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 30px;
}

.footer-box h2,
.footer-box h3 {
  margin-bottom: 15px;
}

.footer-box p {
  font-size: 14px;
  line-height: 1.6;
}

.footer-box ul {
  list-style: none;
  padding: 0;
}

.footer-box ul li {
  margin: 8px 0;
}

.footer-box ul li a {
  color: #fff;
  text-decoration: none;
  transition: 0.3s;
}

.footer-box ul li a:hover {
  text-decoration: underline;
  padding-left: 5px;
}

/* Donate Button */
.donate-btn {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 18px;
  background-color: #f4d03f;
  color: #000;
  border-radius: 25px;
  text-decoration: none;
  font-weight: bold;
  transition: 0.3s;
}

.donate-btn:hover {
  background-color: #f1c40f;
}

/* Social Icons */
.social-icons {
  text-align: center;
  margin: 20px 0;
}

.social-icons a {
  margin: 0 10px;
  font-size: 20px;
  text-decoration: none;
  color: white;
  transition: 0.3s;
}

.social-icons a:hover {
  color: #f4d03f;
}

/* Bottom */
.footer-bottom {
  text-align: center;
  border-top: 1px solid #ddd;
  padding-top: 10px;
  font-size: 14px;
}

.footer-bottom a {
  color: #f4d03f;
  text-decoration: none;
}


  </style>
</head>

<body>

<!-- NAVBAR -->
<?php include "./includes/navbar.php" ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1>About Safe & Home Foundation</h1>
    <p class="mt-3 px-md-5">
      "Caring for the youngest and honoring the oldest—because every life matters."
    </p>
    <a href="#about" class="btn btn-success btn-lg mt-4 px-5">
      <i class="bi bi-arrow-down-circle"></i> Learn More
    </a>
  </div>
</section>

<!-- ABOUT US -->
<section class="section-green" id="about">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <h2 class="section-title">Who We Are</h2>
        <div class="about-card">
          <p style="font-size:1.1rem; line-height:1.9; color:#444;">
            <strong>Safe & Home Foundation</strong> is a non-profit organization
            dedicated to providing care, shelter, and support to both
            <strong>Orphans</strong> and <strong>Senior Citizens</strong>.
            We believe that every individual, regardless of age or circumstance,
            deserves love, respect, and a safe place to call home.
          </p>
          <p style="font-size:1.1rem; line-height:1.9; color:#444; margin-top:15px;">
            Our mission is to <strong>bridge the gap</strong> between the young
            and the elderly by creating a compassionate environment where both
            generations can grow together in harmony.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="highlight-box">
          <i class="bi bi-quote fs-1 mb-3 d-block opacity-75"></i>
          <p>
            "This website is a college final year project created to promote
            social awareness and make it easier for donors and volunteers to
            connect with those in need. Every contribution helps us build a
            better and brighter future for our community."
          </p>
          <p class="mt-3 fw-bold mb-0">
            — The Safe & Home Foundation Team
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OUR VALUES -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title center">Our Core Values</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3 col-sm-6">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-heart-fill"></i>
          </div>
          <h5>Compassion</h5>
          <p>We care deeply for every individual we serve with love and empathy.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-shield-check"></i>
          </div>
          <h5>Trust</h5>
          <p>We build trust through transparency, honesty and accountability.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <h5>Community</h5>
          <p>We believe in the power of community to bring lasting change.</p>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-star-fill"></i>
          </div>
          <h5>Excellence</h5>
          <p>We strive for excellence in everything we do for those we serve.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- OUR VISION & MISSION -->
<section class="section-green">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="about-card h-100">
          <div class="d-flex align-items-center mb-4">
            <div class="value-icon me-3" style="min-width:75px;">
              <i class="bi bi-eye-fill"></i>
            </div>
            <h2 class="section-title mb-0" style="font-size:1.8rem;">Our Vision</h2>
          </div>
          <p style="color:#444; line-height:1.9; font-size:1.05rem;">
            To build a society where <strong>"No Child or Elder feels lonely or neglected"</strong>.
            We dream of a world filled with compassion, where young hearts learn
            empathy and older souls live with dignity and peace.
          </p>
          <div class="quote-box mt-4">
            "Every life is precious. Every person deserves dignity."
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="about-card h-100">
          <div class="d-flex align-items-center mb-4">
            <div class="value-icon me-3" style="min-width:75px;">
              <i class="bi bi-bullseye"></i>
            </div>
            <h2 class="section-title mb-0" style="font-size:1.8rem;">Our Mission</h2>
          </div>
          <p style="color:#444; line-height:1.9; font-size:1.05rem;">
            To provide <strong>food, shelter, healthcare, education</strong> and
            emotional support to orphans and senior citizens through community
            driven initiatives, volunteer programs and generous donations.
          </p>
          <div class="quote-box mt-4">
            "Small acts of kindness can change the world for someone."
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOUNDING IDEA -->
<section class="section-green">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5 text-center">
        <div style="background: linear-gradient(135deg, #198754, #20c997); border-radius: 30px; padding: 50px 30px; color: white;">
          <i class="bi bi-lightbulb-fill" style="font-size: 5rem;"></i>
          <h3 class="mt-3 fw-bold">The Founding Idea</h3>
          <p class="mt-2 opacity-90">
            Born from the belief that everyone deserves a family that cares.
          </p>
        </div>
      </div>
      <div class="col-lg-7">
        <h2 class="section-title">How It Started</h2>
        <div class="about-card">
          <p style="color:#444; line-height:1.9; font-size:1.05rem;">
          The idea of<strong> Safe & Home Foundation </strong> born after witnessing<strong> two painful realities </strong>children growing up without parents, and elderly parents being left alone despite having families. This inspired us to create a place where both orphans and senior citizens can find love, care, and a sense of belonging.<strong>We believe that no one should feel abandoned, and everyone deserves a family that cares</strong>.
          </p>
          <p style="color:#444; line-height:1.9; font-size:1.05rem; margin-top:15px;">
            This project started as a <strong>college initiative</strong> to
            spread awareness and encourage youth participation in social welfare
            activities. It represents the unity of
            <strong>"technology and humanity"</strong> to make a real
            difference in people's lives.
          </p>
          <p style="color:#444; line-height:1.9; font-size:1.05rem; margin-top:15px;">
            What started as a simple idea has grown into a platform that
            connects <strong>donors, volunteers and beneficiaries</strong>
            under one digital roof.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title center">Our Impact in Numbers</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3 col-sm-6">
        <div class="stat-box">
          <div class="stat-number">500+</div>
          <div class="stat-label">Children Educated</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-box">
          <div class="stat-number">300+</div>
          <div class="stat-label">Elders Supported</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-box">
          <div class="stat-number">200+</div>
          <div class="stat-label">Volunteers</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-box">
          <div class="stat-number">50+</div>
          <div class="stat-label">Events Conducted</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT WE DO -->
<section class="section-green">
  <div class="container">
    <h2 class="section-title center text-center">What We Do</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-house-heart-fill"></i>
          </div>
          <h5>Shelter & Care</h5>
          <p>We provide safe homes and round-the-clock care to orphans and senior citizens who have nowhere to go.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-book-fill"></i>
          </div>
          <h5>Education</h5>
          <p>We fund education for orphaned children, giving them the tools to build a better future for themselves.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-hospital-fill"></i>
          </div>
          <h5>Healthcare</h5>
          <p>Regular medical checkups, medicines and healthcare support are provided to all our beneficiaries.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-basket-fill"></i>
          </div>
          <h5>Food & Nutrition</h5>
          <p>We ensure no one goes to bed hungry by providing nutritious meals to everyone under our care.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-people-fill"></i>
          </div>
          <h5>Volunteer Programs</h5>
          <p>We organize volunteer activities that bring joy and companionship to both children and elders.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card">
          <div class="value-icon">
            <i class="bi bi-cash-coin"></i>
          </div>
          <h5>Donation Drive</h5>
          <p>We accept and manage donations transparently to ensure every rupee reaches those who need it most.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title">Meet The Developers</h2>
    <p class="lead mb-5 subtitle">
      This project was built with passion by BCA Final Year students.
    </p>

    <div class="row justify-content-center g-4">

      <!-- Monisha -->
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="team-card">
          <div class="team-avatar">M</div>
          <h5>Monisha Jadav</h5>
          <p>Full Stack Developer</p>
          <span class="badge-role">BCA Final Year</span><br><br>
          <a href="monisha.php" class="portfolio-btn">Portfolio</a>
        </div>
      </div>

      <!-- Prarthna -->
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="team-card">
          <div class="team-avatar">P</div>
          <h5>Prarthna Sajeev Kumar</h5>
          <p>UI/UX Designer</p>
          <span class="badge-role">BCA Final Year</span><br><br>
            <a href="prarthana.php" class="portfolio-btn">Portfolio</a>
        </div>
      </div>

      <!-- Krishnaveni -->
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="team-card">
          <div class="team-avatar">G</div>
          <h5>G P Krishnaveni</h5>
          <p>Database Manager</p>
          <span class="badge-role">BCA Final Year</span>
        </div>
      </div>

      <!-- Aishwarya -->
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="team-card">
          <div class="team-avatar">A</div>
          <h5>Aishwarya P K</h5>
          <p>Content & Testing</p>
          <span class="badge-role">BCA Final Year</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2>Ready to Make a Difference?</h2>
    <p class="lead mt-3 mb-5 opacity-90">
      Join us in our mission to bring hope, love and care<br>
      to those who need it most.
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="donate.php" class="btn-cta btn">
        <i class="bi bi-heart-fill text-danger me-2"></i> Donate Now
      </a>
      <a href="volunteer.php" class="btn btn-outline-light btn-lg px-5" style="border-radius:50px; font-weight:700;">
        <i class="bi bi-people-fill me-2"></i> Volunteer
      </a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="footer">
  <div class="footer-container">
    <!-- Logo & About -->
    <div class="footer-box">
      <h2>🏠 Safe & Home Foundation</h2>
      <p>
        Providing care, shelter, and love to orphans and the elderly in need. 
        Caring for all generations with compassion.
      </p>
    </div>
    
    <!-- Quick Links -->
    <div class="footer-box">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="aboutus.php">→ About Us</a></li>
        <li><a href="contact.php">→ Contact</a></li>
        <li><a href="donate.php">→ Donate</a></li>
        <li><a href="volunteer.php">→ Volunteer</a></li>
        <li><a href="gallery.php">→ Gallery</a></li>
        <li><a href="internship.php">→ Internship</a></li>
      </ul>
    </div>
    
    <!-- Contact Info -->
    <div class="footer-box">
      <h3>Contact Us</h3>
      <p>📍 Agrahar Timsagar, India</p>
      <p>📞 +91 98765 43210</p>
      <p>📧 safehome@gmail.com</p>
      <p>🕐 Mon-Sat: 9:00 AM - 6:00 PM</p>
    </div>
    
    <!-- Donate Section -->
    <div class="footer-box">
      <h3>Support Us</h3>
      <p>Your small help can change a life ❤️</p>
      <a href="donate.php" class="donate-btn">💝 Donate Now</a>
      <div style="margin-top: 15px;">
        <a href="guidelines.php" style="color: #f4d03f; text-decoration: none; display: block; margin: 5px 0;">📋 Guidelines</a>
        <a href="terms.php" style="color: #f4d03f; text-decoration: none; display: block; margin: 5px 0;">📜 Terms & Conditions</a>
      </div>
    </div>
  </div>
  
  <!-- Social Media -->
   <div class="social-icons">
  <h5 class="fw-bold">Follow Us</h5>
  <a href="#" class="text-white me-3 fs-4"><i class="bi bi-facebook"></i></a>
  <a href="#" class="text-white me-3 fs-4"><i class="bi bi-instagram"></i></a>
  <a href="#" class="text-white fs-4"><i class="bi bi-twitter"></i></a>
</div>

  
  <!-- Bottom -->
  <div class="footer-bottom">
    <p>
      © 2025 Safe & Home Foundation | All Rights Reserved |
      Made with <span style="color: #f4d03f;">❤️</span> for a better tomorrow
    </p>
    <p style="margin-top: 5px; font-size: 12px;">
      <a href="terms.php">Terms & Conditions</a> | 
      <a href="guidelines.php">Privacy Policy</a> | 
      <a href="contact.php">Contact Support</a>
    </p>
  </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
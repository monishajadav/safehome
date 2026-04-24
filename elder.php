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
  <title>Elder Care | Safe & Home Foundation</title>

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
      background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                  url("./images/elder.jpg");
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
      left: 50%;
      transform: translateX(-50%);
      width: 70px;
      height: 4px;
      background: linear-gradient(90deg, #198754, #20c997);
      border-radius: 2px;
    }

    /* CARDS */
    .info-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
      height: 100%;
    }
    .info-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(25,135,84,0.2);
    }

    /* VALUE CARDS */
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

    /* STATS */
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

    /* STEPS */
    .step-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      height: 100%;
      position: relative;
    }
    .step-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 40px rgba(25,135,84,0.2);
    }
    .step-number {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(135deg, #198754, #20c997);
      color: white;
      font-size: 1.3rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
    }
    .step-card h5 {
      font-weight: 700;
      color: #198754;
    }

    /* CTA SECTION */
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
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .btn-cta:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      color: #198754;
    }

    /* FADE IN */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.8s ease;
    }
    .fade-in.show {
      opacity: 1;
      transform: translateY(0);
    }

    /* FOOTER */
    footer { background: #198754; color: white; }
    footer a { transition: all 0.3s ease; }
    footer a:hover { color: #ffd700 !important; }

    /* RESPONSIVE */
    @media (max-width: 991px) { .navbar-brand img { height: 60px; } }
    @media (max-width: 768px) {
      .hero h1 { font-size: 2.5rem; }
      .hero p { font-size: 1.1rem; }
      .section-title { font-size: 1.8rem; }
    }
  </style>
</head>

<body>

<!-- NAVBAR -->
<?php include "./includes/navbar.php" ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1>Care For The Elderly</h1>
    <p class="mt-3 px-md-5">
      "A generation who taught us how to live<br>
      are never a burden — they are our greatest blessing."
    </p>
    <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
      <a href="donate.php" class="btn btn-success btn-lg px-5">
        <i class="bi bi-heart-fill"></i> Donate Now
      </a>
      <a href="#how-we-help" class="btn btn-outline-light btn-lg px-5">
        <i class="bi bi-arrow-down-circle"></i> Learn More
      </a>
    </div>
  </div>
</section>

<!-- INTRO -->
<section class="section-green" id="how-we-help">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <h2 class="section-title">Our Commitment to Elders</h2>
        <div class="info-card">
          <p style="font-size:1.1rem; line-height:1.9; color:#444;">
            At <strong>Safe & Home Foundation</strong>, we believe that every elder
            deserves <strong>love, care, and respect</strong>. Many senior citizens
            today live alone, struggling for basic needs or emotional support.
          </p>
          <p style="font-size:1.1rem; line-height:1.9; color:#444; margin-top:15px;">
            Our mission is to stand by them and provide a
            <strong>safe, warm, and caring environment</strong> where they can
            live with dignity and peace.
          </p>
          <p style="font-size:1.1rem; line-height:1.9; color:#444; margin-top:15px;">
            We use your donations to support elders with
            <strong>food, medical assistance, and shelter</strong>.
            Every contribution helps bring dignity and comfort to their lives.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="highlight-box">
          <i class="bi bi-heart-fill fs-1 mb-3 d-block opacity-75"></i>
          <p>
            "Caring for our elders is not charity — it is our duty. They spent
            their lives building the world we live in. It is our turn to give
            back with the same love and dedication they showed us."
          </p>
          <p class="mt-3 fw-bold mb-0">— Safe & Home Foundation</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title">Our Impact on Elder Lives</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3 col-sm-6 fade-in">
        <div class="stat-box">
          <div class="stat-number">300+</div>
          <div class="stat-label">Elders Supported</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 fade-in">
        <div class="stat-box">
          <div class="stat-number">50+</div>
          <div class="stat-label">Medical Camps</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 fade-in">
        <div class="stat-box">
          <div class="stat-number">100+</div>
          <div class="stat-label">Volunteers</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6 fade-in">
        <div class="stat-box">
          <div class="stat-number">20+</div>
          <div class="stat-label">Partner Homes</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title">How We Help Elders</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-house-heart-fill"></i></div>
          <h5>Safe Accommodation</h5>
          <p>Clean, peaceful and safe living spaces for senior citizens who have no one to care for them.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-hospital-fill"></i></div>
          <h5>Healthcare</h5>
          <p>Regular medical checkups, emergency care, and medicine support to keep elders healthy.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-basket-fill"></i></div>
          <h5>Food & Nutrition</h5>
          <p>Nutritious and balanced meals provided daily to ensure no elder goes to bed hungry.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-emoji-smile-fill"></i></div>
          <h5>Emotional Support</h5>
          <p>Companionship, counseling, and social activities to ensure mental well-being.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-people-fill"></i></div>
          <h5>Community Events</h5>
          <p>Regular events, celebrations and activities to keep elders engaged and joyful.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-telephone-fill"></i></div>
          <h5>24/7 Support</h5>
          <p>Round the clock support and assistance for all emergency and non-emergency needs.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section-green">
  <div class="container">
    <h2 class="section-title text-center">How You Can Help</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3 fade-in">
        <div class="step-card">
          <div class="step-number">1</div>
          <h5>Donate</h5>
          <p style="color:#666;">Your donation directly funds food, medicine and shelter for elders in need.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card">
          <div class="step-number">2</div>
          <h5>Volunteer</h5>
          <p style="color:#666;">Spend time with elders, help with activities or assist in medical camps.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card">
          <div class="step-number">3</div>
          <h5>Spread Awareness</h5>
          <p style="color:#666;">Share our mission with friends and family to reach more people in need.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card">
          <div class="step-number">4</div>
          <h5>Partner With Us</h5>
          <p style="color:#666;">Organizations can partner with us to expand our reach and impact.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="section-green">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <div class="quote-box text-center" style="border-left: none; border-top: 6px solid #198754; border-radius: 15px;">
          <i class="bi bi-quote fs-1 text-success opacity-50"></i>
          <p class="fs-4 fw-semibold mt-2">
            "The way a society treats its elders is a reflection of its values.
            Let us choose compassion, dignity and love."
          </p>
          <p class="fw-bold text-success mt-3">— Safe & Home Foundation</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2 style="font-size:2.5rem; font-weight:800;">Make a Difference Today</h2>
    <p class="lead mt-3 mb-5 opacity-90">
      Your support can change the life of an elder who has no one.<br>
      Every rupee counts. Every act of kindness matters.
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="donate.php" class="btn-cta btn">
        <i class="bi bi-heart-fill text-danger me-2"></i> Donate Now
      </a>
      <a href="volunteer.php" class="btn btn-outline-light btn-lg px-5" style="border-radius:50px; font-weight:700;">
        <i class="bi bi-people-fill me-2"></i> Volunteer With Us
      </a>
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
         <a href="guidelines.php" class="text-white d-block"><i class="bi bi-arrow-right"></i> Guide</a>
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
// Fade in animation
const fadeElements = document.querySelectorAll(".fade-in");
const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add("show");
    });
  },
  { threshold: 0.1 }
);
fadeElements.forEach((el) => observer.observe(el));
</script>

</body>
</html>
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
  <title>Safe & Home Foundation - NGO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link href="css/common.css" rel="stylesheet" />
<style>
* {
  font-family: 'Poppins', sans-serif;
}

html { 
  scroll-behavior: smooth; 
}

body {
  margin: 0;
  overflow-x: hidden;
  background: linear-gradient(135deg, #14532d, #1f7a4c, #e8f5e9);
  background-attachment: fixed;
}

/* ================= NAVBAR ================= */
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
/* ================= HERO ================= */
.hero-header {
  background: url("./images/index.png") center/cover no-repeat;
  min-height: 90vh;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  position: relative;
  color: white;
}

.hero-header::after {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
}

.hero-header .container {
  position: relative;
  z-index: 2;
}

.hero-header h1 {
  font-size: 3.2rem;
  font-weight: 800;
}

.hero-header p {
  font-size: 1.2rem;
  opacity: 0.95;
}

/* ================= SECTIONS ================= */

section {
  padding: 80px 0;
  background: transparent;
}

/* ================= SECTION TITLES ================= */

.section-title {
  font-size: 2.4rem;
  font-weight: 700;
  margin-bottom: 3rem;
  position: relative;
  display: inline-block;
  color: #ffffff;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 70px;
  height: 4px;
  background: #ffffff;
  border-radius: 2px;
}

/* ================= CARDS ================= */

.card {
  border: none;
  border-radius: 18px;
  overflow: hidden;
  transition: 0.3s ease;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.card:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.card img {
  height: 220px;
  object-fit: cover;
}

/* ================= IMPACT SECTION ================= */

.impact-section {
  background: transparent;
  color: #ffffff;
}

.count {
  font-size: 3rem;
  font-weight: 800;
}

.impact-section p {
  font-size: 1.1rem;
}

/* ================= HELP CARDS ================= */

.help-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 18px;
  padding: 40px 20px;
  transition: 0.3s ease;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.help-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 18px 35px rgba(0,0,0,0.15);
}

/* ================= TESTIMONIALS ================= */

.testimonial-card {
  border-radius: 18px;
  padding: 30px;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  transition: 0.3s ease;
}

.testimonial-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.12);
}

/* ================= BUTTONS ================= */

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
  text-decoration: none;
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
/* ================= MOBILE ================= */

@media (max-width: 991px) {
  .navbar-brand img {
    height: 60px;
  }
}

@media (max-width: 768px) {
  .hero-header h1 { font-size: 2.3rem; }
  .section-title { font-size: 2rem; }
  .count { font-size: 2.3rem; }
}
</style>

</head>

<body>

<!-- NAVBAR -->
<?php include "./includes/navbar.php" ?>

<!-- HERO -->
<header class="hero-header text-white">
  <div class="container">
    <?php if($is_logged_in): ?>
      <div class="welcome-badge">
        <h4 class="text-success mb-0"><i class="bi bi-heart-fill"></i> Welcome back, <?php echo htmlspecialchars($username); ?>!</h4>
      </div>
    <?php endif; ?>

    <h1>Welcome to Safe & Home Foundation</h1>
    <p class="lead fw-semibold px-md-5">
      "Give a hand to the generation that started our journey,<br>and a foundation to the generation that continues it."
    </p>
    <a href="donate.php" class="btn btn-warning btn-lg text-white mt-3">
      <i class="bi bi-heart-fill"></i> Donate Now
    </a>
  </div>
</header>

<!-- ABOUT -->
<section class="about-section py-5 text-center">
  <div class="container">
    <h2 class="section-title">About Us</h2>
    <p class="lead px-md-5">
      We are a non-profit foundation dedicated to supporting orphans and elders by providing care,
      shelter, and education in a safe and loving environment for all generations.
    </p>
  </div>
</section>

<!-- WHO WE HELP -->
<section class="who-we-help-section py-5">
  <div class="container text-center">
    <h2 class="section-title">Who We Help</h2>
    <div class="row g-4">
      <div class="col-md-3 fade-in-up stagger-1">
        <div class="card shadow-sm h-100">
          <img src="images/elder.jpg" class="card-img-top" alt="Elder Care">
          <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-heart-pulse text-success"></i> Elder Care</h5>
            <p>We provide shelter, healthcare and love to senior citizens.</p>
            <a href="elder.php" class="btn btn-success btn-sm">Learn More</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 fade-in-up stagger-2">
        <div class="card shadow-sm h-100">
          <img src="images/children.jpg" class="card-img-top" alt="Child Support">
          <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-mortarboard text-success"></i> Child Support</h5>
            <p>Education, nutrition and safe homes for children.</p>
            <a href="children.php" class="btn btn-success btn-sm">Learn More</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 fade-in-up stagger-3">
        <div class="card shadow-sm h-100">
          <img src="images/volunteer.jpg" class="card-img-top" alt="Volunteer Programs">
          <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-people text-success"></i> Volunteer</h5>
            <p>Join us in making a difference in lives.</p>
            <a href="volunteer.php" class="btn btn-success btn-sm">Learn More</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 fade-in-up stagger-4">
        <div class="card shadow-sm h-100">
          <img src="images/internship.jpg" class="card-img-top" alt="Internship Program">
          <div class="card-body">
            <h5 class="fw-bold"><i class="bi bi-briefcase text-success"></i> Internship</h5>
            <p>Gain real-world experience while supporting our mission.</p>
            <a href="internship.php" class="btn btn-success btn-sm">Learn More</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="quote-section py-5 text-center">
  <div class="container position-relative">
    <blockquote class="blockquote">
      <p class="mb-0 fs-3 fw-semibold fst-italic px-md-5">
        "Helping one person might not change the world, but it could change the world for one person."
      </p>
    </blockquote>
  </div>
</section>

<!-- IMPACT -->
<section class="impact-section py-5 text-white position-relative">
  <div class="container text-center position-relative">
    <h2 class="section-title text-white">Our Impact</h2>
    <div class="row">
      <div class="col-md-3">
        <i class="bi bi-book fs-1 mb-3"></i>
        <h3 class="count" data-target="507">0</h3>
        <p>Children Educated</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-heart-fill fs-1 mb-3"></i>
        <h3 class="count" data-target="300">0</h3>
        <p>Elders Supported</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-people-fill fs-1 mb-3"></i>
        <h3 class="count" data-target="200">0</h3>
        <p>Volunteers</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-calendar-event fs-1 mb-3"></i>
        <h3 class="count" data-target="50">0</h3>
        <p>Events Conducted</p>
      </div>
    </div>
  </div>
</section>

<!-- HOW YOU CAN HELP -->
<section class="help-section py-5">
  <div class="container text-center">
    <h2 class="section-title">How You Can Help</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="help-card">
          <i class="bi bi-cash-coin" style="font-size: 4rem; color: #198754;"></i>
          <h5 class="mt-3 fw-bold">Donate</h5>
          <p>Your donations help us provide food, shelter, and education.</p>
          <a href="donate.php" class="btn btn-success">Donate Now</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="help-card">
          <i class="bi bi-people" style="font-size: 4rem; color: #198754;"></i>
          <h5 class="mt-3 fw-bold">Volunteer</h5>
          <p>Join our team and make a direct impact in people's lives.</p>
          <a href="volunteer.php" class="btn btn-success">Join Us</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="help-card">
          <i class="bi bi-share" style="font-size: 4rem; color: #198754;"></i>
          <h5 class="mt-3 fw-bold">Internship</h5>
          <p>Join our team and make a direct impact in people's lives in Digital World.</p>
          <a href="internship.php" class="btn btn-success">Join Us</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section py-5 text-center">
  <div class="container">
    <h2 class="fw-bold mb-3">Be Part of the Change</h2>
    <p class="lead mb-4">Your small contribution can make a huge difference in someone's life.</p>
    <a href="donate.php" class="btn btn-success btn-lg">
      <i class="bi bi-heart-fill"></i> Donate Now
    </a>
  </div>
</section>


<!-- TESTIMONIALS -->
<section class="testimonial-section py-5">
  <div class="container">
    <h2 class="section-title text-center">What People Say</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testimonial-card">
          <p class="fst-italic">"This foundation changed my life. They provided education and hope when I needed it most."</p>
          <p class="mb-0 fw-bold text-success">- Rahul, Beneficiary</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card">
          <p class="fst-italic">"Volunteering here has been the most rewarding experience of my life. Truly life-changing!"</p>
          <p class="mb-0 fw-bold text-success">- Priya, Volunteer</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card">
          <p class="fst-italic">"They take such good care of our elders with love, respect and dignity. Highly recommend!"</p>
          <p class="mb-0 fw-bold text-success">- Amit, Donor</p>
        </div>
      </div>
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
      <p>📍 Moodubidri,Karnataka,India</p>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Counter Animation
const counters = document.querySelectorAll(".count");
let hasAnimated = false;

function animateCounts() {
  counters.forEach(counter => {
    const target = +counter.getAttribute("data-target");
    const speed = 100;
    const increment = target / speed;

    const update = () => {
      const current = +counter.innerText;
      if (current < target) {
        counter.innerText = Math.ceil(current + increment);
        setTimeout(update, 30);
      } else {
        counter.innerText = target + "+";
      }
    };
    update();
  });
}

const impactSection = document.querySelector(".impact-section");

window.addEventListener("scroll", () => {
  if (!hasAnimated && impactSection.getBoundingClientRect().top < window.innerHeight) {
    hasAnimated = true;
    animateCounts();
  }
});

// Scroll animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, observerOptions);

document.querySelectorAll('.fade-in-up').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = 'all 0.6s ease-out';
  observer.observe(el);
});
</script>

</body>
</html>
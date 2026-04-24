<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

$success = "";
$error = "";

if(isset($_POST['submit'])) {
    $full_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $college = mysqli_real_escape_string($conn, trim($_POST['college']));
    $course = mysqli_real_escape_string($conn, trim($_POST['course']));
    $year = mysqli_real_escape_string($conn, trim($_POST['year']));
    $area = mysqli_real_escape_string($conn, trim($_POST['area']));
    $duration = mysqli_real_escape_string($conn, trim($_POST['duration']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if(empty($full_name) || empty($email) || empty($phone) || empty($college) || empty($course) || empty($year) || empty($area) || empty($duration)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO internship_applications (full_name, email, phone, college, course, year, area, duration, message) 
                VALUES ('$full_name', '$email', '$phone', '$college', '$course', '$year', '$area', '$duration', '$message')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Your internship application has been submitted successfully! We will contact you soon.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Internship | Safe & Home Foundation</title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    /* NAVBAR */
    .navbar { padding: 0.75rem 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .navbar-brand img { height: 80px; }
    .nav-link { font-size: 0.95rem; font-weight: 500; padding: 0.5rem 1rem !important; transition: color 0.2s; }
    .nav-link:hover { color: #ffc107 !important; }
    .dropdown-menu { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 8px; }
    .dropdown-item:hover { background: #f8f9fa; color: #198754; }
    .btn-warning { background: #ffc107; border: none; font-weight: 600; }
    .btn-outline-light { border-width: 2px; font-weight: 600; }
    .btn-outline-light:hover { background: white; color: #198754; }

    /* HERO */
  
      .hero {
  background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
    url("./images/internship.jpg");
  background-size: cover;
  background-position: center center;  /* Center both horizontally and vertically */
  background-attachment: scroll;  /* Changed from fixed for better mobile support */
  min-height: 100vh;
  height: 100vh;  /* Fixed height */
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: #fff;
  padding: 0 20px;
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

    /* SAME GREEN GRADIENT */
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

    /* INFO CARD */
    .info-card {
      background: white;
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      transition: transform 0.3s ease;
      height: 100%;
    }
    .info-card:hover { transform: translateY(-8px); }

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
    .value-card h5 { font-weight: 700; color: #198754; margin-bottom: 10px; }
    .value-card p { color: #666; font-size: 0.95rem; margin: 0; }

    /* HIGHLIGHT BOX */
    .highlight-box {
      background: linear-gradient(135deg, #198754, #20c997);
      border-radius: 20px;
      padding: 35px;
      color: white;
    }

    /* STEP CARDS */
    .step-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      height: 100%;
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
    .step-card h5 { font-weight: 700; color: #198754; }

    /* FORM */
    .form-card {
      background: white;
      border-radius: 25px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.1);
    }
    .form-control, .form-select {
      border-radius: 10px;
      border: 2px solid #e0e0e0;
      padding: 12px 15px;
      font-size: 0.95rem;
      transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.15);
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
      box-shadow: 0 5px 20px rgba(25,135,84,0.4);
    }
    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(25,135,84,0.5);
      color: white;
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
      .section-title { font-size: 1.8rem; }
      .form-card { padding: 30px 20px; }
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
    <h1>Internship Program</h1>
    <p class="mt-3 px-md-5">
      "Gain real-world experience while making<br>
      a meaningful difference in people's lives."
    </p>
    <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
      <a href="#apply" class="btn btn-success btn-lg px-5">
        <i class="bi bi-pencil-fill"></i> Apply Now
      </a>
      <a href="#about-internship" class="btn btn-outline-light btn-lg px-5">
        <i class="bi bi-arrow-down-circle"></i> Learn More
      </a>
    </div>
  </div>
</section>

<!-- ABOUT INTERNSHIP -->
<section class="section-green" id="about-internship">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <h2 class="section-title">About Our Internship</h2>
        <div class="info-card">
          <p style="font-size:1.1rem; line-height:1.9; color:#444;">
            Our internship program offers <strong>BCA, BSc, and other students</strong>
            a unique opportunity to gain hands-on experience while contributing
            to a meaningful cause.
          </p>
          <p style="font-size:1.1rem; line-height:1.9; color:#444; margin-top:15px;">
            Interns work directly with our team to support
            <strong>elder care, child welfare, content creation, IT support,
            and community outreach</strong> programs.
          </p>
          <p style="font-size:1.1rem; line-height:1.9; color:#444; margin-top:15px;">
            At the end of the internship, you'll receive a
            <strong>certificate of completion</strong> and a
            <strong>letter of recommendation</strong> from our foundation.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="highlight-box">
          <i class="bi bi-briefcase-fill fs-1 mb-3 d-block opacity-75"></i>
          <p style="font-size:1.2rem; line-height:1.9;">
            "An internship here is not just about adding a line to your resume.
            It's about growing as a human being, developing empathy, and
            understanding the true meaning of service."
          </p>
          <p class="mt-3 fw-bold mb-0">— Safe & Home Foundation</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHAT YOU WILL GAIN -->
<section class="section-green">
  <div class="container text-center">
    <h2 class="section-title">What You Will Gain</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-award-fill"></i></div>
          <h5>Certificate</h5>
          <p>Receive an official internship certificate recognized by our foundation.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
          <h5>Recommendation Letter</h5>
          <p>Get a strong letter of recommendation for your future career or studies.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-laptop-fill"></i></div>
          <h5>Real Experience</h5>
          <p>Work on real projects and gain hands-on experience in your field.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-people-fill"></i></div>
          <h5>Network</h5>
          <p>Connect with like-minded students, professionals and community leaders.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-heart-fill"></i></div>
          <h5>Make an Impact</h5>
          <p>Contribute to a cause that truly matters and change lives for the better.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in">
        <div class="value-card">
          <div class="value-icon"><i class="bi bi-graph-up-arrow"></i></div>
          <h5>Personal Growth</h5>
          <p>Develop leadership, communication and problem-solving skills.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- INTERNSHIP AREAS -->
<section class="section-green">
  <div class="container">
    <h2 class="section-title text-center">Internship Areas</h2>
    <div class="row g-4 mt-2">
      <div class="col-md-3 fade-in">
        <div class="step-card text-center">
          <div class="value-icon mx-auto mb-3">
            <i class="bi bi-code-slash"></i>
          </div>
          <h5>IT & Web Development</h5>
          <p style="color:#666; font-size:0.9rem;">Help build and maintain our website and digital platforms.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card text-center">
          <div class="value-icon mx-auto mb-3">
            <i class="bi bi-camera-fill"></i>
          </div>
          <h5>Content & Media</h5>
          <p style="color:#666; font-size:0.9rem;">Create content, manage social media and document our work.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card text-center">
          <div class="value-icon mx-auto mb-3">
            <i class="bi bi-heart-pulse-fill"></i>
          </div>
          <h5>Elder Care Support</h5>
          <p style="color:#666; font-size:0.9rem;">Assist in organizing activities and support for senior citizens.</p>
        </div>
      </div>
      <div class="col-md-3 fade-in">
        <div class="step-card text-center">
          <div class="value-icon mx-auto mb-3">
            <i class="bi bi-mortarboard-fill"></i>
          </div>
          <h5>Child Education</h5>
          <p style="color:#666; font-size:0.9rem;">Support teaching and educational activities for orphaned children.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- APPLICATION FORM -->
<section class="section-green" id="apply">
  <div class="container">
    <h2 class="section-title text-center">Apply for Internship</h2>
    <p class="text-center mb-5" style="color:#555; font-size:1.05rem;">
      Fill in the form below and we'll get back to you within 3-5 working days.
    </p>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="form-card">

          <?php if($success): ?>
            <div class="alert alert-success border-0 rounded-3 p-4 mb-4">
              <i class="bi bi-check-circle-fill fs-4 me-2"></i>
              <strong><?php echo $success; ?></strong>
            </div>
          <?php endif; ?>

          <?php if($error): ?>
            <div class="alert alert-danger border-0 rounded-3 p-4 mb-4">
              <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i>
              <strong><?php echo $error; ?></strong>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="row g-4">

              <!-- Full Name -->
              <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" name="full_name" class="form-control" placeholder="Your full name" required>
                </div>
              </div>

              <!-- Email -->
              <div class="col-md-6">
                <label class="form-label">Email Address *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                  <input type="email" name="email" class="form-control" placeholder="Your email" required>
                </div>
              </div>

              <!-- Phone -->
              <div class="col-md-6">
                <label class="form-label">Phone Number *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input type="tel" name="phone" class="form-control" placeholder="Your phone number" required>
                </div>
              </div>

              <!-- College -->
              <div class="col-md-6">
                <label class="form-label">College/University *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-building-fill"></i></span>
                  <input type="text" name="college" class="form-control" placeholder="Your college name" required>
                </div>
              </div>

              <!-- Course -->
              <div class="col-md-6">
                <label class="form-label">Course *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-book-fill"></i></span>
                  <input type="text" name="course" class="form-control" placeholder="eg. BCA, BSc CS" required>
                </div>
              </div>

              <!-- Year -->
              <div class="col-md-6">
                <label class="form-label">Year of Study *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-calendar-fill"></i></span>
                  <select name="year" class="form-select" required>
                    <option value="">Select Year</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="Final Year">Final Year</option>
                    <option value="Graduated">Graduated</option>
                  </select>
                </div>
              </div>

              <!-- Area of Interest -->
              <div class="col-md-6">
                <label class="form-label">Area of Interest *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-stars"></i></span>
                  <select name="area" class="form-select" required>
                    <option value="">Select Area</option>
                    <option value="IT & Web Development">IT & Web Development</option>
                    <option value="Content & Media">Content & Media</option>
                    <option value="Elder Care Support">Elder Care Support</option>
                    <option value="Child Education">Child Education</option>
                    <option value="Community Outreach">Community Outreach</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>

              <!-- Duration -->
              <div class="col-md-6">
                <label class="form-label">Preferred Duration *</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                  <select name="duration" class="form-select" required>
                    <option value="">Select Duration</option>
                    <option value="1 Month">1 Month</option>
                    <option value="2 Months">2 Months</option>
                    <option value="3 Months">3 Months</option>
                    <option value="6 Months">6 Months</option>
                  </select>
                </div>
              </div>

              <!-- Message -->
              <div class="col-12">
                <label class="form-label">Why do you want to intern with us?</label>
                <textarea name="message" class="form-control" rows="4" placeholder="Tell us about yourself and your motivation..."></textarea>
              </div>

              <!-- Submit -->
              <div class="col-12 text-center mt-3">
                <button type="submit" name="submit" class="btn-submit btn">
                  <i class="bi bi-send-fill me-2"></i> Submit Application
                </button>
              </div>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <h2 style="font-size:2.5rem; font-weight:800;">Ready to Start Your Journey?</h2>
    <p class="lead mt-3 mb-5 opacity-90">
      Join us and make a real difference while building your career.<br>
      Apply today and become part of our mission!
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="#apply" class="btn-cta btn">
        <i class="bi bi-pencil-fill text-success me-2"></i> Apply Now
      </a>
      <a href="volunteer.php" class="btn btn-outline-light btn-lg px-5" style="border-radius:50px; font-weight:700;">
        <i class="bi bi-people-fill me-2"></i> Volunteer Instead
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
        <a href="guidelines.php" class="text-white d-block"><i class="bi bi-arrow-right"></i> Guidelines</a>
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
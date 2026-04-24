<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

$success = "";
$error = "";

// Handle volunteer form submission
if(isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $care_area = mysqli_real_escape_string($conn, $_POST['care_area']);
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    if(empty($name) || empty($email) || empty($phone) || empty($care_area)) {
        $error = "Please fill in all required fields.";
    } else {
        // First, create volunteer_applications table if it doesn't exist
        $create_table = "CREATE TABLE IF NOT EXISTS volunteer_applications (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(15) NOT NULL,
            care_area VARCHAR(50) NOT NULL,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        mysqli_query($conn, $create_table);
        
        // Insert volunteer application
        $sql = "INSERT INTO volunteer_applications (name, email, phone, care_area, message) 
                VALUES ('$name', '$email', '$phone', '$care_area', '$message')";
        
        if(mysqli_query($conn, $sql)) {
            $success = "Thank you for your interest! We'll contact you soon.";
            $_POST = array();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volunteer | Safe & Home Foundation</title>

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

    /* ANIMATED GRADIENT BACKGROUND */
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

    /* NAVBAR */
    .navbar { 
      padding: 0.75rem 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      background: rgba(25,135,84,0.95) !important;
      backdrop-filter: blur(10px);
    }

    .navbar-brand img { 
      height: 80px;
    }

    .nav-link { 
      font-size: 0.95rem;
      font-weight: 500;
      padding: 0.5rem 1rem !important;
      transition: color 0.2s;
    }

    .nav-link:hover { 
      color: #ffc107 !important;
    }

    .dropdown-menu {
      border: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 8px;
      margin-top: 0.5rem;
    }

    .dropdown-item {
      padding: 0.6rem 1.2rem;
      transition: background 0.2s;
    }

    .dropdown-item:hover {
      background: #f8f9fa;
      color: #198754;
    }

    .btn-warning {
      background: #ffc107;
      border: none;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-warning:hover {
      background: #ffb300;
      transform: translateY(-1px);
    }

    .btn-outline-light {
      border-width: 2px;
      font-weight: 600;
      transition: all 0.2s;
    }

    .btn-outline-light:hover {
      background: white;
      color: #198754;
    }

    /* HERO SECTION */
    .hero {
      background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)),
        url("./images/volunteer.jpg");
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      min-height: 90vh;
      display: flex;
      align-items: center;
      text-align: center;
      color: #fff;
    }

    .hero h1 {
      font-size: 3.5rem;
      font-weight: 800;
      text-shadow: 3px 3px 15px rgba(0,0,0,0.5);
      animation: fadeInUp 1s ease-out;
    }

    .hero p {
      font-size: 1.3rem;
      max-width: 850px;
      margin: 20px auto 30px;
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

    /* SECTIONS */
    section:not(.hero) {
      padding: 80px 0;
    }

    .content-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 50px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.2);
      margin-bottom: 30px;
    }

    .section-title {
      color: #198754;
      font-weight: 700;
      text-transform: uppercase;
      text-align: center;
      margin-bottom: 40px;
      font-size: 2.5rem;
      position: relative;
      display: inline-block;
      width: 100%;
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

    .card {
      border: none;
      border-radius: 15px;
      transition: all 0.3s ease;
      background: white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      height: 100%;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .btn-success {
      background: linear-gradient(135deg, #198754 0%, #20c997 100%);
      border: none;
      font-weight: 700;
      padding: 12px 40px;
      border-radius: 50px;
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(25,135,84,0.3);
    }

    .btn-success:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(25,135,84,0.4);
    }

    /* FORM STYLING */
    .form-control, .form-select {
      border-radius: 10px;
      padding: 12px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25);
    }

    /* FOOTER */
    footer {
      background: rgba(25,135,84,0.95) !important;
      backdrop-filter: blur(10px);
      color: #fff;
      text-align: center;
      padding: 25px 0;
      box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }

    footer a {
      transition: all 0.3s ease;
    }

    footer a:hover {
      color: #ffd700 !important;
      transform: translateX(5px);
    }

    /* RESPONSIVE */
    @media (max-width: 991px) {
      .navbar-brand img {
        height: 60px;
      }
      
      .hero h1 { font-size: 2.5rem; }
      .hero p { font-size: 1.1rem; }
      .section-title { font-size: 2rem; }
      .content-card { padding: 30px; }
    }

    @media (max-width: 768px) {
      .hero h1 { font-size: 2rem; }
    }
  </style>
</head>

<body>

  <!-- NAVBAR -->
  <?php include "./includes/navbar.php" ?>
  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <h1>Volunteer Across Generations</h1>
      <p>
        Serve orphaned children and elderly residents by offering your time, compassion, and skills. 
        Together, we create care, dignity, and hope at every stage of life.
      </p>
      <a href="#volunteer-form" class="btn btn-success btn-lg">
        <i class="bi bi-hand-thumbs-up-fill"></i> Become a Volunteer
      </a>
    </div>
  </section>

  <!-- WHY VOLUNTEER -->
  <section class="container">
    <div class="content-card">
      <h2 class="section-title">Why Volunteer With Us</h2>
      <div class="row g-4">
        <div class="col-md-3 col-sm-6">
          <div class="card p-4 text-center">
            <i class="bi bi-people-fill text-success fs-1 mb-3"></i>
            <p class="mb-0">Support lives across generations</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card p-4 text-center">
            <i class="bi bi-heart-fill text-success fs-1 mb-3"></i>
            <p class="mb-0">Make a meaningful social impact</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card p-4 text-center">
            <i class="bi bi-clock-fill text-success fs-1 mb-3"></i>
            <p class="mb-0">Flexible roles & time commitment</p>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card p-4 text-center">
            <i class="bi bi-award-fill text-success fs-1 mb-3"></i>
            <p class="mb-0">Training, guidance & certification</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- VOLUNTEER ROLES -->
  <section class="container">
    <div class="content-card">
      <h2 class="section-title">Volunteer Roles</h2>

      <h4 class="text-success mb-3"><i class="bi bi-people"></i> Children Care</h4>
      <div class="row g-4 mb-5">
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-book-fill text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Teaching & Tutoring</h6>
            <p class="mb-0">Academic support and learning assistance</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-heart-pulse-fill text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Child Mentoring</h6>
            <p class="mb-0">Guidance, emotional care, and supervision</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-palette-fill text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Activity Support</h6>
            <p class="mb-0">Games, arts, and creative engagement</p>
          </div>
        </div>
      </div>

      <h4 class="text-success mb-3"><i class="bi bi-heart-pulse"></i> Old Age Home Care</h4>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-chat-heart-fill text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Elder Companionship</h6>
            <p class="mb-0">Conversation, companionship, and emotional support</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-hospital-fill text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Wellness & Assistance</h6>
            <p class="mb-0">Basic health support and daily activities</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-4 text-center">
            <i class="bi bi-music-note-beamed text-success fs-2 mb-3"></i>
            <h6 class="fw-bold">Recreation & Engagement</h6>
            <p class="mb-0">Music, yoga, storytelling, and group activities</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHO CAN VOLUNTEER -->
  <section class="container">
    <div class="content-card text-center">
      <h2 class="section-title">Who Can Volunteer</h2>
      <p class="fs-5">
        Anyone above 18 years with empathy and responsibility can volunteer. Students, professionals, 
        homemakers, and retirees are welcome. Some roles may require patience, physical assistance, 
        or background verification.
      </p>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="container">
    <div class="content-card">
      <h2 class="section-title">How It Works</h2>
      <div class="row g-4">
        <div class="col-md-3 text-center">
          <div class="card p-4">
            <div class="display-4 text-success mb-3">1</div>
            <h6 class="fw-bold">Submit Application</h6>
          </div>
        </div>
        <div class="col-md-3 text-center">
          <div class="card p-4">
            <div class="display-4 text-success mb-3">2</div>
            <h6 class="fw-bold">Orientation & Training</h6>
          </div>
        </div>
        <div class="col-md-3 text-center">
          <div class="card p-4">
            <div class="display-4 text-success mb-3">3</div>
            <h6 class="fw-bold">Choose Care Area</h6>
          </div>
        </div>
        <div class="col-md-3 text-center">
          <div class="card p-4">
            <div class="display-4 text-success mb-3">4</div>
            <h6 class="fw-bold">Start Volunteering</h6>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- VOLUNTEER FORM -->
  <section class="container" id="volunteer-form">
    <div class="content-card">
      <h2 class="section-title">Volunteer Application</h2>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          
          <?php if($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle"></i> <?php echo $success; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
              <input id="name" name="name" type="text" class="form-control" placeholder="Enter your full name" required />
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input id="email" name="email" type="email" class="form-control" placeholder="your.email@example.com" required />
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input id="phone" name="phone" type="tel" class="form-control" placeholder="+91 XXXXX XXXXX" required />
              </div>
            </div>

            <div class="mb-3">
              <label for="care_area" class="form-label">Preferred Care Area <span class="text-danger">*</span></label>
              <select id="care_area" name="care_area" class="form-select" required>
                <option value="">Select Care Area</option>
                <option value="Orphan Care">Orphan Care</option>
                <option value="Old Age Home Care">Old Age Home Care</option>
                <option value="Both">Both</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="message" class="form-label">Why do you want to volunteer?</label>
              <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tell us about your motivation and availability..."></textarea>
            </div>

            <button type="submit" name="submit" class="btn btn-success w-100 btn-lg">
              <i class="bi bi-send-fill"></i> Submit Application
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="container">
    <div class="content-card">
      <h2 class="section-title">Volunteer Experiences</h2>
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card p-4">
            <i class="bi bi-quote text-success fs-1"></i>
            <p class="fst-italic">"Helping children grow with confidence has been life-changing."</p>
            <strong class="text-success">- Sneha, Children Care Volunteer</strong>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-4">
            <i class="bi bi-quote text-success fs-1"></i>
            <p class="fst-italic">"Spending time with elders taught me patience, empathy, and gratitude."</p>
            <strong class="text-success">- Ramesh, Old Age Home Volunteer</strong>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FINAL CTA -->
  <section class="container text-center">
    <div class="content-card">
      <h2 class="section-title">Be There for Someone Who Needs You</h2>
      <p class="fs-5 mb-4">
        Whether it's a child seeking guidance or an elder seeking companionship, your presence can change a life.
      </p>
      <a href="#volunteer-form" class="btn btn-success btn-lg">
        <i class="bi bi-hand-thumbs-up-fill"></i> Volunteer Today
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
      <hr class="bg-white mt-4 opacity-25">
      <p class="text-center mb-0">© 2025 Safe & Home Foundation | All Rights Reserved | Made with <i class="bi bi-heart-fill"></i> for a better tomorrow</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
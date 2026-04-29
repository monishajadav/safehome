<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $is_logged_in ? $_SESSION['username'] : '';

$success = "";
$error = "";

// Handle form submission
if (isset($_POST['submit'])) {
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $message = mysqli_real_escape_string($conn, $_POST['message']);
  $volunteer = isset($_POST['volunteer']) ? 'Yes' : 'No';

  // Validation
  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $error = "Please fill all required fields.";
  } else {
    // Combine all info into message
    $full_message = "Subject: $subject\nVolunteer Interest: $volunteer\n\n$message";

    $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES ('$name', '$email', '$phone', '$full_message')";

    if (mysqli_query($conn, $sql)) {
      $success = "Thank you! Your message has been sent successfully. We'll contact you soon.";
      // Clear form after successful submission
      $_POST = array();
    } else {
      $error = "Error: " . mysqli_error($conn);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us | Safe & Home Foundation</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
        :root {
      --primary-color: #28a745;
      /* Green - Hope & care */
      --secondary-color: #17a2b8;
      /* Teal/Cyan - Trust & calm */
      --accent-color: #ffc107;
      /* Yellow - Warmth & optimism */
      --dark-color: #343a40;
      /* Dark gray for text */
    }


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

    .contact-header {
      background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
        url("images/contact.jpg");
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      color: white;
      padding: 120px 0;
      text-align: center;
      animation: fadeIn 1s ease-in;
      position: relative;
      min-height: 120vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

 
    .contact-header h1 {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .contact-section {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 50px;
      margin: -80px auto 50px;
      box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
      max-width: 1200px;
      animation: slideUp 1s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(50px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-control,
    .form-select {
      border-radius: 10px;
      padding: 12px;
      border: 2px solid #e9ecef;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
      transform: translateY(-2px);
    }

    .form-group {
      animation: fadeInUp 0.6s ease-out backwards;
    }

    .form-group:nth-child(1) {
      animation-delay: 0.1s;
    }

    .form-group:nth-child(2) {
      animation-delay: 0.2s;
    }

    .form-group:nth-child(3) {
      animation-delay: 0.3s;
    }

    .form-group:nth-child(4) {
      animation-delay: 0.4s;
    }

    .form-group:nth-child(5) {
      animation-delay: 0.5s;
    }

    .form-group:nth-child(6) {
      animation-delay: 0.6s;
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

    .contact-info {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
      animation: fadeInRight 0.8s ease-out backwards;
    }

    .contact-info:nth-child(1) {
      animation-delay: 0.2s;
    }

    .contact-info:nth-child(2) {
      animation-delay: 0.3s;
    }

    .contact-info:nth-child(3) {
      animation-delay: 0.4s;
    }

    .contact-info:nth-child(4) {
      animation-delay: 0.5s;
    }

    @keyframes fadeInRight {
      from {
        opacity: 0;
        transform: translateX(30px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .contact-info:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .contact-info i {
      color: var(--primary-color);
      margin-right: 10px;
      font-size: 1.2rem;
    }

    .social-icons {
      animation: fadeIn 1s ease-in 0.8s backwards;
    }

    .social-icons a {
      display: inline-block;
      width: 45px;
      height: 45px;
      line-height: 45px;
      text-align: center;
      background: var(--primary-color);
      color: white;
      border-radius: 50%;
      margin-right: 10px;
      transition: all 0.3s ease;
    }

    .social-icons a:hover {
      background: var(--secondary-color);
      transform: translateY(-5px) rotate(360deg);
    }

    .btn-submit {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      border: none;
      padding: 15px 40px;
      border-radius: 10px;
      font-weight: bold;
      color: white;
      transition: all 0.3s ease;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
      }

      50% {
        box-shadow: 0 0 0 15px rgba(40, 167, 69, 0);
      }
    }

    .btn-submit:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4);
      color: white;
      animation: none;
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

    .section-title {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 30px;
      color: var(--dark-color);
      position: relative;
      padding-bottom: 15px;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 4px;
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      border-radius: 2px;
    }

    .alert {
      animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>
<?php include "./includes/navbar.php" ?>
  <!-- Header Section -->
  <section class="contact-header">
  </section>

  <!-- Contact Section -->
  <section class="py-5">
    <div class="container">
      <div class="contact-section">
        <div class="row g-5">

          <!-- Contact Form -->
          <div class="col-lg-7">
            <h3 class="section-title">Send Us a Message</h3>

            <form method="POST" action="">

              <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <div class="row mb-3 form-group">
                <div class="col-md-6 mb-3 mb-md-0">
                  <label class="form-label">Full Name <span class="text-danger">*</span></label>
                  <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email Address <span class="text-danger">*</span></label>
                  <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required>
                </div>
              </div>

              <div class="mb-3 form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="+91 XXXXX XXXXX" pattern="[0-9+\s-]*">
              </div>

              <div class="mb-3 form-group">
                <label class="form-label">Subject <span class="text-danger">*</span></label>
                <select name="subject" class="form-select" required>
                  <option selected disabled value="">-- Select Subject --</option>
                  <option>General Inquiry</option>
                  <option>Volunteer Opportunity</option>
                  <option>Donation Information</option>
                  <option>Sponsorship</option>
                  <option>Partnership</option>
                  <option>Other</option>
                </select>
              </div>

              <div class="mb-3 form-group">
                <label class="form-label">Message <span class="text-danger">*</span></label>
                <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
              </div>

              <div class="form-check mb-4 form-group">
                <input class="form-check-input" type="checkbox" name="volunteer" id="volunteerCheck">
                <label class="form-check-label" for="volunteerCheck">
                  I am interested in volunteering with Safe & Home Foundation
                </label>
              </div>

              <div class="form-group">
                <button type="submit" name="submit" class="btn btn-submit">
                  <i class="fas fa-paper-plane"></i> Send Message
                </button>
              </div>
            </form>
          </div>

          <!-- Contact Details -->
          <div class="col-lg-5">
            <h3 class="section-title">Get in Touch</h3>

            <div class="contact-info">
              <h5><i class="fas fa-map-marker-alt"></i> Our Office</h5>
              <p class="mb-0">
                #12, Hope Street,<br>
                Bengaluru, Karnataka – 560001, India
              </p>
            </div>

            <div class="contact-info">
              <h5><i class="fas fa-phone"></i> Call Us</h5>
              <p class="mb-0">
                <strong>Helpline:</strong> +91 98765 43210<br>
                <strong>Office:</strong> +91 91234 56789
              </p>
            </div>

            <div class="contact-info">
              <h5><i class="fas fa-envelope"></i> Email Us</h5>
              <p class="mb-0">
                General: info@safehome.org<br>
                Donations: donate@safehome.org<br>
                Volunteer: volunteer@safehome.org
              </p>
            </div>

            <div class="contact-info">
              <h5><i class="fas fa-clock"></i> Working Hours</h5>
              <p class="mb-0">
                Monday – Saturday: 9:00 AM – 5:00 PM<br>
                Sunday: Closed
              </p>
            </div>

            <div class="mt-4">
              <h5>Follow Us</h5>
              <div class="social-icons">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <div class="text-center" style="
        background:#e8f5e9;
          border-radius: 25px;
          padding: 50px 40px;
          box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        ">
          <h2 class="fw-bold mb-3">Every message brings hope to someone in need</h2>
          <p class="lead mb-4">We would love to hear from you. If you have any questions, suggestions, or would like to support our cause, please feel free to contact us</p>
          <a href="donate.php" class="btn btn-success btn-lg">
            <i class="bi bi-heart-fill"></i> Donate Now
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

  <!-- Google Map -->
<section>
  <iframe
    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3888.0!2d74.9729059!3d13.0613613!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba4ab25da9e6b5b%3A0x19b2bd53448f91f9!2sAlva's+Degree+College+(UG)!5e0!3m2!1sen!2sin!4v1714900000000!5m2!1sen!2sin"
    width="100%"
    height="400"
    style="border:0;"
    allowfullscreen=""
    loading="lazy"
    referrerpolicy="no-referrer-when-downgrade">
  </iframe>
</section>

  <!-- Footer Note -->
<?php include "./includes/footer.php" ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
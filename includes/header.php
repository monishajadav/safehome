<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="./images/logo.png" alt="Safe & Home Foundation" />
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="aboutus.php" class="nav-link">About</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Programs</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="elder.php">Elder Care</a></li>
            <li><a class="dropdown-item" href="children.php">Child Welfare</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="volunteer.php">Volunteer</a></li>
            <li><a class="dropdown-item" href="internship.php">Internships</a></li>
          </ul>
        </li>
        <li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>
        <li class="nav-item"><a href="feedback.php" class="nav-link">Feedback</a></li>
        <li class="nav-item"><a href="wellwisher.php" class="nav-link active">Well Wisher</a></li>
        <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
        <li class="nav-item ms-lg-3">
          <a href="donate.php" class="btn btn-warning btn-sm px-3">Donate</a>
        </li>
        <?php if($is_logged_in): ?>
          <li class="nav-item dropdown ms-lg-2">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?php echo htmlspecialchars($username); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item ms-lg-2">
            <a href="register.php" class="btn btn-sm btn-outline-light px-3">Register</a>
          </li>
          <li class="nav-item ms-2">
            <a href="login.php" class="btn btn-outline-light px-4">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
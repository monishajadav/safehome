<?php
session_start();
include('includes/db_connect.php');

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

// Add profile_picture column if it doesn't exist
mysqli_query($conn, "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL");

// Fetch user data
$user_query  = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user        = mysqli_fetch_assoc($user_result);

// ── Profile picture upload ────────────────────────────────────────────────────
if(isset($_POST['upload_picture'])) {
    if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed  = ['jpg', 'jpeg', 'png', 'gif'];
        $filetype = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        if(in_array(strtolower($filetype), $allowed)) {
            $new_filename = 'user_' . $user_id . '_' . time() . '.' . $filetype;
            $upload_path  = 'uploads/profiles/' . $new_filename;
            if(!file_exists('uploads/profiles')) mkdir('uploads/profiles', 0777, true);
            if(move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                if($user['profile_picture'] && file_exists($user['profile_picture'])) unlink($user['profile_picture']);
                mysqli_query($conn, "UPDATE users SET profile_picture='$upload_path' WHERE id='$user_id'");
                $success = "Profile picture updated successfully!";
                $user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));
            } else { $error = "Error uploading file!"; }
        } else { $error = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed!"; }
    }
}

// ── Profile update ────────────────────────────────────────────────────────────
if(isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $check    = mysqli_query($conn, "SELECT * FROM users WHERE (username='$username' OR email='$email') AND id!='$user_id'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Username or email already exists!";
    } else {
        if(mysqli_query($conn, "UPDATE users SET username='$username', email='$email', phone='$phone' WHERE id='$user_id'")) {
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
            $user = mysqli_fetch_assoc(mysqli_query($conn, $user_query));
        } else { $error = "Error: " . mysqli_error($conn); }
    }
}

// ── Password change ───────────────────────────────────────────────────────────
if(isset($_POST['change_password'])) {
    $cur  = $_POST['current_password'];
    $new  = $_POST['new_password'];
    $conf = $_POST['confirm_password'];
    if(!password_verify($cur, $user['password']))  { $error = "Current password is incorrect!"; }
    elseif(strlen($new) < 6)                        { $error = "New password must be at least 6 characters!"; }
    elseif($new !== $conf)                          { $error = "New passwords do not match!"; }
    else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        if(mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id='$user_id'"))
            $success = "Password changed successfully!";
        else $error = "Error: " . mysqli_error($conn);
    }
}

// ── Stats ─────────────────────────────────────────────────────────────────────
$donations_result = @mysqli_query($conn, "SELECT COUNT(*) as total_donations, COALESCE(SUM(amount),0) as total_amount FROM donations WHERE user_id='$user_id'");
$donations_stats  = $donations_result ? mysqli_fetch_assoc($donations_result) : ['total_donations'=>0,'total_amount'=>0];

$volunteer_result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM volunteer_applications WHERE email='{$user['email']}'");
$volunteer_count  = $volunteer_result ? mysqli_fetch_assoc($volunteer_result)['total'] : 0;

$messages_result  = @mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages WHERE email='{$user['email']}'");
$messages_count   = $messages_result ? mysqli_fetch_assoc($messages_result)['total'] : 0;

$internship_result = @mysqli_query($conn, "SELECT COUNT(*) as total FROM internship_applications WHERE email='{$user['email']}'");
$internship_count  = $internship_result ? mysqli_fetch_assoc($internship_result)['total'] : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Profile | Safe & Home Foundation</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body {
      background: linear-gradient(-45deg, #ffeaa7, #fab1a0, #74b9ff, #a29bfe);
      background-size: 400% 400%;
      animation: gradientBG 15s ease infinite;
      overflow-x: hidden; margin: 0; padding: 0;
    }
    @keyframes gradientBG {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    .navbar { padding:.75rem 0; box-shadow:0 2px 8px rgba(0,0,0,0.1); background:rgba(25,135,84,0.95)!important; backdrop-filter:blur(10px); }
    .navbar-brand img { height:80px; }
    .nav-link { font-size:.95rem; font-weight:500; padding:.5rem 1rem!important; transition:color .2s; }
    .nav-link:hover { color:#ffc107!important; }
    .dropdown-menu { border:none; box-shadow:0 4px 12px rgba(0,0,0,0.15); border-radius:8px; margin-top:.5rem; }
    .dropdown-item { padding:.6rem 1.2rem; transition:background .2s; }
    .dropdown-item:hover { background:#f8f9fa; color:#198754; }
    .btn-warning { background:#ffc107; border:none; font-weight:600; transition:all .2s; }
    .btn-warning:hover { background:#ffb300; transform:translateY(-1px); }

    .profile-section { padding:80px 0 40px; }
    .profile-card { background:rgba(255,255,255,0.95); backdrop-filter:blur(10px); border-radius:20px; padding:40px; box-shadow:0 15px 50px rgba(0,0,0,0.2); margin-bottom:30px; }
    .profile-header { text-align:center; margin-bottom:40px; padding-bottom:30px; border-bottom:2px solid #e9ecef; }
    .profile-avatar { width:150px; height:150px; border-radius:50%; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:4rem; color:white; box-shadow:0 10px 30px rgba(0,0,0,0.2); position:relative; overflow:hidden; }
    .profile-avatar img { width:100%; height:100%; object-fit:cover; }
    .upload-btn { position:absolute; bottom:10px; right:10px; width:40px; height:40px; border-radius:50%; background:#198754; border:3px solid white; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .3s ease; }
    .upload-btn:hover { background:#20c997; transform:scale(1.1); }
    .upload-btn i { color:white; font-size:1.2rem; }
    .profile-name { font-size:2rem; font-weight:700; color:#198754; margin-bottom:5px; }
    .profile-email { color:#666; font-size:1.1rem; }

    .stats-row { margin-bottom:30px; }
    .stat-card { background:white; border-radius:15px; padding:25px; text-align:center; box-shadow:0 5px 15px rgba(0,0,0,0.1); transition:all .3s ease; height:100%; }
    .stat-card:hover { transform:translateY(-5px); box-shadow:0 10px 25px rgba(0,0,0,0.15); }
    .stat-card i { font-size:2.5rem; margin-bottom:15px; }
    .stat-card.donations  { border-top:4px solid #667eea; } .stat-card.donations i  { color:#667eea; }
    .stat-card.volunteers { border-top:4px solid #f093fb; } .stat-card.volunteers i { color:#f093fb; }
    .stat-card.messages   { border-top:4px solid #20c997; } .stat-card.messages i   { color:#20c997; }
    .stat-card.internship { border-top:4px solid #fd7e14; } .stat-card.internship i { color:#fd7e14; }
    .stat-card h3 { font-size:2rem; font-weight:800; color:#333; margin-bottom:5px; }
    .stat-card p  { color:#666; margin-bottom:0; }

    .section-title { font-size:1.5rem; font-weight:700; color:#198754; margin-bottom:20px; padding-bottom:10px; border-bottom:2px solid #e9ecef; }
    .form-control, .form-select { border-radius:10px; padding:12px; border:2px solid #e9ecef; transition:all .3s ease; }
    .form-control:focus, .form-select:focus { border-color:#198754; box-shadow:0 0 0 .2rem rgba(25,135,84,0.25); }
    .btn-success { background:linear-gradient(135deg,#198754 0%,#20c997 100%); border:none; font-weight:700; padding:12px 40px; border-radius:50px; transition:all .3s ease; box-shadow:0 5px 15px rgba(25,135,84,0.3); }
    .btn-success:hover { transform:translateY(-3px); box-shadow:0 10px 25px rgba(25,135,84,0.4); }
    .info-box { background:#f8f9fa; border-left:4px solid #198754; padding:15px; border-radius:8px; margin-bottom:20px; }

    .nav-tabs { border-bottom:2px solid #e9ecef; margin-bottom:30px; }
    .nav-tabs .nav-link { border:none; color:#666; font-weight:600; padding:15px 25px; transition:all .3s ease; }
    .nav-tabs .nav-link:hover { color:#198754; background:transparent; }
    .nav-tabs .nav-link.active { color:#198754; background:transparent; border-bottom:3px solid #198754; }

    /* ── Status badges ── */
    .badge-status { padding:5px 14px; border-radius:20px; font-size:0.82rem; font-weight:600; display:inline-flex; align-items:center; gap:5px; }
    .badge-pending  { background:#fef9c3; color:#854d0e; }
    .badge-approved { background:#d1fae5; color:#065f46; }
    .badge-rejected { background:#fee2e2; color:#991b1b; }
    .badge-completed{ background:#dbeafe; color:#1e40af; }

    /* ── Status info box ── */
    .status-notice { border-radius:12px; padding:18px 20px; margin-bottom:16px; display:flex; align-items:flex-start; gap:14px; }
    .status-notice.pending  { background:#fef9c3; border-left:4px solid #f59e0b; }
    .status-notice.approved { background:#d1fae5; border-left:4px solid #10b981; }
    .status-notice.rejected { background:#fee2e2; border-left:4px solid #ef4444; }
    .status-notice i { font-size:1.5rem; margin-top:2px; flex-shrink:0; }
    .status-notice.pending i  { color:#d97706; }
    .status-notice.approved i { color:#059669; }
    .status-notice.rejected i { color:#dc2626; }

    footer { background:rgba(25,135,84,0.95)!important; backdrop-filter:blur(10px); box-shadow:0 -4px 20px rgba(0,0,0,0.1); }
    footer a { transition:all .3s ease; text-decoration:none; }
    footer a:hover { color:#ffd700!important; }

    @media(max-width:991px) {
      .navbar-brand img { height:60px; }
      .profile-card { padding:30px 20px; }
      .profile-name { font-size:1.5rem; }
      .profile-avatar { width:120px; height:120px; font-size:3rem; }
      .nav-tabs .nav-link { padding:10px 14px; font-size:.85rem; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
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
          <li class="nav-item"><a href="aboutus.php" class="nav-link">About-Us</a></li>
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
          <li class="nav-item"><a href="guidelines.php" class="nav-link">Guidelines</a></li>
          <li class="nav-item"><a href="contact.php" class="nav-link">Contact</a></li>
          <li class="nav-item ms-lg-3">
            <a href="donate.php" class="btn btn-warning btn-sm px-3">Donate</a>
          </li>
          <li class="nav-item dropdown ms-lg-2">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              <?php echo htmlspecialchars($user['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item active" href="profile.php">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- PROFILE SECTION -->
  <section class="profile-section">
    <div class="container">

      <!-- PROFILE HEADER -->
      <div class="profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            <?php if($user['profile_picture'] && file_exists($user['profile_picture'])): ?>
              <img src="<?php echo $user['profile_picture']; ?>" alt="Profile Picture">
            <?php else: ?>
              <i class="bi bi-person-circle"></i>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" id="uploadForm" style="display:none;">
              <input type="file" name="profile_picture" id="profilePictureInput" accept="image/*" onchange="document.getElementById('uploadForm').submit();">
              <input type="hidden" name="upload_picture" value="1">
            </form>
            <label for="profilePictureInput" class="upload-btn" title="Upload Profile Picture">
              <i class="bi bi-camera-fill"></i>
            </label>
          </div>
          <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>
          <p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
          <span class="badge bg-success"><i class="bi bi-person-check-fill"></i> <?php echo ucfirst($user['user_type']); ?></span>
        </div>

        <?php if($success): ?>
          <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if($error): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <div class="info-box">
          <h6 class="fw-bold mb-2"><i class="bi bi-info-circle"></i> Account Information</h6>
          <p class="mb-1"><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
          <p class="mb-0"><strong>User ID:</strong> #<?php echo $user['id']; ?></p>
        </div>
      </div>

      <!-- STATS -->
      <div class="row stats-row g-4">
        <div class="col-md-3">
          <div class="stat-card donations">
            <i class="bi bi-cash-coin"></i>
            <h3>₹<?php echo number_format($donations_stats['total_amount']); ?></h3>
            <p>Total Donated</p>
            <small class="text-muted"><?php echo $donations_stats['total_donations']; ?> donations</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card volunteers">
            <i class="bi bi-people-fill"></i>
            <h3><?php echo $volunteer_count; ?></h3>
            <p>Volunteer Applications</p>
            <small class="text-muted">Total submitted</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card internship">
            <i class="bi bi-briefcase-fill"></i>
            <h3><?php echo $internship_count; ?></h3>
            <p>Internship Applications</p>
            <small class="text-muted">Total submitted</small>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-card messages">
            <i class="bi bi-envelope-fill"></i>
            <h3><?php echo $messages_count; ?></h3>
            <p>Messages Sent</p>
            <small class="text-muted">Via contact form</small>
          </div>
        </div>
      </div>

      <!-- TABS -->
      <div class="profile-card">
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
          <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile" type="button"><i class="bi bi-person"></i> Profile</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#donations" type="button"><i class="bi bi-cash-stack"></i> Donations</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#volunteer" type="button"><i class="bi bi-hand-thumbs-up"></i> Volunteer</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#internship" type="button"><i class="bi bi-briefcase"></i> Internship</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#messages" type="button"><i class="bi bi-chat-dots"></i> Messages</button></li>
          <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#security" type="button"><i class="bi bi-shield-lock"></i> Security</button></li>
        </ul>

        <div class="tab-content mt-4">

          <!-- PROFILE TAB -->
          <div class="tab-pane fade show active" id="profile" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-person-fill"></i> Update Profile</h3>
            <form method="POST">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Username <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
              </div>
              <div class="mb-4">
                <label class="form-label">Phone Number</label>
                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
              </div>
              <button type="submit" name="update_profile" class="btn btn-success"><i class="bi bi-save"></i> Update Profile</button>
            </form>
          </div>

          <!-- DONATIONS TAB -->
          <div class="tab-pane fade" id="donations" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-cash-stack"></i> Donation History</h3>
            <?php
            $dlist = @mysqli_query($conn, "SELECT * FROM donations WHERE user_id='$user_id' ORDER BY id DESC");
            if($dlist && mysqli_num_rows($dlist) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr><th>Date</th><th>Amount</th><th>Purpose</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php while($d = mysqli_fetch_assoc($dlist)): ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($d['donated_at'] ?? $d['created_at'] ?? '')); ?></td>
                      <td><strong>₹<?php echo number_format($d['amount']); ?></strong></td>
                      <td><?php echo htmlspecialchars($d['donation_type'] ?? $d['purpose'] ?? ''); ?></td>
                      <td><span class="badge-status badge-completed"><i class="bi bi-check-circle-fill"></i> Completed</span></td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
              <div class="text-center mt-4">
                <p class="fs-5"><strong>Total Donated:</strong> ₹<?php echo number_format($donations_stats['total_amount']); ?></p>
                <a href="donate.php" class="btn btn-success"><i class="bi bi-heart-fill"></i> Donate Again</a>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-cash-coin text-muted" style="font-size:4rem;"></i>
                <p class="fs-5 mt-3">You haven't made any donations yet.</p>
                <a href="donate.php" class="btn btn-success mt-3"><i class="bi bi-heart-fill"></i> Make Your First Donation</a>
              </div>
            <?php endif; ?>
          </div>

          <!-- ═══════════════════════════════════════════
               VOLUNTEER TAB — shows REAL status from DB
          ════════════════════════════════════════════ -->
          <div class="tab-pane fade" id="volunteer" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-hand-thumbs-up"></i> Volunteer Applications</h3>
            <?php
            $vlist = mysqli_query($conn, "SELECT * FROM volunteer_applications WHERE email='{$user['email']}' ORDER BY id DESC");
            if(mysqli_num_rows($vlist) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr><th>Date Applied</th><th>Care Area</th><th>Message</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php while($vol = mysqli_fetch_assoc($vlist)):
                        $vstatus = $vol['status'] ?? 'pending';
                        // Status notice messages
                        $notices = [
                            'pending'  => ['class'=>'pending',  'icon'=>'bi-clock-fill',        'title'=>'Under Review',  'msg'=>'Your volunteer application is currently being reviewed by our team. We will update you soon!'],
                            'approved' => ['class'=>'approved', 'icon'=>'bi-check-circle-fill',  'title'=>'Approved! 🎉', 'msg'=>'Congratulations! Your volunteer application has been approved. Our team will contact you shortly with next steps.'],
                            'rejected' => ['class'=>'rejected', 'icon'=>'bi-x-circle-fill',      'title'=>'Not Selected', 'msg'=>'Unfortunately your application was not selected this time. You are welcome to apply again in the future.'],
                        ];
                        $n = $notices[$vstatus] ?? $notices['pending'];
                    ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($vol['created_at'])); ?></td>
                      <td><strong><?php echo htmlspecialchars($vol['care_area']); ?></strong></td>
                      <td><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($vol['message'] ?? '', 0, 50, '...')); ?></small></td>
                      <td>
                        <?php if($vstatus === 'approved'): ?>
                          <span class="badge-status badge-approved"><i class="bi bi-check-circle-fill"></i> Approved</span>
                        <?php elseif($vstatus === 'rejected'): ?>
                          <span class="badge-status badge-rejected"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                        <?php else: ?>
                          <span class="badge-status badge-pending"><i class="bi bi-clock-fill"></i> Pending</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <!-- Status notice below each row -->
                    <tr>
                      <td colspan="4" style="padding:0 0 10px 0; border:none;">
                        <div class="status-notice <?php echo $n['class']; ?>">
                          <i class="bi <?php echo $n['icon']; ?>"></i>
                          <div>
                            <strong><?php echo $n['title']; ?></strong><br>
                            <small><?php echo $n['msg']; ?></small>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size:4rem;"></i>
                <p class="fs-5 mt-3">You haven't applied as a volunteer yet.</p>
                <a href="volunteer.php" class="btn btn-success mt-3"><i class="bi bi-hand-thumbs-up-fill"></i> Apply Now</a>
              </div>
            <?php endif; ?>
          </div>

          <!-- ═══════════════════════════════════════════
               INTERNSHIP TAB — NEW, shows real status
          ════════════════════════════════════════════ -->
          <div class="tab-pane fade" id="internship" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-briefcase-fill"></i> Internship Applications</h3>
            <?php
            $ilist = @mysqli_query($conn, "SELECT * FROM internship_applications WHERE email='{$user['email']}' ORDER BY applied_at DESC");
            if($ilist && mysqli_num_rows($ilist) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr><th>Applied On</th><th>College</th><th>Course / Year</th><th>Area</th><th>Duration</th><th>Status</th></tr>
                  </thead>
                  <tbody>
                    <?php while($intern = mysqli_fetch_assoc($ilist)):
                        $istatus = $intern['status'] ?? 'pending';
                        $inotices = [
                            'pending'  => ['class'=>'pending',  'icon'=>'bi-clock-fill',       'title'=>'Application Under Review', 'msg'=>'Your internship application is being reviewed. We will get back to you soon!'],
                            'approved' => ['class'=>'approved', 'icon'=>'bi-check-circle-fill', 'title'=>'Internship Approved! 🎉', 'msg'=>'Congratulations! Your internship application has been approved. Our team will contact you with joining details.'],
                            'rejected' => ['class'=>'rejected', 'icon'=>'bi-x-circle-fill',     'title'=>'Not Selected This Time',  'msg'=>'Unfortunately your application was not selected. Please feel free to apply again in the next cycle.'],
                        ];
                        $in = $inotices[$istatus] ?? $inotices['pending'];
                    ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($intern['applied_at'])); ?></td>
                      <td><?php echo htmlspecialchars($intern['college']); ?></td>
                      <td><?php echo htmlspecialchars($intern['course']); ?> / Yr <?php echo htmlspecialchars($intern['year']); ?></td>
                      <td>
                        <span style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);color:#1a472a;padding:3px 11px;border-radius:20px;font-size:.76rem;font-weight:600;">
                          <?php echo htmlspecialchars($intern['area']); ?>
                        </span>
                      </td>
                      <td>
                        <span style="background:linear-gradient(135deg,#e3f2fd,#bbdefb);color:#1565c0;padding:3px 11px;border-radius:20px;font-size:.76rem;font-weight:600;">
                          <?php echo htmlspecialchars($intern['duration']); ?>
                        </span>
                      </td>
                      <td>
                        <?php if($istatus === 'approved'): ?>
                          <span class="badge-status badge-approved"><i class="bi bi-check-circle-fill"></i> Approved</span>
                        <?php elseif($istatus === 'rejected'): ?>
                          <span class="badge-status badge-rejected"><i class="bi bi-x-circle-fill"></i> Rejected</span>
                        <?php else: ?>
                          <span class="badge-status badge-pending"><i class="bi bi-clock-fill"></i> Pending</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <!-- Status notice -->
                    <tr>
                      <td colspan="6" style="padding:0 0 10px 0; border:none;">
                        <div class="status-notice <?php echo $in['class']; ?>">
                          <i class="bi <?php echo $in['icon']; ?>"></i>
                          <div>
                            <strong><?php echo $in['title']; ?></strong><br>
                            <small><?php echo $in['msg']; ?></small>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-briefcase text-muted" style="font-size:4rem;"></i>
                <p class="fs-5 mt-3">You haven't applied for an internship yet.</p>
                <a href="internship.php" class="btn btn-success mt-3"><i class="bi bi-briefcase-fill"></i> Apply Now</a>
              </div>
            <?php endif; ?>
          </div>

          <!-- MESSAGES TAB -->
          <div class="tab-pane fade" id="messages" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-chat-dots"></i> Contact Messages</h3>
            <?php
            $mlist = mysqli_query($conn, "SELECT * FROM contact_messages WHERE email='{$user['email']}' ORDER BY id DESC");
            if(mysqli_num_rows($mlist) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead class="table-light">
                    <tr><th>Date</th><th>Name</th><th>Message</th></tr>
                  </thead>
                  <tbody>
                    <?php while($msg = mysqli_fetch_assoc($mlist)): ?>
                    <tr>
                      <td><?php echo isset($msg['created_at']) ? date('M d, Y', strtotime($msg['created_at'])) : 'N/A'; ?></td>
                      <td><?php echo htmlspecialchars($msg['name']); ?></td>
                      <td><small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($msg['message'] ?? '', 0, 80, '...')); ?></small></td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-envelope text-muted" style="font-size:4rem;"></i>
                <p class="fs-5 mt-3">You haven't sent any messages yet.</p>
                <a href="contact.php" class="btn btn-success mt-3"><i class="bi bi-envelope-fill"></i> Contact Us</a>
              </div>
            <?php endif; ?>
          </div>

          <!-- SECURITY TAB -->
          <div class="tab-pane fade" id="security" role="tabpanel">
            <h3 class="section-title"><i class="bi bi-shield-lock"></i> Change Password</h3>
            <form method="POST">
              <div class="mb-3">
                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="current_password" required>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">New Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" name="new_password" minlength="6" required>
                  <small class="text-muted">At least 6 characters</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                  <input type="password" class="form-control" name="confirm_password" required>
                </div>
              </div>
              <button type="submit" name="change_password" class="btn btn-success"><i class="bi bi-key"></i> Change Password</button>
            </form>
          </div>

        </div>
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
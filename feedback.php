<?php
session_start();
include('includes/db_connect.php');

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username     = $is_logged_in ? $_SESSION['username'] : '';
$success = "";
$error   = "";

// ── FORM SUBMISSION ──────────────────────────────────────────────────────────
if(isset($_POST['submit'])) {
    $name            = mysqli_real_escape_string($conn, trim($_POST['name']));
    $submission_type = mysqli_real_escape_string($conn, trim($_POST['submission_type']));

    if(empty($name)) {
        $error = "Please enter your name.";
    } else {

        // ── TEXT ──
        if($submission_type == 'text') {
            $message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));
            if(empty($message) || strlen($message) < 10) {
                $error = "Please enter a message of at least 10 characters.";
            } else {
                $sql = "INSERT INTO feedback (name, submission_type, message)
                        VALUES ('$name', 'text', '$message')";
                if(mysqli_query($conn, $sql)) {
                    header("Location: feedback.php?success=1");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            }

        // ── AUDIO ──
        } elseif($submission_type == 'audio') {
            $file_saved = false; $upload_path = ''; $original_name = '';

            if(isset($_POST['recorded_audio']) && !empty($_POST['recorded_audio'])) {
                $blob        = preg_replace('#^data:audio/[^;]+;base64,#', '', $_POST['recorded_audio']);
                $blob        = str_replace(' ', '+', $blob);
                $data        = base64_decode($blob);
                $new_name    = time() . '_' . uniqid() . '.webm';
                $upload_path = 'uploads/feedback/audio/' . $new_name;
                if(!is_dir('uploads/feedback/audio/')) mkdir('uploads/feedback/audio/', 0755, true);
                file_put_contents($upload_path, $data);
                $original_name = 'recorded_audio_' . date('Y-m-d') . '.webm';
                $file_saved = true;

            } elseif(isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
                $file    = $_FILES['audio_file'];
                $allowed = ['mp3','wav','ogg','m4a','aac','webm'];
                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(!in_array($ext, $allowed)) {
                    $error = "Invalid audio format. Allowed: MP3, WAV, OGG, M4A, AAC";
                } elseif($file['size'] > 10 * 1024 * 1024) {
                    $error = "File too large. Maximum 10MB.";
                } else {
                    $new_name    = time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = 'uploads/feedback/audio/' . $new_name;
                    if(!is_dir('uploads/feedback/audio/')) mkdir('uploads/feedback/audio/', 0755, true);
                    move_uploaded_file($file['tmp_name'], $upload_path);
                    $original_name = $file['name'];
                    $file_saved = true;
                }
            } else {
                $error = "Please record or upload an audio file.";
            }

            if(empty($error) && $file_saved) {
                $original_name = mysqli_real_escape_string($conn, $original_name);
                $enjoyed_most = mysqli_real_escape_string($conn, trim($_POST['enjoyed_most'] ?? ''));

$sql = "INSERT INTO feedback (name, submission_type, file_path, file_name, time_spent, enjoyed_most)
        VALUES ('$name', 'image', '$upload_path', '$original_name', '$time_spent', '$enjoyed_most')";
                if(mysqli_query($conn, $sql)) { header("Location: feedback.php?success=1"); exit(); }
                else { $error = "Database error. Please try again."; }
            }

        // ── VIDEO ──
        } elseif($submission_type == 'video') {
            $file_saved = false; $upload_path = ''; $original_name = '';

            if(isset($_POST['recorded_video']) && !empty($_POST['recorded_video'])) {
                $blob        = preg_replace('#^data:video/[^;]+;base64,#', '', $_POST['recorded_video']);
                $blob        = str_replace(' ', '+', $blob);
                $data        = base64_decode($blob);
                $new_name    = time() . '_' . uniqid() . '.webm';
                $upload_path = 'uploads/feedback/video/' . $new_name;
                if(!is_dir('uploads/feedback/video/')) mkdir('uploads/feedback/video/', 0755, true);
                file_put_contents($upload_path, $data);
                $original_name = 'recorded_video_' . date('Y-m-d') . '.webm';
                $file_saved = true;

            } elseif(isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
                $file    = $_FILES['video_file'];
                $allowed = ['mp4','avi','mov','mkv','webm'];
                $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if(!in_array($ext, $allowed)) {
                    $error = "Invalid video format. Allowed: MP4, AVI, MOV, MKV, WEBM";
                } elseif($file['size'] > 50 * 1024 * 1024) {
                    $error = "File too large. Maximum 50MB.";
                } else {
                    $new_name    = time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = 'uploads/feedback/video/' . $new_name;
                    if(!is_dir('uploads/feedback/video/')) mkdir('uploads/feedback/video/', 0755, true);
                    move_uploaded_file($file['tmp_name'], $upload_path);
                    $original_name = $file['name'];
                    $file_saved = true;
                }
            } else {
                $error = "Please record or upload a video file.";
            }

            if(empty($error) && $file_saved) {
                $original_name = mysqli_real_escape_string($conn, $original_name);
                $sql = "INSERT INTO feedback (name, submission_type, file_path, file_name)
                        VALUES ('$name', 'video', '$upload_path', '$original_name')";
                if(mysqli_query($conn, $sql)) { header("Location: feedback.php?success=1"); exit(); }
                else { $error = "Database error. Please try again."; }
            }

        // ── IMAGE ──
        } elseif($submission_type == 'image') {
            $time_spent = mysqli_real_escape_string($conn, trim($_POST['time_spent'] ?? ''));

            if(!isset($_FILES['feedback_image']) || $_FILES['feedback_image']['error'] != 0) {
                $error = "Please select an image to upload.";
            } elseif(empty($time_spent)) {
                $error = "Please enter how long you visited the NGO.";
            } else {
                $file         = $_FILES['feedback_image'];
                $allowed_ext  = ['jpg','jpeg','png','gif','webp'];
                $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
                $ext          = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if(!in_array($ext, $allowed_ext) || !in_array($mime, $allowed_mime)) {
                    $error = "Invalid image format. Allowed: JPG, PNG, GIF, WEBP";
                } elseif($file['size'] > 5 * 1024 * 1024) {
                    $error = "Image too large. Maximum 5MB.";
                } else {
                    $upload_dir = 'uploads/feedback/images/';
                    if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $new_name    = time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = $upload_dir . $new_name;

                    if(move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $original_name = mysqli_real_escape_string($conn, $file['name']);
                        $sql = "INSERT INTO feedback (name, submission_type, file_path, file_name, time_spent)
                                VALUES ('$name', 'image', '$upload_path', '$original_name', '$time_spent')";
                        if(mysqli_query($conn, $sql)) { header("Location: feedback.php?success=1"); exit(); }
                        else { $error = "Database error. Please try again."; }
                    } else {
                        $error = "Upload failed. Please check folder permissions.";
                    }
                }
            }
        }
    }
}

if(isset($_GET['success'])) $success = "Thank you! Your feedback has been shared successfully.";

// ── FETCH FEEDBACK ───────────────────────────────────────────────────────────
$type_filter = isset($_GET['type']) ? mysqli_real_escape_string($conn, $_GET['type']) : '';
if(!empty($type_filter)) {
    $result = mysqli_query($conn, "SELECT * FROM feedback WHERE submission_type='$type_filter' ORDER BY submitted_at DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY submitted_at DESC");
}
$total = mysqli_num_rows($result);

$total_all   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback"))['c'];
$total_text  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='text'"))['c'];
$total_audio = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='audio'"))['c'];
$total_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='video'"))['c'];
$total_image = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='image'"))['c'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feedback | Safe & Home Foundation</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    * { font-family: 'Poppins', sans-serif; }
    html { scroll-behavior: smooth; }
    body { overflow-x: hidden; }

    .hero {
      background: url("./images/feedback.jpg") center/cover no-repeat;
      min-height: 80vh; display:flex; align-items:center;
      justify-content:center; text-align:center; position:relative; color:white;
    }
    .hero::after { content:""; position:absolute; inset:0; background:rgba(0,0,0,0.55); }
    .hero .container { position:relative; z-index:2; }
    .hero h1 { font-size:3rem; font-weight:900; }

    .section-green { background:linear-gradient(135deg,#e8f5e9,#c8e6c9); padding:70px 0; }
    .section-white { background:#fff; padding:70px 0; }

    .section-title {
      font-size:2.2rem; font-weight:800; color:#198754;
      position:relative; display:inline-block; margin-bottom:2.5rem;
    }
    .section-title::after {
      content:''; position:absolute; bottom:-10px; left:50%;
      transform:translateX(-50%); width:70px; height:4px;
      background:linear-gradient(90deg,#198754,#20c997); border-radius:2px;
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

    /* STATS */
    .stats-bar { background:white; border-radius:20px; padding:25px; box-shadow:0 8px 25px rgba(0,0,0,0.08); margin-bottom:35px; }
    .stat-item { text-align:center; padding:0 15px; border-right:2px solid #e8f5e9; }
    .stat-item:last-child { border-right:none; }
    .stat-item i  { font-size:1.8rem; color:#198754; display:block; margin-bottom:6px; }
    .stat-item h3 { font-size:1.8rem; font-weight:800; color:#198754; margin:0; }
    .stat-item p  { font-size:0.85rem; color:#666; margin:0; }

    /* FILTER TABS */
    .filter-tabs { display:flex; gap:10px; justify-content:center; margin-bottom:35px; flex-wrap:wrap; }
    .filter-tab {
      background:white; border:2px solid #e0e0e0; border-radius:50px;
      padding:7px 22px; font-weight:600; color:#555;
      transition:all 0.3s ease; text-decoration:none; font-size:0.88rem;
    }
    .filter-tab:hover, .filter-tab.active {
      background:linear-gradient(135deg,#198754,#20c997); border-color:#198754; color:white;
    }

    /* CARDS */
    .feedback-card {
      background:white; border-radius:20px; overflow:hidden;
      box-shadow:0 8px 25px rgba(0,0,0,0.08);
      transition:all 0.3s ease; height:100%;
    }
    .feedback-card:hover { transform:translateY(-8px); box-shadow:0 20px 45px rgba(25,135,84,0.18); }
    .feedback-card.type-text  { border-left:5px solid #198754; }
    .feedback-card.type-audio { border-left:5px solid #0d6efd; }
    .feedback-card.type-video { border-left:5px solid #dc3545; }
    .feedback-card.type-image { border-left:5px solid #fd7e14; padding:0; }

    .card-body-inner { padding:25px; }
    .card-message { color:#555; font-size:0.95rem; line-height:1.8; font-style:italic; margin-bottom:15px; }
    .quote-icon { font-size:2.5rem; color:#e8f5e9; float:right; line-height:1; }

    .card-img-wrap { position:relative; overflow:hidden; cursor:pointer; }
    .card-img-wrap img { width:100%; height:210px; object-fit:cover; transition:transform 0.4s; display:block; }
    .feedback-card:hover .card-img-wrap img { transform:scale(1.05); }
    .img-overlay {
      position:absolute; inset:0; background:rgba(0,0,0,0);
      display:flex; align-items:center; justify-content:center; transition:background 0.3s;
    }
    .feedback-card:hover .img-overlay { background:rgba(0,0,0,0.3); }
    .img-overlay i { color:white; font-size:2rem; opacity:0; transition:opacity 0.3s; }
    .feedback-card:hover .img-overlay i { opacity:1; }
    .image-card-body { padding:20px 25px 25px; }

    .time-badge {
      display:inline-flex; align-items:center; gap:6px;
      background:#fff3cd; color:#b35900; border-radius:20px;
      padding:5px 14px; font-size:0.8rem; font-weight:700; margin-bottom:12px;
    }

    .type-badge {
      position:absolute; top:12px; right:12px;
      padding:4px 12px; border-radius:20px; font-size:0.72rem; font-weight:700; z-index:2;
    }
    .badge-text  { background:#e8f5e9; color:#198754; }
    .badge-audio { background:#cfe2ff; color:#0d6efd; }
    .badge-video { background:#f8d7da; color:#dc3545; }
    .badge-image { background:#fff3cd; color:#b35900; }

    .author-row { display:flex; align-items:center; gap:12px; padding-top:15px; border-top:2px solid #f0f0f0; margin-top:15px; }
    .avatar {
      width:44px; height:44px; border-radius:50%;
      background:linear-gradient(135deg,#198754,#20c997);
      display:flex; align-items:center; justify-content:center;
      color:white; font-weight:700; font-size:1.1rem; flex-shrink:0;
    }
    .avatar.av-audio { background:linear-gradient(135deg,#0d6efd,#6ea8fe); }
    .avatar.av-video { background:linear-gradient(135deg,#dc3545,#f77f8a); }
    .avatar.av-image { background:linear-gradient(135deg,#fd7e14,#ffc107); }
    .author-name { font-weight:700; color:#333; margin:0; font-size:0.92rem; }
    .author-date { color:#aaa; font-size:0.76rem; }

    audio, video { width:100%; border-radius:10px; margin-top:12px; }

    /* LIGHTBOX */
    .lightbox { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center; }
    .lightbox.active { display:flex; }
    .lightbox img { max-width:90vw; max-height:85vh; border-radius:12px; object-fit:contain; }
    .lightbox-close { position:fixed; top:18px; right:24px; color:white; font-size:2.5rem; cursor:pointer; z-index:10000; }

    /* FORM */
    .type-selector { display:flex; gap:12px; justify-content:center; margin-bottom:28px; flex-wrap:wrap; }
    .type-btn {
      background:white; border:3px solid #e0e0e0; border-radius:14px;
      padding:18px 22px; text-align:center; cursor:pointer;
      transition:all 0.3s ease; min-width:110px; font-weight:600; color:#555;
    }
    .type-btn:hover { border-color:#198754; color:#198754; transform:translateY(-4px); }
    .type-btn.active {
      border-color:#198754; background:linear-gradient(135deg,#198754,#20c997);
      color:white; transform:translateY(-4px); box-shadow:0 8px 20px rgba(25,135,84,0.3);
    }
    .type-btn i { font-size:1.8rem; display:block; margin-bottom:6px; }

    .form-card { background:white; border-radius:25px; padding:45px; box-shadow:0 15px 50px rgba(0,0,0,0.1); }
    .form-control { border-radius:10px; border:2px solid #e0e0e0; padding:12px 15px; transition:all 0.3s; }
    .form-control:focus { border-color:#198754; box-shadow:0 0 0 0.2rem rgba(25,135,84,0.15); }
    .form-label { font-weight:600; color:#444; margin-bottom:8px; }
    .input-group-text { background:linear-gradient(135deg,#198754,#20c997); color:white; border:none; border-radius:10px 0 0 10px; }

    .media-tabs { display:flex; gap:8px; margin-bottom:16px; }
    .media-tab { background:#f8f9fa; border:2px solid #e0e0e0; border-radius:10px; padding:7px 18px; font-weight:600; color:#555; cursor:pointer; transition:all 0.3s; font-size:0.88rem; }
    .media-tab.active { background:linear-gradient(135deg,#198754,#20c997); border-color:#198754; color:white; }

    .recorder-box { background:#f8fff8; border:2px dashed #c8e6c9; border-radius:14px; padding:28px; text-align:center; }
    .record-btn {
      width:72px; height:72px; border-radius:50%;
      background:linear-gradient(135deg,#198754,#20c997);
      border:none; color:white; font-size:1.8rem; cursor:pointer;
      transition:all 0.3s; box-shadow:0 5px 18px rgba(25,135,84,0.4);
      display:flex; align-items:center; justify-content:center; margin:0 auto;
    }
    .record-btn.recording { background:linear-gradient(135deg,#dc3545,#ff6b6b); animation:pulse-red 1.5s infinite; }
    @keyframes pulse-red {
      0%   { box-shadow:0 0 0 0 rgba(220,53,69,0.4); }
      70%  { box-shadow:0 0 0 14px rgba(220,53,69,0); }
      100% { box-shadow:0 0 0 0 rgba(220,53,69,0); }
    }
    .timer { font-size:1.4rem; font-weight:700; color:#198754; margin:10px 0; }
    .timer.recording { color:#dc3545; }

    .upload-area {
      border:3px dashed #c8e6c9; border-radius:14px; padding:28px 18px;
      text-align:center; background:#f8fff8; transition:all 0.3s; cursor:pointer; position:relative;
    }
    .upload-area:hover { border-color:#198754; background:#e8f5e9; }
    .upload-area input[type="file"] { position:absolute; opacity:0; width:100%; height:100%; top:0; left:0; cursor:pointer; }
    .upload-area i { font-size:2.2rem; color:#198754; }

    .preview-box { background:#f8fff8; border:2px solid #c8e6c9; border-radius:12px; padding:14px; margin-top:14px; }
    .img-preview-wrap { border:2px solid #c8e6c9; border-radius:14px; overflow:hidden; margin-top:14px; text-align:center; background:#f8fff8; padding:14px; }
    .img-preview-wrap img { max-width:100%; max-height:270px; border-radius:10px; object-fit:cover; }

    .btn-submit {
      background:linear-gradient(135deg,#198754,#20c997); border:none; border-radius:50px;
      padding:14px 48px; font-size:1.05rem; font-weight:700; color:white;
      transition:all 0.3s; box-shadow:0 5px 18px rgba(25,135,84,0.35);
    }
    .btn-submit:hover { transform:translateY(-3px); color:white; }

    .empty-state { background:white; border-radius:20px; padding:55px 30px; text-align:center; box-shadow:0 8px 25px rgba(0,0,0,0.08); }
    .fade-in { opacity:0; transform:translateY(20px); transition:all 0.7s ease; }
    .fade-in.show { opacity:1; transform:translateY(0); }

    footer { background:#198754; color:white; }
    footer a:hover { color:#ffd700 !important; }

    @media(max-width:768px) {
      .hero h1 { font-size:2rem; }
      .form-card { padding:26px 16px; }
      .stat-item { border-right:none; border-bottom:2px solid #e8f5e9; padding:12px 0; }
      .stat-item:last-child { border-bottom:none; }
    }
  </style>
</head>
<body>

<?php include "./includes/navbar.php" ?>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1><i class="bi bi-chat-heart-fill me-3"></i>Community Feedback</h1>
    <p class="mt-3">Share your experience — text, audio, video or a photo memory.</p>
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
      <a href="#feedback-list" class="btn btn-light btn-lg px-4">
        <i class="bi bi-eye me-2"></i> View Feedback
      </a>
      <a href="#feedback-form" class="btn btn-outline-light btn-lg px-4">
        <i class="bi bi-pencil-fill me-2"></i> Share Yours
      </a>
    </div>
  </div>
</section>

<!-- ── FEEDBACK LIST ── -->
<section class="section-green" id="feedback-list">
  <div class="container">
    <h2 class="section-title text-center d-block">What People Are Saying</h2>

    <!-- STATS -->
    <div class="stats-bar">
      <div class="row g-0">
        <div class="col"><div class="stat-item"><i class="bi bi-chat-heart-fill"></i><h3><?php echo $total_all; ?></h3><p>Total</p></div></div>
        <div class="col"><div class="stat-item"><i class="bi bi-chat-text-fill"></i><h3><?php echo $total_text; ?></h3><p>Text</p></div></div>
        <div class="col"><div class="stat-item"><i class="bi bi-mic-fill"></i><h3><?php echo $total_audio; ?></h3><p>Audio</p></div></div>
        <div class="col"><div class="stat-item"><i class="bi bi-camera-video-fill"></i><h3><?php echo $total_video; ?></h3><p>Video</p></div></div>
        <div class="col"><div class="stat-item"><i class="bi bi-images"></i><h3><?php echo $total_image; ?></h3><p>Images</p></div></div>
      </div>
    </div>

    <!-- FILTER TABS -->
    <div class="filter-tabs">
      <a href="feedback.php" class="filter-tab <?php echo empty($type_filter)?'active':''; ?>"><i class="bi bi-grid-fill me-1"></i>All</a>
      <a href="feedback.php?type=text"  class="filter-tab <?php echo $type_filter=='text' ?'active':''; ?>"><i class="bi bi-chat-text-fill me-1"></i>Text</a>
      <a href="feedback.php?type=audio" class="filter-tab <?php echo $type_filter=='audio'?'active':''; ?>"><i class="bi bi-mic-fill me-1"></i>Audio</a>
      <a href="feedback.php?type=video" class="filter-tab <?php echo $type_filter=='video'?'active':''; ?>"><i class="bi bi-camera-video-fill me-1"></i>Video</a>
      <a href="feedback.php?type=image" class="filter-tab <?php echo $type_filter=='image'?'active':''; ?>"><i class="bi bi-images me-1"></i>Images</a>
    </div>

    <?php if($total > 0): ?>
      <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-lg-4 col-md-6 fade-in">

            <?php if($row['submission_type'] == 'text'): ?>
              <div class="feedback-card type-text" style="position:relative;">
                <span class="type-badge badge-text" style="position:absolute;top:15px;right:15px;">Text</span>
                <div class="card-body-inner">
                  <i class="bi bi-quote quote-icon"></i>
                  <p class="card-message"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                  <div class="author-row">
                    <div class="avatar"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                    <div>
                      <p class="author-name"><?php echo htmlspecialchars($row['name']); ?></p>
                      <span class="author-date"><i class="bi bi-calendar me-1"></i><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></span>
                    </div>
                  </div>
                </div>
              </div>

            <?php elseif($row['submission_type'] == 'audio'): ?>
              <div class="feedback-card type-audio" style="position:relative;">
                <span class="type-badge badge-audio" style="position:absolute;top:15px;right:15px;">Audio</span>
                <div class="card-body-inner">
                  <p class="fw-bold text-muted mb-0" style="font-size:0.88rem;">
                    <i class="bi bi-file-music me-1"></i><?php echo htmlspecialchars($row['file_name']); ?>
                  </p>
                  <audio controls>
                    <source src="<?php echo htmlspecialchars($row['file_path']); ?>">
                    Your browser does not support audio.
                  </audio>
                  <div class="author-row">
                    <div class="avatar av-audio"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                    <div>
                      <p class="author-name"><?php echo htmlspecialchars($row['name']); ?></p>
                      <span class="author-date"><i class="bi bi-calendar me-1"></i><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></span>
                    </div>
                  </div>
                </div>
              </div>

            <?php elseif($row['submission_type'] == 'video'): ?>
              <div class="feedback-card type-video" style="position:relative;">
                <span class="type-badge badge-video" style="position:absolute;top:15px;right:15px;">Video</span>
                <div class="card-body-inner">
                  <p class="fw-bold text-muted mb-0" style="font-size:0.88rem;">
                    <i class="bi bi-file-play me-1"></i><?php echo htmlspecialchars($row['file_name']); ?>
                  </p>
                  <video controls style="max-height:220px;object-fit:cover;">
                    <source src="<?php echo htmlspecialchars($row['file_path']); ?>">
                    Your browser does not support video.
                  </video>
                  <div class="author-row">
                    <div class="avatar av-video"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                    <div>
                      <p class="author-name"><?php echo htmlspecialchars($row['name']); ?></p>
                      <span class="author-date"><i class="bi bi-calendar me-1"></i><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></span>
                    </div>
                  </div>
                </div>
              </div>

            <?php elseif($row['submission_type'] == 'image'): ?>
              <div class="feedback-card type-image">
                <span class="type-badge badge-image" style="position:absolute;top:12px;right:12px;z-index:2;">Image</span>
                <div class="card-img-wrap" onclick="openLightbox('<?php echo htmlspecialchars($row['file_path']); ?>')">
                  <img src="<?php echo htmlspecialchars($row['file_path']); ?>"
                       alt="Photo by <?php echo htmlspecialchars($row['name']); ?>">
                  <div class="img-overlay"><i class="bi bi-zoom-in"></i></div>
                </div>
                <div class="image-card-body">
                  <?php if(!empty($row['time_spent'])): ?>
                    <div class="time-badge">
                      <i class="bi bi-clock-fill"></i>
                      Visited for: <?php echo htmlspecialchars($row['time_spent']); ?>
                    </div>
                  <?php endif; ?>
                  <?php if(!empty($row['enjoyed_most'])): ?>
  <div class="time-badge" style="background:#e8f5e9; color:#198754;">
    <i class="bi bi-star-fill"></i>
    Enjoyed: <?php echo htmlspecialchars($row['enjoyed_most']); ?>
  </div>
<?php endif; ?>
                  <div class="author-row">
                    <div class="avatar av-image"><?php echo strtoupper(substr($row['name'],0,1)); ?></div>
                    <div>
                      <p class="author-name"><?php echo htmlspecialchars($row['name']); ?></p>
                      <span class="author-date"><i class="bi bi-calendar me-1"></i><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          </div>
        <?php endwhile; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <i class="bi bi-chat-heart fs-1 text-success opacity-50 d-block mb-4"></i>
        <h4 class="text-success fw-bold">No feedback yet!</h4>
        <p class="text-muted mb-4">Be the first to share your experience.</p>
        <a href="#feedback-form" class="btn btn-success px-4">
          <i class="bi bi-pencil-fill me-2"></i> Share Feedback
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ── FEEDBACK FORM ── -->
<section class="section-white" id="feedback-form">
  <div class="container">
    <h2 class="section-title text-center d-block">Share Your Feedback</h2>
    <p class="text-center mb-5 text-muted">Choose how you'd like to share your experience with us.</p>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="form-card">

          <?php if($success): ?>
            <div class="alert alert-success border-0 rounded-3 p-4 mb-4 text-center">
              <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-2"></i>
              <h5 class="fw-bold">Submitted Successfully!</h5>
              <p class="mb-0"><?php echo $success; ?></p>
            </div>
          <?php endif; ?>

          <?php if($error): ?>
            <div class="alert alert-danger border-0 rounded-3 p-3 mb-4">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <strong><?php echo $error; ?></strong>
            </div>
          <?php endif; ?>

          <!-- TYPE SELECTOR -->
          <div class="type-selector">
            <div class="type-btn active" onclick="selectType('text',this)">
              <i class="bi bi-chat-text-fill"></i>Text
            </div>
            <div class="type-btn" onclick="selectType('audio',this)">
              <i class="bi bi-mic-fill"></i>Audio
            </div>
            <div class="type-btn" onclick="selectType('video',this)">
              <i class="bi bi-camera-video-fill"></i>Video
            </div>
            <div class="type-btn" onclick="selectType('image',this)">
              <i class="bi bi-images"></i>Image
            </div>
          </div>

          <form method="POST" action="feedback.php" enctype="multipart/form-data" id="feedbackForm">
            <input type="hidden" name="submission_type" id="submission_type" value="text">
            <input type="hidden" name="recorded_audio"  id="recorded_audio_data" value="">
            <input type="hidden" name="recorded_video"  id="recorded_video_data" value="">

            <!-- NAME -->
            <div class="mb-4">
              <label class="form-label">Your Name *</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="name" class="form-control" placeholder="Enter your name"
                  value="<?php echo $is_logged_in ? htmlspecialchars($username) : ''; ?>" required>
              </div>
            </div>

            <!-- TEXT -->
            <div id="section_text">
              <div class="mb-4">
                <label class="form-label">Your Message *</label>
                <textarea name="message" id="messageText" class="form-control" rows="5"
                  placeholder="Share your experience, wishes or blessings..."></textarea>
                <small class="text-muted">Minimum 10 characters.</small>
              </div>
            </div>

            <!-- AUDIO -->
            <div id="section_audio" style="display:none;">
              <label class="form-label">Audio Feedback *</label>
              <div class="media-tabs">
                <div class="media-tab active" onclick="switchTab('audio','record',this)"><i class="bi bi-mic-fill me-1"></i> Record</div>
                <div class="media-tab" onclick="switchTab('audio','upload',this)"><i class="bi bi-upload me-1"></i> Upload</div>
              </div>
              <div id="audio_record_box">
                <div class="recorder-box">
                  <p class="text-muted mb-3">Click the button to start recording</p>
                  <button type="button" class="record-btn" id="audioRecordBtn" onclick="toggleAudioRecording()">
                    <i class="bi bi-mic-fill" id="audioRecordIcon"></i>
                  </button>
                  <div class="timer mt-3" id="audioTimer">00:00</div>
                  <p class="text-muted mt-2" id="audioStatus">Ready to record</p>
                  <div id="audio_preview_box" style="display:none;" class="preview-box mt-3">
                    <p class="fw-bold text-success mb-2"><i class="bi bi-check-circle me-1"></i> Recording ready!</p>
                    <audio id="audioPreview" controls></audio>
                  </div>
                </div>
              </div>
              <div id="audio_upload_box" style="display:none;">
                <div class="upload-area">
                  <input type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a,.aac,.webm" onchange="showFileName(this,'audio_file_label')">
                  <i class="bi bi-file-music"></i>
                  <p class="mt-2"><strong>Click to upload audio</strong></p>
                  <p id="audio_file_label" style="color:#198754;font-weight:600;"></p>
                  <small class="text-muted">MP3, WAV, OGG, M4A | Max 10MB</small>
                </div>
              </div>
            </div>

            <!-- VIDEO -->
            <div id="section_video" style="display:none;">
              <label class="form-label">Video Feedback *</label>
              <div class="media-tabs">
                <div class="media-tab active" onclick="switchTab('video','record',this)"><i class="bi bi-camera-video-fill me-1"></i> Record</div>
                <div class="media-tab" onclick="switchTab('video','upload',this)"><i class="bi bi-upload me-1"></i> Upload</div>
              </div>
              <div id="video_record_box">
                <div class="recorder-box">
                  <p class="text-muted mb-3">Click to start recording video</p>
                  <video id="cameraPreview" autoplay muted style="width:100%;max-height:230px;border-radius:10px;display:none;margin-bottom:10px;background:#000;"></video>
                  <button type="button" class="record-btn" id="videoRecordBtn" onclick="toggleVideoRecording()">
                    <i class="bi bi-camera-video-fill" id="videoRecordIcon"></i>
                  </button>
                  <div class="timer mt-3" id="videoTimer">00:00</div>
                  <p class="text-muted mt-2" id="videoStatus">Ready to record</p>
                  <div id="video_preview_box" style="display:none;" class="preview-box mt-3">
                    <p class="fw-bold text-success mb-2"><i class="bi bi-check-circle me-1"></i> Recording ready!</p>
                    <video id="videoPreview" controls style="width:100%;border-radius:8px;"></video>
                  </div>
                </div>
              </div>
              <div id="video_upload_box" style="display:none;">
                <div class="upload-area">
                  <input type="file" name="video_file" accept=".mp4,.avi,.mov,.mkv,.webm" onchange="showFileName(this,'video_file_label')">
                  <i class="bi bi-file-play"></i>
                  <p class="mt-2"><strong>Click to upload video</strong></p>
                  <p id="video_file_label" style="color:#198754;font-weight:600;"></p>
                  <small class="text-muted">MP4, AVI, MOV, MKV | Max 50MB</small>
                </div>
              </div>
            </div>

            <!-- IMAGE -->
            <div id="section_image" style="display:none;">
              <div class="mb-3">
                <label class="form-label">Upload Your Photo *</label>
                <div class="upload-area">
                  <input type="file" name="feedback_image" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="previewImage(this)">
                  <i class="bi bi-image"></i>
                  <p class="mt-2"><strong>Click to select your photo</strong></p>
                  <p id="img_file_label" style="color:#198754;font-weight:600;"></p>
                  <small class="text-muted">JPG, PNG, GIF, WEBP | Max 5MB</small>
                </div>
                <div id="img_preview_wrap" style="display:none;" class="img-preview-wrap">
                  <img id="img_preview" src="" alt="Preview">
                  <p class="text-success fw-bold mt-2 mb-0"><i class="bi bi-check-circle-fill me-1"></i> Photo ready!</p>
                </div>
              </div>
             <div class="mb-4">
  <label class="form-label">
    Time Spent Visiting the NGO 
    <span class="text-muted fw-normal">(optional)</span>
  </label>
  <div class="input-group mb-3">
    <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
    <input type="text" name="time_spent" class="form-control"
      placeholder="e.g. 2 years, 6 months, 3 days...">
  </div>

  <label class="form-label">
    What Did You Enjoy the Most?
    <span class="text-muted fw-normal">(optional)</span>
  </label>
  <div class="input-group">
    <span class="input-group-text"><i class="bi bi-star-fill"></i></span>
    <input type="text" name="enjoyed_most" class="form-control"
      placeholder="e.g. The care, food, activities, staff...">
  </div>
</div>
                <small class="text-muted">How long have you been associated with Safe & Home?</small>
              </div>
            </div>

            <!-- SUBMIT -->
            <div class="text-center mt-4">
              <button type="submit" name="submit" class="btn-submit btn">
                <i class="bi bi-send-fill me-2"></i> Submit Feedback
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <span class="lightbox-close">&times;</span>
  <img id="lightbox-img" src="" alt="Feedback Image">
</div>

<!-- FOOTER -->
<footer class="py-4">
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
    <hr class="mt-4 opacity-25">
    <p class="text-center mb-0">
      © 2025 Safe & Home Foundation | All Rights Reserved |
      Made with <i class="bi bi-heart-fill text-danger"></i> for a better tomorrow
    </p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fade in
const fadeEls = document.querySelectorAll('.fade-in');
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('show'); });
}, { threshold: 0.1 });
fadeEls.forEach(el => obs.observe(el));

// Type selector
function selectType(type, el) {
  document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('submission_type').value = type;
  ['text','audio','video','image'].forEach(t => {
    document.getElementById('section_' + t).style.display = 'none';
  });
  document.getElementById('section_' + type).style.display = 'block';
  document.getElementById('messageText').required = (type === 'text');
  if(type !== 'video') stopAllStreams();
}

// Media tabs
function switchTab(media, tab, el) {
  el.closest('.media-tabs').querySelectorAll('.media-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  if(media === 'audio') {
    document.getElementById('audio_record_box').style.display = tab === 'record' ? 'block' : 'none';
    document.getElementById('audio_upload_box').style.display = tab === 'upload' ? 'block' : 'none';
  } else {
    document.getElementById('video_record_box').style.display = tab === 'record' ? 'block' : 'none';
    document.getElementById('video_upload_box').style.display = tab === 'upload' ? 'block' : 'none';
    if(tab !== 'record') stopAllStreams();
  }
}

function showFileName(input, labelId) {
  if(input.files && input.files[0])
    document.getElementById(labelId).textContent = '✅ ' + input.files[0].name;
}

function previewImage(input) {
  if(input.files && input.files[0]) {
    document.getElementById('img_file_label').textContent = '✅ ' + input.files[0].name;
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('img_preview').src = e.target.result;
      document.getElementById('img_preview_wrap').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Audio recording
let audioMediaRecorder=null, audioChunks=[], audioStream=null;
let audioTimerInterval=null, audioSeconds=0, isAudioRecording=false;

async function toggleAudioRecording() {
  if(!isAudioRecording) {
    try {
      audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
      audioMediaRecorder = new MediaRecorder(audioStream);
      audioChunks = [];
      audioMediaRecorder.ondataavailable = e => audioChunks.push(e.data);
      audioMediaRecorder.onstop = () => {
        const blob = new Blob(audioChunks, { type: 'audio/webm' });
        document.getElementById('audioPreview').src = URL.createObjectURL(blob);
        document.getElementById('audio_preview_box').style.display = 'block';
        const reader = new FileReader();
        reader.onloadend = () => { document.getElementById('recorded_audio_data').value = reader.result; };
        reader.readAsDataURL(blob);
        document.getElementById('audioRecordBtn').classList.remove('recording');
        document.getElementById('audioRecordIcon').className = 'bi bi-mic-fill';
        document.getElementById('audioStatus').textContent = '✅ Recording saved!';
        document.getElementById('audioTimer').classList.remove('recording');
      };
      audioMediaRecorder.start();
      isAudioRecording = true; audioSeconds = 0;
      document.getElementById('audioRecordBtn').classList.add('recording');
      document.getElementById('audioRecordIcon').className = 'bi bi-stop-fill';
      document.getElementById('audioStatus').textContent = '🔴 Recording... Click to stop';
      document.getElementById('audioTimer').classList.add('recording');
      audioTimerInterval = setInterval(() => {
        audioSeconds++;
        document.getElementById('audioTimer').textContent =
          String(Math.floor(audioSeconds/60)).padStart(2,'0') + ':' + String(audioSeconds%60).padStart(2,'0');
      }, 1000);
    } catch(err) { alert('Microphone access denied.'); }
  } else {
    audioMediaRecorder.stop();
    audioStream.getTracks().forEach(t => t.stop());
    isAudioRecording = false;
    clearInterval(audioTimerInterval);
  }
}

// Video recording
let videoMediaRecorder=null, videoChunks=[], videoStream=null;
let videoTimerInterval=null, videoSeconds=0, isVideoRecording=false;

async function toggleVideoRecording() {
  if(!isVideoRecording) {
    try {
      videoStream = await navigator.mediaDevices.getUserMedia({ video:true, audio:true });
      const cam = document.getElementById('cameraPreview');
      cam.srcObject = videoStream; cam.style.display = 'block';
      videoMediaRecorder = new MediaRecorder(videoStream);
      videoChunks = [];
      videoMediaRecorder.ondataavailable = e => videoChunks.push(e.data);
      videoMediaRecorder.onstop = () => {
        const blob = new Blob(videoChunks, { type: 'video/webm' });
        document.getElementById('cameraPreview').style.display = 'none';
        document.getElementById('videoPreview').src = URL.createObjectURL(blob);
        document.getElementById('video_preview_box').style.display = 'block';
        const reader = new FileReader();
        reader.onloadend = () => { document.getElementById('recorded_video_data').value = reader.result; };
        reader.readAsDataURL(blob);
        document.getElementById('videoRecordBtn').classList.remove('recording');
        document.getElementById('videoRecordIcon').className = 'bi bi-camera-video-fill';
        document.getElementById('videoStatus').textContent = '✅ Recording saved!';
        document.getElementById('videoTimer').classList.remove('recording');
      };
      videoMediaRecorder.start();
      isVideoRecording = true; videoSeconds = 0;
      document.getElementById('videoRecordBtn').classList.add('recording');
      document.getElementById('videoRecordIcon').className = 'bi bi-stop-fill';
      document.getElementById('videoStatus').textContent = '🔴 Recording... Click to stop';
      document.getElementById('videoTimer').classList.add('recording');
      videoTimerInterval = setInterval(() => {
        videoSeconds++;
        document.getElementById('videoTimer').textContent =
          String(Math.floor(videoSeconds/60)).padStart(2,'0') + ':' + String(videoSeconds%60).padStart(2,'0');
      }, 1000);
    } catch(err) { alert('Camera/Microphone access denied.'); }
  } else {
    videoMediaRecorder.stop();
    videoStream.getTracks().forEach(t => t.stop());
    isVideoRecording = false;
    clearInterval(videoTimerInterval);
  }
}

function stopAllStreams() {
  if(videoStream) { videoStream.getTracks().forEach(t => t.stop()); videoStream = null; }
  if(audioStream) { audioStream.getTracks().forEach(t => t.stop()); audioStream = null; }
  document.getElementById('cameraPreview').style.display = 'none';
}

// Lightbox
function openLightbox(src) {
  document.getElementById('lightbox-img').src = src;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeLightbox(); });
</script>
</body>
</html>
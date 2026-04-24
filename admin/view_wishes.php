<?php
session_start();

if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('../includes/db_connect.php');

// Handle delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM feedback WHERE id=$id");
    header("Location: view_wishes.php?deleted=1");
    exit();
}

// Filter by type
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if(in_array($filter, ['text','audio','video','image'])) {
    $sql = "SELECT * FROM feedback WHERE submission_type='$filter' ORDER BY submitted_at DESC";
} else {
    $sql = "SELECT * FROM feedback ORDER BY submitted_at DESC";
}

$result = mysqli_query($conn, $sql);
$total  = mysqli_num_rows($result);

// Counts per type
$count_all   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback"))['c'];
$count_text  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='text'"))['c'];
$count_audio = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='audio'"))['c'];
$count_video = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='video'"))['c'];
$count_image = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM feedback WHERE submission_type='image'"))['c'];

// Load into array
$wishes_data = [];
while($row = mysqli_fetch_assoc($result)) $wishes_data[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback — SafeHome Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:       #1a472a;
            --primary-light: #2d6a4f;
            --accent:        #52b788;
            --accent-soft:   #d8f3dc;
            --sidebar-w:     260px;
            --topbar-h:      68px;
            --bg:            #f0f4f1;
            --card:          #ffffff;
            --text:          #1b2b1e;
            --muted:         #6b7f70;
            --border:        #dce8df;
            --radius:        14px;
            --shadow:        0 2px 16px rgba(26,71,42,0.08);
        }
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

        /* ── Sidebar ── */
        .sidebar { position:fixed; top:0; left:0; width:var(--sidebar-w); height:100vh; background:var(--primary); display:flex; flex-direction:column; z-index:100; overflow-y:auto; }
        .sidebar-brand { padding:24px 20px 20px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .sidebar-brand h4 { color:#fff; font-weight:800; font-size:1.25rem; }
        .sidebar-brand h4 span { color:var(--accent); }
        .sidebar-brand small { color:rgba(255,255,255,0.5); font-size:0.72rem; font-weight:500; letter-spacing:0.8px; text-transform:uppercase; }
        .sidebar-nav { padding:16px 12px; flex:1; }
        .nav-label { font-size:0.68rem; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; color:rgba(255,255,255,0.35); padding:10px 10px 6px; }
        .nav-item a { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; color:rgba(255,255,255,0.75); text-decoration:none; font-size:0.875rem; font-weight:500; transition:all 0.2s; margin-bottom:2px; }
        .nav-item a:hover { background:rgba(255,255,255,0.12); color:#fff; }
        .nav-item a.active { background:var(--accent); color:#fff; }
        .nav-item a i { font-size:1rem; width:20px; text-align:center; }
        .sidebar-footer { padding:16px 12px; border-top:1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.875rem; transition:all 0.2s; }
        .sidebar-footer a:hover { background:rgba(231,57,70,0.2); color:#ff8a8a; }

        /* ── Main ── */
        .main-wrap { margin-left:var(--sidebar-w); min-height:100vh; display:flex; flex-direction:column; }

        /* ── Topbar ── */
        .topbar { height:var(--topbar-h); background:var(--card); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 28px; position:sticky; top:0; z-index:50; }
        .topbar-title { font-size:1.15rem; font-weight:700; }
        .topbar-title span { color:var(--accent); }
        .admin-chip { display:flex; align-items:center; gap:8px; background:var(--accent-soft); padding:6px 14px 6px 8px; border-radius:50px; font-size:0.82rem; font-weight:600; color:var(--primary); }
        .admin-chip .avatar-chip { width:30px; height:30px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; }

        /* ── Page ── */
        .page-content { padding:28px; flex:1; }

        /* ── Stats ── */
        .stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:24px; }
        .stat-card { background:var(--card); border-radius:var(--radius); padding:18px; box-shadow:var(--shadow); border:1px solid var(--border); display:flex; align-items:center; gap:14px; transition:transform 0.2s,box-shadow 0.2s; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(26,71,42,0.12); }
        .stat-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
        .stat-info .value { font-size:1.5rem; font-weight:800; line-height:1; }
        .stat-info .label { font-size:0.74rem; color:var(--muted); font-weight:500; margin-top:4px; }
        .icon-blue   { background:#dbeafe; color:#1d4ed8; }
        .icon-green  { background:#d8f3dc; color:#1a472a; }
        .icon-teal   { background:#ccfbf1; color:#0f766e; }
        .icon-red    { background:#fee2e2; color:#991b1b; }
        .icon-orange { background:#ffedd5; color:#9a3412; }

        /* ── Card ── */
        .page-card { background:var(--card); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow); border:1px solid var(--border); margin-bottom:24px; }

        /* ── Filter Tabs ── */
        .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:24px; }
        .filter-tab { border-radius:50px; padding:7px 20px; font-weight:600; font-size:0.85rem; text-decoration:none; border:2px solid var(--border); color:var(--muted); background:var(--card); transition:all 0.2s; }
        .filter-tab:hover { border-color:var(--accent); color:var(--primary); }
        .filter-tab.active { background:var(--accent); border-color:var(--accent); color:#fff; }

        /* ── Feedback Cards ── */
        .feedback-card { background:var(--card); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow); border:1px solid var(--border); margin-bottom:18px; border-left:5px solid var(--border); transition:all 0.3s; }
        .feedback-card:hover { box-shadow:0 8px 28px rgba(26,71,42,0.12); transform:translateY(-2px); }
        .feedback-card.type-text  { border-left-color:var(--accent); }
        .feedback-card.type-audio { border-left-color:#1d4ed8; }
        .feedback-card.type-video { border-left-color:#991b1b; }
        .feedback-card.type-image { border-left-color:#9a3412; }

        /* ── Type Badges ── */
        .type-badge { font-size:0.75rem; padding:4px 12px; border-radius:20px; font-weight:700; display:inline-flex; align-items:center; gap:4px; }
        .badge-text  { background:#d8f3dc; color:#1a472a; }
        .badge-audio { background:#dbeafe; color:#1d4ed8; }
        .badge-video { background:#fee2e2; color:#991b1b; }
        .badge-image { background:#ffedd5; color:#9a3412; }

        /* ── Avatar ── */
        .u-avatar { width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--accent)); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; flex-shrink:0; }

        /* ── Time badge ── */
        .time-badge { display:inline-flex; align-items:center; gap:6px; background:#fff3cd; color:#b35900; border-radius:20px; padding:4px 12px; font-size:0.78rem; font-weight:700; }
        .enjoyed-badge { display:inline-flex; align-items:center; gap:6px; background:var(--accent-soft); color:var(--primary); border-radius:20px; padding:4px 12px; font-size:0.78rem; font-weight:700; }

        /* ── Gallery image ── */
        .gallery-thumb { width:100%; max-height:220px; object-fit:cover; border-radius:10px; margin-bottom:12px; cursor:pointer; transition:opacity 0.2s; }
        .gallery-thumb:hover { opacity:0.85; }

        /* ── Lightbox ── */
        .lightbox { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.92); align-items:center; justify-content:center; }
        .lightbox.active { display:flex; }
        .lightbox img { max-width:90vw; max-height:85vh; border-radius:12px; object-fit:contain; }
        .lightbox-close { position:fixed; top:18px; right:24px; color:white; font-size:2.5rem; cursor:pointer; z-index:10000; }

        /* ── Media ── */
        audio, video { width:100%; border-radius:10px; margin-top:10px; }

        /* ── Empty ── */
        .empty-state { text-align:center; padding:60px 20px; }
        .empty-state i { font-size:3.5rem; color:var(--accent); opacity:0.4; display:block; margin-bottom:14px; }

        /* ── Animations ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }
        .fade-up { animation:fadeUp 0.4s ease both; }
        .d1{animation-delay:.05s;} .d2{animation-delay:.1s;} .d3{animation-delay:.15s;} .d4{animation-delay:.2s;} .d5{animation-delay:.25s;}
    </style>
</head>
<body>

<!-- ═══════════ SIDEBAR ═══════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-house-heart-fill me-2"></i>Safe<span>Home</span></h4>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <div class="nav-item"><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-label mt-2">Management</div>
        <div class="nav-item"><a href="view_users.php"><i class="bi bi-people-fill"></i> Users</a></div>
        <div class="nav-item"><a href="view_volunteers.php"><i class="bi bi-person-check-fill"></i> Volunteers</a></div>
        <div class="nav-item"><a href="view_donations.php"><i class="bi bi-cash-coin"></i> Donations</a></div>
        <div class="nav-item"><a href="view_messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></div>
        <div class="nav-item"><a href="view_wishes.php" class="active"><i class="bi bi-stars"></i> Feedback</a></div>
        <div class="nav-item"><a href="view_internships.php"><i class="bi bi-briefcase-fill"></i> Internships</a></div>
        <div class="nav-label mt-2">Content</div>
        <div class="nav-item"><a href="view_gallery.php"><i class="bi bi-images"></i> Gallery</a></div>
        <div class="nav-item"><a href="view_guidelines.php"><i class="bi bi-journal-text"></i> Guidelines</a></div>
        <div class="nav-item"><a href="view_terms.php"><i class="bi bi-file-earmark-text"></i> Terms</a></div>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</aside>

<!-- ═══════════ MAIN ═══════════ -->
<div class="main-wrap">

    <header class="topbar">
        <div class="topbar-title">
            <i class="bi bi-chat-heart-fill me-2" style="color:var(--accent)"></i>Community <span>Feedback</span>
        </div>
        <div class="admin-chip">
            <div class="avatar-chip">A</div>
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </header>

    <main class="page-content">

        <!-- Alerts -->
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-warning alert-dismissible fade show fade-up">
                <i class="bi bi-trash me-2"></i>Feedback deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card fade-up d1">
                <div class="stat-icon icon-blue"><i class="bi bi-chat-heart-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $count_all; ?></div>
                    <div class="label">Total</div>
                </div>
            </div>
            <div class="stat-card fade-up d2">
                <div class="stat-icon icon-green"><i class="bi bi-chat-text-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $count_text; ?></div>
                    <div class="label">Text</div>
                </div>
            </div>
            <div class="stat-card fade-up d3">
                <div class="stat-icon icon-teal"><i class="bi bi-mic-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $count_audio; ?></div>
                    <div class="label">Audio</div>
                </div>
            </div>
            <div class="stat-card fade-up d4">
                <div class="stat-icon icon-red"><i class="bi bi-camera-video-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $count_video; ?></div>
                    <div class="label">Video</div>
                </div>
            </div>
            <div class="stat-card fade-up d5">
                <div class="stat-icon icon-orange"><i class="bi bi-images"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $count_image; ?></div>
                    <div class="label">Images</div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs fade-up">
            <a href="?filter=all"   class="filter-tab <?php echo $filter=='all'   ?'active':''; ?>"><i class="bi bi-grid-fill me-1"></i>All</a>
            <a href="?filter=text"  class="filter-tab <?php echo $filter=='text'  ?'active':''; ?>"><i class="bi bi-chat-text-fill me-1"></i>Text</a>
            <a href="?filter=audio" class="filter-tab <?php echo $filter=='audio' ?'active':''; ?>"><i class="bi bi-mic-fill me-1"></i>Audio</a>
            <a href="?filter=video" class="filter-tab <?php echo $filter=='video' ?'active':''; ?>"><i class="bi bi-camera-video-fill me-1"></i>Video</a>
            <a href="?filter=image" class="filter-tab <?php echo $filter=='image' ?'active':''; ?>"><i class="bi bi-images me-1"></i>Images</a>
        </div>

        <!-- Feedback Cards -->
        <?php if(count($wishes_data) > 0): ?>
            <?php foreach($wishes_data as $row): ?>
            <div class="feedback-card type-<?php echo $row['submission_type']; ?> fade-up">

                <!-- Header Row -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="u-avatar">
                            <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong class="d-block" style="font-size:0.95rem;">
                                <?php echo htmlspecialchars($row['name']); ?>
                            </strong>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <?php echo date('d M Y, h:i A', strtotime($row['submitted_at'])); ?>
                            </small>
                        </div>
                    </div>
                    <!-- Type Badge -->
                    <?php if($row['submission_type'] == 'text'): ?>
                        <span class="type-badge badge-text"><i class="bi bi-chat-text-fill"></i>Text</span>
                    <?php elseif($row['submission_type'] == 'audio'): ?>
                        <span class="type-badge badge-audio"><i class="bi bi-mic-fill"></i>Audio</span>
                    <?php elseif($row['submission_type'] == 'video'): ?>
                        <span class="type-badge badge-video"><i class="bi bi-camera-video-fill"></i>Video</span>
                    <?php elseif($row['submission_type'] == 'image'): ?>
                        <span class="type-badge badge-image"><i class="bi bi-images"></i>Image</span>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <?php if($row['submission_type'] == 'text'): ?>
                    <p style="font-size:0.95rem;color:#444;line-height:1.8;font-style:italic;margin-bottom:0;">
                        "<?php echo nl2br(htmlspecialchars($row['message'])); ?>"
                    </p>

                <?php elseif($row['submission_type'] == 'audio'): ?>
                    <p class="text-muted mb-1" style="font-size:0.85rem;">
                        <i class="bi bi-file-music me-1"></i><?php echo htmlspecialchars($row['file_name']); ?>
                    </p>
                    <audio controls>
                        <source src="../<?php echo htmlspecialchars($row['file_path']); ?>">
                        Your browser does not support audio.
                    </audio>

                <?php elseif($row['submission_type'] == 'video'): ?>
                    <p class="text-muted mb-1" style="font-size:0.85rem;">
                        <i class="bi bi-file-play me-1"></i><?php echo htmlspecialchars($row['file_name']); ?>
                    </p>
                    <video controls style="max-height:220px;object-fit:cover;">
                        <source src="../<?php echo htmlspecialchars($row['file_path']); ?>">
                        Your browser does not support video.
                    </video>

                <?php elseif($row['submission_type'] == 'image'): ?>
                    <img src="../<?php echo htmlspecialchars($row['file_path']); ?>"
                         alt="Feedback by <?php echo htmlspecialchars($row['name']); ?>"
                         class="gallery-thumb"
                         onclick="openLightbox('../<?php echo htmlspecialchars($row['file_path']); ?>')">
                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <?php if(!empty($row['time_spent'])): ?>
                            <span class="time-badge">
                                <i class="bi bi-clock-fill"></i>
                                Visited: <?php echo htmlspecialchars($row['time_spent']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if(!empty($row['enjoyed_most'])): ?>
                            <span class="enjoyed-badge">
                                <i class="bi bi-star-fill"></i>
                                Enjoyed: <?php echo htmlspecialchars($row['enjoyed_most']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Delete Button -->
                <div class="mt-3">
                    <a href="?delete=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                        class="btn btn-sm btn-outline-danger px-3"
                        onclick="return confirm('Permanently delete this feedback?')">
                        <i class="bi bi-trash me-1"></i> Delete
                    </a>
                </div>

            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="empty-state page-card">
                <i class="bi bi-chat-heart"></i>
                <h5 class="text-muted">No feedback found</h5>
                <p class="text-muted small">
                    <?php echo $filter != 'all' ? 'Try a different filter.' : 'No feedback submitted yet.'; ?>
                </p>
            </div>
        <?php endif; ?>

    </main>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close">&times;</span>
    <img id="lightbox-img" src="" alt="Feedback Image">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeLightbox(); });
</script>
</body>
</html>
<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('../includes/db_connect.php');

// Handle delete
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM contact_messages WHERE id=$id");
    header("Location: view_messages.php?deleted=1");
    exit();
}

// Get all messages
$result = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
$total  = mysqli_num_rows($result);

// Check if created_at column exists
$check_col  = mysqli_query($conn, "SHOW COLUMNS FROM contact_messages LIKE 'created_at'");
$has_date   = mysqli_num_rows($check_col) > 0;

$today      = 0;
$this_month = 0;
if($has_date) {
    $today      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM contact_messages WHERE DATE(created_at)=CURDATE()"))['c'];
    $this_month = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM contact_messages WHERE MONTH(created_at)=MONTH(CURRENT_DATE()) AND YEAR(created_at)=YEAR(CURRENT_DATE())"))['c'];
}

// Load into array for modal loop
$messages_data = [];
while($row = mysqli_fetch_assoc($result)) $messages_data[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — SafeHome Admin</title>
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
        .admin-chip .avatar { width:30px; height:30px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; }

        /* ── Page ── */
        .page-content { padding:28px; flex:1; }

        /* ── Stats ── */
        .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:24px; }
        .stat-card { background:var(--card); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow); border:1px solid var(--border); display:flex; align-items:center; gap:16px; transition:transform 0.2s,box-shadow 0.2s; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(26,71,42,0.12); }
        .stat-icon { width:52px; height:52px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
        .stat-info .value { font-size:1.7rem; font-weight:800; line-height:1; }
        .stat-info .label { font-size:0.78rem; color:var(--muted); font-weight:500; margin-top:4px; }
        .icon-blue  { background:#dbeafe; color:#1d4ed8; }
        .icon-green { background:#d8f3dc; color:#1a472a; }
        .icon-gold  { background:#fef3c7; color:#92400e; }

        /* ── Card ── */
        .page-card { background:var(--card); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow); border:1px solid var(--border); margin-bottom:24px; }

        /* ── Table ── */
        .table thead th { font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--muted); border-bottom:2px solid var(--border); padding:10px 14px; background:transparent; }
        .table tbody td { padding:12px 14px; font-size:0.86rem; vertical-align:middle; border-color:var(--border); }
        .table tbody tr:hover { background:#f6fbf7; }

        /* ── Avatar ── */
        .u-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--accent)); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:1rem; flex-shrink:0; }
        .u-avatar-lg { width:72px; height:72px; font-size:1.8rem; }

        /* ── Badges ── */
        .badge-date { background:var(--accent-soft); color:var(--primary); padding:4px 11px; border-radius:20px; font-size:0.76rem; font-weight:600; }
        .message-preview { max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--muted); font-size:0.84rem; }

        /* ── Modal ── */
        .modal-header { background:var(--primary); color:#fff; border-radius:12px 12px 0 0; }
        .modal-content { border-radius:12px; border:none; }
        .info-row td { padding:10px 12px; font-size:0.875rem; border-color:var(--border); }
        .info-row td:first-child { color:var(--muted); width:35%; }
        .message-box { background:#f8fffe; border:1px solid var(--border); border-radius:10px; padding:16px; font-size:0.9rem; line-height:1.7; color:var(--text); }

        /* ── Empty ── */
        .empty-state { text-align:center; padding:60px 20px; }
        .empty-state i { font-size:3.5rem; color:var(--accent); opacity:0.4; display:block; margin-bottom:14px; }

        /* ── Animations ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }
        .fade-up { animation:fadeUp 0.4s ease both; }
        .d1{animation-delay:.05s;} .d2{animation-delay:.1s;} .d3{animation-delay:.15s;}
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
        <div class="nav-item"><a href="view_messages.php" class="active"><i class="bi bi-chat-dots-fill"></i> Messages</a></div>
        <div class="nav-item"><a href="view_wishes.php"><i class="bi bi-stars"></i> Wishes</a></div>
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
            <i class="bi bi-chat-dots-fill me-2" style="color:var(--accent)"></i>Contact <span>Messages</span>
        </div>
        <div class="admin-chip">
            <div class="avatar">A</div>
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </header>

    <main class="page-content">

        <!-- Alerts -->
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-warning alert-dismissible fade show fade-up">
                <i class="bi bi-trash me-2"></i>Message deleted successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card fade-up d1">
                <div class="stat-icon icon-blue"><i class="bi bi-envelope-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total; ?></div>
                    <div class="label">Total Messages</div>
                </div>
            </div>
            <div class="stat-card fade-up d2">
                <div class="stat-icon icon-green"><i class="bi bi-calendar-month"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $this_month; ?></div>
                    <div class="label">This Month</div>
                </div>
            </div>
            <div class="stat-card fade-up d3">
                <div class="stat-icon icon-gold"><i class="bi bi-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $today; ?></div>
                    <div class="label">Today</div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="page-card fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2" style="color:var(--accent)"></i>All Messages
                </h5>
                <span class="badge bg-success rounded-pill"><?php echo count($messages_data); ?> Messages</span>
            </div>

            <?php if(count($messages_data) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sender</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($messages_data as $i => $row): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="u-avatar">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                    <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <i class="bi bi-envelope me-1 text-muted"></i>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>
                            <td>
                                <i class="bi bi-telephone me-1 text-muted"></i>
                                <?php echo htmlspecialchars($row['phone'] ?? '—'); ?>
                            </td>
                            <td>
                                <span class="message-preview" title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge-date">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php
                                    if($has_date && !empty($row['created_at']))
                                        echo date('d M Y', strtotime($row['created_at']));
                                    else
                                        echo 'N/A';
                                    ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewModal<?php echo $row['id']; ?>"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete this message?')"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-envelope"></i>
                <h5 class="text-muted">No messages yet</h5>
                <p class="text-muted small">Contact messages will appear here.</p>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- ═══════════ MODALS ═══════════ -->
<?php foreach($messages_data as $row): ?>
<div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-fill me-2"></i>Message Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="u-avatar u-avatar-lg mx-auto">
                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                    </div>
                    <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($row['name']); ?></h5>
                    <small class="text-muted">Message #<?php echo $row['id']; ?></small>
                </div>
                <table class="table table-borderless info-row mb-3">
                    <tr>
                        <td><i class="bi bi-envelope me-2 text-muted"></i>Email</td>
                        <td><strong><?php echo htmlspecialchars($row['email']); ?></strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-telephone me-2 text-muted"></i>Phone</td>
                        <td><strong><?php echo htmlspecialchars($row['phone'] ?? '—'); ?></strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-calendar me-2 text-muted"></i>Received</td>
                        <td>
                            <strong>
                            <?php
                            if($has_date && !empty($row['created_at']))
                                echo date('d M Y, h:i A', strtotime($row['created_at']));
                            else
                                echo 'N/A';
                            ?>
                            </strong>
                        </td>
                    </tr>
                </table>
                <label class="form-label fw-bold text-muted"
                    style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.6px;">
                    <i class="bi bi-chat-text me-1"></i>Message
                </label>
                <div class="message-box">
                    <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="?delete=<?php echo $row['id']; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this message?')">
                    <i class="bi bi-trash me-1"></i>Delete
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
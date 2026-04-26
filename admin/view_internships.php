<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('../includes/db_connect.php');

$success = "";
$error   = "";

// ── Approve ───────────────────────────────────────────────────────────────────
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    if (mysqli_query($conn, "UPDATE internship_applications SET status='approved' WHERE id=$id")) {
        $success = "Internship application approved!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// ── Reject ────────────────────────────────────────────────────────────────────
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    if (mysqli_query($conn, "UPDATE internship_applications SET status='rejected' WHERE id=$id")) {
        $success = "Internship application rejected.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// ── Delete ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if (mysqli_query($conn, "DELETE FROM internship_applications WHERE id=$id")) {
        header("Location: view_internships.php?deleted=1");
        exit();
    } else {
        $error = "Error deleting: " . mysqli_error($conn);
    }
}

// ── Filter ────────────────────────────────────────────────────────────────────
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
if ($filter === 'pending') {
    $sql = "SELECT * FROM internship_applications WHERE status='pending' ORDER BY id DESC";
} elseif ($filter === 'approved') {
    $sql = "SELECT * FROM internship_applications WHERE status='approved' ORDER BY id DESC";
} elseif ($filter === 'rejected') {
    $sql = "SELECT * FROM internship_applications WHERE status='rejected' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM internship_applications ORDER BY id DESC";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn) . "<br>SQL: " . $sql);
}

// Load into array (so we can loop twice: table + modals)
$applications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $applications[] = $row;
}

// ── Stats ─────────────────────────────────────────────────────────────────────
$total    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM internship_applications"))['c'];
$pending  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM internship_applications WHERE status='pending'"))['c'];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM internship_applications WHERE status='approved'"))['c'];
$rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM internship_applications WHERE status='rejected'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internships — SafeHome Admin</title>
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
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar { position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh; background: var(--primary); display: flex; flex-direction: column; z-index: 100; overflow-y: auto; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand h4 { color: #fff; font-weight: 800; font-size: 1.25rem; }
        .sidebar-brand h4 span { color: var(--accent); }
        .sidebar-brand small { color: rgba(255,255,255,0.5); font-size: 0.72rem; font-weight: 500; letter-spacing: 0.8px; text-transform: uppercase; }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label { font-size: 0.68rem; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: rgba(255,255,255,0.35); padding: 10px 10px 6px; }
        .nav-item a { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; color: rgba(255,255,255,0.75); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; }
        .nav-item a:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-item a.active { background: var(--accent); color: #fff; }
        .nav-item a i { font-size: 1rem; width: 20px; text-align: center; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px; color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
        .sidebar-footer a:hover { background: rgba(231,57,70,0.2); color: #ff8a8a; }

        /* ── Main ── */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

        /* ── Topbar ── */
        .topbar { height: var(--topbar-h); background: var(--card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 50; }
        .topbar-title { font-size: 1.15rem; font-weight: 700; }
        .topbar-title span { color: var(--accent); }
        .admin-chip { display: flex; align-items: center; gap: 8px; background: var(--accent-soft); padding: 6px 14px 6px 8px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; color: var(--primary); }
        .admin-chip .avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }

        /* ── Page ── */
        .page-content { padding: 28px; flex: 1; }

        /* ── Stats ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px; }
        .stat-card { background: var(--card); border-radius: var(--radius); padding: 20px 22px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; align-items: center; gap: 14px; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; text-decoration: none; color: inherit; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(26,71,42,0.12); color: inherit; }
        .stat-card.active-filter { border: 2px solid var(--accent); background: var(--accent-soft); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
        .stat-info .value { font-size: 1.6rem; font-weight: 800; line-height: 1; }
        .stat-info .label { font-size: 0.76rem; color: var(--muted); font-weight: 500; margin-top: 3px; }
        .icon-blue   { background: #dbeafe; color: #1d4ed8; }
        .icon-yellow { background: #fef9c3; color: #854d0e; }
        .icon-green  { background: #d1fae5; color: #065f46; }
        .icon-red    { background: #fee2e2; color: #991b1b; }

        /* ── Card ── */
        .page-card { background: var(--card); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }

        /* ── Filter tabs ── */
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-tab { padding: 7px 18px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1.5px solid var(--border); color: var(--muted); transition: all 0.2s; }
        .filter-tab:hover { border-color: var(--accent); color: var(--primary); }
        .filter-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Table ── */
        .table thead th { font-size: 0.74rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); border-bottom: 2px solid var(--border); padding: 10px 12px; background: transparent; white-space: nowrap; }
        .table tbody td { padding: 11px 12px; font-size: 0.845rem; vertical-align: middle; border-color: var(--border); }
        .table tbody tr:hover { background: #f6fbf7; }

        /* ── Status badges ── */
        .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 11px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-pending  { background: #fef9c3; color: #854d0e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* ── Area / Duration badges ── */
        .badge-area     { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); color: #1a472a; padding: 3px 11px; border-radius: 20px; font-size: 0.76rem; font-weight: 600; }
        .badge-duration { background: linear-gradient(135deg,#e3f2fd,#bbdefb); color: #1565c0; padding: 3px 11px; border-radius: 20px; font-size: 0.76rem; font-weight: 600; }

        /* ── Avatar ── */
        .i-avatar    { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0; }
        .i-avatar-lg { width: 68px; height: 68px; font-size: 1.7rem; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 3.5rem; color: var(--accent); opacity: 0.35; display: block; margin-bottom: 14px; }

        /* ── Modal ── */
        .modal-header { background: var(--primary); color: #fff; border-radius: 12px 12px 0 0; }
        .modal-content { border-radius: 12px; border: none; }
        .detail-row { padding: 10px 0; border-bottom: 1px solid var(--border); display: flex; align-items: flex-start; gap: 12px; font-size: 0.875rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: var(--primary); min-width: 140px; flex-shrink: 0; }

        /* ── Animations ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp 0.4s ease both; }
        .d1{animation-delay:.05s} .d2{animation-delay:.1s} .d3{animation-delay:.15s} .d4{animation-delay:.2s}
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
        <div class="nav-item"><a href="view_wishes.php"><i class="bi bi-stars"></i> Wishes</a></div>
        <div class="nav-item"><a href="view_internships.php" class="active"><i class="bi bi-briefcase-fill"></i> Internships</a></div>
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
        <div class="topbar-title"><i class="bi bi-briefcase-fill me-2" style="color:var(--accent)"></i>Internship <span>Applications</span></div>
        <div class="admin-chip">
            <div class="avatar">A</div>
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </header>

    <main class="page-content">

        <!-- Alerts -->
        <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-up">
            <i class="bi bi-check-circle me-2"></i><?php echo $success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show fade-up">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning alert-dismissible fade show fade-up">
            <i class="bi bi-trash me-2"></i>Application deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <a href="?filter=all" class="stat-card fade-up d1 <?php echo $filter==='all'?'active-filter':''; ?>">
                <div class="stat-icon icon-blue"><i class="bi bi-briefcase-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $total; ?></div><div class="label">Total</div></div>
            </a>
            <a href="?filter=pending" class="stat-card fade-up d2 <?php echo $filter==='pending'?'active-filter':''; ?>">
                <div class="stat-icon icon-yellow"><i class="bi bi-clock-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $pending; ?></div><div class="label">Pending</div></div>
            </a>
            <a href="?filter=approved" class="stat-card fade-up d3 <?php echo $filter==='approved'?'active-filter':''; ?>">
                <div class="stat-icon icon-green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $approved; ?></div><div class="label">Approved</div></div>
            </a>
            <a href="?filter=rejected" class="stat-card fade-up d4 <?php echo $filter==='rejected'?'active-filter':''; ?>">
                <div class="stat-icon icon-red"><i class="bi bi-x-circle-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $rejected; ?></div><div class="label">Rejected</div></div>
            </a>
        </div>

        <!-- Table Card -->
        <div class="page-card fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-ul me-2" style="color:var(--accent)"></i>
                    <?php
                        if ($filter==='pending')  echo 'Pending Applications';
                        elseif ($filter==='approved') echo 'Approved Applications';
                        elseif ($filter==='rejected') echo 'Rejected Applications';
                        else echo 'All Applications';
                    ?>
                </h5>
                <span class="badge bg-success rounded-pill"><?php echo count($applications); ?> Records</span>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="?filter=all"      class="filter-tab <?php echo $filter==='all'?'active':''; ?>">All (<?php echo $total; ?>)</a>
                <a href="?filter=pending"  class="filter-tab <?php echo $filter==='pending'?'active':''; ?>">⏳ Pending (<?php echo $pending; ?>)</a>
                <a href="?filter=approved" class="filter-tab <?php echo $filter==='approved'?'active':''; ?>">✅ Approved (<?php echo $approved; ?>)</a>
                <a href="?filter=rejected" class="filter-tab <?php echo $filter==='rejected'?'active':''; ?>">❌ Rejected (<?php echo $rejected; ?>)</a>
            </div>

            <?php if (count($applications) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Phone</th>
                            <th>College</th>
                            <th>Course / Year</th>
                            <th>Area</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Applied On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $i => $row):
                            $status = $row['status'] ?? 'pending';
                            $statusIcon = $status==='approved' ? 'bi-check-circle-fill' : ($status==='rejected' ? 'bi-x-circle-fill' : 'bi-clock-fill');
                            // Support both full_name and name column
                            $fullName = $row['full_name'] ?? $row['name'] ?? 'N/A';
                            // Support both applied_at and created_at column
                            $appliedAt = $row['applied_at'] ?? $row['created_at'] ?? null;
                        ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="i-avatar"><?php echo strtoupper(substr($fullName, 0, 1)); ?></div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($fullName); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><i class="bi bi-telephone me-1 text-muted"></i><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['college']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['course']); ?><br>
                                <small class="text-muted">Year <?php echo htmlspecialchars($row['year']); ?></small>
                            </td>
                            <td><span class="badge-area"><?php echo htmlspecialchars($row['area']); ?></span></td>
                            <td><span class="badge-duration"><?php echo htmlspecialchars($row['duration']); ?></span></td>
                            <td>
                                <span class="status-badge status-<?php echo $status; ?>">
                                    <i class="bi <?php echo $statusIcon; ?>"></i>
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td><small><?php echo $appliedAt ? date('d M Y', strtotime($appliedAt)) : 'N/A'; ?></small></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#viewModal<?php echo $row['id']; ?>"
                                        title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if ($status !== 'approved'): ?>
                                <a href="?approve=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                                   class="btn btn-sm btn-outline-success me-1"
                                   onclick="return confirm('Approve this application?')"
                                   title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($status !== 'rejected'): ?>
                                <a href="?reject=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                                   class="btn btn-sm btn-outline-warning me-1"
                                   onclick="return confirm('Reject this application?')"
                                   title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Permanently delete this application?')"
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
                <i class="bi bi-briefcase"></i>
                <h5 class="text-muted">No applications found</h5>
                <p class="text-muted small">No internship applications in this category.</p>
                <a href="view_internships.php" class="btn btn-success btn-sm mt-2">
                    <i class="bi bi-arrow-left me-1"></i>View All
                </a>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- ═══════════════════════════════════════════════════
     MODALS — outside table & main-wrap
════════════════════════════════════════════════════ -->
<?php foreach ($applications as $row):
    $status   = $row['status'] ?? 'pending';
    $fullName = $row['full_name'] ?? $row['name'] ?? 'N/A';
    $appliedAt = $row['applied_at'] ?? $row['created_at'] ?? null;
?>
<div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge-fill me-2"></i>
                    <?php echo htmlspecialchars($fullName); ?>'s Application
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="i-avatar i-avatar-lg mx-auto"><?php echo strtoupper(substr($fullName, 0, 1)); ?></div>
                    <h5 class="mt-3 mb-1"><?php echo htmlspecialchars($fullName); ?></h5>
                    <span class="status-badge status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-envelope-fill me-2"></i>Email</span>
                    <span><?php echo htmlspecialchars($row['email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-telephone-fill me-2"></i>Phone</span>
                    <span><?php echo htmlspecialchars($row['phone']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-building-fill me-2"></i>College</span>
                    <span><?php echo htmlspecialchars($row['college']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-book-fill me-2"></i>Course</span>
                    <span><?php echo htmlspecialchars($row['course']); ?> — Year <?php echo htmlspecialchars($row['year']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-stars me-2"></i>Area</span>
                    <span class="badge-area"><?php echo htmlspecialchars($row['area']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-clock-fill me-2"></i>Duration</span>
                    <span class="badge-duration"><?php echo htmlspecialchars($row['duration']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="bi bi-calendar-check me-2"></i>Applied On</span>
                    <span><?php echo $appliedAt ? date('d M Y, h:i A', strtotime($appliedAt)) : 'N/A'; ?></span>
                </div>
                <?php if (!empty($row['message'])): ?>
                <div class="mt-3" style="background:#f6fbf7; border-radius:10px; padding:14px; border-left:3px solid var(--accent);">
                    <small class="fw-semibold text-muted d-block mb-1"><i class="bi bi-chat-left-text me-1"></i>Motivation</small>
                    <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 gap-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <?php if ($status !== 'approved'): ?>
                <a href="?approve=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                   class="btn btn-success btn-sm"
                   onclick="return confirm('Approve this application?')">
                    <i class="bi bi-check-lg me-1"></i>Approve
                </a>
                <?php endif; ?>
                <?php if ($status !== 'rejected'): ?>
                <a href="?reject=<?php echo $row['id']; ?>&filter=<?php echo $filter; ?>"
                   class="btn btn-warning btn-sm"
                   onclick="return confirm('Reject this application?')">
                    <i class="bi bi-x-lg me-1"></i>Reject
                </a>
                <?php endif; ?>
                <a href="?delete=<?php echo $row['id']; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this application permanently?')">
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
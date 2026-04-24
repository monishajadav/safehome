<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('../includes/db_connect.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function safe_count($conn, $sql) {
    $r = mysqli_query($conn, $sql);
    if (!$r) return 0;
    $row = mysqli_fetch_assoc($r);
    mysqli_free_result($r);
    return (int)($row['cnt'] ?? 0);
}

function get_date_col($conn, $table, $candidates) {
    foreach ($candidates as $col) {
        $r = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$col'");
        if ($r && mysqli_num_rows($r) > 0) { mysqli_free_result($r); return $col; }
    }
    return null;
}

// ── Stats ─────────────────────────────────────────────────────────────────
$total_users       = safe_count($conn, "SELECT COUNT(*) AS cnt FROM users");
$total_donations   = safe_count($conn, "SELECT COUNT(*) AS cnt FROM donations");
$total_volunteers  = safe_count($conn, "SELECT COUNT(*) AS cnt FROM volunteer_applications");
$total_messages    = safe_count($conn, "SELECT COUNT(*) AS cnt FROM contact_messages");
$pending_donations = safe_count($conn, "SELECT COUNT(*) AS cnt FROM donations WHERE LOWER(status)='pending'");

$r_amt = mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) AS total FROM donations WHERE LOWER(status)='completed'");
$total_amount = (float)(mysqli_fetch_assoc($r_amt)['total'] ?? 0);
mysqli_free_result($r_amt);

// ── Detect volunteer date column ─────────────────────────────────────────
$vol_date_col  = get_date_col($conn, 'volunteer_applications', ['created_at','submitted_at','applied_at','date','registered_at']);
$vol_order_col = $vol_date_col ?? 'id';

// ── Recent Donations ──────────────────────────────────────────────────────  ← BUG 1 FIXED (semicolon + $dq)
$recent_donations = [];
$dq = mysqli_query($conn, 'SELECT * FROM donations ORDER BY created_at DESC LIMIT 5');
if ($dq) { while ($row = mysqli_fetch_assoc($dq)) $recent_donations[] = $row; mysqli_free_result($dq); }

// ── Recent Volunteers ─────────────────────────────────────────────────────
$recent_volunteers = [];
$vq = mysqli_query($conn, "SELECT * FROM volunteer_applications ORDER BY $vol_order_col DESC LIMIT 5");
if ($vq) { while ($row = mysqli_fetch_assoc($vq)) $recent_volunteers[] = $row; mysqli_free_result($vq); }

// ── Recent Messages ───────────────────────────────────────────────────────
$recent_messages = [];
$msg_date_col = get_date_col($conn, 'contact_messages', ['created_at','submitted_at','sent_at','date']);
$msg_order    = $msg_date_col ? "$msg_date_col DESC" : "id DESC";
$mq = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY $msg_order LIMIT 5");
if ($mq) { while ($row = mysqli_fetch_assoc($mq)) $recent_messages[] = $row; mysqli_free_result($mq); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — SafeHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
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
        .sidebar-brand span { color: var(--accent); }
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

        /* ── Welcome banner ── */
        .welcome-banner { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #40916c 100%); border-radius: var(--radius); padding: 28px 32px; color: #fff; display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; position: relative; overflow: hidden; }
        .welcome-banner::before { content: ''; position: absolute; top: -40px; right: -40px; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.06); }
        .welcome-banner h3 { font-size: 1.4rem; font-weight: 800; margin-bottom: 4px; }
        .welcome-banner p { opacity: 0.75; font-size: 0.88rem; margin: 0; }
        .welcome-banner .date-pill { background: rgba(255,255,255,0.15); padding: 8px 18px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; white-space: nowrap; }

        /* ── Stats ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 18px; margin-bottom: 28px; }
        .stat-card { background: var(--card); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; align-items: center; gap: 16px; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(26,71,42,0.12); }
        .stat-icon { width: 52px; height: 52px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-info .value { font-size: 1.7rem; font-weight: 800; line-height: 1; }
        .stat-info .label { font-size: 0.78rem; color: var(--muted); font-weight: 500; margin-top: 4px; }
        .stat-info .sub   { font-size: 0.75rem; font-weight: 600; margin-top: 3px; }
        .icon-green  { background: #d8f3dc; color: #1a472a; }
        .icon-blue   { background: #dbeafe; color: #1d4ed8; }
        .icon-gold   { background: #fef3c7; color: #92400e; }
        .icon-purple { background: #ede9fe; color: #5b21b6; }
        .icon-red    { background: #fee2e2; color: #991b1b; }

        /* ── Table card ── */
        .table-card { background: var(--card); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }
        .table-card h5 { font-size: 0.95rem; font-weight: 700; }
        .table-card h5 i { color: var(--accent); }
        .table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); border-bottom: 2px solid var(--border); padding: 10px 12px; background: transparent; }
        .table tbody td { padding: 11px 12px; font-size: 0.855rem; vertical-align: middle; border-color: var(--border); }
        .table tbody tr:hover { background: #f6fbf7; }

        .badge-pill    { display: inline-flex; align-items: center; gap: 4px; padding: 3px 11px; border-radius: 50px; font-size: 0.76rem; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .amount-pill   { background: linear-gradient(135deg,#e8f5e9,#c8e6c9); color: #1a472a; padding: 4px 11px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; font-family: 'DM Mono', monospace; }

        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up  { animation: fadeUp 0.45s ease both; }
        .delay-1  { animation-delay: .05s; } .delay-2 { animation-delay: .10s; }
        .delay-3  { animation-delay: .15s; } .delay-4 { animation-delay: .20s; }
        .delay-5  { animation-delay: .25s; }
    </style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h4><i class="bi bi-house-heart-fill me-2"></i>Safe<span>Home</span></h4>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <div class="nav-item"><a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-label mt-2">Management</div>
        <div class="nav-item"><a href="view_users.php"><i class="bi bi-people-fill"></i> Users</a></div>
        <div class="nav-item"><a href="view_volunteers.php"><i class="bi bi-person-check-fill"></i> Volunteers</a></div>
        <div class="nav-item"><a href="view_donations.php"><i class="bi bi-cash-coin"></i> Donations</a></div>
        <div class="nav-item"><a href="view_messages.php"><i class="bi bi-chat-dots-fill"></i> Messages</a></div>
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

<!-- ═══ MAIN ═══ -->
<div class="main-wrap">

    <header class="topbar">
        <div class="topbar-title">Dashboard <span>Overview</span></div>
        <div class="admin-chip">
            <div class="avatar">A</div>
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </header>

    <main class="page-content">

        <!-- Welcome Banner -->
        <div class="welcome-banner fade-up">
            <div>
                <h3>Welcome back, Admin 👋</h3>
                <p>Here's what's happening at SafeHome today.</p>
            </div>
            <div class="date-pill">
                <i class="bi bi-calendar3 me-2"></i><?php echo date('D, d M Y'); ?>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card fade-up delay-1">
                <div class="stat-icon icon-blue"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total_users; ?></div>
                    <div class="label">Total Users</div>
                    <div class="sub" style="color:#1d4ed8;"><i class="bi bi-arrow-up-short"></i> Registered</div>
                </div>
            </div>
            <div class="stat-card fade-up delay-2">
                <div class="stat-icon icon-gold"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total_donations; ?></div>
                    <div class="label">Total Donations</div>
                    <div class="sub" style="color:#92400e;">₹<?php echo number_format($total_amount, 0); ?> collected</div>
                </div>
            </div>
            <div class="stat-card fade-up delay-3">
                <div class="stat-icon icon-green"><i class="bi bi-person-check-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total_volunteers; ?></div>
                    <div class="label">Volunteers</div>
                    <div class="sub" style="color:#1a472a;"><i class="bi bi-heart-fill"></i> Applications</div>
                </div>
            </div>
            <div class="stat-card fade-up delay-4">
                <div class="stat-icon icon-purple"><i class="bi bi-chat-dots-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total_messages; ?></div>
                    <div class="label">Messages</div>
                    <div class="sub" style="color:#5b21b6;"><i class="bi bi-envelope"></i> Contact</div>
                </div>
            </div>
            <div class="stat-card fade-up delay-5">
                <div class="stat-icon icon-red"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $pending_donations; ?></div>
                    <div class="label">Pending Donations</div>
                    <div class="sub" style="color:#991b1b;">Needs attention</div>
                </div>
            </div>
        </div>

        <!-- Recent Donations + Recent Volunteers -->
        <div class="two-col">

            <!-- Recent Donations -->
            <div class="table-card fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Recent Donations</h5>
                    <a href="view_donations.php" class="btn btn-sm btn-success">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Donor</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php if (count($recent_donations) > 0): $i = 1; foreach ($recent_donations as $row):
                            $status = strtolower(trim($row['status'] ?? 'pending'));
                            $cls  = $status === 'completed' ? 'badge-success' : 'badge-pending';
                            $icon = $status === 'completed' ? 'bi-check-circle-fill' : 'bi-clock-fill';
                            $lbl  = $status === 'completed' ? 'Completed' : 'Pending';
                            // ← BUG 2 FIXED: was 'donated_at', correct column is 'created_at'
                            $date = !empty($row['created_at']) ? date('d M Y', strtotime($row['created_at'])) : 'N/A';
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($row['donation_type'] ?? ''); ?></small>
                            </td>
                            <td><span class="amount-pill">₹<?php echo number_format($row['amount'], 2); ?></span></td>
                            <td><span class="badge-pill <?php echo $cls; ?>"><i class="bi <?php echo $icon; ?>"></i> <?php echo $lbl; ?></span></td>
                            <td><?php echo $date; ?></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-3"><i class="bi bi-inbox d-block fs-4 mb-1"></i>No donations yet</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Volunteers -->
            <div class="table-card fade-up">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="bi bi-person-check-fill me-2"></i>Recent Volunteers</h5>
                    <a href="view_volunteers.php" class="btn btn-sm btn-success">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                        <?php if (count($recent_volunteers) > 0): $vi = 1; foreach ($recent_volunteers as $vol):
                            $vname = $vol['full_name'] ?? $vol['name'] ?? $vol['volunteer_name'] ?? 'N/A';
                            $vdate = 'N/A';
                            if ($vol_date_col && !empty($vol[$vol_date_col])) {
                                $vdate = date('d M Y', strtotime($vol[$vol_date_col]));
                            }
                        ?>
                        <tr>
                            <td><?php echo $vi++; ?></td>
                            <td><strong><?php echo htmlspecialchars($vname); ?></strong></td>
                            <td><small><?php echo htmlspecialchars($vol['email'] ?? ''); ?></small></td>
                            <td><small><?php echo $vdate; ?></small></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-3"><i class="bi bi-inbox d-block fs-4 mb-1"></i>No volunteers yet</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Recent Messages -->
        <div class="table-card fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-chat-dots-fill me-2"></i>Recent Messages</h5>
                <a href="view_messages.php" class="btn btn-sm btn-success">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr><th>#</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                    <?php if (count($recent_messages) > 0): $mi = 1; foreach ($recent_messages as $msg):
                        $mname = $msg['name'] ?? $msg['full_name'] ?? 'N/A';
                        $mdate = 'N/A';
                        if ($msg_date_col && !empty($msg[$msg_date_col])) {
                            $mdate = date('d M Y', strtotime($msg[$msg_date_col]));
                        }
                    ?>
                    <tr>
                        <td><?php echo $mi++; ?></td>
                        <td><strong><?php echo htmlspecialchars($mname); ?></strong></td>
                        <td><small><?php echo htmlspecialchars($msg['email'] ?? ''); ?></small></td>
                        <td>
                            <span title="<?php echo htmlspecialchars($msg['message'] ?? ''); ?>">
                                <?php echo htmlspecialchars(mb_strimwidth($msg['message'] ?? '', 0, 60, '…')); ?>
                            </span>
                        </td>
                        <td><small><?php echo $mdate; ?></small></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center text-muted py-3"><i class="bi bi-inbox d-block fs-4 mb-1"></i>No messages yet</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
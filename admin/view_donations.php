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

// ── Secure Delete ──────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id   = (int) $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM donations WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: view_donations.php?deleted=1");
    exit();
}

// ── Column existence checks ────────────────────────────────────────────────
$has_payment_id     = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM donations LIKE 'payment_id'"))     > 0;
$has_payment_status = mysqli_num_rows(mysqli_query($conn, "SHOW COLUMNS FROM donations LIKE 'payment_status'")) > 0;

// ── Fetch all donations ── BUG FIXED: donated_at → created_at ─────────────
$donations_data = [];
$res = mysqli_query($conn, "SELECT * FROM donations ORDER BY created_at DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $donations_data[] = $row;
    }
    mysqli_free_result($res);
}

$total_records = count($donations_data);

// ── Aggregates ─────────────────────────────────────────────────────────────
$r_total      = mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) AS total FROM donations");
$total_amount = (float)(mysqli_fetch_assoc($r_total)['total'] ?? 0);
mysqli_free_result($r_total);

$success_count = $total_records;
if ($has_payment_status) {
    $r_succ        = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donations WHERE payment_status='Success'");
    $success_count = (int)(mysqli_fetch_assoc($r_succ)['total'] ?? 0);
    mysqli_free_result($r_succ);
}

$avg_donation = $total_records > 0 ? $total_amount / $total_records : 0;

// ── Search / Filter ────────────────────────────────────────────────────────
$search      = trim($_GET['search']      ?? '');
$filter_type = trim($_GET['filter_type'] ?? '');

$filtered = array_filter($donations_data, function ($row) use ($search, $filter_type) {
    $match_search = $search === '' ||
        stripos($row['full_name'],  $search) !== false ||
        stripos($row['email'],      $search) !== false ||
        stripos($row['phone'],      $search) !== false ||
        (isset($row['payment_id']) && stripos($row['payment_id'], $search) !== false);
    $match_type = $filter_type === '' || $row['donation_type'] === $filter_type;
    return $match_search && $match_type;
});
$filtered = array_values($filtered);

$donation_types = array_values(array_unique(array_column($donations_data, 'donation_type')));
sort($donation_types);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations — SafeHome Admin</title>
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

        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

        .topbar { height: var(--topbar-h); background: var(--card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 28px; position: sticky; top: 0; z-index: 50; }
        .topbar-title { font-size: 1.15rem; font-weight: 700; }
        .topbar-title span { color: var(--accent); }
        .admin-chip { display: flex; align-items: center; gap: 8px; background: var(--accent-soft); padding: 6px 14px 6px 8px; border-radius: 50px; font-size: 0.82rem; font-weight: 600; color: var(--primary); }
        .admin-chip .avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; }

        .page-content { padding: 28px; flex: 1; }

        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; margin-bottom: 24px; }
        .stat-card { background: var(--card); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; align-items: center; gap: 16px; transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(26,71,42,0.12); }
        .stat-icon { width: 52px; height: 52px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0; }
        .stat-info .value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
        .stat-info .label { font-size: 0.78rem; color: var(--muted); font-weight: 500; margin-top: 4px; }
        .icon-blue   { background: #dbeafe; color: #1d4ed8; }
        .icon-green  { background: #d8f3dc; color: #1a472a; }
        .icon-gold   { background: #fef3c7; color: #92400e; }
        .icon-purple { background: #ede9fe; color: #6d28d9; }

        .page-card { background: var(--card); border-radius: var(--radius); padding: 22px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }

        .filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        .filter-bar input, .filter-bar select { border: 1px solid var(--border); border-radius: 8px; padding: 7px 14px; font-size: 0.85rem; font-family: inherit; color: var(--text); background: var(--bg); outline: none; transition: border-color 0.2s; }
        .filter-bar input:focus, .filter-bar select:focus { border-color: var(--accent); }
        .filter-bar input { min-width: 220px; }
        .btn-clear { background: none; border: 1px solid var(--border); border-radius: 8px; padding: 7px 14px; font-size: 0.82rem; cursor: pointer; color: var(--muted); transition: all 0.2s; font-family: inherit; }
        .btn-clear:hover { border-color: #e74c3c; color: #e74c3c; }

        .table thead th { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); border-bottom: 2px solid var(--border); padding: 10px 14px; background: transparent; }
        .table tbody td { padding: 12px 14px; font-size: 0.86rem; vertical-align: middle; border-color: var(--border); }
        .table tbody tr:hover { background: #f6fbf7; }

        .amount-badge  { background: var(--accent-soft); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
        .type-badge    { background: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .status-success{ background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .status-pending{ background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-date    { background: var(--accent-soft); color: var(--primary); padding: 4px 11px; border-radius: 20px; font-size: 0.76rem; font-weight: 600; }
        .payment-id    { font-family: monospace; font-size: 0.78rem; background: #f1f5f9; padding: 3px 8px; border-radius: 6px; color: #475569; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: inline-block; }

        .u-avatar    { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; flex-shrink: 0; }
        .u-avatar-lg { width: 72px; height: 72px; font-size: 1.8rem; }

        .modal-header  { background: var(--primary); color: #fff; border-radius: 12px 12px 0 0; }
        .modal-content { border-radius: 12px; border: none; }
        .info-row td   { padding: 10px 12px; font-size: 0.875rem; border-color: var(--border); }
        .info-row td:first-child { color: var(--muted); width: 40%; }

        .empty-state   { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 3.5rem; color: var(--accent); opacity: 0.4; display: block; margin-bottom: 14px; }

        .btn-export { background: var(--primary); color: #fff; border: none; border-radius: 8px; padding: 7px 18px; font-size: 0.82rem; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
        .btn-export:hover { background: var(--primary-light); }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp 0.4s ease both; }
        .d1 { animation-delay: .05s; } .d2 { animation-delay: .1s; } .d3 { animation-delay: .15s; } .d4 { animation-delay: .2s; }
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
        <div class="nav-item"><a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></div>
        <div class="nav-label mt-2">Management</div>
        <div class="nav-item"><a href="view_users.php"><i class="bi bi-people-fill"></i> Users</a></div>
        <div class="nav-item"><a href="view_volunteers.php"><i class="bi bi-person-check-fill"></i> Volunteers</a></div>
        <div class="nav-item"><a href="view_donations.php" class="active"><i class="bi bi-cash-coin"></i> Donations</a></div>
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
        <div class="topbar-title"><i class="bi bi-cash-coin me-2" style="color:var(--accent)"></i>Donation <span>Records</span></div>
        <div class="admin-chip">
            <div class="avatar">A</div>
            <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
        </div>
    </header>

    <main class="page-content">

        <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning alert-dismissible fade show fade-up">
            <i class="bi bi-trash me-2"></i>Donation deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card fade-up d1">
                <div class="stat-icon icon-green"><i class="bi bi-currency-rupee"></i></div>
                <div class="stat-info">
                    <div class="value">₹<?php echo number_format($total_amount, 0); ?></div>
                    <div class="label">Total Collected</div>
                </div>
            </div>
            <div class="stat-card fade-up d2">
                <div class="stat-icon icon-blue"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $total_records; ?></div>
                    <div class="label">Total Donors</div>
                </div>
            </div>
            <div class="stat-card fade-up d3">
                <div class="stat-icon icon-gold"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-info">
                    <div class="value"><?php echo $success_count; ?></div>
                    <div class="label">Successful Payments</div>
                </div>
            </div>
            <div class="stat-card fade-up d4">
                <div class="stat-icon icon-purple"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-info">
                    <div class="value">₹<?php echo number_format($avg_donation, 0); ?></div>
                    <div class="label">Average Donation</div>
                </div>
            </div>
        </div>

        <!-- Table card -->
        <div class="page-card fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="mb-0 fw-bold"><i class="bi bi-list-ul me-2" style="color:var(--accent)"></i>All Donations</h5>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill"><?php echo count($filtered); ?> / <?php echo $total_records; ?> Records</span>
                    <button class="btn-export" onclick="exportCSV()"><i class="bi bi-download"></i> Export CSV</button>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="filter-bar mb-3">
                <input type="text" id="searchInput" placeholder="Search name, email, phone, payment ID…"
                    value="<?php echo htmlspecialchars($search); ?>"
                    oninput="liveFilter()">
                <select id="typeFilter" onchange="liveFilter()">
                    <option value="">All Types</option>
                    <?php foreach ($donation_types as $dt): ?>
                    <option value="<?php echo htmlspecialchars($dt); ?>"
                        <?php echo ($filter_type === $dt) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dt); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn-clear" onclick="clearFilter()"><i class="bi bi-x-circle me-1"></i>Clear</button>
            </div>

            <?php if (count($donations_data) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="donationsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Donor</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Donated For</th>
                            <th>Payment</th>
                            <?php if ($has_payment_id): ?><th>Payment ID</th><?php endif; ?>
                            <?php if ($has_payment_status): ?><th>Status</th><?php endif; ?>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                    <?php foreach ($donations_data as $i => $row): ?>
                        <tr
                            data-name="<?php echo strtolower(htmlspecialchars($row['full_name'])); ?>"
                            data-email="<?php echo strtolower(htmlspecialchars($row['email'])); ?>"
                            data-phone="<?php echo htmlspecialchars($row['phone']); ?>"
                            data-payid="<?php echo strtolower(htmlspecialchars($row['payment_id'] ?? '')); ?>"
                            data-type="<?php echo htmlspecialchars($row['donation_type']); ?>">
                            <td class="row-num"><?php echo $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="u-avatar"><?php echo strtoupper(substr($row['full_name'], 0, 1)); ?></div>
                                    <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                </div>
                            </td>
                            <td><i class="bi bi-envelope me-1 text-muted"></i><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><i class="bi bi-telephone me-1 text-muted"></i><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><span class="amount-badge">₹<?php echo number_format($row['amount'], 2); ?></span></td>
                            <td><span class="type-badge"><?php echo htmlspecialchars($row['donation_type']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <?php if ($has_payment_id): ?>
                            <td>
                                <?php if (!empty($row['payment_id'])): ?>
                                    <span class="payment-id" title="<?php echo htmlspecialchars($row['payment_id']); ?>">
                                        <?php echo htmlspecialchars($row['payment_id']); ?>
                                    </span>
                                <?php else: ?><span class="text-muted">—</span><?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <?php if ($has_payment_status): ?>
                            <td>
                                <?php if ($row['payment_status'] === 'Success'): ?>
                                    <span class="status-success"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                                <?php else: ?>
                                    <span class="status-pending"><i class="bi bi-clock-fill me-1"></i><?php echo htmlspecialchars($row['payment_status']); ?></span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <!-- BUG FIXED: donated_at → created_at (table row date) -->
                            <td><span class="badge-date"><i class="bi bi-calendar me-1"></i><?php echo date('d M Y', strtotime($row['created_at'])); ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewModal<?php echo $row['id']; ?>"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete this donation record?')"
                                    title="Delete">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="noResults" class="empty-state d-none">
                <i class="bi bi-search"></i>
                <h5 class="text-muted">No matching donations</h5>
                <p class="text-muted small">Try a different search term or clear the filters.</p>
            </div>

            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-cash-coin"></i>
                <h5 class="text-muted">No donations yet</h5>
                <p class="text-muted small">Donations will appear here once received.</p>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- ═══ MODALS ═══ -->
<?php foreach ($donations_data as $row): ?>
<div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Donation Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="u-avatar u-avatar-lg mx-auto">
                        <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                    </div>
                    <h5 class="mt-3 mb-0"><?php echo htmlspecialchars($row['full_name']); ?></h5>
                    <small class="text-muted">Donation ID #<?php echo $row['id']; ?></small>
                </div>
                <table class="table table-borderless info-row mb-0">
                    <tr><td><i class="bi bi-envelope me-2 text-muted"></i>Email</td><td><strong><?php echo htmlspecialchars($row['email']); ?></strong></td></tr>
                    <tr><td><i class="bi bi-telephone me-2 text-muted"></i>Phone</td><td><strong><?php echo htmlspecialchars($row['phone']); ?></strong></td></tr>
                    <tr><td><i class="bi bi-currency-rupee me-2 text-muted"></i>Amount</td><td><span class="amount-badge">₹<?php echo number_format($row['amount'], 2); ?></span></td></tr>
                    <tr><td><i class="bi bi-heart me-2 text-muted"></i>Donated For</td><td><span class="type-badge"><?php echo htmlspecialchars($row['donation_type']); ?></span></td></tr>
                    <tr><td><i class="bi bi-credit-card me-2 text-muted"></i>Payment Method</td><td><strong><?php echo htmlspecialchars($row['payment_method']); ?></strong></td></tr>
                    <?php if ($has_payment_id && !empty($row['payment_id'])): ?>
                    <tr><td><i class="bi bi-shield-check me-2 text-muted"></i>Payment ID</td><td><span class="payment-id"><?php echo htmlspecialchars($row['payment_id']); ?></span></td></tr>
                    <?php endif; ?>
                    <?php if ($has_payment_status): ?>
                    <tr>
                        <td><i class="bi bi-check-circle me-2 text-muted"></i>Status</td>
                        <td>
                            <?php if ($row['payment_status'] === 'Success'): ?>
                                <span class="status-success"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                            <?php else: ?>
                                <span class="status-pending"><?php echo htmlspecialchars($row['payment_status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($row['message'])): ?>
                    <tr><td><i class="bi bi-chat me-2 text-muted"></i>Message</td><td><em><?php echo nl2br(htmlspecialchars($row['message'])); ?></em></td></tr>
                    <?php endif; ?>
                    <!-- BUG FIXED: donated_at → created_at (modal date) -->
                    <tr><td><i class="bi bi-calendar-check me-2 text-muted"></i>Donated On</td><td><strong><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></strong></td></tr>
                </table>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <a href="?delete=<?php echo $row['id']; ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this donation?')">
                    <i class="bi bi-trash me-1"></i>Delete
                </a>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function liveFilter() {
    const search = document.getElementById('searchInput').value.toLowerCase().trim();
    const type   = document.getElementById('typeFilter').value;
    const rows   = document.querySelectorAll('#tableBody tr');
    let visible  = 0;

    rows.forEach(row => {
        const matchSearch = !search ||
            row.dataset.name.includes(search)  ||
            row.dataset.email.includes(search) ||
            row.dataset.phone.includes(search) ||
            row.dataset.payid.includes(search);
        const matchType = !type || row.dataset.type === type;

        if (matchSearch && matchType) {
            row.style.display = '';
            visible++;
        } else {
            row.style.display = 'none';
        }
    });

    let n = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') row.querySelector('.row-num').textContent = n++;
    });

    document.getElementById('noResults').classList.toggle('d-none', visible > 0);
}

function clearFilter() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value  = '';
    liveFilter();
}

function exportCSV() {
    const rows    = document.querySelectorAll('#donationsTable tr');
    const csvRows = [];

    rows.forEach(row => {
        if (row.style.display === 'none') return;
        const cols = row.querySelectorAll('th, td');
        const data = Array.from(cols).slice(0, -1)
            .map(td => {
                let text = td.innerText.replace(/\s+/g, ' ').trim();
                return '"' + text.replace(/"/g, '""') + '"';
            });
        csvRows.push(data.join(','));
    });

    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'donations_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
}
</script>
</body>
</html>
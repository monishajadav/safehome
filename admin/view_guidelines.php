<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include('../includes/db_connect.php');
$success = ""; $error = "";

// ── Add ───────────────────────────────────────────────────────────────────────
if (isset($_POST['add'])) {
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $title    = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content  = mysqli_real_escape_string($conn, trim($_POST['content']));
    $icon     = mysqli_real_escape_string($conn, trim($_POST['icon']));
    $order    = (int)$_POST['order'];
    if (empty($category) || empty($title) || empty($content)) { $error = "Please fill all required fields."; }
    else {
        $sql = "INSERT INTO guidelines (category,guideline_title,guideline_content,icon,display_order) VALUES ('$category','$title','$content','$icon','$order')";
        if (mysqli_query($conn, $sql)) $success = "Guideline added successfully!";
        else $error = "Database error: ".mysqli_error($conn);
    }
}

// ── Update ────────────────────────────────────────────────────────────────────
if (isset($_POST['update'])) {
    $id       = (int)$_POST['id'];
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $title    = mysqli_real_escape_string($conn, trim($_POST['title']));
    $content  = mysqli_real_escape_string($conn, trim($_POST['content']));
    $icon     = mysqli_real_escape_string($conn, trim($_POST['icon']));
    $order    = (int)$_POST['order'];
    $sql = "UPDATE guidelines SET category='$category',guideline_title='$title',guideline_content='$content',icon='$icon',display_order='$order' WHERE id=$id";
    if (mysqli_query($conn, $sql)) $success = "Guideline updated!";
    else $error = "Database error: ".mysqli_error($conn);
}

// ── Toggle ────────────────────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE guidelines SET is_active = NOT is_active WHERE id=$id");
    header("Location: view_guidelines.php?toggled=1"); exit();
}

// ── Delete ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM guidelines WHERE id=$id");
    header("Location: view_guidelines.php?deleted=1"); exit();
}

// ── Fetch ─────────────────────────────────────────────────────────────────────
$result     = mysqli_query($conn, "SELECT * FROM guidelines ORDER BY category ASC, display_order ASC");
$guidelines = [];
while ($row = mysqli_fetch_assoc($result)) $guidelines[] = $row;
$total = count($guidelines);
$active_count   = count(array_filter($guidelines, fn($g) => $g['is_active']));
$inactive_count = $total - $active_count;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Guidelines — SafeHome Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--primary:#1a472a;--accent:#52b788;--accent-soft:#d8f3dc;--sidebar-w:260px;--topbar-h:68px;--bg:#f0f4f1;--card:#fff;--text:#1b2b1e;--muted:#6b7f70;--border:#dce8df;--radius:14px;--shadow:0 2px 16px rgba(26,71,42,0.08)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-w);height:100vh;background:var(--primary);display:flex;flex-direction:column;z-index:100;overflow-y:auto}
.sidebar-brand{padding:24px 20px 20px;border-bottom:1px solid rgba(255,255,255,0.1)}
.sidebar-brand h4{color:#fff;font-weight:800;font-size:1.25rem}
.sidebar-brand h4 span{color:var(--accent)}
.sidebar-brand small{color:rgba(255,255,255,0.5);font-size:.72rem;font-weight:500;letter-spacing:.8px;text-transform:uppercase}
.sidebar-nav{padding:16px 12px;flex:1}
.nav-label{font-size:.68rem;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:rgba(255,255,255,0.35);padding:10px 10px 6px}
.nav-item a{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;color:rgba(255,255,255,0.75);text-decoration:none;font-size:.875rem;font-weight:500;transition:all .2s;margin-bottom:2px}
.nav-item a:hover{background:rgba(255,255,255,0.12);color:#fff}
.nav-item a.active{background:var(--accent);color:#fff}
.nav-item a i{font-size:1rem;width:20px;text-align:center}
.sidebar-footer{padding:16px 12px;border-top:1px solid rgba(255,255,255,0.1)}
.sidebar-footer a{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;color:rgba(255,255,255,0.6);text-decoration:none;font-size:.875rem;transition:all .2s}
.sidebar-footer a:hover{background:rgba(231,57,70,0.2);color:#ff8a8a}
.main-wrap{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column}
.topbar{height:var(--topbar-h);background:var(--card);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:50}
.topbar-title{font-size:1.15rem;font-weight:700}
.topbar-title span{color:var(--accent)}
.admin-chip{display:flex;align-items:center;gap:8px;background:var(--accent-soft);padding:6px 14px 6px 8px;border-radius:50px;font-size:.82rem;font-weight:600;color:var(--primary)}
.admin-chip .avatar{width:30px;height:30px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700}
.page-content{padding:28px;flex:1}
.page-card{background:var(--card);border-radius:var(--radius);padding:22px;box-shadow:var(--shadow);border:1px solid var(--border);margin-bottom:24px}

/* Stats */
.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:24px}
.stat-card{background:var(--card);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow);border:1px solid var(--border);display:flex;align-items:center;gap:14px}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
.stat-info .value{font-size:1.6rem;font-weight:800;line-height:1}
.stat-info .label{font-size:.76rem;color:var(--muted);font-weight:500;margin-top:3px}
.icon-blue{background:#dbeafe;color:#1d4ed8}
.icon-green{background:#d1fae5;color:#065f46}
.icon-red{background:#fee2e2;color:#991b1b}

/* Guideline cards */
.g-card{background:var(--card);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);border:1px solid var(--border);border-left:5px solid var(--accent);margin-bottom:16px;transition:box-shadow .2s}
.g-card:hover{box-shadow:0 8px 24px rgba(26,71,42,0.12)}
.g-card.inactive{opacity:.6;border-left-color:#ef4444}
.cat-badge{background:var(--accent-soft);color:var(--primary);padding:4px 11px;border-radius:20px;font-size:.75rem;font-weight:700}

/* Form */
.form-control:focus,.form-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(82,183,136,0.15)}

/* Modal */
.modal-header{background:var(--primary);color:#fff;border-radius:12px 12px 0 0}
.modal-content{border-radius:12px;border:none}

.empty-state{text-align:center;padding:60px 20px}
.empty-state i{font-size:3.5rem;color:var(--accent);opacity:.35;display:block;margin-bottom:14px}
@keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
.fade-up{animation:fadeUp .4s ease both}
</style>
</head>
<body>
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
        <div class="nav-item"><a href="view_internships.php"><i class="bi bi-briefcase-fill"></i> Internships</a></div>
        <div class="nav-label mt-2">Content</div>
        <div class="nav-item"><a href="view_gallery.php"><i class="bi bi-images"></i> Gallery</a></div>
        <div class="nav-item"><a href="view_guidelines.php" class="active"><i class="bi bi-journal-text"></i> Guidelines</a></div>
        <div class="nav-item"><a href="view_terms.php"><i class="bi bi-file-earmark-text"></i> Terms</a></div>
    </nav>
    <div class="sidebar-footer"><a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></div>
</aside>

<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-title"><i class="bi bi-journal-text me-2" style="color:var(--accent)"></i>Guidelines <span>Management</span></div>
        <div class="admin-chip"><div class="avatar">A</div><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></div>
    </header>

    <main class="page-content">
        <?php if (!empty($success)): ?><div class="alert alert-success alert-dismissible fade show fade-up"><i class="bi bi-check-circle me-2"></i><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-danger alert-dismissible fade show fade-up"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if (isset($_GET['toggled'])): ?><div class="alert alert-info alert-dismissible fade show fade-up"><i class="bi bi-info-circle me-2"></i>Guideline visibility updated.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?><div class="alert alert-warning alert-dismissible fade show fade-up"><i class="bi bi-trash me-2"></i>Guideline deleted.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card fade-up">
                <div class="stat-icon icon-blue"><i class="bi bi-journal-text"></i></div>
                <div class="stat-info"><div class="value"><?php echo $total; ?></div><div class="label">Total Guidelines</div></div>
            </div>
            <div class="stat-card fade-up">
                <div class="stat-icon icon-green"><i class="bi bi-eye-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $active_count; ?></div><div class="label">Active / Visible</div></div>
            </div>
            <div class="stat-card fade-up">
                <div class="stat-icon icon-red"><i class="bi bi-eye-slash-fill"></i></div>
                <div class="stat-info"><div class="value"><?php echo $inactive_count; ?></div><div class="label">Hidden</div></div>
            </div>
        </div>

        <!-- Add Form -->
        <div class="page-card fade-up" style="border-top:4px solid var(--accent)">
            <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle me-2" style="color:var(--accent)"></i>Add New Guideline</h5>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            <option value="General">General</option>
                            <option value="Volunteer">Volunteer</option>
                            <option value="Donation">Donation</option>
                            <option value="Visitor">Visitor</option>
                            <option value="Child Welfare">Child Welfare</option>
                            <option value="Elder Care">Elder Care</option>
                            <option value="Emergency">Emergency</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Icon Class</label>
                        <input type="text" name="icon" class="form-control" value="bi-info-circle" placeholder="bi-shield-check">
                        <small class="text-muted">Bootstrap Icons class name</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Order <span class="text-danger">*</span></label>
                        <input type="number" name="order" class="form-control" value="<?php echo $total + 1; ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Guideline Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g., Code of Conduct" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Enter the guideline content..." required></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="add" class="btn btn-success px-4">
                            <i class="bi bi-plus-circle me-2"></i>Add Guideline
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Guidelines List -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2" style="color:var(--accent)"></i>All Guidelines</h5>
            <span class="badge bg-success rounded-pill"><?php echo $total; ?> Total</span>
        </div>

        <?php if ($total > 0): ?>
            <?php foreach ($guidelines as $row): ?>
            <div class="g-card fade-up <?php echo $row['is_active'] ? '' : 'inactive'; ?>">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <i class="<?php echo htmlspecialchars($row['icon']); ?> fs-5 text-success"></i>
                            <span class="cat-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                            <span class="badge bg-secondary">#<?php echo $row['display_order']; ?></span>
                            <?php if (!$row['is_active']): ?>
                            <span class="badge bg-danger">Hidden</span>
                            <?php endif; ?>
                        </div>
                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($row['guideline_title']); ?></h6>
                        <p class="text-muted mb-2" style="font-size:.875rem;line-height:1.6">
                            <?php echo htmlspecialchars(mb_strimwidth($row['guideline_content'], 0, 220, '...')); ?>
                        </p>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>Updated: <?php echo date('d M Y, h:i A', strtotime($row['updated_at'])); ?></small>
                    </div>
                    <div class="d-flex gap-2 flex-shrink-0">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?toggle=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-<?php echo $row['is_active'] ? 'warning' : 'success'; ?>" title="<?php echo $row['is_active'] ? 'Hide' : 'Show'; ?>">
                            <i class="bi bi-<?php echo $row['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                        </a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this guideline permanently?')" title="Delete">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="page-card empty-state">
            <i class="bi bi-journal-text"></i>
            <h5 class="text-muted">No guidelines added yet</h5>
            <p class="text-muted small">Use the form above to add your first guideline.</p>
        </div>
        <?php endif; ?>

    </main>
</div>

<!-- ═══════════ EDIT MODALS — outside all loops ═══════════ -->
<?php foreach ($guidelines as $row): ?>
<div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Guideline</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <?php foreach (['General','Volunteer','Donation','Visitor','Child Welfare','Elder Care','Emergency'] as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $row['category']===$cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Icon Class</label>
                            <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($row['icon']); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Order</label>
                            <input type="number" name="order" class="form-control" value="<?php echo $row['display_order']; ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($row['guideline_title']); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="5" required><?php echo htmlspecialchars($row['guideline_content']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update" class="btn btn-success btn-sm">
                        <i class="bi bi-check-circle me-1"></i>Update Guideline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
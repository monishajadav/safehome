<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include('../includes/db_connect.php');

$success = "";
$error   = "";

// ── Upload ────────────────────────────────────────────────────────────────────
if (isset($_POST['upload'])) {
    $title       = mysqli_real_escape_string($conn, trim($_POST['title']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $category    = mysqli_real_escape_string($conn, trim($_POST['category']));

    if (empty($title)) {
        $error = "Please enter a title.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] != 0) {
        $error = "Please select an image.";
    } else {
        $file     = $_FILES['image'];
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $max_size = 5 * 1024 * 1024;

        if (!in_array($ext, $allowed)) {
            $error = "Invalid file. Allowed: JPG, JPEG, PNG, GIF, WEBP";
        } elseif ($file['size'] > $max_size) {
            $error = "File too large. Maximum 5MB.";
        } else {
            $new_name    = time() . '_' . uniqid() . '.' . $ext;
            $upload_path = '../uploads/gallery/' . $new_name;
            $db_path     = 'uploads/gallery/' . $new_name;

            if (!file_exists('../uploads/gallery')) mkdir('../uploads/gallery', 0777, true);

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $sql = "INSERT INTO gallery (title, description, image_path, category) VALUES ('$title','$description','$db_path','$category')";
                if (mysqli_query($conn, $sql)) $success = "Image uploaded successfully!";
                else $error = "Database error: " . mysqli_error($conn);
            } else {
                $error = "Upload failed. Check folder permissions.";
            }
        }
    }
}

// ── Delete ────────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id  = (int)$_GET['delete'];
    $img = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_path FROM gallery WHERE id=$id"));
    if ($img) {
        $fp = '../' . $img['image_path'];
        if (file_exists($fp)) unlink($fp);
        mysqli_query($conn, "DELETE FROM gallery WHERE id=$id");
    }
    header("Location: view_gallery.php?deleted=1");
    exit();
}

// ── Filter by category ────────────────────────────────────────────────────────
$cat_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';
if ($cat_filter !== 'all') {
    $result = mysqli_query($conn, "SELECT * FROM gallery WHERE category='$cat_filter' ORDER BY uploaded_at DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM gallery ORDER BY uploaded_at DESC");
}

$images = [];
while ($row = mysqli_fetch_assoc($result)) $images[] = $row;
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM gallery"))['c'];

// Category counts
$categories = ['General', 'Elder Care', 'Child Welfare', 'Events', 'Volunteer'];
$cat_counts  = [];
foreach ($categories as $c) {
    $cc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM gallery WHERE category='$c'"));
    $cat_counts[$c] = $cc['cnt'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery — SafeHome Admin</title>
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

        /* ── Card ── */
        .page-card { background: var(--card); border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 24px; }

        /* ── Upload form ── */
        .upload-zone { border: 2.5px dashed var(--accent); border-radius: 12px; padding: 28px; text-align: center; background: var(--accent-soft); cursor: pointer; transition: all 0.25s; position: relative; }
        .upload-zone:hover { border-color: var(--primary); background: #c8e6c9; }
        .upload-zone input[type="file"] { position: absolute; opacity: 0; width: 100%; height: 100%; top: 0; left: 0; cursor: pointer; }
        .upload-zone i { font-size: 2.2rem; color: var(--primary); }
        #imagePreview { max-width: 100%; max-height: 180px; border-radius: 10px; display: none; margin-top: 12px; object-fit: cover; border: 2px solid var(--border); }
        .form-control:focus, .form-select:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(82,183,136,0.15); }

        /* ── Filter tabs ── */
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-tab { padding: 6px 16px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; text-decoration: none; border: 1.5px solid var(--border); color: var(--muted); transition: all 0.2s; }
        .filter-tab:hover { border-color: var(--accent); color: var(--primary); }
        .filter-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Gallery grid ── */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 18px; }
        .gallery-item { background: var(--card); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); border: 1px solid var(--border); transition: transform 0.2s, box-shadow 0.2s; }
        .gallery-item:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(26,71,42,0.13); }
        .gallery-item .img-wrap { position: relative; overflow: hidden; height: 160px; }
        .gallery-item .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s; }
        .gallery-item:hover .img-wrap img { transform: scale(1.07); }
        .gallery-item .img-wrap .overlay { position: absolute; inset: 0; background: rgba(26,71,42,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.25s; gap: 10px; }
        .gallery-item:hover .img-wrap .overlay { opacity: 1; }
        .gallery-item-body { padding: 14px; }
        .gallery-item-body h6 { font-weight: 700; font-size: 0.875rem; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .gallery-item-body p { color: var(--muted); font-size: 0.78rem; margin-bottom: 8px; line-height: 1.4; }
        .cat-badge { background: var(--accent-soft); color: var(--primary); padding: 3px 10px; border-radius: 20px; font-size: 0.73rem; font-weight: 600; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 3.5rem; color: var(--accent); opacity: 0.35; display: block; margin-bottom: 14px; }

        /* ── Lightbox modal ── */
        .modal-header { background: var(--primary); color: #fff; border-radius: 12px 12px 0 0; }
        .modal-content { border-radius: 12px; border: none; }
        #lightboxImg { width: 100%; border-radius: 8px; max-height: 60vh; object-fit: contain; }

        /* ── Animations ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp 0.4s ease both; }
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
        <div class="nav-item"><a href="view_internships.php"><i class="bi bi-briefcase-fill"></i> Internships</a></div>
        <div class="nav-label mt-2">Content</div>
        <div class="nav-item"><a href="view_gallery.php" class="active"><i class="bi bi-images"></i> Gallery</a></div>
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
        <div class="topbar-title"><i class="bi bi-images me-2" style="color:var(--accent)"></i>Gallery <span>Management</span></div>
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
            <i class="bi bi-trash me-2"></i>Image deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <div class="page-card fade-up">
            <h5 class="fw-bold mb-4"><i class="bi bi-cloud-upload me-2" style="color:var(--accent)"></i>Upload New Image</h5>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Select Image <span class="text-danger">*</span></label>
                        <div class="upload-zone">
                            <input type="file" name="image" id="imageInput" accept=".jpg,.jpeg,.png,.gif,.webp" onchange="previewImage(this)" required style="display:none;">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <p class="mt-2 mb-0 fw-semibold">Click to upload</p>
                            <small class="text-muted">JPG, PNG, GIF, WEBP — Max 5MB</small>
                        </div>
                        <img id="imagePreview" src="" alt="Preview">
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Image Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Enter a title..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Write a short description..."></textarea>
                        </div>
                        <button type="submit" name="upload" class="btn btn-success px-4">
                            <i class="bi bi-cloud-upload me-2"></i>Upload Image
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Gallery -->
        <div class="page-card fade-up">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-grid me-2" style="color:var(--accent)"></i>All Images</h5>
                <span class="badge bg-success rounded-pill"><?php echo $total; ?> Photos</span>
            </div>

            <!-- Category filter tabs -->
            <div class="filter-tabs">
                <a href="?cat=all" class="filter-tab <?php echo $cat_filter==='all'?'active':''; ?>">All (<?php echo $total; ?>)</a>
                <?php foreach ($categories as $c): ?>
                <a href="?cat=<?php echo urlencode($c); ?>" class="filter-tab <?php echo $cat_filter===$c?'active':''; ?>">
                    <?php echo $c; ?> (<?php echo $cat_counts[$c]; ?>)
                </a>
                <?php endforeach; ?>
            </div>

            <?php if (count($images) > 0): ?>
            <div class="gallery-grid">
                <?php foreach ($images as $row): ?>
                <div class="gallery-item">
                    <div class="img-wrap">
                        <img src="../<?php echo htmlspecialchars($row['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($row['title']); ?>">
                        <div class="overlay">
                            <button class="btn btn-sm btn-light"
                                    onclick="openLightbox('<?php echo htmlspecialchars('../'.$row['image_path']); ?>','<?php echo htmlspecialchars($row['title']); ?>','<?php echo htmlspecialchars($row['description'] ?? ''); ?>','<?php echo htmlspecialchars($row['category']); ?>')"
                                    title="View">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="?delete=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this image permanently?')"
                               title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    <div class="gallery-item-body">
                        <h6 title="<?php echo htmlspecialchars($row['title']); ?>"><?php echo htmlspecialchars($row['title']); ?></h6>
                        <?php if (!empty($row['description'])): ?>
                        <p><?php echo htmlspecialchars(mb_strimwidth($row['description'], 0, 55, '...')); ?></p>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="cat-badge"><?php echo htmlspecialchars($row['category']); ?></span>
                            <small class="text-muted"><?php echo date('d M Y', strtotime($row['uploaded_at'])); ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-images"></i>
                <h5 class="text-muted">No images found</h5>
                <p class="text-muted small">
                    <?php echo $cat_filter !== 'all' ? "No images in the \"$cat_filter\" category." : "Upload your first image above!"; ?>
                </p>
                <?php if ($cat_filter !== 'all'): ?>
                <a href="view_gallery.php" class="btn btn-success btn-sm mt-2"><i class="bi bi-arrow-left me-1"></i>View All</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- ═══════════ LIGHTBOX MODAL ═══════════ -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightboxTitle"><i class="bi bi-image me-2"></i>Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <img id="lightboxImg" src="" alt="">
                <div class="mt-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span id="lightboxCat" class="cat-badge"></span>
                    </div>
                    <p id="lightboxDesc" class="text-muted small mt-2 mb-0"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const p = document.getElementById('imagePreview');
            p.src = e.target.result;
            p.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Make upload zone also trigger file input
document.querySelector('.upload-zone').addEventListener('click', function() {
    document.getElementById('imageInput').click();
});

function openLightbox(src, title, desc, cat) {
    document.getElementById('lightboxImg').src  = src;
    document.getElementById('lightboxTitle').innerHTML = '<i class="bi bi-image me-2"></i>' + title;
    document.getElementById('lightboxDesc').textContent = desc || '';
    document.getElementById('lightboxCat').textContent  = cat;
    new bootstrap.Modal(document.getElementById('lightboxModal')).show();
}
</script>
</body>
</html>
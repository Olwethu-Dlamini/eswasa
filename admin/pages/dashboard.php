<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// ---- Add Statistics Logic Here ----
$msg_stats = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stats'])) {
    if (isset($_POST['stats']) && is_array($_POST['stats'])) {
        $stmt = $conn->prepare("UPDATE site_statistics SET stat_value = ? WHERE id = ?");
        if ($stmt) {
            foreach ($_POST['stats'] as $id => $value) {
                $id = (int)$id;
                $value = (int)$value;
                $stmt->bind_param('ii', $value, $id);
                $stmt->execute();
            }
            $stmt->close();
            set_flash('success', 'Statistics updated successfully.');
            redirect_self();
        } else {
            set_flash('danger', 'Error preparing statement for statistics update.');
            redirect_self();
        }
    } else {
        set_flash('warning', 'No statistics data received.');
        redirect_self();
    }
}

// Fetch Statistics
$res_stats = $conn->query("SELECT * FROM site_statistics");
$statistics_data = [];
if ($res_stats && $res_stats->num_rows > 0) {
    while ($row = $res_stats->fetch_assoc()) {
        $statistics_data[] = $row;
    }
}

// Handle Add/Edit Slides (non-stats POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_stats'])) {
    $caption   = trim($_POST['caption'] ?? '');
    $url       = trim($_POST['url'] ?? '');
    $slide_id  = isset($_POST['slide_id']) ? (int)$_POST['slide_id'] : 0;
    $updated_by = $_SESSION['username'] ?? 'Admin';
    $date_updated = date('Y-m-d H:i:s');

    if ($url !== '' && !preg_match('~^https?://~i', $url)) {
        $url = 'https://' . $url;
    }

    if ($caption === '') {
        set_flash('danger', 'Caption is required.');
        redirect_self();
    }

    $db_file_path = '';
    if (!empty($_FILES['slide_image']['name'])) {
        $max_bytes = 5 * 1024 * 1024;
        if (!isset($_FILES['slide_image']['error']) || is_array($_FILES['slide_image']['error'])) {
            set_flash('danger', 'Invalid upload parameters.');
            redirect_self();
        }

        if ($_FILES['slide_image']['error'] !== UPLOAD_ERR_OK) {
            $errMap = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server upload limit.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form limit.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];
            $reason = $errMap[$_FILES['slide_image']['error']] ?? 'Unknown upload error.';
            set_flash('danger', "Upload failed: $reason");
            redirect_self();
        }

        if ($_FILES['slide_image']['size'] > $max_bytes) {
            set_flash('danger', 'Image too large. Max is 5MB.');
            redirect_self();
        }

        $ext = strtolower(pathinfo($_FILES['slide_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed_ext, true)) {
            set_flash('danger', 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.');
            redirect_self();
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['slide_image']['tmp_name']);
        $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($mime, $allowed_mime, true)) {
            set_flash('danger', 'Invalid image content.');
            redirect_self();
        }

        $upload_dir_fs = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir_fs)) {
            @mkdir($upload_dir_fs, 0755, true);
        }

        $new_base = uniqid('slide_', true) . '.' . $ext;
        $dest_fs = $upload_dir_fs . $new_base;
        if (!move_uploaded_file($_FILES['slide_image']['tmp_name'], $dest_fs)) {
            set_flash('danger', 'Failed to save uploaded file.');
            redirect_self();
        }

        $db_file_path = 'uploads/' . $new_base;
    }

    if ($slide_id > 0) {
        if ($db_file_path !== '') {
            $old_stmt = $conn->prepare("SELECT file FROM banners WHERE id = ?");
            $old_stmt->bind_param('i', $slide_id);
            $old_stmt->execute();
            $old_stmt->bind_result($old_file);
            $old_stmt->fetch();
            $old_stmt->close();

            $stmt = $conn->prepare("UPDATE banners SET caption = ?, url = ?, updated_by = ?, date_updated = ?, file = ? WHERE id = ?");
            $stmt->bind_param('sssssi', $caption, $url, $updated_by, $date_updated, $db_file_path, $slide_id);
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok && !empty($old_file) && $old_file !== $db_file_path) {
                $old_fs = __DIR__ . '/../' . $old_file;
                if (is_file($old_fs)) @unlink($old_fs);
            }
        } else {
            $stmt = $conn->prepare("UPDATE banners SET caption = ?, url = ?, updated_by = ?, date_updated = ? WHERE id = ?");
            $stmt->bind_param('ssssi', $caption, $url, $updated_by, $date_updated, $slide_id);
            $stmt->execute();
            $stmt->close();
        }
        set_flash('success', 'Slide updated successfully.');
        redirect_self();
    } else {
        $stmt = $conn->prepare("INSERT INTO banners (file, caption, url, updated_by, date_updated) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $db_file_path, $caption, $url, $updated_by, $date_updated);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'New slide added successfully.');
        redirect_self();
    }
}

// Handle Delete
if (isset($_GET['delete_slide'])) {
    $id = (int)$_GET['delete_slide'];
    $stmt = $conn->prepare("SELECT file FROM banners WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($file_rel);
    $found = $stmt->fetch();
    $stmt->close();

    if ($found) {
        if (!empty($file_rel)) {
            $file_fs = __DIR__ . '/../' . $file_rel;
            if (is_file($file_fs)) @unlink($file_fs);
        }
        $del = $conn->prepare("DELETE FROM banners WHERE id = ?");
        $del->bind_param('i', $id);
        $del->execute();
        $del->close();
        set_flash('success', 'Slide deleted successfully.');
    } else {
        set_flash('warning', 'Slide not found.');
    }
    redirect_self();
}

// Fetch Slides
$slides_rs = $conn->query("SELECT id, file, caption, url, updated_by, date_updated FROM banners ORDER BY date_updated DESC");
?>

<!-- Flash Messages (handled globally in layout, but we keep logic here) -->
<?php if (!empty($_SESSION['flash'])): ?>
<div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Slides</h1>
</div>

<section class="content-header">
    <div class="row">
        <div class="col-12">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#slideModal" onclick="openAddSlideModal()">
                <i class="fas fa-plus"></i> Add New Slide
            </button>

            <?php if ($slides_rs && $slides_rs->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>Slide</th>
                                <th>Caption</th>
                                <th>URL</th>
                                <th>Updated By</th>
                                <th>Date</th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $slides_rs->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row['file'])):
                                            $img_src = 'uploads/' . basename($row['file']);
                                            $img_fs = __DIR__ . '/../' . $row['file'];
                                        ?>
                                            <?php if (is_file($img_fs)): ?>
                                                <img src="<?= htmlspecialchars($img_src) ?>" alt="Slide" class="table-img">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center" style="width:120px; height:70px; border-radius:6px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                                <small class="text-muted fst-italic d-block">Missing: <?= htmlspecialchars($row['file']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width:120px; height:70px; border-radius:6px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['caption'] ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($row['url'])): ?>
                                            <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                                <?= htmlspecialchars(mb_strlen($row['url']) > 30 ? mb_substr($row['url'], 0, 30) . '…' : $row['url']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['updated_by'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['date_updated'] ?? '') ?></td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-warning btn-sm"
                                            onclick="openEditSlideModal(
                                                <?= (int)$row['id'] ?>,
                                                `<?= htmlspecialchars(addslashes($row['caption'] ?? '')) ?>`,
                                                `<?= htmlspecialchars(addslashes($row['url'] ?? '')) ?>`,
                                                `<?= htmlspecialchars(addslashes($row['file'] ?? '')) ?>`
                                            )"
                                            data-bs-toggle="modal" data-bs-target="#slideModal" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="?delete_slide=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this slide?');" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h5>No slides found</h5>
                    <p class="text-muted">Add your first slide using the button above.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- MANAGE SITE STATISTICS SECTION -->
<section class="statistics-section mb-5">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h3 class="h4">Manage Site Statistics</h3>
        <button class="btn btn-success" type="submit" form="statisticsForm">
            <i class="fas fa-save"></i> Update Statistics
        </button>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($statistics_data)): ?>
                            <form method="post" id="statisticsForm">
                                <input type="hidden" name="update_stats" value="1">
                                <div class="row">
                                    <?php foreach ($statistics_data as $stat_row): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <label class="form-label small fw-bold">
                                                <?= htmlspecialchars($stat_row['stat_label']) ?>
                                            </label>
                                            <input type="number"
                                                class="form-control form-control-sm"
                                                name="stats[<?= (int)$stat_row['id'] ?>]"
                                                value="<?= (int)$stat_row['stat_value'] ?>"
                                                min="0" required>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> No statistics data found.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Slide Modal -->
<div class="modal fade" id="slideModal" tabindex="-1" aria-labelledby="slideModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="slideForm" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="slideModalLabel">Add/Edit Slide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="slide_id" id="slide_id">
                    <div class="mb-3">
                        <label class="form-label">Slide Image</label>
                        <input type="file" class="form-control" id="slide_image" name="slide_image" accept="image/*">
                        <div class="square-img-preview" id="slide_img_box" style="display:none;"></div>
                        <small class="form-text text-muted">Recommended: 1200×600 px. Max 5MB. JPG/PNG/GIF/WEBP.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Caption</label>
                        <input type="text" class="form-control" id="caption" name="caption" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">URL (Optional)</label>
                        <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Page-Specific Scripts -->
<script>
function openAddSlideModal() {
    document.getElementById('slideModalLabel').innerText = "Add Slide";
    document.getElementById('slideForm').reset();
    document.getElementById('slide_id').value = "";
    const box = document.getElementById('slide_img_box');
    box.style.display = 'none';
    box.style.backgroundImage = '';
}

function openEditSlideModal(id, caption, url, img) {
    document.getElementById('slideModalLabel').innerText = "Edit Slide";
    document.getElementById('slide_id').value = id;
    document.getElementById('caption').value = caption.replace(/\\'/g,"'");
    document.getElementById('url').value = url.replace(/\\'/g,"'");
    const box = document.getElementById('slide_img_box');
    if (img) {
        const imagePath = 'uploads/' + img.split('/').pop(); // safe: only basename
        box.style.backgroundImage = 'url(' + imagePath + ')';
        box.style.display = 'inline-block';
    } else {
        box.style.display = 'none';
        box.style.backgroundImage = '';
    }
    document.getElementById('slide_image').value = "";
}

document.getElementById('slide_image').addEventListener('change', function(e){
    const box = document.getElementById('slide_img_box');
    if (e.target.files?.[0]) {
        const reader = new FileReader();
        reader.onload = evt => {
            box.style.backgroundImage = 'url(' + evt.target.result + ')';
            box.style.display = 'inline-block';
        };
        reader.readAsDataURL(e.target.files[0]);
    } else {
        box.style.display = 'none';
        box.style.backgroundImage = '';
    }
});
</script>
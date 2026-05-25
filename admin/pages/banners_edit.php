<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');
require_once __DIR__ . '/../../includes/cms_helpers.php';

// Detect whether the banners table has a `description` column (schema variation between deployments)
$has_description = false;
$col_check = @$conn->query("SHOW COLUMNS FROM banners LIKE 'description'");
if ($col_check && $col_check->num_rows > 0) {
    $has_description = true;
}

$upload_dir_fs = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir_fs)) {
    @mkdir($upload_dir_fs, 0755, true);
}

// ── Handle Add / Edit ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['delete'])) {
    $caption     = pc_strip_text($_POST['caption'] ?? '');
    $description = pc_strip_text($_POST['description'] ?? '');
    $url         = trim($_POST['url'] ?? '');
    $banner_id   = isset($_POST['banner_id']) ? (int)$_POST['banner_id'] : 0;
    $updated_by  = $_SESSION['username'] ?? 'Admin';
    $date_updated = date('Y-m-d H:i:s');

    if ($url !== '' && !preg_match('~^https?://~i', $url)) {
        $url = 'https://' . $url;
    }

    if ($caption === '') {
        set_flash('danger', 'Caption is required.');
        redirect_self();
    }

    // Handle upload
    $db_file_path = '';
    if (!empty($_FILES['banner_image']['name'])) {
        if ($_FILES['banner_image']['error'] !== UPLOAD_ERR_OK) {
            set_flash('danger', 'Upload failed (error code ' . (int)$_FILES['banner_image']['error'] . ').');
            redirect_self();
        }
        $max_bytes = 5 * 1024 * 1024;
        if ($_FILES['banner_image']['size'] > $max_bytes) {
            set_flash('danger', 'Image too large. Max is 5MB.');
            redirect_self();
        }
        $ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed_ext, true)) {
            set_flash('danger', 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF, WEBP.');
            redirect_self();
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['banner_image']['tmp_name']);
        $allowed_mime = ['image/jpeg','image/png','image/gif','image/webp'];
        if (!in_array($mime, $allowed_mime, true)) {
            set_flash('danger', 'Invalid image content.');
            redirect_self();
        }
        $new_base = uniqid('banner_', true) . '.' . $ext;
        $dest_fs  = $upload_dir_fs . $new_base;
        if (!move_uploaded_file($_FILES['banner_image']['tmp_name'], $dest_fs)) {
            set_flash('danger', 'Failed to save uploaded file.');
            redirect_self();
        }
        $db_file_path = 'uploads/' . $new_base;
    }

    if ($banner_id > 0) {
        // UPDATE
        if ($db_file_path !== '') {
            // Remove old file (best-effort)
            $old_stmt = $conn->prepare("SELECT file FROM banners WHERE id = ?");
            $old_stmt->bind_param('i', $banner_id);
            $old_stmt->execute();
            $old_stmt->bind_result($old_file);
            $old_stmt->fetch();
            $old_stmt->close();

            if ($has_description) {
                $sql = "UPDATE banners SET caption = ?, description = ?, url = ?, updated_by = ?, date_updated = ?, file = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssssi', $caption, $description, $url, $updated_by, $date_updated, $db_file_path, $banner_id);
            } else {
                $sql = "UPDATE banners SET caption = ?, url = ?, updated_by = ?, date_updated = ?, file = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssssi', $caption, $url, $updated_by, $date_updated, $db_file_path, $banner_id);
            }
            $ok = $stmt->execute();
            $stmt->close();

            if ($ok && !empty($old_file) && $old_file !== $db_file_path) {
                $old_fs = __DIR__ . '/../' . $old_file;
                if (is_file($old_fs)) @unlink($old_fs);
            }
        } else {
            if ($has_description) {
                $sql = "UPDATE banners SET caption = ?, description = ?, url = ?, updated_by = ?, date_updated = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('sssssi', $caption, $description, $url, $updated_by, $date_updated, $banner_id);
            } else {
                $sql = "UPDATE banners SET caption = ?, url = ?, updated_by = ?, date_updated = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssssi', $caption, $url, $updated_by, $date_updated, $banner_id);
            }
            $stmt->execute();
            $stmt->close();
        }
        set_flash('success', 'Banner updated.');
        redirect_self();
    } else {
        // INSERT
        if ($has_description) {
            $sql = "INSERT INTO banners (file, caption, description, url, updated_by, date_updated) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssss', $db_file_path, $caption, $description, $url, $updated_by, $date_updated);
        } else {
            $sql = "INSERT INTO banners (file, caption, url, updated_by, date_updated) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $db_file_path, $caption, $url, $updated_by, $date_updated);
        }
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Banner added.');
        redirect_self();
    }
}

// ── Handle Delete ──────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
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
        set_flash('success', 'Banner deleted.');
    } else {
        set_flash('warning', 'Banner not found.');
    }
    header('Location: index.php?page=banners_edit.php');
    exit;
}

// ── Pre-fill on edit ───────────────────────────────────────────
$edit_banner = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM banners WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_banner = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ── Fetch all banners ──────────────────────────────────────────
$banners_rs = $conn->query("SELECT * FROM banners ORDER BY date_updated DESC, id DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Banner Slider</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="openAddBannerModal()">
        <i class="fas fa-plus me-1"></i> Add Banner
    </button>
</div>

<p class="text-muted">Banners appear in the rotating slider at the top of the home page. Recommended image size: 1920 x 700 px. Max 5MB.</p>

<div class="card">
    <div class="card-body">
        <?php if ($banners_rs && $banners_rs->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Caption</th>
                            <?php if ($has_description): ?><th>Description</th><?php endif; ?>
                            <th>URL</th>
                            <th>Updated</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $banners_rs->fetch_assoc()):
                            $img_rel = $row['file'] ?? '';
                            $img_src = '';
                            if (!empty($img_rel)) {
                                if (strpos($img_rel, 'admin/') === 0) {
                                    $img_src = '../' . $img_rel;
                                } elseif (strpos($img_rel, 'uploads/') === 0) {
                                    $img_src = $img_rel;
                                } else {
                                    $img_src = 'uploads/' . basename($img_rel);
                                }
                            }
                        ?>
                            <tr>
                                <td>
                                    <?php if ($img_src): ?>
                                        <img src="<?= htmlspecialchars($img_src) ?>" alt="" style="width:120px;height:60px;object-fit:cover;border:1px solid #ddd;border-radius:4px;">
                                    <?php else: ?>
                                        <div style="width:120px;height:60px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;border-radius:4px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['caption'] ?? '') ?></td>
                                <?php if ($has_description): ?>
                                    <td><?= htmlspecialchars(mb_strimwidth($row['description'] ?? '', 0, 60, '…')) ?></td>
                                <?php endif; ?>
                                <td>
                                    <?php if (!empty($row['url'])): ?>
                                        <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank" rel="noopener">
                                            <?= htmlspecialchars(mb_strimwidth($row['url'], 0, 30, '…')) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($row['date_updated'] ?? '') ?></td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick='openEditBannerModal(<?= json_encode([
                                            "id" => (int)$row["id"],
                                            "caption" => $row["caption"] ?? "",
                                            "description" => $row["description"] ?? "",
                                            "url" => $row["url"] ?? "",
                                            "file" => $row["file"] ?? "",
                                            "img_src" => $img_src,
                                        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                                        data-bs-toggle="modal" data-bs-target="#bannerModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="?page=banners_edit.php&delete=<?= (int)$row['id'] ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this banner?');">
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
                <h5>No banners yet</h5>
                <p class="text-muted">Click "Add Banner" to create your first slide.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">Add Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="banner_id" id="banner_id" value="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image</label>
                        <input type="file" class="form-control" name="banner_image" id="banner_image" accept="image/*">
                        <div id="banner_current_preview" class="mt-2" style="display:none;">
                            <small class="text-muted d-block">Current image:</small>
                            <img id="banner_current_img" src="" style="max-height:120px;border:1px solid #ddd;border-radius:4px;">
                        </div>
                        <small class="form-text text-muted">Recommended 1920 x 700 px. Max 5MB. JPG/PNG/GIF/WEBP. Leave empty to keep current.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Caption *</label>
                        <input type="text" class="form-control" name="caption" id="banner_caption" required>
                    </div>
                    <?php if ($has_description): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" name="description" id="banner_description" rows="3"></textarea>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Link URL (optional)</label>
                        <input type="url" class="form-control" name="url" id="banner_url" placeholder="https://example.com">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddBannerModal() {
    document.getElementById('bannerModalLabel').innerText = 'Add Banner';
    document.getElementById('banner_id').value = '';
    document.getElementById('banner_caption').value = '';
    var d = document.getElementById('banner_description');
    if (d) d.value = '';
    document.getElementById('banner_url').value = '';
    document.getElementById('banner_image').value = '';
    document.getElementById('banner_current_preview').style.display = 'none';
}

function openEditBannerModal(b) {
    document.getElementById('bannerModalLabel').innerText = 'Edit Banner';
    document.getElementById('banner_id').value = b.id;
    document.getElementById('banner_caption').value = b.caption || '';
    var d = document.getElementById('banner_description');
    if (d) d.value = b.description || '';
    document.getElementById('banner_url').value = b.url || '';
    document.getElementById('banner_image').value = '';
    if (b.img_src) {
        document.getElementById('banner_current_img').src = b.img_src;
        document.getElementById('banner_current_preview').style.display = 'block';
    } else {
        document.getElementById('banner_current_preview').style.display = 'none';
    }
}
</script>

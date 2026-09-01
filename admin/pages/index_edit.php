<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// =============================================================================
// BANNER SLIDER (table: banners)
// =============================================================================

// Detect optional description column
$banner_has_description = false;
$col_check = @$conn->query("SHOW COLUMNS FROM banners LIKE 'description'");
if ($col_check && $col_check->num_rows > 0) {
    $banner_has_description = true;
}

$upload_dir_fs = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir_fs)) {
    @mkdir($upload_dir_fs, 0755, true);
}

// ── Banner: Add/Edit ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_banner'])) {
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

    // Handle upload — prefer the cropper's base64 payload; fall back to a
    // raw file upload if the user picked a file the cropper passed through.
    $db_file_path = '';
    $crop_path = pc_save_base64_image($_POST['banner_image_cropped'] ?? '', $upload_dir_fs, 'banner');
    if (is_string($crop_path)) {
        // banners.file stores paths relative to admin/ (uploads/...)
        $db_file_path = 'uploads/' . basename($crop_path);
    } elseif (!empty($_FILES['banner_image']['name'])) {
        if ($_FILES['banner_image']['error'] !== UPLOAD_ERR_OK) {
            set_flash('danger', 'Upload failed (error ' . (int)$_FILES['banner_image']['error'] . ').');
            redirect_self();
        }
        if ($_FILES['banner_image']['size'] > 5 * 1024 * 1024) {
            set_flash('danger', 'Image too large. Max is 5MB.');
            redirect_self();
        }
        $ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif','webp'], true)) {
            set_flash('danger', 'Invalid file type. Allowed: JPG/PNG/GIF/WEBP.');
            redirect_self();
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['banner_image']['tmp_name']);
        if (!in_array($mime, ['image/jpeg','image/png','image/gif','image/webp'], true)) {
            set_flash('danger', 'Invalid image content.');
            redirect_self();
        }
        $new_base = uniqid('banner_', true) . '.' . $ext;
        if (!move_uploaded_file($_FILES['banner_image']['tmp_name'], $upload_dir_fs . $new_base)) {
            set_flash('danger', 'Failed to save uploaded file.');
            redirect_self();
        }
        $db_file_path = 'uploads/' . $new_base;
    }

    if ($banner_id > 0) {
        if ($db_file_path !== '') {
            $old_stmt = $conn->prepare("SELECT file FROM banners WHERE id = ?");
            $old_stmt->bind_param('i', $banner_id);
            $old_stmt->execute();
            $old_stmt->bind_result($old_file);
            $old_stmt->fetch();
            $old_stmt->close();

            if ($banner_has_description) {
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
            if ($banner_has_description) {
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
        if ($banner_has_description) {
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

// ── Banner: Delete ────────────────────────────────────────────────
if (isset($_GET['delete_banner'])) {
    $id = (int)$_GET['delete_banner'];
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
    header('Location: index.php?page=index_edit.php');
    exit;
}

// =============================================================================
// HOME SECTIONS (page_content)
// =============================================================================

$heading_keys = [
    'index_discover_heading',
    'index_marks_heading',
    'index_affiliations_heading',
];

$discover_text_keys = [];
$discover_url_keys  = [];
for ($i = 1; $i <= 5; $i++) {
    $discover_text_keys[] = "index_discover_{$i}_title";
    $discover_text_keys[] = "index_discover_{$i}_desc";
    $discover_url_keys[]  = "index_discover_{$i}_url";
}

$mark_text_keys  = [];
$mark_url_keys   = [];
$mark_image_keys = [];
for ($i = 1; $i <= 3; $i++) {
    $mark_text_keys[]  = "index_mark_{$i}_title";
    $mark_text_keys[]  = "index_mark_{$i}_desc";
    $mark_url_keys[]   = "index_mark_{$i}_explore_url";
    $mark_url_keys[]   = "index_mark_{$i}_verify_url";
    $mark_image_keys[] = "index_mark_{$i}_image";
}

// Affiliation logos live in the index_affiliations table, not page_content, so
// the slider isn't capped at a fixed slot count. Only its heading is CMS copy.
$text_keys  = array_merge($heading_keys, $discover_text_keys, $mark_text_keys);
$url_keys   = array_merge($discover_url_keys, $mark_url_keys);
$image_keys = $mark_image_keys;

// ── Affiliations: create / update ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_aff'])) {
    $aff_id     = !empty($_POST['aff_id']) ? (int)$_POST['aff_id'] : null;
    $aff_alt    = pc_strip_text($_POST['aff_alt'] ?? '');
    $aff_url    = strip_tags(trim((string)($_POST['aff_url'] ?? '')));
    $aff_sort   = (int)($_POST['aff_sort_order'] ?? 0);
    $aff_active = !empty($_POST['aff_is_active']) ? 1 : 0;
    $aff_logo   = pc_strip_text($_POST['aff_existing_logo'] ?? '');

    $errors = [];
    if ($aff_alt === '') $errors[] = 'Alt text is required — screen readers announce it in place of the logo.';

    // Prefer the cropper's base64 payload; fall back to a raw file upload
    // (e.g. SVG logos the cropper passes through untouched).
    $up = pc_save_base64_image($_POST['aff_logo_cropped'] ?? '', ADMIN_ROOT . '/uploads/affiliations/', 'aff');
    if (!is_string($up)) {
        $up = pc_upload_image('aff_logo_file', ADMIN_ROOT . '/uploads/affiliations/', 'aff');
    }
    if ($up === false) {
        $errors[] = 'Logo upload failed (check file type — JPG/PNG/WebP/SVG/GIF — and size under 5 MB).';
    } elseif ($up) {
        $aff_logo = $up;
    }
    if ($aff_logo === '') $errors[] = 'A logo image is required.';

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: index.php?page=index_edit.php' . ($aff_id ? '&edit_aff=' . $aff_id : '&new_aff=1'));
        exit;
    }

    if ($aff_id) {
        $stmt = $conn->prepare('UPDATE index_affiliations SET logo_path = ?, url = ?, alt = ?, sort_order = ?, is_active = ? WHERE id = ?');
        $stmt->bind_param('sssiii', $aff_logo, $aff_url, $aff_alt, $aff_sort, $aff_active, $aff_id);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Affiliation updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO index_affiliations (logo_path, url, alt, sort_order, is_active) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssii', $aff_logo, $aff_url, $aff_alt, $aff_sort, $aff_active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Affiliation added.');
    }
    header('Location: index.php?page=index_edit.php');
    exit;
}

// ── Affiliations: toggle / delete ─────────────────────────────
if (isset($_GET['toggle_aff'])) {
    $aff_id = (int)$_GET['toggle_aff'];
    $stmt = $conn->prepare('UPDATE index_affiliations SET is_active = 1 - is_active WHERE id = ?');
    $stmt->bind_param('i', $aff_id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: index.php?page=index_edit.php');
    exit;
}
if (isset($_GET['delete_aff'])) {
    $aff_id = (int)$_GET['delete_aff'];
    // Only uploads this page created are removed; seeded logos under
    // assets/img/ are shared with other pages and must stay.
    $sel = $conn->prepare('SELECT logo_path FROM index_affiliations WHERE id = ?');
    $sel->bind_param('i', $aff_id);
    $sel->execute();
    $aff_row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($aff_row && !empty($aff_row['logo_path']) && strpos($aff_row['logo_path'], 'admin/uploads/affiliations/') === 0) {
        $fs = dirname(ADMIN_ROOT) . '/' . $aff_row['logo_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM index_affiliations WHERE id = ?');
    $stmt->bind_param('i', $aff_id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Affiliation deleted.');
    header('Location: index.php?page=index_edit.php');
    exit;
}

// ── Home Sections: Save ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_index'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    foreach ($url_keys as $k) {
        $v = trim((string)($_POST[$k] ?? ''));
        $v = strip_tags($v);
        $kv[$k] = $v;
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'index');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'index');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Home page content saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $url_keys, $image_keys));
$banners_rs = $conn->query("SELECT * FROM banners ORDER BY date_updated DESC, id DESC");

$aff_res  = $conn->query('SELECT * FROM index_affiliations ORDER BY sort_order ASC, id ASC');
$aff_rows = $aff_res ? $aff_res->fetch_all(MYSQLI_ASSOC) : [];

$aff_edit   = null;
$aff_is_new = isset($_GET['new_aff']);
if (isset($_GET['edit_aff'])) {
    $stmt = $conn->prepare('SELECT * FROM index_affiliations WHERE id = ?');
    $aff_eid = (int)$_GET['edit_aff'];
    $stmt->bind_param('i', $aff_eid);
    $stmt->execute();
    $aff_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Home Page</h1>
    <a href="../index.php" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<p class="text-muted">Everything that appears on the home page — the rotating slider, the Discover cards, Certification Marks and Affiliations.</p>

<!-- =================================================================== -->
<!--  BANNER SLIDER                                                       -->
<!-- =================================================================== -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Banner Slider</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bannerModal" onclick="openAddBannerModal()">
                <i class="fas fa-plus me-1"></i> Add Banner
            </button>
        </div>
        <p class="text-muted small mb-3">Banners appear in the rotating slider at the top of the home page. Picking an image opens a cropper fixed at 1920 &times; 700 px so you see exactly what gets saved. Max 5MB.</p>

        <?php if ($banners_rs && $banners_rs->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Caption</th>
                            <?php if ($banner_has_description): ?><th>Description</th><?php endif; ?>
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
                                <?php if ($banner_has_description): ?>
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
                                    <a href="?page=index_edit.php&delete_banner=<?= (int)$row['id'] ?>"
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
            <div class="text-center py-4">
                <i class="fas fa-images fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No banners yet — click "Add Banner".</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- =================================================================== -->
<!--  HOME SECTIONS                                                       -->
<!-- =================================================================== -->
<form method="POST" enctype="multipart/form-data">

    <!-- Section Headings -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Section Headings</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Discover heading</label>
                    <input type="text" name="index_discover_heading" class="form-control" value="<?= pc_h($pc['index_discover_heading']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Certification Marks heading</label>
                    <input type="text" name="index_marks_heading" class="form-control" value="<?= pc_h($pc['index_marks_heading']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Affiliations heading</label>
                    <input type="text" name="index_affiliations_heading" class="form-control" value="<?= pc_h($pc['index_affiliations_heading']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Discover Cards -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Discover Section (5 cards)</h5>
            <p class="text-muted small">Icons are fixed decorative SVGs and are not editable. Edit the title, description and link target.</p>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">Card <?= $i ?></h6>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Title</label>
                        <input type="text" name="index_discover_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc["index_discover_{$i}_title"]) ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="index_discover_<?= $i ?>_desc" class="form-control" rows="2"><?= pc_h($pc["index_discover_{$i}_desc"]) ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Link URL</label>
                        <input type="text" name="index_discover_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc["index_discover_{$i}_url"]) ?>" placeholder="Certification.php or https://...">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Certification Marks -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Marks Section (3 cards)</h5>
            <?php for ($i = 1; $i <= 3; $i++):
                $img_key = "index_mark_{$i}_image";
                $current_img = $pc[$img_key] ?? '';
            ?>
                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">Mark <?= $i ?></h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Title</label>
                                <input type="text" name="index_mark_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_title"]) ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="index_mark_<?= $i ?>_desc" class="form-control" rows="3"><?= pc_h($pc["index_mark_{$i}_desc"]) ?></textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Explore URL</label>
                                    <input type="text" name="index_mark_<?= $i ?>_explore_url" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_explore_url"]) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Verify URL</label>
                                    <input type="text" name="index_mark_<?= $i ?>_verify_url" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_verify_url"]) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Mark Image</label>
                            <div class="mb-2">
                                <img data-crop-preview="index_mark_<?= $i ?>_image_preview"
                                     src="<?= !empty($current_img) ? '../' . pc_h(pc_image_src($current_img)) : '' ?>"
                                     style="max-height:120px;max-width:100%;border:1px solid #ddd;padding:4px;background:#fff;<?= empty($current_img) ? 'display:none;' : '' ?>"
                                     onload="this.style.display='inline-block'" alt="">
                            </div>
                            <input type="file" name="index_mark_<?= $i ?>_image_file" accept="image/*" class="form-control crop-input"
                                   data-crop-label="Mark <?= $i ?> Image">
                            <input type="hidden" name="index_mark_<?= $i ?>_image_cropped">
                            <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="text-end mb-4">
        <button type="submit" name="save_index" class="btn btn-primary px-5">
            <i class="fas fa-save me-2"></i>Save Home Sections
        </button>
    </div>
</form>

<!-- ============ Affiliations ============ -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Affiliations (<?= count($aff_rows) ?>)</strong>
        <?php if (!$aff_edit && !$aff_is_new): ?>
            <a href="index.php?page=index_edit.php&new_aff=1" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add affiliation
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <p class="text-muted small">
            Logos in the seamless scrolling band at the foot of the home page. The set is
            duplicated automatically for the loop, so add each logo once. Its heading is
            edited under Home Sections above.
        </p>

        <?php if ($aff_edit || $aff_is_new):
            $a = $aff_edit ?: [
                'id' => 0, 'logo_path' => '', 'url' => '', 'alt' => '',
                'sort_order' => ($aff_rows ? (max(array_column($aff_rows, 'sort_order')) + 1) : 1),
                'is_active' => 1,
            ];
        ?>
            <form method="POST" enctype="multipart/form-data" class="border rounded p-3 mb-3">
                <?php if ($aff_edit): ?>
                    <input type="hidden" name="aff_id" value="<?= (int)$a['id'] ?>">
                <?php endif; ?>
                <input type="hidden" name="aff_existing_logo" value="<?= pc_h($a['logo_path']) ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Alt text *</label>
                        <input type="text" name="aff_alt" class="form-control" required maxlength="200"
                               value="<?= pc_h($a['alt']) ?>" placeholder="e.g. ISO">
                        <div class="form-text">Announced by screen readers in place of the logo.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Link URL</label>
                        <input type="url" name="aff_url" class="form-control"
                               value="<?= pc_h($a['url']) ?>" placeholder="https://...">
                        <div class="form-text">Optional. Leave empty and the logo isn't a link.</div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sort order</label>
                        <input type="number" name="aff_sort_order" class="form-control" value="<?= (int)$a['sort_order'] ?>">
                        <div class="form-text">Lower = earlier.</div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Logo *</label>
                        <div class="mb-2">
                            <img data-crop-preview="aff_logo_preview"
                                 src="<?= !empty($a['logo_path']) ? '../' . pc_h(pc_image_src($a['logo_path'])) : '' ?>"
                                 style="max-height:70px;border:1px solid #ddd;padding:4px;background:#fff;<?= empty($a['logo_path']) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                            <?php if (!empty($a['logo_path'])): ?>
                                <code class="ms-2 small text-muted"><?= pc_h($a['logo_path']) ?></code>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="aff_logo_file" class="form-control crop-input"
                               accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif"
                               data-crop-label="Affiliation Logo">
                        <input type="hidden" name="aff_logo_cropped">
                        <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="aff_is_active" id="aff_is_active" value="1" class="form-check-input"
                                   <?= (int)$a['is_active'] === 1 ? 'checked' : '' ?>>
                            <label for="aff_is_active" class="form-check-label">Show in the slider</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="index.php?page=index_edit.php" class="btn btn-link text-decoration-none">Cancel</a>
                    <button type="submit" name="save_aff" value="1" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= $aff_edit ? 'Save changes' : 'Add affiliation' ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($aff_rows): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Order</th>
                            <th style="width: 120px;">Logo</th>
                            <th>Alt text</th>
                            <th>Link</th>
                            <th style="width: 90px;" class="text-center">Active</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aff_rows as $row): ?>
                            <tr>
                                <td><?= (int)$row['sort_order'] ?></td>
                                <td><img src="../<?= pc_h(pc_image_src($row['logo_path'])) ?>" style="max-height:36px;max-width:100px;object-fit:contain" alt=""></td>
                                <td><?= pc_h($row['alt']) ?></td>
                                <td class="small text-muted text-break"><?= pc_h($row['url']) ?: '&mdash;' ?></td>
                                <td class="text-center">
                                    <a href="index.php?page=index_edit.php&toggle_aff=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                        <?php if ((int)$row['is_active'] === 1): ?>
                                            <span class="badge bg-success">On</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Off</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="index.php?page=index_edit.php&edit_aff=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="index.php?page=index_edit.php&delete_aff=<?= (int)$row['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this affiliation? This cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">
                No affiliations yet. <a href="index.php?page=index_edit.php&new_aff=1">Add the first one</a>.
            </p>
        <?php endif; ?>
    </div>
</div>

<!-- Banner Modal (uses its own form, separate from the sections form above) -->
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
                    <input type="hidden" name="save_banner" value="1">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image</label>
                        <input type="file" class="form-control crop-input" name="banner_image" id="banner_image" accept="image/*"
                               data-crop-w="1920" data-crop-h="700" data-crop-label="Banner Image">
                        <input type="hidden" name="banner_image_cropped" id="banner_image_cropped">
                        <div id="banner_current_preview" class="mt-2" style="display:none;">
                            <small class="text-muted d-block" id="banner_preview_label">Current image:</small>
                            <img id="banner_current_img" data-crop-preview="banner_image_preview" src="" style="max-height:120px;border:1px solid #ddd;border-radius:4px;">
                        </div>
                        <small class="form-text text-muted">Pick an image &mdash; the cropper opens at 1920 &times; 700 px so you see exactly what the slider will show. Max 5MB. Leave empty to keep current.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Caption *</label>
                        <input type="text" class="form-control" name="caption" id="banner_caption" required>
                    </div>
                    <?php if ($banner_has_description): ?>
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
    document.getElementById('banner_image_cropped').value = '';
    document.getElementById('banner_preview_label').innerText = 'Current image:';
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
    document.getElementById('banner_image_cropped').value = '';
    document.getElementById('banner_preview_label').innerText = 'Current image:';
    if (b.img_src) {
        document.getElementById('banner_current_img').src = b.img_src;
        document.getElementById('banner_current_preview').style.display = 'block';
    } else {
        document.getElementById('banner_current_preview').style.display = 'none';
    }
}

// When the cropper applies a selection it fills banner_image_cropped and
// updates the preview <img> — surface it even when adding a new banner.
document.getElementById('banner_image_cropped').addEventListener('change', function () {
    if (this.value) {
        document.getElementById('banner_preview_label').innerText = 'New cropped image (saved on Save Banner):';
        document.getElementById('banner_current_preview').style.display = 'block';
    }
});
</script>

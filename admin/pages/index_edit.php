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

    // Handle upload
    $db_file_path = '';
    if (!empty($_FILES['banner_image']['name'])) {
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
for ($i = 1; $i <= 4; $i++) {
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

$aff_text_keys  = [];
$aff_url_keys   = [];
$aff_image_keys = [];
for ($i = 1; $i <= 10; $i++) {
    $aff_text_keys[]  = "index_affiliation_{$i}_alt";
    $aff_url_keys[]   = "index_affiliation_{$i}_url";
    $aff_image_keys[] = "index_affiliation_{$i}_logo";
}

$text_keys  = array_merge($heading_keys, $discover_text_keys, $mark_text_keys, $aff_text_keys);
$url_keys   = array_merge($discover_url_keys, $mark_url_keys, $aff_url_keys);
$image_keys = array_merge($mark_image_keys, $aff_image_keys);

// ── Home Sections: Save ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_index'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($url_keys as $k) {
        $v = trim((string)($_POST[$k] ?? ''));
        $v = strip_tags($v);
        $kv[$k] = $v;
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'index');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Home page content saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $url_keys, $image_keys));
$banners_rs = $conn->query("SELECT * FROM banners ORDER BY date_updated DESC, id DESC");

$aff_default_alts = ['ISO','IEC','ITU','IAF','ILAC','ARSO','SADCAS','SADC','SADCSTAN','ASTM'];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Home Page</h1>
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
        <p class="text-muted small mb-3">Banners appear in the rotating slider at the top of the home page. Recommended 1920 x 700 px. Max 5MB.</p>

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
            <h5 class="mb-3">Discover Section (4 cards)</h5>
            <p class="text-muted small">Icons are fixed decorative SVGs and are not editable. Edit the title, description and link target.</p>
            <?php for ($i = 1; $i <= 4; $i++): ?>
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
                            <?php if (!empty($current_img)): ?>
                                <div class="mb-2">
                                    <img src="../<?= pc_h(pc_image_src($current_img)) ?>" style="max-height:120px;max-width:100%;border:1px solid #ddd;padding:4px;background:#fff;" alt="">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="index_mark_<?= $i ?>_image_file" accept="image/*" class="form-control">
                            <div class="form-text">Leave empty to keep current image.</div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Affiliations -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Affiliations Section (10 logos)</h5>
            <p class="text-muted small">Logos appear in the seamless scrolling band. The same set is duplicated automatically for the loop.</p>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 10; $i++):
                    $logo_key = "index_affiliation_{$i}_logo";
                    $current_logo = $pc[$logo_key] ?? '';
                    $default_alt = $aff_default_alts[$i - 1] ?? ('Affiliation ' . $i);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-2">Affiliation <?= $i ?> <span class="text-muted small">(<?= htmlspecialchars($default_alt) ?>)</span></h6>
                            <?php if (!empty($current_logo)): ?>
                                <div class="mb-2 text-center" style="background:#fff;border:1px solid #eee;padding:8px;">
                                    <img src="../<?= pc_h(pc_image_src($current_logo)) ?>" style="max-height:70px;max-width:100%;" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Logo image</label>
                                <input type="file" name="index_affiliation_<?= $i ?>_logo_file" accept="image/*" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Link URL</label>
                                <input type="url" name="index_affiliation_<?= $i ?>_url" class="form-control form-control-sm" value="<?= pc_h($pc["index_affiliation_{$i}_url"]) ?>" placeholder="https://...">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold">Alt text</label>
                                <input type="text" name="index_affiliation_<?= $i ?>_alt" class="form-control form-control-sm" value="<?= pc_h($pc["index_affiliation_{$i}_alt"]) ?>">
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="text-end mb-4">
        <button type="submit" name="save_index" class="btn btn-primary px-5">
            <i class="fas fa-save me-2"></i>Save Home Sections
        </button>
    </div>
</form>

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

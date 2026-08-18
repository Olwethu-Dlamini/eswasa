<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_announcements.php';

// ── Announcement upload helper (sanitize + uniquify + MIME-check) ─
function announcements_upload(string $field, string $upload_dir, int $max_bytes = 26214400): array {
    if (empty($_FILES[$field]['name'])) {
        return ['ok' => true, 'path' => null, 'error' => ''];
    }
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Upload failed.'];
    }
    if ($_FILES[$field]['size'] > $max_bytes) {
        return ['ok' => false, 'path' => null, 'error' => 'File exceeds size limit (25 MB).'];
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $original = $_FILES[$field]['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed_ext, true)) {
        return ['ok' => false, 'path' => null, 'error' => 'Only PDF, JPG, JPEG, PNG, or WEBP files are allowed.'];
    }

    $allowed_mime = [
        'pdf'  => ['application/pdf', 'application/x-pdf'],
        'jpg'  => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
    ];
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        $expected = $allowed_mime[$ext] ?? [];
        if ($mime && $expected && !in_array(strtolower($mime), $expected, true)) {
            return ['ok' => false, 'path' => null, 'error' => 'File does not match the expected type for its extension.'];
        }
    }

    $stem = pathinfo($original, PATHINFO_FILENAME);
    $stem = strtolower($stem);
    $stem = preg_replace('/[^a-z0-9]+/', '-', $stem);
    $stem = trim($stem, '-');
    if ($stem === '') $stem = 'announcement';
    if (strlen($stem) > 60) $stem = substr($stem, 0, 60);

    $unique = $stem . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            return ['ok' => false, 'path' => null, 'error' => 'Could not create uploads directory.'];
        }
    }

    $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $unique;
    if (!move_uploaded_file($tmp, $target)) {
        return ['ok' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    }

    return ['ok' => true, 'path' => 'announcements/' . $unique, 'error' => ''];
}

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_announcements_content'])) {
    $kv = [];
    foreach ($announcements_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=announcements_edit.php&tab=content');
    exit;
}

// ── POST: CREATE / UPDATE announcement ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = pc_strip_text($_POST['title'] ?? '');
    $description = pc_strip_text($_POST['description'] ?? '');
    $announcement_type = $_POST['announcement_type'] ?? 'news';
    $published_date = $_POST['published_date'] ?? '';
    $external_link = trim($_POST['external_link'] ?? '');
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $allowed_types = ['news', 'update', 'closure', 'event', 'policy'];
    if (!in_array($announcement_type, $allowed_types, true)) $announcement_type = 'news';

    if ($external_link !== '' && !filter_var($external_link, FILTER_VALIDATE_URL)) {
        set_flash('danger', 'External link must be a valid URL.');
        header('Location: index.php?page=announcements_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }

    if (!$title || !$published_date || !$description) {
        set_flash('danger', 'Title, Description, and Published Date are required.');
        header('Location: index.php?page=announcements_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }

    $upload = announcements_upload('file', __DIR__ . '/../uploads/announcements/');
    if (!$upload['ok']) {
        set_flash('danger', $upload['error']);
        header('Location: index.php?page=announcements_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }
    $file_path = $upload['path']; // null when no new file uploaded

    if ($id) {
        if ($file_path) {
            // Remove old file from disk
            $old = $conn->prepare('SELECT file_path FROM eswasa_announcements WHERE id = ?');
            $old->bind_param('i', $id);
            $old->execute();
            $oldRow = $old->get_result()->fetch_assoc();
            $old->close();
            if ($oldRow && !empty($oldRow['file_path'])) {
                $oldFile = __DIR__ . '/../uploads/' . $oldRow['file_path'];
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $stmt = $conn->prepare('UPDATE eswasa_announcements SET title = ?, description = ?, announcement_type = ?, published_date = ?, external_link = ?, file_path = ? WHERE id = ?');
            $stmt->bind_param('ssssssi', $title, $description, $announcement_type, $published_date, $external_link, $file_path, $id);
        } else {
            $stmt = $conn->prepare('UPDATE eswasa_announcements SET title = ?, description = ?, announcement_type = ?, published_date = ?, external_link = ? WHERE id = ?');
            $stmt->bind_param('sssssi', $title, $description, $announcement_type, $published_date, $external_link, $id);
        }
        $msg = 'Announcement updated.';
    } else {
        $stmt = $conn->prepare('INSERT INTO eswasa_announcements (title, description, announcement_type, published_date, external_link, file_path) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $title, $description, $announcement_type, $published_date, $external_link, $file_path);
        $msg = 'Announcement added.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('danger', 'Database error: ' . $conn->error);
    }
    if ($stmt) $stmt->close();
    header('Location: index.php?page=announcements_edit.php&tab=list');
    exit;
}

// ── GET: DELETE ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('SELECT file_path FROM eswasa_announcements WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['file_path'])) {
        $file_to_delete = __DIR__ . '/../uploads/' . $row['file_path'];
        if (is_file($file_to_delete)) @unlink($file_to_delete);
    }

    $del = $conn->prepare('DELETE FROM eswasa_announcements WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    set_flash('success', 'Announcement deleted.');
    header('Location: index.php?page=announcements_edit.php&tab=list');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$announcements = $conn->query('SELECT * FROM eswasa_announcements ORDER BY published_date DESC');

$edit_ann = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM eswasa_announcements WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_ann = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$pc = pc_get_many($conn, $announcements_keys, $announcements_defaults);

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'list';
if ($edit_ann) $active_tab = 'list';

function ann_type_label_admin(string $t): string {
    return [
        'news'    => 'News',
        'update'  => 'Update',
        'closure' => 'Closure',
        'event'   => 'Event',
        'policy'  => 'Policy',
    ][$t] ?? ucfirst($t);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Announcements</h1>
    <div class="d-flex gap-2">
        <a href="../announcements.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
            + Add Announcement
        </button>
    </div>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'list' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-list" type="button" role="tab">
            Announcements
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'content' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">
            Page Content
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- ========== TAB: Announcements ========== -->
    <div class="tab-pane fade <?= $active_tab === 'list' ? 'show active' : '' ?>" id="tab-list" role="tabpanel">

        <?php if ($edit_ann): ?>
            <div class="card mb-4">
                <div class="card-header">Edit Announcement</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $edit_ann['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Title *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($edit_ann['title']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description *</label>
                            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($edit_ann['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Type *</label>
                                <select name="announcement_type" class="form-select" required>
                                    <?php foreach (['news','update','closure','event','policy'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $edit_ann['announcement_type'] === $opt ? 'selected' : '' ?>>
                                            <?= ann_type_label_admin($opt) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Published Date *</label>
                                <input type="date" name="published_date" class="form-control" required
                                       value="<?= htmlspecialchars($edit_ann['published_date']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">External Link (Optional)</label>
                            <input type="url" name="external_link" class="form-control"
                                   value="<?= htmlspecialchars($edit_ann['external_link'] ?? '') ?>"
                                   placeholder="https://example.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">File (Optional)</label>
                            <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                            <?php if (!empty($edit_ann['file_path'])): ?>
                                <div class="mt-2">
                                    <a href="uploads/<?= htmlspecialchars($edit_ann['file_path']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i>View Current File
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">PDF / JPG / PNG / WEBP, up to 25 MB. Leave blank to keep current file.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Announcement</button>
                            <a href="index.php?page=announcements_edit.php&tab=list" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                All Announcements (<?= $announcements ? $announcements->num_rows : 0 ?>)
            </div>
            <div class="card-body">
                <?php if ($announcements && $announcements->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Published</th>
                                    <th>File / Link</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($a = $announcements->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($a['title']) ?></td>
                                        <td><?= ann_type_label_admin($a['announcement_type']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($a['published_date'])) ?></td>
                                        <td>
                                            <?php if (!empty($a['file_path'])): ?>
                                                <a href="uploads/<?= htmlspecialchars($a['file_path']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">View File</a>
                                            <?php elseif (!empty($a['external_link'])): ?>
                                                <a href="<?= htmlspecialchars($a['external_link']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">External Link</a>
                                            <?php else: ?>
                                                &mdash;
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=announcements_edit.php&tab=list&edit=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=announcements_edit.php&delete=<?= $a['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this announcement?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No announcements yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">Add your first announcement</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the Announcements archive page (breadcrumb, intro card, section heading, empty state). Announcements themselves are managed on the other tab.
        </p>

        <form method="POST">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="announcements_breadcrumb_title" class="form-control" value="<?= pc_h($pc['announcements_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">"Home" link label</label>
                            <input type="text" name="announcements_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['announcements_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current page label</label>
                            <input type="text" name="announcements_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['announcements_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Intro Card</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="announcements_intro_title" class="form-control" value="<?= pc_h($pc['announcements_intro_title']) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="announcements_intro_body" class="form-control" rows="6"><?= pc_h($pc['announcements_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Section Heading</h5>
                    <div class="mb-0">
                        <label class="form-label">Heading above the history list</label>
                        <input type="text" name="announcements_section_title" class="form-control" value="<?= pc_h($pc['announcements_section_title']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Empty State</h5>
                    <div class="mb-0">
                        <label class="form-label">Message shown when no announcements exist</label>
                        <input type="text" name="announcements_empty_state" class="form-control" value="<?= pc_h($pc['announcements_empty_state']) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_announcements_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAnnouncementModalLabel">Add New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="announcement_type" class="form-select" required>
                                <?php foreach (['news','update','closure','event','policy'] as $opt): ?>
                                    <option value="<?= $opt ?>"><?= ann_type_label_admin($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">External Link (Optional)</label>
                        <input type="url" name="external_link" class="form-control" placeholder="https://example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File (Optional)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <div class="form-text">PDF / JPG / PNG / WEBP, up to 25 MB. Filenames are sanitized automatically.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

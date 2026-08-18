<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_policies.php';

// ── PDF upload helper (sanitize + uniquify + MIME-check) ──────────
function policies_upload_pdf(string $field, string $upload_dir, int $max_bytes = 26214400): array {
    if (empty($_FILES[$field]['name'])) {
        return ['ok' => true, 'path' => null, 'error' => ''];
    }
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Upload failed.'];
    }
    if ($_FILES[$field]['size'] > $max_bytes) {
        return ['ok' => false, 'path' => null, 'error' => 'PDF exceeds 25 MB.'];
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $original = $_FILES[$field]['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return ['ok' => false, 'path' => null, 'error' => 'Only PDF files are allowed.'];
    }

    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if ($mime && !in_array(strtolower($mime), ['application/pdf', 'application/x-pdf', 'binary/octet-stream'], true)) {
            return ['ok' => false, 'path' => null, 'error' => 'File does not appear to be a valid PDF.'];
        }
    }

    $stem = pathinfo($original, PATHINFO_FILENAME);
    $stem = strtolower($stem);
    $stem = preg_replace('/[^a-z0-9]+/', '-', $stem);
    $stem = trim($stem, '-');
    if ($stem === '') $stem = 'policy';
    if (strlen($stem) > 60) $stem = substr($stem, 0, 60);

    $unique = $stem . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.pdf';

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            return ['ok' => false, 'path' => null, 'error' => 'Could not create uploads directory.'];
        }
    }

    $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $unique;
    if (!move_uploaded_file($tmp, $target)) {
        return ['ok' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    }

    return ['ok' => true, 'path' => 'admin/uploads/policies/' . $unique, 'error' => ''];
}

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_policies_content'])) {
    $kv = [];
    foreach ($policies_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=policies_edit.php&tab=content');
    exit;
}

// ── POST: CREATE / UPDATE policy ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = pc_strip_text($_POST['title'] ?? '');
    $description = pc_strip_text($_POST['description'] ?? '');
    $category = pc_strip_text($_POST['category'] ?? 'General') ?: 'General';
    $icon = pc_strip_text($_POST['icon'] ?? 'fa-file-alt') ?: 'fa-file-alt';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_internal = !empty($_POST['is_internal']) ? 1 : 0;
    $internal_path = trim($_POST['internal_path'] ?? '');
    $existing_file_path = trim($_POST['existing_file_path'] ?? '');
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$description) {
        set_flash('danger', 'Title and Description are required.');
        header('Location: index.php?page=policies_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }

    // Determine file_path based on internal vs PDF
    $file_path = $existing_file_path; // default: keep existing
    if ($is_internal) {
        if ($internal_path === '') {
            set_flash('danger', 'Internal page URL is required for internal policies.');
            header('Location: index.php?page=policies_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
            exit;
        }
        $file_path = $internal_path;
    } else {
        $upload = policies_upload_pdf('file', __DIR__ . '/../uploads/policies/');
        if (!$upload['ok']) {
            set_flash('danger', $upload['error']);
            header('Location: index.php?page=policies_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
            exit;
        }
        if ($upload['path']) {
            $file_path = $upload['path'];
        }
    }

    if (!$file_path) {
        set_flash('danger', 'A PDF file (or internal page URL) is required.');
        header('Location: index.php?page=policies_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }

    if ($id) {
        // If a new PDF was uploaded and the previous file lived in admin/uploads/policies/, remove it.
        if (!$is_internal && !empty($upload['path'])) {
            $old = $conn->prepare('SELECT file_path FROM eswasa_policies WHERE id = ?');
            $old->bind_param('i', $id);
            $old->execute();
            $oldRow = $old->get_result()->fetch_assoc();
            $old->close();
            if ($oldRow && strpos($oldRow['file_path'], 'admin/uploads/policies/') === 0) {
                $oldFile = __DIR__ . '/../../' . $oldRow['file_path'];
                if (is_file($oldFile)) @unlink($oldFile);
            }
        }
        $stmt = $conn->prepare('UPDATE eswasa_policies SET title = ?, description = ?, file_path = ?, icon = ?, category = ?, sort_order = ?, is_internal = ? WHERE id = ?');
        $stmt->bind_param('sssssiii', $title, $description, $file_path, $icon, $category, $sort_order, $is_internal, $id);
        $msg = 'Policy updated.';
    } else {
        $stmt = $conn->prepare('INSERT INTO eswasa_policies (title, description, file_path, icon, category, sort_order, is_internal) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssii', $title, $description, $file_path, $icon, $category, $sort_order, $is_internal);
        $msg = 'Policy added.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('danger', 'Database error: ' . $conn->error);
    }
    if ($stmt) $stmt->close();
    header('Location: index.php?page=policies_edit.php&tab=list');
    exit;
}

// ── GET: DELETE ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('SELECT file_path FROM eswasa_policies WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Only remove file if it was admin-uploaded (lives under admin/uploads/policies/)
    if ($row && strpos($row['file_path'] ?? '', 'admin/uploads/policies/') === 0) {
        $fileFs = __DIR__ . '/../../' . $row['file_path'];
        if (is_file($fileFs)) @unlink($fileFs);
    }

    $del = $conn->prepare('DELETE FROM eswasa_policies WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    set_flash('success', 'Policy deleted.');
    header('Location: index.php?page=policies_edit.php&tab=list');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$policies = $conn->query('SELECT * FROM eswasa_policies ORDER BY sort_order ASC, id ASC');

$edit_policy = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM eswasa_policies WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_policy = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$pc = pc_get_many($conn, $policies_keys, $policies_defaults);

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'list';
if ($edit_policy) $active_tab = 'list';

// Common Font Awesome icons for the datalist
$icon_suggestions = [
    'fa-balance-scale','fa-comment-alt','fa-gavel','fa-certificate',
    'fa-info-circle','fa-clipboard-check','fa-ban','fa-expand-arrows-alt',
    'fa-tasks','fa-search','fa-user-shield','fa-file-signature',
    'fa-file-pdf','fa-file-alt','fa-shield-alt','fa-handshake',
];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Policies</h1>
    <div class="d-flex gap-2">
        <a href="../policies.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPolicyModal">
            + Add Policy
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

<datalist id="iconSuggestions">
    <?php foreach ($icon_suggestions as $i): ?>
        <option value="<?= $i ?>"></option>
    <?php endforeach; ?>
</datalist>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'list' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-list" type="button" role="tab">
            Documents
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

    <!-- ========== TAB: Documents ========== -->
    <div class="tab-pane fade <?= $active_tab === 'list' ? 'show active' : '' ?>" id="tab-list" role="tabpanel">

        <?php if ($edit_policy):
            $editIsInternal = (int)$edit_policy['is_internal'] === 1;
        ?>
            <div class="card mb-4">
                <div class="card-header">Edit Policy</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= (int)$edit_policy['id'] ?>">
                        <input type="hidden" name="existing_file_path" value="<?= htmlspecialchars($edit_policy['file_path']) ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Title *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($edit_policy['title']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description *</label>
                            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_policy['description']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <input type="text" name="category" class="form-control"
                                       value="<?= htmlspecialchars($edit_policy['category']) ?>">
                                <div class="form-text">Free text — e.g. Certification, Customer Care, Information.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Icon</label>
                                <input type="text" name="icon" class="form-control" list="iconSuggestions"
                                       value="<?= htmlspecialchars($edit_policy['icon']) ?>">
                                <div class="form-text">Font Awesome class, e.g. <code>fa-balance-scale</code>.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                       value="<?= (int)$edit_policy['sort_order'] ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_internal" id="edit_is_internal" class="form-check-input" value="1"
                                       <?= $editIsInternal ? 'checked' : '' ?>>
                                <label for="edit_is_internal" class="form-check-label">This is an internal page (not a PDF)</label>
                            </div>
                        </div>
                        <div class="mb-3 js-internal-path" style="<?= $editIsInternal ? '' : 'display:none;' ?>">
                            <label class="form-label fw-bold">Internal page URL</label>
                            <input type="text" name="internal_path" class="form-control"
                                   value="<?= $editIsInternal ? htmlspecialchars($edit_policy['file_path']) : '' ?>"
                                   placeholder="e.g. privacy.php">
                        </div>
                        <div class="mb-3 js-pdf-upload" style="<?= $editIsInternal ? 'display:none;' : '' ?>">
                            <label class="form-label fw-bold">PDF File</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf,.pdf">
                            <?php if (!$editIsInternal && !empty($edit_policy['file_path'])): ?>
                                <div class="mt-2 small">
                                    Current:
                                    <a href="../<?= htmlspecialchars($edit_policy['file_path']) ?>" target="_blank" rel="noopener">
                                        <?= htmlspecialchars(basename($edit_policy['file_path'])) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">PDF only, up to 25 MB. Leave blank to keep the current file.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Policy</button>
                            <a href="index.php?page=policies_edit.php&tab=list" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                All Policies (<?= $policies ? $policies->num_rows : 0 ?>)
            </div>
            <div class="card-body">
                <?php if ($policies && $policies->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Order</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>File / Page</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $policies->fetch_assoc()):
                                    $internal = (int)$p['is_internal'] === 1;
                                ?>
                                    <tr>
                                        <td><?= (int)$p['sort_order'] ?></td>
                                        <td><i class="fas <?= htmlspecialchars($p['icon']) ?> me-2" style="color: #2B3388;"></i><?= htmlspecialchars($p['title']) ?></td>
                                        <td><?= htmlspecialchars($p['category']) ?></td>
                                        <td>
                                            <?php if ($internal): ?>
                                                <span class="badge bg-secondary">Internal page</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">PDF</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../<?= htmlspecialchars($p['file_path']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                                <?= $internal ? 'View page' : 'View PDF' ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="index.php?page=policies_edit.php&tab=list&edit=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=policies_edit.php&delete=<?= (int)$p['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this policy?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No policies yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addPolicyModal">Add your first policy</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the Policies page (breadcrumb, section heading, intro card, empty state). Documents themselves are managed on the other tab.
        </p>

        <form method="POST">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="policies_breadcrumb_title" class="form-control" value="<?= pc_h($pc['policies_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">"Home" label</label>
                            <input type="text" name="policies_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['policies_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Parent label</label>
                            <input type="text" name="policies_breadcrumb_parent_label" class="form-control" value="<?= pc_h($pc['policies_breadcrumb_parent_label']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Current label</label>
                            <input type="text" name="policies_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['policies_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Section Heading &amp; Intro</h5>
                    <div class="mb-3">
                        <label class="form-label">Section heading</label>
                        <input type="text" name="policies_section_title" class="form-control" value="<?= pc_h($pc['policies_section_title']) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Intro body (separate paragraphs with a blank line)</label>
                        <textarea name="policies_intro_body" class="form-control" rows="4"><?= pc_h($pc['policies_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Empty State</h5>
                    <div class="mb-0">
                        <label class="form-label">Message shown when no policies are published</label>
                        <input type="text" name="policies_empty_state" class="form-control" value="<?= pc_h($pc['policies_empty_state']) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_policies_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Add Policy Modal -->
<div class="modal fade" id="addPolicyModal" tabindex="-1" aria-labelledby="addPolicyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPolicyModalLabel">Add New Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="existing_file_path" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Category</label>
                            <input type="text" name="category" class="form-control" value="General">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Icon</label>
                            <input type="text" name="icon" class="form-control" list="iconSuggestions" value="fa-file-alt">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_internal" id="add_is_internal" class="form-check-input" value="1">
                            <label for="add_is_internal" class="form-check-label">This is an internal page (not a PDF)</label>
                        </div>
                    </div>
                    <div class="mb-3 js-internal-path" style="display:none;">
                        <label class="form-label fw-bold">Internal page URL</label>
                        <input type="text" name="internal_path" class="form-control" placeholder="e.g. privacy.php">
                    </div>
                    <div class="mb-3 js-pdf-upload">
                        <label class="form-label fw-bold">PDF File *</label>
                        <input type="file" name="file" class="form-control" accept="application/pdf,.pdf">
                        <div class="form-text">PDF only, up to 25 MB. Filenames are sanitized automatically.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Policy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle the Internal-URL vs PDF-upload fields based on the "is_internal" checkbox.
(function() {
    function wireForm(scope) {
        var cb = scope.querySelector('input[name="is_internal"]');
        if (!cb) return;
        var pdfBlock = scope.querySelector('.js-pdf-upload');
        var intBlock = scope.querySelector('.js-internal-path');
        function sync() {
            if (cb.checked) {
                if (pdfBlock) pdfBlock.style.display = 'none';
                if (intBlock) intBlock.style.display = '';
            } else {
                if (pdfBlock) pdfBlock.style.display = '';
                if (intBlock) intBlock.style.display = 'none';
            }
        }
        cb.addEventListener('change', sync);
    }
    document.querySelectorAll('form').forEach(wireForm);
})();
</script>

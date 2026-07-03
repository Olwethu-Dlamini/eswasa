<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_publications.php';

// ── PDF upload helper (sanitize + uniquify + MIME-check) ──────────
function publications_upload_pdf(string $field, string $upload_dir, int $max_bytes = 26214400): array {
    // returns ['ok'=>bool, 'path'=>'publications/foo.pdf', 'error'=>string]
    if (empty($_FILES[$field]['name'])) {
        return ['ok' => true, 'path' => null, 'error' => ''];
    }
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Upload failed.'];
    }
    if ($_FILES[$field]['size'] > $max_bytes) {
        return ['ok' => false, 'path' => null, 'error' => 'PDF exceeds size limit (25 MB).'];
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $original = $_FILES[$field]['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return ['ok' => false, 'path' => null, 'error' => 'Only PDF files are allowed.'];
    }

    // MIME sniff (best-effort — falls back to extension check above)
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if ($mime && !in_array(strtolower($mime), ['application/pdf', 'application/x-pdf', 'binary/octet-stream'], true)) {
            return ['ok' => false, 'path' => null, 'error' => 'File does not appear to be a valid PDF.'];
        }
    }

    // Sanitize stem: keep alnum + dashes, collapse whitespace + underscores
    $stem = pathinfo($original, PATHINFO_FILENAME);
    $stem = strtolower($stem);
    $stem = preg_replace('/[^a-z0-9]+/', '-', $stem);
    $stem = trim($stem, '-');
    if ($stem === '') $stem = 'publication';
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

    return ['ok' => true, 'path' => 'publications/' . $unique, 'error' => ''];
}

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_publications_content'])) {
    $kv = [];
    foreach ($publications_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=publications_edit.php&tab=content');
    exit;
}

// ── POST: FOLDER actions (create / update / delete custom groups) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_action'])) {
    $fa = $_POST['folder_action'];

    if ($fa === 'create') {
        $name = pc_strip_text($_POST['name'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($name === '') {
            set_flash('error', 'Folder name is required.');
        } else {
            $stmt = $conn->prepare('INSERT INTO eswasa_publication_groups (name, type_key, sort_order, is_system) VALUES (?, NULL, ?, 0)');
            $stmt->bind_param('si', $name, $sort);
            set_flash($stmt->execute() ? 'success' : 'error', $stmt->error ? 'Database error: ' . $conn->error : 'Folder created.');
            $stmt->close();
        }
    } elseif ($fa === 'update') {
        // Rename / reorder. Allowed for system and custom groups alike.
        $id   = (int)($_POST['id'] ?? 0);
        $name = pc_strip_text($_POST['name'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        if ($id > 0 && $name !== '') {
            $stmt = $conn->prepare('UPDATE eswasa_publication_groups SET name = ?, sort_order = ? WHERE id = ?');
            $stmt->bind_param('sii', $name, $sort, $id);
            set_flash($stmt->execute() ? 'success' : 'error', $stmt->error ? 'Database error: ' . $conn->error : 'Folder updated.');
            $stmt->close();
        } else {
            set_flash('error', 'Folder name is required.');
        }
    } elseif ($fa === 'delete') {
        // Only custom (non-system) folders can be deleted. Their documents
        // fall back to auto-by-type (group_id cleared); files are untouched.
        $id  = (int)($_POST['id'] ?? 0);
        $chk = $conn->prepare('SELECT is_system FROM eswasa_publication_groups WHERE id = ?');
        $chk->bind_param('i', $id);
        $chk->execute();
        $row = $chk->get_result()->fetch_assoc();
        $chk->close();
        if ($row && (int)$row['is_system'] === 0) {
            $u = $conn->prepare('UPDATE eswasa_publications SET group_id = NULL WHERE group_id = ?');
            $u->bind_param('i', $id);
            $u->execute();
            $u->close();
            $d = $conn->prepare('DELETE FROM eswasa_publication_groups WHERE id = ?');
            $d->bind_param('i', $id);
            $d->execute();
            $d->close();
            set_flash('success', 'Folder deleted. Its documents are back to Auto (by type).');
        } else {
            set_flash('error', 'System type folders cannot be deleted.');
        }
    }
    header('Location: index.php?page=publications_edit.php&tab=folders');
    exit;
}

// ── POST: CREATE / UPDATE publication ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = pc_strip_text($_POST['title'] ?? '');
    $description = pc_strip_text($_POST['description'] ?? '');
    $pub_type = $_POST['pub_type'] ?? 'report';
    $published_date = $_POST['published_date'] ?? '';
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $allowed_types = ['standard', 'report', 'guidance', 'newsletter', 'annual_report'];
    if (!in_array($pub_type, $allowed_types, true)) $pub_type = 'report';

    // Custom-folder assignment. Empty = "Auto (by type)" => NULL. Validate the
    // id is an existing custom (non-system) folder; otherwise fall back to NULL.
    $group_id = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
    if ($group_id !== null) {
        $gchk = $conn->prepare('SELECT id FROM eswasa_publication_groups WHERE id = ? AND is_system = 0');
        $gchk->bind_param('i', $group_id);
        $gchk->execute();
        if (!$gchk->get_result()->fetch_assoc()) $group_id = null;
        $gchk->close();
    }

    if (!$title || !$published_date || !$description) {
        set_flash('error', 'Title, Description, and Published Date are required.');
        header('Location: index.php?page=publications_edit.php&tab=documents' . ($id ? "&edit=$id" : ''));
        exit;
    }

    $upload = publications_upload_pdf('file', __DIR__ . '/../uploads/publications/');
    if (!$upload['ok']) {
        set_flash('error', $upload['error']);
        header('Location: index.php?page=publications_edit.php&tab=documents' . ($id ? "&edit=$id" : ''));
        exit;
    }
    $file_path = $upload['path']; // null when no new file uploaded

    if ($id) {
        // If a new PDF was uploaded on edit, remove the old one
        if ($file_path) {
            $old = $conn->prepare('SELECT file_path FROM eswasa_publications WHERE id = ?');
            $old->bind_param('i', $id);
            $old->execute();
            $oldRow = $old->get_result()->fetch_assoc();
            $old->close();
            if ($oldRow && !empty($oldRow['file_path'])) {
                $oldFile = __DIR__ . '/../uploads/' . $oldRow['file_path'];
                if (is_file($oldFile)) @unlink($oldFile);
            }
            $stmt = $conn->prepare('UPDATE eswasa_publications SET title = ?, description = ?, pub_type = ?, group_id = ?, file_path = ?, published_date = ? WHERE id = ?');
            $stmt->bind_param('sssissi', $title, $description, $pub_type, $group_id, $file_path, $published_date, $id);
        } else {
            $stmt = $conn->prepare('UPDATE eswasa_publications SET title = ?, description = ?, pub_type = ?, group_id = ?, published_date = ? WHERE id = ?');
            $stmt->bind_param('sssisi', $title, $description, $pub_type, $group_id, $published_date, $id);
        }
        $msg = 'Publication updated.';
    } else {
        if (!$file_path) {
            set_flash('error', 'A PDF file is required when creating a publication.');
            header('Location: index.php?page=publications_edit.php&tab=documents');
            exit;
        }
        $stmt = $conn->prepare('INSERT INTO eswasa_publications (title, description, pub_type, group_id, file_path, published_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssiss', $title, $description, $pub_type, $group_id, $file_path, $published_date);
        $msg = 'Publication added.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }
    if ($stmt) $stmt->close();
    header('Location: index.php?page=publications_edit.php&tab=documents');
    exit;
}

// ── GET: DELETE ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('SELECT file_path FROM eswasa_publications WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && !empty($row['file_path'])) {
        $file_to_delete = __DIR__ . '/../uploads/' . $row['file_path'];
        if (is_file($file_to_delete)) @unlink($file_to_delete);
    }

    $del = $conn->prepare('DELETE FROM eswasa_publications WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    set_flash('success', 'Publication deleted.');
    header('Location: index.php?page=publications_edit.php&tab=documents');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$publications = $conn->query('SELECT * FROM eswasa_publications ORDER BY published_date DESC');

$edit_pub = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM eswasa_publications WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_pub = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$pc = pc_get_many($conn, $publications_keys, $publications_defaults);

// Folders (groups) with a live document count per group: a publication counts
// toward a group if it is explicitly assigned (group_id) OR auto-matched by type.
$all_groups = [];
$custom_groups = [];
$gq = $conn->query(
    'SELECT g.*, (
        SELECT COUNT(*) FROM eswasa_publications p
        WHERE p.group_id = g.id
           OR (p.group_id IS NULL AND p.pub_type = g.type_key COLLATE utf8mb4_unicode_ci)
     ) AS pub_count
     FROM eswasa_publication_groups g
     ORDER BY g.sort_order ASC, g.id ASC'
);
if ($gq) {
    while ($g = $gq->fetch_assoc()) {
        $all_groups[] = $g;
        if (!(int)$g['is_system']) $custom_groups[] = $g;
    }
}

$active_tab = in_array(($_GET['tab'] ?? ''), ['content', 'folders', 'documents'], true) ? $_GET['tab'] : 'documents';
if ($edit_pub) $active_tab = 'documents';

function pub_type_label_admin(string $t): string {
    return [
        'standard'      => 'Standard',
        'report'        => 'Report',
        'guidance'      => 'Guidance Document',
        'newsletter'    => 'Newsletter',
        'annual_report' => 'Annual Report',
    ][$t] ?? ucfirst($t);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Publications</h1>
    <div class="d-flex gap-2">
        <a href="../publications.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
            + Add Publication
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
        <button class="nav-link <?= $active_tab === 'documents' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-documents" type="button" role="tab">
            Documents
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'folders' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-folders" type="button" role="tab">
            Folders
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
    <div class="tab-pane fade <?= $active_tab === 'documents' ? 'show active' : '' ?>" id="tab-documents" role="tabpanel">

        <?php if ($edit_pub): ?>
            <div class="card mb-4">
                <div class="card-header">Edit Publication</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $edit_pub['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Title *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($edit_pub['title']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description *</label>
                            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_pub['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Type *</label>
                                <select name="pub_type" class="form-select" required>
                                    <?php foreach (['standard','report','guidance','newsletter','annual_report'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $edit_pub['pub_type'] === $opt ? 'selected' : '' ?>>
                                            <?= pub_type_label_admin($opt) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Published Date *</label>
                                <input type="date" name="published_date" class="form-control" required
                                       value="<?= htmlspecialchars($edit_pub['published_date']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Folder</label>
                            <select name="group_id" class="form-select">
                                <option value="">Auto (by type)</option>
                                <?php foreach ($custom_groups as $cg): ?>
                                    <option value="<?= (int)$cg['id'] ?>" <?= ((int)($edit_pub['group_id'] ?? 0) === (int)$cg['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cg['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">"Auto" groups this document under its Type. Pick a custom folder to override.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">PDF File</label>
                            <input type="file" name="file" class="form-control" accept="application/pdf,.pdf">
                            <?php if (!empty($edit_pub['file_path'])): ?>
                                <div class="mt-2">
                                    <a href="uploads/<?= htmlspecialchars($edit_pub['file_path']) ?>"
                                       target="_blank" rel="noopener"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf me-1"></i>View Current PDF
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">Only PDF files. Leave blank to keep the current file.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Publication</button>
                            <a href="index.php?page=publications_edit.php&tab=documents" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                All Publications (<?= $publications ? $publications->num_rows : 0 ?>)
            </div>
            <div class="card-body">
                <?php if ($publications && $publications->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Published</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($p = $publications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($p['title']) ?></td>
                                        <td><?= pub_type_label_admin($p['pub_type']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($p['published_date'])) ?></td>
                                        <td>
                                            <?php if (!empty($p['file_path'])): ?>
                                                <a href="uploads/<?= htmlspecialchars($p['file_path']) ?>"
                                                   target="_blank" rel="noopener"
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file-pdf me-1"></i>View PDF
                                                </a>
                                            <?php else: ?>
                                                &mdash;
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?page=publications_edit.php&tab=documents&edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=publications_edit.php&delete=<?= $p['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this publication?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No publications yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addPublicationModal">Add your first publication</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Folders ========== -->
    <div class="tab-pane fade <?= $active_tab === 'folders' ? 'show active' : '' ?>" id="tab-folders" role="tabpanel">
        <p class="text-muted small mb-3">
            Sections shown on the Publications page, in order. <strong>System</strong> folders auto-collect documents by their Type and can be renamed/reordered but not deleted. Add your own <strong>custom</strong> folders and assign documents to them on the Documents tab. A folder with no documents is hidden on the public page.
        </p>

        <div class="card mb-4">
            <div class="card-header">Add Custom Folder</div>
            <div class="card-body">
                <form method="POST" class="row g-2 align-items-end">
                    <input type="hidden" name="folder_action" value="create">
                    <div class="col-md-7">
                        <label class="form-label fw-bold">Folder name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Financial Statements" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Order</label>
                        <input type="number" name="sort_order" class="form-control" value="100">
                        <div class="form-text">Lower shows first.</div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">All Folders (<?= count($all_groups) ?>)</div>
            <div class="card-body">
                <div class="d-none d-md-flex align-items-center gap-2 px-1 pb-2 mb-1 border-bottom small text-muted fw-bold">
                    <span style="width:80px">Order</span>
                    <span class="flex-grow-1">Name</span>
                    <span style="width:110px">Kind</span>
                    <span style="width:50px">Docs</span>
                    <span style="width:140px">Actions</span>
                </div>
                <?php foreach ($all_groups as $g): ?>
                    <div class="d-flex align-items-center gap-2 flex-wrap border-bottom py-2">
                        <!-- update form: order + name + save -->
                        <form method="POST" class="d-flex align-items-center gap-2 flex-grow-1 mb-0" style="min-width:0">
                            <input type="hidden" name="folder_action" value="update">
                            <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                            <input type="number" name="sort_order" class="form-control form-control-sm"
                                   value="<?= (int)$g['sort_order'] ?>" style="width:80px" aria-label="Sort order">
                            <input type="text" name="name" class="form-control form-control-sm flex-grow-1"
                                   value="<?= htmlspecialchars($g['name']) ?>" required aria-label="Folder name" style="min-width:120px">
                            <span style="width:110px">
                                <?php if ((int)$g['is_system']): ?>
                                    <span class="badge bg-secondary">System</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Custom</span>
                                <?php endif; ?>
                            </span>
                            <span style="width:50px" class="text-muted"><?= (int)$g['pub_count'] ?></span>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        </form>
                        <!-- separate delete form (siblings, never nested) -->
                        <?php if (!(int)$g['is_system']): ?>
                            <form method="POST" class="mb-0"
                                  onsubmit="return confirm('Delete this folder? Its documents return to Auto (by type). Files are not deleted.');">
                                <input type="hidden" name="folder_action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$g['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the Publications page (breadcrumb, intro card, section heading, empty state). Documents themselves are managed on the other tab.
        </p>

        <form method="POST">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="publications_breadcrumb_title" class="form-control" value="<?= pc_h($pc['publications_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">"Home" link label</label>
                            <input type="text" name="publications_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['publications_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current page label</label>
                            <input type="text" name="publications_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['publications_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Intro Card</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="publications_intro_title" class="form-control" value="<?= pc_h($pc['publications_intro_title']) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="publications_intro_body" class="form-control" rows="6"><?= pc_h($pc['publications_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Section Heading</h5>
                    <div class="mb-0">
                        <label class="form-label">Heading above the documents list</label>
                        <input type="text" name="publications_section_title" class="form-control" value="<?= pc_h($pc['publications_section_title']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Empty State</h5>
                    <div class="mb-0">
                        <label class="form-label">Message shown when no publications are available</label>
                        <input type="text" name="publications_empty_state" class="form-control" value="<?= pc_h($pc['publications_empty_state']) ?>">
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_publications_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Add Publication Modal -->
<div class="modal fade" id="addPublicationModal" tabindex="-1" aria-labelledby="addPublicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPublicationModalLabel">Add New Publication</h5>
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
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="pub_type" class="form-select" required>
                                <?php foreach (['standard','report','guidance','newsletter','annual_report'] as $opt): ?>
                                    <option value="<?= $opt ?>"><?= pub_type_label_admin($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Folder</label>
                        <select name="group_id" class="form-select">
                            <option value="">Auto (by type)</option>
                            <?php foreach ($custom_groups as $cg): ?>
                                <option value="<?= (int)$cg['id'] ?>"><?= htmlspecialchars($cg['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">"Auto" groups this document under its Type. Pick a custom folder to override.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">PDF File *</label>
                        <input type="file" name="file" class="form-control" accept="application/pdf,.pdf" required>
                        <div class="form-text">PDF only, up to 25 MB. Filenames are sanitized automatically.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

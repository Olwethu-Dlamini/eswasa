<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_tenders.php';

// ── PDF upload helper (accepts one entry from a doc_file[] array) ──
function tenders_save_pdf(array $f, string $upload_dir, int $max_bytes = 26214400): array {
    // $f keys: name, tmp_name, size, error. Returns ['ok','path','error'].
    if (empty($f['name'])) {
        return ['ok' => true, 'path' => null, 'error' => ''];
    }
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'path' => null, 'error' => 'Upload failed.'];
    }
    if ($f['size'] > $max_bytes) {
        return ['ok' => false, 'path' => null, 'error' => 'PDF exceeds size limit (25 MB).'];
    }
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return ['ok' => false, 'path' => null, 'error' => 'Only PDF files are allowed.'];
    }
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        if ($mime && !in_array(strtolower($mime), ['application/pdf', 'application/x-pdf', 'binary/octet-stream'], true)) {
            return ['ok' => false, 'path' => null, 'error' => 'A file does not appear to be a valid PDF.'];
        }
    }
    $stem = strtolower(pathinfo($f['name'], PATHINFO_FILENAME));
    $stem = preg_replace('/[^a-z0-9]+/', '-', $stem);
    $stem = trim($stem, '-');
    if ($stem === '') $stem = 'tender';
    if (strlen($stem) > 60) $stem = substr($stem, 0, 60);
    $unique = $stem . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.pdf';

    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
            return ['ok' => false, 'path' => null, 'error' => 'Could not create uploads directory.'];
        }
    }
    $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $unique;
    if (!move_uploaded_file($f['tmp_name'], $target)) {
        return ['ok' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    }
    return ['ok' => true, 'path' => 'tenders/' . $unique, 'error' => ''];
}

// Save any uploaded documents (doc_file[] + doc_label[]) for a tender.
function tenders_process_documents(mysqli $conn, int $tender_id): ?string {
    if (empty($_FILES['doc_file']) || !is_array($_FILES['doc_file']['name'])) return null;
    $labels = $_POST['doc_label'] ?? [];
    $count  = count($_FILES['doc_file']['name']);
    $order  = 0;
    for ($i = 0; $i < $count; $i++) {
        if (empty($_FILES['doc_file']['name'][$i])) continue;
        $f = [
            'name'     => $_FILES['doc_file']['name'][$i],
            'tmp_name' => $_FILES['doc_file']['tmp_name'][$i],
            'size'     => $_FILES['doc_file']['size'][$i],
            'error'    => $_FILES['doc_file']['error'][$i],
        ];
        $up = tenders_save_pdf($f, __DIR__ . '/../uploads/tenders/');
        if (!$up['ok']) return $up['error'];
        $label = pc_strip_text($labels[$i] ?? '');
        if ($label === '') $label = pathinfo($f['name'], PATHINFO_FILENAME);
        $ins = $conn->prepare('INSERT INTO eswasa_tender_documents (tender_id, label, file_path, sort_order) VALUES (?, ?, ?, ?)');
        $ins->bind_param('issi', $tender_id, $label, $up['path'], $order);
        $ins->execute();
        $ins->close();
        $order++;
    }
    return null;
}

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tenders_content'])) {
    $kv = [];
    foreach ($tenders_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=tenders_edit.php&tab=content');
    exit;
}

// ── POST: CREATE / UPDATE tender ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tender'])) {
    $title        = pc_strip_text($_POST['title'] ?? '');
    $reference_no = pc_strip_text($_POST['reference_no'] ?? '');
    $category     = pc_strip_text($_POST['category'] ?? '');
    $description  = pc_strip_text($_POST['description'] ?? '');
    $published    = $_POST['published_date'] ?? '';
    $closing      = $_POST['closing_date'] ?? '';
    $id           = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$description || !$published || !$closing) {
        set_flash('danger', 'Title, Description, Published Date and Closing Date are required.');
        header('Location: index.php?page=tenders_edit.php' . ($id ? "&edit=$id" : ''));
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare('UPDATE eswasa_tenders SET title=?, reference_no=?, category=?, description=?, published_date=?, closing_date=? WHERE id=?');
        $stmt->bind_param('ssssssi', $title, $reference_no, $category, $description, $published, $closing, $id);
        $stmt->execute();
        $stmt->close();
        $tender_id = $id;
        $msg = 'Tender updated.';
    } else {
        $stmt = $conn->prepare('INSERT INTO eswasa_tenders (title, reference_no, category, description, published_date, closing_date) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $title, $reference_no, $category, $description, $published, $closing);
        $stmt->execute();
        $tender_id = $stmt->insert_id;
        $stmt->close();
        $msg = 'Tender added.';
    }

    $docErr = tenders_process_documents($conn, (int)$tender_id);
    if ($docErr) {
        set_flash('danger', $docErr . ' (Tender saved; fix the document and re-upload.)');
        header('Location: index.php?page=tenders_edit.php&edit=' . (int)$tender_id);
        exit;
    }

    set_flash('success', $msg);
    header('Location: index.php?page=tenders_edit.php&edit=' . (int)$tender_id);
    exit;
}

// ── GET: delete a single document ─────────────────────────────────
if (isset($_GET['delete_doc'])) {
    $did = (int)$_GET['delete_doc'];
    $tid = (int)($_GET['edit'] ?? 0);
    $stmt = $conn->prepare('SELECT file_path FROM eswasa_tender_documents WHERE id = ?');
    $stmt->bind_param('i', $did);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row && !empty($row['file_path'])) {
        $file = __DIR__ . '/../uploads/' . $row['file_path'];
        if (is_file($file)) @unlink($file);
    }
    $del = $conn->prepare('DELETE FROM eswasa_tender_documents WHERE id = ?');
    $del->bind_param('i', $did);
    $del->execute();
    $del->close();
    set_flash('success', 'Document removed.');
    header('Location: index.php?page=tenders_edit.php&edit=' . $tid);
    exit;
}

// ── GET: delete a tender (cascade documents + files) ──────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $docs = $conn->prepare('SELECT file_path FROM eswasa_tender_documents WHERE tender_id = ?');
    $docs->bind_param('i', $id);
    $docs->execute();
    $res = $docs->get_result();
    while ($d = $res->fetch_assoc()) {
        if (!empty($d['file_path'])) {
            $file = __DIR__ . '/../uploads/' . $d['file_path'];
            if (is_file($file)) @unlink($file);
        }
    }
    $docs->close();
    $conn->query('DELETE FROM eswasa_tender_documents WHERE tender_id = ' . $id);
    $del = $conn->prepare('DELETE FROM eswasa_tenders WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    set_flash('success', 'Tender deleted.');
    header('Location: index.php?page=tenders_edit.php');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$today = date('Y-m-d');
$tenders = $conn->query('SELECT t.*, (SELECT COUNT(*) FROM eswasa_tender_documents d WHERE d.tender_id = t.id) AS doc_count FROM eswasa_tenders t ORDER BY t.closing_date DESC');

$edit_tender = null;
$edit_docs = [];
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM eswasa_tenders WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_tender = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($edit_tender) {
        $dq = $conn->prepare('SELECT * FROM eswasa_tender_documents WHERE tender_id = ? ORDER BY sort_order ASC, id ASC');
        $dq->bind_param('i', $id);
        $dq->execute();
        $dr = $dq->get_result();
        while ($d = $dr->fetch_assoc()) $edit_docs[] = $d;
        $dq->close();
    }
}

$pc = pc_get_many($conn, $tenders_keys, $tenders_defaults);
$active_tab = in_array(($_GET['tab'] ?? ''), ['content', 'documents'], true) ? $_GET['tab'] : 'documents';
if ($edit_tender) $active_tab = 'documents';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tenders</h1>
    <div class="d-flex gap-2">
        <a href="../tenders.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
        <?php if (!$edit_tender): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTenderModal">+ Add Tender</button>
        <?php endif; ?>
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
        <button class="nav-link <?= $active_tab === 'documents' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-documents" type="button" role="tab">Tenders</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'content' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">Page Content</button>
    </li>
</ul>

<div class="tab-content">

    <!-- ========== TAB: Tenders ========== -->
    <div class="tab-pane fade <?= $active_tab === 'documents' ? 'show active' : '' ?>" id="tab-documents" role="tabpanel">

        <?php if ($edit_tender): ?>
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">Edit Tender — <?= htmlspecialchars($edit_tender['title']) ?></div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="save_tender" value="1">
                        <input type="hidden" name="id" value="<?= (int)$edit_tender['id'] ?>">
                        <?php
                        // Reuse the shared field partial for edit
                        $T = $edit_tender;
                        include __DIR__ . '/_tender_fields.php';
                        ?>

                        <hr>
                        <h6 class="text-muted">Existing documents</h6>
                        <?php if ($edit_docs): ?>
                            <ul class="list-group mb-3">
                                <?php foreach ($edit_docs as $d): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-file-pdf me-2 text-danger"></i><?= htmlspecialchars($d['label']) ?></span>
                                        <span>
                                            <a href="uploads/<?= htmlspecialchars($d['file_path']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">View</a>
                                            <a href="index.php?page=tenders_edit.php&edit=<?= (int)$edit_tender['id'] ?>&delete_doc=<?= (int)$d['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Remove this document?')">Remove</a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted small">No documents yet.</p>
                        <?php endif; ?>

                        <h6 class="text-muted">Add documents</h6>
                        <div id="docRowsEdit"></div>
                        <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="addDocRow('docRowsEdit')">+ Add another document</button>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="index.php?page=tenders_edit.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">All Tenders (<?= $tenders ? $tenders->num_rows : 0 ?>)</div>
            <div class="card-body">
                <?php if ($tenders && $tenders->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Ref</th>
                                    <th>Category</th>
                                    <th>Closing</th>
                                    <th>Status</th>
                                    <th>Docs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($t = $tenders->fetch_assoc()):
                                    $isOpen = $t['closing_date'] >= $today;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($t['title']) ?></td>
                                        <td><?= htmlspecialchars($t['reference_no'] ?? '') ?: '&mdash;' ?></td>
                                        <td><?= htmlspecialchars($t['category'] ?? '') ?: '&mdash;' ?></td>
                                        <td><?= date('Y-m-d', strtotime($t['closing_date'])) ?></td>
                                        <td>
                                            <?php if ($isOpen): ?>
                                                <span class="badge bg-success">Open</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Closed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= (int)$t['doc_count'] ?></td>
                                        <td>
                                            <a href="index.php?page=tenders_edit.php&edit=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=tenders_edit.php&delete=<?= (int)$t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tender and its documents?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No tenders yet. <a href="#" data-bs-toggle="modal" data-bs-target="#addTenderModal">Add your first tender</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">Edit the static text on the Tenders page (breadcrumb, intro card, section headings, empty state).</p>
        <form method="POST">
            <div class="card mb-3"><div class="card-body">
                <h5 class="mb-3">Breadcrumb</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Page Title (banner heading)</label>
                        <input type="text" name="tenders_breadcrumb_title" class="form-control" value="<?= pc_h($pc['tenders_breadcrumb_title']) ?>"></div>
                    <div class="col-md-3"><label class="form-label">"Home" link label</label>
                        <input type="text" name="tenders_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['tenders_breadcrumb_home_label']) ?>"></div>
                    <div class="col-md-3"><label class="form-label">Current page label</label>
                        <input type="text" name="tenders_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['tenders_breadcrumb_current_label']) ?>"></div>
                </div>
            </div></div>
            <div class="card mb-3"><div class="card-body">
                <h5 class="mb-3">Intro Card</h5>
                <div class="mb-3"><label class="form-label">Title</label>
                    <input type="text" name="tenders_intro_title" class="form-control" value="<?= pc_h($pc['tenders_intro_title']) ?>"></div>
                <div class="mb-0"><label class="form-label">Body (separate paragraphs with a blank line)</label>
                    <textarea name="tenders_intro_body" class="form-control" rows="5"><?= pc_h($pc['tenders_intro_body']) ?></textarea></div>
            </div></div>
            <div class="card mb-3"><div class="card-body">
                <h5 class="mb-3">Section Headings</h5>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Open tenders heading</label>
                        <input type="text" name="tenders_open_title" class="form-control" value="<?= pc_h($pc['tenders_open_title']) ?>"></div>
                    <div class="col-md-6"><label class="form-label">Closed tenders heading</label>
                        <input type="text" name="tenders_closed_title" class="form-control" value="<?= pc_h($pc['tenders_closed_title']) ?>"></div>
                </div>
            </div></div>
            <div class="card mb-3"><div class="card-body">
                <h5 class="mb-3">Empty State</h5>
                <div class="mb-0"><label class="form-label">Message shown when there are no open tenders</label>
                    <input type="text" name="tenders_empty_state" class="form-control" value="<?= pc_h($pc['tenders_empty_state']) ?>"></div>
            </div></div>
            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_tenders_content" class="btn btn-primary px-5 shadow-sm"><i class="fas fa-save me-2"></i>Save Page Content</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Tender Modal -->
<div class="modal fade" id="addTenderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Tender</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="save_tender" value="1">
                <div class="modal-body">
                    <?php $T = null; include __DIR__ . '/_tender_fields.php'; ?>
                    <hr>
                    <h6 class="text-muted">Bid documents</h6>
                    <div id="docRowsAdd"></div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDocRow('docRowsAdd')">+ Add another document</button>
                    <div class="form-text">PDF only, up to 25 MB each. You can also add documents later by editing the tender.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Tender</button>
                </div>
            </form>
        </div>
    </div>
</div>

<datalist id="tenderCategories">
    <option value="Goods"></option>
    <option value="Services"></option>
    <option value="Works"></option>
    <option value="Consultancy"></option>
</datalist>

<script>
function addDocRow(containerId) {
    var c = document.getElementById(containerId);
    var row = document.createElement('div');
    row.className = 'row g-2 mb-2 align-items-center';
    row.innerHTML =
        '<div class="col-md-5"><input type="text" name="doc_label[]" class="form-control form-control-sm" placeholder="Label (e.g. Tender Notice)"></div>' +
        '<div class="col-md-6"><input type="file" name="doc_file[]" accept="application/pdf,.pdf" class="form-control form-control-sm"></div>' +
        '<div class="col-md-1"><button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="this.closest(\'.row\').remove()">&times;</button></div>';
    c.appendChild(row);
}
// seed one empty row in each container on load
document.addEventListener('DOMContentLoaded', function () {
    ['docRowsAdd', 'docRowsEdit'].forEach(function (id) { if (document.getElementById(id)) addDocRow(id); });
});
</script>

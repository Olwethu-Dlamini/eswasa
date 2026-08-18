<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ─── Page-level text fields (still in page_content) ────────────
$text_keys = [
    'work_page_title',
    'work_meta_description',
    'work_breadcrumb_crumb_1',
    'work_breadcrumb_crumb_2',
    'work_breadcrumb_title',
    'work_intro_title',
    'work_intro_body',
    'work_section_title',
    'work_cta_1_text', 'work_cta_1_url',
    'work_cta_2_text', 'work_cta_2_url',
];

$status_class_options = [
    'status-published' => 'Published (filled blue)',
    'status-underdev'  => 'Under Development (outline)',
    'status-revision'  => 'Revision (light blue)',
];
$status_class_keys = array_keys($status_class_options);

// ─── POST handlers ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Save the surrounding page text
    if ($action === 'save_work') {
        $kv = [];
        foreach ($text_keys as $k) {
            $kv[$k] = pc_post_value($k);
        }
        $errs = pc_save_many($conn, $kv);
        set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page text saved.');
        redirect_self();
    }

    // Add / Update a programme
    if ($action === 'add' || $action === 'update') {
        $id           = $action === 'update' ? (int)($_POST['id'] ?? 0) : 0;
        $title        = pc_strip_text($_POST['title'] ?? '');
        $url          = trim((string)($_POST['url'] ?? ''));
        $url          = strip_tags($url);
        $details      = pc_strip_text($_POST['details'] ?? '');
        $status_label = pc_strip_text($_POST['status_label'] ?? '');
        $status_class = $_POST['status_class'] ?? 'status-published';
        if (!in_array($status_class, $status_class_keys, true)) $status_class = 'status-published';
        $sort_order   = (int)($_POST['sort_order'] ?? 0);

        if ($title === '') {
            set_flash('danger', 'Title is required.');
            redirect_self();
        }

        if ($action === 'add') {
            $stmt = $conn->prepare(
                "INSERT INTO eswasa_work_programmes
                    (title, url, details, status_label, status_class, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('sssssi', $title, $url, $details, $status_label, $status_class, $sort_order);
            $msg = 'Programme added.';
        } else {
            if ($id <= 0) { set_flash('danger', 'Invalid id.'); redirect_self(); }
            $stmt = $conn->prepare(
                "UPDATE eswasa_work_programmes
                    SET title = ?, url = ?, details = ?, status_label = ?, status_class = ?, sort_order = ?
                  WHERE id = ?"
            );
            $stmt->bind_param('sssssii', $title, $url, $details, $status_label, $status_class, $sort_order, $id);
            $msg = 'Programme updated.';
        }
        $stmt->execute();
        $stmt->close();
        set_flash('success', $msg);
        redirect_self();
    }

    // Delete
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM eswasa_work_programmes WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            set_flash('success', 'Programme deleted.');
        }
        redirect_self();
    }
}

// ─── Load data ──────────────────────────────────────────────────
$pc = pc_get_many($conn, $text_keys);

$programmes = [];
$res = $conn->query("SELECT * FROM eswasa_work_programmes ORDER BY sort_order ASC, id ASC");
if ($res) while ($r = $res->fetch_assoc()) $programmes[] = $r;

// Pre-fill for edit modal
$edit_prog = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid > 0) {
        $s = $conn->prepare("SELECT * FROM eswasa_work_programmes WHERE id = ?");
        $s->bind_param('i', $eid);
        $s->execute();
        $edit_prog = $s->get_result()->fetch_assoc();
        $s->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Work Programmes</h1>
        <small class="text-muted">Manage the page meta, intro, CTAs and the unlimited programme list shown on the public <code>work.php</code> page.</small>
    </div>
    <a href="../work.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<style>
    .wp-toc {
        position: sticky; top: 72px; z-index: 30;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
        padding: 10px 0; margin: 0 0 1rem;
        display: flex; gap: 8px; align-items: center;
        overflow-x: auto; scrollbar-width: thin;
    }
    .wp-toc::-webkit-scrollbar { height: 6px; }
    .wp-toc::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.18); border-radius: 3px; }
    .wp-toc a {
        flex-shrink: 0; font-size: 13px; padding: 6px 12px;
        border: 1px solid var(--bs-border-color); border-radius: 999px;
        color: var(--bs-secondary-color); text-decoration: none; white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s;
    }
    .wp-toc a:hover {
        color: var(--bs-primary); border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .06);
    }
    .wp-edit-section { scroll-margin-top: 140px; }
    .wp-status-preview {
        display: inline-block; padding: 4px 12px; border-radius: 999px;
        font-size: 12px; font-weight: 600; line-height: 1.4;
    }
    .wp-status-preview.status-published { background: #2B3388; color: #fff; }
    .wp-status-preview.status-underdev  { background: transparent; color: #2B3388; border: 1px solid #2B3388; }
    .wp-status-preview.status-revision  { background: rgba(43,51,136,0.10); color: #2B3388; }
</style>

<nav class="wp-toc" aria-label="Work Programmes editor sections">
    <a href="#wp-sec-meta">Page Meta</a>
    <a href="#wp-sec-breadcrumb">Breadcrumb</a>
    <a href="#wp-sec-intro">Introduction</a>
    <a href="#wp-sec-list">Programmes (<?= count($programmes) ?>)</a>
    <a href="#wp-sec-cta">CTAs</a>
</nav>

<!-- ─────────────── Page text form ─────────────── -->
<form method="POST">
    <input type="hidden" name="action" value="save_work">

    <div class="card mb-3 wp-edit-section" id="wp-sec-meta">
        <div class="card-body">
            <h5 class="card-title mb-3">Page Meta</h5>
            <div class="mb-3">
                <label class="form-label">Browser Tab Title</label>
                <input type="text" name="work_page_title" class="form-control" value="<?= pc_h($pc['work_page_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Meta Description</label>
                <textarea name="work_meta_description" class="form-control" rows="2"><?= pc_h($pc['work_meta_description']) ?></textarea>
                <small class="text-muted">Shown in search engine results.</small>
            </div>
        </div>
    </div>

    <div class="card mb-3 wp-edit-section" id="wp-sec-breadcrumb">
        <div class="card-body">
            <h5 class="card-title mb-3">Breadcrumb / Hero</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Breadcrumb Crumb 1</label>
                    <input type="text" name="work_breadcrumb_crumb_1" class="form-control" value="<?= pc_h($pc['work_breadcrumb_crumb_1']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Breadcrumb Crumb 2</label>
                    <input type="text" name="work_breadcrumb_crumb_2" class="form-control" value="<?= pc_h($pc['work_breadcrumb_crumb_2']) ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label">Hero / Page Heading</label>
                <input type="text" name="work_breadcrumb_title" class="form-control" value="<?= pc_h($pc['work_breadcrumb_title']) ?>">
                <small class="text-muted">Background image is managed in the Breadcrumbs editor (slug: <code>work</code>).</small>
            </div>
        </div>
    </div>

    <div class="card mb-3 wp-edit-section" id="wp-sec-intro">
        <div class="card-body">
            <h5 class="card-title mb-3">Introduction</h5>
            <div class="mb-3">
                <label class="form-label">Intro Heading</label>
                <input type="text" name="work_intro_title" class="form-control" value="<?= pc_h($pc['work_intro_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Intro Body</label>
                <textarea name="work_intro_body" class="form-control" rows="6"><?= pc_h($pc['work_intro_body']) ?></textarea>
                <small class="text-muted">Separate paragraphs with a blank line.</small>
            </div>
        </div>
    </div>

    <!-- Section heading for the programme list — kept in page_content because it sits above the table -->
    <div class="card mb-3 wp-edit-section" id="wp-sec-list">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h5 class="card-title mb-0">Programme List</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#progModal" onclick="openProgModalAdd()">
                    <i class="fas fa-plus me-1"></i> Add Programme
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">Section Heading (rendered above the list)</label>
                <input type="text" name="work_section_title" class="form-control" value="<?= pc_h($pc['work_section_title']) ?>"
                       placeholder="Current and Recent Projects">
            </div>

            <?php if (empty($programmes)): ?>
                <div class="text-center py-4 text-muted" style="border:1px dashed rgba(43,51,136,0.25); border-radius:4px;">
                    <i class="fas fa-list-ul fa-2x mb-2 d-block"></i>
                    No programmes yet. Click <strong>+ Add Programme</strong> to add the first one.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">Sort</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th style="width:140px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($programmes as $p): ?>
                            <tr>
                                <td class="text-muted small"><?= (int)$p['sort_order'] ?></td>
                                <td>
                                    <div class="fw-semibold"><?= pc_h($p['title']) ?></div>
                                    <?php if (!empty($p['details'])): ?>
                                        <div class="small text-muted"><?= pc_h($p['details']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['url'])): ?>
                                        <div class="small"><i class="fas fa-link me-1 text-muted"></i><?= pc_h($p['url']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="wp-status-preview <?= pc_h($p['status_class']) ?>">
                                        <?= pc_h($p['status_label'] ?: 'Status') ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                            type="button"
                                            data-bs-toggle="modal" data-bs-target="#progModal"
                                            onclick='openProgModalEdit(<?= json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('Delete this programme?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <small class="text-muted d-block mt-2">Rows are ordered by <strong>Sort</strong> ascending, then by id. Lower sort number appears higher up.</small>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3 wp-edit-section" id="wp-sec-cta">
        <div class="card-body">
            <h5 class="card-title mb-3">Call-to-Action Buttons</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">CTA 1 Text</label>
                    <input type="text" name="work_cta_1_text" class="form-control" value="<?= pc_h($pc['work_cta_1_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CTA 1 URL</label>
                    <input type="text" name="work_cta_1_url" class="form-control" value="<?= pc_h($pc['work_cta_1_url']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CTA 2 Text</label>
                    <input type="text" name="work_cta_2_text" class="form-control" value="<?= pc_h($pc['work_cta_2_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CTA 2 URL</label>
                    <input type="text" name="work_cta_2_url" class="form-control" value="<?= pc_h($pc['work_cta_2_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Save Page Text</button>
    </div>
</form>

<!-- ─────────────── Programme Add/Edit modal ─────────────── -->
<div class="modal fade" id="progModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <input type="hidden" name="action" id="prog-action" value="add">
            <input type="hidden" name="id" id="prog-id" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="progModalTitle">Add Programme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" id="prog-title" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Details</label>
                        <input type="text" name="details" id="prog-details" class="form-control"
                               placeholder="Approved: 2020 | Reference: SZNS US 1234: 2020">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Link URL</label>
                        <input type="text" name="url" id="prog-url" class="form-control"
                               placeholder="standard-detail-1234.php">
                        <small class="text-muted">Where the title links to on the public page.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Label</label>
                        <input type="text" name="status_label" id="prog-status-label" class="form-control" placeholder="Published">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Style</label>
                        <select name="status_class" id="prog-status-class" class="form-select">
                            <?php foreach ($status_class_options as $val => $label): ?>
                                <option value="<?= pc_h($val) ?>"><?= pc_h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="prog-sort" class="form-control" value="0">
                        <small class="text-muted">Lower numbers appear first.</small>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div>
                            <small class="text-muted d-block mb-1">Preview</small>
                            <span id="prog-preview" class="wp-status-preview status-published">Published</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Programme</button>
            </div>
        </form>
    </div>
</div>

<script>
function openProgModalAdd() {
    document.getElementById('prog-action').value = 'add';
    document.getElementById('progModalTitle').textContent = 'Add Programme';
    document.getElementById('prog-id').value = '';
    document.getElementById('prog-title').value = '';
    document.getElementById('prog-details').value = '';
    document.getElementById('prog-url').value = '';
    document.getElementById('prog-status-label').value = '';
    document.getElementById('prog-status-class').value = 'status-published';
    document.getElementById('prog-sort').value = 0;
    refreshProgPreview();
}
function openProgModalEdit(row) {
    document.getElementById('prog-action').value = 'update';
    document.getElementById('progModalTitle').textContent = 'Edit Programme #' + row.id;
    document.getElementById('prog-id').value = row.id;
    document.getElementById('prog-title').value = row.title || '';
    document.getElementById('prog-details').value = row.details || '';
    document.getElementById('prog-url').value = row.url || '';
    document.getElementById('prog-status-label').value = row.status_label || '';
    document.getElementById('prog-status-class').value = row.status_class || 'status-published';
    document.getElementById('prog-sort').value = row.sort_order || 0;
    refreshProgPreview();
}
function refreshProgPreview() {
    var prev = document.getElementById('prog-preview');
    var label = document.getElementById('prog-status-label').value || 'Status';
    var cls   = document.getElementById('prog-status-class').value || 'status-published';
    prev.textContent = label;
    prev.classList.remove('status-published','status-underdev','status-revision');
    prev.classList.add(cls);
}
['prog-status-label','prog-status-class'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input',  refreshProgPreview);
        el.addEventListener('change', refreshProgPreview);
    }
});

<?php if ($edit_prog): ?>
// Auto-open edit modal when arriving with ?edit=N
window.addEventListener('DOMContentLoaded', function () {
    openProgModalEdit(<?= json_encode($edit_prog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>);
    new bootstrap.Modal(document.getElementById('progModal')).show();
});
<?php endif; ?>
</script>

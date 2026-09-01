<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key registry ──────────────────────────────────────────────
$text_keys = [
    // Breadcrumb
    'ms_breadcrumb_title','ms_crumb_home','ms_crumb_section','ms_crumb_current',

    // Introduction
    'ms_intro_title','ms_intro_body',

    // Schemes
    'ms_schemes_title',
    'ms_scheme_iso9001_alt','ms_scheme_iso9001_code','ms_scheme_iso9001_name',
    'ms_scheme_iso14001_alt','ms_scheme_iso14001_code','ms_scheme_iso14001_name',
    'ms_scheme_iso22000_alt','ms_scheme_iso22000_code','ms_scheme_iso22000_name',
    'ms_scheme_iso45001_alt','ms_scheme_iso45001_code','ms_scheme_iso45001_name',
    'ms_scheme_haccp_alt','ms_scheme_haccp_code','ms_scheme_haccp_name',

    // Accreditation
    'ms_accred_title','ms_accred_body','ms_accred_img_alt',

    // Portfolio
    'ms_portfolio_title','ms_portfolio_footnote',
    'ms_portfolio_1_code','ms_portfolio_1_name',
    'ms_portfolio_2_code','ms_portfolio_2_name',
    'ms_portfolio_3_code','ms_portfolio_3_name',
    'ms_portfolio_4_code','ms_portfolio_4_name',
    'ms_portfolio_5_code','ms_portfolio_5_name',

    // Certified Organisations
    'ms_certified_title','ms_certified_footer',

    // Documents
    'ms_docs_title',
    // ms_doc_*_title/url removed — docs now live in certification_documents (Certification Documents tab).

    // Why Certify
    'ms_why_title','ms_why_subtitle','ms_why_img_alt',

    // Process
    'ms_process_title',
    'ms_step_1_title','ms_step_1_body',
    'ms_step_2_title','ms_step_2_body',
    'ms_step_3_title','ms_step_3_body',
    'ms_step_4_title','ms_step_4_body',
    'ms_step_5_title','ms_step_5_body',
    'ms_step_decision_title','ms_step_decision_body',
    'ms_step_6_title','ms_step_6_body',
    'ms_step_7_title','ms_step_7_body',
    'ms_step_8_title','ms_step_8_body',

    // Benefits
    'ms_benefits_title',
    'ms_benefit_1','ms_benefit_2','ms_benefit_3','ms_benefit_4','ms_benefit_5',
    'ms_benefit_6','ms_benefit_7','ms_benefit_8','ms_benefit_9','ms_benefit_10',

    // CTA
    'ms_cta_title','ms_cta_subtitle',
    'ms_cta_btn1_text','ms_cta_btn1_url',
    'ms_cta_btn2_text','ms_cta_btn2_url',
    'ms_cta_btn3_text','ms_cta_btn3_url',
];

$image_keys = [
    'ms_scheme_iso9001_img',
    'ms_scheme_iso14001_img',
    'ms_scheme_iso22000_img',
    'ms_scheme_iso45001_img',
    'ms_scheme_haccp_img',
    'ms_accred_img',
    'ms_why_img',
];

// ── Save handler: page content (existing) ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ms'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'ms');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'ms');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    header('Location: index.php?page=managementsystems.php&tab=content');
    exit;
}

// ── Certified organisations ───────────────────────────────────
// Handlers, validation and loading live in the shared partial, which the
// Product and Ingelo admin pages include with their own scheme.
$CO_SCHEME = 'ms';
$CO_PAGE   = 'managementsystems.php';
$CO_NOUN   = 'organisation';
require __DIR__ . '/_certified_orgs_crud.php';

// ── PDF upload helper for certification_documents ─────────────
function ms_upload_doc_pdf(string $field, string $upload_dir, int $max_bytes = 26214400): array {
    if (empty($_FILES[$field]['name'])) return ['ok' => true, 'path' => null, 'error' => ''];
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return ['ok' => false, 'path' => null, 'error' => 'Upload failed.'];
    if ($_FILES[$field]['size'] > $max_bytes) return ['ok' => false, 'path' => null, 'error' => 'PDF exceeds 25 MB.'];
    $tmp = $_FILES[$field]['tmp_name'];
    $original = $_FILES[$field]['name'];
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    if ($ext !== 'pdf') return ['ok' => false, 'path' => null, 'error' => 'Only PDF files are allowed.'];
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if ($mime && !in_array(strtolower($mime), ['application/pdf','application/x-pdf','binary/octet-stream'], true)) {
            return ['ok' => false, 'path' => null, 'error' => 'File does not look like a valid PDF.'];
        }
    }
    $stem = strtolower(pathinfo($original, PATHINFO_FILENAME));
    $stem = preg_replace('/[^a-z0-9]+/', '-', $stem);
    $stem = trim($stem, '-') ?: 'doc';
    if (strlen($stem) > 60) $stem = substr($stem, 0, 60);
    $unique = $stem . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.pdf';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
        return ['ok' => false, 'path' => null, 'error' => 'Could not create uploads directory.'];
    }
    $target = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $unique;
    if (!move_uploaded_file($tmp, $target)) return ['ok' => false, 'path' => null, 'error' => 'Failed to save uploaded file.'];
    return ['ok' => true, 'path' => 'admin/uploads/docs/' . $unique, 'error' => ''];
}

// ── Save handler: certification document (create / update) ────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_doc'])) {
    $id         = !empty($_POST['doc_id']) ? (int)$_POST['doc_id'] : null;
    $title      = pc_strip_text($_POST['doc_title'] ?? '');
    $sort_order = (int)($_POST['doc_sort_order'] ?? 0);
    $is_active  = !empty($_POST['doc_is_active']) ? 1 : 0;
    $existing   = trim($_POST['doc_existing_path'] ?? '');
    $manual_path = trim($_POST['doc_manual_path'] ?? '');

    $errors = [];
    if ($title === '') $errors[] = 'Document title is required.';

    // file_path resolution: new upload wins, else manual path, else keep existing.
    $file_path = $existing;
    $up = ms_upload_doc_pdf('doc_file', __DIR__ . '/../uploads/docs/');
    if (!$up['ok']) $errors[] = $up['error'];
    if ($up['path']) {
        $file_path = $up['path'];
        // If a new PDF was uploaded and the old file lived under admin/uploads/docs/, clean it up.
        if ($id && $existing !== '' && strpos($existing, 'admin/uploads/docs/') === 0) {
            $oldfs = __DIR__ . '/../../' . $existing;
            if (is_file($oldfs)) @unlink($oldfs);
        }
    } elseif ($manual_path !== '') {
        $file_path = $manual_path;
    }

    if ($file_path === '') $errors[] = 'A PDF file or a file path is required.';

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: index.php?page=managementsystems.php&tab=docs' . ($id ? '&edit_doc=' . $id : '&new_doc=1'));
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare('UPDATE certification_documents SET title = ?, file_path = ?, sort_order = ?, is_active = ? WHERE id = ?');
        $stmt->bind_param('ssiii', $title, $file_path, $sort_order, $is_active, $id);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Document updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO certification_documents (title, file_path, sort_order, is_active) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssii', $title, $file_path, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Document added.');
    }
    header('Location: index.php?page=managementsystems.php&tab=docs');
    exit;
}

// ── GET: quick toggle is_active on a doc ──────────────────────
if (isset($_GET['toggle_doc'])) {
    $id = (int)$_GET['toggle_doc'];
    $stmt = $conn->prepare('UPDATE certification_documents SET is_active = 1 - is_active WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: index.php?page=managementsystems.php&tab=docs');
    exit;
}

// ── GET: delete a doc ─────────────────────────────────────────
if (isset($_GET['delete_doc'])) {
    $id = (int)$_GET['delete_doc'];
    $sel = $conn->prepare('SELECT file_path FROM certification_documents WHERE id = ?');
    $sel->bind_param('i', $id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($row && !empty($row['file_path']) && strpos($row['file_path'], 'admin/uploads/docs/') === 0) {
        $fs = __DIR__ . '/../../' . $row['file_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM certification_documents WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Document deleted.');
    header('Location: index.php?page=managementsystems.php&tab=docs');
    exit;
}

// ── Load data ─────────────────────────────────────────────────
$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));

$orgs = $co_rows;  // loaded by _certified_orgs_crud.php

$docs_res = $conn->query('SELECT * FROM certification_documents ORDER BY sort_order ASC, id ASC');
$docs = $docs_res ? $docs_res->fetch_all(MYSQLI_ASSOC) : [];

$edit_org = $co_edit;
$is_new_org = $co_is_new;

$edit_doc = null;
$is_new_doc = isset($_GET['new_doc']);
if (isset($_GET['edit_doc'])) {
    $stmt = $conn->prepare('SELECT * FROM certification_documents WHERE id = ?');
    $eid = (int)$_GET['edit_doc'];
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $edit_doc = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$active_tab = $_GET['tab'] ?? 'orgs';
if (!in_array($active_tab, ['orgs', 'docs', 'content'], true)) $active_tab = 'orgs';
if ($edit_org || $is_new_org) $active_tab = 'orgs';
if ($edit_doc || $is_new_doc) $active_tab = 'docs';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Management Systems Certification</h1>
    <div class="d-flex gap-2">
        <a href="../managementsystems.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
        <?php // The Add buttons used to live here, gated on $active_tab. That
              // is server-side state set from ?tab=, but the tabs below are
              // Bootstrap client-side tabs that swap panes without reloading
              // or changing the URL. So switching to Certification Documents
              // left this header still showing "Add organisation", and there
              // was no way to reach the add-document form except by typing
              // ?new_doc=1 by hand. Each Add button now sits inside its own
              // tab pane, governed by the same client-side state as the
              // content it belongs to.
              // See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md (A8). ?>
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'orgs' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-orgs" type="button" role="tab">
            Certified Organisations <span class="badge bg-secondary ms-1"><?= count($orgs) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'docs' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-docs" type="button" role="tab">
            Certification Documents <span class="badge bg-secondary ms-1"><?= count($docs) ?></span>
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

<!-- ============ TAB: Certified Organisations ============ -->
<div class="tab-pane fade <?= $active_tab === 'orgs' ? 'show active' : '' ?>" id="tab-orgs" role="tabpanel">
    <?php require __DIR__ . '/_certified_orgs_ui.php'; ?>
</div>

<!-- ============ TAB: Certification Documents ============ -->
<div class="tab-pane fade <?= $active_tab === 'docs' ? 'show active' : '' ?>" id="tab-docs" role="tabpanel">

    <?php if (!$edit_doc && !$is_new_doc): ?>
        <div class="d-flex justify-content-end mb-3">
            <a href="index.php?page=managementsystems.php&new_doc=1" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add document
            </a>
        </div>
    <?php endif; ?>

    <?php if ($edit_doc || $is_new_doc):
        $d = $edit_doc ?: [
            'id' => 0, 'title' => '', 'file_path' => '',
            'sort_order' => ($docs ? (max(array_column($docs, 'sort_order')) + 1) : 1),
            'is_active' => 1,
        ];
        $is_uploaded = !empty($d['file_path']) && strpos($d['file_path'], 'admin/uploads/docs/') === 0;
    ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><?= $edit_doc ? 'Edit document' : 'Add document' ?></strong>
                <a href="index.php?page=managementsystems.php&tab=docs" class="btn btn-sm btn-link text-decoration-none">&larr; Back to list</a>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_doc): ?>
                        <input type="hidden" name="doc_id" value="<?= (int)$d['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="doc_existing_path" value="<?= pc_h($d['file_path']) ?>">

                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Title *</label>
                            <input type="text" name="doc_title" class="form-control" required maxlength="500"
                                   value="<?= pc_h($d['title']) ?>" placeholder="e.g. Procedure for Appeals Handling">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Sort order</label>
                            <input type="number" name="doc_sort_order" class="form-control" value="<?= (int)$d['sort_order'] ?>">
                            <div class="form-text">Lower = earlier.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">PDF file</label>
                            <input type="file" name="doc_file" class="form-control" accept="application/pdf,.pdf">
                            <div class="form-text">Upload a fresh PDF (up to 25 MB) — replaces the existing file. Leave empty to keep the current one.</div>
                            <?php if (!empty($d['file_path'])): ?>
                                <div class="mt-2 small">
                                    Current:
                                    <a href="../<?= pc_h($d['file_path']) ?>" target="_blank" rel="noopener"><?= pc_h(basename($d['file_path'])) ?></a>
                                    <?= $is_uploaded ? '<span class="badge bg-success ms-2">Uploaded</span>' : '<span class="badge bg-secondary ms-2">External path</span>' ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-9">
                            <label class="form-label fw-bold">Or external path / URL</label>
                            <input type="text" name="doc_manual_path" class="form-control"
                                   placeholder="e.g. CER_RU_028 RULES FOR THE USE OF THE CERTIFICATION MARK.pdf">
                            <div class="form-text">Use this for PDFs already on the server (relative to web root) or full URLs. Overrides "current" if filled, but is ignored when a new PDF is uploaded above.</div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="doc_is_active" id="doc_is_active" value="1" class="form-check-input"
                                       <?= (int)$d['is_active'] === 1 ? 'checked' : '' ?>>
                                <label for="doc_is_active" class="form-check-label">Show on public page</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="index.php?page=managementsystems.php&tab=docs" class="btn btn-link text-decoration-none">Cancel</a>
                        <button type="submit" name="save_doc" value="1" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> <?= $edit_doc ? 'Save changes' : 'Add document' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$edit_doc && !$is_new_doc): ?>
        <div class="card">
            <div class="card-header">All certification documents (<?= count($docs) ?>)</div>
            <div class="card-body p-0">
                <?php if ($docs): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Order</th>
                                    <th>Title</th>
                                    <th style="width: 110px;">Source</th>
                                    <th style="width: 90px;" class="text-center">Active</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($docs as $row):
                                    $uploaded = !empty($row['file_path']) && strpos($row['file_path'], 'admin/uploads/docs/') === 0;
                                ?>
                                    <tr>
                                        <td><?= (int)$row['sort_order'] ?></td>
                                        <td>
                                            <?= pc_h($row['title']) ?>
                                            <div class="small text-muted text-truncate" style="max-width: 460px;" title="<?= pc_h($row['file_path']) ?>"><?= pc_h($row['file_path']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($uploaded): ?>
                                                <span class="badge bg-success">Uploaded</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">External</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?page=managementsystems.php&tab=docs&toggle_doc=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                                <?php if ((int)$row['is_active'] === 1): ?>
                                                    <span class="badge bg-success">On</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Off</span>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="../<?= pc_h($row['file_path']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">Open</a>
                                            <a href="index.php?page=managementsystems.php&tab=docs&edit_doc=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=managementsystems.php&tab=docs&delete_doc=<?= (int)$row['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this document?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        No documents yet. <a href="index.php?page=managementsystems.php&new_doc=1">Add the first one</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ============ TAB: Page Content ============ -->
<div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">

<form method="POST" enctype="multipart/form-data">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title (large heading on banner)</label>
                <input type="text" name="ms_breadcrumb_title" class="form-control" value="<?= pc_h($pc['ms_breadcrumb_title']) ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Crumb: Home</label>
                    <input type="text" name="ms_crumb_home" class="form-control" value="<?= pc_h($pc['ms_crumb_home']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crumb: Section</label>
                    <input type="text" name="ms_crumb_section" class="form-control" value="<?= pc_h($pc['ms_crumb_section']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crumb: Current page</label>
                    <input type="text" name="ms_crumb_current" class="form-control" value="<?= pc_h($pc['ms_crumb_current']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Introduction -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Introduction</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="ms_intro_title" class="form-control" value="<?= pc_h($pc['ms_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="ms_intro_body" class="form-control" rows="8"><?= pc_h($pc['ms_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Certification Schemes -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Schemes Offered</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_schemes_title" class="form-control" value="<?= pc_h($pc['ms_schemes_title']) ?>">
            </div>

            <?php
            $schemes = [
                'iso9001'  => 'ISO 9001 — Quality',
                'iso14001' => 'ISO 14001 — Environmental',
                'iso22000' => 'ISO 22000 — Food Safety',
                'iso45001' => 'ISO 45001 — Occupational Health & Safety',
                'haccp'    => 'HACCP — SANS 10330',
            ];
            foreach ($schemes as $slug => $label):
                $img_key = 'ms_scheme_'.$slug.'_img';
                $alt_key = 'ms_scheme_'.$slug.'_alt';
                $code_key = 'ms_scheme_'.$slug.'_code';
                $name_key = 'ms_scheme_'.$slug.'_name';
            ?>
            <div class="border rounded p-3 mb-3">
                <h6 class="mb-3"><?= pc_h($label) ?></h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Image</label>
                        <div class="mb-2">
                            <img data-crop-preview="<?= $img_key ?>_preview"
                                 src="<?= !empty($pc[$img_key]) ? '../' . pc_h(pc_image_src($pc[$img_key])) : '' ?>"
                                 style="max-height:80px;border:1px solid #ddd;<?= empty($pc[$img_key]) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                        </div>
                        <input type="file" name="<?= $img_key ?>_file" accept="image/*" class="form-control form-control-sm crop-input"
                               data-crop-label="Scheme Image">
                        <input type="hidden" name="<?= $img_key ?>_cropped">
                        <small class="text-muted">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alt text</label>
                        <input type="text" name="<?= $alt_key ?>" class="form-control" value="<?= pc_h($pc[$alt_key]) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Standard Code</label>
                        <input type="text" name="<?= $code_key ?>" class="form-control" value="<?= pc_h($pc[$code_key]) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Standard Name</label>
                        <input type="text" name="<?= $name_key ?>" class="form-control" value="<?= pc_h($pc[$name_key]) ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Accreditation -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Accreditation Card</h5>
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="ms_accred_title" class="form-control" value="<?= pc_h($pc['ms_accred_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="ms_accred_body" class="form-control" rows="6"><?= pc_h($pc['ms_accred_body']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt text</label>
                        <input type="text" name="ms_accred_img_alt" class="form-control" value="<?= pc_h($pc['ms_accred_img_alt']) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Accreditation Logo</label>
                    <div class="mb-2">
                        <img data-crop-preview="ms_accred_img_preview"
                             src="<?= !empty($pc['ms_accred_img']) ? '../' . pc_h(pc_image_src($pc['ms_accred_img'])) : '' ?>"
                             style="max-height:120px;border:1px solid #ddd;<?= empty($pc['ms_accred_img']) ? 'display:none;' : '' ?>"
                             onload="this.style.display='inline-block'" alt="">
                    </div>
                    <input type="file" name="ms_accred_img_file" accept="image/*" class="form-control crop-input"
                           data-crop-label="Accreditation Logo">
                    <input type="hidden" name="ms_accred_img_cropped">
                    <small class="text-muted">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certifications Portfolio Card</h5>
            <div class="mb-3">
                <label class="form-label">Card Title</label>
                <input type="text" name="ms_portfolio_title" class="form-control" value="<?= pc_h($pc['ms_portfolio_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <input type="text" name="ms_portfolio_<?= $i ?>_code" class="form-control" placeholder="e.g. SZNS ISO 9001" value="<?= pc_h($pc['ms_portfolio_'.$i.'_code']) ?>">
                </div>
                <div class="col-md-8">
                    <input type="text" name="ms_portfolio_<?= $i ?>_name" class="form-control" placeholder="e.g. Quality Management Systems" value="<?= pc_h($pc['ms_portfolio_'.$i.'_name']) ?>">
                </div>
            </div>
            <?php endfor; ?>
            <div class="mt-3">
                <label class="form-label">Footnote</label>
                <textarea name="ms_portfolio_footnote" class="form-control" rows="2"><?= pc_h($pc['ms_portfolio_footnote']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Certified Organisations -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certified Organisations</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_certified_title" class="form-control" value="<?= pc_h($pc['ms_certified_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Footer note (link to Certification Status Register is appended automatically)</label>
                <textarea name="ms_certified_footer" class="form-control" rows="2"><?= pc_h($pc['ms_certified_footer']) ?></textarea>
            </div>
            <small class="text-muted">Logo tiles, organisation names, and standards are managed in the <strong>Certified Organisations</strong> tab.</small>
        </div>
    </div>

    <!-- Documents -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Documents</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_docs_title" class="form-control" value="<?= pc_h($pc['ms_docs_title']) ?>">
            </div>
            <small class="text-muted">Document cards (title, file, sort, on/off) are managed in the <strong>Certification Documents</strong> tab.</small>
        </div>
    </div>

    <!-- Why Certify -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Why Certify with ESWASA?</h5>
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="ms_why_title" class="form-control" value="<?= pc_h($pc['ms_why_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="ms_why_subtitle" class="form-control" value="<?= pc_h($pc['ms_why_subtitle']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt text</label>
                        <textarea name="ms_why_img_alt" class="form-control" rows="2"><?= pc_h($pc['ms_why_img_alt']) ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Why-Certify Illustration</label>
                    <div class="mb-2">
                        <img data-crop-preview="ms_why_img_preview"
                             src="<?= !empty($pc['ms_why_img']) ? '../' . pc_h(pc_image_src($pc['ms_why_img'])) : '' ?>"
                             style="max-width:100%;max-height:160px;border:1px solid #ddd;<?= empty($pc['ms_why_img']) ? 'display:none;' : '' ?>"
                             onload="this.style.display='inline-block'" alt="">
                    </div>
                    <input type="file" name="ms_why_img_file" accept="image/*" class="form-control crop-input"
                           data-crop-label="Why Certify Image">
                    <input type="hidden" name="ms_why_img_cropped">
                    <small class="text-muted">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Steps -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">How Certification Works (Process)</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_process_title" class="form-control" value="<?= pc_h($pc['ms_process_title']) ?>">
            </div>
            <?php
            $process_steps = [
                '1' => 'Row 1, Step 1',
                '2' => 'Row 1, Step 2',
                '3' => 'Row 1, Step 3',
                '4' => 'Row 2, Step 4',
                '5' => 'Row 2, Step 5',
                'decision' => 'Row 2, Highlight Circle (Certification Decision)',
                '6' => 'Row 3, Step 6',
                '7' => 'Row 3, Step 7',
                '8' => 'Row 3, Step 8',
            ];
            foreach ($process_steps as $idx => $label):
                $title_key = 'ms_step_'.$idx.'_title';
                $body_key  = 'ms_step_'.$idx.'_body';
            ?>
            <div class="row g-2 mb-2">
                <div class="col-md-3 d-flex align-items-center"><small class="text-muted"><?= pc_h($label) ?></small></div>
                <div class="col-md-3">
                    <input type="text" name="<?= $title_key ?>" class="form-control" placeholder="Heading (e.g. Step 1)" value="<?= pc_h($pc[$title_key]) ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="<?= $body_key ?>" class="form-control" placeholder="Description" value="<?= pc_h($pc[$body_key]) ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Benefits -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Benefits of Certification</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_benefits_title" class="form-control" value="<?= pc_h($pc['ms_benefits_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-1 text-end"><span class="text-muted">#<?= $i ?></span></div>
                <div class="col-md-11">
                    <input type="text" name="ms_benefit_<?= $i ?>" class="form-control" value="<?= pc_h($pc['ms_benefit_'.$i]) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CTA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Call-to-Action Section</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="ms_cta_title" class="form-control" value="<?= pc_h($pc['ms_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <textarea name="ms_cta_subtitle" class="form-control" rows="2"><?= pc_h($pc['ms_cta_subtitle']) ?></textarea>
            </div>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> Text</label>
                    <input type="text" name="ms_cta_btn<?= $i ?>_text" class="form-control" value="<?= pc_h($pc['ms_cta_btn'.$i.'_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> URL</label>
                    <input type="text" name="ms_cta_btn<?= $i ?>_url" class="form-control" value="<?= pc_h($pc['ms_cta_btn'.$i.'_url']) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_ms" class="btn btn-primary px-5">Save Changes</button>
    </div>
</form>

</div> <!-- /tab-content[content] -->
</div> <!-- /tab-content wrapper -->

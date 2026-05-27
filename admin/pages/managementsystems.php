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
    'ms_doc_1_title','ms_doc_1_url',
    'ms_doc_2_title','ms_doc_2_url',
    'ms_doc_3_title','ms_doc_3_url',
    'ms_doc_4_title','ms_doc_4_url',
    'ms_doc_5_title','ms_doc_5_url',
    'ms_doc_6_title','ms_doc_6_url',
    'ms_doc_7_title','ms_doc_7_url',
    'ms_doc_8_title','ms_doc_8_url',
    'ms_doc_9_title','ms_doc_9_url',
    'ms_doc_10_title','ms_doc_10_url',
    'ms_doc_11_title','ms_doc_11_url',

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
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'ms');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    header('Location: index.php?page=managementsystems.php&tab=content');
    exit;
}

// ── Save handler: certified organisation (create / update) ────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_org'])) {
    $id          = !empty($_POST['org_id']) ? (int)$_POST['org_id'] : null;
    $name        = pc_strip_text($_POST['org_name'] ?? '');
    $standard    = pc_strip_text($_POST['org_standard'] ?? '');
    $sort_order  = (int)($_POST['org_sort_order'] ?? 0);
    $is_active   = !empty($_POST['org_is_active']) ? 1 : 0;
    $existing    = pc_strip_text($_POST['org_existing_logo'] ?? '');

    $errors = [];
    if ($name === '')     $errors[] = 'Organisation name is required.';
    if ($standard === '') $errors[] = 'Standard is required.';

    $logo_path = $existing;  // default: keep current
    $up = pc_upload_image('org_logo_file', ADMIN_ROOT . '/uploads/orgs/', 'org');
    if ($up === false) {
        $errors[] = 'Logo upload failed (check file type — JPG/PNG/WebP/SVG/GIF — and size under 5 MB).';
    } elseif ($up) {
        $logo_path = $up;
    }

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: index.php?page=managementsystems.php' . ($id ? '&edit_org=' . $id : '&new_org=1'));
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare('UPDATE certified_organisations SET name = ?, standard = ?, logo_path = ?, sort_order = ?, is_active = ? WHERE id = ?');
        $logo_for_db = $logo_path !== '' ? $logo_path : null;
        $stmt->bind_param('sssiii', $name, $standard, $logo_for_db, $sort_order, $is_active, $id);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Organisation updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO certified_organisations (name, standard, logo_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?)');
        $logo_for_db = $logo_path !== '' ? $logo_path : null;
        $stmt->bind_param('sssii', $name, $standard, $logo_for_db, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Organisation added.');
    }
    header('Location: index.php?page=managementsystems.php');
    exit;
}

// ── GET: quick toggle is_active on an org ─────────────────────
if (isset($_GET['toggle_org'])) {
    $id = (int)$_GET['toggle_org'];
    $stmt = $conn->prepare('UPDATE certified_organisations SET is_active = 1 - is_active WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: index.php?page=managementsystems.php');
    exit;
}

// ── GET: delete an org ────────────────────────────────────────
if (isset($_GET['delete_org'])) {
    $id = (int)$_GET['delete_org'];
    // Remove the uploaded logo file if it lives under admin/uploads/orgs/
    $sel = $conn->prepare('SELECT logo_path FROM certified_organisations WHERE id = ?');
    $sel->bind_param('i', $id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($row && !empty($row['logo_path']) && strpos($row['logo_path'], 'admin/uploads/orgs/') === 0) {
        $fs = __DIR__ . '/../../' . $row['logo_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM certified_organisations WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Organisation deleted.');
    header('Location: index.php?page=managementsystems.php');
    exit;
}

// ── Load data ─────────────────────────────────────────────────
$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));

$orgs_res = $conn->query('SELECT * FROM certified_organisations ORDER BY sort_order ASC, id ASC');
$orgs = $orgs_res ? $orgs_res->fetch_all(MYSQLI_ASSOC) : [];

$edit_org = null;
$is_new_org = isset($_GET['new_org']);
if (isset($_GET['edit_org'])) {
    $stmt = $conn->prepare('SELECT * FROM certified_organisations WHERE id = ?');
    $eid = (int)$_GET['edit_org'];
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $edit_org = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'orgs';
if ($edit_org || $is_new_org) $active_tab = 'orgs';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Management Systems Certification</h1>
    <div class="d-flex gap-2">
        <a href="../managementsystems.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
        <?php if ($active_tab === 'orgs' && !$edit_org && !$is_new_org): ?>
            <a href="index.php?page=managementsystems.php&new_org=1" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add organisation
            </a>
        <?php endif; ?>
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
        <button class="nav-link <?= $active_tab === 'content' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">
            Page Content
        </button>
    </li>
</ul>

<div class="tab-content">

<!-- ============ TAB: Certified Organisations ============ -->
<div class="tab-pane fade <?= $active_tab === 'orgs' ? 'show active' : '' ?>" id="tab-orgs" role="tabpanel">

    <?php if ($edit_org || $is_new_org):
        $o = $edit_org ?: [
            'id' => 0, 'name' => '', 'standard' => '', 'logo_path' => null,
            'sort_order' => ($orgs ? (max(array_column($orgs, 'sort_order')) + 1) : 1),
            'is_active' => 1,
        ];
    ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><?= $edit_org ? 'Edit organisation' : 'Add organisation' ?></strong>
                <a href="index.php?page=managementsystems.php" class="btn btn-sm btn-link text-decoration-none">&larr; Back to list</a>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_org): ?>
                        <input type="hidden" name="org_id" value="<?= (int)$o['id'] ?>">
                    <?php endif; ?>
                    <input type="hidden" name="org_existing_logo" value="<?= pc_h($o['logo_path']) ?>">

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Name *</label>
                            <input type="text" name="org_name" class="form-control" required maxlength="200"
                                   value="<?= pc_h($o['name']) ?>" placeholder="e.g. GALP Eswatini">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Standard *</label>
                            <input type="text" name="org_standard" class="form-control" required maxlength="200"
                                   value="<?= pc_h($o['standard']) ?>" placeholder="e.g. SZNS ISO 9001:2015">
                            <div class="form-text">Free text — exactly as it should appear under the logo tile.</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Sort order</label>
                            <input type="number" name="org_sort_order" class="form-control" value="<?= (int)$o['sort_order'] ?>">
                            <div class="form-text">Lower = earlier.</div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-bold">Logo</label>
                            <?php if (!empty($o['logo_path'])): ?>
                                <div class="mb-2">
                                    <img src="../<?= pc_h($o['logo_path']) ?>" style="max-height:80px;border:1px solid #ddd;padding:4px;background:#fff">
                                    <code class="ms-2 small text-muted"><?= pc_h($o['logo_path']) ?></code>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="org_logo_file" class="form-control" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif">
                            <div class="form-text">PNG / JPG / WebP / SVG / GIF up to 5 MB. Leave empty to keep current logo. Tiles without a logo render the name as a wordmark.</div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="org_is_active" id="org_is_active" value="1" class="form-check-input"
                                       <?= (int)$o['is_active'] === 1 ? 'checked' : '' ?>>
                                <label for="org_is_active" class="form-check-label">Show on public page</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="index.php?page=managementsystems.php" class="btn btn-link text-decoration-none">Cancel</a>
                        <button type="submit" name="save_org" value="1" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> <?= $edit_org ? 'Save changes' : 'Add organisation' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$edit_org && !$is_new_org): ?>
        <div class="card">
            <div class="card-header">All certified organisations (<?= count($orgs) ?>)</div>
            <div class="card-body p-0">
                <?php if ($orgs): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Order</th>
                                    <th style="width: 110px;">Logo</th>
                                    <th>Name</th>
                                    <th>Standard</th>
                                    <th style="width: 90px;" class="text-center">Active</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orgs as $row): ?>
                                    <tr>
                                        <td><?= (int)$row['sort_order'] ?></td>
                                        <td>
                                            <?php if (!empty($row['logo_path'])): ?>
                                                <img src="../<?= pc_h($row['logo_path']) ?>" style="max-height:38px;max-width:100px;object-fit:contain">
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">wordmark</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= pc_h($row['name']) ?></td>
                                        <td><?= pc_h($row['standard']) ?></td>
                                        <td class="text-center">
                                            <a href="index.php?page=managementsystems.php&toggle_org=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                                <?php if ((int)$row['is_active'] === 1): ?>
                                                    <span class="badge bg-success">On</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Off</span>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="index.php?page=managementsystems.php&edit_org=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=managementsystems.php&delete_org=<?= (int)$row['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this organisation?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        No organisations yet. <a href="index.php?page=managementsystems.php&new_org=1">Add the first one</a>.
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
                        <?php if (!empty($pc[$img_key])): ?>
                            <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc[$img_key])) ?>" style="max-height:80px;border:1px solid #ddd"></div>
                        <?php endif; ?>
                        <input type="file" name="<?= $img_key ?>_file" accept="image/*" class="form-control form-control-sm">
                        <small class="text-muted">Leave empty to keep current image.</small>
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
                    <?php if (!empty($pc['ms_accred_img'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['ms_accred_img'])) ?>" style="max-height:120px;border:1px solid #ddd"></div>
                    <?php endif; ?>
                    <input type="file" name="ms_accred_img_file" accept="image/*" class="form-control">
                    <small class="text-muted">Leave empty to keep current image.</small>
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
            <?php for ($i = 1; $i <= 11; $i++): ?>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-1 text-end"><span class="text-muted">#<?= $i ?></span></div>
                <div class="col-md-6">
                    <input type="text" name="ms_doc_<?= $i ?>_title" class="form-control" placeholder="Document title shown on card" value="<?= pc_h($pc['ms_doc_'.$i.'_title']) ?>">
                </div>
                <div class="col-md-5">
                    <input type="text" name="ms_doc_<?= $i ?>_url" class="form-control" placeholder="File path or URL (e.g. CER_RU_028.pdf)" value="<?= pc_h($pc['ms_doc_'.$i.'_url']) ?>">
                </div>
            </div>
            <?php endfor; ?>
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
                    <?php if (!empty($pc['ms_why_img'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['ms_why_img'])) ?>" style="max-width:100%;max-height:160px;border:1px solid #ddd"></div>
                    <?php endif; ?>
                    <input type="file" name="ms_why_img_file" accept="image/*" class="form-control">
                    <small class="text-muted">Leave empty to keep current image.</small>
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

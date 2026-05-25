<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    'work_page_title',
    'work_meta_description',
    'work_breadcrumb_crumb_1',
    'work_breadcrumb_crumb_2',
    'work_breadcrumb_title',
    'work_intro_title',
    'work_intro_body',
    'work_section_title',
    'work_item_1_title', 'work_item_1_url', 'work_item_1_details', 'work_item_1_status_label', 'work_item_1_status_class',
    'work_item_2_title', 'work_item_2_url', 'work_item_2_details', 'work_item_2_status_label', 'work_item_2_status_class',
    'work_item_3_title', 'work_item_3_url', 'work_item_3_details', 'work_item_3_status_label', 'work_item_3_status_class',
    'work_item_4_title', 'work_item_4_url', 'work_item_4_details', 'work_item_4_status_label', 'work_item_4_status_class',
    'work_item_5_title', 'work_item_5_url', 'work_item_5_details', 'work_item_5_status_label', 'work_item_5_status_class',
    'work_cta_1_text', 'work_cta_1_url',
    'work_cta_2_text', 'work_cta_2_url',
];
$image_keys = []; // No user-content images on Work Programmes page (breadcrumb bg is managed in Breadcrumbs editor).

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_work'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'work');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));

$status_class_options = [
    'status-published' => 'Published (filled blue)',
    'status-underdev'  => 'Under Development (outline)',
    'status-revision'  => 'Revision (light blue)',
];
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Work Programmes</h1>
        <small class="text-muted">Manage the page meta, intro, programme list and CTAs shown on the public <code>work.php</code> page.</small>
    </div>
    <a href="../work.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<style>
    .wp-toc {
        position: sticky;
        top: 72px;
        z-index: 30;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
        padding: 10px 0;
        margin: 0 0 1rem;
        display: flex;
        gap: 8px;
        align-items: center;
        overflow-x: auto;
        scrollbar-width: thin;
    }
    .wp-toc::-webkit-scrollbar { height: 6px; }
    .wp-toc::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.18); border-radius: 3px; }
    .wp-toc a {
        flex-shrink: 0;
        font-size: 13px;
        padding: 6px 12px;
        border: 1px solid var(--bs-border-color);
        border-radius: 999px;
        color: var(--bs-secondary-color);
        text-decoration: none;
        white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s;
    }
    .wp-toc a:hover {
        color: var(--bs-primary);
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .06);
    }
    .wp-toc .save-pill {
        margin-left: auto;
        font-weight: 600;
    }
    .wp-edit-section { scroll-margin-top: 140px; }
    .wp-save-bar {
        position: sticky;
        bottom: 0;
        z-index: 25;
        background: var(--bs-body-bg);
        border-top: 1px solid var(--bs-border-color);
        padding: 12px 0;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .wp-save-bar .save-hint { font-size: 13px; color: var(--bs-secondary-color); }

    /* Status badge preview — matches the public work.php styling */
    .wp-status-preview {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.4;
    }
    .wp-status-preview.status-published { background: #2B3388; color: #fff; }
    .wp-status-preview.status-underdev  { background: transparent; color: #2B3388; border: 1px solid #2B3388; }
    .wp-status-preview.status-revision  { background: rgba(43,51,136,0.10); color: #2B3388; }

    .wp-programme-card { background: #fff; border: 1px solid var(--bs-border-color); border-radius: 4px; padding: 18px; margin-bottom: 14px; }
    .wp-programme-card .pc-num {
        display: inline-block; min-width: 28px; height: 28px; line-height: 28px;
        text-align: center; border-radius: 50%; background: rgba(43,51,136,0.10);
        color: #2B3388; font-weight: 700; margin-right: 8px;
    }
</style>

<nav class="wp-toc" aria-label="Work Programmes editor sections">
    <a href="#wp-sec-meta">Page Meta</a>
    <a href="#wp-sec-breadcrumb">Breadcrumb</a>
    <a href="#wp-sec-intro">Introduction</a>
    <a href="#wp-sec-list">Programme List</a>
    <a href="#wp-sec-cta">CTAs</a>
    <button type="submit" form="workEditForm" name="save_work" class="btn btn-sm btn-primary save-pill">
        <i class="fas fa-save me-1"></i> Save
    </button>
</nav>

<form id="workEditForm" method="POST" enctype="multipart/form-data">

    <!-- Page Meta -->
    <div class="card mb-3 wp-edit-section" id="wp-sec-meta">
        <div class="card-body">
            <h5 class="card-title mb-3">Page Meta</h5>
            <div class="mb-3">
                <label class="form-label">Browser Tab Title</label>
                <input type="text" name="work_page_title" class="form-control" value="<?= pc_h($pc['work_page_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta Description</label>
                <textarea name="work_meta_description" class="form-control" rows="2"><?= pc_h($pc['work_meta_description']) ?></textarea>
                <small class="text-muted">Shown in search engine results.</small>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
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
                <small class="text-muted">Background image is managed in the Breadcrumbs editor (page slug: <code>work</code>).</small>
            </div>
        </div>
    </div>

    <!-- Intro Box -->
    <div class="card mb-3 wp-edit-section" id="wp-sec-intro">
        <div class="card-body">
            <h5 class="card-title mb-3">Introduction</h5>
            <div class="mb-3">
                <label class="form-label">Intro Heading</label>
                <input type="text" name="work_intro_title" class="form-control" value="<?= pc_h($pc['work_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Body</label>
                <textarea name="work_intro_body" class="form-control" rows="8"><?= pc_h($pc['work_intro_body']) ?></textarea>
                <small class="text-muted">Separate paragraphs with a blank line.</small>
            </div>
        </div>
    </div>

    <!-- Section + Programme Items -->
    <div class="card mb-3 wp-edit-section" id="wp-sec-list">
        <div class="card-body">
            <?php
                $populated = 0;
                for ($i = 1; $i <= 5; $i++) {
                    if (trim((string)($pc["work_item_{$i}_title"] ?? '')) !== '') $populated++;
                }
            ?>
            <div class="d-flex justify-content-between align-items-baseline mb-3">
                <h5 class="card-title mb-0">Programme List
                    <small class="text-muted ms-2"><?= $populated ?> / 5 filled</small>
                </h5>
                <small class="text-muted">Blank slots are hidden on the public page.</small>
            </div>

            <div class="mb-4">
                <label class="form-label">Section Heading</label>
                <input type="text" name="work_section_title" class="form-control" value="<?= pc_h($pc['work_section_title']) ?>"
                       placeholder="Current and Recent Projects">
                <small class="text-muted">Centered title rendered above the programme list with the brand-blue underline.</small>
            </div>

            <?php for ($i = 1; $i <= 5; $i++):
                $row_title = (string)$pc["work_item_{$i}_title"];
                $row_label = (string)$pc["work_item_{$i}_status_label"];
                $row_class = $pc["work_item_{$i}_status_class"] ?: 'status-published';
                $is_filled = $row_title !== '';
            ?>
            <div class="wp-programme-card">
                <div class="d-flex justify-content-between align-items-baseline mb-3">
                    <h6 class="fw-bold mb-0"><span class="pc-num"><?= $i ?></span>Programme</h6>
                    <?php if ($is_filled): ?>
                        <span class="wp-status-preview <?= pc_h($row_class) ?>" id="wp-preview-<?= $i ?>"><?= pc_h($row_label ?: 'Status') ?></span>
                    <?php else: ?>
                        <span class="badge bg-light text-muted border">Empty &mdash; will not show on page</span>
                    <?php endif; ?>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input type="text" name="work_item_<?= $i ?>_title" class="form-control" value="<?= pc_h($row_title) ?>"
                               placeholder="e.g. Development of SZNS for …">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Link URL</label>
                        <input type="text" name="work_item_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc["work_item_{$i}_url"]) ?>"
                               placeholder="standard-detail.php">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Details</label>
                        <input type="text" name="work_item_<?= $i ?>_details" class="form-control" value="<?= pc_h($pc["work_item_{$i}_details"]) ?>"
                               placeholder="Approved: 2020 | Reference: SZNS US 1234: 2020">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Label</label>
                        <input type="text" name="work_item_<?= $i ?>_status_label" class="form-control"
                               value="<?= pc_h($row_label) ?>" placeholder="Published"
                               data-wp-row="<?= $i ?>" data-wp-field="label">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status Style</label>
                        <select name="work_item_<?= $i ?>_status_class" class="form-select"
                                data-wp-row="<?= $i ?>" data-wp-field="class">
                            <?php foreach ($status_class_options as $val => $label): ?>
                                <option value="<?= pc_h($val) ?>" <?= $row_class === $val ? 'selected' : '' ?>><?= pc_h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Leave the Title blank to hide this slot on the public page.</small>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <script>
    // Live status-badge preview as admin edits each programme row.
    (function () {
        document.querySelectorAll('[data-wp-row]').forEach(function (el) {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });
        function updatePreview(e) {
            var row = e.target.getAttribute('data-wp-row');
            var preview = document.getElementById('wp-preview-' + row);
            if (!preview) return;
            var labelEl = document.querySelector('input[data-wp-row="' + row + '"][data-wp-field="label"]');
            var classEl = document.querySelector('select[data-wp-row="' + row + '"][data-wp-field="class"]');
            if (labelEl) preview.textContent = labelEl.value || 'Status';
            if (classEl) {
                preview.classList.remove('status-published', 'status-underdev', 'status-revision');
                preview.classList.add(classEl.value);
            }
        }
    })();
    </script>

    <!-- CTAs -->
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

    <div class="wp-save-bar">
        <span class="save-hint">Changes save the whole Work Programmes page at once.</span>
        <button type="submit" name="save_work" class="btn btn-primary px-5">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

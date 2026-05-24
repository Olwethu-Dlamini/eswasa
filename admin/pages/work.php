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
    <h1 class="h2">Work Programmes</h1>
    <a href="../work.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Page Meta -->
    <div class="card mb-3">
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
    <div class="card mb-3">
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
    <div class="card mb-3">
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
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Programme List</h5>
            <div class="mb-3">
                <label class="form-label">Section Heading</label>
                <input type="text" name="work_section_title" class="form-control" value="<?= pc_h($pc['work_section_title']) ?>">
            </div>

            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="border rounded p-3 mb-3 bg-light">
                <h6 class="fw-bold mb-3">Programme #<?= $i ?></h6>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title</label>
                        <input type="text" name="work_item_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc["work_item_{$i}_title"]) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Link URL</label>
                        <input type="text" name="work_item_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc["work_item_{$i}_url"]) ?>" placeholder="standard-detail.php">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Details</label>
                        <input type="text" name="work_item_<?= $i ?>_details" class="form-control" value="<?= pc_h($pc["work_item_{$i}_details"]) ?>" placeholder="Approved: 2020 | Reference: SZNS US 1234: 2020">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Label</label>
                        <input type="text" name="work_item_<?= $i ?>_status_label" class="form-control" value="<?= pc_h($pc["work_item_{$i}_status_label"]) ?>" placeholder="Published">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status Style</label>
                        <?php $current_class = $pc["work_item_{$i}_status_class"] ?: 'status-published'; ?>
                        <select name="work_item_<?= $i ?>_status_class" class="form-select">
                            <?php foreach ($status_class_options as $val => $label): ?>
                                <option value="<?= pc_h($val) ?>" <?= $current_class === $val ? 'selected' : '' ?>><?= pc_h($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">Leave all fields blank to hide this programme entry on the public page.</small>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CTAs -->
    <div class="card mb-3">
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
        <button type="submit" name="save_work" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

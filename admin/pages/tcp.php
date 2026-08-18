<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    // Hero / breadcrumb
    'tcp_hero_title',
    // Intro box
    'tcp_intro_title',
    'tcp_intro_body',
    // Benefits section header
    'tcp_benefits_title',
    // 4 benefit cards
    'tcp_benefit_1_title', 'tcp_benefit_1_body',
    'tcp_benefit_2_title', 'tcp_benefit_2_body',
    'tcp_benefit_3_title', 'tcp_benefit_3_body',
    'tcp_benefit_4_title', 'tcp_benefit_4_body',
    // Application section
    'tcp_apply_title',
    'tcp_apply_body',
    'tcp_apply_eligibility',
    'tcp_apply_button_text',
    'tcp_apply_button_url',
    'tcp_apply_contact_label',
    'tcp_apply_contact_email',
    // CTA buttons
    'tcp_cta_btn_1_text', 'tcp_cta_btn_1_url',
    'tcp_cta_btn_2_text', 'tcp_cta_btn_2_url',
];

$image_keys = [
    // No editable images on tcp.php (breadcrumb bg is managed in Breadcrumb Images editor).
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tcp'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'tcp');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Technical Committee Platform</h1>
        <p class="text-muted mb-0">Edit the content shown on <a href="../tcp.php" target="_blank">tcp.php</a>. Breadcrumb background is managed under <em>Breadcrumb Images</em>.</p>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- ===== Hero / Page Title ===== -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Page Title</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Hero / Breadcrumb Title</label>
                <input type="text" name="tcp_hero_title" class="form-control" value="<?= pc_h($pc['tcp_hero_title']) ?>">
                <div class="form-text">Shown in the page banner and as the last breadcrumb item.</div>
            </div>
        </div>
    </div>

    <!-- ===== Intro Box ===== -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Intro Box: About Technical Committees</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Intro Title</label>
                <input type="text" name="tcp_intro_title" class="form-control" value="<?= pc_h($pc['tcp_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Intro Body</label>
                <textarea name="tcp_intro_body" class="form-control" rows="8"><?= pc_h($pc['tcp_intro_body']) ?></textarea>
                <div class="form-text">Separate paragraphs with a blank line.</div>
            </div>
        </div>
    </div>

    <!-- ===== Benefits Section ===== -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Key Benefits Section</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Heading</label>
                <input type="text" name="tcp_benefits_title" class="form-control" value="<?= pc_h($pc['tcp_benefits_title']) ?>">
            </div>

            <?php for ($i = 1; $i <= 4; $i++): ?>
            <hr>
            <h6 class="text-uppercase text-muted mb-3">Benefit Card <?= $i ?></h6>
            <div class="mb-3">
                <label class="form-label fw-bold">Title</label>
                <input type="text" name="tcp_benefit_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['tcp_benefit_' . $i . '_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Body</label>
                <textarea name="tcp_benefit_<?= $i ?>_body" class="form-control" rows="3"><?= pc_h($pc['tcp_benefit_' . $i . '_body']) ?></textarea>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ===== Application Section ===== -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Application Section</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Application Title</label>
                <input type="text" name="tcp_apply_title" class="form-control" value="<?= pc_h($pc['tcp_apply_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Application Body</label>
                <textarea name="tcp_apply_body" class="form-control" rows="5"><?= pc_h($pc['tcp_apply_body']) ?></textarea>
                <div class="form-text">Separate paragraphs with a blank line.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Eligibility Note</label>
                <input type="text" name="tcp_apply_eligibility" class="form-control" value="<?= pc_h($pc['tcp_apply_eligibility']) ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Download Button Text</label>
                    <input type="text" name="tcp_apply_button_text" class="form-control" value="<?= pc_h($pc['tcp_apply_button_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Download Button URL</label>
                    <input type="text" name="tcp_apply_button_url" class="form-control" value="<?= pc_h($pc['tcp_apply_button_url']) ?>">
                    <div class="form-text">Path to PDF or external URL. Example: <code>admin/uploads/tc_membership_application.pdf</code></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Contact Label</label>
                    <input type="text" name="tcp_apply_contact_label" class="form-control" value="<?= pc_h($pc['tcp_apply_contact_label']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Contact Email</label>
                    <input type="email" name="tcp_apply_contact_email" class="form-control" value="<?= pc_h($pc['tcp_apply_contact_email']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CTA Buttons ===== -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Bottom CTA Buttons</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Button 1 Text</label>
                    <input type="text" name="tcp_cta_btn_1_text" class="form-control" value="<?= pc_h($pc['tcp_cta_btn_1_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Button 1 URL</label>
                    <input type="text" name="tcp_cta_btn_1_url" class="form-control" value="<?= pc_h($pc['tcp_cta_btn_1_url']) ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Button 2 Text</label>
                    <input type="text" name="tcp_cta_btn_2_text" class="form-control" value="<?= pc_h($pc['tcp_cta_btn_2_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Button 2 URL</label>
                    <input type="text" name="tcp_cta_btn_2_url" class="form-control" value="<?= pc_h($pc['tcp_cta_btn_2_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_tcp" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

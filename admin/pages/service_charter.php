<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_service_charter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service_charter'])) {
    $kv = [];
    foreach ($service_charter_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, $service_charter_keys, $service_charter_defaults);
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Service Charter</h1>
    <a href="../service-charter.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<p class="text-muted small mb-4">
    Edit the public-facing text on the Service Charter page. The five
    charter blocks (Who We Are, Service Standards, Core Values, What We
    Ask Of You, If We Fall Short) remain in code — request a code edit
    if their content needs to change.
</p>

<form method="POST">

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Title (banner heading)</label>
                    <input type="text" name="service_charter_breadcrumb_title" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_title']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">"Home" link label</label>
                    <input type="text" name="service_charter_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_home_label']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Parent section label</label>
                    <input type="text" name="service_charter_breadcrumb_parent_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_parent_label']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Current page label</label>
                    <input type="text" name="service_charter_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_current_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Page Heading &amp; Intro Card</h5>
            <div class="mb-3">
                <label class="form-label">Section heading (shown above the intro card)</label>
                <input type="text" name="service_charter_section_title" class="form-control" value="<?= pc_h($pc['service_charter_section_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Intro card body (separate paragraphs with a blank line)</label>
                <textarea name="service_charter_intro_body" class="form-control" rows="5"><?= pc_h($pc['service_charter_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Bottom "Feedback" Call-to-Action</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="service_charter_cta_title" class="form-control" value="<?= pc_h($pc['service_charter_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body</label>
                <input type="text" name="service_charter_cta_body" class="form-control" value="<?= pc_h($pc['service_charter_cta_body']) ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Button text</label>
                    <input type="text" name="service_charter_cta_button_text" class="form-control" value="<?= pc_h($pc['service_charter_cta_button_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button URL</label>
                    <input type="text" name="service_charter_cta_button_url" class="form-control" value="<?= pc_h($pc['service_charter_cta_button_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_service_charter" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

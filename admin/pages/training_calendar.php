<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key definitions ──────────────────────────────────────────────────────────
$page_text_keys = [
    // Breadcrumb / hero
    'train_cal_breadcrumb_home',
    'train_cal_breadcrumb_parent',
    'train_cal_breadcrumb_current',
    'train_cal_hero_title',
    // Section heading
    'train_cal_section_title',
    'train_cal_section_subtitle',
    // Action buttons
    'train_cal_prospectus_label',
    'train_cal_prospectus_url',
    'train_cal_application_label',
    'train_cal_application_url',
    'train_cal_elearning_label',
    'train_cal_elearning_soon_badge',
    // Trainings list header
    'train_cal_year_label',
    'train_cal_reset_filter_label',
    // Apply modal
    'train_cal_modal_title_prefix',
    'train_cal_modal_title_on',
    'train_cal_modal_intro',
    'train_cal_modal_label_name',
    'train_cal_modal_label_email',
    'train_cal_modal_label_phone',
    'train_cal_modal_label_company',
    'train_cal_modal_label_position',
    'train_cal_modal_label_comments',
    'train_cal_modal_consent',
    'train_cal_modal_submit_label',
];

$session_slots = 13;
$session_field_suffixes = ['date', 'title', 'location', 'duration', 'price'];

$session_keys = [];
for ($n = 1; $n <= $session_slots; $n++) {
    foreach ($session_field_suffixes as $f) {
        $session_keys[] = "train_cal_session_{$n}_{$f}";
    }
}

$all_text_keys = array_merge($page_text_keys, $session_keys);

// ── Save handler ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_train_cal'])) {
    $kv = [];
    foreach ($all_text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Training Calendar saved.');
    redirect_self();
}

$pc = pc_get_many($conn, $all_text_keys);
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Training Calendar</h1>
        <p class="text-muted mb-0">Edit the public training calendar — hero, action buttons, modal copy, and the 13 session slots. Leave a session title blank to hide that slot from the page.</p>
    </div>
    <a href="../training-calendar.php" target="_blank" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-external-link-alt me-1"></i> View page
    </a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Header / Hero -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Hero & Breadcrumb</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hero title</label>
                    <input type="text" name="train_cal_hero_title" class="form-control" value="<?= pc_h($pc['train_cal_hero_title']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Breadcrumb: Home</label>
                    <input type="text" name="train_cal_breadcrumb_home" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_home']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Breadcrumb: Parent</label>
                    <input type="text" name="train_cal_breadcrumb_parent" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_parent']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Breadcrumb: Current</label>
                    <input type="text" name="train_cal_breadcrumb_current" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_current']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Section heading -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Section heading</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Section title</label>
                    <input type="text" name="train_cal_section_title" class="form-control" value="<?= pc_h($pc['train_cal_section_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Section subtitle</label>
                    <input type="text" name="train_cal_section_subtitle" class="form-control" value="<?= pc_h($pc['train_cal_section_subtitle']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Action buttons (Prospectus / Application / E-Learning)</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Prospectus label</label>
                    <input type="text" name="train_cal_prospectus_label" class="form-control" value="<?= pc_h($pc['train_cal_prospectus_label']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Prospectus URL</label>
                    <input type="text" name="train_cal_prospectus_url" class="form-control" value="<?= pc_h($pc['train_cal_prospectus_url']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Application label</label>
                    <input type="text" name="train_cal_application_label" class="form-control" value="<?= pc_h($pc['train_cal_application_label']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Application URL</label>
                    <input type="text" name="train_cal_application_url" class="form-control" value="<?= pc_h($pc['train_cal_application_url']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">E-Learning label</label>
                    <input type="text" name="train_cal_elearning_label" class="form-control" value="<?= pc_h($pc['train_cal_elearning_label']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">E-Learning "Coming soon" badge</label>
                    <input type="text" name="train_cal_elearning_soon_badge" class="form-control" value="<?= pc_h($pc['train_cal_elearning_soon_badge']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Trainings list header -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Trainings list header</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Year label</label>
                    <input type="text" name="train_cal_year_label" class="form-control" value="<?= pc_h($pc['train_cal_year_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">"Reset filter" button label</label>
                    <input type="text" name="train_cal_reset_filter_label" class="form-control" value="<?= pc_h($pc['train_cal_reset_filter_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Apply modal -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">"Apply for Training" modal</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Modal title prefix</label>
                    <input type="text" name="train_cal_modal_title_prefix" class="form-control" value="<?= pc_h($pc['train_cal_modal_title_prefix']) ?>">
                    <div class="form-text">Shown before the training name in the modal title.</div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">"on" connector</label>
                    <input type="text" name="train_cal_modal_title_on" class="form-control" value="<?= pc_h($pc['train_cal_modal_title_on']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Intro paragraph</label>
                    <input type="text" name="train_cal_modal_intro" class="form-control" value="<?= pc_h($pc['train_cal_modal_intro']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Full Name</label>
                    <input type="text" name="train_cal_modal_label_name" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Email</label>
                    <input type="text" name="train_cal_modal_label_email" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_email']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Phone</label>
                    <input type="text" name="train_cal_modal_label_phone" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_phone']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Company</label>
                    <input type="text" name="train_cal_modal_label_company" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_company']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Position</label>
                    <input type="text" name="train_cal_modal_label_position" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_position']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Field label: Comments</label>
                    <input type="text" name="train_cal_modal_label_comments" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_comments']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Consent text</label>
                    <textarea name="train_cal_modal_consent" class="form-control" rows="2"><?= pc_h($pc['train_cal_modal_consent']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Submit button label</label>
                    <input type="text" name="train_cal_modal_submit_label" class="form-control" value="<?= pc_h($pc['train_cal_modal_submit_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions -->
    <h3 class="h4 mt-4 mb-3">Training Sessions (<?= $session_slots ?> slots)</h3>
    <p class="text-muted small">Leave a session's <strong>Title</strong> blank to hide that slot from the public page.</p>

    <?php for ($n = 1; $n <= $session_slots; $n++):
        $kt = "train_cal_session_{$n}_title";
        $kd = "train_cal_session_{$n}_date";
        $kl = "train_cal_session_{$n}_location";
        $kdur = "train_cal_session_{$n}_duration";
        $kp = "train_cal_session_{$n}_price";
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Session <?= $n ?></h5>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="<?= $kt ?>" class="form-control" value="<?= pc_h($pc[$kt]) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date(s)</label>
                    <input type="text" name="<?= $kd ?>" class="form-control" value="<?= pc_h($pc[$kd]) ?>">
                    <div class="form-text">Free text — e.g. "18–22 May; 13–17 July 2026".</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="<?= $kl ?>" class="form-control" value="<?= pc_h($pc[$kl]) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Duration</label>
                    <input type="text" name="<?= $kdur ?>" class="form-control" value="<?= pc_h($pc[$kdur]) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price</label>
                    <input type="text" name="<?= $kp ?>" class="form-control" value="<?= pc_h($pc[$kp]) ?>">
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_train_cal" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

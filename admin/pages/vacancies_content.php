<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Breadcrumb
    'vacancies_breadcrumb_home_label',
    'vacancies_breadcrumb_current_label',
    'vacancies_breadcrumb_title',
    // Intro info-box
    'vacancies_intro_title',
    'vacancies_intro_body',
    // Section heading
    'vacancies_section_title',
    // How to apply info-box
    'vacancies_apply_title',
    'vacancies_apply_body',
    'vacancies_hr_email',
    // Empty state
    'vacancies_empty_state',
];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vacancies_content'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    if (function_exists('set_flash')) {
        set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    }
    redirect_self();
}

// ── Load current values ───────────────────────────────────────
$pc = pc_get_many($conn, $text_keys);
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vacancies — Page Content</h1>
    <a href="../vacancies.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<p class="text-muted small mb-4">
    Edit the static text on the Vacancies page. Use this for the breadcrumb, intro card,
    section heading, and "How to Apply" card. Individual open positions are managed in
    <a href="index.php?page=vacancies_edit.php">Vacancies (Updates)</a>.
</p>

<form method="POST">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Title (banner heading)</label>
                    <input type="text" name="vacancies_breadcrumb_title" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_title']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">"Home" link label</label>
                    <input type="text" name="vacancies_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_home_label']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Current page label</label>
                    <input type="text" name="vacancies_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_current_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Intro card -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Intro Card ("Working at ESWASA")</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="vacancies_intro_title" class="form-control" value="<?= pc_h($pc['vacancies_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="vacancies_intro_body" class="form-control" rows="6"><?= pc_h($pc['vacancies_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Section heading -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Section Heading</h5>
            <div class="mb-0">
                <label class="form-label">Heading above the vacancy cards</label>
                <input type="text" name="vacancies_section_title" class="form-control" value="<?= pc_h($pc['vacancies_section_title']) ?>">
            </div>
        </div>
    </div>

    <!-- Empty state -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Empty State</h5>
            <div class="mb-0">
                <label class="form-label">Message shown when no vacancies are open</label>
                <input type="text" name="vacancies_empty_state" class="form-control" value="<?= pc_h($pc['vacancies_empty_state']) ?>">
            </div>
        </div>
    </div>

    <!-- Apply card -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">"How to Apply" Card</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="vacancies_apply_title" class="form-control" value="<?= pc_h($pc['vacancies_apply_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="vacancies_apply_body" class="form-control" rows="6"><?= pc_h($pc['vacancies_apply_body']) ?></textarea>
                <div class="form-text">
                    Use <code>[email]</code> anywhere in the body — it will be replaced with a clickable mailto link to the HR email below.
                </div>
            </div>
            <div class="mb-0">
                <label class="form-label">HR email address</label>
                <input type="email" name="vacancies_hr_email" class="form-control" value="<?= pc_h($pc['vacancies_hr_email']) ?>">
                <div class="form-text">Used for the <code>[email]</code> placeholder above and for the "Apply for this Position" button inside the vacancy modal.</div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_vacancies_content" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

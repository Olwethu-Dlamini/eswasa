<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    // Breadcrumb
    'cal_breadcrumb_title',

    // Section 1 — About Us
    'cal_about_title',
    'cal_about_body',

    // Section 2 — Products & Services
    'cal_services_title',
    'cal_services_1_label', 'cal_services_1_desc',
    'cal_services_2_label', 'cal_services_2_desc',
    'cal_services_3_label', 'cal_services_3_desc',
    'cal_services_4_label', 'cal_services_4_desc',
    'cal_services_5_label', 'cal_services_5_desc',

    // Section 3 — What is Calibration
    'cal_whatis_title',
    'cal_whatis_body',

    // Section 4 — Purpose
    'cal_purpose_title',
    'cal_purpose_intro',
    'cal_purpose_1', 'cal_purpose_2', 'cal_purpose_3',
    'cal_purpose_4', 'cal_purpose_5', 'cal_purpose_6',

    // Section 5 — Brands section heading (the logos live in logo_lists)
    'cal_brands_title',
    // 13-20 appended below the array.

    // Section 6 — FAQ
    'cal_faq_1_question', 'cal_faq_1_intro',
    'cal_faq_1_1', 'cal_faq_1_2', 'cal_faq_1_3', 'cal_faq_1_4', 'cal_faq_1_5', 'cal_faq_1_6',
    'cal_faq_2_question', 'cal_faq_2_intro',
    'cal_faq_2_1', 'cal_faq_2_2', 'cal_faq_2_3', 'cal_faq_2_4', 'cal_faq_2_5', 'cal_faq_2_6', 'cal_faq_2_7',

    // CTA
    'cal_cta_title',
    'cal_cta_subtitle',
    'cal_cta_btn1_label', 'cal_cta_btn1_url',
    'cal_cta_btn2_label', 'cal_cta_btn2_url',
];

$image_keys = [
    ];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cal'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        // "Remove" wins over everything: it clears the slot so the brand
        // disappears from the page. Previously an image was only ever written,
        // never cleared, so a brand could not be removed at all — and with all
        // twelve slots occupied, none could be added either. See spec C1.
        if (!empty($_POST[$k . '_remove'])) {
            $kv[$k] = '';
            $alt_k = str_replace('_image', '_alt', $k);
            $kv[$alt_k] = '';
            continue;
        }
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'cal');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'cal');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Calibration page saved.');
    redirect_self();
}

// ── Brands ────────────────────────────────────────────────────
// Managed by the shared logo-strip partial. Was twenty fixed slots, of
// which eight were always empty. Brands are logos only, no link.
$LL_KEY = 'cal_brands';
require __DIR__ . '/_logo_list_crud.php';

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Scales &amp; Metrology Page</h1>
    <a href="../Calibration.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_cal" value="1">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="cal_breadcrumb_title" class="form-control" value="<?= pc_h($pc['cal_breadcrumb_title']) ?>">
            </div>
        </div>
    </div>

    <!-- Section 1 — About -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 1 &mdash; About Us</h5>
            <div class="mb-3">
                <label class="form-label">Section title</label>
                <input type="text" name="cal_about_title" class="form-control" value="<?= pc_h($pc['cal_about_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with one blank line)</label>
                <textarea name="cal_about_body" class="form-control" rows="8"><?= pc_h($pc['cal_about_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Section 2 — Products and Services -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 2 &mdash; Our Products and Services</h5>
            <div class="mb-3">
                <label class="form-label">Section title</label>
                <input type="text" name="cal_services_title" class="form-control" value="<?= pc_h($pc['cal_services_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="row g-2 mb-2">
                    <div class="col-md-4">
                        <label class="form-label">Service <?= $i ?> &mdash; label</label>
                        <input type="text" name="cal_services_<?= $i ?>_label" class="form-control" value="<?= pc_h($pc['cal_services_' . $i . '_label']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Service <?= $i ?> &mdash; description</label>
                        <input type="text" name="cal_services_<?= $i ?>_desc" class="form-control" value="<?= pc_h($pc['cal_services_' . $i . '_desc']) ?>">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Section 3 — What is Calibration -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 3 &mdash; What is Calibration?</h5>
            <div class="mb-3">
                <label class="form-label">Section title</label>
                <input type="text" name="cal_whatis_title" class="form-control" value="<?= pc_h($pc['cal_whatis_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with one blank line)</label>
                <textarea name="cal_whatis_body" class="form-control" rows="8"><?= pc_h($pc['cal_whatis_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Section 4 — Purpose -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 4 &mdash; Purpose of Calibration</h5>
            <div class="mb-3">
                <label class="form-label">Section title</label>
                <input type="text" name="cal_purpose_title" class="form-control" value="<?= pc_h($pc['cal_purpose_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro paragraph</label>
                <textarea name="cal_purpose_intro" class="form-control" rows="2"><?= pc_h($pc['cal_purpose_intro']) ?></textarea>
            </div>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="mb-2">
                    <label class="form-label">Bullet <?= $i ?></label>
                    <input type="text" name="cal_purpose_<?= $i ?>" class="form-control" value="<?= pc_h($pc['cal_purpose_' . $i]) ?>">
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Section 5 — Brands -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 5 &mdash; Brands We Supply and Service</h5>
            <div class="mb-3">
                <label class="form-label">Section title</label>
                <input type="text" name="cal_brands_title" class="form-control" value="<?= pc_h($pc['cal_brands_title']) ?>">
            </div>
        </div>
    </div>


    <!-- Section 6 — FAQ -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 6 &mdash; Calibration FAQ</h5>

            <h6 class="mt-3">FAQ 1</h6>
            <div class="mb-2">
                <label class="form-label">Question</label>
                <input type="text" name="cal_faq_1_question" class="form-control" value="<?= pc_h($pc['cal_faq_1_question']) ?>">
            </div>
            <div class="mb-2">
                <label class="form-label">Intro line</label>
                <input type="text" name="cal_faq_1_intro" class="form-control" value="<?= pc_h($pc['cal_faq_1_intro']) ?>">
            </div>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="mb-2">
                    <label class="form-label">Bullet <?= $i ?></label>
                    <input type="text" name="cal_faq_1_<?= $i ?>" class="form-control" value="<?= pc_h($pc['cal_faq_1_' . $i]) ?>">
                </div>
            <?php endfor; ?>

            <h6 class="mt-4">FAQ 2</h6>
            <div class="mb-2">
                <label class="form-label">Question</label>
                <input type="text" name="cal_faq_2_question" class="form-control" value="<?= pc_h($pc['cal_faq_2_question']) ?>">
            </div>
            <div class="mb-2">
                <label class="form-label">Intro line</label>
                <input type="text" name="cal_faq_2_intro" class="form-control" value="<?= pc_h($pc['cal_faq_2_intro']) ?>">
            </div>
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <div class="mb-2">
                    <label class="form-label">Bullet <?= $i ?></label>
                    <input type="text" name="cal_faq_2_<?= $i ?>" class="form-control" value="<?= pc_h($pc['cal_faq_2_' . $i]) ?>">
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CTA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Call to Action (footer band)</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="cal_cta_title" class="form-control" value="<?= pc_h($pc['cal_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <textarea name="cal_cta_subtitle" class="form-control" rows="2"><?= pc_h($pc['cal_cta_subtitle']) ?></textarea>
            </div>
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label class="form-label">Button 1 &mdash; label</label>
                    <input type="text" name="cal_cta_btn1_label" class="form-control" value="<?= pc_h($pc['cal_cta_btn1_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button 1 &mdash; link (URL or page.php)</label>
                    <input type="text" name="cal_cta_btn1_url" class="form-control" value="<?= pc_h($pc['cal_cta_btn1_url']) ?>">
                </div>
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Button 2 &mdash; label</label>
                    <input type="text" name="cal_cta_btn2_label" class="form-control" value="<?= pc_h($pc['cal_cta_btn2_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button 2 &mdash; link (URL or page.php)</label>
                    <input type="text" name="cal_cta_btn2_url" class="form-control" value="<?= pc_h($pc['cal_cta_btn2_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_cal" value="1" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Save Changes</button>
    </div>
</form>

<?php require __DIR__ . '/_logo_list_ui.php'; ?>

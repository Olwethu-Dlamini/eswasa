<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Breadcrumb / hero
    'services_breadcrumb_title',

    // Services grid header
    'services_grid_title',
    'services_grid_subtitle',

    // Service cards (6) — title / desc / url
    'services_card_1_title', 'services_card_1_desc', 'services_card_1_url',
    'services_card_2_title', 'services_card_2_desc', 'services_card_2_url',
    'services_card_3_title', 'services_card_3_desc', 'services_card_3_url',
    'services_card_4_title', 'services_card_4_desc', 'services_card_4_url',
    'services_card_5_title', 'services_card_5_desc', 'services_card_5_url',
    'services_card_6_title', 'services_card_6_desc', 'services_card_6_url',

    // Info sections (2)
    'services_info_1_title', 'services_info_1_body',
    'services_info_2_title', 'services_info_2_body',

    // Affiliations header
    'services_affil_title',
    'services_affil_subtitle',

    // Affiliations (5) — alt / url (image is separate)
    'services_affil_1_alt', 'services_affil_1_url',
    'services_affil_2_alt', 'services_affil_2_url',
    'services_affil_3_alt', 'services_affil_3_url',
    'services_affil_4_alt', 'services_affil_4_url',
    'services_affil_5_alt', 'services_affil_5_url',
];

$image_keys = [
    'services_affil_1_img',
    'services_affil_2_img',
    'services_affil_3_img',
    'services_affil_4_img',
    'services_affil_5_img',
];

// ── Save handler ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_services'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'services');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'services');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));

// Card section labels (for scannable headings only)
$card_labels = [
    1 => 'Certification',
    2 => 'Product Testing',
    3 => 'Calibration Services',
    4 => 'Standards Development',
    5 => 'Standards Sales',
    6 => 'Training Academy',
];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Services Page</h1>
    <a href="../services.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View Page
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_services" value="1">

    <!-- Hero / Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Hero / Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title (H1 + breadcrumb)</label>
                <input type="text" name="services_breadcrumb_title" class="form-control" value="<?= pc_h($pc['services_breadcrumb_title']) ?>">
            </div>
        </div>
    </div>

    <!-- Services Grid Header -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Services Grid — Section Header</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="services_grid_title" class="form-control" value="<?= pc_h($pc['services_grid_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle / Tagline</label>
                <input type="text" name="services_grid_subtitle" class="form-control" value="<?= pc_h($pc['services_grid_subtitle']) ?>">
            </div>
        </div>
    </div>

    <!-- Service Cards (6) -->
    <?php for ($i = 1; $i <= 6; $i++): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Service Card <?= $i ?> &mdash; <?= htmlspecialchars($card_labels[$i]) ?></h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="services_card_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['services_card_' . $i . '_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="services_card_<?= $i ?>_desc" class="form-control" rows="3"><?= pc_h($pc['services_card_' . $i . '_desc']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Link URL <small class="text-muted">(internal page or full URL)</small></label>
                <input type="text" name="services_card_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc['services_card_' . $i . '_url']) ?>">
            </div>
            <div class="form-text">Note: the card icon is a fixed SVG and not editable from this screen.</div>
        </div>
    </div>
    <?php endfor; ?>

    <!-- Info Sections (2) -->
    <?php for ($i = 1; $i <= 2; $i++): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Info Section <?= $i ?></h5>
            <div class="mb-3">
                <label class="form-label">Heading</label>
                <input type="text" name="services_info_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['services_info_' . $i . '_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body <small class="text-muted">(use a blank line between paragraphs)</small></label>
                <textarea name="services_info_<?= $i ?>_body" class="form-control" rows="6"><?= pc_h($pc['services_info_' . $i . '_body']) ?></textarea>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <!-- Affiliations Header -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Affiliations &mdash; Section Header</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="services_affil_title" class="form-control" value="<?= pc_h($pc['services_affil_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle / Tagline</label>
                <input type="text" name="services_affil_subtitle" class="form-control" value="<?= pc_h($pc['services_affil_subtitle']) ?>">
            </div>
        </div>
    </div>

    <!-- Affiliation Logos (5) -->
    <?php for ($i = 1; $i <= 5; $i++): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Affiliation <?= $i ?></h5>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Logo Image</label>
                    <div class="mb-2">
                        <img data-crop-preview="services_affil_<?= $i ?>_img_preview"
                             src="<?= !empty($pc['services_affil_' . $i . '_img']) ? '../' . pc_h(pc_image_src($pc['services_affil_' . $i . '_img'])) : '' ?>"
                             style="max-height:100px;border:1px solid #ddd;background:#fff;padding:8px;<?= empty($pc['services_affil_' . $i . '_img']) ? 'display:none;' : '' ?>"
                             onload="this.style.display='inline-block'" alt="">
                    </div>
                    <input type="file" name="services_affil_<?= $i ?>_img_file" accept="image/*" class="form-control crop-input"
                           data-crop-label="Affiliation <?= $i ?> Logo">
                    <input type="hidden" name="services_affil_<?= $i ?>_img_cropped">
                    <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Alt Text / Name</label>
                        <input type="text" name="services_affil_<?= $i ?>_alt" class="form-control" value="<?= pc_h($pc['services_affil_' . $i . '_alt']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">External URL</label>
                        <input type="url" name="services_affil_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc['services_affil_' . $i . '_url']) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_services" value="1" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

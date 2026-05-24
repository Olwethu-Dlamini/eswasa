<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Breadcrumb / hero
    'cert_breadcrumb_title',

    // Section 1 — Your Path to Quality Excellence
    'cert_path_title',
    'cert_path_intro',
    'cert_why_title',
    'cert_why_body',
    'cert_focus_title',
    'cert_focus_body',

    // Marks grid (4 cards)
    'cert_mark_1_alt', 'cert_mark_1_title', 'cert_mark_1_desc', 'cert_mark_1_explore_url',
    'cert_mark_2_alt', 'cert_mark_2_title', 'cert_mark_2_desc', 'cert_mark_2_explore_url',
    'cert_mark_3_alt', 'cert_mark_3_title', 'cert_mark_3_desc', 'cert_mark_3_explore_url',
    'cert_mark_4_alt', 'cert_mark_4_title', 'cert_mark_4_desc', 'cert_mark_4_explore_url',

    // Section 2 — Benefits
    'cert_benefits_title',
    'cert_benefits_intro',
    'cert_card_1_title', 'cert_card_1_body', 'cert_card_1_list_label', 'cert_card_1_list',
    'cert_card_1_image_alt', 'cert_card_1_btn_label', 'cert_card_1_btn_url',
    'cert_card_2_title', 'cert_card_2_body', 'cert_card_2_list_label', 'cert_card_2_list',
    'cert_card_2_image_alt', 'cert_card_2_btn_label', 'cert_card_2_btn_url',

    // Section 3 — Steps
    'cert_steps_title',
    'cert_steps_subtitle',
    'cert_steps_image_alt',

    // Section 4 — CTA
    'cert_cta_title', 'cert_cta_subtitle',
    'cert_cta_1_label', 'cert_cta_1_url',
    'cert_cta_2_label', 'cert_cta_2_url',
    'cert_cta_3_label', 'cert_cta_3_url',
];

$image_keys = [
    'cert_mark_1_image',
    'cert_mark_2_image',
    'cert_mark_3_image',
    'cert_mark_4_image',
    'cert_card_1_image',
    'cert_card_2_image',
    'cert_steps_image',
];

// ── Save handler ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cert'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        // Bullet lists need newlines preserved — pc_strip_text keeps \n; collapse \n{3,} → \n\n.
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'cert');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Certification Page</h1>
    <a href="../Certification.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View Page
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_cert" value="1">

    <!-- Hero / Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Hero / Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title (H1)</label>
                <input type="text" name="cert_breadcrumb_title" class="form-control" value="<?= pc_h($pc['cert_breadcrumb_title']) ?>">
            </div>
        </div>
    </div>

    <!-- Section 1 — Your Path -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 1 — Your Path to Quality Excellence</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="cert_path_title" class="form-control" value="<?= pc_h($pc['cert_path_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Paragraph (featured card)</label>
                <textarea name="cert_path_intro" class="form-control" rows="5"><?= pc_h($pc['cert_path_intro']) ?></textarea>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Left Card — Why Certify</h6>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="cert_why_title" class="form-control" value="<?= pc_h($pc['cert_why_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea name="cert_why_body" class="form-control" rows="5"><?= pc_h($pc['cert_why_body']) ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Right Card — Our Focus Areas</h6>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="cert_focus_title" class="form-control" value="<?= pc_h($pc['cert_focus_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea name="cert_focus_body" class="form-control" rows="5"><?= pc_h($pc['cert_focus_body']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Marks Grid (4 cards) -->
    <?php for ($i = 1; $i <= 4; $i++): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Certification Mark <?= $i ?></h5>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Mark Image</label>
                    <?php if (!empty($pc['cert_mark_' . $i . '_image'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['cert_mark_' . $i . '_image'])) ?>" style="max-height:120px;border:1px solid #ddd;background:#f8f9fa;padding:8px"></div>
                    <?php endif; ?>
                    <input type="file" name="cert_mark_<?= $i ?>_image_file" accept="image/*" class="form-control">
                    <div class="form-text">Leave empty to keep current image.</div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="cert_mark_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['cert_mark_' . $i . '_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" name="cert_mark_<?= $i ?>_alt" class="form-control" value="<?= pc_h($pc['cert_mark_' . $i . '_alt']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="cert_mark_<?= $i ?>_desc" class="form-control" rows="4"><?= pc_h($pc['cert_mark_' . $i . '_desc']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">"Explore" Link URL</label>
                        <input type="text" name="cert_mark_<?= $i ?>_explore_url" class="form-control" value="<?= pc_h($pc['cert_mark_' . $i . '_explore_url']) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <!-- Section 2 — Benefits Header -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 2 — Benefits Header</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="cert_benefits_title" class="form-control" value="<?= pc_h($pc['cert_benefits_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Paragraph</label>
                <textarea name="cert_benefits_intro" class="form-control" rows="4"><?= pc_h($pc['cert_benefits_intro']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Benefits Cards (2) -->
    <?php for ($i = 1; $i <= 2; $i++): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Benefits Card <?= $i ?></h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="cert_card_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['cert_card_' . $i . '_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body Paragraph</label>
                <textarea name="cert_card_<?= $i ?>_body" class="form-control" rows="5"><?= pc_h($pc['cert_card_' . $i . '_body']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Bullet List Heading (e.g. "You'll be able to:")</label>
                <input type="text" name="cert_card_<?= $i ?>_list_label" class="form-control" value="<?= pc_h($pc['cert_card_' . $i . '_list_label']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Bullet List Items <small class="text-muted">(one per line — each line becomes a bullet)</small></label>
                <textarea name="cert_card_<?= $i ?>_list" class="form-control" rows="5"><?= pc_h($pc['cert_card_' . $i . '_list']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Background Mark Image</label>
                    <?php if (!empty($pc['cert_card_' . $i . '_image'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['cert_card_' . $i . '_image'])) ?>" style="max-height:120px;border:1px solid #ddd;background:#f8f9fa;padding:8px"></div>
                    <?php endif; ?>
                    <input type="file" name="cert_card_<?= $i ?>_image_file" accept="image/*" class="form-control">
                    <div class="form-text">Leave empty to keep current image.</div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <input type="text" name="cert_card_<?= $i ?>_image_alt" class="form-control" value="<?= pc_h($pc['cert_card_' . $i . '_image_alt']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button Label</label>
                        <input type="text" name="cert_card_<?= $i ?>_btn_label" class="form-control" value="<?= pc_h($pc['cert_card_' . $i . '_btn_label']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button URL</label>
                        <input type="text" name="cert_card_<?= $i ?>_btn_url" class="form-control" value="<?= pc_h($pc['cert_card_' . $i . '_btn_url']) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <!-- Section 3 — Steps -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 3 — Steps to Certification</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="cert_steps_title" class="form-control" value="<?= pc_h($pc['cert_steps_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <input type="text" name="cert_steps_subtitle" class="form-control" value="<?= pc_h($pc['cert_steps_subtitle']) ?>">
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Steps Diagram Image</label>
                    <?php if (!empty($pc['cert_steps_image'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['cert_steps_image'])) ?>" style="max-height:160px;border:1px solid #ddd;background:#f8f9fa;padding:8px"></div>
                    <?php endif; ?>
                    <input type="file" name="cert_steps_image_file" accept="image/*" class="form-control">
                    <div class="form-text">Leave empty to keep current image.</div>
                </div>
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Image Alt Text</label>
                        <textarea name="cert_steps_image_alt" class="form-control" rows="3"><?= pc_h($pc['cert_steps_image_alt']) ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 4 — CTA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section 4 — Call-to-Action</h5>
            <div class="mb-3">
                <label class="form-label">CTA Title</label>
                <input type="text" name="cert_cta_title" class="form-control" value="<?= pc_h($pc['cert_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">CTA Subtitle</label>
                <input type="text" name="cert_cta_subtitle" class="form-control" value="<?= pc_h($pc['cert_cta_subtitle']) ?>">
            </div>
            <hr>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="row mb-2">
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> Label</label>
                    <input type="text" name="cert_cta_<?= $i ?>_label" class="form-control" value="<?= pc_h($pc['cert_cta_' . $i . '_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> URL</label>
                    <input type="text" name="cert_cta_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc['cert_cta_' . $i . '_url']) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_cert" value="1" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

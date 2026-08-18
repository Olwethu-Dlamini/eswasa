<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    // Hero / breadcrumb
    'ingelo_hero_title',
    // Intro
    'ingelo_intro_image_alt',
    'ingelo_intro_title',
    'ingelo_intro_body',
    // What is the scheme?
    'ingelo_section_what_title',
    'ingelo_section_what_lead',
    'ingelo_section_what_item_1_title',
    'ingelo_section_what_item_1_body',
    'ingelo_section_what_item_2_title',
    'ingelo_section_what_item_2_body',
    // Benefits
    'ingelo_benefits_title',
    'ingelo_benefit_1_title', 'ingelo_benefit_1_body',
    'ingelo_benefit_2_title', 'ingelo_benefit_2_body',
    'ingelo_benefit_3_title', 'ingelo_benefit_3_body',
    'ingelo_benefit_4_title', 'ingelo_benefit_4_body',
    'ingelo_benefit_5_title', 'ingelo_benefit_5_body',
    'ingelo_benefit_6_title', 'ingelo_benefit_6_body',
    'ingelo_benefit_7_title', 'ingelo_benefit_7_body',
    'ingelo_benefit_8_title', 'ingelo_benefit_8_body',
    // Who can apply
    'ingelo_who_title',
    'ingelo_who_item_1',
    'ingelo_who_item_2',
    'ingelo_who_item_3',
    // Standards
    'ingelo_standards_title',
    'ingelo_standards_list',
    // Application
    'ingelo_apply_title',
    'ingelo_apply_lead',
    'ingelo_apply_step_1',
    'ingelo_apply_step_2',
    'ingelo_apply_button_text',
    'ingelo_apply_button_url',
    'ingelo_apply_support_note',
    // CTA
    'ingelo_cta_title',
    'ingelo_cta_subtitle',
    'ingelo_cta_btn_1_text', 'ingelo_cta_btn_1_url',
    'ingelo_cta_btn_2_text', 'ingelo_cta_btn_2_url',
];

$image_keys = [
    'ingelo_intro_image',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ingelo'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }

    // Document uploads win over whatever is in the matching URL box, so an
    // existing external link keeps working when nothing new is uploaded.
    // Rejections are surfaced rather than dropped. See spec item C2.
    $doc_errors = [];
    foreach (['ingelo_apply_button_url'] as $dk) {
        $up = pc_upload_document($dk . '_file', ADMIN_ROOT . '/uploads/', 'doc');
        if (is_string($up) && strpos($up, 'ERR:') === 0) {
            $doc_errors[] = substr($up, 4);
        } elseif (is_string($up)) {
            $kv[$dk] = $up;
        }
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'ingelo');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'ingelo');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    if ($doc_errors) {
        set_flash('danger', 'Saved, but a document was not replaced: ' . implode(' ', $doc_errors));
    } else {
        set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Ingelo page saved.');
    }
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Ingelo Certification Page</h1>
    <a href="../ingelo.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Hero / Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Hero / Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Hero Title (also breadcrumb label)</label>
                <input type="text" name="ingelo_hero_title" class="form-control" value="<?= pc_h($pc['ingelo_hero_title']) ?>">
            </div>
            <div class="form-text">
                Banner background image is managed in <a href="index.php?page=breadcrumbs_edit.php">Breadcrumb Images</a>.
            </div>
        </div>
    </div>

    <!-- Intro Section -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Intro Section</h5>
            <div class="mb-3">
                <label class="form-label">Intro Image</label>
                <div class="mb-2">
                    <img data-crop-preview="ingelo_intro_image_preview"
                         src="<?= !empty($pc['ingelo_intro_image']) ? '../' . pc_h(pc_image_src($pc['ingelo_intro_image'])) : '' ?>"
                         style="max-height:120px;border:1px solid #ddd;<?= empty($pc['ingelo_intro_image']) ? 'display:none;' : '' ?>"
                         onload="this.style.display='inline-block'" alt="">
                </div>
                <input type="file" name="ingelo_intro_image_file" accept="image/*" class="form-control crop-input"
                       data-crop-label="Intro Image">
                <input type="hidden" name="ingelo_intro_image_cropped">
                <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Image Alt Text</label>
                <input type="text" name="ingelo_intro_image_alt" class="form-control" value="<?= pc_h($pc['ingelo_intro_image_alt']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Title</label>
                <input type="text" name="ingelo_intro_title" class="form-control" value="<?= pc_h($pc['ingelo_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Body (separate paragraphs with a blank line)</label>
                <textarea name="ingelo_intro_body" class="form-control" rows="6"><?= pc_h($pc['ingelo_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- What is the scheme? -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">What is the Scheme?</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ingelo_section_what_title" class="form-control" value="<?= pc_h($pc['ingelo_section_what_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Lead Paragraph</label>
                <textarea name="ingelo_section_what_lead" class="form-control" rows="3"><?= pc_h($pc['ingelo_section_what_lead']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Item 1 Title</label>
                    <input type="text" name="ingelo_section_what_item_1_title" class="form-control" value="<?= pc_h($pc['ingelo_section_what_item_1_title']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Item 1 Body</label>
                    <textarea name="ingelo_section_what_item_1_body" class="form-control" rows="2"><?= pc_h($pc['ingelo_section_what_item_1_body']) ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Item 2 Title</label>
                    <input type="text" name="ingelo_section_what_item_2_title" class="form-control" value="<?= pc_h($pc['ingelo_section_what_item_2_title']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Item 2 Body</label>
                    <textarea name="ingelo_section_what_item_2_body" class="form-control" rows="2"><?= pc_h($pc['ingelo_section_what_item_2_body']) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Benefits of Participation</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ingelo_benefits_title" class="form-control" value="<?= pc_h($pc['ingelo_benefits_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="row border-top pt-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Benefit <?= $i ?> Title</label>
                        <input type="text" name="ingelo_benefit_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc['ingelo_benefit_' . $i . '_title']) ?>">
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Benefit <?= $i ?> Body</label>
                        <textarea name="ingelo_benefit_<?= $i ?>_body" class="form-control" rows="2"><?= pc_h($pc['ingelo_benefit_' . $i . '_body']) ?></textarea>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Who Can Apply -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Who Can Apply?</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ingelo_who_title" class="form-control" value="<?= pc_h($pc['ingelo_who_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="mb-3">
                    <label class="form-label">Eligibility Item <?= $i ?></label>
                    <textarea name="ingelo_who_item_<?= $i ?>" class="form-control" rows="2"><?= pc_h($pc['ingelo_who_item_' . $i]) ?></textarea>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Standards -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Available Standards</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ingelo_standards_title" class="form-control" value="<?= pc_h($pc['ingelo_standards_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Standards List (one item per line)</label>
                <textarea name="ingelo_standards_list" class="form-control" rows="14"><?= pc_h($pc['ingelo_standards_list']) ?></textarea>
                <div class="form-text">Each line becomes a list item on the public page.</div>
            </div>
        </div>
    </div>

    <!-- Application -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">How to Apply</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ingelo_apply_title" class="form-control" value="<?= pc_h($pc['ingelo_apply_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Lead Paragraph</label>
                <textarea name="ingelo_apply_lead" class="form-control" rows="2"><?= pc_h($pc['ingelo_apply_lead']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Step 1</label>
                <textarea name="ingelo_apply_step_1" class="form-control" rows="2"><?= pc_h($pc['ingelo_apply_step_1']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Step 2</label>
                <textarea name="ingelo_apply_step_2" class="form-control" rows="2"><?= pc_h($pc['ingelo_apply_step_2']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Download Button Text</label>
                    <input type="text" name="ingelo_apply_button_text" class="form-control" value="<?= pc_h($pc['ingelo_apply_button_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Download Button URL</label>
                    <?php $doc_key = 'ingelo_apply_button_url'; $doc_label = 'Application form (Apply button target)';
                          include __DIR__ . '/../includes/document_field.php'; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Support Note</label>
                <textarea name="ingelo_apply_support_note" class="form-control" rows="2"><?= pc_h($pc['ingelo_apply_support_note']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Call-to-Action Section</h5>
            <div class="mb-3">
                <label class="form-label">CTA Title</label>
                <input type="text" name="ingelo_cta_title" class="form-control" value="<?= pc_h($pc['ingelo_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">CTA Subtitle</label>
                <textarea name="ingelo_cta_subtitle" class="form-control" rows="2"><?= pc_h($pc['ingelo_cta_subtitle']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Button 1 Text</label>
                    <input type="text" name="ingelo_cta_btn_1_text" class="form-control" value="<?= pc_h($pc['ingelo_cta_btn_1_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Button 1 URL</label>
                    <input type="text" name="ingelo_cta_btn_1_url" class="form-control" value="<?= pc_h($pc['ingelo_cta_btn_1_url']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Button 2 Text</label>
                    <input type="text" name="ingelo_cta_btn_2_text" class="form-control" value="<?= pc_h($pc['ingelo_cta_btn_2_text']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Button 2 URL</label>
                    <input type="text" name="ingelo_cta_btn_2_url" class="form-control" value="<?= pc_h($pc['ingelo_cta_btn_2_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <button type="submit" name="save_ingelo" class="btn btn-primary">Save Changes</button>
    </div>
</form>

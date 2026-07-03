<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    'prod_hero_title',
    'prod_about_title', 'prod_about_body',
    'prod_testing_title', 'prod_testing_body',
    'prod_process_title',
    'prod_step_1_text', 'prod_step_2_text', 'prod_step_3_text',
    'prod_step_4_text', 'prod_step_5_text', 'prod_step_6_text',
    'prod_certified_title',
    'prod_img_1_alt', 'prod_img_2_alt', 'prod_img_3_alt', 'prod_img_4_alt',
    'prod_producers_title',
    'prod_cta_title', 'prod_cta_subtitle',
    'prod_cta_btn1_label', 'prod_cta_btn1_url',
    'prod_cta_btn2_label', 'prod_cta_btn2_url',
];
$image_keys = [
    'prod_img_1', 'prod_img_2', 'prod_img_3', 'prod_img_4',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_prod'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw upload.
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'prod');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'prod');
        }
        if (is_string($path)) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Product Certification</h1>
    <a href="../product.php" target="_blank" class="btn btn-outline-secondary btn-sm">View Public Page</a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Hero / Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Hero / Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title</label>
                <input type="text" name="prod_hero_title" class="form-control" value="<?= pc_h($pc['prod_hero_title']) ?>">
            </div>
        </div>
    </div>

    <!-- About Section -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">About Product Certification</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="prod_about_title" class="form-control" value="<?= pc_h($pc['prod_about_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="prod_about_body" class="form-control" rows="6"><?= pc_h($pc['prod_about_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Product Testing Services -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Product Testing Services</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="prod_testing_title" class="form-control" value="<?= pc_h($pc['prod_testing_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="prod_testing_body" class="form-control" rows="6"><?= pc_h($pc['prod_testing_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Certification Process -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Product Certification Process</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="prod_process_title" class="form-control" value="<?= pc_h($pc['prod_process_title']) ?>">
            </div>
            <p class="form-text mb-3">These six steps appear in the mobile stacked list. The desktop SVG diagram is a fixed graphic and is not editable from here.</p>
            <?php for ($i = 1; $i <= 6; $i++): $k = "prod_step_{$i}_text"; ?>
            <div class="mb-3">
                <label class="form-label">Step <?= $i ?></label>
                <input type="text" name="<?= $k ?>" class="form-control" value="<?= pc_h($pc[$k]) ?>">
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Certified Products Gallery -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">ESWASA Certified Products (Gallery)</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="prod_certified_title" class="form-control" value="<?= pc_h($pc['prod_certified_title']) ?>">
            </div>
            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++):
                    $imgK = "prod_img_{$i}";
                    $altK = "prod_img_{$i}_alt";
                ?>
                <div class="col-md-6 mb-3">
                    <fieldset class="border rounded p-3 h-100">
                        <legend class="float-none w-auto px-2 fs-6">Image <?= $i ?></legend>
                        <div class="mb-2">
                            <img data-crop-preview="<?= $imgK ?>_preview"
                                 src="<?= !empty($pc[$imgK]) ? '../' . pc_h(pc_image_src($pc[$imgK])) : '' ?>"
                                 style="max-height:120px;border:1px solid #ddd;<?= empty($pc[$imgK]) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Replace Image</label>
                            <input type="file" name="<?= $imgK ?>_file" accept="image/*" class="form-control crop-input"
                                   data-crop-w="1200" data-crop-h="600" data-crop-label="Certified Product Photo <?= $i ?>">
                            <input type="hidden" name="<?= $imgK ?>_cropped">
                            <div class="form-text">Pick a photo &mdash; the cropper opens at 1200 &times; 600 px (matches the gallery). Leave empty to keep current.</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Alt Text</label>
                            <input type="text" name="<?= $altK ?>" class="form-control" value="<?= pc_h($pc[$altK]) ?>">
                        </div>
                    </fieldset>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Producers Section -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Certified Product Producers</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="prod_producers_title" class="form-control" value="<?= pc_h($pc['prod_producers_title']) ?>">
            </div>
            <p class="form-text mb-0">The producer tiles (logos, products, standards) are managed in code via the <code>$producers</code> array in <code>product.php</code>. Edit that file directly to add or remove producers.</p>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Call to Action (Footer Banner)</h5>
            <div class="mb-3">
                <label class="form-label">CTA Title</label>
                <input type="text" name="prod_cta_title" class="form-control" value="<?= pc_h($pc['prod_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">CTA Subtitle</label>
                <input type="text" name="prod_cta_subtitle" class="form-control" value="<?= pc_h($pc['prod_cta_subtitle']) ?>">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Button 1 Label</label>
                        <input type="text" name="prod_cta_btn1_label" class="form-control" value="<?= pc_h($pc['prod_cta_btn1_label']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button 1 URL</label>
                        <input type="text" name="prod_cta_btn1_url" class="form-control" value="<?= pc_h($pc['prod_cta_btn1_url']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Button 2 Label</label>
                        <input type="text" name="prod_cta_btn2_label" class="form-control" value="<?= pc_h($pc['prod_cta_btn2_label']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button 2 URL</label>
                        <input type="text" name="prod_cta_btn2_url" class="form-control" value="<?= pc_h($pc['prod_cta_btn2_url']) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 pb-5">
        <button type="submit" name="save_prod" class="btn btn-primary">Save Changes</button>
        <a href="../product.php" target="_blank" class="btn btn-outline-secondary">View Public Page</a>
    </div>
</form>

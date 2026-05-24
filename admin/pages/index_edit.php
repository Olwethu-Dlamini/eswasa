<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key registry ───────────────────────────────────────────────
$heading_keys = [
    'index_discover_heading',
    'index_marks_heading',
    'index_affiliations_heading',
];

$discover_text_keys = [];
$discover_url_keys  = [];
for ($i = 1; $i <= 4; $i++) {
    $discover_text_keys[] = "index_discover_{$i}_title";
    $discover_text_keys[] = "index_discover_{$i}_desc";
    $discover_url_keys[]  = "index_discover_{$i}_url";
}

$mark_text_keys  = [];
$mark_url_keys   = [];
$mark_image_keys = [];
for ($i = 1; $i <= 4; $i++) {
    $mark_text_keys[]  = "index_mark_{$i}_title";
    $mark_text_keys[]  = "index_mark_{$i}_desc";
    $mark_url_keys[]   = "index_mark_{$i}_explore_url";
    $mark_url_keys[]   = "index_mark_{$i}_verify_url";
    $mark_image_keys[] = "index_mark_{$i}_image";
}

$aff_text_keys  = [];
$aff_url_keys   = [];
$aff_image_keys = [];
for ($i = 1; $i <= 11; $i++) {
    $aff_text_keys[]  = "index_affiliation_{$i}_alt";
    $aff_url_keys[]   = "index_affiliation_{$i}_url";
    $aff_image_keys[] = "index_affiliation_{$i}_logo";
}

$text_keys  = array_merge($heading_keys, $discover_text_keys, $mark_text_keys, $aff_text_keys);
$url_keys   = array_merge($discover_url_keys, $mark_url_keys, $aff_url_keys);
$image_keys = array_merge($mark_image_keys, $aff_image_keys);

// ── Save handler ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_index'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($url_keys as $k) {
        // URLs: keep as-is but trim/strip tags. Don't apply paragraph squash.
        $v = trim((string)($_POST[$k] ?? ''));
        $v = strip_tags($v);
        $kv[$k] = $v;
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'index');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Home page content saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $url_keys, $image_keys));

// Default labels for affiliation display
$aff_default_alts = ['ISO','IEC','ITU','IAF','ILAC','SABS','SADCAS','SADC','SADCSTAN','ARSO','ASTM'];
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Home Page Content</h1>
</div>

<p class="text-muted">Edit the Discover cards, Certification Marks and Affiliation logos that appear on the home page. Banner slider and statistics are managed on separate pages.</p>

<form method="POST" enctype="multipart/form-data">

    <!-- Section Headings -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Section Headings</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Discover heading</label>
                    <input type="text" name="index_discover_heading" class="form-control" value="<?= pc_h($pc['index_discover_heading']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Certification Marks heading</label>
                    <input type="text" name="index_marks_heading" class="form-control" value="<?= pc_h($pc['index_marks_heading']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Affiliations heading</label>
                    <input type="text" name="index_affiliations_heading" class="form-control" value="<?= pc_h($pc['index_affiliations_heading']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Discover Cards -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Discover Section (4 cards)</h5>
            <p class="text-muted small">Icons are fixed decorative SVGs and are not editable. Edit the title, description and link target.</p>
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">Card <?= $i ?></h6>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Title</label>
                        <input type="text" name="index_discover_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc["index_discover_{$i}_title"]) ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="index_discover_<?= $i ?>_desc" class="form-control" rows="2"><?= pc_h($pc["index_discover_{$i}_desc"]) ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Link URL</label>
                        <input type="text" name="index_discover_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc["index_discover_{$i}_url"]) ?>" placeholder="Certification.php or https://...">
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Certification Marks -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Marks Section (4 cards)</h5>
            <?php for ($i = 1; $i <= 4; $i++):
                $img_key = "index_mark_{$i}_image";
                $current_img = $pc[$img_key] ?? '';
            ?>
                <div class="border rounded p-3 mb-3">
                    <h6 class="mb-3">Mark <?= $i ?></h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Title</label>
                                <input type="text" name="index_mark_<?= $i ?>_title" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_title"]) ?>">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Description</label>
                                <textarea name="index_mark_<?= $i ?>_desc" class="form-control" rows="3"><?= pc_h($pc["index_mark_{$i}_desc"]) ?></textarea>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Explore URL</label>
                                    <input type="text" name="index_mark_<?= $i ?>_explore_url" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_explore_url"]) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Verify URL</label>
                                    <input type="text" name="index_mark_<?= $i ?>_verify_url" class="form-control" value="<?= pc_h($pc["index_mark_{$i}_verify_url"]) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Mark Image</label>
                            <?php if (!empty($current_img)): ?>
                                <div class="mb-2">
                                    <img src="../<?= pc_h(pc_image_src($current_img)) ?>" style="max-height:120px;max-width:100%;border:1px solid #ddd;padding:4px;background:#fff;" alt="">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="index_mark_<?= $i ?>_image_file" accept="image/*" class="form-control">
                            <div class="form-text">Leave empty to keep current image.</div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Affiliations -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Affiliations Section (11 logos)</h5>
            <p class="text-muted small">Logos appear in the seamless scrolling band. The same set is duplicated automatically for the loop.</p>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 11; $i++):
                    $logo_key = "index_affiliation_{$i}_logo";
                    $current_logo = $pc[$logo_key] ?? '';
                    $default_alt = $aff_default_alts[$i - 1] ?? ('Affiliation ' . $i);
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-2">Affiliation <?= $i ?> <span class="text-muted small">(<?= htmlspecialchars($default_alt) ?>)</span></h6>
                            <?php if (!empty($current_logo)): ?>
                                <div class="mb-2 text-center" style="background:#fff;border:1px solid #eee;padding:8px;">
                                    <img src="../<?= pc_h(pc_image_src($current_logo)) ?>" style="max-height:70px;max-width:100%;" alt="">
                                </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Logo image</label>
                                <input type="file" name="index_affiliation_<?= $i ?>_logo_file" accept="image/*" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Link URL</label>
                                <input type="url" name="index_affiliation_<?= $i ?>_url" class="form-control form-control-sm" value="<?= pc_h($pc["index_affiliation_{$i}_url"]) ?>" placeholder="https://...">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold">Alt text</label>
                                <input type="text" name="index_affiliation_<?= $i ?>_alt" class="form-control form-control-sm" value="<?= pc_h($pc["index_affiliation_{$i}_alt"]) ?>">
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="text-end mb-4">
        <button type="submit" name="save_index" class="btn btn-primary px-5">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

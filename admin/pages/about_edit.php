<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';

$upload_dir = ADMIN_ROOT . '/uploads/';

// Ensure upload directory exists and is writable
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {

    $image_updates = [];
    $image_errors  = [];
    $image_keys = ['about_values_image'];
    // Affiliation and accreditation logo strips were a hardcoded PHP array on
    // about-us.php until Batch B. See spec item B2.
    for ($i = 1; $i <= 10; $i++) { $image_keys[] = "about_affiliation_{$i}_logo"; }
    for ($i = 1; $i <= 4;  $i++) { $image_keys[] = "about_accreditation_{$i}_logo"; }

    // Check uploads dir is writable before attempting image saves
    $uploads_writable = is_dir($upload_dir) && is_writable($upload_dir);

    foreach ($image_keys as $key) {
        $base64 = $_POST[$key . '_cropped'] ?? '';
        if (!empty($base64) && strpos($base64, 'data:image') === 0) {
            if (!$uploads_writable) {
                $image_errors[] = $key . ' (uploads folder not writable)';
                continue;
            }

            list($type, $data) = explode(';', $base64);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            if ($data === false) {
                $image_errors[] = $key . ' (invalid image data)';
                continue;
            }

            $ext = 'jpg';
            if (strpos($type, 'image/png') !== false) $ext = 'png';
            if (strpos($type, 'image/webp') !== false) $ext = 'webp';

            $new_name = uniqid('about_', true) . '.' . $ext;
            $dest     = $upload_dir . $new_name;

            if (file_put_contents($dest, $data)) {
                $image_updates[$key] = 'admin/uploads/' . $new_name;
            } else {
                $image_errors[] = $key . ' (file write failed)';
            }
        }
    }

    // These are page_content keys, so they take the same absent-means-unchanged
    // guard as every other editor. Written as an explicit list rather than a
    // loop, they were missed when that guard was rolled out — a partial post
    // blanked the About Us body text. See spec item C6.
    $text_fields = [];
    foreach ([
        'about_intro',
        'about_vision',
        'about_mission',
        'about_history',
        'about_val_transparency',
        'about_val_people',
        'about_val_responsiveness',
        'about_val_innovation',
        'about_val_professionalism',
        'about_breadcrumb_title',
    ] as $k) {
        $text_fields[$k] = pc_post_value($k);
    }

    // Section headings + logo strip labels, all hardcoded before Batch B.
    $extra_text_keys = [
        'about_heading_main', 'about_heading_visionmission', 'about_heading_vision',
        'about_heading_mission', 'about_heading_values', 'about_heading_history',
        'about_val_transparency_title', 'about_val_responsiveness_title',
        'about_val_people_title', 'about_val_innovation_title',
        'about_val_professionalism_title',
        'about_affiliations_title', 'about_accreditation_title', 'about_accreditation_body',
    ];
    for ($i = 1; $i <= 10; $i++) {
        $extra_text_keys[] = "about_affiliation_{$i}_alt";
        $extra_text_keys[] = "about_affiliation_{$i}_url";
    }
    for ($i = 1; $i <= 4; $i++) {
        $extra_text_keys[] = "about_accreditation_{$i}_alt";
        $extra_text_keys[] = "about_accreditation_{$i}_url";
    }
    foreach ($extra_text_keys as $k) {
        $text_fields[$k] = pc_post_value($k);
    }

    $all = array_merge($text_fields, $image_updates);

    // This page writes through its own upsert rather than pc_save_many(), so it
    // has to apply the same rule: a null from pc_post_value() means the field
    // was not submitted and must be left alone. Without this the null binds as
    // SQL NULL and wipes the stored value — worse than the empty string the
    // guard was introduced to prevent. See spec item C6.
    $all = array_filter($all, static function ($v) { return $v !== null; });

    $upsert = $conn->prepare("INSERT INTO page_content (page_key, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");

    $save_errors = [];
    foreach ($all as $key => $value) {
        $upsert->bind_param('ss', $key, $value);
        if (!$upsert->execute()) $save_errors[] = $key;
    }
    $upsert->close();

    if (!empty($image_errors)) {
        set_flash('danger', 'Image save failed: ' . implode(', ', $image_errors));
    } elseif (!empty($save_errors)) {
        set_flash('danger', 'Error saving: ' . implode(', ', $save_errors));
    } else {
        set_flash('success', 'About Us updated.');
    }

    // Redirect back to this page (uses robust SCRIPT_NAME-based redirect)
    redirect_self();
}

// ── Load Values ──────────────────────────────────────────────
// Keys and defaults come from the shared registry, so this editor and
// about-us.php can never drift apart. Defaults are applied for keys with no
// stored row yet, otherwise the form would show blank logo and heading fields
// for content the page is visibly rendering. See spec item B2.
require_once __DIR__ . '/../../includes/cms_keys_about.php';
$keys = $about_keys;

$pc = pc_get_many($conn, $keys, $about_defaults);

function e($v) { return htmlspecialchars($v ?? ''); }
/**
 * Web path for an image preview in this editor.
 *
 * An empty value used to return just "../", which the browser requests as a
 * page and then draws as a broken-image icon. Invisible until Batch B added
 * fourteen logo slots here, most of them empty — five broken icons on one
 * screen. See spec item C5 / UI pass.
 */
function img_preview_src($path) {
    $p = trim((string)$path);
    return $p === '' ? '' : '../' . ltrim($p, '/');
}

/** An image preview, or a neutral "no image yet" tile when the slot is unset. */
function img_preview_tag($id, $path) {
    $src = img_preview_src($path);
    if ($src === '') {
        return '<div class="logo-card-fixed mb-2 img-preview-empty">'
             . '<img id="prev_' . htmlspecialchars($id) . '" alt="" hidden>'
             . '<span>No image yet</span></div>';
    }
    return '<div class="logo-card-fixed mb-2">'
         . '<img id="prev_' . htmlspecialchars($id) . '" src="' . htmlspecialchars($src) . '" alt="">'
         . '</div>';
}
?>

<style>
/* CUSTOM OVERLAY EDITOR */
#editor-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.9); z-index: 10000; display: none;
    flex-direction: column; align-items: center; justify-content: center;
}
#editor-header { width: 100%; padding: 15px 30px; background: #222; color: #fff; display: flex; justify-content: space-between; }
#editor-body { flex: 1; width: 90%; max-height: 70vh; margin: 20px 0; overflow: hidden; background: #111; }
#editor-footer { width: 100%; padding: 20px; background: #222; display: flex; justify-content: space-between; align-items: center; }
#editor-img-container { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
#edit-image { display: block; max-width: 100%; }
.logo-card-fixed { border: 1px solid #ddd; background: #fff; padding: 10px; display: flex; align-items: center; justify-content: center; height: 150px; overflow: hidden; }
.logo-card-fixed img { max-width: 80% !important; max-height: 80% !important; object-fit: contain; }
</style>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">About Us</h1>
    <a href="../about-us.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<form method="post" id="aboutForm">
    <input type="hidden" name="save_about" value="1">


            <div class="card mb-3">
      <div class="card-body">
        <h5>Breadcrumb</h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Breadcrumb Title</label>
                    <input type="text" class="form-control" name="about_breadcrumb_title" value="<?= e($pc['about_breadcrumb_title']??'Who We Are') ?>" placeholder="e.g. Who We Are">
                    <small class="text-muted">The heading displayed on the breadcrumb banner</small>
                </div>
                <?php // The breadcrumb image moved to the central Breadcrumb Images
                      // screen, which every other page already used. Keeping a second
                      // editor for the same image here would mean one of the two
                      // silently had no effect. See spec item B2. ?>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Background Image</label>
                    <div class="alert alert-light border mb-0 py-2 px-3 small">
                        Managed centrally under
                        <a href="index.php?page=breadcrumbs_edit.php">Breadcrumb Images</a>
                        &rarr; <strong>About Us (Who Are We)</strong>, alongside every other page banner.
                    </div>
                </div>
            </div>
        
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Who We Are</h5>
<div class="mb-3"><label class="form-label fw-bold">Section heading</label><input type="text" class="form-control" name="about_heading_main" value="<?= e($pc['about_heading_main']??'') ?>"></div><label class="form-label fw-bold">Body</label><textarea class="form-control" name="about_intro" rows="5"><?= e($pc['about_intro']??'') ?></textarea>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Vision</h5>
<div class="mb-3"><label class="form-label fw-bold">Section heading (covers Vision &amp; Mission)</label><input type="text" class="form-control" name="about_heading_visionmission" value="<?= e($pc['about_heading_visionmission']??'') ?>"></div><div class="mb-3"><label class="form-label fw-bold">"Vision" sub-heading</label><input type="text" class="form-control" name="about_heading_vision" value="<?= e($pc['about_heading_vision']??'') ?>"></div><label class="form-label fw-bold">Body</label><textarea class="form-control" name="about_vision" rows="4"><?= e($pc['about_vision']??'') ?></textarea>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Mission</h5>
<div class="mb-3"><label class="form-label fw-bold">"Mission" sub-heading</label><input type="text" class="form-control" name="about_heading_mission" value="<?= e($pc['about_heading_mission']??'') ?>"></div><label class="form-label fw-bold">Body</label><textarea class="form-control" name="about_mission" rows="4"><?= e($pc['about_mission']??'') ?></textarea>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Core Values</h5>

            <div class="mb-3"><label class="form-label fw-bold">Section heading</label><input type="text" class="form-control" name="about_heading_values" value="<?= e($pc['about_heading_values']??'') ?>"></div>
            <div class="row g-3">
                <?php foreach(['about_val_transparency'=>'Transparency','about_val_people'=>'People','about_val_responsiveness'=>'Responsiveness','about_val_innovation'=>'Innovation','about_val_professionalism'=>'Professionalism'] as $k=>$l): ?>
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100">
                        <label class="form-label small mb-1">Value name <span class="text-muted">(was <?= $l ?>)</span></label>
                        <input type="text" class="form-control form-control-sm mb-2" name="<?= $k ?>_title" value="<?= e($pc[$k.'_title']??'') ?>">
                        <label class="form-label small mb-1">Description</label>
                        <textarea class="form-control form-control-sm" name="<?= $k ?>" rows="2"><?= e($pc[$k]??'') ?></textarea>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Brief History</h5>
<div class="mb-3"><label class="form-label fw-bold">Section heading</label><input type="text" class="form-control" name="about_heading_history" value="<?= e($pc['about_heading_history']??'') ?>"></div><label class="form-label fw-bold">Body (separate paragraphs with a blank line)</label><textarea class="form-control" name="about_history" rows="10"><?= e($pc['about_history']??'') ?></textarea>
      </div>
    </div>
    <div class="card mb-3">
      <div class="card-body">
        <h5>Images</h5>

        <?php /* Only one image actually appears on the About Us page: the picture at
                 the centre of the Core Values wheel. The four fields that used to sit
                 here — Intro Banner, Team Banner, Vision, Mission — were left over
                 from an earlier design; about-us.php referenced none of them, so an
                 editor could upload to all four and see no change on the site.
                 They are removed rather than left to mislead. The stored values and
                 the uploaded files are untouched, so restoring them is a template
                 change if that design ever comes back. */ ?>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card p-3 border shadow-sm">
                    <label class="mb-2 fw-bold">Core Values picture <small class="text-muted">(square, 800&times;800)</small></label>
                    <?= img_preview_tag('about_values_image', $pc['about_values_image'] ?? '') ?>
                    <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_values_image', 800, 800)">
                    <input type="hidden" name="about_values_image_cropped" id="about_values_image_cropped">
                    <div class="form-text">Sits in the middle of the Core Values wheel on the public page.</div>
                </div>
            </div>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <h5>Affiliations &amp; Accreditation</h5>

            <div class="mb-3">
                <label class="form-label fw-bold">Affiliations heading</label>
                <input type="text" class="form-control" name="about_affiliations_title" value="<?= e($pc['about_affiliations_title']??'') ?>">
            </div>
            <p class="text-muted small">Ten slots. Leave a logo empty to hide that slot &mdash; the strip shortens automatically.</p>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 10; $i++):
                    $kl = "about_affiliation_{$i}_logo";
                    $ka = "about_affiliation_{$i}_alt";
                    $ku = "about_affiliation_{$i}_url";
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card p-3 border shadow-sm h-100">
                        <label class="mb-2 fw-bold small">Affiliation <?= $i ?></label>
                        <?= img_preview_tag($kl, $pc[$kl] ?? '') ?>
                        <input type="file" class="form-control form-control-sm mb-2" accept="image/*" onchange="initCropper(this, '<?= $kl ?>', 400, 240)">
                        <input type="hidden" name="<?= $kl ?>_cropped" id="<?= $kl ?>_cropped">
                        <label class="form-label small mb-1">Name (also the image's alt text)</label>
                        <input type="text" class="form-control form-control-sm mb-2" name="<?= $ka ?>" value="<?= e($pc[$ka]??'') ?>" placeholder="e.g. ISO">
                        <label class="form-label small mb-1">Website</label>
                        <input type="text" class="form-control form-control-sm" name="<?= $ku ?>" value="<?= e($pc[$ku]??'') ?>" placeholder="https://...">
                    </div>
                </div>
                <?php endfor; ?>
            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label class="form-label fw-bold">Accreditation heading</label>
                <input type="text" class="form-control" name="about_accreditation_title" value="<?= e($pc['about_accreditation_title']??'') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Accreditation caption</label>
                <input type="text" class="form-control" name="about_accreditation_body" value="<?= e($pc['about_accreditation_body']??'') ?>">
            </div>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 4; $i++):
                    $kl = "about_accreditation_{$i}_logo";
                    $ka = "about_accreditation_{$i}_alt";
                    $ku = "about_accreditation_{$i}_url";
                ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card p-3 border shadow-sm h-100">
                        <label class="mb-2 fw-bold small">Accrediting body <?= $i ?></label>
                        <?= img_preview_tag($kl, $pc[$kl] ?? '') ?>
                        <input type="file" class="form-control form-control-sm mb-2" accept="image/*" onchange="initCropper(this, '<?= $kl ?>', 400, 240)">
                        <input type="hidden" name="<?= $kl ?>_cropped" id="<?= $kl ?>_cropped">
                        <label class="form-label small mb-1">Name</label>
                        <input type="text" class="form-control form-control-sm mb-2" name="<?= $ka ?>" value="<?= e($pc[$ka]??'') ?>" placeholder="e.g. SADCAS">
                        <label class="form-label small mb-1">Website</label>
                        <input type="text" class="form-control form-control-sm" name="<?= $ku ?>" value="<?= e($pc[$ku]??'') ?>" placeholder="https://...">
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        
      </div>
    </div>


    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" class="btn btn-primary px-5 shadow-sm"><i class="fas fa-save me-2"></i>Save All Changes</button>
    </div>
</form>

<!-- Editor overlay is appended to <body> via JS to avoid layout containment issues -->

<script>
(function() {
    function initEditor() {
        if (typeof Cropper === 'undefined') {
            console.error('Cropper.js not available — image editor disabled');
            // Fallback: file inputs still work but without cropper overlay
            window.initCropper = function(input, key, w, h) {
                if (!input.files || !input.files[0]) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('prev_' + key).src = e.target.result;
                    document.getElementById(key + '_cropped').value = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            };
            return;
        }

    // Build overlay and append directly to <body> so no parent CSS can clip it
    var overlayHTML =
        '<div id="editor-overlay">' +
            '<div id="editor-header">' +
                '<h4 class="m-0">Image Selection</h4>' +
                '<button type="button" class="btn btn-sm btn-light" id="editor-close-btn">✕ Close</button>' +
            '</div>' +
            '<div id="editor-body">' +
                '<div id="editor-img-container">' +
                    '<img id="edit-image">' +
                '</div>' +
            '</div>' +
            '<div id="editor-footer">' +
                '<div class="btn-group">' +
                    '<button type="button" class="btn btn-outline-light" id="editor-rotate-left"><i class="fas fa-undo"></i></button>' +
                    '<button type="button" class="btn btn-outline-light" id="editor-rotate-right"><i class="fas fa-redo"></i></button>' +
                '</div>' +
                '<button type="button" class="btn btn-primary px-5 fw-bold" id="editor-apply-btn">Apply Selection</button>' +
            '</div>' +
        '</div>';

    var wrapper = document.createElement('div');
    wrapper.innerHTML = overlayHTML;
    document.body.appendChild(wrapper.firstChild);

    var cropper = null;
    var currentKey = '';
    var currentW = 1200;
    var currentH = 560;
    var overlay = document.getElementById('editor-overlay');
    var editImage = document.getElementById('edit-image');

    // Button handlers
    document.getElementById('editor-close-btn').addEventListener('click', closeEditor);
    document.getElementById('editor-apply-btn').addEventListener('click', applyAndClose);
    document.getElementById('editor-rotate-left').addEventListener('click', function() { if (cropper) cropper.rotate(-90); });
    document.getElementById('editor-rotate-right').addEventListener('click', function() { if (cropper) cropper.rotate(90); });

    window.initCropper = function(input, key, w, h) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0]; // grab reference before clearing
        currentKey = key;
        currentW = w;
        currentH = h;

        var reader = new FileReader();
        reader.onload = function(e) {
            if (cropper) { cropper.destroy(); cropper = null; }

            editImage.onload = function() {
                overlay.style.display = 'flex';
                cropper = new Cropper(editImage, {
                    aspectRatio: w / h,
                    viewMode: 1,
                    dragMode: 'crop',
                    autoCropArea: 1,
                    responsive: true,
                    background: true,
                    ready: function() {
                        this.cropper.crop();
                    }
                });
            };
            editImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
        input.value = ''; // clear after file reference is saved
    };

    function applyAndClose() {
        if (!cropper) return;
        var canvas = cropper.getCroppedCanvas({ width: currentW, height: currentH });
        if (!canvas) return;
        var base64 = canvas.toDataURL('image/jpeg', 0.9);

        document.getElementById(currentKey + '_cropped').value = base64;
        document.getElementById('prev_' + currentKey).src = base64;

        closeEditor();
    }

    function closeEditor() {
        overlay.style.display = 'none';
        if (cropper) { cropper.destroy(); cropper = null; }
    }
    } // end initEditor

    // Try to init immediately, or retry after loading from fallback CDN
    if (typeof Cropper !== 'undefined') {
        initEditor();
    } else {
        var fallback = document.createElement('script');
        fallback.src = 'https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js';
        fallback.onload = function() { initEditor(); };
        fallback.onerror = function() {
            console.error('Cropper.js fallback CDN also failed');
            initEditor(); // will use basic fallback
        };
        document.head.appendChild(fallback);
    }
})();
</script>
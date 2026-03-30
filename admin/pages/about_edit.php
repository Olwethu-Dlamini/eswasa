<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

$upload_dir = ADMIN_ROOT . '/uploads/';

// Ensure upload directory exists and is writable
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {

    $image_updates = [];
    $image_errors  = [];
    $image_keys = ['about_img_vision', 'about_img_mission', 'about_img_team', 'about_img_banner', 'about_breadcrumb_bg'];

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

    $text_fields = [
        'about_intro'               => trim($_POST['about_intro']               ?? ''),
        'about_vision'              => trim($_POST['about_vision']              ?? ''),
        'about_mission'             => trim($_POST['about_mission']             ?? ''),
        'about_history'             => trim($_POST['about_history']             ?? ''),
        'about_val_transparency'    => trim($_POST['about_val_transparency']    ?? ''),
        'about_val_people'          => trim($_POST['about_val_people']          ?? ''),
        'about_val_responsiveness'  => trim($_POST['about_val_responsiveness']  ?? ''),
        'about_val_innovation'      => trim($_POST['about_val_innovation']      ?? ''),
        'about_val_professionalism' => trim($_POST['about_val_professionalism'] ?? ''),
        'about_breadcrumb_title'    => trim($_POST['about_breadcrumb_title']    ?? ''),
    ];

    $all = array_merge($text_fields, $image_updates);

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

    // Redirect back to this page (preserve query string)
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// ── Load Values ──────────────────────────────────────────────
$keys = [
    'about_intro','about_vision','about_mission','about_history',
    'about_val_transparency','about_val_people','about_val_responsiveness',
    'about_val_innovation','about_val_professionalism',
    'about_img_vision','about_img_mission','about_img_team', 'about_img_banner',
    'about_breadcrumb_title','about_breadcrumb_bg'
];

$placeholders = implode(',', array_fill(0, count($keys), '?'));
$stmt = $conn->prepare("SELECT page_key, content FROM page_content WHERE page_key IN ($placeholders)");
$stmt->bind_param(str_repeat('s', count($keys)), ...$keys);
$stmt->execute();
$r = $stmt->get_result();
$pc = [];
while ($row = $r->fetch_assoc()) $pc[$row['page_key']] = $row['content'];
$stmt->close();

function e($v) { return htmlspecialchars($v ?? ''); }
function img_preview_src($path) { return '../' . ltrim($path, '/'); }
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
    <a href="../about-us.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<form method="post" id="aboutForm">
    <input type="hidden" name="save_about" value="1">

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-breadcrumb" type="button">Breadcrumb</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-intro" type="button">Who We Are</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vision" type="button">Vision</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mission" type="button">Mission</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-values" type="button">Core Values</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-history" type="button">History</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-images" type="button">Images</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-breadcrumb">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Breadcrumb Title</label>
                    <input type="text" class="form-control" name="about_breadcrumb_title" value="<?= e($pc['about_breadcrumb_title']??'Who We Are') ?>" placeholder="e.g. Who We Are">
                    <small class="text-muted">The heading displayed on the breadcrumb banner</small>
                </div>
                <div class="col-md-6">
                    <div class="card p-3 border shadow-sm">
                        <label class="mb-2 fw-bold">Background Image <small class="text-muted">(1920x400)</small></label>
                        <div class="logo-card-fixed mb-2"><img id="prev_about_breadcrumb_bg" src="<?= img_preview_src($pc['about_breadcrumb_bg']??'assets/img/bg.png') ?>"></div>
                        <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_breadcrumb_bg', 1920, 400)">
                        <input type="hidden" name="about_breadcrumb_bg_cropped" id="about_breadcrumb_bg_cropped">
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-intro"><textarea class="form-control" name="about_intro" rows="5"><?= e($pc['about_intro']??'') ?></textarea></div>
        <div class="tab-pane fade" id="tab-vision"><textarea class="form-control" name="about_vision" rows="4"><?= e($pc['about_vision']??'') ?></textarea></div>
        <div class="tab-pane fade" id="tab-mission"><textarea class="form-control" name="about_mission" rows="4"><?= e($pc['about_mission']??'') ?></textarea></div>
        <div class="tab-pane fade" id="tab-values">
            <div class="row g-3">
                <?php foreach(['about_val_transparency'=>'Transparency','about_val_people'=>'People','about_val_responsiveness'=>'Responsiveness','about_val_innovation'=>'Innovation','about_val_professionalism'=>'Professionalism'] as $k=>$l): ?>
                <div class="col-md-6"><label class="form-label"><?= $l ?></label><textarea class="form-control" name="<?= $k ?>" rows="2"><?= e($pc[$k]??'') ?></textarea></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-history"><textarea class="form-control" name="about_history" rows="10"><?= e($pc['about_history']??'') ?></textarea></div>
        <div class="tab-pane fade" id="tab-images">
            <div class="row g-4">
                <?php foreach(['about_img_banner'=>'Intro Banner','about_img_team'=>'Team Banner','about_img_vision'=>'Vision','about_img_mission'=>'Mission'] as $k=>$l):
                    $isBanner = (strpos($k, 'banner') !== false || $k === 'about_img_team');
                    $w = 1200; $h = $isBanner ? 350 : 560;
                ?>
                <div class="col-md-6">
                    <div class="card p-3 border shadow-sm">
                        <label class="mb-2 fw-bold"><?= $l ?> <small class="text-muted">(<?= $w ?>×<?= $h ?>)</small></label>
                        <div class="logo-card-fixed mb-2"><img id="prev_<?= $k ?>" src="<?= img_preview_src($pc[$k]??'') ?>"></div>
                        <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, '<?= $k ?>', <?= $w ?>, <?= $h ?>)">
                        <input type="hidden" name="<?= $k ?>_cropped" id="<?= $k ?>_cropped">
                    </div>
                </div>
                <?php endforeach; ?>
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
<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

$upload_dir = ADMIN_ROOT . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ── Page definitions: slug => [label, frontend file, default image] ──
$pages = [
    'services'            => ['Our Services',           'services.php',             'assets/img/bg.png'],
    'board'               => ['Board',                  'board.php',                'assets/img/bg.png'],
    'calibration'         => ['Calibration',            'Calibration.php',          'assets/img/bg/calibrationbg.png'],
    'certification'       => ['Certification',          'Certification.php',        'assets/img/bg/breadcrumb_bg.jpg'],
    'contact'             => ['Contact Us',             'contact.php',              'assets/img/bg/breadcrumb_bg.jpg'],
    'contactcalibration'  => ['Contact Calibration',    'contactcalibration.php',   'assets/img/bg/breadcrumb_bg.jpg'],
    'disclaimer'          => ['Disclaimer',             'disclaimer.php',           'assets/img/bg.png'],
    'events'              => ['Events',                 'events.php',               'assets/img/bg/breadcrumb_bg.jpg'],
    'faq'                 => ['FAQ',                    'faq.php',                  'assets/img/bg/breadcrumb_bg.jpg'],
    'ingelo'              => ['Ingelo',                 'ingelo.php',               'assets/img/bg/Ingelo.png'],
    'managementsystems'   => ['Management Systems',     'managementsystems.php',    'assets/img/bg/breadcrumb_bg.jpg'],
    'meetourteam'         => ['Meet Our Team',          'Meetourteam.php',          'assets/img/bg/bg.png'],
    'news'                => ['News',                   'news.php',                 'assets/img/bg/breadcrumb_bg.jpg'],
    'privacy'             => ['Privacy Policy',         'privacy.php',              'assets/img/bg.png'],
    'product'             => ['Product Certification',  'product.php',              'assets/img/bg/breadcrumb_bg.jpg'],
    'publications'        => ['Publications',           'publications.php',         'assets/img/bg/breadcrumb_bg.jpg'],
    'purchase'            => ['Purchase Standards',      'purchase.php',            'assets/img/bg/breadcrumb_bg.jpg'],
    'qoute'               => ['Request Quote',          'qoute.php',               'assets/img/bg/breadcrumb_bg.jpg'],
    'qoute_certification' => ['Certification Quote',    'qoute_certification.php', 'assets/img/bg/breadcrumb_bg.jpg'],
    'qoute_training'      => ['Training Quote',         'qoute_training.php',      'assets/img/bg/breadcrumb_bg.jpg'],
    'standards'           => ['Standards',              'Standards.php',            'assets/img/bg/breadcrumb_bg.jpg'],
    'tcp'                 => ['Technical Committee',    'tcp.php',                  'assets/img/bg/breadcrumb_bg.jpg'],
    'terms'               => ['Terms & Conditions',     'terms.php',               'assets/img/bg.png'],
    'training_about'      => ['Training Academy',       'training-about.php',      'assets/img/bg/breadcrumb_bg.jpg'],
    'training_calendar'   => ['Training Calendar',      'training-calendar.php',   'assets/img/bg/breadcrumb_bg.jpg'],
    'vacancies'           => ['Vacancies',              'vacancies.php',           'assets/img/bg/breadcrumb_bg.jpg'],
    'work'                => ['Work Programmes',        'work.php',                'assets/img/bg/breadcrumb_bg.jpg'],
    'announcements'       => ['Announcements',          'announcements.php',       'assets/img/bg/breadcrumb_bg.jpg'],
];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_breadcrumbs'])) {
    $uploads_writable = is_dir($upload_dir) && is_writable($upload_dir);
    $image_updates = [];
    $image_errors  = [];

    foreach ($pages as $slug => $info) {
        $field = 'breadcrumb_bg_' . $slug . '_cropped';
        $base64 = $_POST[$field] ?? '';
        if (!empty($base64) && strpos($base64, 'data:image') === 0) {
            if (!$uploads_writable) {
                $image_errors[] = $info[0] . ' (uploads folder not writable)';
                continue;
            }

            list($type, $data) = explode(';', $base64);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);

            if ($data === false) {
                $image_errors[] = $info[0] . ' (invalid image data)';
                continue;
            }

            $ext = 'jpg';
            if (strpos($type, 'image/png') !== false) $ext = 'png';
            if (strpos($type, 'image/webp') !== false) $ext = 'webp';

            $new_name = uniqid('breadcrumb_', true) . '.' . $ext;
            $dest = $upload_dir . $new_name;

            if (file_put_contents($dest, $data)) {
                $image_updates['breadcrumb_bg_' . $slug] = 'admin/uploads/' . $new_name;
            } else {
                $image_errors[] = $info[0] . ' (file write failed)';
            }
        }
    }

    if (!empty($image_updates)) {
        $upsert = $conn->prepare("INSERT INTO page_content (page_key, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = VALUES(content)");

        foreach ($image_updates as $key => $value) {
            $upsert->bind_param('ss', $key, $value);
            $upsert->execute();
        }
        $upsert->close();
    }

    if (!empty($image_errors)) {
        set_flash('danger', 'Some images failed: ' . implode(', ', $image_errors));
    } elseif (empty($image_updates)) {
        set_flash('info', 'No changes were made.');
    } else {
        set_flash('success', count($image_updates) . ' breadcrumb image(s) updated successfully.');
    }

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// ── Load current breadcrumb values ──────────────────────────
$db_keys = [];
foreach ($pages as $slug => $info) {
    $db_keys[] = 'breadcrumb_bg_' . $slug;
}
$placeholders = implode(',', array_fill(0, count($db_keys), '?'));
$stmt = $conn->prepare("SELECT page_key, content FROM page_content WHERE page_key IN ($placeholders)");
$stmt->bind_param(str_repeat('s', count($db_keys)), ...$db_keys);
$stmt->execute();
$r = $stmt->get_result();
$current = [];
while ($row = $r->fetch_assoc()) $current[$row['page_key']] = $row['content'];
$stmt->close();

function bc_preview($slug, $current, $default) {
    $key = 'breadcrumb_bg_' . $slug;
    if (!empty($current[$key])) {
        return '../' . ltrim($current[$key], '/');
    }
    return '../' . ltrim($default, '/');
}
?>

<style>
#editor-overlay {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.9); z-index: 10000; display: none;
    flex-direction: column; align-items: center; justify-content: center;
}
#editor-header { width: 100%; padding: 15px 30px; background: #222; color: #fff; display: flex; justify-content: space-between; align-items: center; }
#editor-body { flex: 1; width: 90%; max-height: 70vh; margin: 20px 0; overflow: hidden; background: #111; }
#editor-footer { width: 100%; padding: 20px; background: #222; display: flex; justify-content: space-between; align-items: center; }
#editor-img-container { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
#edit-image { display: block; max-width: 100%; }
.bc-card { border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden; transition: box-shadow 0.2s; }
.bc-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.bc-preview { width: 100%; height: 120px; background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; }
.bc-preview::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(3,18,39,0.4); }
.bc-preview .bc-label { position: absolute; bottom: 10px; left: 12px; color: #fff; font-weight: 600; font-size: 14px; z-index: 1; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
.bc-actions { padding: 12px; background: #f8f9fa; }
.bc-actions .btn { font-size: 13px; }
.bc-dim { font-size: 11px; color: #6c757d; }
</style>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Breadcrumb Images</h1>
        <p class="text-muted mb-0">Manage the banner background image for every page. All images are cropped to <strong>1920 x 400</strong> pixels.</p>
    </div>
</div>

<form method="post" id="breadcrumbsForm">
    <input type="hidden" name="save_breadcrumbs" value="1">

    <div class="row g-3">
        <?php foreach ($pages as $slug => $info):
            $label   = $info[0];
            $file    = $info[1];
            $default = $info[2];
            $key     = 'breadcrumb_bg_' . $slug;
            $preview = bc_preview($slug, $current, $default);
            $has_custom = !empty($current[$key]);
        ?>
        <div class="col-md-4 col-lg-3">
            <div class="bc-card">
                <div class="bc-preview" id="prev_box_<?= $slug ?>" style="background-image: url('<?= htmlspecialchars($preview) ?>');">
                    <span class="bc-label"><?= htmlspecialchars($label) ?></span>
                </div>
                <div class="bc-actions">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="bc-dim">1920 x 400</span>
                        <?php if ($has_custom): ?>
                            <span class="badge bg-success" style="font-size:10px;">Custom</span>
                        <?php else: ?>
                            <span class="badge bg-secondary" style="font-size:10px;">Default</span>
                        <?php endif; ?>
                    </div>
                    <label class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-camera me-1"></i> Change Image
                        <input type="file" class="d-none" accept="image/*"
                               onchange="initCropper(this, '<?= $key ?>', 1920, 400, '<?= htmlspecialchars($label) ?>')">
                    </label>
                    <input type="hidden" name="<?= $key ?>_cropped" id="<?= $key ?>_cropped">
                    <a href="../<?= htmlspecialchars($file) ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100 mt-1">
                        <i class="fas fa-external-link-alt me-1"></i> View Page
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save All Changes
        </button>
    </div>
</form>

<script>
(function() {
    function initEditor() {
        if (typeof Cropper === 'undefined') {
            console.error('Cropper.js not available');
            window.initCropper = function(input, key, w, h, label) {
                if (!input.files || !input.files[0]) return;
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(key + '_cropped').value = e.target.result;
                    var box = document.getElementById('prev_box_' + key.replace('breadcrumb_bg_', ''));
                    if (box) box.style.backgroundImage = "url('" + e.target.result + "')";
                };
                reader.readAsDataURL(input.files[0]);
            };
            return;
        }

        // Build overlay
        var overlayHTML =
            '<div id="editor-overlay">' +
                '<div id="editor-header">' +
                    '<h4 class="m-0" id="editor-title">Crop Breadcrumb Image</h4>' +
                    '<button type="button" class="btn btn-sm btn-light" id="editor-close-btn">\u2715 Close</button>' +
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
                    '<span class="text-light">1920 x 400</span>' +
                    '<button type="button" class="btn btn-primary px-5 fw-bold" id="editor-apply-btn">Apply Selection</button>' +
                '</div>' +
            '</div>';

        var wrapper = document.createElement('div');
        wrapper.innerHTML = overlayHTML;
        document.body.appendChild(wrapper.firstChild);

        var cropper = null;
        var currentKey = '';
        var currentW = 1920;
        var currentH = 400;
        var overlay = document.getElementById('editor-overlay');
        var editImage = document.getElementById('edit-image');

        document.getElementById('editor-close-btn').addEventListener('click', closeEditor);
        document.getElementById('editor-apply-btn').addEventListener('click', applyAndClose);
        document.getElementById('editor-rotate-left').addEventListener('click', function() { if (cropper) cropper.rotate(-90); });
        document.getElementById('editor-rotate-right').addEventListener('click', function() { if (cropper) cropper.rotate(90); });

        window.initCropper = function(input, key, w, h, label) {
            if (!input.files || !input.files[0]) return;
            var file = input.files[0];
            currentKey = key;
            currentW = w;
            currentH = h;

            document.getElementById('editor-title').textContent = 'Crop: ' + (label || 'Breadcrumb Image');

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
                        ready: function() { this.cropper.crop(); }
                    });
                };
                editImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
            input.value = '';
        };

        function applyAndClose() {
            if (!cropper) return;
            var canvas = cropper.getCroppedCanvas({ width: currentW, height: currentH });
            if (!canvas) return;
            var base64 = canvas.toDataURL('image/jpeg', 0.9);

            document.getElementById(currentKey + '_cropped').value = base64;

            // Update preview card
            var slug = currentKey.replace('breadcrumb_bg_', '');
            var box = document.getElementById('prev_box_' + slug);
            if (box) box.style.backgroundImage = "url('" + base64 + "')";

            // Update badge to show pending
            var badge = box ? box.closest('.bc-card').querySelector('.badge') : null;
            if (badge) {
                badge.className = 'badge bg-warning';
                badge.textContent = 'Pending';
                badge.style.fontSize = '10px';
            }

            closeEditor();
        }

        function closeEditor() {
            overlay.style.display = 'none';
            if (cropper) { cropper.destroy(); cropper = null; }
        }
    }

    if (typeof Cropper !== 'undefined') {
        initEditor();
    } else {
        var fallback = document.createElement('script');
        fallback.src = 'https://unpkg.com/cropperjs@1.5.13/dist/cropper.min.js';
        fallback.onload = function() { initEditor(); };
        fallback.onerror = function() {
            console.error('Cropper.js fallback CDN also failed');
            initEditor();
        };
        document.head.appendChild(fallback);
    }
})();
</script>

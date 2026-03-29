<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

$upload_dir = ADMIN_ROOT . '/uploads/';

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {

    $image_updates = [];
    $image_keys = ['about_img_vision', 'about_img_mission', 'about_img_team', 'about_img_banner'];

    foreach ($image_keys as $key) {
        $base64 = $_POST[$key . '_cropped'] ?? '';
        if (!empty($base64) && strpos($base64, 'data:image') === 0) {
            // Decode base64
            list($type, $data) = explode(';', $base64);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            $ext = 'jpg';
            if (strpos($type, 'image/png') !== false) $ext = 'png';
            if (strpos($type, 'image/webp') !== false) $ext = 'webp';

            $new_name = uniqid('about_', true) . '.' . $ext;
            $dest     = $upload_dir . $new_name;

            if (file_put_contents($dest, $data)) {
                $image_updates[$key] = 'admin/uploads/' . $new_name;
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
    ];

    $all = array_merge($text_fields, $image_updates);

    $stmt = $conn->prepare(
        "INSERT INTO page_content (page_key, content)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE content = VALUES(content), updated_at = CURRENT_TIMESTAMP"
    );
    $save_errors = [];
    foreach ($all as $key => $value) {
        $stmt->bind_param('ss', $key, $value);
        if (!$stmt->execute()) $save_errors[] = $key;
    }
    $stmt->close();

    if (empty($save_errors)) {
        set_flash('success', 'About Us page updated successfully.');
    } else {
        set_flash('danger', 'Some fields failed to save.');
    }
    redirect_self();
}

// ── Load current values ───────────────────────────────────────
$keys = [
    'about_intro','about_vision','about_mission','about_history',
    'about_val_transparency','about_val_people','about_val_responsiveness',
    'about_val_innovation','about_val_professionalism',
    'about_img_vision','about_img_mission','about_img_team', 'about_img_banner'
];
$placeholders = implode(',', array_fill(0, count($keys), '?'));
$res = $conn->prepare("SELECT page_key, content FROM page_content WHERE page_key IN ($placeholders)");
$res->bind_param(str_repeat('s', count($keys)), ...$keys);
$res->execute();
$r = $res->get_result();
$pc = [];
while ($row = $r->fetch_assoc()) $pc[$row['page_key']] = $row['content'];
$res->close();

$defaults = [
    'about_intro'               => 'The Eswatini Standards Authority (ESWASA) is the national body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.',
    'about_vision'              => 'A competitive and Sustainable Trade Environment informed by effective standardization and conformity assurance in Eswatini.',
    'about_mission'             => 'We provide and promote internationally recognized quality standards and conformity assessment services to improve business performance, minimize health and safety risks and ensure environmental integrity in collaboration with regulators.',
    'about_history'             => "The Eswatini Standards Authority (ESWASA) is a parastatal organisation within the Ministry of Commerce, Industry, and Trade established by the Eswatini government under the Standards and Quality Act (10) of 2003, amended in 2023.\n\nESWASA is mandated by this Act to promote quality and standards in local businesses, government, and industry.",
    'about_val_transparency'    => 'We conduct our business with honesty, openness, and integrity in all standardization processes.',
    'about_val_people'          => 'We prioritize people—building trust, collaboration, and mutually beneficial relationships with stakeholders.',
    'about_val_responsiveness'  => 'We act promptly and effectively to meet the evolving needs of our customers, markets, and partners.',
    'about_val_innovation'      => 'We embrace creative thinking and continuous improvement to enhance our standards and services.',
    'about_val_professionalism' => 'We uphold the highest standards of competence, reliability, and accountability in all our operations.',
    'about_img_vision'          => 'assets/img/maguga.jpg',
    'about_img_mission'         => 'assets/img/vision.jpg',
    'about_img_team'            => 'assets/img/blog_thumb10.jpg',
    'about_img_banner'          => 'assets/img/blog_thumb11.jpg',
];
foreach ($defaults as $k => $v) {
    if (empty($pc[$k])) $pc[$k] = $v;
}

function e($v) { return htmlspecialchars($v ?? ''); }

function img_preview_src($path) {
    return '../' . ltrim($path, '/');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">About Us</h1>
    <a href="../about-us.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View Page
    </a>
</div>

<form method="post" id="aboutForm">
    <input type="hidden" name="save_about" value="1">

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-intro"   type="button">Who We Are</button></li>
        <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-vision"  type="button">Vision</button></li>
        <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-mission" type="button">Mission</button></li>
        <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-values"  type="button">Core Values</button></li>
        <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-history" type="button">History</button></li>
        <li class="nav-item"><button class="nav-link"        data-bs-toggle="tab" data-bs-target="#tab-images"  type="button">Images</button></li>
    </ul>

    <div class="tab-content">

        <!-- WHO WE ARE -->
        <div class="tab-pane fade show active" id="tab-intro">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Introduction</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Lead paragraph shown on the About Us page.</label>
                    <textarea class="form-control" name="about_intro" rows="5"><?= e($pc['about_intro']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- VISION -->
        <div class="tab-pane fade" id="tab-vision">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Vision Statement</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Shown in the Vision card. Change the photo in the Images tab.</label>
                    <textarea class="form-control" name="about_vision" rows="4"><?= e($pc['about_vision']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- MISSION -->
        <div class="tab-pane fade" id="tab-mission">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Mission Statement</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Shown in the Mission card. Change the photo in the Images tab.</label>
                    <textarea class="form-control" name="about_mission" rows="4"><?= e($pc['about_mission']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- CORE VALUES -->
        <div class="tab-pane fade" id="tab-values">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Core Values</div>
                <div class="card-body">
                    <p class="text-muted small mb-4">Edit the description for each value. Titles and icons are fixed in the front-end.</p>
                    <div class="row g-3">
                        <?php
                        $vals = [
                            ['key'=>'about_val_transparency',    'label'=>'Transparency'],
                            ['key'=>'about_val_people',          'label'=>'People-Centricity'],
                            ['key'=>'about_val_responsiveness',  'label'=>'Responsiveness'],
                            ['key'=>'about_val_innovation',      'label'=>'Innovation'],
                            ['key'=>'about_val_professionalism', 'label'=>'Professionalism'],
                        ];
                        foreach ($vals as $v): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $v['label'] ?></label>
                            <textarea class="form-control" name="<?= $v['key'] ?>" rows="3"><?= e($pc[$v['key']]) ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- HISTORY -->
        <div class="tab-pane fade" id="tab-history">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">Brief History</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Separate paragraphs with a blank line.</label>
                    <textarea class="form-control" name="about_history" rows="12"><?= e($pc['about_history']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- IMAGES -->
        <div class="tab-pane fade" id="tab-images">
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <i class="fas fa-magic me-2"></i>
                Select an image to open the <strong>Full-Screen Editor</strong>.
            </div>

            <div class="row g-4">
                <!-- Introductory Banner -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            Introductory Banner <small class="text-muted">1200 × 350</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 ratio ratio-21x9 bg-light rounded overflow-hidden border">
                                <img id="prev_about_img_banner" src="<?= img_preview_src($pc['about_img_banner']) ?>" style="object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_img_banner', 1200/350)">
                            <input type="hidden" name="about_img_banner_cropped" id="about_img_banner_cropped">
                        </div>
                    </div>
                </div>

                <!-- History/Team Banner -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            History/Team Banner <small class="text-muted">1200 × 350</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 ratio ratio-21x9 bg-light rounded overflow-hidden border">
                                <img id="prev_about_img_team" src="<?= img_preview_src($pc['about_img_team']) ?>" style="object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_img_team', 1200/350)">
                            <input type="hidden" name="about_img_team_cropped" id="about_img_team_cropped">
                        </div>
                    </div>
                </div>

                <!-- Vision -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            Vision Image <small class="text-muted">1200 × 560</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 ratio ratio-16x9 bg-light rounded overflow-hidden border">
                                <img id="prev_about_img_vision" src="<?= img_preview_src($pc['about_img_vision']) ?>" style="object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_img_vision', 1200/560)">
                            <input type="hidden" name="about_img_vision_cropped" id="about_img_vision_cropped">
                        </div>
                    </div>
                </div>

                <!-- Mission -->
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            Mission Image <small class="text-muted">1200 × 560</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 ratio ratio-16x9 bg-light rounded overflow-hidden border">
                                <img id="prev_about_img_mission" src="<?= img_preview_src($pc['about_img_mission']) ?>" style="object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" accept="image/*" onchange="initCropper(this, 'about_img_mission', 1200/560)">
                            <input type="hidden" name="about_img_mission_cropped" id="about_img_mission_cropped">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save bar -->
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="index.php?page=about_edit.php" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
    </div>
</form>

<!-- Cropper Modal -->
<div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title"><i class="fas fa-crop-alt me-2"></i> Image Editor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="height: 75vh; background: #1a1a1a;">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <img id="cropperImage" style="display: block; max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer bg-light border-0 d-flex justify-content-between p-3">
                <div class="btn-group shadow-sm">
                    <button type="button" class="btn btn-white border" onclick="cropper.rotate(-90)" title="Rotate Left"><i class="fas fa-undo"></i></button>
                    <button type="button" class="btn btn-white border" onclick="cropper.rotate(90)" title="Rotate Right"><i class="fas fa-redo"></i></button>
                    <button type="button" class="btn btn-white border" onclick="cropper.zoom(0.1)" title="Zoom In"><i class="fas fa-search-plus"></i></button>
                    <button type="button" class="btn btn-white border" onclick="cropper.zoom(-0.1)" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
                    <button type="button" class="btn btn-white border" onclick="cropper.reset()" title="Reset"><i class="fas fa-sync-alt"></i></button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Discard</button>
                    <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" onclick="cropAndSave()">
                        <i class="fas fa-check me-2"></i>Apply Crop
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cropper;
let currentKey;
let modal;
let cropImg;

// Wait for both DOM and Bootstrap/Cropper to be ready
window.addEventListener('load', function() {
    const modalEl = document.getElementById('cropperModal');
    if (modalEl && typeof bootstrap !== 'undefined') {
        modal = new bootstrap.Modal(modalEl);
        cropImg = document.getElementById('cropperImage');
    }
});

function initCropper(input, key, ratio) {
    if (!input.files || !input.files[0]) return;
    currentKey = key;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        cropImg.src = e.target.result;
        
        if (modal) {
            modal.show();
            
            // Re-init cropper once modal is shown
            if (cropper) cropper.destroy();
            setTimeout(() => {
                cropper = new Cropper(cropImg, {
                    aspectRatio: ratio,
                    viewMode: 0, 
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    background: false,
                });
            }, 300);
        }
    };
    reader.readAsDataURL(input.files[0]);
    input.value = '';
}

function cropAndSave() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({
        width: 1200,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });
    
    const base64 = canvas.toDataURL('image/jpeg', 0.9);
    const hiddenInput = document.getElementById(currentKey + '_cropped');
    const previewImg = document.getElementById('prev_' + currentKey);
    
    if (hiddenInput) hiddenInput.value = base64;
    if (previewImg) previewImg.src = base64;
    
    modal.hide();
}

// Warn on unsaved changes
(function(){
    let dirty = false;
    document.addEventListener('input', (e) => {
        if (e.target.closest('#aboutForm')) dirty = true;
    });
    window.addEventListener('beforeunload', e => {
        if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
    document.getElementById('aboutForm')?.addEventListener('submit', () => { dirty = false; });
})();
</script>
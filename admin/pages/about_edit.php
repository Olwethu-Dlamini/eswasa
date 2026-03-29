<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

$upload_dir = ADMIN_ROOT . '/uploads/';

// ── Image upload helper ───────────────────────────────────────
function handle_image_upload($file_key, $upload_dir) {
    if (empty($_FILES[$file_key]['name'])) return null;

    $file = $_FILES[$file_key];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $map = [
            UPLOAD_ERR_INI_SIZE  => 'File exceeds server limit.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit.',
            UPLOAD_ERR_PARTIAL   => 'File only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR=> 'Missing temp folder.',
            UPLOAD_ERR_CANT_WRITE=> 'Failed to write to disk.',
        ];
        return ['error' => $map[$file['error']] ?? 'Upload error.'];
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        return ['error' => 'File "' . htmlspecialchars($file['name']) . '" exceeds the 3MB limit.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
        return ['error' => 'Invalid file type. Allowed: JPG, PNG, WEBP.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) {
        return ['error' => 'Invalid image content detected.'];
    }

    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);

    $new_name = uniqid('about_', true) . '.' . $ext;
    $dest     = $upload_dir . $new_name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['error' => 'Failed to save file to disk.'];
    }

    return ['path' => 'admin/uploads/' . $new_name];
}

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_about'])) {

    $upload_errors = [];

    $image_keys = ['about_img_vision', 'about_img_mission', 'about_img_team'];
    $image_updates = [];
    foreach ($image_keys as $key) {
        if (!empty($_FILES[$key]['name'])) {
            $result = handle_image_upload($key, $upload_dir);
            if (isset($result['error'])) {
                $upload_errors[] = $result['error'];
            } elseif (isset($result['path'])) {
                $image_updates[$key] = $result['path'];
            }
        }
    }

    if (!empty($upload_errors)) {
        set_flash('danger', implode('<br>', $upload_errors));
        redirect_self();
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
    'about_img_vision','about_img_mission','about_img_team',
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
];
foreach ($defaults as $k => $v) {
    if (empty($pc[$k])) $pc[$k] = $v;
}

function e($v) { return htmlspecialchars($v ?? ''); }

function img_preview_src($path) {
    // Paths stored as 'admin/uploads/...' or 'assets/img/...' — prepend ../ for admin context
    return '../' . ltrim($path, '/');
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">About Us</h1>
    <a href="../about-us.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View Page
    </a>
</div>

<form method="post" id="aboutForm" enctype="multipart/form-data">
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
            <div class="card">
                <div class="card-header">Introduction</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Lead paragraph shown on the About Us page.</label>
                    <textarea class="form-control" name="about_intro" rows="5"><?= e($pc['about_intro']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- VISION -->
        <div class="tab-pane fade" id="tab-vision">
            <div class="card">
                <div class="card-header">Vision Statement</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Shown in the Vision card. Change the photo in the Images tab.</label>
                    <textarea class="form-control" name="about_vision" rows="4"><?= e($pc['about_vision']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- MISSION -->
        <div class="tab-pane fade" id="tab-mission">
            <div class="card">
                <div class="card-header">Mission Statement</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Shown in the Mission card. Change the photo in the Images tab.</label>
                    <textarea class="form-control" name="about_mission" rows="4"><?= e($pc['about_mission']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- CORE VALUES -->
        <div class="tab-pane fade" id="tab-values">
            <div class="card">
                <div class="card-header">Core Values</div>
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
            <div class="card">
                <div class="card-header">Brief History</div>
                <div class="card-body">
                    <label class="form-label text-muted small">Separate paragraphs with a blank line.</label>
                    <textarea class="form-control" name="about_history" rows="12"><?= e($pc['about_history']) ?></textarea>
                </div>
            </div>
        </div>

        <!-- IMAGES -->
        <div class="tab-pane fade" id="tab-images">

            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Max <strong>3MB</strong> per image &nbsp;·&nbsp; Formats: JPG, PNG, WEBP &nbsp;·&nbsp;
                Leave a field empty to keep the current image.
            </div>

            <div class="row g-4">

                <!-- Vision -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Vision Image <small class="text-muted ms-2">1200 × 560 px recommended</small></div>
                        <div class="card-body">
                            <div class="mb-3" style="height:160px;overflow:hidden;border:1px solid var(--bs-border-color);border-radius:4px;background:#f8f9fa;">
                                <img id="prev_vision" src="<?= img_preview_src($pc['about_img_vision']) ?>"
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" name="about_img_vision"
                                   accept=".jpg,.jpeg,.png,.webp"
                                   onchange="previewImg(this,'prev_vision')">
                            <div class="form-text">Max 3MB · JPG, PNG, WEBP</div>
                        </div>
                    </div>
                </div>

                <!-- Mission -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">Mission Image <small class="text-muted ms-2">1200 × 560 px recommended</small></div>
                        <div class="card-body">
                            <div class="mb-3" style="height:160px;overflow:hidden;border:1px solid var(--bs-border-color);border-radius:4px;background:#f8f9fa;">
                                <img id="prev_mission" src="<?= img_preview_src($pc['about_img_mission']) ?>"
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" name="about_img_mission"
                                   accept=".jpg,.jpeg,.png,.webp"
                                   onchange="previewImg(this,'prev_mission')">
                            <div class="form-text">Max 3MB · JPG, PNG, WEBP</div>
                        </div>
                    </div>
                </div>

                <!-- Team Banner -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">Team Banner <small class="text-muted ms-2">1200 × 350 px recommended</small></div>
                        <div class="card-body">
                            <div class="mb-3" style="height:175px;overflow:hidden;border:1px solid var(--bs-border-color);border-radius:4px;background:#f8f9fa;">
                                <img id="prev_team" src="<?= img_preview_src($pc['about_img_team']) ?>"
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <input type="file" class="form-control" name="about_img_team"
                                   accept=".jpg,.jpeg,.png,.webp"
                                   onchange="previewImg(this,'prev_team')">
                            <div class="form-text">Max 3MB · JPG, PNG, WEBP</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- /.tab-content -->

    <!-- Save bar -->
    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="index.php?page=about_edit.php" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary px-4">
            <i class="fas fa-save me-1"></i> Save Changes
        </button>
    </div>

</form>

<script>
function previewImg(input, previewId) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 3 * 1024 * 1024) {
        alert('File is ' + (file.size/1024/1024).toFixed(1) + 'MB — max allowed is 3MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => document.getElementById(previewId).src = e.target.result;
    reader.readAsDataURL(file);
}

// Warn on unsaved changes
(function(){
    let dirty = false;
    document.querySelectorAll('#aboutForm textarea, #aboutForm input').forEach(el => {
        el.addEventListener('change', ()=>{ dirty=true; });
        el.addEventListener('input',  ()=>{ dirty=true; });
    });
    window.addEventListener('beforeunload', e => {
        if (dirty) { e.preventDefault(); e.returnValue=''; }
    });
    document.getElementById('aboutForm').addEventListener('submit', ()=>{ dirty=false; });
})();
</script>
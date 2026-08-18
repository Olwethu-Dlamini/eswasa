<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Field lists ───────────────────────────────────────────────
$text_keys = [
    // breadcrumb
    'train_about_breadcrumb_title',
    // hero / intro
    'train_about_hero_title','train_about_hero_subtitle','train_about_intro_body',
    // training formats
    'train_about_format_1_tag','train_about_format_1_duration','train_about_format_1_audience',
    'train_about_format_2_tag','train_about_format_2_duration','train_about_format_2_audience',
    'train_about_format_note',
    // courses text (image keys handled separately)
    'train_about_course_1_name','train_about_course_1_alt','train_about_course_1_modal_title','train_about_course_1_overview','train_about_course_1_benefits','train_about_course_1_courses','train_about_course_1_duration',
    'train_about_course_2_name','train_about_course_2_alt','train_about_course_2_modal_title','train_about_course_2_overview','train_about_course_2_benefits','train_about_course_2_courses','train_about_course_2_duration',
    'train_about_course_3_name','train_about_course_3_alt','train_about_course_3_modal_title','train_about_course_3_overview','train_about_course_3_benefits','train_about_course_3_courses','train_about_course_3_duration',
    'train_about_course_4_name','train_about_course_4_alt','train_about_course_4_modal_title','train_about_course_4_overview','train_about_course_4_benefits','train_about_course_4_courses','train_about_course_4_duration',
    'train_about_course_5_name','train_about_course_5_alt','train_about_course_5_modal_title','train_about_course_5_overview','train_about_course_5_benefits','train_about_course_5_courses','train_about_course_5_duration',
    'train_about_course_6_name','train_about_course_6_alt','train_about_course_6_modal_title','train_about_course_6_overview','train_about_course_6_benefits','train_about_course_6_courses','train_about_course_6_duration',
    'train_about_course_7_name','train_about_course_7_alt','train_about_course_7_modal_title','train_about_course_7_overview','train_about_course_7_benefits','train_about_course_7_courses','train_about_course_7_duration',
    // Why train
    'train_about_why_title','train_about_why_subtitle',
    'train_about_why_1_title','train_about_why_1_body',
    'train_about_why_2_title','train_about_why_2_body',
    'train_about_why_3_title','train_about_why_3_body',
    'train_about_why_4_title','train_about_why_4_body',
    'train_about_why_5_title','train_about_why_5_body',
    'train_about_why_6_title','train_about_why_6_body',
    // Policies
    'train_about_policies_title','train_about_policies_subtitle',
    'train_about_policy_application_tab','train_about_policy_application_title','train_about_policy_application_body',
    'train_about_policy_acceptance_tab','train_about_policy_acceptance_title','train_about_policy_acceptance_body',
    'train_about_policy_cancellations_tab','train_about_policy_cancellations_title','train_about_policy_cancellations_body',
    'train_about_policy_fees_tab','train_about_policy_fees_title','train_about_policy_fees_body',
    'train_about_bank_title','train_about_bank_name','train_about_bank_account_name','train_about_bank_account_number','train_about_bank_branch_code','train_about_bank_branch_name','train_about_bank_swift','train_about_bank_note',
    'train_about_policy_travel_tab','train_about_policy_travel_title','train_about_policy_travel_body',
    'train_about_policy_inhouse_tab','train_about_policy_inhouse_title','train_about_policy_inhouse_body',
    'train_about_policy_assessments_tab','train_about_policy_assessments_title',
    'train_about_assess_eval_title','train_about_assess_eval_list',
    'train_about_assess_cert_title','train_about_assess_cert_list',
    'train_about_assess_pass_mark',
];

$image_keys = [
    'train_about_course_1_image',
    'train_about_course_2_image',
    'train_about_course_3_image',
    'train_about_course_4_image',
    'train_about_course_5_image',
    'train_about_course_6_image',
    'train_about_course_7_image',
];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_train_about'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        // Prefer the cropper's base64 payload; fall back to a raw file
        // upload (e.g. SVG logos the cropper passes through untouched).
        $path = pc_save_base64_image($_POST[$k . '_cropped'] ?? '', ADMIN_ROOT . '/uploads/', 'train_about');
        if (!is_string($path)) {
            $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'train_about');
        }
        if (is_string($path)) {
            $kv[$k] = $path;
        }
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Training Academy page saved.');
    redirect_self();
}

// ── Load current values ───────────────────────────────────────
$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));

// Course catalogue metadata (for friendlier admin UI)
$courses = [
    1 => 'Course 1 — Quality Management System',
    2 => 'Course 2 — Health and Safety',
    3 => 'Course 3 — Environmental',
    4 => 'Course 4 — Good Agricultural Practices',
    5 => 'Course 5 — Wellness',
    6 => 'Course 6 — Food Safety',
    7 => 'Course 7 — Auditing',
];
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Training Academy (About)</h1>
    <a href="../training-about.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_train_about" value="1">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Breadcrumb</h5>
            <label class="form-label">Breadcrumb Title</label>
            <input type="text" name="train_about_breadcrumb_title" class="form-control"
                   value="<?= pc_h($pc['train_about_breadcrumb_title']) ?>">
            <small class="text-muted">For the background image, use the <em>Breadcrumb Images</em> page (slug: <code>training_about</code>).</small>
        </div>
    </div>

    <!-- Hero / Intro -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Programmes Intro Section</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="train_about_hero_title" class="form-control"
                           value="<?= pc_h($pc['train_about_hero_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Subtitle</label>
                    <input type="text" name="train_about_hero_subtitle" class="form-control"
                           value="<?= pc_h($pc['train_about_hero_subtitle']) ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Intro Paragraphs</label>
                    <textarea name="train_about_intro_body" class="form-control" rows="6"><?= pc_h($pc['train_about_intro_body']) ?></textarea>
                    <small class="text-muted">Separate paragraphs with a blank line.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Training formats -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Training Formats (two-card row)</h5>
            <div class="row g-3">
                <?php for ($n = 1; $n <= 2; $n++): ?>
                <div class="col-md-6">
                    <h6 class="mb-2">Format <?= $n ?></h6>
                    <label class="form-label">Tag (e.g. "Awareness Training")</label>
                    <input type="text" name="train_about_format_<?= $n ?>_tag" class="form-control mb-2"
                           value="<?= pc_h($pc['train_about_format_' . $n . '_tag']) ?>">
                    <label class="form-label">Duration</label>
                    <input type="text" name="train_about_format_<?= $n ?>_duration" class="form-control mb-2"
                           value="<?= pc_h($pc['train_about_format_' . $n . '_duration']) ?>">
                    <label class="form-label">Audience</label>
                    <textarea name="train_about_format_<?= $n ?>_audience" class="form-control" rows="3"><?= pc_h($pc['train_about_format_' . $n . '_audience']) ?></textarea>
                </div>
                <?php endfor; ?>
                <div class="col-12">
                    <label class="form-label">Footnote under the two format cards</label>
                    <input type="text" name="train_about_format_note" class="form-control"
                           value="<?= pc_h($pc['train_about_format_note']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Course Catalogue -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Course Catalogue (7 fixed slots)</h5>
            <p class="text-muted mb-3">Edit name, image and modal content for each of the 7 catalogue cards. Slot order is fixed.</p>
            <div class="accordion" id="trainAboutCoursesAccordion">
                <?php foreach ($courses as $i => $label):
                    $img_key  = 'train_about_course_' . $i . '_image';
                    $img_src  = pc_image_src($pc[$img_key]);
                    $coll_id  = 'courseCollapse' . $i;
                ?>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="courseHeading<?= $i ?>">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#<?= $coll_id ?>" aria-expanded="false" aria-controls="<?= $coll_id ?>">
                            <?= pc_h($label) ?>
                        </button>
                    </h2>
                    <div id="<?= $coll_id ?>" class="accordion-collapse collapse"
                         aria-labelledby="courseHeading<?= $i ?>" data-bs-parent="#trainAboutCoursesAccordion">
                        <div class="accordion-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Card Name (shown on catalogue)</label>
                                    <input type="text" name="train_about_course_<?= $i ?>_name" class="form-control"
                                           value="<?= pc_h($pc['train_about_course_' . $i . '_name']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Modal Title</label>
                                    <input type="text" name="train_about_course_<?= $i ?>_modal_title" class="form-control"
                                           value="<?= pc_h($pc['train_about_course_' . $i . '_modal_title']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Image Alt Text</label>
                                    <input type="text" name="train_about_course_<?= $i ?>_alt" class="form-control"
                                           value="<?= pc_h($pc['train_about_course_' . $i . '_alt']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Card Image</label>
                                    <div class="mb-2">
                                        <img data-crop-preview="train_about_course_<?= $i ?>_image_preview"
                                             src="<?= !empty($pc[$img_key]) ? '../' . pc_h($img_src) : '' ?>"
                                             alt="" style="max-height:120px;border:1px solid #ddd;background:#fff;padding:6px;<?= empty($pc[$img_key]) ? 'display:none;' : '' ?>"
                                             onload="this.style.display='inline-block'">
                                    </div>
                                    <input type="file" name="train_about_course_<?= $i ?>_image_file" accept="image/*" class="form-control crop-input"
                                           data-crop-label="Course <?= $i ?> Image">
                                    <input type="hidden" name="train_about_course_<?= $i ?>_image_cropped">
                                    <small class="text-muted">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Course Overview (modal paragraph)</label>
                                    <textarea name="train_about_course_<?= $i ?>_overview" class="form-control" rows="3"><?= pc_h($pc['train_about_course_' . $i . '_overview']) ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Key Benefits</label>
                                    <textarea name="train_about_course_<?= $i ?>_benefits" class="form-control" rows="6"><?= pc_h($pc['train_about_course_' . $i . '_benefits']) ?></textarea>
                                    <small class="text-muted">One bullet per line.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Available Courses</label>
                                    <textarea name="train_about_course_<?= $i ?>_courses" class="form-control" rows="6"><?= pc_h($pc['train_about_course_' . $i . '_courses']) ?></textarea>
                                    <small class="text-muted">One bullet per line.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Duration &amp; Format (modal paragraph)</label>
                                    <textarea name="train_about_course_<?= $i ?>_duration" class="form-control" rows="2"><?= pc_h($pc['train_about_course_' . $i . '_duration']) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Why Train With ESWASA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Why Train With ESWASA? (6-card grid)</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="train_about_why_title" class="form-control"
                           value="<?= pc_h($pc['train_about_why_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Section Subtitle</label>
                    <input type="text" name="train_about_why_subtitle" class="form-control"
                           value="<?= pc_h($pc['train_about_why_subtitle']) ?>">
                </div>
                <?php for ($n = 1; $n <= 6; $n++): ?>
                <div class="col-md-6">
                    <fieldset class="border rounded p-3 h-100">
                        <legend class="float-none w-auto px-2 fs-6">Reason <?= $n ?></legend>
                        <label class="form-label">Title</label>
                        <input type="text" name="train_about_why_<?= $n ?>_title" class="form-control mb-2"
                               value="<?= pc_h($pc['train_about_why_' . $n . '_title']) ?>">
                        <label class="form-label">Body</label>
                        <textarea name="train_about_why_<?= $n ?>_body" class="form-control" rows="4"><?= pc_h($pc['train_about_why_' . $n . '_body']) ?></textarea>
                    </fieldset>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Policies — tabs -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Training Academy — General Information (policy tabs)</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Section Title</label>
                    <input type="text" name="train_about_policies_title" class="form-control"
                           value="<?= pc_h($pc['train_about_policies_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Section Subtitle</label>
                    <input type="text" name="train_about_policies_subtitle" class="form-control"
                           value="<?= pc_h($pc['train_about_policies_subtitle']) ?>">
                </div>
            </div>

            <?php
            $simple_policies = [
                'application'   => 'Application',
                'acceptance'    => 'Acceptance',
                'cancellations' => 'Cancellations',
                'travel'        => 'Travel and Accommodation',
                'inhouse'       => 'Training Venues',
            ];
            foreach ($simple_policies as $slug => $label):
            ?>
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2 fs-6"><?= pc_h($label) ?> tab</legend>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tab Label</label>
                        <input type="text" name="train_about_policy_<?= $slug ?>_tab" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_' . $slug . '_tab']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Card Heading</label>
                        <input type="text" name="train_about_policy_<?= $slug ?>_title" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_' . $slug . '_title']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea name="train_about_policy_<?= $slug ?>_body" class="form-control" rows="4"><?= pc_h($pc['train_about_policy_' . $slug . '_body']) ?></textarea>
                    </div>
                </div>
            </fieldset>
            <?php endforeach; ?>

            <!-- Course Fees tab — has bank details -->
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2 fs-6">Course Fees tab</legend>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tab Label</label>
                        <input type="text" name="train_about_policy_fees_tab" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_fees_tab']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Card Heading</label>
                        <input type="text" name="train_about_policy_fees_title" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_fees_title']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Fees Body</label>
                        <textarea name="train_about_policy_fees_body" class="form-control" rows="4"><?= pc_h($pc['train_about_policy_fees_body']) ?></textarea>
                    </div>
                    <div class="col-12">
                        <h6 class="mt-2">Banking Details</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Banking Details Heading</label>
                        <input type="text" name="train_about_bank_title" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_title']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="train_about_bank_name" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="train_about_bank_account_name" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_account_name']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="train_about_bank_account_number" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_account_number']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Branch Code</label>
                        <input type="text" name="train_about_bank_branch_code" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_branch_code']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Branch Name</label>
                        <input type="text" name="train_about_bank_branch_name" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_branch_name']) ?>">
                    </div>
                    <?php // The page shows a SWIFT code but the editor had no field for it,
                          // so it could only be changed in code. See spec item B6. ?>
                    <div class="col-md-6">
                        <label class="form-label">SWIFT Code</label>
                        <input type="text" name="train_about_bank_swift" class="form-control"
                               value="<?= pc_h($pc['train_about_bank_swift']) ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Footnote (under banking table)</label>
                        <textarea name="train_about_bank_note" class="form-control" rows="2"><?= pc_h($pc['train_about_bank_note']) ?></textarea>
                    </div>
                </div>
            </fieldset>

            <!-- Assessments tab -->
            <fieldset class="border rounded p-3 mb-3">
                <legend class="float-none w-auto px-2 fs-6">Assessments tab</legend>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tab Label</label>
                        <input type="text" name="train_about_policy_assessments_tab" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_assessments_tab']) ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Card Heading</label>
                        <input type="text" name="train_about_policy_assessments_title" class="form-control"
                               value="<?= pc_h($pc['train_about_policy_assessments_title']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Evaluation Card Title</label>
                        <input type="text" name="train_about_assess_eval_title" class="form-control"
                               value="<?= pc_h($pc['train_about_assess_eval_title']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Certificates Card Title</label>
                        <input type="text" name="train_about_assess_cert_title" class="form-control"
                               value="<?= pc_h($pc['train_about_assess_cert_title']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Evaluation Bullets</label>
                        <textarea name="train_about_assess_eval_list" class="form-control" rows="5"><?= pc_h($pc['train_about_assess_eval_list']) ?></textarea>
                        <small class="text-muted">One bullet per line.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Certificate Bullets</label>
                        <textarea name="train_about_assess_cert_list" class="form-control" rows="5"><?= pc_h($pc['train_about_assess_cert_list']) ?></textarea>
                        <small class="text-muted">One bullet per line.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Passing Mark Pill Text</label>
                        <input type="text" name="train_about_assess_pass_mark" class="form-control"
                               value="<?= pc_h($pc['train_about_assess_pass_mark']) ?>">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end mb-5">
        <button type="submit" name="save_train_about" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

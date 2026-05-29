<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';

$text_keys = [
    // Breadcrumb / meta
    'std_breadcrumb_title',
    'std_meta_description',

    // Section 1 — About / mandate
    'std_about_title',
    'standards_mandate',
    'std_sectors_title',
    'std_sectors_intro',
    'std_sector_1', 'std_sector_2', 'std_sector_3', 'std_sector_4',
    'std_sector_5', 'std_sector_6', 'std_sector_7', 'std_sector_8',
    'std_catalogue_note',

    // What is a Standard
    'std_what_title',
    'std_what_body',

    // Benefits
    'std_benefits_title',
    'std_benefit_1', 'std_benefit_2', 'std_benefit_3', 'std_benefit_4', 'std_benefit_5',

    // Process
    'std_process_title',
    'standards_process_desc',
    'std_process_step_0_title', 'std_process_step_0_body',
    'std_process_step_1_title', 'std_process_step_1_body',
    'std_process_step_2_title', 'std_process_step_2_body',
    'std_process_step_3_title', 'std_process_step_3_body',
    'std_process_step_4_title', 'std_process_step_4_body',
    'std_process_step_5_title', 'std_process_step_5_body', 'std_process_step_5_pill',
    'std_process_step_6_title', 'std_process_step_6_body',
    'std_process_step_7_title', 'std_process_step_7_body', 'std_process_step_7_pill',
    'std_process_step_8_title', 'std_process_step_8_body', 'std_process_step_8_pill',

    // Proposal
    'std_proposal_title',
    'standards_proposal',
    'std_proposal_portal_url',
    'std_proposal_email_primary',
    'std_proposal_email_secondary',
    'std_proposal_form_url',
    'std_proposal_form_label',
    'std_proposal_note',

    // Technical Committees
    'std_tc_section_title',
    'std_tc_about_title',
    'std_tc_about_body',
    'std_tc_benefits_title',
    'std_tc_benefit_1_title', 'std_tc_benefit_1_body',
    'std_tc_benefit_2_title', 'std_tc_benefit_2_body',
    'std_tc_benefit_3_title', 'std_tc_benefit_3_body',
    'std_tc_benefit_4_title', 'std_tc_benefit_4_body',
    'std_tc_apply_title',
    'std_tc_apply_body',
    'std_tc_portal_url',
    'std_tc_register_url',
    'std_workprog_title',
    'std_workprog_body',
    'std_workprog_url',

    // Purchase
    'std_purchase_section_title',
    'std_sales_title',
    'std_sales_body',
    'std_estore_url',
    'std_catalogue_url',

    'std_popular_title',
    'std_popular_intro',
    'std_popular_1_code', 'std_popular_1_name',
    'std_popular_2_code', 'std_popular_2_name',
    'std_popular_3_code', 'std_popular_3_name',
    'std_popular_4_code', 'std_popular_4_name',
    'std_popular_5_code', 'std_popular_5_name',
    'std_popular_6_code', 'std_popular_6_name',

    'std_copyright_title',
    'std_copyright_body',

    'std_affiliations_title',
    'std_affiliations_intro',
    'std_aff_1_name', 'std_aff_1_full', 'std_aff_1_url',
    'std_aff_2_name', 'std_aff_2_full', 'std_aff_2_url',
    'std_aff_3_name', 'std_aff_3_full', 'std_aff_3_url',
    'std_aff_4_name', 'std_aff_4_full', 'std_aff_4_url',
    'std_aff_5_name', 'std_aff_5_full', 'std_aff_5_url',

    // Information Centre
    'std_info_section_title',
    'std_info_about_title',
    'std_info_about_intro',
    'std_info_item_1', 'std_info_item_2', 'std_info_item_3', 'std_info_item_4',
    'std_info_about_outro',
    'std_afcfta_title',
    'std_afcfta_body',
    'std_nep_title',
    'std_nep_body',
    'std_nep_image_alt',

    // CTA
    'std_cta_title',
    'std_cta_subtitle',
    'std_cta_btn_1_label', 'std_cta_btn_1_url',
    'std_cta_btn_2_label', 'std_cta_btn_2_url',
    'std_cta_btn_3_label', 'std_cta_btn_3_url',
];

$image_keys = [
    'std_popular_1_image',
    'std_popular_2_image',
    'std_popular_3_image',
    'std_popular_4_image',
    'std_popular_5_image',
    'std_popular_6_image',
    'std_aff_1_image',
    'std_aff_2_image',
    'std_aff_3_image',
    'std_aff_4_image',
    'std_aff_5_image',
    'std_nep_image',
];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_std'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'std');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Standards page saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Standards Page</h1>
    <a href="../Standards.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type'] ?? 'success') ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['message'] ?? '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<style>
    .std-toc {
        position: sticky;
        top: 72px;
        z-index: 30;
        background: var(--bs-body-bg);
        border-bottom: 1px solid var(--bs-border-color);
        padding: 10px 0;
        margin: 0 0 1rem;
        display: flex;
        gap: 8px;
        align-items: center;
        overflow-x: auto;
        scrollbar-width: thin;
    }
    .std-toc::-webkit-scrollbar { height: 6px; }
    .std-toc::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.18); border-radius: 3px; }
    .std-toc a {
        flex-shrink: 0;
        font-size: 13px;
        padding: 6px 12px;
        border: 1px solid var(--bs-border-color);
        border-radius: 999px;
        color: var(--bs-secondary-color);
        text-decoration: none;
        white-space: nowrap;
        transition: background-color .15s, color .15s, border-color .15s;
    }
    .std-toc a:hover {
        color: var(--bs-primary);
        border-color: var(--bs-primary);
        background: rgba(var(--bs-primary-rgb), .06);
    }
    .std-toc .save-pill {
        margin-left: auto;
        font-weight: 600;
    }
    .std-edit-section { scroll-margin-top: 140px; }
    .std-save-bar {
        position: sticky;
        bottom: 0;
        z-index: 25;
        background: var(--bs-body-bg);
        border-top: 1px solid var(--bs-border-color);
        padding: 12px 0;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .std-save-bar .save-hint {
        font-size: 13px;
        color: var(--bs-secondary-color);
    }
</style>

<nav class="std-toc" aria-label="Standards editor sections">
    <a href="#sec-meta">Breadcrumb</a>
    <a href="#sec-about">About Standards</a>
    <a href="#sec-what">What is a Standard</a>
    <a href="#sec-benefits">Benefits</a>
    <a href="#sec-process">9-Stage Process</a>
    <a href="#sec-proposal">Submit Proposal</a>
    <a href="#sec-tc">Technical Committees</a>
    <a href="#sec-purchase">Purchase Standards</a>
    <a href="#sec-affiliations">Affiliations</a>
    <a href="#sec-info">Info Centre</a>
    <a href="#sec-cta">CTA</a>
    <button type="submit" form="standardsEditForm" name="save_std" class="btn btn-sm btn-primary save-pill">
        <i class="fas fa-save me-1"></i> Save
    </button>
</nav>

<form id="standardsEditForm" method="POST" enctype="multipart/form-data">

    <!-- ───────── Breadcrumb / Meta ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-meta">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb &amp; Meta</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Breadcrumb / Page Title</label>
                <input type="text" name="std_breadcrumb_title" class="form-control" value="<?= pc_h($pc['std_breadcrumb_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Meta Description</label>
                <textarea name="std_meta_description" class="form-control" rows="2"><?= pc_h($pc['std_meta_description']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- ───────── Section 1: About / Mandate ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-about">
        <div class="card-body">
            <h5 class="mb-3">Section 1 — About Standards Development</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_about_title" class="form-control" value="<?= pc_h($pc['std_about_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Mandate (intro paragraphs)</label>
                <textarea name="standards_mandate" class="form-control" rows="7"><?= pc_h($pc['standards_mandate']) ?></textarea>
                <div class="form-text">Separate paragraphs with a blank line.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Sectors Heading</label>
                <input type="text" name="std_sectors_title" class="form-control" value="<?= pc_h($pc['std_sectors_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Sectors Intro</label>
                <input type="text" name="std_sectors_intro" class="form-control" value="<?= pc_h($pc['std_sectors_intro']) ?>">
            </div>
            <div class="row g-2">
                <?php for ($i = 1; $i <= 8; $i++): $k = "std_sector_$i"; ?>
                <div class="col-md-6">
                    <label class="form-label">Sector <?= $i ?></label>
                    <input type="text" name="<?= $k ?>" class="form-control" value="<?= pc_h($pc[$k]) ?>">
                </div>
                <?php endfor; ?>
            </div>
            <div class="mt-3">
                <label class="form-label fw-bold">Catalogue note</label>
                <input type="text" name="std_catalogue_note" class="form-control" value="<?= pc_h($pc['std_catalogue_note']) ?>">
            </div>
        </div>
    </div>

    <!-- ───────── What is a Standard ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-what">
        <div class="card-body">
            <h5 class="mb-3">What is a Standard?</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_what_title" class="form-control" value="<?= pc_h($pc['std_what_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Body (paragraphs)</label>
                <textarea name="std_what_body" class="form-control" rows="8"><?= pc_h($pc['std_what_body']) ?></textarea>
                <div class="form-text">Separate paragraphs with a blank line.</div>
            </div>
        </div>
    </div>

    <!-- ───────── Benefits ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-benefits">
        <div class="card-body">
            <h5 class="mb-3">Benefits of Standards</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_benefits_title" class="form-control" value="<?= pc_h($pc['std_benefits_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 5; $i++): $k = "std_benefit_$i"; ?>
            <div class="mb-2">
                <label class="form-label">Benefit <?= $i ?></label>
                <input type="text" name="<?= $k ?>" class="form-control" value="<?= pc_h($pc[$k]) ?>">
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- ───────── 9-Stage Process ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-process">
        <div class="card-body">
            <h5 class="mb-3">Standards Development Process — 9 Stages</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_process_title" class="form-control" value="<?= pc_h($pc['std_process_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Process Intro / Description</label>
                <textarea name="standards_process_desc" class="form-control" rows="2"><?= pc_h($pc['standards_process_desc']) ?></textarea>
            </div>

            <?php
            $stage_specs = [
                0 => ['label' => 'Stage 0 — Preliminary',           'pill' => false],
                1 => ['label' => 'Stage 1 — Proposal',              'pill' => false],
                2 => ['label' => 'Stage 2 — Preparatory',           'pill' => false],
                3 => ['label' => 'Stage 3 — Committee',             'pill' => false],
                4 => ['label' => 'Stage 4 — Enquiry',               'pill' => false],
                5 => ['label' => 'Stage 5 — Disposal of Comments',  'pill' => true],
                6 => ['label' => 'Stage 6 — Approval',              'pill' => false],
                7 => ['label' => 'Stage 7 — Endorsement',           'pill' => true],
                8 => ['label' => 'Stage 8 — Publication',           'pill' => true],
            ];
            foreach ($stage_specs as $n => $spec):
                $tk = "std_process_step_{$n}_title";
                $bk = "std_process_step_{$n}_body";
                $pk = "std_process_step_{$n}_pill";
            ?>
            <fieldset class="border rounded p-3 mb-3">
                <legend class="fs-6 fw-bold px-2" style="width:auto;"><?= htmlspecialchars($spec['label']) ?></legend>
                <div class="mb-2">
                    <label class="form-label">Title</label>
                    <input type="text" name="<?= $tk ?>" class="form-control" value="<?= pc_h($pc[$tk]) ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Body</label>
                    <textarea name="<?= $bk ?>" class="form-control" rows="3"><?= pc_h($pc[$bk]) ?></textarea>
                </div>
                <?php if ($spec['pill']): ?>
                <div class="mb-0">
                    <label class="form-label">Time Pill (e.g. "within 30 days")</label>
                    <input type="text" name="<?= $pk ?>" class="form-control" value="<?= pc_h($pc[$pk]) ?>">
                </div>
                <?php endif; ?>
            </fieldset>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ───────── Proposal ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-proposal">
        <div class="card-body">
            <h5 class="mb-3">Submitting a Standards Proposal</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_proposal_title" class="form-control" value="<?= pc_h($pc['std_proposal_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Lead Text</label>
                <input type="text" name="standards_proposal" class="form-control" value="<?= pc_h($pc['standards_proposal']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Online Portal URL</label>
                <input type="url" name="std_proposal_portal_url" class="form-control" value="<?= pc_h($pc['std_proposal_portal_url']) ?>">
            </div>
            <div class="row g-2">
                <div class="col-md-6">
                    <label class="form-label">Primary Email</label>
                    <input type="email" name="std_proposal_email_primary" class="form-control" value="<?= pc_h($pc['std_proposal_email_primary']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary Email</label>
                    <input type="email" name="std_proposal_email_secondary" class="form-control" value="<?= pc_h($pc['std_proposal_email_secondary']) ?>">
                </div>
            </div>
            <div class="row g-2 mt-1">
                <div class="col-md-8">
                    <label class="form-label">Proposal Form PDF URL</label>
                    <input type="text" name="std_proposal_form_url" class="form-control" value="<?= pc_h($pc['std_proposal_form_url']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Form Button Label</label>
                    <input type="text" name="std_proposal_form_label" class="form-control" value="<?= pc_h($pc['std_proposal_form_label']) ?>">
                </div>
            </div>
            <div class="mt-3">
                <label class="form-label fw-bold">Note (appears under the button)</label>
                <textarea name="std_proposal_note" class="form-control" rows="3"><?= pc_h($pc['std_proposal_note']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- ───────── Technical Committees ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-tc">
        <div class="card-body">
            <h5 class="mb-3">Technical Committees &amp; Work Programmes</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Heading</label>
                <input type="text" name="std_tc_section_title" class="form-control" value="<?= pc_h($pc['std_tc_section_title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">About TCs — Title</label>
                <input type="text" name="std_tc_about_title" class="form-control" value="<?= pc_h($pc['std_tc_about_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">About TCs — Body</label>
                <textarea name="std_tc_about_body" class="form-control" rows="6"><?= pc_h($pc['std_tc_about_body']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Benefits Block Title</label>
                <input type="text" name="std_tc_benefits_title" class="form-control" value="<?= pc_h($pc['std_tc_benefits_title']) ?>">
            </div>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="col-md-6">
                    <fieldset class="border rounded p-2">
                        <legend class="fs-6 fw-bold px-2" style="width:auto;">Benefit <?= $i ?></legend>
                        <input type="text" name="std_tc_benefit_<?= $i ?>_title" class="form-control mb-2" value="<?= pc_h($pc["std_tc_benefit_{$i}_title"]) ?>" placeholder="Title">
                        <textarea name="std_tc_benefit_<?= $i ?>_body" class="form-control" rows="3" placeholder="Body"><?= pc_h($pc["std_tc_benefit_{$i}_body"]) ?></textarea>
                    </fieldset>
                </div>
                <?php endfor; ?>
            </div>

            <div class="mt-3 mb-3">
                <label class="form-label fw-bold">Apply Block — Title</label>
                <input type="text" name="std_tc_apply_title" class="form-control" value="<?= pc_h($pc['std_tc_apply_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Apply Block — Body</label>
                <textarea name="std_tc_apply_body" class="form-control" rows="5"><?= pc_h($pc['std_tc_apply_body']) ?></textarea>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label">TC Portal URL</label>
                    <input type="url" name="std_tc_portal_url" class="form-control" value="<?= pc_h($pc['std_tc_portal_url']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Register Interest URL</label>
                    <input type="text" name="std_tc_register_url" class="form-control" value="<?= pc_h($pc['std_tc_register_url']) ?>">
                </div>
            </div>

            <hr>
            <div class="mb-3">
                <label class="form-label fw-bold">Work Programme — Title</label>
                <input type="text" name="std_workprog_title" class="form-control" value="<?= pc_h($pc['std_workprog_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Work Programme — Body</label>
                <textarea name="std_workprog_body" class="form-control" rows="4"><?= pc_h($pc['std_workprog_body']) ?></textarea>
            </div>
            <div class="mb-0">
                <label class="form-label">Work Programme URL</label>
                <input type="url" name="std_workprog_url" class="form-control" value="<?= pc_h($pc['std_workprog_url']) ?>">
            </div>
        </div>
    </div>

    <!-- ───────── Purchase Standards ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-purchase">
        <div class="card-body">
            <h5 class="mb-3">Purchase Standards</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Heading</label>
                <input type="text" name="std_purchase_section_title" class="form-control" value="<?= pc_h($pc['std_purchase_section_title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Sales Block — Title</label>
                <input type="text" name="std_sales_title" class="form-control" value="<?= pc_h($pc['std_sales_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Sales Block — Body</label>
                <textarea name="std_sales_body" class="form-control" rows="5"><?= pc_h($pc['std_sales_body']) ?></textarea>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label">ESWASA estore URL</label>
                    <input type="url" name="std_estore_url" class="form-control" value="<?= pc_h($pc['std_estore_url']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Standards Catalogue URL</label>
                    <input type="text" name="std_catalogue_url" class="form-control" value="<?= pc_h($pc['std_catalogue_url']) ?>">
                </div>
            </div>

            <hr>
            <div class="mb-3">
                <label class="form-label fw-bold">Most Popular — Block Title</label>
                <input type="text" name="std_popular_title" class="form-control" value="<?= pc_h($pc['std_popular_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Most Popular — Intro</label>
                <input type="text" name="std_popular_intro" class="form-control" value="<?= pc_h($pc['std_popular_intro']) ?>">
            </div>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 6; $i++):
                    $code = "std_popular_{$i}_code";
                    $name = "std_popular_{$i}_name";
                    $img  = "std_popular_{$i}_image";
                ?>
                <div class="col-md-6">
                    <fieldset class="border rounded p-2">
                        <legend class="fs-6 fw-bold px-2" style="width:auto;">Popular Standard <?= $i ?></legend>
                        <label class="form-label">Code</label>
                        <input type="text" name="<?= $code ?>" class="form-control mb-2" value="<?= pc_h($pc[$code]) ?>" placeholder="e.g. SZNS ISO 9001:2015">
                        <label class="form-label">Name</label>
                        <input type="text" name="<?= $name ?>" class="form-control mb-2" value="<?= pc_h($pc[$name]) ?>" placeholder="e.g. Quality Management Systems">
                        <label class="form-label">Image</label>
                        <?php if (!empty($pc[$img])): ?>
                            <div class="mb-1"><img src="../<?= pc_h(pc_image_src($pc[$img])) ?>" style="max-height:80px;border:1px solid #ddd;background:#fff;padding:4px"></div>
                        <?php endif; ?>
                        <input type="file" name="<?= $img ?>_file" accept="image/*" class="form-control">
                        <div class="form-text">Leave empty to keep current image.</div>
                    </fieldset>
                </div>
                <?php endfor; ?>
            </div>

            <hr class="mt-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Copyrights — Title</label>
                <input type="text" name="std_copyright_title" class="form-control" value="<?= pc_h($pc['std_copyright_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label fw-bold">Copyrights — Body</label>
                <textarea name="std_copyright_body" class="form-control" rows="4"><?= pc_h($pc['std_copyright_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- ───────── Affiliations ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-affiliations">
        <div class="card-body">
            <h5 class="mb-3">Our Affiliations</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Title</label>
                <input type="text" name="std_affiliations_title" class="form-control" value="<?= pc_h($pc['std_affiliations_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Intro</label>
                <textarea name="std_affiliations_intro" class="form-control" rows="2"><?= pc_h($pc['std_affiliations_intro']) ?></textarea>
            </div>
            <div class="row g-3">
                <?php for ($i = 1; $i <= 5; $i++):
                    $kn = "std_aff_{$i}_name";
                    $kf = "std_aff_{$i}_full";
                    $kl = "std_aff_{$i}_url";
                    $ki = "std_aff_{$i}_image";
                ?>
                <div class="col-md-6">
                    <fieldset class="border rounded p-2">
                        <legend class="fs-6 fw-bold px-2" style="width:auto;">Affiliation <?= $i ?></legend>
                        <label class="form-label">Short Name</label>
                        <input type="text" name="<?= $kn ?>" class="form-control mb-2" value="<?= pc_h($pc[$kn]) ?>" placeholder="e.g. ISO">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="<?= $kf ?>" class="form-control mb-2" value="<?= pc_h($pc[$kf]) ?>" placeholder="International Organization for Standardization">
                        <label class="form-label">URL</label>
                        <input type="text" name="<?= $kl ?>" class="form-control mb-2" value="<?= pc_h($pc[$kl]) ?>" placeholder="https://...">
                        <label class="form-label">Logo</label>
                        <?php if (!empty($pc[$ki])): ?>
                            <div class="mb-1"><img src="../<?= pc_h(pc_image_src($pc[$ki])) ?>" style="max-height:60px;border:1px solid #ddd;background:#fff;padding:4px"></div>
                        <?php endif; ?>
                        <input type="file" name="<?= $ki ?>_file" accept="image/*" class="form-control">
                        <div class="form-text">Leave empty to keep current image.</div>
                    </fieldset>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- ───────── Information Centre ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-info">
        <div class="card-body">
            <h5 class="mb-3">Information Centre</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">Section Heading</label>
                <input type="text" name="std_info_section_title" class="form-control" value="<?= pc_h($pc['std_info_section_title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">About IC — Title</label>
                <input type="text" name="std_info_about_title" class="form-control" value="<?= pc_h($pc['std_info_about_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">About IC — Intro</label>
                <input type="text" name="std_info_about_intro" class="form-control" value="<?= pc_h($pc['std_info_about_intro']) ?>">
            </div>
            <?php for ($i = 1; $i <= 4; $i++): $k = "std_info_item_$i"; ?>
            <div class="mb-2">
                <label class="form-label">Info Item <?= $i ?></label>
                <input type="text" name="<?= $k ?>" class="form-control" value="<?= pc_h($pc[$k]) ?>">
            </div>
            <?php endfor; ?>
            <div class="mb-3 mt-2">
                <label class="form-label fw-bold">About IC — Outro</label>
                <input type="text" name="std_info_about_outro" class="form-control" value="<?= pc_h($pc['std_info_about_outro']) ?>">
            </div>

            <hr>
            <div class="mb-3">
                <label class="form-label fw-bold">AfCFTA — Title</label>
                <input type="text" name="std_afcfta_title" class="form-control" value="<?= pc_h($pc['std_afcfta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">AfCFTA — Body</label>
                <textarea name="std_afcfta_body" class="form-control" rows="3"><?= pc_h($pc['std_afcfta_body']) ?></textarea>
            </div>

            <hr>
            <div class="mb-3">
                <label class="form-label fw-bold">National Enquiry Point — Title</label>
                <input type="text" name="std_nep_title" class="form-control" value="<?= pc_h($pc['std_nep_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">National Enquiry Point — Body</label>
                <textarea name="std_nep_body" class="form-control" rows="3"><?= pc_h($pc['std_nep_body']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">NEP Image</label>
                <?php if (!empty($pc['std_nep_image'])): ?>
                    <div class="mb-1"><img src="../<?= pc_h(pc_image_src($pc['std_nep_image'])) ?>" style="max-height:120px;border:1px solid #ddd"></div>
                <?php endif; ?>
                <input type="file" name="std_nep_image_file" accept="image/*" class="form-control">
                <div class="form-text">Leave empty to keep current image.</div>
            </div>
            <div class="mb-0">
                <label class="form-label">NEP Image — Alt Text</label>
                <input type="text" name="std_nep_image_alt" class="form-control" value="<?= pc_h($pc['std_nep_image_alt']) ?>">
            </div>
        </div>
    </div>

    <!-- ───────── CTA ───────── -->
    <div class="card mb-3 std-edit-section" id="sec-cta">
        <div class="card-body">
            <h5 class="mb-3">Get Involved — Page CTA</h5>
            <div class="mb-3">
                <label class="form-label fw-bold">CTA Title</label>
                <input type="text" name="std_cta_title" class="form-control" value="<?= pc_h($pc['std_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">CTA Subtitle</label>
                <input type="text" name="std_cta_subtitle" class="form-control" value="<?= pc_h($pc['std_cta_subtitle']) ?>">
            </div>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-5">
                    <label class="form-label">Button <?= $i ?> Label</label>
                    <input type="text" name="std_cta_btn_<?= $i ?>_label" class="form-control" value="<?= pc_h($pc["std_cta_btn_{$i}_label"]) ?>">
                </div>
                <div class="col-md-7">
                    <label class="form-label">Button <?= $i ?> URL</label>
                    <input type="text" name="std_cta_btn_<?= $i ?>_url" class="form-control" value="<?= pc_h($pc["std_cta_btn_{$i}_url"]) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="std-save-bar">
        <span class="save-hint">Changes save the whole page at once.</span>
        <button type="submit" name="save_std" class="btn btn-primary px-5"><i class="fas fa-save me-2"></i>Save Changes</button>
    </div>
</form>

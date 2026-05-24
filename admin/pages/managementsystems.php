<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key registry ──────────────────────────────────────────────
$text_keys = [
    // Breadcrumb
    'ms_breadcrumb_title','ms_crumb_home','ms_crumb_section','ms_crumb_current',

    // Introduction
    'ms_intro_title','ms_intro_body',

    // Schemes
    'ms_schemes_title',
    'ms_scheme_iso9001_alt','ms_scheme_iso9001_code','ms_scheme_iso9001_name',
    'ms_scheme_iso14001_alt','ms_scheme_iso14001_code','ms_scheme_iso14001_name',
    'ms_scheme_iso22000_alt','ms_scheme_iso22000_code','ms_scheme_iso22000_name',
    'ms_scheme_iso45001_alt','ms_scheme_iso45001_code','ms_scheme_iso45001_name',
    'ms_scheme_haccp_alt','ms_scheme_haccp_code','ms_scheme_haccp_name',

    // Accreditation
    'ms_accred_title','ms_accred_body','ms_accred_img_alt',

    // Portfolio
    'ms_portfolio_title','ms_portfolio_footnote',
    'ms_portfolio_1_code','ms_portfolio_1_name',
    'ms_portfolio_2_code','ms_portfolio_2_name',
    'ms_portfolio_3_code','ms_portfolio_3_name',
    'ms_portfolio_4_code','ms_portfolio_4_name',
    'ms_portfolio_5_code','ms_portfolio_5_name',

    // Certified Organisations
    'ms_certified_title','ms_certified_footer',

    // Documents
    'ms_docs_title',
    'ms_doc_1_title','ms_doc_1_url',
    'ms_doc_2_title','ms_doc_2_url',
    'ms_doc_3_title','ms_doc_3_url',
    'ms_doc_4_title','ms_doc_4_url',
    'ms_doc_5_title','ms_doc_5_url',
    'ms_doc_6_title','ms_doc_6_url',
    'ms_doc_7_title','ms_doc_7_url',
    'ms_doc_8_title','ms_doc_8_url',
    'ms_doc_9_title','ms_doc_9_url',
    'ms_doc_10_title','ms_doc_10_url',
    'ms_doc_11_title','ms_doc_11_url',

    // Why Certify
    'ms_why_title','ms_why_subtitle','ms_why_img_alt',

    // Process
    'ms_process_title',
    'ms_step_1_title','ms_step_1_body',
    'ms_step_2_title','ms_step_2_body',
    'ms_step_3_title','ms_step_3_body',
    'ms_step_4_title','ms_step_4_body',
    'ms_step_5_title','ms_step_5_body',
    'ms_step_decision_title','ms_step_decision_body',
    'ms_step_6_title','ms_step_6_body',
    'ms_step_7_title','ms_step_7_body',
    'ms_step_8_title','ms_step_8_body',

    // Benefits
    'ms_benefits_title',
    'ms_benefit_1','ms_benefit_2','ms_benefit_3','ms_benefit_4','ms_benefit_5',
    'ms_benefit_6','ms_benefit_7','ms_benefit_8','ms_benefit_9','ms_benefit_10',

    // CTA
    'ms_cta_title','ms_cta_subtitle',
    'ms_cta_btn1_text','ms_cta_btn1_url',
    'ms_cta_btn2_text','ms_cta_btn2_url',
    'ms_cta_btn3_text','ms_cta_btn3_url',
];

$image_keys = [
    'ms_scheme_iso9001_img',
    'ms_scheme_iso14001_img',
    'ms_scheme_iso22000_img',
    'ms_scheme_iso45001_img',
    'ms_scheme_haccp_img',
    'ms_accred_img',
    'ms_why_img',
];

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_ms'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'ms');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Management Systems Certification</h1>
    <a href="../managementsystems.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title (large heading on banner)</label>
                <input type="text" name="ms_breadcrumb_title" class="form-control" value="<?= pc_h($pc['ms_breadcrumb_title']) ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Crumb: Home</label>
                    <input type="text" name="ms_crumb_home" class="form-control" value="<?= pc_h($pc['ms_crumb_home']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crumb: Section</label>
                    <input type="text" name="ms_crumb_section" class="form-control" value="<?= pc_h($pc['ms_crumb_section']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Crumb: Current page</label>
                    <input type="text" name="ms_crumb_current" class="form-control" value="<?= pc_h($pc['ms_crumb_current']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Introduction -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Introduction</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="ms_intro_title" class="form-control" value="<?= pc_h($pc['ms_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="ms_intro_body" class="form-control" rows="8"><?= pc_h($pc['ms_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Certification Schemes -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Schemes Offered</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_schemes_title" class="form-control" value="<?= pc_h($pc['ms_schemes_title']) ?>">
            </div>

            <?php
            $schemes = [
                'iso9001'  => 'ISO 9001 — Quality',
                'iso14001' => 'ISO 14001 — Environmental',
                'iso22000' => 'ISO 22000 — Food Safety',
                'iso45001' => 'ISO 45001 — Occupational Health & Safety',
                'haccp'    => 'HACCP — SANS 10330',
            ];
            foreach ($schemes as $slug => $label):
                $img_key = 'ms_scheme_'.$slug.'_img';
                $alt_key = 'ms_scheme_'.$slug.'_alt';
                $code_key = 'ms_scheme_'.$slug.'_code';
                $name_key = 'ms_scheme_'.$slug.'_name';
            ?>
            <div class="border rounded p-3 mb-3">
                <h6 class="mb-3"><?= pc_h($label) ?></h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Image</label>
                        <?php if (!empty($pc[$img_key])): ?>
                            <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc[$img_key])) ?>" style="max-height:80px;border:1px solid #ddd"></div>
                        <?php endif; ?>
                        <input type="file" name="<?= $img_key ?>_file" accept="image/*" class="form-control form-control-sm">
                        <small class="text-muted">Leave empty to keep current image.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alt text</label>
                        <input type="text" name="<?= $alt_key ?>" class="form-control" value="<?= pc_h($pc[$alt_key]) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Standard Code</label>
                        <input type="text" name="<?= $code_key ?>" class="form-control" value="<?= pc_h($pc[$code_key]) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Standard Name</label>
                        <input type="text" name="<?= $name_key ?>" class="form-control" value="<?= pc_h($pc[$name_key]) ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Accreditation -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Accreditation Card</h5>
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="ms_accred_title" class="form-control" value="<?= pc_h($pc['ms_accred_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="ms_accred_body" class="form-control" rows="6"><?= pc_h($pc['ms_accred_body']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt text</label>
                        <input type="text" name="ms_accred_img_alt" class="form-control" value="<?= pc_h($pc['ms_accred_img_alt']) ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Accreditation Logo</label>
                    <?php if (!empty($pc['ms_accred_img'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['ms_accred_img'])) ?>" style="max-height:120px;border:1px solid #ddd"></div>
                    <?php endif; ?>
                    <input type="file" name="ms_accred_img_file" accept="image/*" class="form-control">
                    <small class="text-muted">Leave empty to keep current image.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certifications Portfolio Card</h5>
            <div class="mb-3">
                <label class="form-label">Card Title</label>
                <input type="text" name="ms_portfolio_title" class="form-control" value="<?= pc_h($pc['ms_portfolio_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-4">
                    <input type="text" name="ms_portfolio_<?= $i ?>_code" class="form-control" placeholder="e.g. SZNS ISO 9001" value="<?= pc_h($pc['ms_portfolio_'.$i.'_code']) ?>">
                </div>
                <div class="col-md-8">
                    <input type="text" name="ms_portfolio_<?= $i ?>_name" class="form-control" placeholder="e.g. Quality Management Systems" value="<?= pc_h($pc['ms_portfolio_'.$i.'_name']) ?>">
                </div>
            </div>
            <?php endfor; ?>
            <div class="mt-3">
                <label class="form-label">Footnote</label>
                <textarea name="ms_portfolio_footnote" class="form-control" rows="2"><?= pc_h($pc['ms_portfolio_footnote']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Certified Organisations -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certified Organisations</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_certified_title" class="form-control" value="<?= pc_h($pc['ms_certified_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Footer note (link to Certification Status Register is appended automatically)</label>
                <textarea name="ms_certified_footer" class="form-control" rows="2"><?= pc_h($pc['ms_certified_footer']) ?></textarea>
            </div>
            <small class="text-muted">Note: the client logo tiles below the title are sourced from <code>assets/img/clients/</code> by filename slug and are not edited here.</small>
        </div>
    </div>

    <!-- Documents -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Certification Documents</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_docs_title" class="form-control" value="<?= pc_h($pc['ms_docs_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 11; $i++): ?>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-1 text-end"><span class="text-muted">#<?= $i ?></span></div>
                <div class="col-md-6">
                    <input type="text" name="ms_doc_<?= $i ?>_title" class="form-control" placeholder="Document title shown on card" value="<?= pc_h($pc['ms_doc_'.$i.'_title']) ?>">
                </div>
                <div class="col-md-5">
                    <input type="text" name="ms_doc_<?= $i ?>_url" class="form-control" placeholder="File path or URL (e.g. CER_RU_028.pdf)" value="<?= pc_h($pc['ms_doc_'.$i.'_url']) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Why Certify -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Why Certify with ESWASA?</h5>
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="ms_why_title" class="form-control" value="<?= pc_h($pc['ms_why_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle</label>
                        <input type="text" name="ms_why_subtitle" class="form-control" value="<?= pc_h($pc['ms_why_subtitle']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Alt text</label>
                        <textarea name="ms_why_img_alt" class="form-control" rows="2"><?= pc_h($pc['ms_why_img_alt']) ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Why-Certify Illustration</label>
                    <?php if (!empty($pc['ms_why_img'])): ?>
                        <div class="mb-2"><img src="../<?= pc_h(pc_image_src($pc['ms_why_img'])) ?>" style="max-width:100%;max-height:160px;border:1px solid #ddd"></div>
                    <?php endif; ?>
                    <input type="file" name="ms_why_img_file" accept="image/*" class="form-control">
                    <small class="text-muted">Leave empty to keep current image.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Steps -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">How Certification Works (Process)</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_process_title" class="form-control" value="<?= pc_h($pc['ms_process_title']) ?>">
            </div>
            <?php
            $process_steps = [
                '1' => 'Row 1, Step 1',
                '2' => 'Row 1, Step 2',
                '3' => 'Row 1, Step 3',
                '4' => 'Row 2, Step 4',
                '5' => 'Row 2, Step 5',
                'decision' => 'Row 2, Highlight Circle (Certification Decision)',
                '6' => 'Row 3, Step 6',
                '7' => 'Row 3, Step 7',
                '8' => 'Row 3, Step 8',
            ];
            foreach ($process_steps as $idx => $label):
                $title_key = 'ms_step_'.$idx.'_title';
                $body_key  = 'ms_step_'.$idx.'_body';
            ?>
            <div class="row g-2 mb-2">
                <div class="col-md-3 d-flex align-items-center"><small class="text-muted"><?= pc_h($label) ?></small></div>
                <div class="col-md-3">
                    <input type="text" name="<?= $title_key ?>" class="form-control" placeholder="Heading (e.g. Step 1)" value="<?= pc_h($pc[$title_key]) ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="<?= $body_key ?>" class="form-control" placeholder="Description" value="<?= pc_h($pc[$body_key]) ?>">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Benefits -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Benefits of Certification</h5>
            <div class="mb-3">
                <label class="form-label">Section Title</label>
                <input type="text" name="ms_benefits_title" class="form-control" value="<?= pc_h($pc['ms_benefits_title']) ?>">
            </div>
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-md-1 text-end"><span class="text-muted">#<?= $i ?></span></div>
                <div class="col-md-11">
                    <input type="text" name="ms_benefit_<?= $i ?>" class="form-control" value="<?= pc_h($pc['ms_benefit_'.$i]) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- CTA -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Call-to-Action Section</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="ms_cta_title" class="form-control" value="<?= pc_h($pc['ms_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <textarea name="ms_cta_subtitle" class="form-control" rows="2"><?= pc_h($pc['ms_cta_subtitle']) ?></textarea>
            </div>
            <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> Text</label>
                    <input type="text" name="ms_cta_btn<?= $i ?>_text" class="form-control" value="<?= pc_h($pc['ms_cta_btn'.$i.'_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button <?= $i ?> URL</label>
                    <input type="text" name="ms_cta_btn<?= $i ?>_url" class="form-control" value="<?= pc_h($pc['ms_cta_btn'.$i.'_url']) ?>">
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_ms" class="btn btn-primary px-5">Save Changes</button>
    </div>
</form>

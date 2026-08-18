<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Hero / breadcrumb
    'cert_status_breadcrumb_title',
    // Section title block
    'cert_status_section_title',
    'cert_status_section_subtitle',
    // Intro card
    'cert_status_intro',
    // Suspended block
    'cert_status_suspended_title',
    'cert_status_suspended_empty',
    'cert_status_suspended_col_client',
    'cert_status_suspended_col_cert_no',
    'cert_status_suspended_col_scope',
    'cert_status_suspended_col_date',
    'cert_status_suspended_col_reason',
    // Withdrawn block
    'cert_status_withdrawn_title',
    'cert_status_withdrawn_empty',
    'cert_status_withdrawn_col_client',
    'cert_status_withdrawn_col_cert_no',
    'cert_status_withdrawn_col_scope',
    'cert_status_withdrawn_col_date',
    // Reduced block
    'cert_status_reduced_title',
    'cert_status_reduced_empty',
    'cert_status_reduced_col_client',
    'cert_status_reduced_col_cert_no',
    'cert_status_reduced_col_scope',
    'cert_status_reduced_col_date',
    'cert_status_reduced_col_note',
    // Footer — Appeals
    'cert_status_footer_appeals_label',
    'cert_status_footer_appeals_body',
    'cert_status_footer_appeals_link_label',
    'cert_status_footer_appeals_link_url',
    // Footer — Complaints
    'cert_status_footer_complaints_label',
    'cert_status_footer_complaints_body',
    'cert_status_footer_complaints_link_label',
    'cert_status_footer_complaints_link_url',
    // Footer — Information requests
    'cert_status_footer_info_label',
    'cert_status_footer_info_body',
    'cert_status_footer_info_phone_1',
    'cert_status_footer_info_phone_2',
    'cert_status_footer_info_link_label',
    'cert_status_footer_info_link_url',
];

$image_keys = [];

// ── Save handler ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cert_status'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }

    // Document uploads win over whatever is in the matching URL box, so an
    // existing external link keeps working when nothing new is uploaded.
    // Rejections are surfaced rather than dropped. See spec item C2.
    $doc_errors = [];
    foreach (['cert_status_footer_appeals_link_url', 'cert_status_footer_complaints_link_url', 'cert_status_footer_info_link_url'] as $dk) {
        $up = pc_upload_document($dk . '_file', ADMIN_ROOT . '/uploads/', 'doc');
        if (is_string($up) && strpos($up, 'ERR:') === 0) {
            $doc_errors[] = substr($up, 4);
        } elseif (is_string($up)) {
            $kv[$dk] = $up;
        }
    }
    $errs = pc_save_many($conn, $kv);
    if ($doc_errors) {
        set_flash('danger', 'Saved, but a document was not replaced: ' . implode(' ', $doc_errors));
    } else {
        set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    }
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Certification Status Page</h1>
    <a href="../certification-status.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View page
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_cert_status" value="1">

    <!-- Hero / Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Hero / Breadcrumb</h5>
            <div class="mb-3">
                <label class="form-label">Page Title (H1)</label>
                <input type="text" name="cert_status_breadcrumb_title" class="form-control" value="<?= pc_h($pc['cert_status_breadcrumb_title']) ?>">
            </div>
        </div>
    </div>

    <!-- Section Title -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section Title</h5>
            <div class="mb-3">
                <label class="form-label">Title (H2)</label>
                <input type="text" name="cert_status_section_title" class="form-control" value="<?= pc_h($pc['cert_status_section_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Subtitle</label>
                <input type="text" name="cert_status_section_subtitle" class="form-control" value="<?= pc_h($pc['cert_status_section_subtitle']) ?>">
            </div>
        </div>
    </div>

    <!-- Intro / legal basis -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Intro Card (Legal Basis)</h5>
            <div class="mb-3">
                <label class="form-label">Intro Paragraph(s)
                    <small class="text-muted">— separate paragraphs with a blank line</small>
                </label>
                <textarea name="cert_status_intro" class="form-control" rows="6"><?= pc_h($pc['cert_status_intro']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Suspended block -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section: Suspended Certifications</h5>
            <div class="mb-3">
                <label class="form-label">Block Title</label>
                <input type="text" name="cert_status_suspended_title" class="form-control" value="<?= pc_h($pc['cert_status_suspended_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Empty-State Message <small class="text-muted">(shown when there are zero suspended entries)</small></label>
                <input type="text" name="cert_status_suspended_empty" class="form-control" value="<?= pc_h($pc['cert_status_suspended_empty']) ?>">
            </div>
            <hr>
            <h6>Table Column Headings</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Client</label>
                    <input type="text" name="cert_status_suspended_col_client" class="form-control" value="<?= pc_h($pc['cert_status_suspended_col_client']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Certificate No.</label>
                    <input type="text" name="cert_status_suspended_col_cert_no" class="form-control" value="<?= pc_h($pc['cert_status_suspended_col_cert_no']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Standard / Scope</label>
                    <input type="text" name="cert_status_suspended_col_scope" class="form-control" value="<?= pc_h($pc['cert_status_suspended_col_scope']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Date</label>
                    <input type="text" name="cert_status_suspended_col_date" class="form-control" value="<?= pc_h($pc['cert_status_suspended_col_date']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Reason</label>
                    <input type="text" name="cert_status_suspended_col_reason" class="form-control" value="<?= pc_h($pc['cert_status_suspended_col_reason']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawn block -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section: Withdrawn / Cancelled Certifications</h5>
            <div class="mb-3">
                <label class="form-label">Block Title</label>
                <input type="text" name="cert_status_withdrawn_title" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Empty-State Message</label>
                <input type="text" name="cert_status_withdrawn_empty" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_empty']) ?>">
            </div>
            <hr>
            <h6>Table Column Headings</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Client</label>
                    <input type="text" name="cert_status_withdrawn_col_client" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_col_client']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Certificate No.</label>
                    <input type="text" name="cert_status_withdrawn_col_cert_no" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_col_cert_no']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Standard / Scope</label>
                    <input type="text" name="cert_status_withdrawn_col_scope" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_col_scope']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Date</label>
                    <input type="text" name="cert_status_withdrawn_col_date" class="form-control" value="<?= pc_h($pc['cert_status_withdrawn_col_date']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Reduced Scope block -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Section: Reduced-Scope Certifications</h5>
            <div class="mb-3">
                <label class="form-label">Block Title</label>
                <input type="text" name="cert_status_reduced_title" class="form-control" value="<?= pc_h($pc['cert_status_reduced_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Empty-State Message</label>
                <input type="text" name="cert_status_reduced_empty" class="form-control" value="<?= pc_h($pc['cert_status_reduced_empty']) ?>">
            </div>
            <hr>
            <h6>Table Column Headings</h6>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Client</label>
                    <input type="text" name="cert_status_reduced_col_client" class="form-control" value="<?= pc_h($pc['cert_status_reduced_col_client']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Certificate No.</label>
                    <input type="text" name="cert_status_reduced_col_cert_no" class="form-control" value="<?= pc_h($pc['cert_status_reduced_col_cert_no']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Standard / Original Scope</label>
                    <input type="text" name="cert_status_reduced_col_scope" class="form-control" value="<?= pc_h($pc['cert_status_reduced_col_scope']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Date / Effective</label>
                    <input type="text" name="cert_status_reduced_col_date" class="form-control" value="<?= pc_h($pc['cert_status_reduced_col_date']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Notes</label>
                    <input type="text" name="cert_status_reduced_col_note" class="form-control" value="<?= pc_h($pc['cert_status_reduced_col_note']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer note — Appeals -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Footer Note — Disputing a Decision (Appeals)</h5>
            <div class="mb-3">
                <label class="form-label">Lead Label</label>
                <input type="text" name="cert_status_footer_appeals_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_appeals_label']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body Text</label>
                <textarea name="cert_status_footer_appeals_body" class="form-control" rows="4"><?= pc_h($pc['cert_status_footer_appeals_body']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link Label</label>
                    <input type="text" name="cert_status_footer_appeals_link_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_appeals_link_label']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link URL</label>
                    <?php $doc_key = 'cert_status_footer_appeals_link_url'; $doc_label = 'Appeals procedure document';
                          include __DIR__ . '/../includes/document_field.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer note — Complaints -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Footer Note — Lodging a Complaint</h5>
            <div class="mb-3">
                <label class="form-label">Lead Label</label>
                <input type="text" name="cert_status_footer_complaints_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_complaints_label']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body Text</label>
                <textarea name="cert_status_footer_complaints_body" class="form-control" rows="4"><?= pc_h($pc['cert_status_footer_complaints_body']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link Label</label>
                    <input type="text" name="cert_status_footer_complaints_link_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_complaints_link_label']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link URL</label>
                    <?php $doc_key = 'cert_status_footer_complaints_link_url'; $doc_label = 'Complaints procedure document';
                          include __DIR__ . '/../includes/document_field.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer note — Information requests -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Footer Note — Requesting Information</h5>
            <div class="mb-3">
                <label class="form-label">Lead Label</label>
                <input type="text" name="cert_status_footer_info_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_info_label']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body Text</label>
                <textarea name="cert_status_footer_info_body" class="form-control" rows="3"><?= pc_h($pc['cert_status_footer_info_body']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Phone 1</label>
                    <input type="text" name="cert_status_footer_info_phone_1" class="form-control" value="<?= pc_h($pc['cert_status_footer_info_phone_1']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Phone 2</label>
                    <input type="text" name="cert_status_footer_info_phone_2" class="form-control" value="<?= pc_h($pc['cert_status_footer_info_phone_2']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link Label</label>
                    <input type="text" name="cert_status_footer_info_link_label" class="form-control" value="<?= pc_h($pc['cert_status_footer_info_link_label']) ?>">
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Link URL</label>
                    <?php $doc_key = 'cert_status_footer_info_link_url'; $doc_label = 'Requests-for-information procedure document';
                          include __DIR__ . '/../includes/document_field.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_cert_status" value="1" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

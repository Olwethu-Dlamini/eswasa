<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Breadcrumb
    'purchase_breadcrumb_home_label',
    'purchase_breadcrumb_parent_label',
    'purchase_breadcrumb_current_label',
    'purchase_breadcrumb_title',
    // Intro
    'purchase_intro_title',
    'purchase_intro_body',
    // Catalogue card
    'purchase_catalogue_title',
    'purchase_catalogue_body',
    'purchase_catalogue_link_text',
    'purchase_catalogue_link_url',
    // Webstore card
    'purchase_webstore_title',
    'purchase_webstore_body',
    'purchase_webstore_link_text',
    'purchase_webstore_link_url',
    // Assistance card
    'purchase_assist_title',
    'purchase_assist_body',
    'purchase_contact_phone',
    'purchase_contact_fax',
    'purchase_contact_email_general',
    'purchase_contact_email_sales',
    // CTA
    'purchase_cta_primary_text',
    'purchase_cta_primary_url',
    'purchase_cta_secondary_text',
    'purchase_cta_secondary_url',
];
$image_keys = []; // No images on this page

// ── Save handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_purchase'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }

    // Document uploads win over whatever is in the matching URL box, so an
    // existing external link keeps working when nothing new is uploaded.
    // Rejections are surfaced rather than dropped. See spec item C2.
    $doc_errors = [];
    foreach (['purchase_catalogue_link_url'] as $dk) {
        $up = pc_upload_document($dk . '_file', ADMIN_ROOT . '/uploads/', 'doc');
        if (is_string($up) && strpos($up, 'ERR:') === 0) {
            $doc_errors[] = substr($up, 4);
        } elseif (is_string($up)) {
            $kv[$dk] = $up;
        }
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'purchase');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    if (function_exists('set_flash')) {
        if ($doc_errors) {
            set_flash('danger', 'Saved, but a document was not replaced: ' . implode(' ', $doc_errors));
        } else {
            set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
        }
    }
    redirect_self();
}

// ── Load current values ───────────────────────────────────────
$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Purchase Standards</h1>
    <a href="../purchase.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
</div>

<form method="POST" enctype="multipart/form-data">

    <!-- Breadcrumb -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Title (banner heading)</label>
                    <input type="text" name="purchase_breadcrumb_title" class="form-control" value="<?= pc_h($pc['purchase_breadcrumb_title']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">"Home" link label</label>
                    <input type="text" name="purchase_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['purchase_breadcrumb_home_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Parent section label</label>
                    <input type="text" name="purchase_breadcrumb_parent_label" class="form-control" value="<?= pc_h($pc['purchase_breadcrumb_parent_label']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current page label</label>
                    <input type="text" name="purchase_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['purchase_breadcrumb_current_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Intro -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Intro Box</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="purchase_intro_title" class="form-control" value="<?= pc_h($pc['purchase_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="purchase_intro_body" class="form-control" rows="6"><?= pc_h($pc['purchase_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- Standards Catalogue card -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Standards Catalogue Card</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="purchase_catalogue_title" class="form-control" value="<?= pc_h($pc['purchase_catalogue_title']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Body</label>
                    <textarea name="purchase_catalogue_body" class="form-control" rows="3"><?= pc_h($pc['purchase_catalogue_body']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link text</label>
                    <input type="text" name="purchase_catalogue_link_text" class="form-control" value="<?= pc_h($pc['purchase_catalogue_link_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link URL (e.g. admin/uploads/catalogue.pdf)</label>
                    <?php $doc_key = 'purchase_catalogue_link_url'; $doc_label = 'Standards catalogue';
                          include __DIR__ . '/../includes/document_field.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Webstore card -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Online Webstore Card</h5>
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label">Title</label>
                    <input type="text" name="purchase_webstore_title" class="form-control" value="<?= pc_h($pc['purchase_webstore_title']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Body</label>
                    <textarea name="purchase_webstore_body" class="form-control" rows="3"><?= pc_h($pc['purchase_webstore_body']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link text</label>
                    <input type="text" name="purchase_webstore_link_text" class="form-control" value="<?= pc_h($pc['purchase_webstore_link_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Link URL</label>
                    <input type="url" name="purchase_webstore_link_url" class="form-control" value="<?= pc_h($pc['purchase_webstore_link_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Assistance / Contact -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Need Assistance Card</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="purchase_assist_title" class="form-control" value="<?= pc_h($pc['purchase_assist_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Lead paragraph</label>
                <textarea name="purchase_assist_body" class="form-control" rows="4"><?= pc_h($pc['purchase_assist_body']) ?></textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Telephone</label>
                    <input type="text" name="purchase_contact_phone" class="form-control" value="<?= pc_h($pc['purchase_contact_phone']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fax</label>
                    <input type="text" name="purchase_contact_fax" class="form-control" value="<?= pc_h($pc['purchase_contact_fax']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email (General)</label>
                    <input type="text" name="purchase_contact_email_general" class="form-control" value="<?= pc_h($pc['purchase_contact_email_general']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email (Sales)</label>
                    <input type="text" name="purchase_contact_email_sales" class="form-control" value="<?= pc_h($pc['purchase_contact_email_sales']) ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- CTA buttons -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Bottom CTA Buttons</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Primary button text</label>
                    <input type="text" name="purchase_cta_primary_text" class="form-control" value="<?= pc_h($pc['purchase_cta_primary_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Primary button URL</label>
                    <input type="text" name="purchase_cta_primary_url" class="form-control" value="<?= pc_h($pc['purchase_cta_primary_url']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary button text</label>
                    <input type="text" name="purchase_cta_secondary_text" class="form-control" value="<?= pc_h($pc['purchase_cta_secondary_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Secondary button URL</label>
                    <input type="text" name="purchase_cta_secondary_url" class="form-control" value="<?= pc_h($pc['purchase_cta_secondary_url']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_purchase" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

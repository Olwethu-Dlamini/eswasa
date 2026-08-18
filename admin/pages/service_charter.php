<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_service_charter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_service_charter'])) {
    $kv = [];
    foreach ($service_charter_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, $service_charter_keys, $service_charter_defaults);
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Service Charter</h1>
    <a href="../service-charter.php" target="_blank" class="btn btn-sm btn-outline-secondary">View page</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<p class="text-muted small mb-4">
    Edit the public-facing text on the Service Charter page. Everything on the
    page is editable here, including the five charter blocks.
</p>

<form method="POST">

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Breadcrumb</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Title (banner heading)</label>
                    <input type="text" name="service_charter_breadcrumb_title" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_title']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">"Home" link label</label>
                    <input type="text" name="service_charter_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_home_label']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Parent section label</label>
                    <input type="text" name="service_charter_breadcrumb_parent_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_parent_label']) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Current page label</label>
                    <input type="text" name="service_charter_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['service_charter_breadcrumb_current_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Page Heading &amp; Intro Card</h5>
            <div class="mb-3">
                <label class="form-label">Section heading (shown above the intro card)</label>
                <input type="text" name="service_charter_section_title" class="form-control" value="<?= pc_h($pc['service_charter_section_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Intro card body (separate paragraphs with a blank line)</label>
                <textarea name="service_charter_intro_body" class="form-control" rows="5"><?= pc_h($pc['service_charter_intro_body']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Bottom "Feedback" Call-to-Action</h5>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="service_charter_cta_title" class="form-control" value="<?= pc_h($pc['service_charter_cta_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Body</label>
                <input type="text" name="service_charter_cta_body" class="form-control" value="<?= pc_h($pc['service_charter_cta_body']) ?>">
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Button text</label>
                    <input type="text" name="service_charter_cta_button_text" class="form-control" value="<?= pc_h($pc['service_charter_cta_button_text']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Button URL</label>
                    <input type="text" name="service_charter_cta_button_url" class="form-control" value="<?= pc_h($pc['service_charter_cta_button_url']) ?>">
                </div>
            </div>
        </div>
    </div>


    <?php /* The five charter blocks were hardcoded until Batch B. Bullet lists
             take one item per line. See spec item B4. */ ?>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Charter block 1 &mdash; Who We Are</h5>
            <div class="mb-3">
                <label class="form-label">Heading</label>
                <input type="text" name="charter_who_title" class="form-control" value="<?= pc_h($pc['charter_who_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Body (separate paragraphs with a blank line)</label>
                <textarea name="charter_who_body" class="form-control" rows="4"><?= pc_h($pc['charter_who_body']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Charter block 2 &mdash; Our Service Standards</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Heading</label>
                    <input type="text" name="charter_standards_title" class="form-control" value="<?= pc_h($pc['charter_standards_title']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Intro line</label>
                    <input type="text" name="charter_standards_intro" class="form-control" value="<?= pc_h($pc['charter_standards_intro']) ?>">
                </div>
            </div>
            <label class="form-label fw-bold">Commitments</label>
            <div class="form-text mb-2">Each commitment shows as a tile. Leave a pair blank to hide it &mdash; there is room for eight.</div>
            <div class="row g-2">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                    <div class="col-md-6">
                        <div class="border rounded p-2 mb-1">
                            <label class="form-label small mb-1">Commitment <?= $i ?> &mdash; label</label>
                            <input type="text" name="charter_commit_<?= $i ?>_label" class="form-control form-control-sm mb-2"
                                   value="<?= pc_h($pc["charter_commit_{$i}_label"]) ?>" placeholder="e.g. Acknowledgement">
                            <label class="form-label small mb-1">Description</label>
                            <textarea name="charter_commit_<?= $i ?>_body" class="form-control form-control-sm" rows="2"><?= pc_h($pc["charter_commit_{$i}_body"]) ?></textarea>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Charter block 3 &mdash; Our Core Values</h5>
            <div class="mb-3">
                <label class="form-label">Heading</label>
                <input type="text" name="charter_values_title" class="form-control" value="<?= pc_h($pc['charter_values_title']) ?>">
            </div>
            <div class="mb-0">
                <label class="form-label">Values &mdash; one per line</label>
                <textarea name="charter_values_items" class="form-control" rows="6"><?= pc_h($pc['charter_values_items']) ?></textarea>
                <div class="form-text">Write each as <code>Name &mdash; description</code>. The part before the dash is shown in bold.</div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Charter block 4 &mdash; What We Ask Of You</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Heading</label>
                    <input type="text" name="charter_ask_title" class="form-control" value="<?= pc_h($pc['charter_ask_title']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Intro line</label>
                    <input type="text" name="charter_ask_intro" class="form-control" value="<?= pc_h($pc['charter_ask_intro']) ?>">
                </div>
            </div>
            <label class="form-label">Bullet points &mdash; one per line</label>
            <textarea name="charter_ask_items" class="form-control" rows="6"><?= pc_h($pc['charter_ask_items']) ?></textarea>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-3">Charter block 5 &mdash; If We Fall Short</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Heading</label>
                    <input type="text" name="charter_short_title" class="form-control" value="<?= pc_h($pc['charter_short_title']) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label">Intro line</label>
                    <input type="text" name="charter_short_intro" class="form-control" value="<?= pc_h($pc['charter_short_intro']) ?>">
                </div>
            </div>
            <label class="form-label fw-bold">How people can reach you</label>
            <div class="form-text mb-2">
                Give a link target to make the whole line clickable &mdash; a page
                (<code>customer-feedback.php</code>), an email (<code>mailto:info@eswasa.co.sz</code>)
                or a phone number (<code>tel:+26825184633</code>). Leave it blank for plain text.
            </div>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" name="charter_short_<?= $i ?>_text" class="form-control form-control-sm"
                               value="<?= pc_h($pc["charter_short_{$i}_text"]) ?>" placeholder="Route <?= $i ?> — leave blank to hide">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="charter_short_<?= $i ?>_url" class="form-control form-control-sm"
                               value="<?= pc_h($pc["charter_short_{$i}_url"]) ?>" placeholder="link target (optional)">
                    </div>
                </div>
            <?php endfor; ?>
            <div class="mt-3">
                <label class="form-label">Closing paragraph</label>
                <textarea name="charter_short_outro" class="form-control" rows="2"><?= pc_h($pc['charter_short_outro']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top text-end">
        <button type="submit" name="save_service_charter" class="btn btn-primary px-5 shadow-sm">
            <i class="fas fa-save me-2"></i>Save Changes
        </button>
    </div>
</form>

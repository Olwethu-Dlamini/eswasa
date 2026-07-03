<?php
// admin/pages/site_settings.php — site-wide settings (analytics, etc.).
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';

// ---- POST: save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $ga4 = trim($_POST['site_ga4_id'] ?? '');

    // Accept a Google tag id (G-XXXX), legacy UA-XXXX, or GTM-XXXX — or blank
    // to disable tracking. Reject anything else so a stray paste can't inject.
    if ($ga4 !== '' && !preg_match('/^(G|UA|GTM)-[A-Z0-9-]+$/i', $ga4)) {
        set_flash('danger', 'That does not look like a valid Google tag ID (expected e.g. G-XXXXXXXXXX). Left unchanged.');
        redirect_self();
    }

    pc_save($conn, 'site_ga4_id', $ga4);
    set_flash('success', $ga4 === '' ? 'Analytics disabled.' : 'Analytics ID saved.');
    redirect_self();
}

// ---- Current values ----
$settings = pc_get_many($conn, ['site_ga4_id'], ['site_ga4_id' => '']);
$ga4_id = $settings['site_ga4_id'];
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Site Settings</h1>
</div>

<div class="row">
    <div class="col-lg-7">
        <form method="post">
            <input type="hidden" name="save_settings" value="1">

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Website Analytics</div>
                <div class="card-body">
                    <label class="form-label">Google Analytics Measurement ID</label>
                    <input type="text" class="form-control" name="site_ga4_id"
                           value="<?= htmlspecialchars($ga4_id) ?>"
                           placeholder="G-XXXXXXXXXX">
                    <small class="form-text text-muted d-block mt-2">
                        Paste your Google Analytics 4 Measurement ID (looks like
                        <code>G-XXXXXXXXXX</code>). The tracking code is then added to
                        every public page automatically. Leave blank to turn tracking off.
                        <br>Find it in Google Analytics → Admin → Data Streams → your stream.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save settings
            </button>
        </form>
    </div>
</div>

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

    // Where contact-form notifications are emailed. Blank falls back to the
    // default at send time; anything non-blank must be a valid address, or a
    // typo would silently send notifications nowhere. See spec item A2.
    $notify = trim($_POST['site_contact_notify_email'] ?? '');
    if ($notify !== '' && !filter_var($notify, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'That notification email address is not valid. Nothing was saved.');
        redirect_self();
    }

    pc_save($conn, 'site_ga4_id', $ga4);
    pc_save($conn, 'site_contact_notify_email', $notify);
    set_flash('success', 'Settings saved.');
    redirect_self();
}

// ---- Current values ----
$settings = pc_get_many($conn, ['site_ga4_id', 'site_contact_notify_email'], [
    'site_ga4_id'               => '',
    'site_contact_notify_email' => '',
]);
$ga4_id       = $settings['site_ga4_id'];
$notify_email = $settings['site_contact_notify_email'];
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

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-envelope me-2"></i>Contact Form Notifications</div>
                <div class="card-body">
                    <label class="form-label">Send new contact messages to</label>
                    <input type="email" class="form-control" name="site_contact_notify_email"
                           value="<?= htmlspecialchars($notify_email) ?>"
                           placeholder="info@eswasa.co.sz">
                    <small class="form-text text-muted d-block mt-2">
                        When someone submits the Contact Us form, a copy is emailed here.
                        Leave blank to use <code>info@eswasa.co.sz</code>.
                        <br>Every message is also saved in the admin regardless of email
                        delivery &mdash; see <strong>Contact Us</strong> in the sidebar.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save settings
            </button>
        </form>
    </div>
</div>

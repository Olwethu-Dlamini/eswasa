<?php
// admin/pages/site_settings.php — site-wide settings (analytics, email, etc.).
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require_once __DIR__ . '/../../includes/mailer.php';
require_once __DIR__ . '/../../includes/form_inboxes.php';

// Recipient fields: the all-forms default first, then one per inbox.
$notify_fields = [ESWASA_NOTIFY_DEFAULT_KEY => 'All forms (default)'];
$notify_inbox_of = [];
foreach (eswasa_inboxes() as $inbox_key => $ib) {
    $notify_fields[$ib['notify_key']] = $ib['inbox'];
    $notify_inbox_of[$ib['notify_key']] = $inbox_key;
}

$smtp_keys = array_column(ESWASA_MAIL_SETTING_KEYS, 1, null);
$smtp_key_of = array_combine(array_keys(ESWASA_MAIL_SETTING_KEYS), $smtp_keys);

// ---- Every POST here must come from this page (see csrf_valid()) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['send_test_email']) || isset($_POST['save_settings'])) && !csrf_valid()) {
    set_flash('danger', 'That form had expired, so nothing was changed. Please try again.');
    redirect_self();
}

// ---- POST: send a test email ----
// Uses the saved settings, so what is tested is exactly what the forms use.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test_email'])) {
    $test_to = trim($_POST['test_to'] ?? '');
    if (!filter_var($test_to, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Enter a valid address to send the test email to.');
        redirect_self();
    }
    $s = eswasa_mail_settings($conn);
    $via = $s['smtp'] ? 'SMTP (' . $s['host'] . ':' . $s['port'] . ')' : 'PHP mail() — SMTP is not configured';
    $html = '<p>This is a test email from the ESWASA website admin.</p>'
          . '<p>If you are reading it, the site can deliver form notifications to this address.</p>'
          . '<p style="color:#666;font-size:13px;">Sent via ' . htmlspecialchars($via) . ' at '
          . htmlspecialchars(date('Y-m-d H:i:s')) . ' by ' . htmlspecialchars($_SESSION['username'] ?? 'admin') . '.</p>';
    $r = eswasa_send_mail($conn, $test_to, 'ESWASA website — test email', $html, ['context' => 'test']);
    log_activity($conn, 'mail.test', $test_to, ($r['ok'] ? 'sent' : 'failed') . ' via ' . $r['transport']);
    if ($r['ok']) {
        set_flash('success', 'Test email sent to ' . $test_to . ' via ' . $via . '. Check the inbox (and the spam folder).');
    } else {
        set_flash('danger', 'The test email could not be sent via ' . $via . ': ' . $r['error']);
    }
    redirect_self();
}

// ---- POST: save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $ga4 = trim($_POST['site_ga4_id'] ?? '');

    // Accept a Google tag id (G-XXXX), legacy UA-XXXX, or GTM-XXXX — or blank
    // to disable tracking. Reject anything else so a stray paste can't inject.
    if ($ga4 !== '' && !preg_match('/^(G|UA|GTM)-[A-Z0-9-]+$/i', $ga4)) {
        set_flash('danger', 'That does not look like a valid Google tag ID (expected e.g. G-XXXXXXXXXX). Left unchanged.');
        redirect_self();
    }

    // Where each form's notifications are emailed. Blank falls back to the
    // all-forms default at send time; anything non-blank must be entirely
    // valid addresses, or a typo would silently send notifications nowhere.
    // See spec item A2.
    $notify = [];
    foreach ($notify_fields as $key => $label) {
        $raw = trim((string)($_POST[$key] ?? ''));
        $tokens = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $bad = array_filter($tokens, function ($t) {
            return !filter_var($t, FILTER_VALIDATE_EMAIL);
        });
        if ($bad) {
            set_flash('danger', 'Not a valid email address for ' . $label . ': ' . implode(', ', $bad) . '. Nothing was saved.');
            redirect_self();
        }
        $notify[$key] = implode(', ', eswasa_parse_emails($raw));
    }

    // ---- SMTP ----
    // Fields set by an environment variable on the server are shown read-only
    // and never written here: the environment would override them anyway.
    $current = eswasa_mail_settings($conn);
    $stored_before = pc_get_many($conn, $smtp_keys);
    $smtp = [];
    foreach (['host', 'port', 'secure', 'username', 'from_email', 'from_name'] as $f) {
        if ($current['source'][$f] !== 'env') {
            $smtp[$f] = trim((string)($_POST[$smtp_key_of[$f]] ?? ''));
        }
    }
    $smtp_errors = [];
    if (isset($smtp['host']) && $smtp['host'] !== '' && !preg_match('/^[A-Za-z0-9.-]+$/', $smtp['host'])) {
        $smtp_errors[] = 'the SMTP server should be a host name such as mail.eswasa.co.sz';
    }
    if (isset($smtp['port']) && $smtp['port'] !== '' && (!ctype_digit($smtp['port']) || (int)$smtp['port'] < 1 || (int)$smtp['port'] > 65535)) {
        $smtp_errors[] = 'the port must be a number between 1 and 65535';
    }
    if (isset($smtp['secure']) && !in_array($smtp['secure'], ['tls', 'ssl', 'none'], true)) {
        $smtp['secure'] = 'tls';
    }
    if (isset($smtp['from_email']) && $smtp['from_email'] !== '' && !filter_var($smtp['from_email'], FILTER_VALIDATE_EMAIL)) {
        $smtp_errors[] = 'the "send from" address is not a valid email address';
    }
    if (isset($smtp['from_name'])) {
        $smtp['from_name'] = pc_strip_text($smtp['from_name']);
    }
    if ($smtp_errors) {
        set_flash('danger', 'Nothing was saved: ' . implode('; ', $smtp_errors) . '.');
        redirect_self();
    }

    pc_save($conn, 'site_ga4_id', $ga4);
    foreach ($notify as $key => $list) {
        pc_save($conn, $key, $list);
    }
    foreach ($smtp as $f => $v) {
        pc_save($conn, $smtp_key_of[$f], $v);
    }

    // The password is write-only: the form never shows it, so a blank box
    // means "keep what is saved". Clearing it takes the explicit checkbox.
    //
    // Except when the server or the username changes: then a saved password
    // is never carried over to the new login. Otherwise pointing the server
    // field somewhere else would hand the saved password to that server on
    // the next email, without anyone ever having typed it there.
    $password_note = '';
    $password_dropped = false;
    $login_changed = (isset($smtp['host']) && $smtp['host'] !== (string)($stored_before[$smtp_key_of['host']] ?? ''))
        || (isset($smtp['username']) && $smtp['username'] !== (string)($stored_before[$smtp_key_of['username']] ?? ''));
    if ($current['source']['password'] !== 'env') {
        $typed = (string)($_POST[$smtp_key_of['password']] ?? '');
        if ($login_changed && $typed === '' && $current['password'] !== '') {
            pc_save($conn, $smtp_key_of['password'], '');
            $password_note = ' The SMTP server or username changed, so the saved password was removed: enter it again.';
            $password_dropped = true;
        } elseif (!empty($_POST['clear_smtp_password'])) {
            pc_save($conn, $smtp_key_of['password'], '');
            $password_note = ' SMTP password removed.';
        } elseif (($_POST[$smtp_key_of['password']] ?? '') !== '') {
            pc_save($conn, $smtp_key_of['password'], (string)$_POST[$smtp_key_of['password']]);
            $password_note = ' SMTP password updated.';
        }
    }

    $host_note = $smtp['host'] ?? 'set on the server';
    log_activity($conn, 'settings.save', 'Site Settings',
        'SMTP server: ' . ($host_note !== '' ? $host_note : 'none') . '.' . $password_note);
    set_flash($password_dropped ? 'warning' : 'success', 'Settings saved.' . $password_note);
    redirect_self();
}

// ---- Current values ----
$settings = pc_get_many($conn, array_merge(['site_ga4_id'], array_keys($notify_fields)));
$ga4_id   = $settings['site_ga4_id'];

$mail   = eswasa_mail_settings($conn);
$stored = pc_get_many($conn, $smtp_keys);   // raw saved values, before defaults
$mail_log = eswasa_mail_log_recent($conn, 15);
$mail_log_ready = false;
try {
    $mail_log_ready = ($r = @$conn->query("SHOW TABLES LIKE 'mail_log'")) && $r->num_rows > 0;
} catch (Throwable $e) {
}

$current_user_email = '';
if ($u = getCurrentUser($conn)) {
    $current_user_email = (string)($u['email'] ?? '');
}

// Value shown in an SMTP field: the environment's value (read-only) when it
// is set there, otherwise what is saved in the admin.
function smtp_field_value(array $mail, array $stored, array $key_of, string $f): string
{
    return $mail['source'][$f] === 'env' ? (string)$mail[$f] : (string)($stored[$key_of[$f]] ?? '');
}
function smtp_env_note(array $mail, string $f): string
{
    if ($mail['source'][$f] !== 'env') {
        return '';
    }
    return '<small class="text-warning-emphasis d-block mt-1"><i class="fas fa-lock me-1"></i>Set on the server by <code>'
        . htmlspecialchars(ESWASA_MAIL_SETTING_KEYS[$f][0]) . '</code> &mdash; change it there.</small>';
}
$env_attr = function (string $f) use ($mail) {
    return $mail['source'][$f] === 'env' ? ' disabled' : '';
};
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Site Settings</h1>
</div>

<div class="row">
    <div class="col-lg-7">
        <form method="post" autocomplete="off">
            <input type="hidden" name="save_settings" value="1">
            <?= csrf_field() ?>

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

            <div class="card mb-4" id="notifications">
                <div class="card-header"><i class="fas fa-bell me-2"></i>Form Notifications</div>
                <div class="card-body">
                    <p class="small text-muted">
                        Who is emailed when a form is submitted. Separate several addresses
                        with commas. A blank form uses the default; a blank default uses
                        <code><?= htmlspecialchars(ESWASA_NOTIFY_FALLBACK) ?></code>.
                        Under each form is where its notifications go right now.
                        Every submission is also saved in the admin whether or not the email
                        arrives.
                    </p>
                    <?php foreach ($notify_fields as $key => $label):
                        $is_default = $key === ESWASA_NOTIFY_DEFAULT_KEY; ?>
                        <div class="row g-2 align-items-center mb-2">
                            <label class="col-sm-5 col-form-label col-form-label-sm<?= $is_default ? ' fw-semibold' : '' ?>" for="n_<?= htmlspecialchars($key) ?>">
                                <?= htmlspecialchars($label) ?>
                            </label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control form-control-sm" id="n_<?= htmlspecialchars($key) ?>"
                                       name="<?= htmlspecialchars($key) ?>"
                                       value="<?= htmlspecialchars((string)$settings[$key]) ?>"
                                       placeholder="<?= $is_default ? htmlspecialchars(ESWASA_NOTIFY_FALLBACK) : 'same as the default' ?>">
                                <?php if (!$is_default):
                                    [$goes_to, $from_key] = eswasa_notify_recipients_with_source($conn, $notify_inbox_of[$key]);
                                    $why = $from_key === $key ? ''
                                        : ($from_key === ESWASA_NOTIFY_DEFAULT_KEY ? ' (the default)'
                                        : ($from_key === '' ? ' (built in)'
                                        : ' (the address shown on the ' . $notify_fields[$key] . ' page — set one here to override it)')); ?>
                                    <small class="text-muted d-block mt-1">
                                        Sends to <?= htmlspecialchars(implode(', ', $goes_to)) ?><?= htmlspecialchars($why) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($is_default): ?><hr class="my-2"><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card mb-4" id="smtp">
                <div class="card-header"><i class="fas fa-paper-plane me-2"></i>Email Delivery (SMTP)</div>
                <div class="card-body">
                    <p class="small text-muted">
                        The details of the mailbox the website sends from, as given by your
                        email provider or in Plesk &rsaquo; Mail. Leave the server blank to keep
                        using the web server&rsquo;s built-in mail, which is often filtered as spam.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">SMTP server</label>
                            <input type="text" class="form-control" name="site_smtp_host"
                                   value="<?= htmlspecialchars(smtp_field_value($mail, $stored, $smtp_key_of, 'host')) ?>"
                                   placeholder="e.g. mail.eswasa.co.sz"<?= $env_attr('host') ?>>
                            <?= smtp_env_note($mail, 'host') ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Port</label>
                            <input type="text" inputmode="numeric" class="form-control" name="site_smtp_port"
                                   value="<?= htmlspecialchars(smtp_field_value($mail, $stored, $smtp_key_of, 'port')) ?>"
                                   placeholder="587"<?= $env_attr('port') ?>>
                            <?= smtp_env_note($mail, 'port') ?>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Encryption</label>
                            <?php $sec = smtp_field_value($mail, $stored, $smtp_key_of, 'secure') ?: 'tls'; ?>
                            <select class="form-select" name="site_smtp_secure"<?= $env_attr('secure') ?>>
                                <option value="tls"  <?= $sec === 'tls'  ? 'selected' : '' ?>>STARTTLS &mdash; usually port 587 (recommended)</option>
                                <option value="ssl"  <?= $sec === 'ssl'  ? 'selected' : '' ?>>SSL/TLS &mdash; usually port 465</option>
                                <option value="none" <?= $sec === 'none' ? 'selected' : '' ?>>None &mdash; not recommended</option>
                            </select>
                            <?= smtp_env_note($mail, 'secure') ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="site_smtp_username"
                                   value="<?= htmlspecialchars(smtp_field_value($mail, $stored, $smtp_key_of, 'username')) ?>"
                                   placeholder="usually the full email address" autocomplete="off"<?= $env_attr('username') ?>>
                            <?= smtp_env_note($mail, 'username') ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <?php if ($mail['source']['password'] === 'env'): ?>
                                <input type="password" class="form-control" value="" placeholder="set on the server" disabled>
                                <?= smtp_env_note($mail, 'password') ?>
                            <?php else: ?>
                                <input type="password" class="form-control" name="site_smtp_password" value=""
                                       autocomplete="new-password"
                                       placeholder="<?= $mail['password'] !== '' ? 'saved — leave blank to keep' : 'mailbox password' ?>">
                                <?php if ($mail['password'] !== ''): ?>
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox" name="clear_smtp_password" value="1" id="clearSmtpPw">
                                        <label class="form-check-label small" for="clearSmtpPw">Remove the saved password</label>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Send from (email)</label>
                            <input type="email" class="form-control" name="site_mail_from_email"
                                   value="<?= htmlspecialchars(smtp_field_value($mail, $stored, $smtp_key_of, 'from_email')) ?>"
                                   placeholder="blank = the username above"<?= $env_attr('from_email') ?>>
                            <?= smtp_env_note($mail, 'from_email') ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Send from (name)</label>
                            <input type="text" class="form-control" name="site_mail_from_name"
                                   value="<?= htmlspecialchars(smtp_field_value($mail, $stored, $smtp_key_of, 'from_name')) ?>"
                                   placeholder="<?= htmlspecialchars(ESWASA_MAIL_DEFAULT_FROM_NAME) ?>"<?= $env_attr('from_name') ?>>
                            <?= smtp_env_note($mail, 'from_name') ?>
                        </div>
                    </div>
                    <small class="form-text text-muted d-block mt-3">
                        Most providers only accept mail sent <em>from</em> the mailbox that logs in,
                        so leave &ldquo;Send from&rdquo; blank unless yours allows otherwise.
                        The password is never shown again once saved.
                    </small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save settings
            </button>
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-circle-check me-2"></i>Email Status</div>
            <div class="card-body">
                <?php if ($mail['smtp']): ?>
                    <p class="mb-2">
                        <span class="badge bg-success">SMTP</span>
                        Sending through <strong><?= htmlspecialchars($mail['host'] . ':' . $mail['port']) ?></strong>
                        (<?= htmlspecialchars(['tls' => 'STARTTLS', 'ssl' => 'SSL/TLS', 'none' => 'no encryption'][$mail['secure']]) ?>)
                        <?= $mail['username'] !== '' ? 'as <strong>' . htmlspecialchars($mail['username']) . '</strong>' : 'without logging in' ?>.
                    </p>
                <?php else: ?>
                    <p class="mb-2">
                        <span class="badge bg-warning text-dark">Not configured</span>
                        SMTP is not set up, so emails go through the web server&rsquo;s built-in
                        <code>mail()</code>. They may be delayed, filtered as spam or dropped.
                    </p>
                <?php endif; ?>
                <p class="small text-muted mb-3">
                    Emails are sent from <strong><?= htmlspecialchars($mail['from_name'] . ' <' . $mail['from_email'] . '>') ?></strong>.
                </p>

                <form method="post" class="d-flex gap-2">
                    <input type="hidden" name="send_test_email" value="1">
                    <?= csrf_field() ?>
                    <input type="email" class="form-control form-control-sm" name="test_to" required
                           value="<?= htmlspecialchars($current_user_email) ?>" placeholder="you@example.com">
                    <button type="submit" class="btn btn-sm btn-outline-primary text-nowrap">
                        <i class="fas fa-paper-plane me-1"></i>Send test
                    </button>
                </form>
                <small class="text-muted d-block mt-2">Uses the saved settings &mdash; save first.</small>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><i class="fas fa-list me-2"></i>Recent Email Activity</div>
            <div class="card-body p-0">
                <?php if (!$mail_log_ready): ?>
                    <p class="small text-muted p-3 mb-0">
                        The email log is not set up yet. Run
                        <code>admin/sql/upgrade_2026_09_25.sql</code> on the database.
                    </p>
                <?php elseif (!$mail_log): ?>
                    <p class="small text-muted p-3 mb-0">No emails sent yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 small align-middle">
                            <thead class="table-light">
                                <tr><th>When</th><th>What</th><th>Result</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($mail_log as $m): ?>
                                <tr>
                                    <td class="text-nowrap"><?= htmlspecialchars(date('d M H:i', strtotime($m['created_at']))) ?></td>
                                    <td>
                                        <div class="text-truncate" style="max-width:220px;" title="<?= htmlspecialchars($m['subject']) ?>"><?= htmlspecialchars($m['subject']) ?></div>
                                        <div class="text-muted text-truncate" style="max-width:220px;" title="<?= htmlspecialchars($m['recipients']) ?>">to <?= htmlspecialchars($m['recipients']) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($m['status'] === 'sent'): ?>
                                            <span class="badge bg-success">Sent</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger" title="<?= htmlspecialchars((string)$m['error']) ?>">Failed</span>
                                        <?php endif; ?>
                                        <div class="text-muted"><?= htmlspecialchars($m['transport']) ?></div>
                                    </td>
                                </tr>
                                <?php if ($m['status'] !== 'sent' && $m['error']): ?>
                                    <tr><td colspan="3" class="text-danger-emphasis border-top-0 pt-0"><?= htmlspecialchars($m['error']) ?></td></tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

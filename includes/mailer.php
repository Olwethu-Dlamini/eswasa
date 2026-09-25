<?php
/**
 * includes/mailer.php — the one way the site sends email.
 *
 * Why this exists: every form notification used to go through @mail(), which
 * hands the message, unauthenticated, to the web server's local mail program.
 * On shared hosting that mail is routinely rejected or filed as spam, and the
 * @ meant a failure left no trace anywhere. This sends through an
 * authenticated SMTP mailbox when one is configured, falls back to mail() when
 * not (so nothing changes until SMTP details are entered), and records every
 * attempt in mail_log so a delivery problem shows up in Admin › Site Settings
 * instead of disappearing.
 *
 * SMTP settings — for each field the first one set wins:
 *   1. Environment variables on the host:
 *        ESWASA_SMTP_HOST, ESWASA_SMTP_PORT, ESWASA_SMTP_SECURE (tls|ssl|none),
 *        ESWASA_SMTP_USER, ESWASA_SMTP_PASS,
 *        ESWASA_MAIL_FROM, ESWASA_MAIL_FROM_NAME
 *   2. Admin › Site Settings › Email delivery (page_content keys below).
 * SMTP is used as soon as a host is set; with no host, PHP mail() is used.
 *
 * This repository is public. Never write a real SMTP password into this file
 * or into env.php — enter it in the admin, or set it as an environment
 * variable on the server.
 */

if (defined('ESWASA_MAILER_LOADED')) {
    return; // guard against double-include
}
define('ESWASA_MAILER_LOADED', true);

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/cms_helpers.php';
require_once __DIR__ . '/lib/PHPMailer/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/SMTP.php';

// field => [environment variable, page_content key]
const ESWASA_MAIL_SETTING_KEYS = [
    'host'       => ['ESWASA_SMTP_HOST',      'site_smtp_host'],
    'port'       => ['ESWASA_SMTP_PORT',      'site_smtp_port'],
    'secure'     => ['ESWASA_SMTP_SECURE',    'site_smtp_secure'],
    'username'   => ['ESWASA_SMTP_USER',      'site_smtp_username'],
    'password'   => ['ESWASA_SMTP_PASS',      'site_smtp_password'],
    'from_email' => ['ESWASA_MAIL_FROM',      'site_mail_from_email'],
    'from_name'  => ['ESWASA_MAIL_FROM_NAME', 'site_mail_from_name'],
];

// Sender used when none is configured. It has to be on a domain this site is
// allowed to send for, or SPF/DMARC checks fail and the mail is dropped.
const ESWASA_MAIL_DEFAULT_FROM = 'no-reply@eswasa.co.sz';
const ESWASA_MAIL_DEFAULT_FROM_NAME = 'ESWASA Website';

// Canonical public address of the site, used for links inside emails.
const ESWASA_SITE_URL_DEFAULT = 'https://www.eswasa.co.sz';

// A dead SMTP server must not hold a request open for PHPMailer's default
// five minutes.
const ESWASA_SMTP_TIMEOUT = 15;

/**
 * The effective mail settings, with where each value came from.
 *
 * 'source' maps each field to 'env', 'settings' or 'default'; the admin page
 * uses it to show which fields the server environment is overriding.
 */
function eswasa_mail_settings(mysqli $conn): array
{
    $stored = [];
    try {
        $stored = pc_get_many($conn, array_column(ESWASA_MAIL_SETTING_KEYS, 1));
    } catch (Throwable $e) {
        error_log('Mail settings could not be read: ' . $e->getMessage());
    }

    $s = ['source' => []];
    foreach (ESWASA_MAIL_SETTING_KEYS as $field => [$env, $key]) {
        $value = getenv($env);
        if ($value !== false && $value !== '') {
            $s[$field] = (string)$value;
            $s['source'][$field] = 'env';
            continue;
        }
        $value = (string)($stored[$key] ?? '');
        // Passwords are taken exactly as typed; everything else is trimmed.
        $s[$field] = $field === 'password' ? $value : trim($value);
        $s['source'][$field] = $s[$field] !== '' ? 'settings' : 'default';
    }

    $s['secure'] = strtolower($s['secure']);
    if (!in_array($s['secure'], ['tls', 'ssl', 'none'], true)) {
        $s['secure'] = 'tls';
    }
    $s['port'] = (int)$s['port'];
    if ($s['port'] < 1 || $s['port'] > 65535) {
        $s['port'] = $s['secure'] === 'ssl' ? 465 : 587;
    }

    if (!filter_var($s['from_email'], FILTER_VALIDATE_EMAIL)) {
        // Most SMTP servers only accept mail from the mailbox that logged in,
        // so that is the natural default sender once SMTP is set up.
        $s['from_email'] = filter_var($s['username'], FILTER_VALIDATE_EMAIL)
            ? $s['username']
            : ESWASA_MAIL_DEFAULT_FROM;
    }
    if ($s['from_name'] === '') {
        $s['from_name'] = ESWASA_MAIL_DEFAULT_FROM_NAME;
    }

    $s['smtp'] = $s['host'] !== '';
    return $s;
}

/**
 * Absolute URL on the public site, for links inside emails.
 *
 * Production always uses the canonical address rather than the request's Host
 * header: the Host header is supplied by the visitor, and trusting it would
 * let anyone submitting a form plant a link to their own site in an email
 * that staff trust. ESWASA_SITE_URL overrides it (e.g. for a staging copy).
 *
 * On a developer's machine the links follow the request so they work on
 * Laragon — but only when the host is one that cannot be registered on the
 * internet: localhost, a .test/.localhost name, or a private IP address, or
 * when APP_ENV=development is set explicitly on the server. APP_ENV's own
 * auto-detection is not enough here: it also counts any host *beginning*
 * with "10." or "192.168." as local, and "10.attacker.example" begins with
 * "10.".
 */
function eswasa_site_url(string $path = ''): string
{
    $base = getenv('ESWASA_SITE_URL');
    if (!$base) {
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $base = eswasa_is_dev_host($host)
            ? eswasa_request_base_url($host)
            : ESWASA_SITE_URL_DEFAULT;
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/** True only for hosts that can safely be echoed into a link (see above). */
function eswasa_is_dev_host(string $host): bool
{
    if ($host === '' || !preg_match('/^(\[[0-9a-f:]+\]|[a-z0-9.-]+)(:\d{1,5})?$/', $host)) {
        return false;
    }
    if (getenv('APP_ENV') === 'development') {
        return true;
    }
    $bare = trim((string)preg_replace('/:\d{1,5}$/', '', $host), '[]');
    if ($bare === 'localhost' || preg_match('/\.(test|localhost)$/', $bare)) {
        return true;
    }
    // An IP literal in a private or loopback range; a real name such as
    // 10.example.com is not an IP literal and fails the first check.
    return filter_var($bare, FILTER_VALIDATE_IP) !== false
        && filter_var($bare, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
}

/** scheme://host/sub-folder of this request, for local development only. */
function eswasa_request_base_url(string $host): string
{
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $root  = realpath(__DIR__ . '/..');
    $doc   = realpath((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $sub   = ($root && $doc && strpos($root, $doc) === 0)
        ? str_replace('\\', '/', substr($root, strlen($doc)))
        : '';
    return ($https ? 'https://' : 'http://') . $host . $sub;
}

/**
 * Split a comma-, semicolon- or space-separated list into valid addresses.
 * Invalid entries are dropped; duplicates are removed.
 */
function eswasa_parse_emails(string $list): array
{
    $out = [];
    foreach (preg_split('/[\s,;]+/', $list, -1, PREG_SPLIT_NO_EMPTY) as $addr) {
        if (filter_var($addr, FILTER_VALIDATE_EMAIL)) {
            $out[strtolower($addr)] = $addr;
        }
    }
    return array_values($out);
}

/**
 * Send one HTML email. Never throws.
 *
 * $to   one address, a comma-separated list, or an array of addresses
 * $opts reply_to, reply_to_name — who a "Reply" in the inbox goes to
 *       context                 — short tag for the log, e.g. 'contact#12'
 *       text                    — plain-text body (derived from $html if absent)
 *       settings                — use these instead of the stored settings
 *                                 (the admin test button, before saving)
 *
 * Returns ['ok' => bool, 'transport' => 'smtp'|'mail', 'error' => ?string].
 */
function eswasa_send_mail(mysqli $conn, $to, string $subject, string $html, array $opts = []): array
{
    $settings   = $opts['settings'] ?? eswasa_mail_settings($conn);
    $recipients = is_array($to) ? eswasa_parse_emails(implode(',', $to)) : eswasa_parse_emails((string)$to);
    $transport  = $settings['smtp'] ? 'smtp' : 'mail';
    $subject    = trim((string)preg_replace('/[\r\n]+/', ' ', $subject));
    $error      = null;

    if (!$recipients) {
        $error = 'No valid recipient address.';
    } else {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->CharSet = \PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;

            if ($settings['smtp']) {
                $mail->isSMTP();
                $mail->Host    = $settings['host'];
                $mail->Port    = $settings['port'];
                $mail->Timeout = ESWASA_SMTP_TIMEOUT;
                if ($settings['secure'] === 'ssl') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($settings['secure'] === 'tls') {
                    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure  = '';
                    $mail->SMTPAutoTLS = false;
                }
                if ($settings['username'] !== '') {
                    $mail->SMTPAuth = true;
                    $mail->Username = $settings['username'];
                    $mail->Password = $settings['password'];
                }
            } else {
                $mail->isMail();
            }

            $mail->setFrom($settings['from_email'], $settings['from_name']);
            foreach ($recipients as $addr) {
                $mail->addAddress($addr);
            }
            $reply_to = trim((string)($opts['reply_to'] ?? ''));
            if ($reply_to !== '' && filter_var($reply_to, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($reply_to, trim((string)($opts['reply_to_name'] ?? '')));
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $opts['text'] ?? eswasa_html_to_text($html);
            $mail->send();
        } catch (Throwable $e) {
            $error = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
        }
    }

    eswasa_mail_log(
        $conn,
        isset($opts['context']) ? (string)$opts['context'] : null,
        $recipients ? implode(', ', $recipients) : (is_array($to) ? implode(', ', $to) : (string)$to),
        $subject,
        $transport,
        $error
    );

    return ['ok' => $error === null, 'transport' => $transport, 'error' => $error];
}

/**
 * Record a send attempt in mail_log. Best-effort: a missing table (migration
 * not yet run) must never break the form that triggered the email.
 */
function eswasa_mail_log(mysqli $conn, ?string $context, string $recipients, string $subject, string $transport, ?string $error): void
{
    $status = $error === null ? 'sent' : 'failed';
    if ($error !== null) {
        error_log('Email ' . $status . ' (' . ($context ?? '-') . ') to ' . $recipients . ': ' . $error);
    }
    try {
        $stmt = @$conn->prepare(
            'INSERT INTO mail_log (context, recipients, subject, transport, status, error)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            return;
        }
        $recipients = mb_substr($recipients, 0, 500);
        $subject    = mb_substr($subject, 0, 255);
        $error      = $error === null ? null : mb_substr($error, 0, 1000);
        $stmt->bind_param('ssssss', $context, $recipients, $subject, $transport, $status, $error);
        @$stmt->execute();
        $stmt->close();

        // The log is for diagnosing delivery, not an archive. Trim it now and
        // then rather than on every send.
        if (mt_rand(1, 50) === 1) {
            @$conn->query('DELETE FROM mail_log WHERE created_at < NOW() - INTERVAL 180 DAY');
        }
    } catch (Throwable $e) {
        error_log('mail_log write failed: ' . $e->getMessage());
    }
}

/** The most recent send attempts, newest first. Empty if the table is missing. */
function eswasa_mail_log_recent(mysqli $conn, int $limit = 25): array
{
    $rows = [];
    try {
        $res = @$conn->query(
            'SELECT context, recipients, subject, transport, status, error, created_at
               FROM mail_log ORDER BY id DESC LIMIT ' . max(1, min(200, $limit))
        );
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }
    } catch (Throwable $e) {
        // Table not migrated yet — the settings page says so.
    }
    return $rows;
}

/** Plain-text alternative for clients that don't render HTML. */
function eswasa_html_to_text(string $html): string
{
    $text = preg_replace('~<(br|/p|/tr|/h[1-6]|/div|/li)\b[^>]*>~i', "\n", $html);
    $text = preg_replace('~</t[dh]>~i', "\t", (string)$text);
    $text = html_entity_decode(strip_tags((string)$text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace("/[ \t]+\n/", "\n", $text);
    return trim((string)preg_replace("/\n{3,}/", "\n\n", (string)$text));
}

/**
 * Let the visitor go before a slow step (such as sending email) runs.
 *
 * Under PHP-FPM — how Plesk normally serves PHP — this sends the response,
 * redirect included, and the script carries on in the background, so a slow
 * or unreachable SMTP server never makes a visitor wait. Where that is not
 * available the script simply continues and the visitor waits as before.
 * The session is closed first: otherwise the page the visitor is redirected
 * to would block on the session lock until the email finished.
 */
function eswasa_finish_response_early(): void
{
    ignore_user_abort(true);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } elseif (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
    }
}

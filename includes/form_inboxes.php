<?php
/**
 * includes/form_inboxes.php — every public form that lands in an admin inbox,
 * in one list.
 *
 * Each entry says where a form's submissions are stored, which admin page
 * shows them, what counts as unread and who is emailed when one arrives. The
 * notification emails, the sidebar badges and the admin notification bell
 * all read from here, so adding a form means adding one entry rather than
 * touching each of those.
 *
 * The SQL fragments are fixed strings in this file — nothing from a request
 * is ever interpolated into them.
 */

if (defined('ESWASA_FORM_INBOXES_LOADED')) {
    return;
}
define('ESWASA_FORM_INBOXES_LOADED', true);

require_once __DIR__ . '/mailer.php';

// Who is emailed when a form has no recipient of its own and the "all forms"
// default is blank as well.
const ESWASA_NOTIFY_FALLBACK = 'info@eswasa.co.sz';
const ESWASA_NOTIFY_DEFAULT_KEY = 'site_notify_default_email';

/**
 * key => [
 *   label       one submission, e.g. "Training quote request"
 *   inbox       the admin inbox's name
 *   page        admin/pages/ file that lists them
 *   table       where they are stored
 *   where       which rows of that table belong to this inbox
 *   unread      what "not yet opened" means for this table
 *   who, what   SQL expressions summarising a row for the notification list
 *   notify_key  page_content key holding this form's recipients
 *   fallback    older page_content keys tried before the default (optional)
 *   icon        Font Awesome icon for the admin
 * ]
 */
function eswasa_inboxes(): array
{
    static $inboxes = null;
    if ($inboxes !== null) {
        return $inboxes;
    }

    $quote = function (string $source, string $label, string $inbox) {
        return [
            'label'      => $label,
            'inbox'      => $inbox,
            'page'       => 'qoute_' . $source . '.php',
            'table'      => 'eswasa_quote_requests',
            'where'      => "source = '" . $source . "'",
            'unread'     => "status = 'new'",
            'who'        => "COALESCE(NULLIF(contact_name, ''), NULLIF(organization, ''), NULLIF(contact_email, ''), 'Unknown')",
            'what'       => "COALESCE(NULLIF(organization, ''), '')",
            'notify_key' => 'site_notify_quote_' . $source . '_email',
            'icon'       => 'fa-file-invoice',
        ];
    };

    $inboxes = [
        'contact' => [
            'label'      => 'Contact message',
            'inbox'      => 'Contact Us',
            'page'       => 'contact_edit.php',
            'table'      => 'eswasa_contact_messages',
            'where'      => '1 = 1',
            'unread'     => "status = 'new'",
            'who'        => 'name',
            'what'       => 'subject',
            'notify_key' => 'site_contact_notify_email',
            'icon'       => 'fa-envelope',
        ],
        'feedback' => [
            'label'      => 'Customer feedback',
            'inbox'      => 'Customer Feedback',
            'page'       => 'customer_feedback.php',
            'table'      => 'eswasa_customer_feedback',
            'where'      => '1 = 1',
            'unread'     => 'is_read = 0',
            'who'        => "COALESCE(NULLIF(email, ''), 'Anonymous')",
            'what'       => "CONCAT_WS(' — ', NULLIF(feedback_type, ''), NULLIF(service, ''))",
            'notify_key' => 'site_notify_feedback_email',
            // The feedback page already had an address staff maintain; keep
            // honouring it for anyone who has not set a recipient here.
            'fallback'   => ['customer_feedback_fallback_email'],
            'icon'       => 'fa-comment-dots',
        ],
        'quote_training'      => $quote('training', 'Training quote request', 'Training Quote Requests'),
        'quote_certification' => $quote('certification', 'Certification quote request', 'Certification Quote Requests'),
        'quote_calibration'   => $quote('calibration', 'Calibration quote request', 'Calibration Quote Requests'),
        'quote_other'         => $quote('other', 'General quote request', 'General Quote Requests'),
    ];
    return $inboxes;
}

function eswasa_inbox(string $key): ?array
{
    return eswasa_inboxes()[$key] ?? null;
}

/**
 * The addresses notified about a new submission to one inbox: its own
 * recipients, else any older setting it inherited, else the all-forms
 * default, else info@eswasa.co.sz.
 */
function eswasa_notify_recipients(mysqli $conn, string $key): array
{
    $ib = eswasa_inbox($key);
    $keys = array_merge($ib ? [$ib['notify_key']] : [], $ib['fallback'] ?? [], [ESWASA_NOTIFY_DEFAULT_KEY]);
    $stored = [];
    try {
        $stored = pc_get_many($conn, $keys);
    } catch (Throwable $e) {
        error_log('Notification recipients could not be read: ' . $e->getMessage());
    }
    foreach ($keys as $k) {
        $list = eswasa_parse_emails((string)($stored[$k] ?? ''));
        if ($list) {
            return $list;
        }
    }
    return [ESWASA_NOTIFY_FALLBACK];
}

/**
 * Email staff about a submission that has just been saved. Never throws: the
 * submission is already in the database, and a mail problem must not turn
 * that into an error for the visitor.
 *
 * $summary  short text for the subject line, e.g. the message subject
 * $rows     label => value pairs for the body; empty values are left out
 * $opts     reply_to, reply_to_name — the submitter, so staff can just reply
 *           note — optional line shown above the details
 */
function eswasa_notify_submission(mysqli $conn, string $key, int $id, string $summary, array $rows, array $opts = []): array
{
    try {
        $ib = eswasa_inbox($key);
        if (!$ib) {
            return ['ok' => false, 'transport' => '', 'error' => 'Unknown inbox ' . $key];
        }
        $title   = 'New ' . lcfirst($ib['label']);
        $summary = trim((string)preg_replace('/\s+/', ' ', $summary));
        if (mb_strlen($summary) > 80) {
            $summary = mb_substr($summary, 0, 79) . '…';
        }
        $admin_url = $id > 0
            ? eswasa_site_url('admin/index.php?' . http_build_query(['page' => $ib['page'], 'view' => $id]))
            : eswasa_site_url('admin/index.php?' . http_build_query(['page' => $ib['page']]));

        $html = eswasa_notification_html($title, $rows, $admin_url, $ib['inbox'], $opts);
        return eswasa_send_mail(
            $conn,
            eswasa_notify_recipients($conn, $key),
            '[ESWASA] ' . $title . ($summary !== '' ? ': ' . $summary : ''),
            $html,
            [
                'reply_to'      => $opts['reply_to'] ?? null,
                'reply_to_name' => $opts['reply_to_name'] ?? null,
                'context'       => $key . '#' . $id,
            ]
        );
    } catch (Throwable $e) {
        error_log('Form notification failed (' . $key . '#' . $id . '): ' . $e->getMessage());
        return ['ok' => false, 'transport' => '', 'error' => $e->getMessage()];
    }
}

/** The body of a notification email: a heading, the details, a link to the admin. */
function eswasa_notification_html(string $title, array $rows, string $admin_url, string $inbox, array $opts = []): string
{
    $e = function ($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    };

    $table = '';
    foreach ($rows as $label => $value) {
        if (is_array($value)) {
            $value = implode(', ', array_map('strval', $value));
        }
        $value = trim((string)$value);
        if ($value === '') {
            continue;
        }
        $table .= '<tr>'
            . '<td style="padding:8px 12px 8px 0;color:#5b6180;width:34%;vertical-align:top;border-bottom:1px solid #eceef5;">' . $e($label) . '</td>'
            . '<td style="padding:8px 0;vertical-align:top;border-bottom:1px solid #eceef5;">' . nl2br($e($value)) . '</td>'
            . '</tr>';
    }

    $note = !empty($opts['note'])
        ? '<p style="margin:0 0 14px;">' . $e($opts['note']) . '</p>'
        : '';

    $reply = !empty($opts['reply_to']) && filter_var($opts['reply_to'], FILTER_VALIDATE_EMAIL)
        ? 'Reply to this email to answer ' . $e($opts['reply_to_name'] ?: $opts['reply_to']) . ' directly. '
        : '';

    return '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.45;color:#1f2340;max-width:640px;">'
        . '<div style="background:#2B3388;color:#ffffff;padding:16px 20px;border-radius:6px 6px 0 0;">'
        .   '<div style="font-size:12px;letter-spacing:.05em;text-transform:uppercase;opacity:.85;">ESWASA website</div>'
        .   '<div style="font-size:20px;font-weight:bold;margin-top:4px;">' . $e($title) . '</div>'
        . '</div>'
        . '<div style="border:1px solid #dfe1ee;border-top:0;padding:18px 20px;border-radius:0 0 6px 6px;">'
        .   $note
        .   '<table cellpadding="0" cellspacing="0" style="width:100%;border-collapse:collapse;font-size:15px;">' . $table . '</table>'
        .   '<p style="margin:20px 0 0;">'
        .     '<a href="' . $e($admin_url) . '" style="display:inline-block;background:#2B3388;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:5px;font-weight:bold;">Open in the admin</a>'
        .   '</p>'
        .   '<p style="margin:18px 0 0;font-size:12px;color:#8a8fa8;">'
        .     'Received ' . $e(date('j F Y \a\t H:i')) . '. ' . $reply
        .     'It is also saved under ' . $e($inbox) . ' in the admin.'
        .   '</p>'
        . '</div>'
        . '</div>';
}

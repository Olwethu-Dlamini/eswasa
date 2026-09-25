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
 *   viewed      the SET clause that marks one as opened (read_at/read_by are
 *               set alongside it)
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
            'viewed'     => "status = 'viewed'",
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
            'viewed'     => "status = 'read'",
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
            'viewed'     => 'is_read = 1',
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
        'training_application' => [
            'label'      => 'Training application',
            'inbox'      => 'Training Applications',
            'page'       => 'training_applications.php',
            'table'      => 'eswasa_training_applications',
            'where'      => '1 = 1',
            'unread'     => "status = 'new'",
            'viewed'     => "status = 'viewed'",
            'who'        => 'full_name',
            'what'       => "CONCAT_WS(' — ', NULLIF(training_code, ''), NULLIF(intake_label, ''))",
            'notify_key' => 'site_notify_training_app_email',
            'icon'       => 'fa-user-graduate',
        ],
    ];
    return $inboxes;
}

function eswasa_inbox(string $key): ?array
{
    return eswasa_inboxes()[$key] ?? null;
}

/**
 * Unread submissions per inbox, e.g. ['contact' => 2, 'feedback' => 0, ...].
 * An inbox whose table is missing (migration not run) counts as 0 rather
 * than breaking the page that asked.
 */
function eswasa_inbox_counts(mysqli $conn): array
{
    $counts = [];
    foreach (eswasa_inboxes() as $key => $ib) {
        $counts[$key] = 0;
        try {
            $res = @$conn->query(
                "SELECT COUNT(*) AS c FROM {$ib['table']} WHERE ({$ib['where']}) AND ({$ib['unread']})"
            );
            if ($res) {
                $counts[$key] = (int)($res->fetch_assoc()['c'] ?? 0);
            }
        } catch (Throwable $e) {
            // leave it at 0
        }
    }
    return $counts;
}

/**
 * The newest unread submissions across every inbox, newest first — what the
 * admin's notification bell lists. 'age' is seconds since it arrived,
 * worked out by the database so it is right whatever time zone PHP or the
 * browser is in. 'url' is relative to admin/.
 */
function eswasa_inbox_latest(mysqli $conn, int $limit = 10): array
{
    $limit = max(1, min(50, $limit));
    $items = [];
    foreach (eswasa_inboxes() as $key => $ib) {
        try {
            $res = @$conn->query(
                "SELECT id, {$ib['who']} AS who, {$ib['what']} AS what, created_at,
                        TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age
                   FROM {$ib['table']}
                  WHERE ({$ib['where']}) AND ({$ib['unread']})
               ORDER BY created_at DESC, id DESC
                  LIMIT {$limit}"
            );
            if (!$res) {
                continue;
            }
            while ($row = $res->fetch_assoc()) {
                $items[] = [
                    'key'   => $key . ':' . (int)$row['id'],
                    'inbox' => $key,
                    'id'    => (int)$row['id'],
                    'label' => $ib['label'],
                    'icon'  => $ib['icon'],
                    'who'   => (string)$row['who'],
                    'what'  => (string)$row['what'],
                    'age'   => max(0, (int)$row['age']),
                    'url'   => 'index.php?' . http_build_query(['page' => $ib['page'], 'view' => (int)$row['id']]),
                ];
            }
        } catch (Throwable $e) {
            // table missing — nothing to list from it
        }
    }
    usort($items, function ($a, $b) {
        return $a['age'] <=> $b['age'];
    });
    return array_slice($items, 0, $limit);
}

/**
 * Mark one submission as opened, if it has not been already, recording who
 * opened it and when. Returns true when this call changed it.
 *
 * Only an unread row is touched, so opening something already in progress
 * or closed never moves it back to "viewed". read_at/read_by keep the
 * *first* opening: a submission set back to New and opened again still says
 * who saw it first. The status and read_at are set in the same statement on
 * purpose: see section 3 of admin/sql/upgrade_2026_09_25.sql for why that
 * ordering matters on the production database.
 */
function eswasa_mark_viewed(mysqli $conn, string $key, int $id, string $by): bool
{
    $ib = eswasa_inbox($key);
    if (!$ib || $id < 1) {
        return false;
    }
    try {
        $stmt = $conn->prepare(
            "UPDATE {$ib['table']} SET {$ib['viewed']}, read_at = COALESCE(read_at, NOW()), read_by = COALESCE(read_by, ?)
              WHERE id = ? AND ({$ib['where']}) AND ({$ib['unread']})"
        );
        if (!$stmt) {
            return false;
        }
        $by = mb_substr($by, 0, 50);
        $stmt->bind_param('si', $by, $id);
        $stmt->execute();
        $changed = $stmt->affected_rows > 0;
        $stmt->close();
        return $changed;
    } catch (Throwable $e) {
        error_log('mark viewed failed (' . $key . '#' . $id . '): ' . $e->getMessage());
        return false;
    }
}

/**
 * Whether one submission is still unread: true/false, or null when it does
 * not exist or the database could not be asked.
 */
function eswasa_is_unread(mysqli $conn, string $key, int $id): ?bool
{
    $ib = eswasa_inbox($key);
    if (!$ib) {
        return null;
    }
    try {
        $stmt = $conn->prepare(
            "SELECT ({$ib['unread']}) AS u FROM {$ib['table']} WHERE id = ? AND ({$ib['where']})"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? (bool)$row['u'] : null;
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * When, and by whom, a submission was first opened — null if never. Read
 * back from the database rather than taken from PHP's clock, so it matches
 * what the inbox pages show (PHP and MySQL may not share a time zone).
 */
function eswasa_viewed_info(mysqli $conn, string $key, int $id): ?array
{
    $ib = eswasa_inbox($key);
    if (!$ib) {
        return null;
    }
    try {
        $stmt = $conn->prepare("SELECT read_at, read_by FROM {$ib['table']} WHERE id = ? AND ({$ib['where']})");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return ($row && $row['read_at']) ? $row : null;
    } catch (Throwable $e) {
        return null;
    }
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

        // The database's clock, not PHP's: the admin lists submissions by
        // their created_at, and PHP and MySQL need not share a time zone.
        $received = null;
        try {
            if ($res = @$conn->query('SELECT NOW()')) {
                $received = strtotime((string)$res->fetch_row()[0]) ?: null;
            }
        } catch (Throwable $e) {
        }
        $opts['received'] = $received ?? time();

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
        .     'Received ' . $e(date('j F Y \a\t H:i', (int)($opts['received'] ?? time()))) . '. ' . $reply
        .     'It is also saved under ' . $e($inbox) . ' in the admin.'
        .   '</p>'
        . '</div>'
        . '</div>';
}

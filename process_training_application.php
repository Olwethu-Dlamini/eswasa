<?php
/**
 * process_training_application.php — receives the "Apply" form on the
 * training calendar (training-calendar.php).
 *
 * Before this the form had no action and its inputs had no names. A script
 * showed "Thank you … your application has been submitted" and then threw
 * the application away: nothing was stored and nobody was told. Every
 * applicant since the calendar went live was turned away without knowing it.
 *
 * Stores the application in eswasa_training_applications (listed in the admin
 * under Training › Applications), redirects back to the calendar with
 * ?applied=1 (or 0 plus a message in the session), then emails staff.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require_once __DIR__ . '/includes/form_inboxes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$field = function (string $key, int $max = 255): string {
    $v = pc_strip_text(is_string($_POST[$key] ?? null) ? $_POST[$key] : '');
    return mb_substr($v, 0, $max);
};

$full_name    = $field('full_name');
$email        = $field('email');
$phone        = $field('phone', 50);
$company      = $field('company');
$position     = $field('position');
$comments     = $field('comments', 5000);
$session_id   = (int)($_POST['session_id'] ?? 0);
$intake_start = $field('intake_start', 10);
$intake_label = $field('intake_label', 128);
$consent      = !empty($_POST['consent']);

$back = function (bool $ok, string $error = '') {
    if (!$ok) {
        $_SESSION['training_apply_error'] = $error;
    }
    header('Location: training-calendar.php?applied=' . ($ok ? '1' : '0') . '#apply-result');
};

// ── Validate ──────────────────────────────────────────────────
// The browser enforces most of this already; this is for everything else.
// Phone follows the contact form: count digits, not characters, so local
// numbers such as "7612 3456" are accepted.
$phone_digits = preg_replace('/\D+/', '', $phone);
$error = '';
if ($full_name === '') {
    $error = 'Please enter your full name.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
} elseif (strlen((string)$phone_digits) < 7 || strlen((string)$phone_digits) > 15) {
    $error = 'Please enter a valid phone number (at least 7 digits).';
} elseif (!$consent) {
    $error = 'Please tick the box to agree to the training policies.';
}

// The training is looked up rather than taken from the form, so what staff
// see is what is on the calendar, not whatever a request claimed.
$training = null;
if ($error === '') {
    $stmt = $conn->prepare('SELECT id, code, title FROM training_sessions WHERE id = ? AND is_active = 1');
    $stmt->bind_param('i', $session_id);
    $stmt->execute();
    $training = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$training) {
        $error = 'That training is no longer open for applications. Please choose another date or contact us.';
    }
}

if ($error !== '') {
    $back(false, $error);
    exit;
}

// Same for the intake. If its dates were edited after the page loaded, keep
// the label the applicant saw, so the application still says what they chose.
$start = DateTime::createFromFormat('!Y-m-d', $intake_start);
$intake_start = ($start && $start->format('Y-m-d') === $intake_start) ? $intake_start : null;
if ($intake_start !== null) {
    $stmt = $conn->prepare('SELECT label FROM training_intakes WHERE session_id = ? AND start_date = ? ORDER BY sort_order, id LIMIT 1');
    $stmt->bind_param('is', $session_id, $intake_start);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($row) {
        $intake_label = (string)$row['label'];
    }
}

// ── Insert ────────────────────────────────────────────────────
// Wrapped because mysqli throws from PHP 8.1: a database that refuses the
// insert (the migration not run yet, say) must still send the applicant back
// with a message, not a server error page that loses what they typed.
$code   = (string)$training['code'];
$title  = (string)$training['title'];
$new_id = 0;
try {
    $stmt = $conn->prepare(
        'INSERT INTO eswasa_training_applications
            (session_id, training_code, training_title, intake_start, intake_label,
             full_name, email, phone, company, position, comments)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    if (!$stmt) {
        throw new RuntimeException((string)$conn->error);
    }
    $company_db  = $company !== '' ? $company : null;
    $position_db = $position !== '' ? $position : null;
    $comments_db = $comments !== '' ? $comments : null;
    $stmt->bind_param(
        'issssssssss',
        $session_id, $code, $title, $intake_start, $intake_label,
        $full_name, $email, $phone, $company_db, $position_db, $comments_db
    );
    if (!$stmt->execute()) {
        throw new RuntimeException((string)$stmt->error);
    }
    $new_id = (int)$conn->insert_id;
    $stmt->close();
} catch (Throwable $e) {
    error_log('Training application could not be saved: ' . $e->getMessage());
    $back(false, 'Sorry — we could not save your application. Please try again, or email us at info@eswasa.co.sz.');
    exit;
}

$back(true);

// ── Notify staff ──────────────────────────────────────────────
// After the redirect has gone out, so the applicant never waits on the mail
// server. Recipients: Site Settings › Form Notifications › Training
// Applications.
eswasa_finish_response_early();
eswasa_notify_submission(
    $conn,
    'training_application',
    $new_id,
    $code . ' — ' . $full_name,
    [
        'Training'     => $code . ' — ' . $title,
        'Intake'       => $intake_label,
        'Name'         => $full_name,
        'Email'        => $email,
        'Phone'        => $phone,
        'Organisation' => $company,
        'Position'     => $position,
        'Comments'     => $comments,
    ],
    ['reply_to' => $email, 'reply_to_name' => $full_name]
);
exit;

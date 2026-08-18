<?php
// Public endpoint for the three quote-request forms
// (qoute_training.php, qoute_certification.php, qoute_calibration.php).
// Stores the submission in eswasa_quote_requests; redirects back to the
// referring form with ?quote_sent=1 (or 0 on failure).

declare(strict_types=1);

require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

// ── Infer source from the referring page ────────────────────────
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$source = 'other';
if (stripos($referer, 'qoute_training') !== false || stripos($referer, 'quote_training') !== false) {
    $source = 'training';
} elseif (stripos($referer, 'qoute_certification') !== false || stripos($referer, 'quote_certification') !== false) {
    $source = 'certification';
} elseif (stripos($referer, 'qoute_calibration') !== false || stripos($referer, 'quote_calibration') !== false) {
    $source = 'calibration';
}

// Allow forms to override via hidden quote_source field if added later
if (!empty($_POST['quote_source'])) {
    $forced = strtolower(trim((string)$_POST['quote_source']));
    if (in_array($forced, ['training','certification','calibration','other'], true)) {
        $source = $forced;
    }
}

// ── Pull common contact fields (try a few aliases) ─────────────
$pick = function (array $keys) {
    foreach ($keys as $k) {
        if (isset($_POST[$k]) && trim((string)$_POST[$k]) !== '') {
            return trim((string)$_POST[$k]);
        }
    }
    return null;
};

// Field-name aliases across the five quote forms. Note "full_names" (plural):
// the individual training form uses it, and its absence here meant every
// submission from that form stored contact_name = NULL, so the admin inbox
// showed a dash instead of the requester's name. Verified against the actual
// input names in all five forms.
// See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A3.
$contact_name = $pick(['contact_person', 'contactName', 'contact_name', 'full_names', 'full_name', 'name']);
$contact_email = $pick(['email', 'contact_email']);
$contact_phone = $pick(['phone', 'contact_phone', 'tel']);
$organization  = $pick(['organisation_name', 'organization_name', 'company_name', 'company', 'organisation']);

if ($contact_name) $contact_name = pc_strip_text($contact_name);
if ($contact_phone) $contact_phone = pc_strip_text($contact_phone);
if ($organization)  $organization  = pc_strip_text($organization);
if ($contact_email) {
    $contact_email = filter_var(trim($contact_email), FILTER_SANITIZE_EMAIL);
    if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $contact_email = pc_strip_text($contact_email); // store as-is but cleaned
    }
}

// ── Capture full form payload as JSON (everything submitted) ───
$safe_post = [];
foreach ($_POST as $k => $v) {
    if (is_array($v)) {
        $safe_post[$k] = array_map(fn ($x) => is_scalar($x) ? pc_strip_text((string)$x) : '', $v);
    } else {
        $safe_post[$k] = pc_strip_text((string)$v);
    }
}
$raw_form = json_encode($safe_post, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// ── Handle file attachments (documents[]) ──────────────────────
//
// PDF only. The previous rule accepted nine extensions and trusted the
// filename alone, which meant any file could be uploaded by renaming it.
// Anything rejected was skipped with a bare `continue`, so a submitter saw a
// success page and never learned their attachment had been discarded — every
// rejection below now records a reason that is reported back on the form.
// Limits: 10 MB per file, 5 files per submission.
// See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, items A3/A4.
const QUOTE_MAX_FILES     = 5;
const QUOTE_MAX_FILE_SIZE = 10485760; // 10 MB

$attachments_paths = [];
$attachment_errors = [];

if (!empty($_FILES['documents']) && is_array($_FILES['documents']['name'])) {
    $upload_dir_fs = __DIR__ . '/admin/uploads/quotes/';
    if (!is_dir($upload_dir_fs)) @mkdir($upload_dir_fs, 0755, true);

    $n = count($_FILES['documents']['name']);
    $accepted = 0;

    for ($i = 0; $i < $n; $i++) {
        $err  = $_FILES['documents']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        $orig = basename((string)($_FILES['documents']['name'][$i] ?? ''));

        if ($err === UPLOAD_ERR_NO_FILE || $orig === '') {
            continue; // empty slot, not an error
        }
        $label = $orig !== '' ? '"' . $orig . '"' : 'a file';

        if ($accepted >= QUOTE_MAX_FILES) {
            $attachment_errors[] = $label . ' was not attached (limit is ' . QUOTE_MAX_FILES . ' files).';
            continue;
        }
        if ($err !== UPLOAD_ERR_OK) {
            $attachment_errors[] = $label . ' failed to upload.';
            continue;
        }

        $size = (int)($_FILES['documents']['size'][$i] ?? 0);
        if ($size === 0) {
            $attachment_errors[] = $label . ' is empty.';
            continue;
        }
        if ($size > QUOTE_MAX_FILE_SIZE) {
            $attachment_errors[] = $label . ' is larger than the 10 MB limit.';
            continue;
        }

        $tmp = $_FILES['documents']['tmp_name'][$i];
        if (!is_uploaded_file($tmp)) {
            $attachment_errors[] = $label . ' could not be verified.';
            continue;
        }

        // Check the file's actual contents, not its extension.
        $mime = '';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = (string)finfo_file($finfo, $tmp);
            finfo_close($finfo);
        }
        if ($mime !== '' && !in_array(strtolower($mime), ['application/pdf', 'application/x-pdf'], true)) {
            $attachment_errors[] = $label . ' is not a PDF — only PDF files can be attached.';
            continue;
        }
        if (strtolower(pathinfo($orig, PATHINFO_EXTENSION)) !== 'pdf') {
            $attachment_errors[] = $label . ' is not a PDF — only PDF files can be attached.';
            continue;
        }

        $new_name = uniqid('quote_', true) . '.pdf';
        if (move_uploaded_file($tmp, $upload_dir_fs . $new_name)) {
            $attachments_paths[] = 'admin/uploads/quotes/' . $new_name;
            $accepted++;
        } else {
            $attachment_errors[] = $label . ' could not be saved.';
        }
    }
}
$attachments_db = $attachments_paths ? json_encode($attachments_paths) : null;

// ── Insert ─────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO eswasa_quote_requests
        (source, contact_name, contact_email, contact_phone, organization, raw_form, attachments)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    'sssssss',
    $source,
    $contact_name,
    $contact_email,
    $contact_phone,
    $organization,
    $raw_form,
    $attachments_db
);
$ok = $stmt->execute();
$new_id = $ok ? (int)$conn->insert_id : 0;
$stmt->close();

// ── Redirect back ──────────────────────────────────────────────
//
// Resolve the return page from the (validated) source rather than redirecting
// to a raw HTTP_REFERER, which is attacker-controllable and would let this
// endpoint be used as an open redirect.
$return_pages = [
    'training'      => 'qoute_training.php',
    'certification' => 'qoute_certification.php',
    'calibration'   => 'qoute_calibration.php',
    'other'         => 'qoute.php',
];
$back = $return_pages[$source] ?? 'qoute.php';

$query = ['quote_sent' => $ok ? '1' : '0', 'ref' => $source];

// Carry any attachment rejections back so the submitter is told what happened
// instead of being shown an unqualified success page. Kept in the session
// rather than the URL: the messages contain user-supplied filenames.
if ($attachment_errors) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['quote_attachment_errors'] = $attachment_errors;
    $query['attach_err'] = count($attachment_errors);
}

header('Location: ' . $back . '?' . http_build_query($query));
exit;

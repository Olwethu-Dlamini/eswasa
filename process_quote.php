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

$contact_name = $pick(['contact_person', 'contactName', 'contact_name', 'full_name', 'name']);
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
$attachments_paths = [];
if (!empty($_FILES['documents']) && is_array($_FILES['documents']['name'])) {
    $upload_dir_fs = __DIR__ . '/admin/uploads/quotes/';
    if (!is_dir($upload_dir_fs)) @mkdir($upload_dir_fs, 0755, true);

    $allowed_ext = ['pdf','doc','docx','jpg','jpeg','png','webp','xls','xlsx'];
    $max_per_file = 10 * 1024 * 1024; // 10MB per file

    $n = count($_FILES['documents']['name']);
    for ($i = 0; $i < $n; $i++) {
        if (($_FILES['documents']['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
        $size = (int)($_FILES['documents']['size'][$i] ?? 0);
        if ($size === 0 || $size > $max_per_file) continue;

        $orig = basename((string)$_FILES['documents']['name'][$i]);
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) continue;

        $new_name = uniqid('quote_', true) . '.' . $ext;
        if (move_uploaded_file($_FILES['documents']['tmp_name'][$i], $upload_dir_fs . $new_name)) {
            $attachments_paths[] = 'admin/uploads/quotes/' . $new_name;
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
$back = $referer ?: '/';
$sep = (strpos($back, '?') === false) ? '?' : '&';
$back .= $sep . 'quote_sent=' . ($ok ? '1' : '0') . '&ref=' . urlencode($source);

header('Location: ' . $back);
exit;

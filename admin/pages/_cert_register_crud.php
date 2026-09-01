<?php
/**
 * Handlers and loading for the certification status registers.
 *
 * One table, `certification_register`, holds the entries for all three
 * registers (Management Systems, Product, Ingelo) across all three statuses
 * (suspended, withdrawn, reduced). The scheme being edited comes from ?scheme=
 * and is carried through every redirect so the editor stays where they were.
 *
 * Include from cert_status_edit.php BEFORE any output. Afterwards
 * $reg_scheme, $reg_by_status, $reg_edit and $reg_is_new are ready for the UI.
 *
 * See docs/superpowers/specs/2026-09-01-cms-batch-d-design.md.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';

$REG_SCHEMES = [
    'ms'      => 'Management Systems',
    'product' => 'Product',
    'ingelo'  => 'Ingelo',
];
$REG_STATUSES = [
    'suspended' => 'Currently Suspended',
    'withdrawn' => 'Withdrawn / Cancelled',
    'reduced'   => 'Reduced Scope',
];

$reg_scheme = $_GET['scheme'] ?? ($_POST['reg_scheme'] ?? 'ms');
if (!isset($REG_SCHEMES[$reg_scheme])) $reg_scheme = 'ms';

$reg_self = 'index.php?page=cert_status_edit.php&scheme=' . urlencode($reg_scheme);

// ── POST: create / update an entry ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_reg'])) {
    $rid       = !empty($_POST['reg_id']) ? (int)$_POST['reg_id'] : null;
    $status    = $_POST['reg_status'] ?? '';
    $client    = pc_strip_text($_POST['reg_client_name'] ?? '');
    $cert_no   = pc_strip_text($_POST['reg_cert_no'] ?? '');
    $scope     = pc_strip_text($_POST['reg_scope'] ?? '');
    $effective = trim((string)($_POST['reg_effective_date'] ?? ''));
    $note      = pc_strip_text($_POST['reg_reason_note'] ?? '');
    $sort      = (int)($_POST['reg_sort_order'] ?? 0);
    $active    = !empty($_POST['reg_is_active']) ? 1 : 0;
    $logo      = pc_strip_text($_POST['reg_existing_logo'] ?? '');

    $errors = [];
    if (!isset($REG_STATUSES[$status])) $errors[] = 'Status must be one of the three register sections.';
    if ($client === '')  $errors[] = 'Client name is required.';
    if ($cert_no === '') $errors[] = 'Certificate number is required.';
    if ($scope === '')   $errors[] = 'Standard / scope is required.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $effective)) {
        $errors[] = 'The effective date must be a real date (YYYY-MM-DD).';
    }

    // Prefer the cropper's base64 payload; fall back to a raw file upload
    // (e.g. SVG logos the cropper passes through untouched).
    $up = pc_save_base64_image($_POST['reg_logo_cropped'] ?? '', ADMIN_ROOT . '/uploads/register/', 'reg');
    if (!is_string($up)) {
        $up = pc_upload_image('reg_logo_file', ADMIN_ROOT . '/uploads/register/', 'reg');
    }
    if ($up === false) {
        $errors[] = 'Logo upload failed (check file type — JPG/PNG/WebP/SVG/GIF — and size under 5 MB).';
    } elseif ($up) {
        $logo = $up;
    }

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: ' . $reg_self . ($rid ? '&edit_reg=' . $rid : '&new_reg=1&status=' . urlencode($status)));
        exit;
    }

    $logo_for_db = $logo !== '' ? $logo : null;
    $note_for_db = $note !== '' ? $note : null;

    if ($rid) {
        // Scoped by scheme so a crafted id can't reach another register's rows.
        $stmt = $conn->prepare('UPDATE certification_register SET status = ?, client_name = ?, logo_path = ?, cert_no = ?, scope = ?, effective_date = ?, reason_note = ?, sort_order = ?, is_active = ? WHERE id = ? AND scheme = ?');
        $stmt->bind_param('sssssssiiis', $status, $client, $logo_for_db, $cert_no, $scope, $effective, $note_for_db, $sort, $active, $rid, $reg_scheme);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Register entry updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO certification_register (scheme, status, client_name, logo_path, cert_no, scope, effective_date, reason_note, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssssssii', $reg_scheme, $status, $client, $logo_for_db, $cert_no, $scope, $effective, $note_for_db, $sort, $active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Register entry added.');
    }
    header('Location: ' . $reg_self);
    exit;
}

// ── GET: toggle / delete ──────────────────────────────────────
if (isset($_GET['toggle_reg'])) {
    $rid = (int)$_GET['toggle_reg'];
    $stmt = $conn->prepare('UPDATE certification_register SET is_active = 1 - is_active WHERE id = ? AND scheme = ?');
    $stmt->bind_param('is', $rid, $reg_scheme);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Published state toggled.');
    header('Location: ' . $reg_self);
    exit;
}
if (isset($_GET['delete_reg'])) {
    $rid = (int)$_GET['delete_reg'];
    $sel = $conn->prepare('SELECT logo_path FROM certification_register WHERE id = ? AND scheme = ?');
    $sel->bind_param('is', $rid, $reg_scheme);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($row && !empty($row['logo_path']) && strpos($row['logo_path'], 'admin/uploads/register/') === 0) {
        $fs = dirname(ADMIN_ROOT) . '/' . $row['logo_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM certification_register WHERE id = ? AND scheme = ?');
    $stmt->bind_param('is', $rid, $reg_scheme);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Register entry deleted.');
    header('Location: ' . $reg_self);
    exit;
}

// ── Load ──────────────────────────────────────────────────────
$reg_by_status = ['suspended' => [], 'withdrawn' => [], 'reduced' => []];
$stmt = $conn->prepare('SELECT * FROM certification_register WHERE scheme = ? ORDER BY sort_order ASC, effective_date DESC, id ASC');
$stmt->bind_param('s', $reg_scheme);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    if (isset($reg_by_status[$row['status']])) $reg_by_status[$row['status']][] = $row;
}
$stmt->close();
$reg_total = array_sum(array_map('count', $reg_by_status));

$reg_edit   = null;
$reg_is_new = isset($_GET['new_reg']);
if (isset($_GET['edit_reg'])) {
    $stmt = $conn->prepare('SELECT * FROM certification_register WHERE id = ? AND scheme = ?');
    $reid = (int)$_GET['edit_reg'];
    $stmt->bind_param('is', $reid, $reg_scheme);
    $stmt->execute();
    $reg_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

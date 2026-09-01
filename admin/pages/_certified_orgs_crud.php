<?php
/**
 * Shared CRUD for the certified-company logo grids.
 *
 * One table, `certified_organisations`, backs three public grids that differ
 * only in which columns they show:
 *
 *   ms       managementsystems.php  logo + name + standard
 *   product  product.php            logo + name + product + standard
 *   ingelo   ingelo.php             logo + name + product   (standard optional)
 *
 * Rather than three near-identical tables and three copies of this code, the
 * rows carry a `scheme` column and each admin page includes this partial with
 * its own scheme. See docs/superpowers/specs/2026-09-01-cms-batch-d-design.md.
 *
 * Include BEFORE any output, having set:
 *   $CO_SCHEME  'ms' | 'product' | 'ingelo'
 *   $CO_PAGE    the admin page filename, e.g. 'product.php'
 *   $CO_NOUN    singular label for messages/buttons, e.g. 'producer'
 *
 * Afterwards $co_rows, $co_edit and $co_is_new are ready for _certified_orgs_ui.php.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';

$CO_SCHEME = $CO_SCHEME ?? 'ms';
$CO_PAGE   = $CO_PAGE   ?? 'managementsystems.php';
$CO_NOUN   = $CO_NOUN   ?? 'organisation';

// Which of the two descriptive columns this scheme uses, and whether they are
// mandatory. Drives both the validation below and the fields in the UI partial.
$CO_FIELDS = [
    'ms'      => ['product' => null,       'standard' => 'required'],
    'product' => ['product' => 'required', 'standard' => 'required'],
    'ingelo'  => ['product' => 'required', 'standard' => 'optional'],
][$CO_SCHEME] ?? ['product' => null, 'standard' => 'required'];

$co_self = 'index.php?page=' . $CO_PAGE;

// ── POST: create / update ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_org'])) {
    $id         = !empty($_POST['org_id']) ? (int)$_POST['org_id'] : null;
    $name       = pc_strip_text($_POST['org_name'] ?? '');
    $standard   = pc_strip_text($_POST['org_standard'] ?? '');
    $product    = pc_strip_text($_POST['org_product'] ?? '');
    $sort_order = (int)($_POST['org_sort_order'] ?? 0);
    $is_active  = !empty($_POST['org_is_active']) ? 1 : 0;
    $existing   = pc_strip_text($_POST['org_existing_logo'] ?? '');

    $errors = [];
    if ($name === '') $errors[] = ucfirst($CO_NOUN) . ' name is required.';
    if ($CO_FIELDS['standard'] === 'required' && $standard === '') $errors[] = 'Standard is required.';
    if ($CO_FIELDS['product']  === 'required' && $product  === '') $errors[] = 'Product is required.';

    $logo_path = $existing;  // default: keep current
    // Prefer the cropper's base64 payload; fall back to a raw file upload
    // (e.g. SVG logos the cropper passes through untouched).
    $up = pc_save_base64_image($_POST['org_logo_cropped'] ?? '', ADMIN_ROOT . '/uploads/orgs/', 'org');
    if (!is_string($up)) {
        $up = pc_upload_image('org_logo_file', ADMIN_ROOT . '/uploads/orgs/', 'org');
    }
    if ($up === false) {
        $errors[] = 'Logo upload failed (check file type — JPG/PNG/WebP/SVG/GIF — and size under 5 MB).';
    } elseif ($up) {
        $logo_path = $up;
    }

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: ' . $co_self . ($id ? '&edit_org=' . $id : '&new_org=1'));
        exit;
    }

    $logo_for_db    = $logo_path !== '' ? $logo_path : null;
    $product_for_db = ($CO_FIELDS['product'] !== null && $product !== '') ? $product : null;

    if ($id) {
        // Scoped by scheme so a crafted id can't reach another page's rows.
        $stmt = $conn->prepare('UPDATE certified_organisations SET name = ?, standard = ?, product = ?, logo_path = ?, sort_order = ?, is_active = ? WHERE id = ? AND scheme = ?');
        $stmt->bind_param('ssssiiis', $name, $standard, $product_for_db, $logo_for_db, $sort_order, $is_active, $id, $CO_SCHEME);
        $stmt->execute();
        $stmt->close();
        set_flash('success', ucfirst($CO_NOUN) . ' updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO certified_organisations (scheme, name, standard, product, logo_path, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('sssssii', $CO_SCHEME, $name, $standard, $product_for_db, $logo_for_db, $sort_order, $is_active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', ucfirst($CO_NOUN) . ' added.');
    }
    header('Location: ' . $co_self);
    exit;
}

// ── GET: quick toggle is_active ───────────────────────────────
if (isset($_GET['toggle_org'])) {
    $id = (int)$_GET['toggle_org'];
    $stmt = $conn->prepare('UPDATE certified_organisations SET is_active = 1 - is_active WHERE id = ? AND scheme = ?');
    $stmt->bind_param('is', $id, $CO_SCHEME);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: ' . $co_self);
    exit;
}

// ── GET: delete ───────────────────────────────────────────────
if (isset($_GET['delete_org'])) {
    $id = (int)$_GET['delete_org'];
    // Remove the uploaded logo file if it lives under admin/uploads/orgs/
    $sel = $conn->prepare('SELECT logo_path FROM certified_organisations WHERE id = ? AND scheme = ?');
    $sel->bind_param('is', $id, $CO_SCHEME);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($row && !empty($row['logo_path']) && strpos($row['logo_path'], 'admin/uploads/orgs/') === 0) {
        $fs = dirname(ADMIN_ROOT) . '/' . $row['logo_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM certified_organisations WHERE id = ? AND scheme = ?');
    $stmt->bind_param('is', $id, $CO_SCHEME);
    $stmt->execute();
    $stmt->close();
    set_flash('success', ucfirst($CO_NOUN) . ' deleted.');
    header('Location: ' . $co_self);
    exit;
}

// ── Load ──────────────────────────────────────────────────────
$stmt = $conn->prepare('SELECT * FROM certified_organisations WHERE scheme = ? ORDER BY sort_order ASC, id ASC');
$stmt->bind_param('s', $CO_SCHEME);
$stmt->execute();
$co_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$co_edit   = null;
$co_is_new = isset($_GET['new_org']);
if (isset($_GET['edit_org'])) {
    $stmt = $conn->prepare('SELECT * FROM certified_organisations WHERE id = ? AND scheme = ?');
    $eid = (int)$_GET['edit_org'];
    $stmt->bind_param('is', $eid, $CO_SCHEME);
    $stmt->execute();
    $co_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

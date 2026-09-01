<?php
/**
 * Handlers and loading for one logo strip. Include BEFORE any output, having
 * set $LL_KEY to a key from $LOGO_LISTS. Afterwards $ll_rows, $ll_edit and
 * $ll_is_new are ready for _logo_list_ui.php.
 *
 * The query-string parameters are namespaced with the list key so two strips
 * on one admin page (About Us has affiliations and accreditation) can be
 * edited independently without one opening the other's form.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/logo_lists.php';

if (!isset($LOGO_LISTS[$LL_KEY ?? ''])) {
    exit('_logo_list_crud.php included without a valid $LL_KEY.');
}
$LL_CFG  = $LOGO_LISTS[$LL_KEY];
$LL_NOUN = $LL_CFG['noun'];
$ll_self = 'index.php?page=' . $LL_CFG['page'];
$ll_p    = 'll_' . $LL_KEY . '_';   // per-list parameter prefix

// ── POST: create / update ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_logo']) && ($_POST['logo_list_key'] ?? '') === $LL_KEY) {
    $id     = !empty($_POST['logo_id']) ? (int)$_POST['logo_id'] : null;
    $alt    = pc_strip_text($_POST['logo_alt'] ?? '');
    $url    = $LL_CFG['url'] ? strip_tags(trim((string)($_POST['logo_url'] ?? ''))) : '';
    $sort   = (int)($_POST['logo_sort_order'] ?? 0);
    $active = !empty($_POST['logo_is_active']) ? 1 : 0;
    $logo   = pc_strip_text($_POST['logo_existing'] ?? '');

    $errors = [];
    if ($alt === '') $errors[] = 'Name / alt text is required — screen readers announce it in place of the logo.';

    // Prefer the cropper's base64 payload; fall back to a raw file upload
    // (e.g. SVG logos the cropper passes through untouched).
    $up = pc_save_base64_image($_POST['logo_cropped'] ?? '', ADMIN_ROOT . '/uploads/logos/', 'logo');
    if (!is_string($up)) {
        $up = pc_upload_image('logo_file', ADMIN_ROOT . '/uploads/logos/', 'logo');
    }
    if ($up === false) {
        $errors[] = 'Logo upload failed (check file type — JPG/PNG/WebP/SVG/GIF — and size under 5 MB).';
    } elseif ($up) {
        $logo = $up;
    }
    if ($logo === '') $errors[] = 'A logo image is required.';

    if ($errors) {
        set_flash('danger', implode(' ', $errors));
        header('Location: ' . $ll_self . '&' . $ll_p . ($id ? 'edit=' . $id : 'new=1'));
        exit;
    }

    if ($id) {
        // Scoped by list_key so a crafted id can't reach another strip's rows.
        $stmt = $conn->prepare('UPDATE logo_lists SET logo_path = ?, url = ?, alt = ?, sort_order = ?, is_active = ? WHERE id = ? AND list_key = ?');
        $stmt->bind_param('sssiiis', $logo, $url, $alt, $sort, $active, $id, $LL_KEY);
        $stmt->execute();
        $stmt->close();
        set_flash('success', ucfirst($LL_NOUN) . ' updated.');
    } else {
        $stmt = $conn->prepare('INSERT INTO logo_lists (list_key, logo_path, url, alt, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssii', $LL_KEY, $logo, $url, $alt, $sort, $active);
        $stmt->execute();
        $stmt->close();
        set_flash('success', ucfirst($LL_NOUN) . ' added.');
    }
    header('Location: ' . $ll_self);
    exit;
}

// ── GET: toggle / delete ──────────────────────────────────────
if (isset($_GET[$ll_p . 'toggle'])) {
    $id = (int)$_GET[$ll_p . 'toggle'];
    $stmt = $conn->prepare('UPDATE logo_lists SET is_active = 1 - is_active WHERE id = ? AND list_key = ?');
    $stmt->bind_param('is', $id, $LL_KEY);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: ' . $ll_self);
    exit;
}
if (isset($_GET[$ll_p . 'delete'])) {
    $id = (int)$_GET[$ll_p . 'delete'];
    // Only files this manager uploaded are removed; logos seeded from
    // assets/img/ are shared with other pages and must stay on disk.
    $sel = $conn->prepare('SELECT logo_path FROM logo_lists WHERE id = ? AND list_key = ?');
    $sel->bind_param('is', $id, $LL_KEY);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();
    $sel->close();
    if ($row && !empty($row['logo_path']) && strpos($row['logo_path'], 'admin/uploads/logos/') === 0) {
        $fs = dirname(ADMIN_ROOT) . '/' . $row['logo_path'];
        if (is_file($fs)) @unlink($fs);
    }
    $stmt = $conn->prepare('DELETE FROM logo_lists WHERE id = ? AND list_key = ?');
    $stmt->bind_param('is', $id, $LL_KEY);
    $stmt->execute();
    $stmt->close();
    set_flash('success', ucfirst($LL_NOUN) . ' deleted.');
    header('Location: ' . $ll_self);
    exit;
}

// ── Load ──────────────────────────────────────────────────────
$stmt = $conn->prepare('SELECT * FROM logo_lists WHERE list_key = ? ORDER BY sort_order ASC, id ASC');
$stmt->bind_param('s', $LL_KEY);
$stmt->execute();
$ll_rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$ll_edit   = null;
$ll_is_new = isset($_GET[$ll_p . 'new']);
if (isset($_GET[$ll_p . 'edit'])) {
    $stmt = $conn->prepare('SELECT * FROM logo_lists WHERE id = ? AND list_key = ?');
    $eid = (int)$_GET[$ll_p . 'edit'];
    $stmt->bind_param('is', $eid, $LL_KEY);
    $stmt->execute();
    $ll_edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

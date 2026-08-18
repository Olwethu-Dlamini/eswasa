<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/training_families.php';

// ── Page content keys (the static page copy — unchanged from before) ─────────
$page_text_keys = [
    // Breadcrumb / hero
    'train_cal_breadcrumb_home',
    'train_cal_breadcrumb_parent',
    'train_cal_breadcrumb_current',
    'train_cal_hero_title',
    // Section heading
    'train_cal_section_title',
    'train_cal_section_subtitle',
    // Action buttons
    'train_cal_prospectus_label',
    'train_cal_prospectus_url',
    'train_cal_application_label',
    'train_cal_application_url',
    'train_cal_elearning_label',
    'train_cal_elearning_soon_badge',
    // Trainings list header
    'train_cal_year_label',
    'train_cal_reset_filter_label',
    // Apply modal
    'train_cal_modal_title_prefix',
    'train_cal_modal_title_on',
    'train_cal_modal_intro',
    'train_cal_modal_label_name',
    'train_cal_modal_label_email',
    'train_cal_modal_label_phone',
    'train_cal_modal_label_company',
    'train_cal_modal_label_position',
    'train_cal_modal_label_comments',
    'train_cal_modal_consent',
    'train_cal_modal_submit_label',
];

// ── POST: save page content ──────────────────────────────────────────────────
/**
 * Store an uploaded PDF under admin/uploads/ and return its web path.
 *
 * Editors were previously expected to type a file path into a text box, which
 * is how the Prospectus link came to point at admin/downloads/ — a directory
 * that has never existed — leaving the button 404ing with nothing in the UI to
 * suggest anything was wrong. A file picker removes the opportunity for that.
 *
 * Returns the stored path on success, null when no file was submitted, or a
 * string starting with "ERR:" describing why it was rejected.
 * See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md (A6).
 */
function train_cal_upload_pdf(string $field, int $max_bytes = 26214400): ?string
{
    if (empty($_FILES[$field]['name']) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($_FILES[$field]['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return 'ERR:Upload failed (error ' . (int)$_FILES[$field]['error'] . ').';
    }
    if ((int)$_FILES[$field]['size'] > $max_bytes) {
        return 'ERR:PDF is larger than the 25 MB limit.';
    }

    $tmp = $_FILES[$field]['tmp_name'];
    if (!is_uploaded_file($tmp)) {
        return 'ERR:Upload could not be verified.';
    }

    // Trust the file's contents, not its extension.
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $tmp);
        finfo_close($finfo);
        if ($mime && !in_array(strtolower($mime), ['application/pdf', 'application/x-pdf'], true)) {
            return 'ERR:That file is not a PDF.';
        }
    }

    $stem = strtolower(pathinfo((string)$_FILES[$field]['name'], PATHINFO_FILENAME));
    $stem = trim(preg_replace('/[^a-z0-9]+/', '-', $stem), '-');
    if ($stem === '') $stem = 'prospectus';
    $stem = substr($stem, 0, 60);

    $dir = ADMIN_ROOT . '/uploads/';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return 'ERR:Could not create the uploads directory.';
    }
    if (!is_writable($dir)) {
        return 'ERR:The uploads directory is not writable.';
    }

    $name = $stem . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.pdf';
    if (!move_uploaded_file($tmp, $dir . $name)) {
        return 'ERR:Failed to save the uploaded file.';
    }
    return 'admin/uploads/' . $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_train_cal_content'])) {
    $kv = [];
    foreach ($page_text_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }

    // A newly uploaded PDF wins over whatever is in the URL box; otherwise the
    // typed value is kept, so existing external links keep working.
    $upload_error = null;
    $uploaded = train_cal_upload_pdf('train_cal_prospectus_file');
    if (is_string($uploaded) && strpos($uploaded, 'ERR:') === 0) {
        $upload_error = substr($uploaded, 4);
    } elseif (is_string($uploaded)) {
        $kv['train_cal_prospectus_url'] = $uploaded;
    }

    $errs = pc_save_many($conn, $kv);
    if ($upload_error !== null) {
        set_flash('danger', 'Page content saved, but the prospectus was not replaced: ' . $upload_error);
        header('Location: index.php?page=training_calendar.php&tab=content');
        exit;
    }
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=training_calendar.php&tab=content');
    exit;
}

// ── POST: save (create / update) a training session + its intakes ────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_session'])) {
    $id         = !empty($_POST['id']) ? (int)$_POST['id'] : null;
    $code       = pc_strip_text($_POST['code'] ?? '');
    $family     = pc_strip_text($_POST['family'] ?? '');
    $title      = pc_strip_text($_POST['title'] ?? '');
    $location   = pc_strip_text($_POST['location'] ?? '') ?: 'Mbabane';
    $duration   = pc_strip_text($_POST['duration'] ?? '') ?: '5 days';
    $price      = pc_strip_text($_POST['price'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active  = !empty($_POST['is_active']) ? 1 : 0;

    $errors = [];
    if ($code === '')                                     $errors[] = 'Code is required.';
    if ($title === '')                                    $errors[] = 'Title is required.';
    if (!isset($TRAINING_FAMILIES[$family]))              $errors[] = 'Family must be one of the predefined options.';

    // Collect intakes from parallel arrays
    $intake_starts = $_POST['intake_start']  ?? [];
    $intake_ends   = $_POST['intake_end']    ?? [];
    $intake_labels = $_POST['intake_label']  ?? [];

    $intakes = [];
    $count = max(count($intake_starts), count($intake_ends), count($intake_labels));
    for ($i = 0; $i < $count; $i++) {
        $s = trim($intake_starts[$i] ?? '');
        $e = trim($intake_ends[$i]   ?? '');
        $l = pc_strip_text($intake_labels[$i] ?? '');
        // Skip fully blank rows (lets editors leave the appended slot empty)
        if ($s === '' && $e === '' && $l === '') continue;

        if ($s === '' || $e === '') { $errors[] = 'Each intake needs a start and end date.'; continue; }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $e)) {
            $errors[] = 'Intake dates must be ISO format (YYYY-MM-DD).'; continue;
        }
        if ($e < $s) { $errors[] = 'Intake end date must be on or after the start date.'; continue; }
        if ($l === '') {
            $l = derive_intake_label($s, $e);
        }
        $intakes[] = ['start' => $s, 'end' => $e, 'label' => $l, 'sort' => ($i + 1) * 10];
    }

    if ($errors) {
        set_flash('danger', implode(' ', array_unique($errors)));
        $redirect_id = $id ?: '';
        header('Location: index.php?page=training_calendar.php' . ($redirect_id ? '&edit=' . $redirect_id : '&action=new'));
        exit;
    }

    $conn->begin_transaction();
    try {
        if ($id) {
            $stmt = $conn->prepare('UPDATE training_sessions SET code = ?, family = ?, title = ?, location = ?, duration = ?, price = ?, sort_order = ?, is_active = ? WHERE id = ?');
            $stmt->bind_param('ssssssiii', $code, $family, $title, $location, $duration, $price, $sort_order, $is_active, $id);
            $stmt->execute();
            $stmt->close();
            // Replace intakes wholesale — simpler than diffing on a small set
            $del = $conn->prepare('DELETE FROM training_intakes WHERE session_id = ?');
            $del->bind_param('i', $id);
            $del->execute();
            $del->close();
            $session_id = $id;
            $msg = 'Training session updated.';
        } else {
            $stmt = $conn->prepare('INSERT INTO training_sessions (code, family, title, location, duration, price, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('ssssssii', $code, $family, $title, $location, $duration, $price, $sort_order, $is_active);
            $stmt->execute();
            $session_id = $conn->insert_id;
            $stmt->close();
            $msg = 'Training session added.';
        }

        if ($intakes) {
            $ins = $conn->prepare('INSERT INTO training_intakes (session_id, start_date, end_date, label, sort_order) VALUES (?, ?, ?, ?, ?)');
            foreach ($intakes as $it) {
                $ins->bind_param('isssi', $session_id, $it['start'], $it['end'], $it['label'], $it['sort']);
                $ins->execute();
            }
            $ins->close();
        }

        $conn->commit();
        set_flash('success', $msg);
    } catch (Throwable $e) {
        $conn->rollback();
        set_flash('danger', 'Database error: ' . $e->getMessage());
    }
    header('Location: index.php?page=training_calendar.php');
    exit;
}

// ── GET: quick toggle is_active ──────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $conn->prepare('UPDATE training_sessions SET is_active = 1 - is_active WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Active state toggled.');
    header('Location: index.php?page=training_calendar.php');
    exit;
}

// ── GET: delete ──────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM training_sessions WHERE id = ?'); // intakes cascade
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Training session deleted.');
    header('Location: index.php?page=training_calendar.php');
    exit;
}

// ── Helpers ──────────────────────────────────────────────────────────────────
function derive_intake_label(string $start, string $end): string {
    // "2026-05-18" + "2026-05-22" → "18–22 May"
    // "2026-11-30" + "2026-12-04" → "30 November – 4 December"
    $s = strtotime($start);
    $e = strtotime($end);
    if (!$s || !$e) return '';
    $sd = (int)date('j', $s);
    $ed = (int)date('j', $e);
    $sm = date('F', $s);
    $em = date('F', $e);
    if ($sm === $em) {
        return $sd . '–' . $ed . ' ' . $sm;
    }
    return $sd . ' ' . $sm . ' – ' . $ed . ' ' . $em;
}

// ── Load data ────────────────────────────────────────────────────────────────
$sessions_res = $conn->query('SELECT * FROM training_sessions ORDER BY sort_order ASC, id ASC');
$sessions = $sessions_res ? $sessions_res->fetch_all(MYSQLI_ASSOC) : [];

// Intake counts (for the list view)
$intake_counts = [];
$ic = $conn->query('SELECT session_id, COUNT(*) AS c FROM training_intakes GROUP BY session_id');
if ($ic) {
    while ($r = $ic->fetch_assoc()) $intake_counts[(int)$r['session_id']] = (int)$r['c'];
}

// Editing? Load the session + its intakes
$edit_session = null;
$edit_intakes = [];
$is_new = isset($_GET['action']) && $_GET['action'] === 'new';
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM training_sessions WHERE id = ?');
    $stmt->bind_param('i', $eid);
    $stmt->execute();
    $edit_session = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($edit_session) {
        $stmt = $conn->prepare('SELECT * FROM training_intakes WHERE session_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->bind_param('i', $eid);
        $stmt->execute();
        $edit_intakes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

$pc = pc_get_many($conn, $page_text_keys);

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'sessions';
if ($edit_session || $is_new) $active_tab = 'sessions';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Training Calendar</h1>
        <p class="text-muted mb-0">Manage the training sessions, their intake dates, and the static copy on the public page.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../training-calendar.php" target="_blank" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-external-link-alt me-1"></i> View page
        </a>
        <?php if (!$edit_session && !$is_new): ?>
            <a href="index.php?page=training_calendar.php&action=new" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add training
            </a>
        <?php endif; ?>
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'sessions' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-sessions" type="button" role="tab">
            Sessions <span class="badge bg-secondary ms-1"><?= count($sessions) ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'content' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">
            Page content
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- ============ TAB: Sessions ============ -->
    <div class="tab-pane fade <?= $active_tab === 'sessions' ? 'show active' : '' ?>" id="tab-sessions" role="tabpanel">

        <?php if ($edit_session || $is_new):
            $s = $edit_session ?: [
                'id' => 0, 'code' => '', 'family' => '', 'title' => '',
                'location' => 'Mbabane', 'duration' => '5 days', 'price' => '',
                'sort_order' => ($sessions ? (max(array_column($sessions, 'sort_order')) + 1) : 1),
                'is_active' => 1,
            ];
        ?>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><?= $edit_session ? 'Edit training' : 'Add training' ?></strong>
                    <a href="index.php?page=training_calendar.php" class="btn btn-sm btn-link text-decoration-none">&larr; Back to list</a>
                </div>
                <div class="card-body">
                    <form method="POST" id="sessionForm">
                        <?php if ($edit_session): ?>
                            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Code *</label>
                                <input type="text" name="code" class="form-control" required maxlength="32"
                                       value="<?= pc_h($s['code']) ?>" placeholder="e.g. QMS 02">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Family *</label>
                                <select name="family" class="form-select" required>
                                    <option value="">— choose —</option>
                                    <?php foreach ($TRAINING_FAMILIES as $fam => $colour): ?>
                                        <option value="<?= pc_h($fam) ?>" <?= $s['family'] === $fam ? 'selected' : '' ?>>
                                            <?= pc_h($fam) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Drives the colour swatch and legend grouping.</div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Sort order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= (int)$s['sort_order'] ?>">
                                <div class="form-text">Lower = earlier on page.</div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Duration</label>
                                <input type="text" name="duration" class="form-control" value="<?= pc_h($s['duration']) ?>" placeholder="5 days">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" name="location" class="form-control" value="<?= pc_h($s['location']) ?>" placeholder="Mbabane">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Title *</label>
                                <input type="text" name="title" class="form-control" required maxlength="500"
                                       value="<?= pc_h($s['title']) ?>"
                                       placeholder="e.g. Quality Management Systems — SZNS ISO 9001:2015 — Understanding & Implementation">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Price</label>
                                <input type="text" name="price" class="form-control" value="<?= pc_h($s['price']) ?>" placeholder="optional, e.g. E 4 500">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                           <?= (int)$s['is_active'] === 1 ? 'checked' : '' ?>>
                                    <label for="is_active" class="form-check-label">Show on public page</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Intakes</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addIntakeBtn">
                                <i class="fas fa-plus me-1"></i> Add intake
                            </button>
                        </div>
                        <p class="text-muted small mb-3">Each intake is a single run of this training. Label autofills from the dates (you can edit it).</p>

                        <div id="intakes-list">
                            <?php
                            $rows = $edit_intakes ?: [[]];  // one blank row on new
                            foreach ($rows as $idx => $it):
                                $start = $it['start_date'] ?? '';
                                $end   = $it['end_date']   ?? '';
                                $label = $it['label']      ?? '';
                            ?>
                                <div class="row g-2 align-items-end intake-row mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">Start date</label>
                                        <input type="date" name="intake_start[]" class="form-control intake-start" value="<?= pc_h($start) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small mb-1">End date</label>
                                        <input type="date" name="intake_end[]" class="form-control intake-end" value="<?= pc_h($end) ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small mb-1">Label (auto-derived)</label>
                                        <input type="text" name="intake_label[]" class="form-control intake-label" value="<?= pc_h($label) ?>" placeholder="e.g. 18–22 May">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-intake" title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="index.php?page=training_calendar.php" class="btn btn-link text-decoration-none">Cancel</a>
                            <button type="submit" name="save_session" value="1" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> <?= $edit_session ? 'Save changes' : 'Add training' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$edit_session && !$is_new): ?>
            <div class="card">
                <div class="card-header">All trainings (<?= count($sessions) ?>)</div>
                <div class="card-body p-0">
                    <?php if ($sessions): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">Order</th>
                                        <th style="width: 90px;">Code</th>
                                        <th>Title</th>
                                        <th style="width: 130px;">Family</th>
                                        <th style="width: 90px;" class="text-center">Intakes</th>
                                        <th style="width: 90px;" class="text-center">Active</th>
                                        <th style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sessions as $row):
                                        $colour = $TRAINING_FAMILIES[$row['family']] ?? '#888';
                                        $count = $intake_counts[(int)$row['id']] ?? 0;
                                    ?>
                                        <tr>
                                            <td><?= (int)$row['sort_order'] ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?= pc_h($colour) ?>; color: #fff;"><?= pc_h($row['code']) ?></span>
                                            </td>
                                            <td><?= pc_h($row['title']) ?></td>
                                            <td><?= pc_h($row['family']) ?></td>
                                            <td class="text-center"><?= $count ?></td>
                                            <td class="text-center">
                                                <a href="index.php?page=training_calendar.php&toggle=<?= (int)$row['id'] ?>"
                                                   class="btn btn-sm btn-link p-0"
                                                   title="<?= (int)$row['is_active'] === 1 ? 'Click to hide from public page' : 'Click to show on public page' ?>">
                                                    <?php if ((int)$row['is_active'] === 1): ?>
                                                        <span class="badge bg-success">On</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Off</span>
                                                    <?php endif; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="index.php?page=training_calendar.php&edit=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="index.php?page=training_calendar.php&delete=<?= (int)$row['id'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this training and all its intakes?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-4 text-center text-muted">
                            No trainings yet. <a href="index.php?page=training_calendar.php&action=new">Add your first training</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ============ TAB: Page content ============ -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <!-- enctype is required for the prospectus PDF picker below; without it
             the file is silently dropped from the POST. -->
        <form method="POST" enctype="multipart/form-data">

            <!-- Header / Hero -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Hero & Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hero title</label>
                            <input type="text" name="train_cal_hero_title" class="form-control" value="<?= pc_h($pc['train_cal_hero_title']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Breadcrumb: Home</label>
                            <input type="text" name="train_cal_breadcrumb_home" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_home']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Breadcrumb: Parent</label>
                            <input type="text" name="train_cal_breadcrumb_parent" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_parent']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Breadcrumb: Current</label>
                            <input type="text" name="train_cal_breadcrumb_current" class="form-control" value="<?= pc_h($pc['train_cal_breadcrumb_current']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section heading -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Section heading</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Section title</label>
                            <input type="text" name="train_cal_section_title" class="form-control" value="<?= pc_h($pc['train_cal_section_title']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Section subtitle</label>
                            <input type="text" name="train_cal_section_subtitle" class="form-control" value="<?= pc_h($pc['train_cal_section_subtitle']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action buttons -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Action buttons (Prospectus / Application / E-Learning)</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Prospectus label</label>
                            <input type="text" name="train_cal_prospectus_label" class="form-control" value="<?= pc_h($pc['train_cal_prospectus_label']) ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Prospectus PDF</label>
                            <?php
                                // Show whether the current link actually resolves. The stored
                                // path silently pointed at a non-existent directory for months
                                // with nothing in the UI to indicate the button was dead.
                                $prospectus_path = trim($pc['train_cal_prospectus_url']);
                                $is_local = $prospectus_path !== '' && !preg_match('#^(https?:)?//#i', $prospectus_path);
                                $exists   = $is_local && is_file(dirname(ADMIN_ROOT) . '/' . ltrim($prospectus_path, '/'));
                            ?>
                            <?php if ($prospectus_path !== ''): ?>
                                <div class="mb-2 small">
                                    <?php if (!$is_local): ?>
                                        <span class="badge bg-info">external link</span>
                                    <?php elseif ($exists): ?>
                                        <span class="badge bg-success">file found</span>
                                        <a href="../<?= pc_h($prospectus_path) ?>" target="_blank" rel="noopener" class="ms-1">view current PDF</a>
                                    <?php else: ?>
                                        <span class="badge bg-danger">file missing</span>
                                        <span class="text-muted ms-1">the download link is broken &mdash; upload a replacement below</span>
                                    <?php endif; ?>
                                    <div class="text-muted mt-1"><code><?= pc_h($prospectus_path) ?></code></div>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="train_cal_prospectus_file" class="form-control" accept="application/pdf,.pdf">
                            <div class="form-text">
                                Upload a PDF to replace the current prospectus (max 25 MB).
                                Leave empty to keep the existing file.
                            </div>
                            <label class="form-label mt-2 small text-muted">Or link to an external URL instead</label>
                            <input type="text" name="train_cal_prospectus_url" class="form-control form-control-sm"
                                   value="<?= pc_h($pc['train_cal_prospectus_url']) ?>">
                            <div class="form-text">Only used when no file is uploaded above.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Application label</label>
                            <input type="text" name="train_cal_application_label" class="form-control" value="<?= pc_h($pc['train_cal_application_label']) ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Application URL</label>
                            <input type="text" name="train_cal_application_url" class="form-control" value="<?= pc_h($pc['train_cal_application_url']) ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">E-Learning label</label>
                            <input type="text" name="train_cal_elearning_label" class="form-control" value="<?= pc_h($pc['train_cal_elearning_label']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">E-Learning "Coming soon" badge</label>
                            <input type="text" name="train_cal_elearning_soon_badge" class="form-control" value="<?= pc_h($pc['train_cal_elearning_soon_badge']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trainings list header -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">Trainings list header</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Year label</label>
                            <input type="text" name="train_cal_year_label" class="form-control" value="<?= pc_h($pc['train_cal_year_label']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">"Reset filter" button label</label>
                            <input type="text" name="train_cal_reset_filter_label" class="form-control" value="<?= pc_h($pc['train_cal_reset_filter_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apply modal -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">"Apply for Training" modal</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Modal title prefix</label>
                            <input type="text" name="train_cal_modal_title_prefix" class="form-control" value="<?= pc_h($pc['train_cal_modal_title_prefix']) ?>">
                            <div class="form-text">Shown before the training name in the modal title.</div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">"on" connector</label>
                            <input type="text" name="train_cal_modal_title_on" class="form-control" value="<?= pc_h($pc['train_cal_modal_title_on']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Intro paragraph</label>
                            <input type="text" name="train_cal_modal_intro" class="form-control" value="<?= pc_h($pc['train_cal_modal_intro']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Full Name</label>
                            <input type="text" name="train_cal_modal_label_name" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_name']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Email</label>
                            <input type="text" name="train_cal_modal_label_email" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_email']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Phone</label>
                            <input type="text" name="train_cal_modal_label_phone" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_phone']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Company</label>
                            <input type="text" name="train_cal_modal_label_company" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_company']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Position</label>
                            <input type="text" name="train_cal_modal_label_position" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_position']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Field label: Comments</label>
                            <input type="text" name="train_cal_modal_label_comments" class="form-control" value="<?= pc_h($pc['train_cal_modal_label_comments']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Consent text</label>
                            <textarea name="train_cal_modal_consent" class="form-control" rows="2"><?= pc_h($pc['train_cal_modal_consent']) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Submit button label</label>
                            <input type="text" name="train_cal_modal_submit_label" class="form-control" value="<?= pc_h($pc['train_cal_modal_submit_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_train_cal_content" value="1" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i> Save page content
                </button>
            </div>
        </form>
    </div>

</div>

<?php if ($edit_session || $is_new): ?>
<script>
(function() {
    // Auto-derive a human label like "18–22 May" / "30 November – 4 December" from two ISO dates.
    function deriveLabel(startStr, endStr) {
        if (!startStr || !endStr) return '';
        var s = new Date(startStr + 'T00:00:00');
        var e = new Date(endStr   + 'T00:00:00');
        if (isNaN(s) || isNaN(e)) return '';
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        var sd = s.getDate(), ed = e.getDate();
        var sm = months[s.getMonth()], em = months[e.getMonth()];
        if (sm === em) return sd + '–' + ed + ' ' + sm;
        return sd + ' ' + sm + ' – ' + ed + ' ' + em;
    }

    function wireRow(row) {
        var startEl = row.querySelector('.intake-start');
        var endEl   = row.querySelector('.intake-end');
        var labelEl = row.querySelector('.intake-label');
        var removeBtn = row.querySelector('.remove-intake');

        function sync() {
            // Only auto-fill if the user hasn't typed a custom label
            if (!labelEl.dataset.userEdited) {
                labelEl.value = deriveLabel(startEl.value, endEl.value);
            }
        }
        startEl.addEventListener('change', sync);
        endEl.addEventListener('change', sync);
        labelEl.addEventListener('input', function () {
            // Track edits only when there's content; clearing reverts to auto
            labelEl.dataset.userEdited = labelEl.value.trim() ? '1' : '';
        });

        // Pre-existing rows: treat the label as user-edited so we don't overwrite seeded values.
        if (labelEl.value.trim() && deriveLabel(startEl.value, endEl.value) !== labelEl.value.trim()) {
            labelEl.dataset.userEdited = '1';
        }

        removeBtn.addEventListener('click', function () {
            var rows = document.querySelectorAll('.intake-row');
            if (rows.length === 1) {
                // Don't remove the last row — just clear it.
                startEl.value = ''; endEl.value = ''; labelEl.value = '';
                labelEl.dataset.userEdited = '';
            } else {
                row.remove();
            }
        });
    }

    document.querySelectorAll('.intake-row').forEach(wireRow);

    document.getElementById('addIntakeBtn').addEventListener('click', function () {
        var list = document.getElementById('intakes-list');
        var template = list.querySelector('.intake-row');
        var clone = template.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (i) { i.value = ''; });
        var lbl = clone.querySelector('.intake-label');
        if (lbl) delete lbl.dataset.userEdited;
        list.appendChild(clone);
        wireRow(clone);
        clone.querySelector('.intake-start').focus();
    });
})();
</script>
<?php endif; ?>

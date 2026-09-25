<?php
/**
 * admin/pages/training_applications.php — applications sent from the Apply
 * form on the public training calendar (process_training_application.php).
 *
 * Laid out like the quote inboxes: a list, a detail window per application,
 * a status and internal notes. The list can be narrowed to one training, so
 * staff can see everyone who applied for a given course.
 */
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');
require_once __DIR__ . '/../../includes/cms_helpers.php';

$app_statuses = [
    'new'       => ['bg-primary',   'New'],
    'viewed'    => ['bg-info text-dark', 'Viewed'],
    'contacted' => ['bg-warning text-dark', 'Contacted'],
    'closed'    => ['bg-secondary', 'Closed'],
];

// ── Status update ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $new_status = (string)$_POST['update_status'];
    if (isset($app_statuses[$new_status])) {
        $stmt = $conn->prepare('UPDATE eswasa_training_applications SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $new_status, $id);
        $stmt->execute();
        $stmt->close();
        log_activity($conn, 'application.status', 'eswasa_training_applications#' . $id, $new_status);
        set_flash('success', 'Status updated.');
    }
    redirect_self();
}

// ── Notes update ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $notes = pc_strip_text($_POST['notes'] ?? '');
    $stmt = $conn->prepare('UPDATE eswasa_training_applications SET notes = ? WHERE id = ?');
    $stmt->bind_param('si', $notes, $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Notes saved.');
    redirect_self();
}

// ── Delete ────────────────────────────────────────────────────
if (isset($_GET['delete_application'])) {
    $id = (int)$_GET['delete_application'];
    $stmt = $conn->prepare('DELETE FROM eswasa_training_applications WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    log_activity($conn, 'application.delete', 'eswasa_training_applications#' . $id);
    set_flash('success', 'Application deleted.');
    redirect_self();
}

// ── Fetch ─────────────────────────────────────────────────────
$table_ready = true;
$rows = [];
$trainings = [];
$filter = trim((string)($_GET['training'] ?? ''));
try {
    $res = $conn->query(
        "SELECT training_code, MAX(training_title) AS training_title, COUNT(*) AS n
           FROM eswasa_training_applications
          GROUP BY training_code
          ORDER BY training_code"
    );
    while ($r = $res->fetch_assoc()) {
        $trainings[(string)$r['training_code']] = $r;
    }

    $sql = 'SELECT * FROM eswasa_training_applications';
    if ($filter !== '') {
        $stmt = $conn->prepare($sql . ' WHERE training_code = ? ORDER BY created_at DESC, id DESC');
        $stmt->bind_param('s', $filter);
    } else {
        $stmt = $conn->prepare($sql . ' ORDER BY created_at DESC, id DESC');
    }
    $stmt->execute();
    $rs = $stmt->get_result();
    while ($r = $rs->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();
} catch (Throwable $e) {
    $table_ready = false; // migration not run yet
}

$counts = array_fill_keys(array_keys($app_statuses), 0);
foreach ($rows as $r) {
    if (isset($counts[$r['status']])) $counts[$r['status']]++;
}

$app_badge = function (string $status) use ($app_statuses) {
    [$cls, $label] = $app_statuses[$status] ?? ['bg-light text-dark', $status];
    return '<span class="badge ' . $cls . '">' . htmlspecialchars($label) . '</span>';
};
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom gap-2">
    <div>
        <h1 class="h2 mb-1">Training Applications</h1>
        <small class="text-muted">
            <?= $counts['new'] ?> new ·
            <?= $counts['viewed'] ?> viewed ·
            <?= $counts['contacted'] ?> contacted ·
            <?= $counts['closed'] ?> closed
        </small>
    </div>
    <?php if ($trainings): ?>
        <form method="get" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="page" value="training_applications.php">
            <select name="training" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:260px;">
                <option value="">All trainings</option>
                <?php foreach ($trainings as $code => $t): ?>
                    <option value="<?= htmlspecialchars((string)$code) ?>" <?= $filter === (string)$code ? 'selected' : '' ?>>
                        <?= htmlspecialchars($code . ' — ' . $t['training_title'] . ' (' . $t['n'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php endif; ?>
</div>

<?php if (!$table_ready): ?>
    <div class="alert alert-warning">
        The applications table does not exist yet. Run
        <code>admin/sql/upgrade_2026_09_25.sql</code> on the database.
    </div>
<?php elseif (empty($rows)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
            <h5>No applications<?= $filter !== '' ? ' for this training' : ' yet' ?></h5>
            <p class="text-muted mb-0">Applications sent from the training calendar will appear here.</p>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Received</th>
                            <th>Applicant</th>
                            <th>Training</th>
                            <th>Status</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r):
                        $modal_id = 'amodal_' . (int)$r['id']; ?>
                        <tr>
                            <td class="small text-nowrap"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['created_at']))) ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars($r['full_name']) ?></div>
                                <div class="small"><a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a></div>
                                <?php if ($r['phone'] !== ''): ?>
                                    <div class="small text-muted"><?= htmlspecialchars($r['phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars((string)$r['training_code']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars((string)$r['intake_label']) ?></div>
                            </td>
                            <td><?= $app_badge((string)$r['status']) ?></td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="?<?= htmlspecialchars(http_build_query(['page' => 'training_applications.php', 'training' => $filter, 'delete_application' => (int)$r['id']])) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this application?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php foreach ($rows as $r):
        $modal_id = 'amodal_' . (int)$r['id']; ?>
        <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Application #<?= (int)$r['id'] ?>
                            <?= $app_badge((string)$r['status']) ?>
                            <small class="text-muted ms-2"><?= htmlspecialchars(date('d M Y, H:i', strtotime($r['created_at']))) ?></small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <dl class="row mb-3">
                            <dt class="col-sm-4">Training</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($r['training_code'] . ' — ' . $r['training_title']) ?></dd>
                            <dt class="col-sm-4">Intake</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars((string)$r['intake_label']) ?></dd>
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($r['full_name']) ?></dd>
                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8"><a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a></dd>
                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($r['phone']) ?></dd>
                            <dt class="col-sm-4">Organisation</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars((string)($r['company'] ?? '')) ?: '<span class="text-muted">&mdash;</span>' ?></dd>
                            <dt class="col-sm-4">Position</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars((string)($r['position'] ?? '')) ?: '<span class="text-muted">&mdash;</span>' ?></dd>
                        </dl>
                        <?php if (!empty($r['comments'])): ?>
                            <h6 class="fw-bold">Comments</h6>
                            <p style="white-space: pre-line;"><?= htmlspecialchars($r['comments']) ?></p>
                        <?php endif; ?>

                        <h6>Status</h6>
                        <form method="POST" class="mb-3 d-flex gap-2 align-items-center">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <select name="update_status" class="form-select form-select-sm" style="max-width:200px;">
                                <?php foreach ($app_statuses as $val => [$cls, $lbl]): ?>
                                    <option value="<?= $val ?>" <?= $r['status'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update status</button>
                        </form>

                        <h6>Internal notes</h6>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <textarea name="notes" class="form-control mb-2" rows="3" placeholder="Add notes (not visible to the applicant)..."><?= htmlspecialchars((string)($r['notes'] ?? '')) ?></textarea>
                            <button type="submit" name="save_notes" value="1" class="btn btn-sm btn-outline-primary">Save notes</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <a href="mailto:<?= htmlspecialchars($r['email']) ?>?subject=<?= rawurlencode('Your application: ' . $r['training_code'] . ' — ' . $r['intake_label']) ?>" class="btn btn-primary">
                            <i class="fas fa-reply me-1"></i>Reply via email
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

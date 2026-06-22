<?php
// admin/pages/activity_log.php — read-only audit trail viewer.
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// ---- Filters ----
$f_user   = trim($_GET['f_user'] ?? '');
$f_action = trim($_GET['f_action'] ?? '');
$f_from   = trim($_GET['f_from'] ?? '');
$f_to     = trim($_GET['f_to'] ?? '');

$where  = [];
$params = [];
$types  = '';
if ($f_user !== '')   { $where[] = 'username LIKE ?';      $params[] = '%' . $f_user . '%'; $types .= 's'; }
if ($f_action !== '') { $where[] = 'action LIKE ?';        $params[] = '%' . $f_action . '%'; $types .= 's'; }
if ($f_from !== '')   { $where[] = 'created_at >= ?';      $params[] = $f_from . ' 00:00:00'; $types .= 's'; }
if ($f_to !== '')     { $where[] = 'created_at <= ?';      $params[] = $f_to . ' 23:59:59'; $types .= 's'; }
$where_sql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Distinct actions for the filter dropdown.
$actions = [];
if ($ares = @$conn->query("SELECT DISTINCT action FROM activity_log ORDER BY action")) {
    while ($r = $ares->fetch_assoc()) $actions[] = $r['action'];
}

// ---- Rows (cap at 500) ----
$rows = [];
$sql = "SELECT username, action, entity, details, ip_address, created_at
        FROM activity_log $where_sql
        ORDER BY created_at DESC, id DESC
        LIMIT 500";
if ($stmt = @$conn->prepare($sql)) {
    if ($types !== '') { $stmt->bind_param($types, ...$params); }
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $rows[] = $row;
    }
    $stmt->close();
}

function badge_class($action) {
    if (strpos($action, 'fail') !== false) return 'bg-danger';
    if (strpos($action, 'delete') !== false) return 'bg-warning text-dark';
    if (strpos($action, 'login') !== false || strpos($action, 'logout') !== false) return 'bg-secondary';
    if (strpos($action, 'create') !== false) return 'bg-success';
    return 'bg-info text-dark';
}
?>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Activity Log</h1>
    <span class="text-muted small">Showing latest <?= count($rows) ?> (max 500)</span>
</div>

<form method="get" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="page" value="activity_log.php">
    <div class="col-sm-3">
        <label class="form-label small mb-1">User</label>
        <input type="text" name="f_user" class="form-control form-control-sm" value="<?= htmlspecialchars($f_user) ?>" placeholder="username">
    </div>
    <div class="col-sm-3">
        <label class="form-label small mb-1">Action</label>
        <select name="f_action" class="form-select form-select-sm">
            <option value="">All actions</option>
            <?php foreach ($actions as $a): ?>
                <option value="<?= htmlspecialchars($a) ?>" <?= $f_action === $a ? 'selected' : '' ?>><?= htmlspecialchars($a) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-sm-2">
        <label class="form-label small mb-1">From</label>
        <input type="date" name="f_from" class="form-control form-control-sm" value="<?= htmlspecialchars($f_from) ?>">
    </div>
    <div class="col-sm-2">
        <label class="form-label small mb-1">To</label>
        <input type="date" name="f_to" class="form-control form-control-sm" value="<?= htmlspecialchars($f_to) ?>">
    </div>
    <div class="col-sm-2 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
        <a href="index.php?page=activity_log.php" class="btn btn-sm btn-outline-secondary">Reset</a>
    </div>
</form>

<?php if (empty($rows)): ?>
    <div class="text-center py-5">
        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
        <h5>No activity recorded yet</h5>
        <p class="text-muted">Actions like logins and content edits will appear here.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>When</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Item</th>
                    <th>Details</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td class="small text-nowrap"><?= htmlspecialchars($r['created_at']) ?></td>
                        <td class="small"><?= htmlspecialchars($r['username'] ?? '—') ?></td>
                        <td><span class="badge <?= badge_class($r['action']) ?>"><?= htmlspecialchars($r['action']) ?></span></td>
                        <td class="small"><?= htmlspecialchars($r['entity'] ?? '') ?></td>
                        <td class="small"><?= htmlspecialchars($r['details'] ?? '') ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($r['ip_address'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

// Detect available columns on site_statistics (handles legacy schemas)
$has_stat_key   = false;
$has_stat_label = false;
$has_stat_value = false;
$has_stat_name  = false;
$cols_rs = @$conn->query("SHOW COLUMNS FROM site_statistics");
if ($cols_rs) {
    while ($c = $cols_rs->fetch_assoc()) {
        switch ($c['Field']) {
            case 'stat_key':   $has_stat_key = true; break;
            case 'stat_label': $has_stat_label = true; break;
            case 'stat_value': $has_stat_value = true; break;
            case 'stat_name':  $has_stat_name = true; break;
        }
    }
}

$label_col = $has_stat_label ? 'stat_label' : ($has_stat_name ? 'stat_name' : null);
$value_col = $has_stat_value ? 'stat_value' : null;

// ── Handle update of existing rows ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_stats'])) {
    if (!$label_col || !$value_col) {
        set_flash('danger', 'site_statistics table is missing required columns (label / value).');
        redirect_self();
    }
    if (isset($_POST['stats']) && is_array($_POST['stats'])) {
        $sql = "UPDATE site_statistics SET {$label_col} = ?, {$value_col} = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            foreach ($_POST['stats'] as $id => $vals) {
                $id    = (int)$id;
                $label = trim((string)($vals['label'] ?? ''));
                $value = (int)($vals['value'] ?? 0);
                if ($label === '') continue;
                $stmt->bind_param('sii', $label, $value, $id);
                $stmt->execute();
            }
            $stmt->close();
            set_flash('success', 'Statistics updated.');
        } else {
            set_flash('danger', 'Could not prepare update statement.');
        }
    }
    redirect_self();
}

// ── Handle Add new row ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stat'])) {
    if (!$label_col || !$value_col) {
        set_flash('danger', 'site_statistics table is missing required columns.');
        redirect_self();
    }
    $new_label = trim($_POST['new_label'] ?? '');
    $new_value = (int)($_POST['new_value'] ?? 0);
    $new_key   = trim($_POST['new_key'] ?? '');

    if ($new_label === '') {
        set_flash('warning', 'Label is required.');
        redirect_self();
    }

    if ($has_stat_key) {
        if ($new_key === '') {
            // Auto-generate from label
            $new_key = preg_replace('/[^a-z0-9_]+/', '_', strtolower($new_label));
            $new_key = trim($new_key, '_');
            if ($new_key === '') $new_key = 'stat_' . time();
        }
        $sql = "INSERT INTO site_statistics (stat_key, {$label_col}, {$value_col}) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $new_key, $new_label, $new_value);
    } else {
        $sql = "INSERT INTO site_statistics ({$label_col}, {$value_col}) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $new_label, $new_value);
    }
    if ($stmt && $stmt->execute()) {
        set_flash('success', 'Statistic added.');
    } else {
        set_flash('danger', 'Failed to add statistic: ' . $conn->error);
    }
    if ($stmt) $stmt->close();
    redirect_self();
}

// ── Handle Delete ──────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM site_statistics WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Statistic deleted.');
    header('Location: index.php?page=statistics_edit.php');
    exit;
}

// ── Fetch existing rows ────────────────────────────────────────
$rows = [];
$rs = $conn->query("SELECT * FROM site_statistics ORDER BY id ASC");
if ($rs) {
    while ($r = $rs->fetch_assoc()) {
        $rows[] = $r;
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Site Statistics</h1>
</div>

<p class="text-muted">These figures appear in the public statistics counter (members, events, partners, etc.). Edit labels and values, or add new metrics.</p>

<?php if (!$label_col || !$value_col): ?>
    <div class="alert alert-danger">
        The <code>site_statistics</code> table is missing required columns. Expected at least a label column (<code>stat_label</code> or <code>stat_name</code>) and <code>stat_value</code>.
    </div>
<?php else: ?>

<form method="POST" class="card mb-4">
    <input type="hidden" name="save_stats" value="1">
    <div class="card-body">
        <h5 class="mb-3">Existing Statistics</h5>
        <?php if (empty($rows)): ?>
            <p class="text-muted mb-0">No statistics yet. Use the form below to add one.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <?php if ($has_stat_key): ?><th style="width:160px;">Key</th><?php endif; ?>
                            <th>Label</th>
                            <th style="width:160px;">Value</th>
                            <th style="width:80px;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r):
                            $id = (int)$r['id'];
                            $label_val = $r[$label_col] ?? '';
                            $value_val = (int)($r[$value_col] ?? 0);
                        ?>
                            <tr>
                                <?php if ($has_stat_key): ?>
                                    <td><code class="small"><?= htmlspecialchars($r['stat_key'] ?? '') ?></code></td>
                                <?php endif; ?>
                                <td>
                                    <input type="text" name="stats[<?= $id ?>][label]"
                                           class="form-control form-control-sm"
                                           value="<?= htmlspecialchars($label_val) ?>">
                                </td>
                                <td>
                                    <input type="number" name="stats[<?= $id ?>][value]"
                                           class="form-control form-control-sm"
                                           value="<?= $value_val ?>" min="0">
                                </td>
                                <td>
                                    <a href="?page=statistics_edit.php&delete=<?= $id ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this statistic?');"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        <?php endif; ?>
    </div>
</form>

<form method="POST" class="card">
    <input type="hidden" name="add_stat" value="1">
    <div class="card-body">
        <h5 class="mb-3">Add New Statistic</h5>
        <div class="row g-3">
            <?php if ($has_stat_key): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Key (optional)</label>
                    <input type="text" name="new_key" class="form-control" placeholder="auto-generated">
                </div>
            <?php endif; ?>
            <div class="col-md-<?= $has_stat_key ? '5' : '7' ?>">
                <label class="form-label small fw-bold">Label *</label>
                <input type="text" name="new_label" class="form-control" required placeholder="e.g. Partners">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">Value</label>
                <input type="number" name="new_value" class="form-control" value="0" min="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-plus me-1"></i> Add
                </button>
            </div>
        </div>
    </div>
</form>

<?php endif; ?>

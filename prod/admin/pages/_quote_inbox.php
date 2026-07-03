<?php
// Shared admin inbox for quote requests. Loaded by qoute_training.php,
// qoute_certification.php, qoute_calibration.php (each sets
// $quote_source_filter + $quote_page_label before include).
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');
require_once __DIR__ . '/../../includes/cms_helpers.php';

$quote_source_filter = $quote_source_filter ?? 'other';
$quote_page_label    = $quote_page_label    ?? 'Quote Requests';

// ── Status update ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $new_status = $_POST['update_status'];
    if (in_array($new_status, ['new','in_progress','closed'], true)) {
        $stmt = $conn->prepare("UPDATE eswasa_quote_requests SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $new_status, $id);
        $stmt->execute();
        $stmt->close();
        set_flash('success', 'Status updated.');
    }
    redirect_self();
}

// ── Notes update ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_notes'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $notes = pc_strip_text($_POST['notes'] ?? '');
    $stmt = $conn->prepare("UPDATE eswasa_quote_requests SET notes = ? WHERE id = ?");
    $stmt->bind_param('si', $notes, $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Notes saved.');
    redirect_self();
}

// ── Delete ────────────────────────────────────────────────────
if (isset($_GET['delete_quote'])) {
    $id = (int)$_GET['delete_quote'];
    // Remove attachments from disk
    $stmt = $conn->prepare("SELECT attachments FROM eswasa_quote_requests WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($att_json);
    if ($stmt->fetch()) {
        $stmt->close();
        $att = $att_json ? json_decode($att_json, true) : [];
        if (is_array($att)) {
            foreach ($att as $p) {
                $fs = realpath(__DIR__ . '/../../' . ltrim((string)$p, '/'));
                if ($fs && strpos($fs, __DIR__ . '/../uploads/quotes/') !== false && is_file($fs)) {
                    @unlink($fs);
                }
            }
        }
    } else {
        $stmt->close();
    }
    $del = $conn->prepare("DELETE FROM eswasa_quote_requests WHERE id = ?");
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();
    set_flash('success', 'Quote request deleted.');
    redirect_self();
}

// ── Fetch ─────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT id, source, contact_name, contact_email, contact_phone, organization,
            raw_form, attachments, status, notes, created_at
       FROM eswasa_quote_requests
      WHERE source = ?
   ORDER BY created_at DESC"
);
$stmt->bind_param('s', $quote_source_filter);
$stmt->execute();
$rs = $stmt->get_result();
$rows = [];
while ($r = $rs->fetch_assoc()) $rows[] = $r;
$stmt->close();

// Counts per status (this filter only)
$counts = ['new' => 0, 'in_progress' => 0, 'closed' => 0];
foreach ($rows as $r) {
    if (isset($counts[$r['status']])) $counts[$r['status']]++;
}

function quote_status_badge(string $status): string {
    $map = [
        'new'         => ['bg-primary',   'New'],
        'in_progress' => ['bg-warning',   'In progress'],
        'closed'      => ['bg-secondary', 'Closed'],
    ];
    [$cls, $label] = $map[$status] ?? ['bg-light text-dark', $status];
    return '<span class="badge ' . $cls . '">' . htmlspecialchars($label) . '</span>';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1"><?= htmlspecialchars($quote_page_label) ?></h1>
        <small class="text-muted">
            <?= $counts['new'] ?> new ·
            <?= $counts['in_progress'] ?> in progress ·
            <?= $counts['closed'] ?> closed
        </small>
    </div>
</div>

<?php if (empty($rows)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>No quote requests yet</h5>
            <p class="text-muted mb-0">Submissions from the public form will appear here.</p>
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
                            <th>Contact</th>
                            <th>Organisation</th>
                            <th>Status</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r):
                        $modal_id = 'qmodal_' . (int)$r['id'];
                        $raw = $r['raw_form'] ? json_decode($r['raw_form'], true) : [];
                        if (!is_array($raw)) $raw = [];
                        $attachments = $r['attachments'] ? json_decode($r['attachments'], true) : [];
                        if (!is_array($attachments)) $attachments = [];
                    ?>
                        <tr>
                            <td class="small text-nowrap"><?= htmlspecialchars($r['created_at']) ?></td>
                            <td>
                                <div class="fw-semibold"><?= htmlspecialchars((string)($r['contact_name'] ?? '—')) ?></div>
                                <?php if (!empty($r['contact_email'])): ?>
                                    <div class="small"><a href="mailto:<?= htmlspecialchars($r['contact_email']) ?>"><?= htmlspecialchars($r['contact_email']) ?></a></div>
                                <?php endif; ?>
                                <?php if (!empty($r['contact_phone'])): ?>
                                    <div class="small text-muted"><?= htmlspecialchars($r['contact_phone']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars((string)($r['organization'] ?? '—')) ?></td>
                            <td><?= quote_status_badge((string)$r['status']) ?></td>
                            <td class="text-nowrap">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <a href="?page=qoute_<?= htmlspecialchars($quote_source_filter) ?>.php&delete_quote=<?= (int)$r['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this quote request? Attachments will be removed too.');">
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

    <!-- Detail modals -->
    <?php foreach ($rows as $r):
        $modal_id = 'qmodal_' . (int)$r['id'];
        $raw = $r['raw_form'] ? json_decode($r['raw_form'], true) : [];
        if (!is_array($raw)) $raw = [];
        $attachments = $r['attachments'] ? json_decode($r['attachments'], true) : [];
        if (!is_array($attachments)) $attachments = [];
    ?>
        <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Quote Request #<?= (int)$r['id'] ?>
                            <?= quote_status_badge((string)$r['status']) ?>
                            <small class="text-muted ms-2"><?= htmlspecialchars($r['created_at']) ?></small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <h6 class="mt-0">Submitted form fields</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-striped">
                                <tbody>
                                    <?php foreach ($raw as $k => $v):
                                        if ($k === 'quote_source') continue;
                                    ?>
                                        <tr>
                                            <th style="width:35%" class="text-nowrap"><?= htmlspecialchars((string)$k) ?></th>
                                            <td><?php
                                                if (is_array($v)) {
                                                    echo htmlspecialchars(implode(', ', array_map('strval', $v)));
                                                } else {
                                                    echo nl2br(htmlspecialchars((string)$v));
                                                }
                                            ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($attachments)): ?>
                            <h6>Attachments</h6>
                            <ul class="list-unstyled mb-3">
                                <?php foreach ($attachments as $a): ?>
                                    <li>
                                        <i class="fas fa-paperclip me-1"></i>
                                        <a href="../<?= htmlspecialchars((string)$a) ?>" target="_blank" rel="noopener">
                                            <?= htmlspecialchars(basename((string)$a)) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <h6>Status</h6>
                        <form method="POST" class="mb-3 d-flex gap-2 align-items-center">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <select name="update_status" class="form-select form-select-sm" style="max-width:200px;">
                                <?php foreach (['new'=>'New','in_progress'=>'In progress','closed'=>'Closed'] as $val=>$lbl): ?>
                                    <option value="<?= $val ?>" <?= $r['status']===$val?'selected':'' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Update status</button>
                        </form>

                        <h6>Internal notes</h6>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                            <textarea name="notes" class="form-control mb-2" rows="3" placeholder="Add notes (not visible to the requester)..."><?= htmlspecialchars((string)($r['notes'] ?? '')) ?></textarea>
                            <button type="submit" name="save_notes" value="1" class="btn btn-sm btn-outline-primary">Save notes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

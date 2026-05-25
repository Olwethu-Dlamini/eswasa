<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_customer_feedback.php';

// Ensure the submissions table exists even if no public submission has ever happened
@$conn->query("CREATE TABLE IF NOT EXISTS eswasa_customer_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service VARCHAR(150),
    feedback_type VARCHAR(50),
    resolved VARCHAR(20),
    issue TEXT,
    rating TINYINT,
    suggestion TEXT,
    email VARCHAR(150),
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at),
    INDEX idx_is_read (is_read)
)");

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customer_feedback_content'])) {
    $kv = [];
    foreach ($customer_feedback_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=customer_feedback.php&tab=content');
    exit;
}

// ── GET: mark-as-read toggle ──────────────────────────────────────
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $val = isset($_GET['unread']) ? 0 : 1;
    $stmt = $conn->prepare('UPDATE eswasa_customer_feedback SET is_read = ? WHERE id = ?');
    $stmt->bind_param('ii', $val, $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', $val ? 'Marked as read.' : 'Marked as unread.');
    header('Location: index.php?page=customer_feedback.php&tab=inbox');
    exit;
}

// ── GET: DELETE submission ────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM eswasa_customer_feedback WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Submission deleted.');
    header('Location: index.php?page=customer_feedback.php&tab=inbox');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$submissions = $conn->query('SELECT * FROM eswasa_customer_feedback ORDER BY created_at DESC');
$unread_cnt = 0;
$rows = [];
if ($submissions) {
    while ($r = $submissions->fetch_assoc()) {
        if (!(int)$r['is_read']) $unread_cnt++;
        $rows[] = $r;
    }
}

$pc = pc_get_many($conn, $customer_feedback_keys, $customer_feedback_defaults);

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'inbox';

function fb_stars(int $n): string {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $on = $i <= $n;
        $out .= '<i class="fas fa-star" style="color: ' . ($on ? '#2B3388' : 'rgba(43,51,136,0.20)') . ';"></i>';
    }
    return $out;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Customer Feedback</h1>
    <a href="../customer-feedback.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Form</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'inbox' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-inbox" type="button" role="tab">
            Inbox
            <?php if ($unread_cnt > 0): ?>
                <span class="badge bg-primary ms-1"><?= $unread_cnt ?> unread</span>
            <?php endif; ?>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link <?= $active_tab === 'content' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-content" type="button" role="tab">
            Page Content
        </button>
    </li>
</ul>

<div class="tab-content">

    <!-- ========== TAB: Inbox ========== -->
    <div class="tab-pane fade <?= $active_tab === 'inbox' ? 'show active' : '' ?>" id="tab-inbox" role="tabpanel">

        <div class="card">
            <div class="card-header">
                Submissions (<?= count($rows) ?>)
            </div>
            <div class="card-body">
                <?php if (empty($rows)): ?>
                    <p class="text-muted mb-0">No submissions yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 36px;"></th>
                                    <th>Received</th>
                                    <th>Service</th>
                                    <th>Type</th>
                                    <th>Resolved</th>
                                    <th>Rating</th>
                                    <th>Email</th>
                                    <th style="width: 230px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $r):
                                    $unread = !(int)$r['is_read'];
                                ?>
                                    <tr class="<?= $unread ? 'table-active' : '' ?>" style="<?= $unread ? 'font-weight: 600;' : '' ?>">
                                        <td class="text-center">
                                            <?php if ($unread): ?>
                                                <span class="badge bg-primary rounded-pill" title="Unread">&nbsp;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('Y-m-d H:i', strtotime($r['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($r['service']) ?></td>
                                        <td><?= htmlspecialchars($r['feedback_type']) ?></td>
                                        <td><?= htmlspecialchars($r['resolved']) ?></td>
                                        <td><?= fb_stars((int)$r['rating']) ?></td>
                                        <td>
                                            <?php if (!empty($r['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a>
                                            <?php else: ?>
                                                <span class="text-muted">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#viewModal<?= (int)$r['id'] ?>">
                                                View
                                            </button>
                                            <?php if ($unread): ?>
                                                <a href="index.php?page=customer_feedback.php&mark_read=<?= (int)$r['id'] ?>" class="btn btn-sm btn-outline-secondary">Mark read</a>
                                            <?php else: ?>
                                                <a href="index.php?page=customer_feedback.php&mark_read=<?= (int)$r['id'] ?>&unread=1" class="btn btn-sm btn-outline-secondary">Mark unread</a>
                                            <?php endif; ?>
                                            <a href="index.php?page=customer_feedback.php&delete=<?= (int)$r['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this submission?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the Customer Feedback form page.
        </p>

        <form method="POST">

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="customer_feedback_breadcrumb_title" class="form-control" value="<?= pc_h($pc['customer_feedback_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">"Home" link label</label>
                            <input type="text" name="customer_feedback_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['customer_feedback_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Parent label</label>
                            <input type="text" name="customer_feedback_breadcrumb_parent_label" class="form-control" value="<?= pc_h($pc['customer_feedback_breadcrumb_parent_label']) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Current page label</label>
                            <input type="text" name="customer_feedback_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['customer_feedback_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Intro Card</h5>
                    <div class="mb-0">
                        <label class="form-label">Intro body (separate paragraphs with a blank line)</label>
                        <textarea name="customer_feedback_intro_body" class="form-control" rows="4"><?= pc_h($pc['customer_feedback_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Form Strings</h5>
                    <div class="mb-3">
                        <label class="form-label">Submit button label</label>
                        <input type="text" name="customer_feedback_submit_button" class="form-control" value="<?= pc_h($pc['customer_feedback_submit_button']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Success message (shown after submission)</label>
                        <input type="text" name="customer_feedback_success_message" class="form-control" value="<?= pc_h($pc['customer_feedback_success_message']) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Fallback email address</label>
                        <input type="email" name="customer_feedback_fallback_email" class="form-control" value="<?= pc_h($pc['customer_feedback_fallback_email']) ?>">
                        <div class="form-text">Notifications are emailed here; also shown to users if the form fails to save.</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_customer_feedback_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- View Modals -->
<?php foreach ($rows as $r): ?>
    <div class="modal fade" id="viewModal<?= (int)$r['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?= htmlspecialchars($r['feedback_type'] ?: 'Submission') ?>
                        <small class="text-muted ms-2"><?= date('d M Y, H:i', strtotime($r['created_at'])) ?></small>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dl class="row mb-2">
                        <dt class="col-sm-4">Service</dt><dd class="col-sm-8"><?= htmlspecialchars($r['service']) ?></dd>
                        <dt class="col-sm-4">Type</dt><dd class="col-sm-8"><?= htmlspecialchars($r['feedback_type']) ?></dd>
                        <dt class="col-sm-4">Resolved?</dt><dd class="col-sm-8"><?= htmlspecialchars($r['resolved']) ?></dd>
                        <dt class="col-sm-4">Rating</dt><dd class="col-sm-8"><?= fb_stars((int)$r['rating']) ?></dd>
                        <dt class="col-sm-4">Email</dt><dd class="col-sm-8">
                            <?php if (!empty($r['email'])): ?>
                                <a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a>
                            <?php else: ?>
                                <span class="text-muted">Not provided</span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                    <hr>
                    <h6 class="fw-bold">Issue</h6>
                    <p style="white-space: pre-line;"><?= htmlspecialchars($r['issue']) ?></p>
                    <?php if (!empty($r['suggestion'])): ?>
                        <h6 class="fw-bold mt-3">Suggestion</h6>
                        <p style="white-space: pre-line;"><?= htmlspecialchars($r['suggestion']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <?php if (!(int)$r['is_read']): ?>
                        <a href="index.php?page=customer_feedback.php&mark_read=<?= (int)$r['id'] ?>" class="btn btn-outline-secondary">Mark Read</a>
                    <?php endif; ?>
                    <?php if (!empty($r['email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($r['email']) ?>" class="btn btn-primary">
                            <i class="fas fa-reply me-1"></i>Reply via email
                        </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

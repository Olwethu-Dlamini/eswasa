<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_faq.php';

// ── POST: save Page Content ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_faq_content'])) {
    $kv = [];
    foreach ($faq_keys as $k) {
        $kv[$k] = pc_post_value($k);
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=faq_edit.php&tab=content');
    exit;
}

// ── POST: CREATE / UPDATE FAQ ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = pc_strip_text($_POST['question'] ?? '');
    $answer = pc_strip_text($_POST['answer'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    $allowed_categories = ['training', 'standards', 'general'];
    if (!in_array($category, $allowed_categories, true)) $category = 'general';

    if (!$question || !$answer) {
        set_flash('danger', 'Question and Answer are required.');
        header('Location: index.php?page=faq_edit.php&tab=list' . ($id ? "&edit=$id" : ''));
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare('UPDATE eswasa_faq SET question = ?, answer = ?, category = ?, sort_order = ? WHERE id = ?');
        $stmt->bind_param('sssii', $question, $answer, $category, $sort_order, $id);
        $msg = 'FAQ updated.';
    } else {
        $stmt = $conn->prepare('INSERT INTO eswasa_faq (question, answer, category, sort_order) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('sssi', $question, $answer, $category, $sort_order);
        $msg = 'FAQ added.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('danger', 'Database error: ' . $conn->error);
    }
    if ($stmt) $stmt->close();
    header('Location: index.php?page=faq_edit.php&tab=list');
    exit;
}

// ── GET: DELETE ───────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM eswasa_faq WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'FAQ deleted.');
    header('Location: index.php?page=faq_edit.php&tab=list');
    exit;
}

// ── Load data ─────────────────────────────────────────────────────
$faqs = ['training' => [], 'standards' => [], 'general' => []];
$res = $conn->query('SELECT * FROM eswasa_faq ORDER BY category, sort_order ASC, id ASC');
while ($row = $res->fetch_assoc()) {
    if (isset($faqs[$row['category']])) {
        $faqs[$row['category']][] = $row;
    }
}

$edit_faq = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM eswasa_faq WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $edit_faq = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$pc = pc_get_many($conn, $faq_keys, $faq_defaults);

$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'list';
if ($edit_faq) $active_tab = 'list';

function faq_category_label_admin(string $c): string {
    return [
        'training'  => 'Training & Certification',
        'standards' => 'Standards & Certification',
        'general'   => 'General Information',
    ][$c] ?? ucfirst($c);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">FAQ</h1>
    <div class="d-flex gap-2">
        <a href="../faq.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
            + Add Question
        </button>
    </div>
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
        <button class="nav-link <?= $active_tab === 'list' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-list" type="button" role="tab">
            Questions
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

    <!-- ========== TAB: Questions ========== -->
    <div class="tab-pane fade <?= $active_tab === 'list' ? 'show active' : '' ?>" id="tab-list" role="tabpanel">

        <?php if ($edit_faq): ?>
            <div class="card mb-4">
                <div class="card-header">Edit Question</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= (int)$edit_faq['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Question *</label>
                            <input type="text" name="question" class="form-control" required
                                   value="<?= htmlspecialchars($edit_faq['question']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Answer *</label>
                            <textarea name="answer" class="form-control" rows="5" required><?= htmlspecialchars($edit_faq['answer']) ?></textarea>
                            <div class="form-text">Plain text. Line breaks are preserved on the public page.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category *</label>
                                <select name="category" class="form-select" required>
                                    <?php foreach (['training','standards','general'] as $opt): ?>
                                        <option value="<?= $opt ?>" <?= $edit_faq['category'] === $opt ? 'selected' : '' ?>>
                                            <?= faq_category_label_admin($opt) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= (int)$edit_faq['sort_order'] ?>">
                                <div class="form-text">Lower numbers appear first within a category.</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Question</button>
                            <a href="index.php?page=faq_edit.php&tab=list" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach (['training','standards','general'] as $cat_key): ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?= faq_category_label_admin($cat_key) ?> (<?= count($faqs[$cat_key]) ?>)</span>
                </div>
                <div class="card-body">
                    <?php if (empty($faqs[$cat_key])): ?>
                        <p class="text-muted mb-0">No questions in this category yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">Order</th>
                                        <th>Question</th>
                                        <th style="width: 160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faqs[$cat_key] as $item): ?>
                                        <tr>
                                            <td><?= (int)$item['sort_order'] ?></td>
                                            <td><?= htmlspecialchars($item['question']) ?></td>
                                            <td>
                                                <a href="index.php?page=faq_edit.php&tab=list&edit=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="index.php?page=faq_edit.php&delete=<?= (int)$item['id'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this FAQ?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the FAQ page (breadcrumb, intro card, category headings, contact box, empty-state message). The questions themselves are managed on the other tab.
        </p>

        <form method="POST">

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="faq_breadcrumb_title" class="form-control" value="<?= pc_h($pc['faq_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">"Home" link label</label>
                            <input type="text" name="faq_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['faq_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current page label</label>
                            <input type="text" name="faq_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['faq_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Intro Card</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="faq_intro_title" class="form-control" value="<?= pc_h($pc['faq_intro_title']) ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="faq_intro_body" class="form-control" rows="5"><?= pc_h($pc['faq_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Category Headings</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Training & Certification</label>
                            <input type="text" name="faq_category_training_title" class="form-control" value="<?= pc_h($pc['faq_category_training_title']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Standards & Certification</label>
                            <input type="text" name="faq_category_standards_title" class="form-control" value="<?= pc_h($pc['faq_category_standards_title']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">General Information</label>
                            <input type="text" name="faq_category_general_title" class="form-control" value="<?= pc_h($pc['faq_category_general_title']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Empty State</h5>
                    <div class="mb-0">
                        <label class="form-label">Message shown for any category that has no questions yet</label>
                        <input type="text" name="faq_category_empty_state" class="form-control" value="<?= pc_h($pc['faq_category_empty_state']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Contact Box (bottom of page)</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="faq_contact_title" class="form-control" value="<?= pc_h($pc['faq_contact_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <textarea name="faq_contact_body" class="form-control" rows="3"><?= pc_h($pc['faq_contact_body']) ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="faq_contact_phone" class="form-control" value="<?= pc_h($pc['faq_contact_phone']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="faq_contact_email" class="form-control" value="<?= pc_h($pc['faq_contact_email']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_faq_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFaqModalLabel">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Question *</label>
                        <input type="text" name="question" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Answer *</label>
                        <textarea name="answer" class="form-control" rows="5" required></textarea>
                        <div class="form-text">Plain text. Line breaks are preserved on the public page.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category *</label>
                            <select name="category" class="form-select" required>
                                <?php foreach (['training','standards','general'] as $opt): ?>
                                    <option value="<?= $opt ?>"><?= faq_category_label_admin($opt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                            <div class="form-text">Lower numbers appear first within a category.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

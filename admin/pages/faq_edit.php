<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$question || !$answer) {
        set_flash('error', 'Question and Answer are required.');
        header("Location: index.php?page=faq_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE eswasa_faq SET question = ?, answer = ?, category = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param('ssssi', $question, $answer, $category, $sort_order, $id);
        $msg = 'FAQ updated successfully.';
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO eswasa_faq (question, answer, category, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $question, $answer, $category, $sort_order);
        $msg = 'FAQ added successfully.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }
    $stmt->close();
    header("Location: index.php?page=faq_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM eswasa_faq WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    set_flash('success', 'FAQ deleted.');
    header("Location: index.php?page=faq_edit.php");
    exit;
}

// Fetch all FAQs grouped by category
$faqs = [
    'training' => [],
    'standards' => [],
    'general' => []
];
$result = $conn->query("SELECT * FROM eswasa_faq ORDER BY category, sort_order ASC");
while ($row = $result->fetch_assoc()) {
    $faqs[$row['category']][] = $row;
}

// Pre-fill for edit
$edit_faq = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_faq WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_faq = $result->fetch_assoc();
    $stmt->close();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage FAQs</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal">
        + Add FAQ
    </button>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFaqModalLabel">Add New FAQ</h5>
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
                        <textarea name="answer" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="training">Training & Certification</option>
                                <option value="standards">Standards & Certification</option>
                                <option value="general">General Information</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit FAQ Modal -->
<?php if ($edit_faq): ?>
<div class="modal fade show d-block" id="editFaqModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit FAQ</h5>
                <a href="index.php?page=faq_edit.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $edit_faq['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Question *</label>
                        <input type="text" name="question" class="form-control" required 
                               value="<?= htmlspecialchars($edit_faq['question']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Answer *</label>
                        <textarea name="answer" class="form-control" rows="4" required><?= htmlspecialchars($edit_faq['answer']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category *</label>
                            <select name="category" class="form-select" required>
                                <option value="training" <?= $edit_faq['category'] == 'training' ? 'selected' : '' ?>>Training & Certification</option>
                                <option value="standards" <?= $edit_faq['category'] == 'standards' ? 'selected' : '' ?>>Standards & Certification</option>
                                <option value="general" <?= $edit_faq['category'] == 'general' ? 'selected' : '' ?>>General Information</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $edit_faq['sort_order'] ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?page=faq_edit.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<!-- FAQ List by Category -->
<div class="card mb-4">
    <div class="card-header">
        Training & Certification FAQs (<?= count($faqs['training']) ?>)
    </div>
    <div class="card-body">
        <?= renderFaqTable($faqs['training']) ?>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Standards & Certification FAQs (<?= count($faqs['standards']) ?>)
    </div>
    <div class="card-body">
        <?= renderFaqTable($faqs['standards']) ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        General Information FAQs (<?= count($faqs['general']) ?>)
    </div>
    <div class="card-body">
        <?= renderFaqTable($faqs['general']) ?>
    </div>
</div>

<?php
function renderFaqTable($items) {
    if (empty($items)) {
        return '<p>No FAQs in this category.</p>';
    }
    $html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Question</th><th>Actions</th></tr></thead><tbody>';
    foreach ($items as $item) {
        $html .= '<tr>
            <td>' . htmlspecialchars($item['question']) . '</td>
            <td>
                <a href="index.php?page=faq_edit.php&edit=' . $item['id'] . '" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="index.php?page=faq_edit.php&delete=' . $item['id'] . '" 
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm(\'Delete this FAQ?\')">Delete</a>
            </td>
        </tr>';
    }
    $html .= '</tbody></table></div>';
    return $html;
}
?>
<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require __DIR__ . '/../../includes/cms_keys_vacancies.php';
$text_keys = $vacancies_keys;

// ── POST: save Page Content ────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vacancies_content'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header('Location: index.php?page=vacancies_edit.php&tab=content');
    exit;
}

// ── POST: CREATE / UPDATE vacancy ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = pc_strip_text($_POST['title'] ?? '');
    $location = pc_strip_text($_POST['location'] ?? '');
    $closing_date = $_POST['closing_date'] ?? '';
    $description = pc_strip_text($_POST['description'] ?? '');
    $responsibilities = pc_strip_text($_POST['responsibilities'] ?? '');
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$closing_date || !$description) {
        set_flash('error', 'Title, Closing Date, and Description are required.');
        header("Location: index.php?page=vacancies_edit.php&tab=positions" . ($id ? "&edit=$id" : ""));
        exit;
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE eswasa_vacancies SET title = ?, location = ?, closing_date = ?, description = ?, responsibilities = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $title, $location, $closing_date, $description, $responsibilities, $id);
        $msg = 'Vacancy updated successfully.';
    } else {
        $stmt = $conn->prepare("INSERT INTO eswasa_vacancies (title, location, closing_date, description, responsibilities) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $title, $location, $closing_date, $description, $responsibilities);
        $msg = 'Vacancy added successfully.';
    }

    if ($stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }
    $stmt->close();
    header("Location: index.php?page=vacancies_edit.php&tab=positions");
    exit;
}

// ── GET: DELETE ───────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM eswasa_vacancies WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        set_flash('success', 'Vacancy deleted successfully.');
    }
    $stmt->close();
    header("Location: index.php?page=vacancies_edit.php&tab=positions");
    exit;
}

// ── Load data ─────────────────────────────────────────────────
$vacancies = $conn->query("SELECT * FROM eswasa_vacancies ORDER BY closing_date ASC");

$edit_vacancy = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_vacancies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_vacancy = $result->fetch_assoc();
    $stmt->close();
}

$pc = pc_get_many($conn, $text_keys, $vacancies_defaults);

// Active tab — edit mode forces the positions tab so the user sees the prefilled form
$active_tab = ($_GET['tab'] ?? '') === 'content' ? 'content' : 'positions';
if ($edit_vacancy) $active_tab = 'positions';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vacancies</h1>
    <div class="d-flex gap-2">
        <a href="../vacancies.php" target="_blank" class="btn btn-sm btn-outline-secondary">View Page</a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVacancyModal">
            + Add Vacancy
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
        <button class="nav-link <?= $active_tab === 'positions' ? 'active' : '' ?>"
                data-bs-toggle="tab" data-bs-target="#tab-positions" type="button" role="tab">
            Open Positions
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

    <!-- ========== TAB: Open Positions ========== -->
    <div class="tab-pane fade <?= $active_tab === 'positions' ? 'show active' : '' ?>" id="tab-positions" role="tabpanel">

        <?php if ($edit_vacancy): ?>
            <div class="card mb-4">
                <div class="card-header">Edit Vacancy</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_vacancy['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Position Title *</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($edit_vacancy['title']) ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Location *</label>
                                <input type="text" name="location" class="form-control" required
                                       value="<?= htmlspecialchars($edit_vacancy['location']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Closing Date *</label>
                                <input type="date" name="closing_date" class="form-control" required
                                       value="<?= $edit_vacancy['closing_date'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description *</label>
                            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($edit_vacancy['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Key Responsibilities</label>
                            <textarea name="responsibilities" class="form-control" rows="4"><?= htmlspecialchars($edit_vacancy['responsibilities']) ?></textarea>
                            <div class="form-text">Optional.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Vacancy</button>
                            <a href="index.php?page=vacancies_edit.php&tab=positions" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                All Vacancies (<?= $vacancies->num_rows ?>)
            </div>
            <div class="card-body">
                <?php if ($vacancies->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Position</th>
                                    <th>Location</th>
                                    <th>Closing Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($v = $vacancies->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($v['title']) ?></td>
                                        <td><?= htmlspecialchars($v['location']) ?></td>
                                        <td><?= date('Y-m-d', strtotime($v['closing_date'])) ?></td>
                                        <td>
                                            <a href="index.php?page=vacancies_edit.php&tab=positions&edit=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="index.php?page=vacancies_edit.php&delete=<?= $v['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this vacancy?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No vacancies posted. <a href="#" data-bs-toggle="modal" data-bs-target="#addVacancyModal">Add your first vacancy</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ========== TAB: Page Content ========== -->
    <div class="tab-pane fade <?= $active_tab === 'content' ? 'show active' : '' ?>" id="tab-content" role="tabpanel">
        <p class="text-muted small mb-3">
            Edit the static text on the Vacancies page (breadcrumb, intro card, section heading, "How to Apply" card, empty state). Open positions are managed on the other tab.
        </p>

        <form method="POST">

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Breadcrumb</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Page Title (banner heading)</label>
                            <input type="text" name="vacancies_breadcrumb_title" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_title']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">"Home" link label</label>
                            <input type="text" name="vacancies_breadcrumb_home_label" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_home_label']) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current page label</label>
                            <input type="text" name="vacancies_breadcrumb_current_label" class="form-control" value="<?= pc_h($pc['vacancies_breadcrumb_current_label']) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Intro Card ("Working at ESWASA")</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="vacancies_intro_title" class="form-control" value="<?= pc_h($pc['vacancies_intro_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="vacancies_intro_body" class="form-control" rows="6"><?= pc_h($pc['vacancies_intro_body']) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Section Heading</h5>
                    <div class="mb-0">
                        <label class="form-label">Heading above the vacancy cards</label>
                        <input type="text" name="vacancies_section_title" class="form-control" value="<?= pc_h($pc['vacancies_section_title']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Empty State</h5>
                    <div class="mb-0">
                        <label class="form-label">Message shown when no vacancies are open</label>
                        <input type="text" name="vacancies_empty_state" class="form-control" value="<?= pc_h($pc['vacancies_empty_state']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">"How to Apply" Card</h5>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="vacancies_apply_title" class="form-control" value="<?= pc_h($pc['vacancies_apply_title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Body (separate paragraphs with a blank line)</label>
                        <textarea name="vacancies_apply_body" class="form-control" rows="6"><?= pc_h($pc['vacancies_apply_body']) ?></textarea>
                        <div class="form-text">
                            Use <code>[email]</code> anywhere in the body — it will be replaced with a clickable mailto link to the HR email below.
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">HR email address</label>
                        <input type="email" name="vacancies_hr_email" class="form-control" value="<?= pc_h($pc['vacancies_hr_email']) ?>">
                        <div class="form-text">Used for the <code>[email]</code> placeholder above and for the "Apply for this Position" button inside the vacancy modal.</div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" name="save_vacancies_content" class="btn btn-primary px-5 shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Page Content
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Add Vacancy Modal -->
<div class="modal fade" id="addVacancyModal" tabindex="-1" aria-labelledby="addVacancyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVacancyModalLabel">Add New Vacancy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Position Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Location *</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Closing Date *</label>
                            <input type="date" name="closing_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Overview of the role..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Key Responsibilities</label>
                        <textarea name="responsibilities" class="form-control" rows="4" placeholder="List responsibilities, one per line..."></textarea>
                        <div class="form-text">Optional. Will be displayed as a bullet list on the public page.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Vacancy</button>
                </div>
            </form>
        </div>
    </div>
</div>

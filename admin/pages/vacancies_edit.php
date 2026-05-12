<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle form submission (CREATE / UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $closing_date = $_POST['closing_date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $responsibilities = trim($_POST['responsibilities'] ?? '');
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$closing_date || !$description) {
        set_flash('error', 'Title, Closing Date, and Description are required.');
        header("Location: index.php?page=vacancies_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }

    if ($id) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE eswasa_vacancies SET title = ?, location = ?, closing_date = ?, description = ?, responsibilities = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $title, $location, $closing_date, $description, $responsibilities, $id);
        $msg = 'Vacancy updated successfully.';
    } else {
        // INSERT
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
    header("Location: index.php?page=vacancies_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM eswasa_vacancies WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        set_flash('success', 'Vacancy deleted successfully.');
    }
    $stmt->close();
    header("Location: index.php?page=vacancies_edit.php");
    exit;
}

// Fetch all vacancies
$vacancies = $conn->query("SELECT * FROM eswasa_vacancies ORDER BY closing_date ASC");

// Pre-fill for edit
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
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Vacancies</h1>
    <!-- Add Vacancy Button (opens modal) -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVacancyModal">
        + Add Vacancy
    </button>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

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

<!-- Edit Form (only shown when editing) -->
<?php if ($edit_vacancy): ?>
    <div class="card mb-4">
        <div class="card-header">
            Edit Vacancy
        </div>
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
                    <a href="index.php?page=vacancies_edit.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Vacancies List (always visible) -->
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
                                    <a href="index.php?page=vacancies_edit.php&edit=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
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
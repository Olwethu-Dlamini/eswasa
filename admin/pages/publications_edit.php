<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle form submission (CREATE / UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $pub_type = $_POST['pub_type'] ?? 'report';
    $published_date = $_POST['published_date'] ?? '';
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$published_date || !$description) {
        set_flash('error', 'Title, Description, and Published Date are required.');
        header("Location: index.php?page=publications_edit.php");
        exit;
    }

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = __DIR__ . '/../uploads/publications/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['file']['name']);
        $target_file = $upload_dir . $file_name;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['pdf'];

        if (in_array($fileType, $allowed)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = 'publications/' . $file_name;
            } else {
                set_flash('error', 'Error uploading PDF.');
                header("Location: index.php?page=publications_edit.php");
                exit;
            }
        } else {
            set_flash('error', 'Only PDF files are allowed.');
            header("Location: index.php?page=publications_edit.php");
            exit;
        }
    }

    if ($id) {
        // UPDATE
        if ($file_path) {
            $stmt = $conn->prepare("UPDATE eswasa_publications SET title = ?, description = ?, pub_type = ?, file_path = ?, published_date = ? WHERE id = ?");
            $stmt->bind_param('sssssi', $title, $description, $pub_type, $file_path, $published_date, $id);
        } else {
            $stmt = $conn->prepare("UPDATE eswasa_publications SET title = ?, description = ?, pub_type = ?, published_date = ? WHERE id = ?");
            $stmt->bind_param('ssssi', $title, $description, $pub_type, $published_date, $id);
        }
        $msg = 'Publication updated successfully.';
    } else {
        // INSERT
        if (!$file_path) {
            set_flash('error', 'PDF file is required.');
            header("Location: index.php?page=publications_edit.php");
            exit;
        }
        $stmt = $conn->prepare("INSERT INTO eswasa_publications (title, description, pub_type, file_path, published_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $title, $description, $pub_type, $file_path, $published_date);
        $msg = 'Publication added successfully.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }
    $stmt->close();
    header("Location: index.php?page=publications_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT file_path FROM eswasa_publications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && !empty($row['file_path'])) {
        $file_to_delete = __DIR__ . '/../uploads/' . $row['file_path'];
        if (file_exists($file_to_delete)) {
            unlink($file_to_delete);
        }
    }

    $del = $conn->prepare("DELETE FROM eswasa_publications WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    set_flash('success', 'Publication deleted.');
    header("Location: index.php?page=publications_edit.php");
    exit;
}

// Fetch all publications
$publications = $conn->query("SELECT * FROM eswasa_publications ORDER BY published_date DESC");

// Pre-fill for edit
$edit_pub = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_publications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_pub = $result->fetch_assoc();
    $stmt->close();
}

function getTypeLabel($type) {
    $labels = [
        'standard' => 'Standard',
        'report' => 'Report',
        'guidance' => 'Guidance Document',
        'newsletter' => 'Newsletter',
        'annual_report' => 'Annual Report'
    ];
    return $labels[$type] ?? ucfirst($type);
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Publications</h1>
    <!-- Add Publication Button (opens modal) -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPublicationModal">
        + Add Publication
    </button>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Add Publication Modal -->
<div class="modal fade" id="addPublicationModal" tabindex="-1" aria-labelledby="addPublicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPublicationModalLabel">Add New Publication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="pub_type" class="form-select" required>
                                <option value="standard">Standard</option>
                                <option value="report">Report</option>
                                <option value="guidance">Guidance Document</option>
                                <option value="newsletter">Newsletter</option>
                                <option value="annual_report">Annual Report</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">PDF File *</label>
                        <input type="file" name="file" class="form-control" accept=".pdf" required>
                        <div class="form-text">Only PDF files allowed.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Publication Modal (if editing) -->
<?php if ($edit_pub): ?>
<div class="modal fade show d-block" id="editPublicationModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Publication</h5>
                <a href="index.php?page=publications_edit.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $edit_pub['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" class="form-control" required 
                               value="<?= htmlspecialchars($edit_pub['title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($edit_pub['description']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="pub_type" class="form-select" required>
                                <option value="standard" <?= $edit_pub['pub_type'] == 'standard' ? 'selected' : '' ?>>Standard</option>
                                <option value="report" <?= $edit_pub['pub_type'] == 'report' ? 'selected' : '' ?>>Report</option>
                                <option value="guidance" <?= $edit_pub['pub_type'] == 'guidance' ? 'selected' : '' ?>>Guidance Document</option>
                                <option value="newsletter" <?= $edit_pub['pub_type'] == 'newsletter' ? 'selected' : '' ?>>Newsletter</option>
                                <option value="annual_report" <?= $edit_pub['pub_type'] == 'annual_report' ? 'selected' : '' ?>>Annual Report</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required
                                   value="<?= $edit_pub['published_date'] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">PDF File</label>
                        <input type="file" name="file" class="form-control" accept=".pdf">
                        <?php if (!empty($edit_pub['file_path'])): ?>
                            <div class="mt-2">
                                <a href="uploads/<?= htmlspecialchars($edit_pub['file_path']) ?>" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary">
                                    View Current PDF
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Leave blank to keep current file.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?page=publications_edit.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Publication</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<!-- Publications List -->
<div class="card">
    <div class="card-header">
        All Publications (<?= $publications->num_rows ?>)
    </div>
    <div class="card-body">
        <?php if ($publications->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Published</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($p = $publications->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['title']) ?></td>
                                <td><?= getTypeLabel($p['pub_type']) ?></td>
                                <td><?= date('Y-m-d', strtotime($p['published_date'])) ?></td>
                                <td>
                                    <?php if (!empty($p['file_path'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($p['file_path']) ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-secondary">
                                            View PDF
                                        </a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=publications_edit.php&edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="index.php?page=publications_edit.php&delete=<?= $p['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this publication?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No publications found.</p>
        <?php endif; ?>
    </div>
</div>
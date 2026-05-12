<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $announcement_type = $_POST['announcement_type'] ?? 'news';
    $published_date = $_POST['published_date'] ?? '';
    $external_link = trim($_POST['external_link'] ?? '');
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    if (!$title || !$published_date || !$description) {
        set_flash('error', 'Title, Description, and Published Date are required.');
        header("Location: index.php?page=announcements_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }

    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = __DIR__ . '/../uploads/announcements/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['file']['name']);
        $target_file = $upload_dir . $file_name;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileType, $allowed)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $file_path = 'announcements/' . $file_name;
            } else {
                set_flash('error', 'Error uploading file.');
                header("Location: index.php?page=announcements_edit.php" . ($id ? "&edit=$id" : ""));
                exit;
            }
        } else {
            set_flash('error', 'Only PDF, JPG, JPEG, PNG, and WEBP files are allowed.');
            header("Location: index.php?page=announcements_edit.php" . ($id ? "&edit=$id" : ""));
            exit;
        }
    }

    if ($id) {
        // UPDATE
        if ($file_path) {
            $stmt = $conn->prepare("UPDATE eswasa_announcements SET title = ?, description = ?, announcement_type = ?, published_date = ?, external_link = ?, file_path = ? WHERE id = ?");
            $stmt->bind_param('ssssssi', $title, $description, $announcement_type, $published_date, $external_link, $file_path, $id);
        } else {
            $stmt = $conn->prepare("UPDATE eswasa_announcements SET title = ?, description = ?, announcement_type = ?, published_date = ?, external_link = ? WHERE id = ?");
            $stmt->bind_param('sssssi', $title, $description, $announcement_type, $published_date, $external_link, $id);
        }
        $msg = 'Announcement updated successfully.';
    } else {
        // INSERT
        $stmt = $conn->prepare("INSERT INTO eswasa_announcements (title, description, announcement_type, published_date, external_link, file_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssss', $title, $description, $announcement_type, $published_date, $external_link, $file_path);
        $msg = 'Announcement added successfully.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $msg);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }
    $stmt->close();
    header("Location: index.php?page=announcements_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT file_path FROM eswasa_announcements WHERE id = ?");
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

    $del = $conn->prepare("DELETE FROM eswasa_announcements WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    set_flash('success', 'Announcement deleted.');
    header("Location: index.php?page=announcements_edit.php");
    exit;
}

// Fetch all announcements
$announcements = $conn->query("SELECT * FROM eswasa_announcements ORDER BY published_date DESC");

// Pre-fill for edit
$edit_ann = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_ann = $result->fetch_assoc();
    $stmt->close();
}

// Helper: Get type label and class
function getTypeInfo($type) {
    $types = [
        'news' => ['label' => 'News', 'class' => 'type-news'],
        'update' => ['label' => 'Update', 'class' => 'type-update'],
        'closure' => ['label' => 'Closure', 'class' => 'type-closure'],
        'event' => ['label' => 'Event', 'class' => 'type-event'],
        'policy' => ['label' => 'Policy', 'class' => 'type-policy']
    ];
    return $types[$type] ?? ['label' => ucfirst($type), 'class' => 'type-' . $type];
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Announcements</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
        + Add Announcement
    </button>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Add Announcement Modal -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAnnouncementModalLabel">Add New Announcement</h5>
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
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="announcement_type" class="form-select" required>
                                <option value="news">News</option>
                                <option value="update">Update</option>
                                <option value="closure">Closure</option>
                                <option value="event">Event</option>
                                <option value="policy">Policy</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">External Link (Optional)</label>
                        <input type="url" name="external_link" class="form-control" placeholder="https://example.com">
                        <div class="form-text">Leave blank if linking to an uploaded file.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Upload (Optional)</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <div class="form-text">PDF, JPG, PNG, WEBP allowed. Max size: server default.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Announcement Modal -->
<?php if ($edit_ann): ?>
<div class="modal fade show d-block" id="editAnnouncementModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Announcement</h5>
                <a href="index.php?page=announcements_edit.php" class="btn-close" aria-label="Close"></a>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $edit_ann['id'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title *</label>
                        <input type="text" name="title" class="form-control" required 
                               value="<?= htmlspecialchars($edit_ann['title']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($edit_ann['description']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type *</label>
                            <select name="announcement_type" class="form-select" required>
                                <option value="news" <?= $edit_ann['announcement_type'] == 'news' ? 'selected' : '' ?>>News</option>
                                <option value="update" <?= $edit_ann['announcement_type'] == 'update' ? 'selected' : '' ?>>Update</option>
                                <option value="closure" <?= $edit_ann['announcement_type'] == 'closure' ? 'selected' : '' ?>>Closure</option>
                                <option value="event" <?= $edit_ann['announcement_type'] == 'event' ? 'selected' : '' ?>>Event</option>
                                <option value="policy" <?= $edit_ann['announcement_type'] == 'policy' ? 'selected' : '' ?>>Policy</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Published Date *</label>
                            <input type="date" name="published_date" class="form-control" required
                                   value="<?= $edit_ann['published_date'] ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">External Link (Optional)</label>
                        <input type="url" name="external_link" class="form-control" 
                               value="<?= htmlspecialchars($edit_ann['external_link']) ?>" 
                               placeholder="https://example.com">
                        <div class="form-text">Leave blank if linking to an uploaded file.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Upload</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        <?php if (!empty($edit_ann['file_path'])): ?>
                            <div class="mt-2">
                                <a href="admin/uploads/<?= htmlspecialchars($edit_ann['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    View Current File
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">Leave blank to keep current file.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?page=announcements_edit.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal-backdrop fade show"></div>
<?php endif; ?>

<!-- Announcements List -->
<div class="card">
    <div class="card-header">
        All Announcements (<?= $announcements->num_rows ?>)
    </div>
    <div class="card-body">
        <?php if ($announcements->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Published</th>
                            <th>File / Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($a = $announcements->fetch_assoc()): 
                            $typeInfo = getTypeInfo($a['announcement_type']);
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($a['title']) ?></td>
                                <td><span class="announcement-type <?= $typeInfo['class'] ?>"><?= $typeInfo['label'] ?></span></td>
                                <td><?= date('Y-m-d', strtotime($a['published_date'])) ?></td>
                                <td>
                                    <?php if (!empty($a['file_path'])): ?>
                                        <a href="admin/uploads/<?= htmlspecialchars($a['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">View File</a>
                                    <?php elseif (!empty($a['external_link'])): ?>
                                        <a href="<?= htmlspecialchars($a['external_link']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">External Link</a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?page=announcements_edit.php&edit=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="index.php?page=announcements_edit.php&delete=<?= $a['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this announcement?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No announcements posted.</p>
        <?php endif; ?>
    </div>
</div>
<?php
// Prevent direct access
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle form submission (CREATE / UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $category = $_POST['category'] ?? 'workshop';
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    // Validate required fields
    if (!$title || !$event_date) {
        set_flash('error', 'Title and Date are required.');
        header("Location: index.php?page=events_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }

    $image = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $file_name;
            } else {
                set_flash('error', 'Error uploading image. Check folder permissions.');
                header("Location: index.php?page=events_edit.php" . ($id ? "&edit=$id" : ""));
                exit;
            }
        } else {
            set_flash('error', 'Only JPG, JPEG, PNG & WEBP files are allowed.');
            header("Location: index.php?page=events_edit.php" . ($id ? "&edit=$id" : ""));
            exit;
        }
    }

    // Save to database
    if ($id) {
        // UPDATE
        if ($image !== null) {
            $stmt = $conn->prepare("UPDATE eswasa_events SET title = ?, description = ?, location = ?, event_date = ?, category = ?, image = ? WHERE id = ?");
            $stmt->bind_param('ssssssi', $title, $description, $location, $event_date, $category, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE eswasa_events SET title = ?, description = ?, location = ?, event_date = ?, category = ? WHERE id = ?");
            $stmt->bind_param('sssssi', $title, $description, $location, $event_date, $category, $id);
        }
        $message = 'Event updated successfully.';
    } else {
        // INSERT
        if ($image !== null) {
            $stmt = $conn->prepare("INSERT INTO eswasa_events (title, description, location, event_date, category, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $title, $description, $location, $event_date, $category, $image);
        } else {
            $stmt = $conn->prepare("INSERT INTO eswasa_events (title, description, location, event_date, category) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssss', $title, $description, $location, $event_date, $category);
        }
        $message = 'Event added successfully.';
    }

    if ($stmt && $stmt->execute()) {
        set_flash('success', $message);
    } else {
        set_flash('error', 'Database error: ' . $conn->error);
    }

    $stmt->close();
    header("Location: index.php?page=events_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM eswasa_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        set_flash('success', 'Event deleted successfully.');
    } else {
        set_flash('error', 'Failed to delete event.');
    }
    $stmt->close();
    header("Location: index.php?page=events_edit.php");
    exit;
}

// Fetch all events
$events_result = $conn->query("SELECT * FROM eswasa_events ORDER BY event_date DESC");

// Pre-fill for edit
$edit_event = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_event = $result->fetch_assoc();
    $stmt->close();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Manage Events</h1>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Add/Edit Form -->
<div class="card mb-4">
    <div class="card-header">
        <?= $edit_event ? 'Edit Event' : 'Add New Event' ?>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit_event['id'] ?? '' ?>">

            <div class="mb-3">
                <label class="form-label fw-bold">Event Title *</label>
                <input type="text" name="title" class="form-control" required 
                       value="<?= htmlspecialchars($edit_event['title'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($edit_event['description'] ?? '') ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Location</label>
                    <input type="text" name="location" class="form-control" 
                           value="<?= htmlspecialchars($edit_event['location'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Date *</label>
                    <input type="date" name="event_date" class="form-control" required 
                           value="<?= $edit_event['event_date'] ?? '' ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Category</label>
                <select name="category" class="form-select">
                    <option value="training" <?= ($edit_event['category'] ?? '') == 'training' ? 'selected' : '' ?>>Training</option>
                    <option value="workshop" <?= ($edit_event['category'] ?? '') == 'workshop' ? 'selected' : '' ?>>Workshop</option>
                    <option value="conference" <?= ($edit_event['category'] ?? '') == 'conference' ? 'selected' : '' ?>>Conference</option>
                    <option value="seminar" <?= ($edit_event['category'] ?? '') == 'seminar' ? 'selected' : '' ?>>Seminar</option>
                    <option value="webinar" <?= ($edit_event['category'] ?? '') == 'webinar' ? 'selected' : '' ?>>Webinar</option>
                    <option value="tc_meeting" <?= ($edit_event['category'] ?? '') == 'tc_meeting' ? 'selected' : '' ?>>Technical Committee Meeting</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Image (Optional)</label>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <?php if ($edit_event && !empty($edit_event['image'])): ?>
                    <div class="mt-2">
                        <img src="uploads/<?= htmlspecialchars($edit_event['image']) ?>" 
                             width="100" 
                             alt="Current event image"
                             onerror="this.src='assets/img/default-thumb.jpg'; this.onerror=null;">
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary"><?= $edit_event ? 'Update Event' : 'Add Event' ?></button>
            <?php if ($edit_event): ?>
                <a href="index.php?page=events_edit.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Events List -->
<div class="card">
    <div class="card-header">
        All Events (<?= $events_result->num_rows ?>)
    </div>
    <div class="card-body">
        <?php if ($events_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $events_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= date('d M, Y', strtotime($event['event_date'])) ?></td>
                                <td><?= htmlspecialchars($event['location']) ?></td>
                                <td><?= ucfirst(htmlspecialchars($event['category'])) ?></td>
                                <td>
                                    <a href="index.php?page=events_edit.php&edit=<?= $event['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="index.php?page=events_edit.php&delete=<?= $event['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</div>
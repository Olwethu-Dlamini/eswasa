<?php
// Prevent direct access
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
require_once __DIR__ . '/../../includes/cms_helpers.php';
require_once __DIR__ . '/../../includes/event_images.php';

eswasa_ensure_event_images_table($conn);
$MAX_IMAGES = eswasa_event_images_max();

$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}
$allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

/**
 * Save a single $_FILES entry into uploads/, returning the stored filename
 * (or null on failure). Names are made unique to avoid collisions.
 */
function eswasa_save_event_image_upload(array $file, string $upload_dir, array $allowed_ext): ?string {
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return null;
    }
    $orig = basename($file['name']);
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) return null;

    $stem = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($orig, PATHINFO_FILENAME));
    if ($stem === '') $stem = 'event';
    $stem = substr($stem, 0, 40);
    $unique = $stem . '_' . date('Ymd_His') . '_' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;
    $target = $upload_dir . $unique;

    if (!move_uploaded_file($file['tmp_name'], $target)) return null;
    return $unique;
}

// Handle DELETE a single gallery image (admin remove button)
if (isset($_GET['delete_image']) && isset($_GET['edit'])) {
    $img_id = (int)$_GET['delete_image'];
    $event_id = (int)$_GET['edit'];

    if ($stmt = $conn->prepare("SELECT image FROM eswasa_event_images WHERE id = ? AND event_id = ?")) {
        $stmt->bind_param('ii', $img_id, $event_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $filename = (string)$row['image'];
            if ($d = $conn->prepare("DELETE FROM eswasa_event_images WHERE id = ? AND event_id = ?")) {
                $d->bind_param('ii', $img_id, $event_id);
                $d->execute();
                $d->close();
            }
            // Best-effort file removal — only if no other event references it
            $still_used = false;
            if ($u = $conn->prepare("SELECT 1 FROM eswasa_event_images WHERE image = ? LIMIT 1")) {
                $u->bind_param('s', $filename);
                $u->execute();
                $still_used = (bool)$u->get_result()->fetch_assoc();
                $u->close();
            }
            if (!$still_used) {
                @unlink($upload_dir . $filename);
            }
            // If the event's `image` cover pointed at the deleted file, promote the next gallery image
            if ($c = $conn->prepare("SELECT image FROM eswasa_events WHERE id = ?")) {
                $c->bind_param('i', $event_id);
                $c->execute();
                $cr = $c->get_result()->fetch_assoc();
                $c->close();
                if ($cr && $cr['image'] === $filename) {
                    $new_cover = '';
                    if ($n = $conn->prepare("SELECT image FROM eswasa_event_images WHERE event_id = ? ORDER BY sort_order ASC, id ASC LIMIT 1")) {
                        $n->bind_param('i', $event_id);
                        $n->execute();
                        $nr = $n->get_result()->fetch_assoc();
                        $n->close();
                        if ($nr) $new_cover = (string)$nr['image'];
                    }
                    if ($up = $conn->prepare("UPDATE eswasa_events SET image = ? WHERE id = ?")) {
                        $up->bind_param('si', $new_cover, $event_id);
                        $up->execute();
                        $up->close();
                    }
                }
            }
            set_flash('success', 'Image removed.');
        }
    }
    header("Location: index.php?page=events_edit.php&edit=$event_id");
    exit;
}

// Handle form submission (CREATE / UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = pc_strip_text($_POST['title'] ?? '');
    $description = pc_strip_text($_POST['description'] ?? '');
    $location = pc_strip_text($_POST['location'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $category = $_POST['category'] ?? 'workshop';
    $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;

    // Validate required fields
    if (!$title || !$event_date) {
        set_flash('error', 'Title and Date are required.');
        header("Location: index.php?page=events_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }

    // Save (insert/update) the row first so we have an event id for image rows.
    if ($id) {
        $stmt = $conn->prepare("UPDATE eswasa_events SET title = ?, description = ?, location = ?, event_date = ?, category = ? WHERE id = ?");
        $stmt->bind_param('sssssi', $title, $description, $location, $event_date, $category, $id);
        $message = 'Event updated successfully.';
    } else {
        $stmt = $conn->prepare("INSERT INTO eswasa_events (title, description, location, event_date, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $title, $description, $location, $event_date, $category);
        $message = 'Event added successfully.';
    }

    if (!$stmt || !$stmt->execute()) {
        set_flash('error', 'Database error: ' . $conn->error);
        header("Location: index.php?page=events_edit.php" . ($id ? "&edit=$id" : ""));
        exit;
    }
    if (!$id) $id = (int)$conn->insert_id;
    $stmt->close();

    // Process gallery uploads (multi-file). Cap at $MAX_IMAGES total.
    $skipped_over_cap = 0;
    $invalid_count = 0;
    $existing_count = eswasa_count_event_images($conn, $id);
    $remaining_slots = max(0, $MAX_IMAGES - $existing_count);

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        // Find current max sort_order to append after it
        $next_order = 0;
        if ($r = $conn->query("SELECT COALESCE(MAX(sort_order), -1) + 1 AS n FROM eswasa_event_images WHERE event_id = " . (int)$id)) {
            $next_order = (int)($r->fetch_assoc()['n'] ?? 0);
        }

        $names = $_FILES['images']['name'];
        $count = count($names);
        for ($i = 0; $i < $count; $i++) {
            $f = [
                'name' => $_FILES['images']['name'][$i] ?? '',
                'type' => $_FILES['images']['type'][$i] ?? '',
                'tmp_name' => $_FILES['images']['tmp_name'][$i] ?? '',
                'error' => $_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES['images']['size'][$i] ?? 0,
            ];
            if (empty($f['name'])) continue;

            if ($remaining_slots <= 0) { $skipped_over_cap++; continue; }

            $saved = eswasa_save_event_image_upload($f, $upload_dir, $allowed_ext);
            if (!$saved) { $invalid_count++; continue; }

            if ($ins = $conn->prepare("INSERT INTO eswasa_event_images (event_id, image, sort_order) VALUES (?, ?, ?)")) {
                $ins->bind_param('isi', $id, $saved, $next_order);
                $ins->execute();
                $ins->close();
                $next_order++;
                $remaining_slots--;

                // First-ever image of this event becomes the cover (eswasa_events.image)
                $current_cover = '';
                if ($c = $conn->prepare("SELECT image FROM eswasa_events WHERE id = ?")) {
                    $c->bind_param('i', $id);
                    $c->execute();
                    $cr = $c->get_result()->fetch_assoc();
                    $c->close();
                    if ($cr) $current_cover = (string)$cr['image'];
                }
                if ($current_cover === '') {
                    if ($up = $conn->prepare("UPDATE eswasa_events SET image = ? WHERE id = ?")) {
                        $up->bind_param('si', $saved, $id);
                        $up->execute();
                        $up->close();
                    }
                }
            } else {
                // DB insert failed — remove the orphan file
                @unlink($upload_dir . $saved);
                $invalid_count++;
            }
        }
    }

    $notes = [];
    if ($skipped_over_cap > 0) $notes[] = "$skipped_over_cap image(s) skipped — max $MAX_IMAGES per event.";
    if ($invalid_count > 0)    $notes[] = "$invalid_count image(s) rejected (wrong type or upload failed).";
    if ($notes) {
        set_flash('warning', $message . ' ' . implode(' ', $notes));
    } else {
        set_flash('success', $message);
    }

    header("Location: index.php?page=events_edit.php&edit=$id");
    exit;
}

// Handle DELETE event
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Pull file list first so we can clean up disk after FK cascade
    $files = [];
    if ($r = $conn->query("SELECT image FROM eswasa_event_images WHERE event_id = $id")) {
        while ($row = $r->fetch_assoc()) $files[] = (string)$row['image'];
    }
    if ($c = $conn->prepare("SELECT image FROM eswasa_events WHERE id = ?")) {
        $c->bind_param('i', $id);
        $c->execute();
        $cr = $c->get_result()->fetch_assoc();
        $c->close();
        if ($cr && !empty($cr['image'])) $files[] = (string)$cr['image'];
    }

    $stmt = $conn->prepare("DELETE FROM eswasa_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        set_flash('success', 'Event deleted successfully.');
        foreach (array_unique($files) as $fn) {
            // Only delete the file if nothing else references it
            if ($u = $conn->prepare("SELECT 1 FROM eswasa_event_images WHERE image = ? LIMIT 1")) {
                $u->bind_param('s', $fn);
                $u->execute();
                $used = (bool)$u->get_result()->fetch_assoc();
                $u->close();
                if (!$used) @unlink($upload_dir . $fn);
            }
        }
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
$edit_images = [];
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_event = $result->fetch_assoc();
    $stmt->close();
    if ($edit_event) {
        $edit_images = eswasa_get_event_images($conn, (int)$edit_event['id']);
    }
}
$existing_count = $edit_event ? count($edit_images) : 0;
$remaining = max(0, $MAX_IMAGES - $existing_count);
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
                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                    <span>Event Images (up to <?= $MAX_IMAGES ?>) </span>
                    <?php if ($edit_event): ?>
                        <small class="text-muted fw-normal"><?= $existing_count ?> / <?= $MAX_IMAGES ?> used &middot; <?= $remaining ?> slot<?= $remaining === 1 ? '' : 's' ?> left</small>
                    <?php endif; ?>
                </label>

                <?php if (!empty($edit_images)): ?>
                    <div class="d-flex flex-wrap gap-2 mb-2" style="border:1px solid #e5e7eb; border-radius:4px; padding:10px; background:#fafafa;">
                        <?php foreach ($edit_images as $i => $img): ?>
                            <div class="position-relative" style="width:110px;">
                                <img src="uploads/<?= htmlspecialchars($img['image']) ?>"
                                     alt="Image <?= $i + 1 ?>"
                                     style="width:110px; height:80px; object-fit:cover; border:1px solid #ddd; border-radius:3px;"
                                     onerror="this.src='../assets/img/default-thumb.jpg'; this.onerror=null;">
                                <?php if (!empty($img['id'])): ?>
                                    <a href="index.php?page=events_edit.php&edit=<?= (int)$edit_event['id'] ?>&delete_image=<?= (int)$img['id'] ?>"
                                       onclick="return confirm('Remove this image?')"
                                       class="btn btn-sm btn-danger"
                                       style="position:absolute; top:4px; right:4px; padding:2px 6px; line-height:1; font-size:0.75rem;"
                                       title="Remove image">&times;</a>
                                <?php else: ?>
                                    <span class="badge bg-secondary" style="position:absolute; top:4px; right:4px; font-size:0.65rem;" title="Legacy cover (will move into gallery after re-save)">legacy</span>
                                <?php endif; ?>
                                <?php if ($i === 0): ?>
                                    <span class="badge" style="position:absolute; bottom:4px; left:4px; background:#2B3388; font-size:0.65rem;">Cover</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple
                       <?= ($edit_event && $remaining <= 0) ? 'disabled' : '' ?>>
                <div class="form-text">
                    <?php if ($edit_event && $remaining <= 0): ?>
                        Gallery is full. Remove an image to free a slot.
                    <?php else: ?>
                        JPG/PNG/WEBP. First image becomes the cover. Hold Ctrl/Cmd or Shift to pick multiple files.
                    <?php endif; ?>
                </div>
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
                            <th>Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $events_result->fetch_assoc()): ?>
                            <?php $img_count = eswasa_count_event_images($conn, (int)$event['id']); ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($event['title'] ?? '')) ?></td>
                                <td><?= date('d M, Y', strtotime($event['event_date'])) ?></td>
                                <td><?= htmlspecialchars((string)($event['location'] ?? '')) ?></td>
                                <td><?= ucfirst(htmlspecialchars((string)($event['category'] ?? ''))) ?></td>
                                <td>
                                    <span class="badge bg-<?= $img_count > 0 ? 'primary' : 'secondary' ?>"><?= $img_count ?>/<?= $MAX_IMAGES ?></span>
                                </td>
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

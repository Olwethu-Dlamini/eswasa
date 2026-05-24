<?php
// admin/pages/board_edit.php — CRUD for eswasa_board_members + page hero/intro.
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ---- Page header keys (flat key-value) ----
$text_keys = ['board_hero_title', 'board_intro_body'];

// ---- Action router ----
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Save page hero/intro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_board'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page text saved.');
    redirect_self();
}

// Add member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $name            = pc_strip_text($_POST['name'] ?? '');
    $role            = pc_strip_text($_POST['role'] ?? '');
    $bio             = pc_strip_text($_POST['bio'] ?? '');
    $social_linkedin = pc_strip_text($_POST['social_linkedin'] ?? '');
    $sort_order      = (int)($_POST['sort_order'] ?? 0);

    if ($name === '' || $role === '') {
        set_flash('danger', 'Name and role are required.');
        redirect_self();
    }

    $photo = pc_upload_image('photo_file', ADMIN_ROOT . '/uploads/', 'board');
    if ($photo === null) $photo = '';

    $stmt = $conn->prepare("INSERT INTO eswasa_board_members (name, role, photo, bio, social_linkedin, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('sssssi', $name, $role, $photo, $bio, $social_linkedin, $sort_order);
        if ($stmt->execute()) {
            set_flash('success', 'Board member added.');
        } else {
            set_flash('danger', 'Create failed: ' . ($conn->error ?? 'unknown error'));
        }
        $stmt->close();
    } else {
        set_flash('danger', 'Create failed: ' . ($conn->error ?? 'prepare failed'));
    }
    redirect_self();
}

// Update member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    $id              = (int)($_POST['id'] ?? 0);
    $name            = pc_strip_text($_POST['name'] ?? '');
    $role            = pc_strip_text($_POST['role'] ?? '');
    $bio             = pc_strip_text($_POST['bio'] ?? '');
    $social_linkedin = pc_strip_text($_POST['social_linkedin'] ?? '');
    $sort_order      = (int)($_POST['sort_order'] ?? 0);

    if ($id <= 0 || $name === '' || $role === '') {
        set_flash('danger', 'Name and role are required.');
        redirect_self();
    }

    $new_photo = pc_upload_image('photo_file', ADMIN_ROOT . '/uploads/', 'board');

    if ($new_photo !== null) {
        $stmt = $conn->prepare("UPDATE eswasa_board_members SET name = ?, role = ?, photo = ?, bio = ?, social_linkedin = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param('sssssii', $name, $role, $new_photo, $bio, $social_linkedin, $sort_order, $id);
    } else {
        $stmt = $conn->prepare("UPDATE eswasa_board_members SET name = ?, role = ?, bio = ?, social_linkedin = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param('ssssii', $name, $role, $bio, $social_linkedin, $sort_order, $id);
    }
    if ($stmt && $stmt->execute()) {
        set_flash('success', 'Board member updated.');
    } else {
        set_flash('danger', 'Update failed: ' . ($conn->error ?? 'unknown error'));
    }
    if ($stmt) $stmt->close();
    redirect_self();
}

// Delete member
if ($action === 'delete') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        set_flash('warning', 'Invalid id.');
        redirect_self();
    }
    $stmt = $conn->prepare("DELETE FROM eswasa_board_members WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt && $stmt->execute()) {
        set_flash('success', 'Board member deleted.');
    } else {
        set_flash('danger', 'Delete failed: ' . ($conn->error ?? 'unknown error'));
    }
    if ($stmt) $stmt->close();
    redirect_self();
}

// ---- Load data ----
$pc = pc_get_many($conn, $text_keys, [
    'board_hero_title' => 'Members of the Council',
    'board_intro_body' => 'Strategic Oversight & Governance',
]);

$members = [];
$rs = $conn->query("SELECT * FROM eswasa_board_members ORDER BY sort_order ASC, name ASC");
if ($rs) {
    while ($r = $rs->fetch_assoc()) $members[] = $r;
}

// Pre-fill for edit
$edit_member = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM eswasa_board_members WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $eid);
        $stmt->execute();
        $res = $stmt->get_result();
        $edit_member = $res->fetch_assoc();
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Board Members</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBoardMemberModal">
        <i class="fas fa-plus me-1"></i> Add board member
    </button>
</div>

<!-- Page text card -->
<div class="card mb-4">
    <div class="card-header"><h5 class="mb-0">Page Header Text</h5></div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="save_board" value="1">
            <div class="mb-3">
                <label class="form-label fw-bold">Hero / Page Title</label>
                <input type="text" name="board_hero_title" class="form-control" maxlength="200"
                       value="<?= pc_h($pc['board_hero_title']) ?>">
                <div class="form-text">Shown in the breadcrumb and at the top of the page.</div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Intro / Subtitle</label>
                <textarea name="board_intro_body" class="form-control" rows="2"><?= pc_h($pc['board_intro_body']) ?></textarea>
                <div class="form-text">Small text shown under the page title.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save Page Text</button>
        </form>
    </div>
</div>

<!-- Board members list -->
<div class="card">
    <div class="card-header">All Board Members (<?= count($members) ?>)</div>
    <div class="card-body">
        <?php if (empty($members)): ?>
            <p class="text-center text-muted py-4 mb-0">No board members yet. Use "Add board member" to create the first one.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">Photo</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th style="width:80px;">Order</th>
                            <th class="text-end" style="width:160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($m['photo'])): ?>
                                        <img src="../<?= pc_h(pc_image_src($m['photo'])) ?>" alt="" style="width:56px;height:56px;object-fit:cover;border:1px solid #ddd;">
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= pc_h($m['name']) ?></td>
                                <td><?= pc_h($m['role']) ?></td>
                                <td><?= (int)$m['sort_order'] ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="index.php?page=board_edit.php&edit=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="index.php?page=board_edit.php&action=delete&id=<?= (int)$m['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete &quot;<?= pc_h($m['name']) ?>&quot;? This cannot be undone.');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addBoardMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Board Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" name="name" class="form-control" required maxlength="150">
                        <div class="form-text">Include honorific with period, e.g. Mrs. Dumile Sibandze.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <input type="text" name="role" class="form-control" required maxlength="150" placeholder="Chairperson, Council Member, ...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Photo</label>
                        <input type="file" name="photo_file" class="form-control" accept="image/*">
                        <div class="form-text">JPG/PNG/WEBP. Square images look best.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bio</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Short biography (optional)."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">LinkedIn URL</label>
                            <input type="url" name="social_linkedin" class="form-control" placeholder="https://linkedin.com/in/...">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0" step="1">
                            <div class="form-text">Lower numbers appear first.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal (server-rendered when ?edit=N) -->
<?php if ($edit_member): ?>
<div class="modal fade show d-block" tabindex="-1" aria-modal="true" role="dialog" style="background:rgba(0,0,0,.4);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int)$edit_member['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Board Member</h5>
                    <a href="index.php?page=board_edit.php" class="btn-close" aria-label="Close"></a>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" name="name" class="form-control" required maxlength="150"
                               value="<?= pc_h($edit_member['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role *</label>
                        <input type="text" name="role" class="form-control" required maxlength="150"
                               value="<?= pc_h($edit_member['role']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Photo</label>
                        <?php if (!empty($edit_member['photo'])): ?>
                            <div class="mb-2">
                                <img src="../<?= pc_h(pc_image_src($edit_member['photo'])) ?>" style="max-height:120px;border:1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="photo_file" class="form-control" accept="image/*">
                        <div class="form-text">Leave empty to keep current image.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bio</label>
                        <textarea name="bio" class="form-control" rows="3"><?= pc_h($edit_member['bio']) ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-bold">LinkedIn URL</label>
                            <input type="url" name="social_linkedin" class="form-control"
                                   value="<?= pc_h($edit_member['social_linkedin']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" min="0" step="1"
                                   value="<?= (int)$edit_member['sort_order'] ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?page=board_edit.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

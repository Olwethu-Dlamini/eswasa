<?php
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ---------------------------------------------------------------------------
// POST handlers — single self-posting page.
// Actions: save_page_text | add | update | delete
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // -- Save page-level text (hero title + intro body) --
    if ($action === 'save_page_text') {
        $text_keys = ['team_hero_title', 'team_intro_body'];
        $kv = [];
        foreach ($text_keys as $k) {
            $kv[$k] = pc_strip_text($_POST[$k] ?? '');
        }
        $errs = pc_save_many($conn, $kv);
        set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page text saved.');
        header("Location: index.php?page=about_team.php");
        exit;
    }

    // -- Add or Update team member --
    if ($action === 'add' || $action === 'update') {
        $id          = $action === 'update' ? (int)($_POST['id'] ?? 0) : 0;
        $name        = pc_strip_text($_POST['name'] ?? '');
        $role        = pc_strip_text($_POST['role'] ?? '');
        $section_raw = (string)($_POST['section'] ?? 'management');
        $section     = in_array($section_raw, ['council','management','staff'], true) ? $section_raw : 'management';
        $bio         = pc_strip_text($_POST['bio'] ?? '');
        $linkedin    = pc_strip_text($_POST['social_linkedin'] ?? '');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $is_vacant   = !empty($_POST['is_vacant']) ? 1 : 0;

        if ($name === '' || $role === '') {
            set_flash('danger', 'Name and Role are required.');
            header("Location: index.php?page=about_team.php" . ($id ? "&edit=$id" : ""));
            exit;
        }

        // Photo upload (optional). If no new file, keep existing on update.
        $photo_path = pc_upload_image('photo_file', ADMIN_ROOT . '/uploads/', 'team');

        if ($action === 'add') {
            $photo = $photo_path ?? '';
            $stmt = $conn->prepare(
                "INSERT INTO eswasa_team_members
                    (name, role, photo, bio, section, social_linkedin, sort_order, is_vacant)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            // s s s s s s i i  → 6 strings + 2 ints
            $stmt->bind_param('ssssssii', $name, $role, $photo, $bio, $section, $linkedin, $sort_order, $is_vacant);
            $msg = 'Team member added.';
        } else {
            if ($id <= 0) {
                set_flash('danger', 'Invalid member id.');
                header("Location: index.php?page=about_team.php");
                exit;
            }
            if ($photo_path !== null) {
                $stmt = $conn->prepare(
                    "UPDATE eswasa_team_members
                        SET name = ?, role = ?, photo = ?, bio = ?, section = ?, social_linkedin = ?, sort_order = ?, is_vacant = ?
                      WHERE id = ?"
                );
                $stmt->bind_param('ssssssiii', $name, $role, $photo_path, $bio, $section, $linkedin, $sort_order, $is_vacant, $id);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE eswasa_team_members
                        SET name = ?, role = ?, bio = ?, section = ?, social_linkedin = ?, sort_order = ?, is_vacant = ?
                      WHERE id = ?"
                );
                $stmt->bind_param('sssssiii', $name, $role, $bio, $section, $linkedin, $sort_order, $is_vacant, $id);
            }
            $msg = 'Team member updated.';
        }

        if ($stmt && $stmt->execute()) {
            set_flash('success', $msg);
        } else {
            set_flash('danger', 'Database error: ' . $conn->error);
        }
        if ($stmt) $stmt->close();
        header("Location: index.php?page=about_team.php");
        exit;
    }

    // -- Delete team member --
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM eswasa_team_members WHERE id = ?");
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                set_flash('success', 'Team member deleted.');
            } else {
                set_flash('danger', 'Database error: ' . $conn->error);
            }
            $stmt->close();
        }
        header("Location: index.php?page=about_team.php");
        exit;
    }
}

// ---------------------------------------------------------------------------
// Read page-level text (with defaults matching the front-end fallbacks)
// ---------------------------------------------------------------------------
$pc = pc_get_many($conn, [
    'team_hero_title',
    'team_intro_body',
], [
    'team_hero_title' => 'Meet Our Team',
    'team_intro_body' => 'Meet the leadership team dedicated to helping you achieve compliance, ensure quality, and promote the sustainability of Eswatini’s industries.',
]);

// ---------------------------------------------------------------------------
// Fetch all team members
// ---------------------------------------------------------------------------
$members = [];
if ($res = $conn->query("SELECT * FROM eswasa_team_members ORDER BY section ASC, sort_order ASC, name ASC")) {
    while ($r = $res->fetch_assoc()) {
        $members[] = $r;
    }
}

// Pre-fill for edit
$edit_member = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid > 0) {
        $stmt = $conn->prepare("SELECT * FROM eswasa_team_members WHERE id = ?");
        $stmt->bind_param('i', $eid);
        $stmt->execute();
        $r = $stmt->get_result();
        $edit_member = $r->fetch_assoc();
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">Meet Our Team</h1>
        <small class="text-muted">Manage the <strong>Council</strong> and <strong>Executive Team</strong> shown on the public Meet Our Team page.</small>
    </div>
    <a href="../Meetourteam.php" target="_blank" class="btn btn-outline-secondary btn-sm">View Public Page</a>
</div>

<!-- ============================================================
     Card 1: Page-level text (hero title + intro body)
     ============================================================ -->
<div class="card mb-4">
    <div class="card-header">Page Header Text</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="save_page_text">

            <div class="mb-3">
                <label class="form-label fw-bold">Hero / Page Title</label>
                <input type="text" name="team_hero_title" class="form-control"
                       value="<?= pc_h($pc['team_hero_title']) ?>">
                <div class="form-text">Shown in the breadcrumb hero band.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Intro Paragraph</label>
                <textarea name="team_intro_body" class="form-control" rows="3"><?= pc_h($pc['team_intro_body']) ?></textarea>
                <div class="form-text">Shown below the page title, above the Council and Executive Team sections.</div>
            </div>

            <button type="submit" class="btn btn-primary">Save Page Text</button>
        </form>
    </div>
</div>

<!-- ============================================================
     Card 2: Team member CRUD — grouped by section
     ============================================================ -->
<?php
// Bucket members by section so each one renders under its own subhead
$by_section = ['council' => [], 'management' => [], 'staff' => []];
foreach ($members as $m) {
    $sec = (string)$m['section'];
    if (!isset($by_section[$sec])) $by_section[$sec] = [];
    $by_section[$sec][] = $m;
}
// Re-sort each bucket by sort_order then name
foreach ($by_section as &$rows) {
    usort($rows, function ($a, $b) {
        $sa = (int)($a['sort_order'] ?? 0);
        $sb = (int)($b['sort_order'] ?? 0);
        if ($sa !== $sb) return $sa <=> $sb;
        return strcmp((string)$a['name'], (string)$b['name']);
    });
}
unset($rows);

$section_meta = [
    'council'    => ['label' => 'Council',        'badge' => 'bg-info',      'icon' => 'fa-landmark',         'blurb' => 'Governance — appears at the top of Meet Our Team.'],
    'management' => ['label' => 'Executive Team', 'badge' => 'bg-primary',   'icon' => 'fa-user-tie',         'blurb' => 'Day-to-day management — appears below Council.'],
    'staff'      => ['label' => 'Staff',          'badge' => 'bg-secondary', 'icon' => 'fa-users',            'blurb' => 'General staff — currently not surfaced on the public page.'],
];
?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            Team Members (<?= count($members) ?>)
            <small class="text-muted ms-2">
                <?= count($by_section['council']) ?> Council ·
                <?= count($by_section['management']) ?> Executive ·
                <?= count($by_section['staff']) ?> Staff
            </small>
        </span>
        <?php if (!$edit_member): ?>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                + Add Team Member
            </button>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (empty($members)): ?>
            <p class="text-muted mb-0">No team members yet. Click &ldquo;Add Team Member&rdquo; to add the first one.</p>
        <?php else: ?>
            <?php foreach (['council','management','staff'] as $sec_key):
                $rows = $by_section[$sec_key];
                $meta = $section_meta[$sec_key];
                if (empty($rows)) continue;
            ?>
                <div class="mb-4">
                    <div class="d-flex align-items-baseline justify-content-between mb-2">
                        <h5 class="mb-0">
                            <i class="fas <?= $meta['icon'] ?> me-2 text-muted"></i>
                            <?= htmlspecialchars($meta['label']) ?>
                            <span class="badge <?= $meta['badge'] ?> ms-2"><?= count($rows) ?></span>
                        </h5>
                        <small class="text-muted"><?= htmlspecialchars($meta['blurb']) ?></small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:80px">Photo</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th style="width:90px">Sort</th>
                                    <th style="width:80px">Vacant</th>
                                    <th style="width:170px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $m): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($m['photo'])): ?>
                                                <img src="../<?= pc_h(pc_image_src($m['photo'])) ?>" alt=""
                                                     style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:1px solid #ddd">
                                            <?php else: ?>
                                                <span class="text-muted small">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= pc_h($m['name']) ?></td>
                                        <td><?= pc_h($m['role']) ?></td>
                                        <td><?= (int)$m['sort_order'] ?></td>
                                        <td><?= !empty($m['is_vacant']) ? '<span class="badge bg-warning text-dark">Vacant</span>' : '' ?></td>
                                        <td>
                                            <a href="index.php?page=about_team.php&edit=<?= (int)$m['id'] ?>"
                                               class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this team member?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     Add Member Modal
     ============================================================ -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Team Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Name *</label>
                            <input type="text" name="name" class="form-control" required>
                            <div class="form-text">Include honorific (e.g. Mr., Ms., Dr.) with period.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Role *</label>
                            <input type="text" name="role" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Section *</label>
                            <select name="section" class="form-select" required>
                                <option value="council">Council</option>
                                <option value="management" selected>Executive Team</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                            <div class="form-text">Lower = earlier. Lowest = featured leader.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold d-block">Vacant?</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="is_vacant" value="1" class="form-check-input" id="add_is_vacant">
                                <label class="form-check-label" for="add_is_vacant">Hide on public page</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Photo</label>
                        <input type="file" name="photo_file" accept="image/*" class="form-control">
                        <div class="form-text">JPG, PNG, WEBP or GIF.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Bio</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Short bio (optional)."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">LinkedIn URL</label>
                        <input type="url" name="social_linkedin" class="form-control" placeholder="https://www.linkedin.com/in/...">
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

<!-- ============================================================
     Edit Member (inline card shown when ?edit=N is in URL)
     ============================================================ -->
<?php if ($edit_member): ?>
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        Edit Team Member &mdash; <?= pc_h($edit_member['name']) ?>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$edit_member['id'] ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Name *</label>
                    <input type="text" name="name" class="form-control" required
                           value="<?= pc_h($edit_member['name']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Role *</label>
                    <input type="text" name="role" class="form-control" required
                           value="<?= pc_h($edit_member['role']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Section *</label>
                    <select name="section" class="form-select" required>
                        <option value="council" <?= $edit_member['section'] === 'council' ? 'selected' : '' ?>>Council</option>
                        <option value="management" <?= $edit_member['section'] === 'management' ? 'selected' : '' ?>>Executive Team</option>
                        <option value="staff" <?= $edit_member['section'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="<?= (int)$edit_member['sort_order'] ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold d-block">Vacant?</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="is_vacant" value="1" class="form-check-input" id="edit_is_vacant"
                            <?= !empty($edit_member['is_vacant']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="edit_is_vacant">Hide on public page</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Photo</label>
                <?php if (!empty($edit_member['photo'])): ?>
                    <div class="mb-2">
                        <img src="../<?= pc_h(pc_image_src($edit_member['photo'])) ?>" alt=""
                             style="max-height:120px;border:1px solid #ddd;border-radius:4px">
                    </div>
                <?php endif; ?>
                <input type="file" name="photo_file" accept="image/*" class="form-control">
                <div class="form-text">Leave empty to keep current image.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Bio</label>
                <textarea name="bio" class="form-control" rows="3"><?= pc_h($edit_member['bio']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">LinkedIn URL</label>
                <input type="url" name="social_linkedin" class="form-control"
                       value="<?= pc_h($edit_member['social_linkedin']) ?>">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php?page=about_team.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

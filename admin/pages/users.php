<?php
// admin/pages/users.php — real CRUD against the `users` table.
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

$current_user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// The first account created is the super user and cannot be deleted by anyone,
// including itself. Without this, an admin can delete every account and lock
// the organisation out of its own CMS with no way back in short of editing the
// database by hand. Enforced server-side below as well as hidden in the UI, so
// a hand-crafted ?delete_user=1 URL can't bypass it.
// See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A7.
const SUPER_USER_ID = 1;

// ---- POST: add / update user ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $id       = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = $_POST['role'] ?? 'author';
    $password = $_POST['password'] ?? '';

    // Only one role is supported — everyone with admin access is a full admin.
    $role = 'admin';

    if ($username === '' || $email === '') {
        set_flash('danger', 'Username and email are required.');
        redirect_self();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('danger', 'Invalid email address.');
        redirect_self();
    }

    if ($id > 0) {
        // Update existing
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param('ssssi', $username, $email, $role, $hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param('sssi', $username, $email, $role, $id);
        }
        if ($stmt && $stmt->execute()) {
            set_flash('success', 'User updated.');
            log_activity($conn, 'user.update', 'users#' . $id, 'Updated user ' . $username);
        } else {
            set_flash('danger', 'Update failed: ' . ($conn->error ?? 'unknown error'));
        }
        if ($stmt) $stmt->close();
    } else {
        // Insert new
        if ($password === '') {
            set_flash('danger', 'Password is required for new users.');
            redirect_self();
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $email, $role, $hash);
        if ($stmt && $stmt->execute()) {
            set_flash('success', 'User created.');
            log_activity($conn, 'user.create', 'users#' . $conn->insert_id, 'Created user ' . $username . ' (' . $email . ')');
        } else {
            $msg = $conn->error ?? 'unknown error';
            // Friendlier dup-key message.
            // strpos() rather than str_contains(): the latter is PHP 8 only,
            // and on a PHP 7.4 host this line fataled with "call to undefined
            // function" — so adding a user with a duplicate username produced
            // a white screen instead of this message. See spec item A7.
            if (strpos($msg, 'Duplicate entry') !== false) {
                $msg = 'A user with that username or email already exists.';
            }
            set_flash('danger', 'Create failed: ' . $msg);
        }
        if ($stmt) $stmt->close();
    }
    redirect_self();
}

// ---- DELETE ----
if (isset($_GET['delete_user'])) {
    $del_id = (int)$_GET['delete_user'];
    if ($del_id <= 0) {
        set_flash('warning', 'Invalid user id.');
        redirect_self();
    }
    if ($del_id === $current_user_id) {
        set_flash('danger', 'You cannot delete the account you are logged in as.');
        redirect_self();
    }
    if ($del_id === SUPER_USER_ID) {
        set_flash('danger', 'The primary administrator account cannot be deleted.');
        redirect_self();
    }
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $del_id);
    if ($stmt && $stmt->execute()) {
        set_flash('success', 'User deleted.');
        log_activity($conn, 'user.delete', 'users#' . $del_id, 'Deleted user');
    } else {
        set_flash('danger', 'Delete failed.');
    }
    if ($stmt) $stmt->close();
    redirect_self();
}

// ---- LIST ----
$users = [];
$rs = $conn->query("SELECT id, username, email, role, created_at FROM users ORDER BY role, username");
if ($rs) {
    while ($r = $rs->fetch_assoc()) $users[] = $r;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Users</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openAddUserModal()">
        <i class="fas fa-plus me-1"></i> Add user
    </button>
</div>

<?php if (empty($users)): ?>
    <div class="text-center py-5">
        <i class="fas fa-users fa-3x text-muted mb-3"></i>
        <h5>No users yet</h5>
        <p class="text-muted">Add your first user using the button above.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($u['username']) ?>
                            <?php if ((int)$u['id'] === $current_user_id): ?>
                                <span class="badge bg-info ms-2">you</span>
                            <?php endif; ?>
                            <?php if ((int)$u['id'] === SUPER_USER_ID): ?>
                                <span class="badge bg-secondary ms-2" title="The primary administrator account cannot be deleted.">primary admin</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($u['created_at']) ?></td>
                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#userModal"
                                    onclick="openEditUserModal(<?= (int)$u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>', '<?= htmlspecialchars(addslashes($u['email'])) ?>')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <?php if ((int)$u['id'] !== $current_user_id && (int)$u['id'] !== SUPER_USER_ID): ?>
                                <a href="?page=users.php&delete_user=<?= (int)$u['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete user &quot;<?= htmlspecialchars(addslashes($u['username'])) ?>&quot;? This cannot be undone.');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<!-- Add / Edit Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="save_user" value="1">
                    <input type="hidden" name="id" id="user_id" value="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" id="user_username" required maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="user_email" required maxlength="100">
                    </div>
                    <input type="hidden" name="role" id="user_role" value="admin">
                    <div class="mb-1">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="user_password" autocomplete="new-password">
                        <small class="form-text text-muted" id="pw_hint">Required for new users. Leave blank when editing to keep the existing password.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('userModalLabel').textContent = 'Add user';
    document.getElementById('user_id').value = '';
    document.getElementById('user_username').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = true;
}
function openEditUserModal(id, username, email) {
    document.getElementById('userModalLabel').textContent = 'Edit user';
    document.getElementById('user_id').value = id;
    document.getElementById('user_username').value = username.replace(/\\'/g, "'");
    document.getElementById('user_email').value = email.replace(/\\'/g, "'");
    document.getElementById('user_password').value = '';
    document.getElementById('user_password').required = false;
}
</script>

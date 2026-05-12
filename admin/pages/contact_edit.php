<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle status update
if (isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'] ?? 'new';
    if (in_array($status, ['new', 'read', 'replied'])) {
        $stmt = $conn->prepare("UPDATE eswasa_contact_messages SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        set_flash('success', 'Message status updated.');
    }
    header("Location: index.php?page=contact_edit.php");
    exit;
}

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM eswasa_contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    set_flash('success', 'Message deleted.');
    header("Location: index.php?page=contact_edit.php");
    exit;
}

// Fetch all messages
$messages = $conn->query("SELECT * FROM eswasa_contact_messages ORDER BY created_at DESC");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Contact Messages</h1>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" aria-labelledby="viewMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMessageModalLabel">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Content loaded by JS -->
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        All Messages (<?= $messages->num_rows ?>)
    </div>
    <div class="card-body">
        <?php if ($messages->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($msg = $messages->fetch_assoc()): ?>
                            <tr style="<?= $msg['status'] == 'new' ? 'background-color: #fff8e1;' : '' ?>">
                                <td><?= htmlspecialchars($msg['name']) ?></td>
                                <td><?= htmlspecialchars($msg['email']) ?></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($msg['created_at'])) ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="new" <?= $msg['status'] == 'new' ? 'selected' : '' ?>>New</option>
                                            <option value="read" <?= $msg['status'] == 'read' ? 'selected' : '' ?>>Read</option>
                                            <option value="replied" <?= $msg['status'] == 'replied' ? 'selected' : '' ?>>Replied</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <!-- View button triggers modal -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary view-message-btn" 
                                            data-id="<?= $msg['id'] ?>"
                                            data-name="<?= htmlspecialchars($msg['name']) ?>"
                                            data-email="<?= htmlspecialchars($msg['email']) ?>"
                                            data-phone="<?= htmlspecialchars($msg['phone']) ?>"
                                            data-subject="<?= htmlspecialchars($msg['subject']) ?>"
                                            data-message="<?= htmlspecialchars($msg['message']) ?>"
                                            data-date="<?= date('F j, Y \a\t g:i A', strtotime($msg['created_at'])) ?>">
                                        View
                                    </button>
                                    <a href="?delete=<?= $msg['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this message?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No messages received.</p>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle View button click
    document.querySelectorAll('.view-message-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const subject = this.getAttribute('data-subject');
            const message = this.getAttribute('data-message');
            const date = this.getAttribute('data-date');

            // Build modal content
            const html = `
                <p><strong>Name:</strong> ${name}</p>
                <p><strong>Email:</strong> ${email}</p>
                <p><strong>Phone:</strong> ${phone}</p>
                <p><strong>Subject:</strong> ${subject}</p>
                <hr>
                <p><strong>Message:</strong></p>
                <p>${message.replace(/\n/g, '<br>')}</p>
                <hr>
                <p><em>Sent on: ${date}</em></p>
            `;

            document.getElementById('messageModalBody').innerHTML = html;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewMessageModal'));
            modal.show();
        });
    });
});
</script>
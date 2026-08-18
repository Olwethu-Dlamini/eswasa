<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Content keys (front-end contact.php) ──────────────────────
$contact_text_keys = [
    'contact_breadcrumb_title',
    'contact_intro_title',
    'contact_intro_text',
    'contact_office1_title',
    'contact_office1_addr',
    'contact_office1_phone1_label', 'contact_office1_phone1_tel',
    'contact_office1_phone2_label', 'contact_office1_phone2_tel',
    'contact_office2_title',
    'contact_office2_addr',
    'contact_office2_phone1_label', 'contact_office2_phone1_tel',
    'contact_postal_title',
    'contact_postal_lines',
    'contact_website_title',
    'contact_website_url', 'contact_website_label',
    'contact_email_addr',
    'contact_form_title',
    'contact_form_success_text',
    'contact_form_name_ph',
    'contact_form_email_ph',
    'contact_form_phone_ph',
    'contact_form_subject_ph',
    'contact_form_message_ph',
    'contact_form_submit_label',
    'contact_map_embed',
];

// ── Save handler for page content ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_contact'])) {
    $kv = [];
    foreach ($contact_text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Page content saved.');
    header("Location: index.php?page=contact_edit.php");
    exit;
}

$pc = pc_get_many($conn, $contact_text_keys);

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
    <h1 class="h2">Contact Page</h1>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ── Page Content Editor ─────────────────────────────────── -->
<form method="POST" enctype="multipart/form-data" class="mb-5">

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Breadcrumb &amp; Intro</h5>
            <div class="mb-3">
                <label class="form-label">Breadcrumb / Page Title</label>
                <input type="text" name="contact_breadcrumb_title" class="form-control" value="<?= pc_h($pc['contact_breadcrumb_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Heading</label>
                <input type="text" name="contact_intro_title" class="form-control" value="<?= pc_h($pc['contact_intro_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Intro Text</label>
                <textarea name="contact_intro_text" class="form-control" rows="2"><?= pc_h($pc['contact_intro_text']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Head Office</h5>
            <div class="mb-3">
                <label class="form-label">Card Title</label>
                <input type="text" name="contact_office1_title" class="form-control" value="<?= pc_h($pc['contact_office1_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Address (one line per row)</label>
                <textarea name="contact_office1_addr" class="form-control" rows="4"><?= pc_h($pc['contact_office1_addr']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone 1 — Display Label</label>
                    <input type="text" name="contact_office1_phone1_label" class="form-control" value="<?= pc_h($pc['contact_office1_phone1_label']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone 1 — tel: link</label>
                    <input type="text" name="contact_office1_phone1_tel" class="form-control" value="<?= pc_h($pc['contact_office1_phone1_tel']) ?>" placeholder="+26825184633">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone 2 — Display Label</label>
                    <input type="text" name="contact_office1_phone2_label" class="form-control" value="<?= pc_h($pc['contact_office1_phone2_label']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone 2 — tel: link</label>
                    <input type="text" name="contact_office1_phone2_tel" class="form-control" value="<?= pc_h($pc['contact_office1_phone2_tel']) ?>">
                </div>
            </div>
            <small class="text-muted">Leave a phone label empty to hide that line on the page.</small>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Metrology Laboratory</h5>
            <div class="mb-3">
                <label class="form-label">Card Title</label>
                <input type="text" name="contact_office2_title" class="form-control" value="<?= pc_h($pc['contact_office2_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Address (one line per row)</label>
                <textarea name="contact_office2_addr" class="form-control" rows="4"><?= pc_h($pc['contact_office2_addr']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone — Display Label</label>
                    <input type="text" name="contact_office2_phone1_label" class="form-control" value="<?= pc_h($pc['contact_office2_phone1_label']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone — tel: link</label>
                    <input type="text" name="contact_office2_phone1_tel" class="form-control" value="<?= pc_h($pc['contact_office2_phone1_tel']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Postal, Website &amp; Email</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postal Section Title</label>
                    <input type="text" name="contact_postal_title" class="form-control" value="<?= pc_h($pc['contact_postal_title']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Postal Address (one line per row)</label>
                    <textarea name="contact_postal_lines" class="form-control" rows="3"><?= pc_h($pc['contact_postal_lines']) ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Website Section Title</label>
                    <input type="text" name="contact_website_title" class="form-control" value="<?= pc_h($pc['contact_website_title']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Website URL</label>
                    <input type="url" name="contact_website_url" class="form-control" value="<?= pc_h($pc['contact_website_url']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Website Display Label</label>
                    <input type="text" name="contact_website_label" class="form-control" value="<?= pc_h($pc['contact_website_label']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contact Email Address</label>
                    <input type="email" name="contact_email_addr" class="form-control" value="<?= pc_h($pc['contact_email_addr']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Contact Form Labels</h5>
            <small class="text-muted d-block mb-3">Form submission and validation are unchanged; only the visible labels/placeholders are editable.</small>
            <div class="mb-3">
                <label class="form-label">Form Heading</label>
                <input type="text" name="contact_form_title" class="form-control" value="<?= pc_h($pc['contact_form_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Success Message (shown after submit)</label>
                <textarea name="contact_form_success_text" class="form-control" rows="2"><?= pc_h($pc['contact_form_success_text']) ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name Placeholder</label>
                    <input type="text" name="contact_form_name_ph" class="form-control" value="<?= pc_h($pc['contact_form_name_ph']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email Placeholder</label>
                    <input type="text" name="contact_form_email_ph" class="form-control" value="<?= pc_h($pc['contact_form_email_ph']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone Placeholder</label>
                    <input type="text" name="contact_form_phone_ph" class="form-control" value="<?= pc_h($pc['contact_form_phone_ph']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Subject Placeholder</label>
                    <input type="text" name="contact_form_subject_ph" class="form-control" value="<?= pc_h($pc['contact_form_subject_ph']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Message Placeholder</label>
                    <input type="text" name="contact_form_message_ph" class="form-control" value="<?= pc_h($pc['contact_form_message_ph']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Submit Button Label</label>
                    <input type="text" name="contact_form_submit_label" class="form-control" value="<?= pc_h($pc['contact_form_submit_label']) ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Map Embed</h5>
            <div class="mb-2">
                <label class="form-label">Google Maps Embed URL</label>
                <textarea name="contact_map_embed" class="form-control" rows="3"><?= pc_h($pc['contact_map_embed']) ?></textarea>
                <small class="text-muted">Paste the <code>src</code> URL from a Google Maps embed iframe.</small>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" name="save_contact" class="btn btn-primary">Save Page Content</button>
    </div>
</form>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2 class="h3">Contact Messages</h2>
</div>

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
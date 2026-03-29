<?php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = ['mandate', 'process_desc', 'proposal_intro', 'implementation_intro'];
    foreach ($fields as $field) {
        // Clean: remove HTML, preserve line breaks
        $content = trim($_POST[$field] ?? '');
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8'); // prevent XSS
        $page_key = 'standards_' . $field;
        $stmt = $conn->prepare("INSERT INTO page_content (page_key, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = ?, updated_at = NOW()");
        $stmt->bind_param('sss', $page_key, $content, $content);
        $stmt->execute();
        $stmt->close();
    }
    set_flash('success', 'Standards page updated successfully.');
    redirect_self();
}

// Helper
function getContent($conn, $key) {
    $stmt = $conn->prepare("SELECT content FROM page_content WHERE page_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['content'] : '';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Standards Page</h1>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST">

    <div class="mb-4">
        <label class="form-label fw-bold">Standards Development Mandate</label>
        <textarea name="mandate" class="form-control" rows="6" placeholder="Example: Under Section 5 of the Standards Act, 1968..."><?= htmlspecialchars(getContent($conn, 'standards_mandate')) ?></textarea>
        <div class="form-text">Write 2–4 short paragraphs. Press Enter for a new paragraph.</div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Process Overview</label>
        <textarea name="process_desc" class="form-control" rows="4" placeholder="Example: ESWASA follows a structured 8-stage process..."><?= htmlspecialchars(getContent($conn, 'standards_process_desc')) ?></textarea>
        <div class="form-text">A short description (1–3 sentences) above the numbered steps.</div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Proposal Instructions</label>
        <textarea name="proposal_intro" class="form-control" rows="5" placeholder="Example: To propose a new standard, please complete the official form..."><?= htmlspecialchars(getContent($conn, 'standards_proposal_intro')) ?></textarea>
        <div class="form-text">Instructions for stakeholders. The download button and contact links appear automatically below.</div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Implementation Section Intro</label>
        <textarea name="implementation_intro" class="form-control" rows="4" placeholder="Example: ESWASA offers standards for purchase and training..."><?= htmlspecialchars(getContent($conn, 'standards_implementation_intro')) ?></textarea>
        <div class="form-text">A short intro before the list of standards and training info.</div>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>

</form>
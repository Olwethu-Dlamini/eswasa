<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $stmt = $conn->prepare("INSERT INTO page_content (page_key, content) VALUES (?, ?) ON DUPLICATE KEY UPDATE content = ?, updated_at = NOW()");
    $stmt->bind_param('sss', 'calibration_content', $content, $content);
    $stmt->execute();
    $stmt->close();
    set_flash('success', 'Calibration page updated.');
    redirect_self();
}

$stmt = $conn->prepare("SELECT content FROM page_content WHERE page_key = ?");
$stmt->bind_param("s", "calibration_content");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$content = $row ? $row['content'] : '';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Calibration</h1>
</div>

<form method="POST">
    <div class="mb-3">
        <label class="form-label fw-bold">Calibration Services</label>
        <textarea name="content" class="form-control" rows="10"><?= htmlspecialchars($content) ?></textarea>
        <div class="form-text">Describe your calibration services and capabilities.</div>
    </div>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>
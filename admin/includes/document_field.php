<?php
/**
 * admin/includes/document_field.php — reusable "document" form control.
 *
 * Renders a file picker for a PDF/Word document, a status badge saying whether
 * the current target actually resolves, and an optional URL box underneath for
 * genuinely external links.
 *
 * Document links used to be plain text boxes holding server paths. Editors had
 * to type them correctly, and a wrong one was invisible in the CMS — the only
 * way to find out was to click the button on the live site. Two links were
 * broken that way (the training prospectus and the Technical Committee
 * application form).
 *
 * Usage, inside a <form enctype="multipart/form-data">:
 *
 *     <?php
 *     $doc_key   = 'ingelo_apply_button_url';
 *     $doc_label = 'Application form';
 *     $doc_help  = 'Shown on the Apply button.';   // optional
 *     include __DIR__ . '/../includes/document_field.php';
 *     ?>
 *
 * The matching save handler pairs it with pc_upload_document("{$key}_file", ...),
 * where an upload wins over the URL box, so existing links keep working.
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-c-design.md (C2).
 */
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }

$doc_key   = $doc_key   ?? '';
$doc_label = $doc_label ?? 'Document';
$doc_help  = $doc_help  ?? '';
$doc_value = trim((string)($pc[$doc_key] ?? ''));

[$doc_state, $doc_state_label] = pc_document_status($doc_value);
$doc_badge = [
    'found'    => 'bg-success',
    'missing'  => 'bg-danger',
    'external' => 'bg-info',
    'empty'    => 'bg-secondary',
][$doc_state] ?? 'bg-secondary';
?>
<div class="mb-3">
    <label class="form-label fw-bold"><?= htmlspecialchars($doc_label) ?></label>

    <div class="mb-2 small">
        <span class="badge <?= $doc_badge ?>"><?= htmlspecialchars($doc_state_label) ?></span>
        <?php if ($doc_state === 'found'): ?>
            <a href="../<?= pc_h($doc_value) ?>" target="_blank" rel="noopener" class="ms-1">view current document</a>
        <?php elseif ($doc_state === 'missing'): ?>
            <span class="text-muted ms-1">this link is broken on the live site &mdash; upload a replacement below</span>
        <?php elseif ($doc_state === 'external'): ?>
            <a href="<?= pc_h($doc_value) ?>" target="_blank" rel="noopener" class="ms-1">open link</a>
        <?php endif; ?>
        <?php if ($doc_value !== ''): ?>
            <div class="text-muted mt-1"><code><?= pc_h($doc_value) ?></code></div>
        <?php endif; ?>
    </div>

    <input type="file" name="<?= pc_h($doc_key) ?>_file" class="form-control"
           accept="application/pdf,.pdf,.doc,.docx">
    <div class="form-text">
        Upload a PDF or Word document to replace the current one (max 25&nbsp;MB).
        Leave empty to keep what is there.
        <?= $doc_help !== '' ? ' ' . htmlspecialchars($doc_help) : '' ?>
    </div>

    <label class="form-label mt-2 small text-muted">Or link to an external URL instead</label>
    <input type="text" name="<?= pc_h($doc_key) ?>" class="form-control form-control-sm"
           value="<?= pc_h($doc_value) ?>">
    <div class="form-text">Only used when no file is uploaded above.</div>
</div>

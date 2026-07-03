<?php
// Shared stub used by editors that aren't built out yet — keeps the
// sidebar functional (no 404s) and tells admins what's coming.
// Each stub page sets $stub_title and $stub_public_url then includes this.
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$stub_title       = $stub_title       ?? ($page_title ?? 'Editor');
$stub_public_url  = $stub_public_url  ?? null;
$stub_intro       = $stub_intro       ?? 'A dedicated admin editor for this page hasn\'t been built yet — the content lives directly in the page file for now.';
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><?= htmlspecialchars($stub_title) ?></h1>
</div>

<div class="row">
    <div class="col-lg-9">
        <div class="card border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">
                    <i class="fas fa-tools me-2"></i>Editor coming soon
                </h5>
                <p class="mb-3"><?= htmlspecialchars($stub_intro) ?></p>
                <?php if ($stub_public_url): ?>
                <p class="mb-1"><strong>Live page:</strong>
                    <a href="../<?= htmlspecialchars($stub_public_url) ?>" target="_blank" rel="noopener">
                        /<?= htmlspecialchars($stub_public_url) ?>
                        <i class="fas fa-external-link-alt ms-1 small"></i>
                    </a>
                </p>
                <?php endif; ?>
                <p class="text-muted small mb-0">
                    To request this editor, contact the development team. In the meantime,
                    edit the underlying PHP file directly or use a related editor from
                    the sidebar if one exists.
                </p>
            </div>
        </div>
    </div>
</div>

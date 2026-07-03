<?php
/**
 * Shared tender form fields. Expects $T = the tender row (edit) or null (add).
 * Included by tenders_edit.php inside both the Add modal and the Edit card.
 */
if (!defined('ESWASA_ADMIN')) { exit('Direct access not permitted.'); }
$T = $T ?? null;
$v = function (string $k) use ($T) { return htmlspecialchars($T[$k] ?? ''); };
?>
<div class="mb-3">
    <label class="form-label fw-bold">Title *</label>
    <input type="text" name="title" class="form-control" required value="<?= $v('title') ?>">
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Reference No.</label>
        <input type="text" name="reference_no" class="form-control" value="<?= $v('reference_no') ?>" placeholder="e.g. ESWASA/T/2026/01">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Category</label>
        <input type="text" name="category" class="form-control" list="tenderCategories" value="<?= $v('category') ?>" placeholder="Goods / Services / Works / Consultancy">
    </div>
</div>
<div class="mb-3">
    <label class="form-label fw-bold">Description *</label>
    <textarea name="description" class="form-control" rows="4" required><?= $v('description') ?></textarea>
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Published Date *</label>
        <input type="date" name="published_date" class="form-control" required value="<?= $v('published_date') ?>">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Closing Date (submission deadline) *</label>
        <input type="date" name="closing_date" class="form-control" required value="<?= $v('closing_date') ?>">
    </div>
</div>

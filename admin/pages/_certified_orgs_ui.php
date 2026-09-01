<?php
/**
 * Markup for the certified-company logo grids. Include after
 * _certified_orgs_crud.php, which sets $co_rows / $co_edit / $co_is_new and
 * the $CO_* configuration this file reads.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$co_self = 'index.php?page=' . $CO_PAGE;
?>

<?php if (!$co_edit && !$co_is_new): ?>
    <div class="d-flex justify-content-end mb-3">
        <a href="<?= pc_h($co_self) ?>&new_org=1" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Add <?= pc_h($CO_NOUN) ?>
        </a>
    </div>
<?php endif; ?>

<?php if ($co_edit || $co_is_new):
    $o = $co_edit ?: [
        'id' => 0, 'name' => '', 'standard' => '', 'product' => '', 'logo_path' => null,
        'sort_order' => ($co_rows ? (max(array_column($co_rows, 'sort_order')) + 1) : 1),
        'is_active' => 1,
    ];
?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong><?= $co_edit ? 'Edit ' . pc_h($CO_NOUN) : 'Add ' . pc_h($CO_NOUN) ?></strong>
            <a href="<?= pc_h($co_self) ?>" class="btn btn-sm btn-link text-decoration-none">&larr; Back to list</a>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php if ($co_edit): ?>
                    <input type="hidden" name="org_id" value="<?= (int)$o['id'] ?>">
                <?php endif; ?>
                <input type="hidden" name="org_existing_logo" value="<?= pc_h($o['logo_path']) ?>">

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Name *</label>
                        <input type="text" name="org_name" class="form-control" required maxlength="200"
                               value="<?= pc_h($o['name']) ?>" placeholder="e.g. GALP Eswatini">
                    </div>

                    <?php if ($CO_FIELDS['product'] !== null): ?>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Product <?= $CO_FIELDS['product'] === 'required' ? '*' : '' ?></label>
                            <input type="text" name="org_product" class="form-control" maxlength="200"
                                   <?= $CO_FIELDS['product'] === 'required' ? 'required' : '' ?>
                                   value="<?= pc_h($o['product'] ?? '') ?>" placeholder="e.g. Chilli Sauce">
                            <div class="form-text">Shown under the logo on the public tile.</div>
                        </div>
                    <?php endif; ?>

                    <?php if ($CO_FIELDS['standard'] !== null): ?>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Standard <?= $CO_FIELDS['standard'] === 'required' ? '*' : '' ?></label>
                            <input type="text" name="org_standard" class="form-control" maxlength="200"
                                   <?= $CO_FIELDS['standard'] === 'required' ? 'required' : '' ?>
                                   value="<?= pc_h($o['standard']) ?>" placeholder="e.g. SZNS ISO 9001:2015">
                            <div class="form-text">Free text &mdash; exactly as it should appear under the logo tile.<?= $CO_FIELDS['standard'] === 'optional' ? ' Optional; leave empty to show nothing.' : '' ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <label class="form-label fw-bold">Sort order</label>
                        <input type="number" name="org_sort_order" class="form-control" value="<?= (int)$o['sort_order'] ?>">
                        <div class="form-text">Lower = earlier.</div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Logo</label>
                        <div class="mb-2">
                            <img data-crop-preview="org_logo_preview"
                                 src="<?= !empty($o['logo_path']) ? '../' . pc_h($o['logo_path']) : '' ?>"
                                 style="max-height:80px;border:1px solid #ddd;padding:4px;background:#fff;<?= empty($o['logo_path']) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                            <?php if (!empty($o['logo_path'])): ?>
                                <code class="ms-2 small text-muted"><?= pc_h($o['logo_path']) ?></code>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="org_logo_file" class="form-control crop-input" accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif"
                               data-crop-label="Logo">
                        <input type="hidden" name="org_logo_cropped">
                        <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). PNG / JPG / WebP / SVG / GIF up to 5 MB. Leave empty to keep current. Tiles without a logo render the name as a wordmark.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="org_is_active" id="org_is_active" value="1" class="form-check-input"
                                   <?= (int)$o['is_active'] === 1 ? 'checked' : '' ?>>
                            <label for="org_is_active" class="form-check-label">Show on public page</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="<?= pc_h($co_self) ?>" class="btn btn-link text-decoration-none">Cancel</a>
                    <button type="submit" name="save_org" value="1" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= $co_edit ? 'Save changes' : 'Add ' . pc_h($CO_NOUN) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$co_edit && !$co_is_new): ?>
    <div class="card">
        <div class="card-header">All <?= pc_h($CO_NOUN) ?>s (<?= count($co_rows) ?>)</div>
        <div class="card-body p-0">
            <?php if ($co_rows): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Order</th>
                                <th style="width: 110px;">Logo</th>
                                <th>Name</th>
                                <?php if ($CO_FIELDS['product']  !== null): ?><th>Product</th><?php endif; ?>
                                <?php if ($CO_FIELDS['standard'] !== null): ?><th>Standard</th><?php endif; ?>
                                <th style="width: 90px;" class="text-center">Active</th>
                                <th style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($co_rows as $row): ?>
                                <tr>
                                    <td><?= (int)$row['sort_order'] ?></td>
                                    <td>
                                        <?php if (!empty($row['logo_path'])): ?>
                                            <img src="../<?= pc_h($row['logo_path']) ?>" style="max-height:38px;max-width:100px;object-fit:contain" alt="">
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">wordmark</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= pc_h($row['name']) ?></td>
                                    <?php if ($CO_FIELDS['product']  !== null): ?><td><?= pc_h($row['product'] ?? '') ?></td><?php endif; ?>
                                    <?php if ($CO_FIELDS['standard'] !== null): ?><td><?= pc_h($row['standard']) ?></td><?php endif; ?>
                                    <td class="text-center">
                                        <a href="<?= pc_h($co_self) ?>&toggle_org=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                            <?php if ((int)$row['is_active'] === 1): ?>
                                                <span class="badge bg-success">On</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Off</span>
                                            <?php endif; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= pc_h($co_self) ?>&edit_org=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= pc_h($co_self) ?>&delete_org=<?= (int)$row['id'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Delete this <?= pc_h($CO_NOUN) ?>? This cannot be undone.')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted p-3 mb-0">
                    No <?= pc_h($CO_NOUN) ?>s yet.
                    <a href="<?= pc_h($co_self) ?>&new_org=1">Add the first one</a>.
                </p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

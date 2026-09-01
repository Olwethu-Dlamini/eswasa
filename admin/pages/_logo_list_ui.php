<?php
/**
 * Manager for one logo strip. Include after _logo_list_crud.php, which sets
 * $LL_KEY, $LL_CFG, $LL_NOUN, $ll_rows, $ll_edit and $ll_is_new.
 *
 * Renders its own <form>, so it must sit outside any surrounding form.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$ll_self = 'index.php?page=' . $LL_CFG['page'];
$ll_p    = 'll_' . $LL_KEY . '_';
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong><?= pc_h($LL_CFG['label']) ?> (<?= count($ll_rows) ?>)</strong>
        <?php if (!$ll_edit && !$ll_is_new): ?>
            <a href="<?= pc_h($ll_self) ?>&<?= pc_h($ll_p) ?>new=1" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add <?= pc_h($LL_NOUN) ?>
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body">

        <?php if ($ll_edit || $ll_is_new):
            $r = $ll_edit ?: [
                'id' => 0, 'logo_path' => '', 'url' => '', 'alt' => '',
                'sort_order' => ($ll_rows ? (max(array_column($ll_rows, 'sort_order')) + 10) : 10),
                'is_active' => 1,
            ];
        ?>
            <form method="POST" enctype="multipart/form-data" class="border rounded p-3 mb-3">
                <input type="hidden" name="logo_list_key" value="<?= pc_h($LL_KEY) ?>">
                <?php if ($ll_edit): ?>
                    <input type="hidden" name="logo_id" value="<?= (int)$r['id'] ?>">
                <?php endif; ?>
                <input type="hidden" name="logo_existing" value="<?= pc_h($r['logo_path']) ?>">

                <div class="row g-3">
                    <div class="col-md-<?= $LL_CFG['url'] ? '4' : '9' ?>">
                        <label class="form-label fw-bold">Name / alt text *</label>
                        <input type="text" name="logo_alt" class="form-control" required maxlength="200"
                               value="<?= pc_h($r['alt']) ?>" placeholder="e.g. ISO">
                        <div class="form-text">Announced by screen readers in place of the logo.</div>
                    </div>
                    <?php if ($LL_CFG['url']): ?>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Website</label>
                            <input type="url" name="logo_url" class="form-control"
                                   value="<?= pc_h($r['url']) ?>" placeholder="https://...">
                            <div class="form-text">Optional. Leave empty and the logo isn't a link.</div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Sort order</label>
                        <input type="number" name="logo_sort_order" class="form-control" value="<?= (int)$r['sort_order'] ?>">
                        <div class="form-text">Lower = earlier.</div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Logo *</label>
                        <div class="mb-2">
                            <img data-crop-preview="<?= pc_h($ll_p) ?>logo_preview"
                                 src="<?= !empty($r['logo_path']) ? '../' . pc_h(pc_image_src($r['logo_path'])) : '' ?>"
                                 style="max-height:70px;border:1px solid #ddd;padding:4px;background:#fff;<?= empty($r['logo_path']) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                            <?php if (!empty($r['logo_path'])): ?>
                                <code class="ms-2 small text-muted"><?= pc_h($r['logo_path']) ?></code>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="logo_file" class="form-control crop-input"
                               accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif"
                               data-crop-label="<?= pc_h(ucfirst($LL_NOUN)) ?> Logo">
                        <input type="hidden" name="logo_cropped">
                        <div class="form-text">Pick an image &mdash; the cropper opens so you can trim it (free aspect). Leave empty to keep current.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="logo_is_active" id="<?= pc_h($ll_p) ?>active" value="1" class="form-check-input"
                                   <?= (int)$r['is_active'] === 1 ? 'checked' : '' ?>>
                            <label for="<?= pc_h($ll_p) ?>active" class="form-check-label">Show on the public page</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <a href="<?= pc_h($ll_self) ?>" class="btn btn-link text-decoration-none">Cancel</a>
                    <button type="submit" name="save_logo" value="1" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= $ll_edit ? 'Save changes' : 'Add ' . pc_h($LL_NOUN) ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($ll_rows): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">Order</th>
                            <th style="width: 120px;">Logo</th>
                            <th>Name</th>
                            <?php if ($LL_CFG['url']): ?><th>Website</th><?php endif; ?>
                            <th style="width: 90px;" class="text-center">Active</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ll_rows as $row): ?>
                            <tr>
                                <td><?= (int)$row['sort_order'] ?></td>
                                <td><img src="../<?= pc_h(pc_image_src($row['logo_path'])) ?>" style="max-height:36px;max-width:100px;object-fit:contain" alt=""></td>
                                <td><?= pc_h($row['alt']) ?></td>
                                <?php if ($LL_CFG['url']): ?>
                                    <td class="small text-muted text-break"><?= $row['url'] !== '' ? pc_h($row['url']) : '&mdash;' ?></td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <a href="<?= pc_h($ll_self) ?>&<?= pc_h($ll_p) ?>toggle=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                        <?php if ((int)$row['is_active'] === 1): ?>
                                            <span class="badge bg-success">On</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Off</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= pc_h($ll_self) ?>&<?= pc_h($ll_p) ?>edit=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="<?= pc_h($ll_self) ?>&<?= pc_h($ll_p) ?>delete=<?= (int)$row['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Delete this <?= pc_h($LL_NOUN) ?>? This cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">
                No <?= pc_h($LL_NOUN) ?>s yet.
                <a href="<?= pc_h($ll_self) ?>&<?= pc_h($ll_p) ?>new=1">Add the first one</a>.
            </p>
        <?php endif; ?>
    </div>
</div>

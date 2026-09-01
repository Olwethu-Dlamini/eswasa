<?php
/**
 * Register-entry manager. Include after _cert_register_crud.php, which sets
 * $reg_scheme, $reg_by_status, $reg_edit, $reg_is_new and the two label maps.
 */
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$reg_self  = 'index.php?page=cert_status_edit.php&scheme=' . urlencode($reg_scheme);
$reg_pages = [
    'ms'      => 'certification-status-management-systems.php',
    'product' => 'certification-status-product.php',
    'ingelo'  => 'certification-status-ingelo.php',
];
?>

<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <span class="fw-bold me-1">Register:</span>
    <?php foreach ($REG_SCHEMES as $sk => $sl): ?>
        <a href="index.php?page=cert_status_edit.php&scheme=<?= urlencode($sk) ?>"
           class="btn btn-sm <?= $sk === $reg_scheme ? 'btn-primary' : 'btn-outline-primary' ?>">
            <?= pc_h($sl) ?>
        </a>
    <?php endforeach; ?>
    <a href="../<?= pc_h($reg_pages[$reg_scheme]) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="fas fa-external-link-alt me-1"></i> View this register
    </a>
</div>

<?php if ($reg_edit || $reg_is_new):
    $r = $reg_edit ?: [
        'id' => 0, 'status' => ($_GET['status'] ?? 'suspended'), 'client_name' => '', 'logo_path' => '',
        'cert_no' => '', 'scope' => '', 'effective_date' => date('Y-m-d'), 'reason_note' => '',
        'sort_order' => ($reg_total ? ($reg_total + 1) * 10 : 10),
        'is_active' => 1,
    ];
    if (!isset($REG_STATUSES[$r['status']])) $r['status'] = 'suspended';
?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong><?= $reg_edit ? 'Edit entry' : 'Add entry' ?> &mdash; <?= pc_h($REG_SCHEMES[$reg_scheme]) ?> register</strong>
            <a href="<?= pc_h($reg_self) ?>" class="btn btn-sm btn-link text-decoration-none">&larr; Back to list</a>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="reg_scheme" value="<?= pc_h($reg_scheme) ?>">
                <?php if ($reg_edit): ?>
                    <input type="hidden" name="reg_id" value="<?= (int)$r['id'] ?>">
                <?php endif; ?>
                <input type="hidden" name="reg_existing_logo" value="<?= pc_h($r['logo_path']) ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Section *</label>
                        <select name="reg_status" class="form-select" required>
                            <?php foreach ($REG_STATUSES as $stk => $stl): ?>
                                <option value="<?= pc_h($stk) ?>" <?= $r['status'] === $stk ? 'selected' : '' ?>><?= pc_h($stl) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Which of the three tables the entry appears in.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">Client *</label>
                        <input type="text" name="reg_client_name" class="form-control" required maxlength="200"
                               value="<?= pc_h($r['client_name']) ?>" placeholder="e.g. GALP Eswatini">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Certificate No. *</label>
                        <input type="text" name="reg_cert_no" class="form-control" required maxlength="100"
                               value="<?= pc_h($r['cert_no']) ?>" placeholder="e.g. MS-2024-001">
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-bold">Standard / Scope *</label>
                        <input type="text" name="reg_scope" class="form-control" required maxlength="500"
                               value="<?= pc_h($r['scope']) ?>" placeholder="e.g. SZNS ISO 9001:2015 — Manufacture of plastic containers">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Effective date *</label>
                        <input type="date" name="reg_effective_date" class="form-control" required
                               value="<?= pc_h($r['effective_date']) ?>">
                        <div class="form-text">Suspended on / withdrawn on / effective from.</div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Sort order</label>
                        <input type="number" name="reg_sort_order" class="form-control" value="<?= (int)$r['sort_order'] ?>">
                        <div class="form-text">Lower = earlier.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Reason / Notes</label>
                        <input type="text" name="reg_reason_note" class="form-control" maxlength="500"
                               value="<?= pc_h($r['reason_note']) ?>" placeholder="e.g. Missed surveillance audit">
                        <div class="form-text">
                            Shown in the Reason column for suspensions and the Notes column for
                            reduced scope. The Withdrawn table has no such column, so it is
                            stored but not displayed there.
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Client logo</label>
                        <div class="mb-2">
                            <img data-crop-preview="reg_logo_preview"
                                 src="<?= !empty($r['logo_path']) ? '../' . pc_h($r['logo_path']) : '' ?>"
                                 style="max-height:70px;border:1px solid #ddd;padding:4px;background:#fff;<?= empty($r['logo_path']) ? 'display:none;' : '' ?>"
                                 onload="this.style.display='inline-block'" alt="">
                            <?php if (!empty($r['logo_path'])): ?>
                                <code class="ms-2 small text-muted"><?= pc_h($r['logo_path']) ?></code>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="reg_logo_file" class="form-control crop-input"
                               accept="image/png,image/jpeg,image/webp,image/svg+xml,image/gif"
                               data-crop-label="Client Logo">
                        <input type="hidden" name="reg_logo_cropped">
                        <div class="form-text">Optional. Shown beside the client name in the register; without one, the name appears on its own. Leave empty to keep current.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="reg_is_active" id="reg_is_active" value="1" class="form-check-input"
                                   <?= (int)$r['is_active'] === 1 ? 'checked' : '' ?>>
                            <label for="reg_is_active" class="form-check-label">Publish on the register</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="<?= pc_h($reg_self) ?>" class="btn btn-link text-decoration-none">Cancel</a>
                    <button type="submit" name="save_reg" value="1" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> <?= $reg_edit ? 'Save changes' : 'Add entry' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$reg_edit && !$reg_is_new): ?>
    <?php foreach ($REG_STATUSES as $stk => $stl): $rows = $reg_by_status[$stk]; ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong><?= pc_h($stl) ?> <span class="badge bg-secondary ms-1"><?= count($rows) ?></span></strong>
                <a href="<?= pc_h($reg_self) ?>&new_reg=1&status=<?= urlencode($stk) ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Add entry
                </a>
            </div>
            <div class="card-body p-0">
                <?php if ($rows): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Order</th>
                                    <th style="width: 90px;">Logo</th>
                                    <th>Client</th>
                                    <th style="width: 140px;">Certificate No.</th>
                                    <th>Standard / Scope</th>
                                    <th style="width: 110px;">Effective</th>
                                    <th style="width: 90px;" class="text-center">Live</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td><?= (int)$row['sort_order'] ?></td>
                                        <td>
                                            <?php if (!empty($row['logo_path'])): ?>
                                                <img src="../<?= pc_h($row['logo_path']) ?>" style="max-height:32px;max-width:80px;object-fit:contain" alt="">
                                            <?php else: ?>
                                                <span class="text-muted">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= pc_h($row['client_name']) ?></td>
                                        <td><?= pc_h($row['cert_no']) ?></td>
                                        <td class="small"><?= pc_h($row['scope']) ?></td>
                                        <td class="small text-nowrap"><?= pc_h($row['effective_date']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= pc_h($reg_self) ?>&toggle_reg=<?= (int)$row['id'] ?>" class="btn btn-sm btn-link p-0">
                                                <?php if ((int)$row['is_active'] === 1): ?>
                                                    <span class="badge bg-success">On</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Off</span>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?= pc_h($reg_self) ?>&edit_reg=<?= (int)$row['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="<?= pc_h($reg_self) ?>&delete_reg=<?= (int)$row['id'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Delete this register entry? This cannot be undone.')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-3 mb-0">
                        Nothing in this section &mdash; the public page shows its empty-state message.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

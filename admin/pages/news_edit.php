<?php
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

require_once __DIR__ . '/../../includes/cms_helpers.php';

// ── Key inventory ─────────────────────────────────────────────
$text_keys = [
    // Page-level
    'news_meta_description',
    'news_breadcrumb_label',
    'news_hero_title',

    // 6 flat item slots
    'news_item_1_title', 'news_item_1_date', 'news_item_1_body', 'news_item_1_url',
    'news_item_2_title', 'news_item_2_date', 'news_item_2_body', 'news_item_2_url',
    'news_item_3_title', 'news_item_3_date', 'news_item_3_body', 'news_item_3_url',
    'news_item_4_title', 'news_item_4_date', 'news_item_4_body', 'news_item_4_url',
    'news_item_5_title', 'news_item_5_date', 'news_item_5_body', 'news_item_5_url',
    'news_item_6_title', 'news_item_6_date', 'news_item_6_body', 'news_item_6_url',
];

$image_keys = [
    'news_item_1_image',
    'news_item_2_image',
    'news_item_3_image',
    'news_item_4_image',
    'news_item_5_image',
    'news_item_6_image',
];

// ── Save handler ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    $kv = [];
    foreach ($text_keys as $k) {
        $kv[$k] = pc_strip_text($_POST[$k] ?? '');
    }
    foreach ($image_keys as $k) {
        $path = pc_upload_image($k . '_file', ADMIN_ROOT . '/uploads/', 'news');
        if ($path !== null) $kv[$k] = $path;
    }
    $errs = pc_save_many($conn, $kv);
    set_flash($errs ? 'danger' : 'success', $errs ? 'Save errors: ' . implode(', ', $errs) : 'Saved.');
    redirect_self();
}

$pc = pc_get_many($conn, array_merge($text_keys, $image_keys));
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit News Page</h1>
    <a href="../news.php" target="_blank" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i> View Page
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="save_news" value="1">

    <!-- Page-level -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Page Header</h5>
            <div class="mb-3">
                <label class="form-label">Hero / Page Title</label>
                <input type="text" name="news_hero_title" class="form-control" value="<?= pc_h($pc['news_hero_title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Breadcrumb Label</label>
                <input type="text" name="news_breadcrumb_label" class="form-control" value="<?= pc_h($pc['news_breadcrumb_label']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Meta Description (SEO)</label>
                <textarea name="news_meta_description" class="form-control" rows="2"><?= pc_h($pc['news_meta_description']) ?></textarea>
            </div>
        </div>
    </div>

    <!-- News item slots -->
    <?php for ($i = 1; $i <= 6; $i++):
        $k_title = "news_item_{$i}_title";
        $k_date  = "news_item_{$i}_date";
        $k_body  = "news_item_{$i}_body";
        $k_image = "news_item_{$i}_image";
        $k_url   = "news_item_{$i}_url";
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">News Item <?= $i ?></h5>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="<?= $k_title ?>" class="form-control" value="<?= pc_h($pc[$k_title]) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Date</label>
                    <input type="text" name="<?= $k_date ?>" class="form-control" value="<?= pc_h($pc[$k_date]) ?>" placeholder="e.g. 21 Dec, 2023">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Category / Body</label>
                <input type="text" name="<?= $k_body ?>" class="form-control" value="<?= pc_h($pc[$k_body]) ?>" placeholder="Short label (e.g. Corporate, Update)">
            </div>
            <div class="mb-3">
                <label class="form-label">Link URL</label>
                <input type="url" name="<?= $k_url ?>" class="form-control" value="<?= pc_h($pc[$k_url]) ?>" placeholder="https://...">
            </div>
            <div class="mb-3">
                <label class="form-label">Image</label>
                <?php if (!empty($pc[$k_image])): ?>
                    <div class="mb-2">
                        <img src="../<?= pc_h(pc_image_src($pc[$k_image])) ?>" style="max-height:120px;border:1px solid #ddd" alt="current">
                    </div>
                <?php endif; ?>
                <input type="file" name="<?= $k_image ?>_file" accept="image/*" class="form-control">
                <small class="form-text text-muted">Leave empty to keep current image.</small>
            </div>
        </div>
    </div>
    <?php endfor; ?>

    <div class="mb-4">
        <button type="submit" name="save_news" class="btn btn-primary">Save Changes</button>
    </div>
</form>

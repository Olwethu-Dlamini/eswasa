<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_announcements.php';

$pc = pc_get_many($conn, $announcements_keys, $announcements_defaults);

$announcements = $conn->query("SELECT * FROM eswasa_announcements ORDER BY published_date DESC");

function ann_type_label(string $t): string {
    return [
        'news'    => 'News',
        'update'  => 'Update',
        'closure' => 'Closure',
        'event'   => 'Event',
        'policy'  => 'Policy',
    ][$t] ?? ucfirst($t);
}

function ann_type_icon(string $t): string {
    return [
        'news'    => 'fa-newspaper',
        'update'  => 'fa-bullhorn',
        'closure' => 'fa-door-closed',
        'event'   => 'fa-calendar-alt',
        'policy'  => 'fa-file-contract',
    ][$t] ?? 'fa-bullhorn';
}

function ann_file_url(string $stored): string {
    $parts = array_map('rawurlencode', explode('/', $stored));
    return 'admin/uploads/' . implode('/', $parts);
}

function ann_file_kind(string $stored): string {
    $ext = strtolower(pathinfo($stored, PATHINFO_EXTENSION));
    if ($ext === 'pdf') return 'PDF';
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) return 'Image';
    return strtoupper($ext);
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['announcements_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Archive of news, updates, and announcements from the Eswatini Standards Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        /* ESWASA theme base — locked spec (#2B3388, #fff, Arial 15px) */
        body { font-family: Arial, sans-serif; font-size: 15px; color: #2B3388; }
        body h1, body h2, body h3, body h4, body h5, body h6 { font-family: Arial, sans-serif; color: #2B3388; }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td { font-family: Arial, sans-serif; }
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Intro info-box (canonical centered title + 60px divider) */
        .info-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .info-box h3 { color: #2B3388; font-weight: 700; margin: 0; }
        .info-box.is-intro { text-align: center; }
        .info-box.is-intro .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 24px;
            border-radius: 0;
        }
        .info-box.is-intro p { text-align: left; margin-bottom: 12px; }
        .info-box.is-intro p:last-child { margin-bottom: 0; }

        /* History list — row entries with timeline-ish left rule */
        .ann-list { border: 1px solid rgba(43, 51, 136, 0.15); border-radius: 4px; background: #fff; }
        .ann-row {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.10);
            transition: background-color .15s ease;
        }
        .ann-row:last-child { border-bottom: 0; }
        .ann-row:hover { background-color: rgba(43, 51, 136, 0.03); }
        .ann-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 14px;
            margin-bottom: 8px;
            font-size: 0.85rem;
            color: rgba(43, 51, 136, 0.75);
        }
        .ann-meta .sep { color: rgba(43, 51, 136, 0.35); }
        .ann-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            border-radius: 3px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
        }
        .ann-title {
            color: #2B3388;
            font-weight: 600;
            font-size: 1.1rem;
            line-height: 1.4;
            margin: 0 0 8px 0;
        }
        .ann-title a { color: #2B3388; text-decoration: none; }
        .ann-title a:hover { text-decoration: underline; }
        .ann-desc {
            color: #2B3388;
            line-height: 1.65;
            margin: 0 0 12px 0;
            white-space: pre-line;
        }
        .ann-desc:last-child { margin-bottom: 0; }
        .ann-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .ann-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
            color: #2B3388;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .ann-btn:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }
        .ann-empty {
            padding: 40px 20px;
            text-align: center;
            color: rgba(43, 51, 136, 0.75);
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }

        @media (max-width: 575.98px) {
            .ann-row { padding: 16px 18px; }
            .ann-title { font-size: 1rem; }
            .ann-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('announcements', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['announcements_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['announcements_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['announcements_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">

                <div class="info-box is-intro">
                    <h3><?= pc_h($pc['announcements_intro_title']) ?></h3>
                    <div class="section-divider"></div>
                    <?= pc_paragraphs_html($pc['announcements_intro_body']) ?>
                </div>

                <h2><?= pc_h($pc['announcements_section_title']) ?></h2>
                <div class="section-divider mb-4" style="margin-left: 0; margin-right: 0;"></div>

                <?php if ($announcements && $announcements->num_rows > 0): ?>
                    <div class="ann-list">
                        <?php while ($a = $announcements->fetch_assoc()):
                            $type      = $a['announcement_type'];
                            $label     = ann_type_label($type);
                            $icon      = ann_type_icon($type);
                            $dateOut   = date('d M Y', strtotime($a['published_date']));
                            $filePath  = trim((string)($a['file_path'] ?? ''));
                            $extLink   = trim((string)($a['external_link'] ?? ''));
                            $hasFile   = $filePath !== '';
                            $hasLink   = $extLink !== '';
                        ?>
                            <div class="ann-row">
                                <div class="ann-meta">
                                    <span class="ann-badge"><i class="fas <?= $icon ?>"></i><?= htmlspecialchars($label) ?></span>
                                    <span><i class="far fa-calendar me-1"></i><?= $dateOut ?></span>
                                </div>

                                <h3 class="ann-title">
                                    <?php if ($hasFile): ?>
                                        <a href="<?= htmlspecialchars(ann_file_url($filePath)) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($a['title']) ?></a>
                                    <?php elseif ($hasLink): ?>
                                        <a href="<?= htmlspecialchars($extLink) ?>" target="_blank" rel="noopener"><?= htmlspecialchars($a['title']) ?></a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($a['title']) ?>
                                    <?php endif; ?>
                                </h3>

                                <?php if (!empty($a['description'])): ?>
                                    <p class="ann-desc"><?= htmlspecialchars($a['description']) ?></p>
                                <?php endif; ?>

                                <?php if ($hasFile || $hasLink): ?>
                                    <div class="ann-actions">
                                        <?php if ($hasFile): ?>
                                            <a class="ann-btn" href="<?= htmlspecialchars(ann_file_url($filePath)) ?>" target="_blank" rel="noopener">
                                                <i class="fas fa-file-pdf"></i>
                                                <?php
                                                $kind = ann_file_kind($filePath);
                                                echo $kind === 'PDF' ? 'View PDF' : ($kind === 'Image' ? 'View Image' : 'View File');
                                                ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($hasLink): ?>
                                            <a class="ann-btn" href="<?= htmlspecialchars($extLink) ?>" target="_blank" rel="noopener">
                                                <i class="fas fa-external-link-alt"></i>Visit Link
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="ann-empty">
                        <?= pc_h($pc['announcements_empty_state']) ?>
                    </div>
                <?php endif; ?>

            </div>
        </section>

    </main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>

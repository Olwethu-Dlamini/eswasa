<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_publications.php';

$pc = pc_get_many($conn, $publications_keys, $publications_defaults);

// ── Grouped "folders" ─────────────────────────────────────────────
// Sections come from eswasa_publication_groups (system type groups + custom
// folders), ordered by sort_order. Each publication lands in its custom folder
// (group_id) if assigned, otherwise the system group matching its pub_type.
// Groups with no publications are not rendered.
$groups = [];
if ($gres = $conn->query("SELECT * FROM eswasa_publication_groups ORDER BY sort_order ASC, id ASC")) {
    while ($g = $gres->fetch_assoc()) {
        $groups[(int)$g['id']] = $g;
    }
}
$type_to_group = [];
foreach ($groups as $gid => $g) {
    if (!empty($g['type_key'])) $type_to_group[$g['type_key']] = $gid;
}

$buckets = []; // group_id => array of publication rows (newest first)
if ($pres = $conn->query("SELECT * FROM eswasa_publications ORDER BY published_date DESC, id DESC")) {
    while ($pub = $pres->fetch_assoc()) {
        $gid = !empty($pub['group_id']) ? (int)$pub['group_id'] : null;
        // Assigned folder missing (e.g. deleted) → fall back to the type group.
        if ($gid === null || !isset($groups[$gid])) {
            $gid = $type_to_group[$pub['pub_type']] ?? null;
        }
        if ($gid === null) continue; // unknown type with no home — skip defensively
        $buckets[$gid][] = $pub;
    }
}

$has_any_pub = false;
foreach ($groups as $gid => $g) {
    if (!empty($buckets[$gid])) { $has_any_pub = true; break; }
}

function pub_type_label(string $t): string {
    return [
        'standard'      => 'Standard',
        'report'        => 'Report',
        'guidance'      => 'Guidance Document',
        'newsletter'    => 'Newsletter',
        'annual_report' => 'Annual Report',
    ][$t] ?? ucfirst($t);
}

function pub_file_url(string $stored): string {
    // file_path is stored as e.g. "publications/foo.pdf"; live under /admin/uploads/
    $parts = array_map('rawurlencode', explode('/', $stored));
    return 'admin/uploads/' . implode('/', $parts);
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['publications_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Access publications, reports, and documents from the Eswatini Standards Authority (ESWASA).">
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

        /* Grouped folders */
        .pub-group { margin-bottom: 38px; }
        .pub-group:last-child { margin-bottom: 0; }
        .pub-group-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #2B3388;
            margin: 0 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid rgba(43, 51, 136, 0.15);
        }
        .pub-group-title i { font-size: 1rem; color: rgba(43, 51, 136, 0.65); }
        .pub-group-count {
            margin-left: auto;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(43, 51, 136, 0.70);
            border: 1px solid rgba(43, 51, 136, 0.20);
            border-radius: 999px;
            padding: 1px 10px;
        }

        /* Documents list — row-style, not cards */
        .pub-list {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            background: #fff;
            overflow: hidden;
        }
        .pub-row {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 22px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.10);
            transition: background-color .15s ease;
        }
        .pub-row:last-child { border-bottom: 0; }
        .pub-row:hover { background-color: rgba(43, 51, 136, 0.03); }
        .pub-icon {
            flex: 0 0 auto;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.20);
            border-radius: 4px;
            font-size: 16px;
        }
        .pub-main { flex: 1 1 auto; min-width: 0; }
        .pub-title {
            display: block;
            color: #2B3388;
            font-weight: 600;
            font-size: 1.05rem;
            text-decoration: none;
            margin-bottom: 4px;
            word-break: break-word;
        }
        .pub-title:hover { color: #2B3388; text-decoration: underline; }
        .pub-meta {
            font-size: 0.85rem;
            color: rgba(43, 51, 136, 0.75);
            display: flex;
            flex-wrap: wrap;
            gap: 4px 12px;
        }
        .pub-meta .sep { color: rgba(43, 51, 136, 0.35); }
        .pub-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
        }
        .pub-download {
            flex: 0 0 auto;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .pub-download:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }
        .pub-empty {
            padding: 40px 20px;
            text-align: center;
            color: rgba(43, 51, 136, 0.75);
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }

        @media (max-width: 575.98px) {
            .pub-row { flex-wrap: wrap; padding: 14px 16px; gap: 12px; }
            .pub-icon { width: 34px; height: 34px; }
            .pub-download { width: 100%; text-align: center; }
            .pub-title { font-size: 1rem; }
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('publications', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['publications_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['publications_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['publications_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">

                <div class="info-box is-intro">
                    <h3><?= pc_h($pc['publications_intro_title']) ?></h3>
                    <div class="section-divider"></div>
                    <?= pc_paragraphs_html($pc['publications_intro_body']) ?>
                </div>

                <h2><?= pc_h($pc['publications_section_title']) ?></h2>
                <div class="section-divider mb-4" style="margin-left: 0; margin-right: 0;"></div>

                <?php if ($has_any_pub): ?>
                    <?php foreach ($groups as $gid => $g): ?>
                        <?php if (empty($buckets[$gid])) continue; // hide empty folders ?>
                        <div class="pub-group">
                            <h3 class="pub-group-title">
                                <i class="fas fa-folder" aria-hidden="true"></i>
                                <?= htmlspecialchars($g['name']) ?>
                                <span class="pub-group-count"><?= count($buckets[$gid]) ?></span>
                            </h3>
                            <div class="pub-list">
                                <?php foreach ($buckets[$gid] as $pub): ?>
                                    <?php
                                    $url = pub_file_url($pub['file_path']);
                                    $fullPath = __DIR__ . '/admin/uploads/' . $pub['file_path'];
                                    $sizeStr = '';
                                    if (file_exists($fullPath)) {
                                        $bytes = filesize($fullPath);
                                        $sizeStr = $bytes >= 1048576
                                            ? round($bytes / 1048576, 1) . ' MB'
                                            : round($bytes / 1024, 0) . ' KB';
                                    }
                                    ?>
                                    <div class="pub-row">
                                        <span class="pub-icon" aria-hidden="true"><i class="fas fa-file-pdf"></i></span>
                                        <div class="pub-main">
                                            <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="pub-title">
                                                <?= htmlspecialchars($pub['title']) ?>
                                            </a>
                                            <div class="pub-meta">
                                                <span class="pub-badge"><?= htmlspecialchars(pub_type_label($pub['pub_type'])) ?></span>
                                                <span>Published <?= date('d M Y', strtotime($pub['published_date'])) ?></span>
                                                <?php if ($sizeStr !== ''): ?>
                                                    <span class="sep">&middot;</span>
                                                    <span>PDF, <?= $sizeStr ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="pub-download">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="pub-empty">
                        <?= pc_h($pc['publications_empty_state']) ?>
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

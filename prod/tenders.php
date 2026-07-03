<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_tenders.php';

$pc = pc_get_many($conn, $tenders_keys, $tenders_defaults);

// ── Load tenders, split Open vs Closed by closing_date ────────────
$today  = date('Y-m-d');
$open   = [];
$closed = [];
if ($tres = $conn->query('SELECT * FROM eswasa_tenders ORDER BY closing_date ASC')) {
    while ($t = $tres->fetch_assoc()) {
        if ($t['closing_date'] >= $today) $open[] = $t;   // soonest deadline first
        else                              $closed[] = $t;
    }
}
// Closed archive: most recently closed first
usort($closed, function ($a, $b) { return strcmp($b['closing_date'], $a['closing_date']); });

// All documents bucketed by tender (one query, no N+1)
$docs_by_tender = [];
if ($dres = $conn->query('SELECT * FROM eswasa_tender_documents ORDER BY sort_order ASC, id ASC')) {
    while ($d = $dres->fetch_assoc()) {
        $docs_by_tender[(int)$d['tender_id']][] = $d;
    }
}

function tender_doc_url(string $stored): string {
    // file_path stored as e.g. "tenders/foo.pdf"; lives under /admin/uploads/
    $parts = array_map('rawurlencode', explode('/', $stored));
    return 'admin/uploads/' . implode('/', $parts);
}
function tender_file_size(string $stored): string {
    $full = __DIR__ . '/admin/uploads/' . $stored;
    if (!file_exists($full)) return '';
    $bytes = filesize($full);
    return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : round($bytes / 1024, 0) . ' KB';
}

/** Render one tender block. */
function render_tender(array $t, array $docs, bool $isOpen): void {
    $closing = date('d M Y', strtotime($t['closing_date']));
    ?>
    <div class="tender-card<?= $isOpen ? '' : ' is-closed' ?>">
        <div class="tender-head">
            <h3 class="tender-title"><?= htmlspecialchars($t['title']) ?></h3>
            <span class="tender-status <?= $isOpen ? 'open' : 'closed' ?>"><?= $isOpen ? 'Open' : 'Closed' ?></span>
        </div>
        <div class="tender-meta">
            <?php if (!empty($t['reference_no'])): ?>
                <span><strong>Ref:</strong> <?= htmlspecialchars($t['reference_no']) ?></span>
            <?php endif; ?>
            <?php if (!empty($t['category'])): ?>
                <span><strong>Category:</strong> <?= htmlspecialchars($t['category']) ?></span>
            <?php endif; ?>
            <span class="tender-deadline">
                <i class="fas fa-clock" aria-hidden="true"></i>
                <?= $isOpen ? 'Closes' : 'Closed' ?> <?= $closing ?>
            </span>
        </div>
        <?php if (!empty($t['description'])): ?>
            <div class="tender-desc"><?= nl2br(htmlspecialchars($t['description'])) ?></div>
        <?php endif; ?>
        <?php if (!empty($docs)): ?>
            <div class="tender-docs">
                <span class="tender-docs-label">Bid documents</span>
                <ul class="tender-doc-list">
                    <?php foreach ($docs as $d):
                        $url = tender_doc_url($d['file_path']);
                        $size = tender_file_size($d['file_path']);
                    ?>
                        <li>
                            <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="tender-doc">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                <span class="tender-doc-name"><?= htmlspecialchars($d['label']) ?></span>
                                <span class="tender-doc-size">PDF<?= $size !== '' ? ', ' . $size : '' ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <p class="tender-nodocs">Documents will be published shortly.</p>
        <?php endif; ?>
    </div>
    <?php
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['tenders_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Current procurement opportunities, tender notices, bid documents and submission deadlines from the Eswatini Standards Authority (ESWASA).">
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
            width: 60px; height: 2px; background: #2B3388;
            margin: 16px auto 24px; border-radius: 0;
        }
        .info-box.is-intro p { text-align: left; margin-bottom: 12px; }
        .info-box.is-intro p:last-child { margin-bottom: 0; }

        .tenders-section-title { font-size: 1.35rem; font-weight: 700; color: #2B3388; margin: 0; }
        .section-divider { width: 60px; height: 2px; background: #2B3388; border-radius: 0; }

        /* Tender card */
        .tender-card {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            background: #fff;
            padding: 22px 24px;
            margin-bottom: 20px;
        }
        .tender-card.is-closed { border-left-color: rgba(43, 51, 136, 0.30); opacity: 0.92; }
        .tender-head { display: flex; align-items: flex-start; gap: 12px; }
        .tender-title { font-size: 1.2rem; font-weight: 700; color: #2B3388; margin: 0; flex: 1 1 auto; }
        .tender-status {
            flex: 0 0 auto;
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
            padding: 3px 10px; border-radius: 3px;
        }
        .tender-status.open   { background: #2B3388; color: #fff; }
        .tender-status.closed { background: #fff; color: rgba(43, 51, 136, 0.70); border: 1px solid rgba(43, 51, 136, 0.30); }
        .tender-meta {
            display: flex; flex-wrap: wrap; gap: 6px 22px;
            font-size: 0.9rem; color: rgba(43, 51, 136, 0.85);
            margin: 10px 0 14px; padding-bottom: 12px;
            border-bottom: 1px dashed rgba(43, 51, 136, 0.15);
        }
        .tender-deadline { font-weight: 700; color: #2B3388; }
        .tender-deadline i { margin-right: 5px; }
        .tender-desc { line-height: 1.6; margin-bottom: 16px; }
        .tender-docs-label {
            display: block; font-weight: 700; font-size: 0.85rem;
            text-transform: uppercase; letter-spacing: 0.02em;
            color: rgba(43, 51, 136, 0.70); margin-bottom: 8px;
        }
        .tender-doc-list { list-style: none; padding: 0; margin: 0; }
        .tender-doc-list li { margin-bottom: 8px; }
        .tender-doc {
            display: inline-flex; align-items: center; gap: 10px;
            color: #2B3388; text-decoration: none;
            border: 1px solid rgba(43, 51, 136, 0.20); border-radius: 4px;
            padding: 8px 14px; font-weight: 600;
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }
        .tender-doc:hover { background: #2B3388; color: #fff; border-color: #2B3388; }
        .tender-doc:hover .tender-doc-size { color: rgba(255,255,255,0.85); }
        .tender-doc-size { font-weight: 400; font-size: 0.8rem; color: rgba(43, 51, 136, 0.65); }
        .tender-nodocs { font-style: italic; color: rgba(43, 51, 136, 0.70); margin: 0; }

        .tenders-empty {
            padding: 40px 20px; text-align: center;
            color: rgba(43, 51, 136, 0.75); background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15); border-radius: 4px;
        }
        .tenders-closed-wrap { margin-top: 44px; }

        @media (max-width: 575.98px) {
            .tender-card { padding: 18px 16px; }
            .tender-head { flex-wrap: wrap; }
            .tender-doc { width: 100%; }
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('tenders', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['tenders_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['tenders_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['tenders_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">

                <div class="info-box is-intro">
                    <h3><?= pc_h($pc['tenders_intro_title']) ?></h3>
                    <div class="section-divider"></div>
                    <?= pc_paragraphs_html($pc['tenders_intro_body']) ?>
                </div>

                <!-- Open tenders -->
                <h2 class="tenders-section-title"><?= pc_h($pc['tenders_open_title']) ?></h2>
                <div class="section-divider mb-4" style="margin-left:0;"></div>

                <?php if (!empty($open)): ?>
                    <?php foreach ($open as $t): ?>
                        <?php render_tender($t, $docs_by_tender[(int)$t['id']] ?? [], true); ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="tenders-empty"><?= pc_h($pc['tenders_empty_state']) ?></div>
                <?php endif; ?>

                <!-- Closed tenders archive (only if any) -->
                <?php if (!empty($closed)): ?>
                    <div class="tenders-closed-wrap">
                        <h2 class="tenders-section-title"><?= pc_h($pc['tenders_closed_title']) ?></h2>
                        <div class="section-divider mb-4" style="margin-left:0;"></div>
                        <?php foreach ($closed as $t): ?>
                            <?php render_tender($t, $docs_by_tender[(int)$t['id']] ?? [], false); ?>
                        <?php endforeach; ?>
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

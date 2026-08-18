<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_service_charter.php';

$pc = pc_get_many($conn, $service_charter_keys, $service_charter_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['service_charter_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="The ESWASA Service Charter — our commitments to customers on accessibility, turnaround times, quality of service, impartiality and how to escalate complaints.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
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

        /* Intro info-box (canonical centered title + 60px divider) — NO blue-left accent */
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

        /* Charter blocks — borders over shadows, no left accent */
        .charter-section { padding: 50px 0 70px; }
        .charter-section-title {
            color: #2B3388;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
        }
        .charter-section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 14px auto 30px;
            border-radius: 0;
        }
        .charter-block {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 24px 26px;
            margin-bottom: 18px;
        }
        .charter-block h3 {
            color: #2B3388;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .charter-block p,
        .charter-block li {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 10px;
        }
        .charter-block ul { padding-left: 20px; margin: 0; }

        /* Service Standards grid — flat cells (no blue left bar) */
        .commitment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 16px;
        }
        .commitment-item {
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 14px 16px;
            border-radius: 4px;
        }
        .commitment-item strong { display: block; color: #2B3388; margin-bottom: 4px; font-size: 0.95rem; }
        .commitment-item span { color: #2B3388; font-size: 0.92rem; line-height: 1.5; }

        /* Contact CTA — brand-blue panel */
        .contact-cta {
            background: #2B3388;
            color: #fff;
            padding: 30px;
            border-radius: 4px;
            text-align: center;
            margin-top: 30px;
        }
        .contact-cta h3 { color: #fff; font-weight: 700; margin: 0 0 10px; }
        .contact-cta p { color: rgba(255,255,255,0.92); margin: 0 0 18px; line-height: 1.6; }
        .contact-cta .btn-charter {
            display: inline-block;
            background: #fff;
            color: #2B3388 !important;
            padding: 10px 22px;
            border-radius: 3px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.95rem;
            transition: background .2s ease;
        }
        .contact-cta .btn-charter:hover { background: rgba(255,255,255,0.88); }

        @media (max-width: 767.98px) {
            .charter-section { padding: 30px 0 40px; }
            .charter-section-title { font-size: 1.25rem; }
            .commitment-grid { grid-template-columns: 1fr; }
            .charter-block { padding: 20px 18px; }
            .breadcrumb-content .title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('service-charter', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['service_charter_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['service_charter_breadcrumb_parent_label']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['service_charter_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['service_charter_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="charter-section">
            <div class="container">

                <h2 class="charter-section-title"><?= pc_h($pc['service_charter_section_title']) ?></h2>
                <div class="charter-section-divider"></div>

                <div class="info-box is-intro">
                    <?= pc_paragraphs_html($pc['service_charter_intro_body']) ?>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <?php /* These five blocks were hardcoded until Batch B; they now come
                                 from page_content with the previous wording as defaults, so the
                                 page renders identically until someone edits it.
                                 See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (B4). */ ?>
                        <div class="charter-block">
                            <h3><?= pc_h($pc['charter_who_title']) ?></h3>
                            <?= pc_paragraphs_html($pc['charter_who_body']) ?>
                        </div>

                        <div class="charter-block">
                            <h3><?= pc_h($pc['charter_standards_title']) ?></h3>
                            <p><?= pc_h($pc['charter_standards_intro']) ?></p>
                            <div class="commitment-grid">
                                <?php for ($i = 1; $i <= 8; $i++):
                                    $cl = trim((string)$pc["charter_commit_{$i}_label"]);
                                    $cb = trim((string)$pc["charter_commit_{$i}_body"]);
                                    if ($cl === '' && $cb === '') continue; ?>
                                <div class="commitment-item">
                                    <strong><?= pc_h($cl) ?></strong>
                                    <span><?= pc_h($cb) ?></span>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="charter-block">
                            <h3><?= pc_h($pc['charter_values_title']) ?></h3>
                            <ul>
                                <?= pc_list_items($pc['charter_values_items'], true) ?>
                            </ul>
                        </div>

                        <div class="charter-block">
                            <h3><?= pc_h($pc['charter_ask_title']) ?></h3>
                            <p><?= pc_h($pc['charter_ask_intro']) ?></p>
                            <ul>
                                <?= pc_list_items($pc['charter_ask_items']) ?>
                            </ul>
                        </div>

                        <div class="charter-block">
                            <h3><?= pc_h($pc['charter_short_title']) ?></h3>
                            <p><?= pc_h($pc['charter_short_intro']) ?></p>
                            <ul>
                                <?php for ($i = 1; $i <= 6; $i++):
                                    $t = trim((string)$pc["charter_short_{$i}_text"]);
                                    if ($t === '') continue;
                                    $u = trim((string)$pc["charter_short_{$i}_url"]); ?>
                                    <li><?php if ($u !== ''): ?><a href="<?= pc_h($u) ?>"><?= pc_h($t) ?></a><?php else: ?><?= pc_h($t) ?><?php endif; ?></li>
                                <?php endfor; ?>
                            </ul>
                            <?php if (trim((string)$pc['charter_short_outro']) !== ''): ?>
                                <p><?= pc_h($pc['charter_short_outro']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="contact-cta">
                            <h3><?= pc_h($pc['service_charter_cta_title']) ?></h3>
                            <p><?= pc_h($pc['service_charter_cta_body']) ?></p>
                            <a href="<?= pc_h($pc['service_charter_cta_button_url']) ?>" class="btn-charter"><?= pc_h($pc['service_charter_cta_button_text']) ?></a>
                        </div>

                    </div>
                </div>
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

<?php
require_once __DIR__ . '/includes/env.php';
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';

/*
 * Certification Status hub.
 *
 * Each of the three certification marks now has its own public register, so
 * this page — the URL the site and sitemap.xml have always pointed at — lists
 * them rather than 404ing or silently showing only one scheme's entries.
 */
$cert_registers = [
    ['url' => 'certification-status-management-systems.php', 'title' => 'Management Systems Certification', 'body' => 'Certifications issued under the ESWASA Management Systems Certification Scheme — quality, environmental, food safety and occupational health.'],
    ['url' => 'certification-status-product.php',            'title' => 'Product Certification',            'body' => 'Permits issued under the ESWASA Product Certification Scheme for products manufactured to declared national and international standards.'],
    ['url' => 'certification-status-ingelo.php',             'title' => 'Ingelo Certification',             'body' => 'Certifications issued under the Ingelo MSME Product Certification Scheme for micro, small and medium enterprises and local producers.'],
];

// Live counts, so a visitor can see at a glance which registers hold entries.
$cert_register_counts = ['ms' => 0, 'product' => 0, 'ingelo' => 0];
$cnt_res = $conn->query('SELECT scheme, COUNT(*) AS n FROM certification_register WHERE is_active = 1 GROUP BY scheme');
if ($cnt_res) {
    while ($cnt_row = $cnt_res->fetch_assoc()) {
        // Ignore any scheme value that isn't one of the three registers, so a
        // stray row can't add a fourth card.
        if (isset($cert_register_counts[$cnt_row['scheme']])) {
            $cert_register_counts[$cnt_row['scheme']] = (int)$cnt_row['n'];
        }
    }
}
$cert_register_schemes = ['ms', 'product', 'ingelo'];

$pc = pc_get_many($conn, [
    'cert_status_hub_title',
    'cert_status_hub_subtitle',
    'cert_status_hub_intro',
], [
    'cert_status_hub_title'    => 'Certification Status Register',
    'cert_status_hub_subtitle' => 'Public record of suspended, withdrawn and reduced-scope certifications',
    'cert_status_hub_intro'    => "In accordance with the Suspension / Withdrawal / Reduced Scope of Certification Procedure (CER_PR_026), ESWASA publishes information on the certified status of clients whose certification has been suspended, withdrawn or reduced in scope. A separate register is published for each certification mark. Choose the register for the mark you are verifying.",
]);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['cert_status_hub_title']) ?> - ESWASA</title>
    <meta name="description" content="Public registers of suspended, withdrawn and reduced-scope ESWASA certifications, by certification mark.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="includes/cta-section.css">

    <style>
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 15px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        .display-6 { color: #2B3388; font-weight: 700; letter-spacing: -0.01em; }
        .section-divider {
            width: 60px; height: 2px; background: #2B3388;
            margin: 16px auto 0; border-radius: 0;
        }

        .intro-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            padding: 26px 28px;
            max-width: 920px;
            margin: 0 auto 40px;
        }
        .intro-card p {
            margin: 0; color: #2B3388;
            font-size: 15px; line-height: 1.7;
        }

        .status-block { margin-bottom: 42px; }
        .status-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2B3388;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2B3388;
        }
        .status-title .count {
            display: inline-block;
            background: #2B3388;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 12px;
            letter-spacing: 0.4px;
        }

        .status-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            overflow: hidden;
        }
        .status-table th,
        .status-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(43, 51, 136, 0.10);
            font-size: 0.95rem;
            vertical-align: top;
        }
        .status-table th {
            background-color: #2B3388;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-table tr:last-child td { border-bottom: none; }
        .status-table tr:nth-child(even) td {
            background-color: rgba(43, 51, 136, 0.03);
        }
        .status-table .client-with-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-table .client-logo {
            max-width: 64px;
            max-height: 40px;
            width: auto;
            height: auto;
            object-fit: contain;
            flex: 0 0 auto;
        }
        .status-table .client-name { font-weight: 600; }
        .status-table .cert-no { font-weight: 600; white-space: nowrap; }
        .status-table .date    { white-space: nowrap; color: #2B3388; }

        .empty-state {
            background: #fff;
            border: 1px dashed rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            padding: 22px 24px;
            color: #2B3388;
            font-style: italic;
        }

        .footer-note {
            margin-top: 50px;
            padding: 22px 24px;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            font-size: 0.95rem;
            color: #2B3388;
            line-height: 1.7;
        }
        .footer-note strong { color: #2B3388; }
        .footer-note a {
            color: #2B3388;
            text-decoration: underline;
            font-weight: 600;
        }
        .footer-note a:hover { color: #2B3388; }

        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .display-6 { font-size: 1.55rem !important; }
            .intro-card { padding: 20px 18px; }
            .status-title { font-size: 1.1rem; flex-wrap: wrap; }
            .status-table { display: block; overflow-x: auto; }
            .status-table th, .status-table td { font-size: 0.88rem; padding: 10px; }
            .footer-note { padding: 16px 18px; font-size: 0.9rem; }
        }

        .register-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
            max-width: 1080px;
            margin: 0 auto;
        }
        .register-card {
            display: block;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            padding: 24px 26px;
            text-decoration: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .register-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .register-card h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 10px;
            color: #2B3388;
        }
        .register-card h3 .count {
            background: #2B3388;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 2px 10px;
            flex: 0 0 auto;
        }
        .register-card p {
            margin: 0 0 14px;
            font-size: 0.95rem;
            line-height: 1.65;
            color: #2B3388;
        }
        .register-cta {
            font-weight: 600;
            font-size: 0.92rem;
            color: #2B3388;
        }
        .register-cta i { margin-left: 4px; }
    </style>
</head>

<body>

    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner">
            <div class="sk-dot1"></div><div class="sk-dot2"></div>
            <div class="rect3"></div><div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('certification_status', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="Certification.php">Certification</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Certification Status</span>
                            </nav>
                            <h1 class="title"><?= pc_h($pc['cert_status_hub_title']) ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold"><?= pc_h($pc['cert_status_hub_title']) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc['cert_status_hub_subtitle']) ?></p>
                    <div class="section-divider"></div>
                </div>

                <div class="intro-card">
                    <?= pc_paragraphs_html($pc['cert_status_hub_intro']) ?>
                </div>

                <div class="register-grid">
                    <?php foreach ($cert_registers as $i => $reg):
                        $n = $cert_register_counts[$cert_register_schemes[$i]];
                    ?>
                    <a href="<?= pc_h($reg['url']) ?>" class="register-card">
                        <h3>
                            <span><?= pc_h($reg['title']) ?></span>
                            <span class="count"><?= $n ?></span>
                        </h3>
                        <p><?= pc_h($reg['body']) ?></p>
                        <span class="register-cta">
                            <?= $n === 0 ? 'View register — no current entries' : 'View register' ?>
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>
    <!-- main-area-end -->

    <!-- footer-area -->
    <?php include("includes/footer.php")?>
    <!-- footer-area-end -->

    <!-- JS here -->
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.odometer.min.js"></script>
    <script src="assets/js/jquery.appear.js"></script>
    <script src="assets/js/tween-max.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-animation.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/form-contact.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>

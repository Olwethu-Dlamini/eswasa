<?php
include_once 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';

$work_keys = [
    'work_page_title',
    'work_meta_description',
    'work_breadcrumb_crumb_1',
    'work_breadcrumb_crumb_2',
    'work_breadcrumb_title',
    'work_intro_title',
    'work_intro_body',
    'work_section_title',
    'work_cta_1_text', 'work_cta_1_url',
    'work_cta_2_text', 'work_cta_2_url',
];

$work_defaults = [
    'work_page_title'         => 'Work Programmes - ESWASA',
    'work_meta_description'   => "View ESWASA's current and past Work Programmes for Standards Development.",
    'work_breadcrumb_crumb_1' => 'Standards',
    'work_breadcrumb_crumb_2' => 'Work Programmes',
    'work_breadcrumb_title'   => 'Standards Work Programmes',
    'work_intro_title'        => 'ESWASA Standards Development Work Programmes',
    'work_intro_body'         => "The **ESWASA Work Programme** details all current and scheduled standards development and revision projects. This programme is derived from national needs assessments and stakeholder requests, ensuring that the standards developed align with Eswatini's economic and regulatory priorities.\n\nInterested stakeholders are invited to review the programme and provide feedback. For more information on specific projects, please contact us directly.",
    'work_section_title'      => 'Current and Recent Projects',

    'work_cta_1_text' => 'Propose a Standard Project',
    'work_cta_1_url'  => 'Standards.php',
    'work_cta_2_text' => 'General Enquiries',
    'work_cta_2_url'  => 'contact.php',
];

$pc = pc_get_many($conn, $work_keys, $work_defaults);

// Programmes from the dedicated table. Wrapped in try/catch so a missing
// table on legacy environments doesn't break the page.
$work_programmes = [];
try {
    if ($res = @$conn->query("SELECT title, url, details, status_label, status_class FROM eswasa_work_programmes ORDER BY sort_order ASC, id ASC")) {
        while ($r = $res->fetch_assoc()) {
            if (trim((string)($r['title'] ?? '')) !== '') $work_programmes[] = $r;
        }
    }
} catch (\Throwable $e) {
    // Table missing — render empty state.
}
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['work_page_title']) ?></title>
    <meta name="description" content="<?= pc_h($pc['work_meta_description']) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

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

        /* Button Style */
        .btn-wp {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
            margin: 5px;
            padding: 10px 30px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .btn-wp:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Introduction Section (Clean Professional Box) */
        .intro-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 40px;
            margin: 40px 0 60px 0;
            border-radius: 4px;
        }
        .intro-box { text-align: center; }
        .intro-box h3 {
            color: #2B3388;
            margin-top: 0;
            margin-bottom: 0;
            font-weight: 700;
        }
        .intro-box .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 24px;
            border-radius: 0;
        }
        .intro-box p { text-align: left; }
        .intro-box p {
            font-size: 15px;
            line-height: 1.6;
            color: #2B3388;
        }

        /* Work Programme List Styling - Card Look */
        .wp-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            margin-bottom: 12px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.04);
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .wp-list-item:hover {
            box-shadow: 0 4px 8px rgba(43, 51, 136, 0.07);
            border-color: #2B3388;
        }

        .wp-content {
            flex-grow: 1;
        }
        .wp-title {
            font-weight: 600;
            color: #2B3388;
            font-size: 1.1em;
            margin-bottom: 5px;
            transition: color 0.2s;
        }
        .wp-title a {
            color: inherit;
            text-decoration: none;
        }
        .wp-title a:hover {
            color: #2B3388;
            text-decoration: underline;
        }
        .wp-details {
            font-size: 0.9em;
            color: #2B3388;
        }
        .wp-status {
            text-align: right;
            padding-left: 30px;
            min-width: 150px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85em;
            text-transform: uppercase;
            background-color: #2B3388;
            color: #fff;
        }
        .status-published {
            background-color: #2B3388;
            color: #fff;
        }
        .status-underdev {
            background-color: #fff;
            color: #2B3388;
            border: 1px solid #2B3388;
        }
        .status-revision {
            background-color: rgba(43, 51, 136, 0.15);
            color: #2B3388;
        }

        @media (max-width: 767.98px) {
            .intro-box { padding: 24px; margin: 24px 0 32px 0; }
            .intro-box h3 { font-size: 1.2rem; }
            .wp-list-item { flex-direction: column; align-items: flex-start; padding: 16px; }
            .wp-status { text-align: left; padding-left: 0; min-width: 0; margin-top: 10px; }
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

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('work', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['work_breadcrumb_crumb_1']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['work_breadcrumb_crumb_2']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['work_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <div class="container">
                <div class="intro-box">
                    <h3><?= pc_h($pc['work_intro_title']) ?></h3>
                    <div class="section-divider"></div>
                    <?= pc_paragraphs_html($pc['work_intro_body']) ?>
                </div>

                <div class="section__title text-center mt-5 mb-4">
                    <h2 class="title" style="color:#2B3388; font-weight:700;"><?= pc_h($pc['work_section_title']) ?></h2>
                    <div class="section-divider"></div>
                </div>

                <div class="wp-list-container">
                    <?php if (empty($work_programmes)): ?>
                        <div class="text-center text-muted py-4" style="border: 1px dashed rgba(43,51,136,0.25); border-radius: 4px;">
                            <p class="mb-0" style="color:#2B3388;">No projects to display yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($work_programmes as $p): ?>
                        <div class="wp-list-item">
                            <div class="wp-content">
                                <div class="wp-title">
                                    <?php if (!empty($p['url'])): ?>
                                        <a href="<?= pc_h($p['url']) ?>"><?= pc_h($p['title']) ?></a>
                                    <?php else: ?>
                                        <?= pc_h($p['title']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="wp-details"><?= pc_h($p['details']) ?></div>
                            </div>
                            <div class="wp-status">
                                <span class="status-badge <?= pc_h($p['status_class']) ?>"><?= pc_h($p['status_label']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center my-5 pt-4">
                    <a href="<?= pc_h($pc['work_cta_1_url']) ?>" class="btn-cta"><?= pc_h($pc['work_cta_1_text']) ?></a>
                    <a href="<?= pc_h($pc['work_cta_2_url']) ?>" class="btn-cta"><?= pc_h($pc['work_cta_2_text']) ?></a>
                </div>

            </div>
        </section>

    </main>
    <?php include("includes/footer.php")?>
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

<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once __DIR__ . '/includes/breadcrumb_helper.php';

$tcp_keys = [
    // Hero / breadcrumb
    'tcp_hero_title',
    // Intro box
    'tcp_intro_title',
    'tcp_intro_body',
    // Benefits section header
    'tcp_benefits_title',
    // 4 benefit cards
    'tcp_benefit_1_title', 'tcp_benefit_1_body',
    'tcp_benefit_2_title', 'tcp_benefit_2_body',
    'tcp_benefit_3_title', 'tcp_benefit_3_body',
    'tcp_benefit_4_title', 'tcp_benefit_4_body',
    // Application section
    'tcp_apply_title',
    'tcp_apply_body',
    'tcp_apply_eligibility',
    'tcp_apply_button_text',
    'tcp_apply_button_url',
    'tcp_apply_contact_label',
    'tcp_apply_contact_email',
    // CTA buttons
    'tcp_cta_btn_1_text', 'tcp_cta_btn_1_url',
    'tcp_cta_btn_2_text', 'tcp_cta_btn_2_url',
];

$tcp_defaults = [
    'tcp_hero_title'          => 'Technical Committee Platform',
    'tcp_intro_title'         => 'About Technical Committees (TCs)',
    'tcp_intro_body'          => "Technical Committees (TCs) are the cornerstone of the ESWASA standards development process. They are composed of volunteers who are qualified in the subject matter and represent a balance of interested parties, including producers, users, consumers, government, and other relevant stakeholders.\n\nTCs are responsible for developing, maintaining, and revising Eswatini National Standards (SZNS) within their specific technical areas. They ensure that standards are developed through a consensus-based process, reflecting the needs and expertise of all relevant parties.",
    'tcp_benefits_title'      => 'Key Benefits of Joining an ESWASA TC',
    'tcp_benefit_1_title'     => 'Market Expansion',
    'tcp_benefit_1_body'      => 'Contribute to standards that facilitate trade and regional integration. Participation ensures your products and services meet international benchmarks, opening doors to new domestic and global markets.',
    'tcp_benefit_2_title'     => 'Operational Optimization',
    'tcp_benefit_2_body'      => 'Gain early access to best practices in Quality & Management Systems (e.g., ISO 9001, ISO 45001). Implement efficient, safety-focused processes before they become mandatory, reducing waste and costs.',
    'tcp_benefit_3_title'     => 'Customer Trust Building',
    'tcp_benefit_3_body'      => 'Shape standards for critical areas like Food Safety and Product Quality. Demonstrating commitment to Eswatini National Standards (SZNS) strengthens brand reputation and consumer confidence.',
    'tcp_benefit_4_title'     => 'Regulatory Compliance',
    'tcp_benefit_4_body'      => 'Influence the technical requirements that may become government regulations. By contributing, you ensure standards are practical and achievable for your sector, easing future compliance burdens.',
    'tcp_apply_title'         => 'Apply to be a TC Member',
    'tcp_apply_body'          => "Becoming a member of an ESWASA Technical Committee is a great way to contribute to the development of standards that impact your industry and society. Members gain valuable insights, network with experts, and help shape the future of their technical field.",
    'tcp_apply_eligibility'   => 'Eligibility: Membership is open to individuals with relevant expertise and a commitment to the standards development process.',
    'tcp_apply_button_text'   => 'Download TC Membership Application (PDF)',
    // Was admin/uploads/tc_membership_application.pdf, which has never existed —
    // the Apply button 404'd on the live page. Points at the registration form
    // that is actually in the repository; staff can upload a replacement through
    // the CMS. See docs/superpowers/specs/2026-08-18-cms-batch-c-design.md (C2).
    'tcp_apply_button_url'    => 'assets/forms/TEC_SDU_FO_004_Technical_Committee_Registration_Form.doc',
    'tcp_apply_contact_label' => 'Please submit the completed form to',
    'tcp_apply_contact_email' => 'info@eswasa.co.sz',
    'tcp_cta_btn_1_text'      => 'Learn About Standards Development',
    'tcp_cta_btn_1_url'       => 'Standards.php',
    'tcp_cta_btn_2_text'      => 'Contact Us',
    'tcp_cta_btn_2_url'       => 'contact.php',
];

$pc = pc_get_many($conn, $tcp_keys, $tcp_defaults);
?>
<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Technical Committee Platform - ESWASA</title>
    <meta name="description" content="Learn about ESWASA's Technical Committees and how to apply to become a member.">
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

        /* General TC Button Style */
        .btn-tc {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
            margin: 5px;
            transition: background-color 0.3s;
        }
        .btn-tc:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Introduction Box */
        .intro-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 30px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .intro-box h3 {
            color: #2B3388;
            margin-top: 0;
            border-bottom: 2px solid #2B3388;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: inline-block;
        }

        /* Professional Benefit Card Styling */
        .tc-benefit-card {
            position: relative;
            background-color: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(43, 51, 136, 0.02);
            padding: 25px;
            min-height: 250px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .tc-benefit-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 10px rgba(43, 51, 136, 0.07);
        }
        .tc-benefit-card h4 {
            color: #2B3388;
            font-weight: 700;
            margin-top: 0;
            padding-left: 60px;
        }

        /* Icon Box Styling */
        .icon-box {
            position: absolute;
            top: 25px;
            left: 20px;
            width: 35px;
            height: 35px;
            line-height: 35px;
            text-align: center;
            border-radius: 4px;
            background-color: #2B3388;
            color: #fff;
            font-size: 16px;
        }

        /* Application Section Styling (Highlighted Action) */
        .application-section {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 30px;
            margin: 40px 0;
            border-radius: 4px;
        }
        .application-section h3 {
            color: #2B3388;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .application-section a { color: #2B3388; }
        .application-section a:hover { color: #2B3388; }

        /* TC Registration Form download */
        .tc-form-box {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            background: rgba(43, 51, 136, 0.04);
            padding: 26px 28px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .tc-form-text { flex: 1 1 320px; }
        .tc-form-text h3 { color: #2B3388; font-weight: 700; font-size: 1.2rem; margin: 0 0 6px; }
        .tc-form-text p { color: #2B3388; margin: 0; line-height: 1.6; }
        .tc-form-btn {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #2B3388;
            color: #fff;
            border: 1px solid #2B3388;
            border-radius: 4px;
            padding: 12px 22px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color .15s ease, color .15s ease;
        }
        .tc-form-btn:hover { background: #fff; color: #2B3388; }

        @media (max-width: 767.98px) {
            .intro-box { padding: 20px; }
            .tc-benefit-card { padding: 20px; min-height: 0; margin-bottom: 20px; }
            .tc-benefit-card h4 { padding-left: 50px; font-size: 1.05rem; }
            .icon-box { top: 20px; left: 16px; }
            .application-section { padding: 20px; }
            .breadcrumb-content .title { font-size: 1.5rem; }
            .tc-form-box { padding: 20px; }
            .tc-form-btn { width: 100%; justify-content: center; }
        }
    </style>
</head>

<body>
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('tcp', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['tcp_hero_title']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['tcp_hero_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="intro-box">
                    <h3><i class="fas fa-info-circle me-2"></i><?= pc_h($pc['tcp_intro_title']) ?></h3>
                    <?= pc_paragraphs_html($pc['tcp_intro_body']) ?>
                </div>

                <h2 class="text-center mt-5" style="color: #2B3388; font-weight: 700;"><?= pc_h($pc['tcp_benefits_title']) ?></h2>
                <div class="section-divider mb-4"></div>

                <div class="row my-5">
                    <div class="col-lg-6 col-md-12">
                        <div class="tc-benefit-card">
                            <div class="icon-box"><i class="fas fa-globe-africa"></i></div>
                            <h4><?= pc_h($pc['tcp_benefit_1_title']) ?></h4>
                            <p><?= pc_h($pc['tcp_benefit_1_body']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="tc-benefit-card">
                            <div class="icon-box"><i class="fas fa-cogs"></i></div>
                            <h4><?= pc_h($pc['tcp_benefit_2_title']) ?></h4>
                            <p><?= pc_h($pc['tcp_benefit_2_body']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="tc-benefit-card">
                            <div class="icon-box"><i class="fas fa-handshake"></i></div>
                            <h4><?= pc_h($pc['tcp_benefit_3_title']) ?></h4>
                            <p><?= pc_h($pc['tcp_benefit_3_body']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="tc-benefit-card">
                            <div class="icon-box"><i class="fas fa-balance-scale"></i></div>
                            <h4><?= pc_h($pc['tcp_benefit_4_title']) ?></h4>
                            <p><?= pc_h($pc['tcp_benefit_4_body']) ?></p>
                        </div>
                    </div>
                </div>

                <div class="application-section">
                    <h3><i class="fas fa-user-plus me-2"></i><?= pc_h($pc['tcp_apply_title']) ?></h3>
                    <?= pc_paragraphs_html($pc['tcp_apply_body']) ?>
                    <p><strong><?= pc_h($pc['tcp_apply_eligibility']) ?></strong></p>
                    <a href="<?= pc_h($pc['tcp_apply_button_url']) ?>" class="btn btn-tc mt-2" target="_blank"><?= pc_h($pc['tcp_apply_button_text']) ?></a>
                    <p class="mt-3"><?= pc_h($pc['tcp_apply_contact_label']) ?> <a href="mailto:<?= pc_h($pc['tcp_apply_contact_email']) ?>"><?= pc_h($pc['tcp_apply_contact_email']) ?></a>.</p>
                </div>

                <!-- Technical Committee Registration Form (downloadable) -->
                <div class="tc-form-box">
                    <div class="tc-form-text">
                        <h3><i class="fas fa-file-download me-2"></i>Join a Technical Committee</h3>
                        <p>Download the Technical Committee Registration Form, complete it, and return it to us to apply to serve on a technical committee.</p>
                    </div>
                    <a href="assets/forms/TEC_SDU_FO_004_Technical_Committee_Registration_Form.doc" class="tc-form-btn" target="_blank" rel="noopener">
                        <i class="fas fa-file-word" aria-hidden="true"></i> TC Registration Form (DOC)
                    </a>
                </div>

                <div class="text-center my-5">
                    <a href="<?= pc_h($pc['tcp_cta_btn_1_url']) ?>" class="btn-cta"><?= pc_h($pc['tcp_cta_btn_1_text']) ?></a>
                    <a href="<?= pc_h($pc['tcp_cta_btn_2_url']) ?>" class="btn-cta"><?= pc_h($pc['tcp_cta_btn_2_text']) ?></a>
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

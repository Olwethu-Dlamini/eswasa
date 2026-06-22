<?php
require_once __DIR__ . '/includes/env.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once __DIR__ . '/includes/breadcrumb_helper.php';

// All editable keys for the Management Systems page (ms_ prefix).
$ms_keys_defaults = [
    // Breadcrumb
    'ms_breadcrumb_title' => 'Management Systems Certification',
    'ms_crumb_home'       => 'Home',
    'ms_crumb_section'    => 'Certification',
    'ms_crumb_current'    => 'Management Systems',

    // Introduction
    'ms_intro_title' => 'Management Systems Certification',
    'ms_intro_body'  => "Our certification services enable you to demonstrate that your products, processes, systems or services conform to national and international standards.\n\nBy gaining recognition from an international certification body relevant to your industry, you ensure your compliance with regulatory bodies and nurture a culture of continuous improvement.\n\nProve your commitment to quality through ESWASA's certification.",

    // Certification Schemes Offered
    'ms_schemes_title' => 'Certification Schemes Offered',

    'ms_scheme_iso9001_img'  => 'iso9001.png',
    'ms_scheme_iso9001_alt'  => 'SZNS ISO 9001',
    'ms_scheme_iso9001_code' => 'SZNS ISO 9001:2015',
    'ms_scheme_iso9001_name' => 'Quality Management Systems - Requirements',

    'ms_scheme_iso14001_img'  => 'iso14001.png',
    'ms_scheme_iso14001_alt'  => 'SZNS ISO 14001',
    'ms_scheme_iso14001_code' => 'SZNS ISO 14001:2015',
    'ms_scheme_iso14001_name' => 'Environmental Management Systems - Requirements with Guidance for Use',

    'ms_scheme_iso22000_img'  => 'iso22000.png',
    'ms_scheme_iso22000_alt'  => 'SZNS ISO 22000',
    'ms_scheme_iso22000_code' => 'SZNS ISO 22000:2018',
    'ms_scheme_iso22000_name' => 'Food Safety Management Systems - Requirements for any organization in the food chain',

    'ms_scheme_iso45001_img'  => 'iso45001.png',
    'ms_scheme_iso45001_alt'  => 'SZNS ISO 45001',
    'ms_scheme_iso45001_code' => 'SZNS ISO 45001:2018',
    'ms_scheme_iso45001_name' => 'Occupational Health and Safety Management Systems - Requirements with guidance for use',

    'ms_scheme_haccp_img'  => 'haccp.png',
    'ms_scheme_haccp_alt'  => 'HACCP',
    'ms_scheme_haccp_code' => 'SZNS SANS 10330:2020',
    'ms_scheme_haccp_name' => 'Hazard Analysis and Critical Control Point (HACCP)',

    // Accreditation card
    'ms_accred_title'   => 'Accreditation',
    'ms_accred_body'    => "Eswatini Standards Authority Management Systems Certification Services is accredited by the Southern African Development Community Accreditation Service (SADCAS).\n\nScopes: Quality Management Systems to ISO/IEC 17021-1:2015 and ISO/IEC 17021-3:2017 (Certification to ISO 9001:2015), IAF Codes 3, 12, 13 and 38",
    'ms_accred_img'     => 'admin/uploads/image12.png',
    'ms_accred_img_alt' => 'SADCAS Accreditation',

    // Portfolio card
    'ms_portfolio_title' => "ESWASA's Certifications Portfolio",
    'ms_portfolio_1_code' => 'SZNS ISO 9001',
    'ms_portfolio_1_name' => 'Quality Management Systems',
    'ms_portfolio_2_code' => 'SZNS ISO 14001',
    'ms_portfolio_2_name' => 'Environmental Management Systems',
    'ms_portfolio_3_code' => 'SZNS ISO 22000',
    'ms_portfolio_3_name' => 'Food Safety Management Systems',
    'ms_portfolio_4_code' => 'SZNS ISO 45001',
    'ms_portfolio_4_name' => 'Occupational Health & Safety Management Systems',
    'ms_portfolio_5_code' => 'SZNS SANS 10330',
    'ms_portfolio_5_name' => 'Hazard Analysis and Critical Control Points',
    'ms_portfolio_footnote' => 'More certifications can be added to the portfolio informed by interest indicated by clients.',

    // Certified Organisations
    'ms_certified_title'  => 'Some of the Certified Organisations',
    'ms_certified_footer' => 'For information on suspended, withdrawn or reduced-scope certifications, see the Certification Status Register.',

    // Certification Documents
    'ms_docs_title' => 'Certification Documents',

    // Why Certify
    'ms_why_title'    => 'Why Certify with ESWASA?',
    'ms_why_subtitle' => 'We provide reliable, efficient and results-driven certification services.',
    'ms_why_img'      => 'whycertify.webp',
    'ms_why_img_alt'  => 'Why Certify with ESWASA - Demonstrate Competence, Prompt Support, Competitive Price, Integrated Approach, Committed, Local Expertise Global Standards',

    // Certification Process
    'ms_process_title' => 'How Certification Works',
    'ms_step_1_title' => 'Step 1', 'ms_step_1_body' => 'Initial Enquiry',
    'ms_step_2_title' => 'Step 2', 'ms_step_2_body' => 'Promotional Visit & Application',
    'ms_step_3_title' => 'Step 3', 'ms_step_3_body' => 'Quote Provided, Contract & Payment Commitment',
    'ms_step_4_title' => 'Step 4', 'ms_step_4_body' => 'Stage 1 Audit — Documentation & Site Readiness',
    'ms_step_5_title' => 'Step 5', 'ms_step_5_body' => 'Stage 2 Audit — Implementation Effectiveness',
    'ms_step_decision_title' => 'Certification', 'ms_step_decision_body' => 'Decision',
    'ms_step_6_title' => 'Step 6', 'ms_step_6_body' => 'Issue of Certificate',
    'ms_step_7_title' => 'Step 7', 'ms_step_7_body' => '2 Surveillance Audits',
    'ms_step_8_title' => 'Step 8', 'ms_step_8_body' => 'Recertification Audit',

    // Benefits
    'ms_benefits_title' => 'Benefits of Certification',
    'ms_benefit_1'  => 'Improvement in Reputation and Credibility',
    'ms_benefit_2'  => 'Improvement in Customer Satisfaction',
    'ms_benefit_3'  => 'Improved Business Processes and Efficiency',
    'ms_benefit_4'  => 'Opens New Markets and Business Opportunities',
    'ms_benefit_5'  => 'Compliance with Regulations and Managing Risks',
    'ms_benefit_6'  => 'Employee Engagement through Accountability',
    'ms_benefit_7'  => 'Cost Savings After Waste Reduction',
    'ms_benefit_8'  => 'Greater Supplier Relationships',
    'ms_benefit_9'  => 'Framework for Continual Improvement',
    'ms_benefit_10' => 'Competitive Advantage Over Non-Certified Businesses',

    // CTA
    'ms_cta_title'     => 'Begin Your Certification Journey',
    'ms_cta_subtitle'  => 'Submit an application or request a preliminary consultation with our certification team.',
    'ms_cta_btn1_text' => 'Request Quote',
    'ms_cta_btn1_url'  => 'qoute_certification.php',
    'ms_cta_btn2_text' => 'Contact Us Now',
    'ms_cta_btn2_url'  => 'contact.php',
    'ms_cta_btn3_text' => 'Attend Implementation Training',
    'ms_cta_btn3_url'  => 'training-about.php',
];

$pc = pc_get_many($conn, array_keys($ms_keys_defaults), $ms_keys_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Management Systems Certification - ESWASA</title>
    <meta name="description" content="Accredited certification of ISO and food safety management systems by the Eswatini Standards Authority (ESWASA). SANAS-accredited, SADC-recognised.">
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
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen">
    <link rel="stylesheet" type="text/css" href="assets/css/extralayers.css" media="screen">
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

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }

        .cert-section {
            padding: 60px 0;
        }
        .section-title {
            color: #2B3388;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }
        .cert-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.10);
        }
        .cert-card h3 {
            color: #2B3388;
            margin-bottom: 15px;
        }
        .btn-cert {
            background: #2B3388;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            font-weight: 600;
        }
        .btn-cert:hover {
            background: rgba(43, 51, 136, 0.85);
            color: white;
        }
        .scheme-card {
            background: white;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 8px;
            padding: 25px 15px;
            text-align: center;
            height: 100%;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.10);
            transition: all 0.2s ease;
        }
        .scheme-card:hover {
            border-color: #2B3388;
        }
        .scheme-card img {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .certified-wrap {
            background: #fff;
            padding: 0;
            overflow: hidden;
        }
        .certified-wrap .cw-header {
            text-align: center;
            padding: 30px 20px 15px;
        }
        .certified-wrap .cw-header h3 {
            font-size: 1.3rem;
            letter-spacing: 2px;
            color: #2B3388;
            margin-bottom: 10px;
        }
        .certified-wrap .cw-header .cw-divider {
            width: 60px;
            height: 3px;
            background: #2B3388;
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
        }
        .certified-wrap .cw-header .cw-divider::after {
            content: '';
            width: 10px;
            height: 10px;
            border: 2px solid #2B3388;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
        }
        .certified-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }
        .certified-table thead th {
            background: #2B3388;
            color: #fff;
            font-weight: 600;
            padding: 13px 18px;
            border: none;
        }
        .certified-table thead th:nth-child(1) { width: 28%; }
        .certified-table thead th:nth-child(2) { width: 30%; text-align: center; }
        .certified-table thead th:nth-child(3) { width: 42%; }
        .certified-table tbody tr:nth-child(odd) {
            background: #fff;
        }
        .certified-table tbody tr:nth-child(even) {
            background: rgba(43, 51, 136, 0.04);
        }
        .certified-table tbody td {
            padding: 12px 18px;
            border: none;
            color: #2B3388;
            border-bottom: 1px solid rgba(43, 51, 136, 0.12);
        }
        .certified-table tbody td:nth-child(2) {
            text-align: center;
        }
        /* Certified-clients logo grid */
        .client-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            padding: 10px 24px 30px;
        }
        .client-tile {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            min-height: 130px;
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-align: center;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .client-tile:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .client-tile img {
            max-width: 100%;
            max-height: 70px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .client-tile .client-wordmark {
            color: #2B3388;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            line-height: 1.25;
        }
        .client-tile .client-standard {
            color: #2B3388;
            font-size: 0.78rem;
            letter-spacing: 0.3px;
        }
        @media (max-width: 575.98px) {
            .client-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 10px 14px 24px;
            }
            .client-tile { min-height: 110px; padding: 14px 10px; }
            .client-tile .client-wordmark { font-size: 0.88rem; }
            .client-tile .client-standard { font-size: 0.72rem; }
        }
        .doc-card {
            display: block;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 6px;
            padding: 25px 15px 20px;
            text-align: center;
            height: 100%;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .doc-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #2B3388;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .doc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.12);
        }
        .doc-card:hover::after {
            transform: scaleX(1);
        }
        .doc-card:hover .doc-icon {
            border-radius: 8px 8px 0 8px;
            transform: scale(1.1) rotate(-5deg);
        }
        .doc-card:hover .doc-icon i {
            animation: bounce-down 0.5s ease;
        }
        @keyframes bounce-down {
            0%, 100% { transform: translateY(0); }
            40% { transform: translateY(5px); }
            60% { transform: translateY(-2px); }
        }
        .doc-icon {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }
        .doc-icon svg {
            width: 28px;
            height: 28px;
        }
        .doc-card p {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 0;
        }
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0 auto;
            max-width: 920px;
            column-count: 2;
            column-gap: 48px;
        }
        .benefits-list li {
            padding: 10px 0 10px 22px;
            position: relative;
            color: #2B3388;
            font-size: 15px;
            line-height: 1.5;
            break-inside: avoid;
            border-bottom: 1px solid rgba(43, 51, 136, 0.10);
        }
        .benefits-list li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 18px;
            width: 8px;
            height: 2px;
            background: #2B3388;
        }
        @media (max-width: 767.98px) {
            .benefits-list {
                column-count: 1;
                max-width: 560px;
            }
            .benefits-list li { font-size: 0.95rem; padding: 9px 0 9px 20px; }
            .benefits-list li::before { top: 17px; }
        }
        .process-step {
            padding: 20px;
            text-align: center;
        }
        .step-number {
            background: #2B3388;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: 600;
        }
        .standards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        .standard-block {
            background: white;
            padding: 25px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .standard-block:hover {
            box-shadow: 0 4px 12px rgba(46, 49, 145, 0.08);
            border-color: #2B3388;
        }
        .standard-code {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .standard-name {
            font-weight: 600;
            margin-bottom: 12px;
            color: #2B3388;
        }

        @media (max-width: 768px) {
            .standards-grid {
                grid-template-columns: 1fr;
            }
        }


        .cert-process-section .process-row {
    margin-bottom: 40px;
}

.process-circle {
    background: #2B3388;
    color: #fff;
    width: 165px;
    height: 165px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 15px;
    font-weight: 700;
    font-size: 12px;
    line-height: 1.3;
    margin: 10px;
}

.process-circle span {
    font-weight: 400;
    font-size: 15px;
    margin-top: 4px;
}

.process-circle.highlight {
    background: #fff;
    color: #2B3388;
    border: 3px solid #2B3388;
    font-weight: 700;
    font-size: 18px;
}

.process-arrow {
    font-size: 40px;
    font-weight: 700;
    margin: 0 10px;
    color: #2B3388;
}

.process-divider {
    width: 80%;
    height: 2px;
    background: rgba(43, 51, 136, 0.18);
    margin: 20px auto;
    border-radius: 0;
}

@media (max-width: 768px) {
    .process-circle {
        width: 140px;
        height: 140px;
    }
    .process-arrow {
        display: none;
    }
}


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

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('managementsystems', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php"><?= pc_h($pc['ms_crumb_home']) ?></a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['ms_crumb_section']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['ms_crumb_current']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['ms_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Introduction -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="cert-card">
                            <h2 class="section-title" style="text-align: center;"><?= pc_h($pc['ms_intro_title']) ?></h2>
                            <div class="section-divider"></div>
                            <div class="mt-3"><?= pc_paragraphs_html($pc['ms_intro_body']) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Schemes Offered -->
        <section class="cert-section bg-light">
            <div class="container">
                <h2 class="section-title text-center"><?= pc_h($pc['ms_schemes_title']) ?></h2>
                <div class="section-divider"></div>
                <div class="row g-4 justify-content-center mt-4">
                    <?php foreach (['iso9001','iso14001','iso22000','iso45001','haccp'] as $sch): ?>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="<?= pc_h(pc_image_src($pc['ms_scheme_'.$sch.'_img'], $ms_keys_defaults['ms_scheme_'.$sch.'_img'])) ?>" alt="<?= pc_h($pc['ms_scheme_'.$sch.'_alt']) ?>">
                            <div class="standard-code"><?= pc_h($pc['ms_scheme_'.$sch.'_code']) ?></div>
                            <div class="standard-name"><?= pc_h($pc['ms_scheme_'.$sch.'_name']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Accreditation & Portfolio side by side -->
        <section class="cert-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3><?= pc_h($pc['ms_accred_title']) ?></h3>
                            <?= pc_paragraphs_html($pc['ms_accred_body']) ?>
                            <div class="mt-3 d-flex flex-wrap align-items-center gap-3">
                                <img src="<?= pc_h(pc_image_src($pc['ms_accred_img'], 'admin/uploads/image12.png')) ?>" alt="<?= pc_h($pc['ms_accred_img_alt']) ?>" class="img-fluid" style="max-height: 90px; width: auto;">

                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3><?= pc_h($pc['ms_portfolio_title']) ?></h3>
                            <table class="table table-sm mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <tr><td class="fw-bold" style="color:#2B3388;"><?= pc_h($pc['ms_portfolio_'.$i.'_code']) ?></td><td><?= pc_h($pc['ms_portfolio_'.$i.'_name']) ?></td></tr>
                                <?php endfor; ?>
                            </table>
                            <p class="text-muted" style="font-size:0.9rem;"><?= pc_h($pc['ms_portfolio_footnote']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ESWASA Certified Organisations -->
        <section class="cert-section" style="background: rgba(43, 51, 136, 0.04);">
            <div class="container">
                <div class="certified-wrap">
                    <div class="cw-header">
                        <h3><?= pc_h($pc['ms_certified_title']) ?></h3>
                        <div class="cw-divider"></div>
                    </div>
                    <?php
                    // Loaded from the certified_organisations table; managed via
                    // admin → Management Systems → Certified Organisations tab.
                    $clients = [];
                    $cres = $conn->query('SELECT name, standard, logo_path FROM certified_organisations WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
                    if ($cres) {
                        while ($crow = $cres->fetch_assoc()) $clients[] = $crow;
                    }
                    ?>
                    <div class="client-grid">
                        <?php foreach ($clients as $c):
                            $logo = trim((string)($c['logo_path'] ?? ''));
                        ?>
                        <div class="client-tile">
                            <?php if ($logo !== ''): ?>
                                <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($c['name']) ?> logo">
                            <?php else: ?>
                                <div class="client-wordmark"><?= htmlspecialchars($c['name']) ?></div>
                            <?php endif; ?>
                            <div class="client-standard"><?= htmlspecialchars($c['standard']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-center mt-4 mb-0" style="font-size: 0.95rem;">
                        <?= pc_h($pc['ms_certified_footer']) ?>
                        <a href="certification-status.php" style="color:#2B3388; font-weight:600; text-decoration:underline;">Certification Status Register</a>.
                    </p>
                </div>
            </div>
        </section>

        <!-- Certification Documents -->
        <section class="cert-section">
            <div class="container">
                <div class="cw-header" style="padding: 0 0 30px;">
                    <h3><?= pc_h($pc['ms_docs_title']) ?></h3>
                    <div class="cw-divider"></div>
                </div>
                <?php
                // Loaded from certification_documents table; managed via
                // admin → Management Systems → Certification Documents tab.
                $docs = [];
                $dres = $conn->query('SELECT title, file_path FROM certification_documents WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');
                if ($dres) { while ($drow = $dres->fetch_assoc()) $docs[] = $drow; }
                ?>
                <div class="row g-4">
                    <?php foreach ($docs as $doc): ?>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="<?= pc_h($doc['file_path']) ?>" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #2B3388;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p><?= pc_h($doc['title']) ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

<section class="cert-section py-5" style="background: rgba(43, 51, 136, 0.04);">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title"><?= pc_h($pc['ms_why_title']) ?></h2>
            <div class="section-divider"></div>
            <p class="section-subtitle mt-3"><?= pc_h($pc['ms_why_subtitle']) ?></p>
        </div>
        <div class="text-center">
            <img src="<?= pc_h(pc_image_src($pc['ms_why_img'], 'whycertify.webp')) ?>" alt="<?= pc_h($pc['ms_why_img_alt']) ?>" class="img-fluid" style="max-width: 900px;">
        </div>
    </div>
</section>

<!-- Certification Process -->
<section class="cert-process-section py-5 bg-light">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title"><?= pc_h($pc['ms_process_title']) ?></h2>
            <div class="section-divider"></div>
        </div>

        <!-- ROW 1 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle"><?= pc_h($pc['ms_step_1_title']) ?><br><span><?= pc_h($pc['ms_step_1_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle"><?= pc_h($pc['ms_step_2_title']) ?><br><span><?= pc_h($pc['ms_step_2_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle"><?= pc_h($pc['ms_step_3_title']) ?><br><span><?= pc_h($pc['ms_step_3_body']) ?></span></div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 2 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle"><?= pc_h($pc['ms_step_4_title']) ?><br><span><?= pc_h($pc['ms_step_4_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle"><?= pc_h($pc['ms_step_5_title']) ?><br><span><?= pc_h($pc['ms_step_5_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle highlight"><?= pc_h($pc['ms_step_decision_title']) ?><br><?= pc_h($pc['ms_step_decision_body']) ?></div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 3 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle"><?= pc_h($pc['ms_step_6_title']) ?><br><span><?= pc_h($pc['ms_step_6_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle"><?= pc_h($pc['ms_step_7_title']) ?><br><span><?= pc_h($pc['ms_step_7_body']) ?></span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle"><?= pc_h($pc['ms_step_8_title']) ?><br><span><?= pc_h($pc['ms_step_8_body']) ?></span></div>
        </div>

    </div>
</section>

<!-- Benefits of Certification -->
<section class="cert-section py-5">
    <div class="container">
        <div class="main_title centered upper mb-4 text-center">
            <h2 class="display-6 fw-bold"><?= pc_h($pc['ms_benefits_title']) ?></h2>
            <div class="section-divider"></div>
        </div>
        <ul class="benefits-list">
            <?php for ($i = 1; $i <= 10; $i++): ?>
            <li><?= pc_h($pc['ms_benefit_'.$i]) ?></li>
            <?php endfor; ?>
        </ul>
    </div>
</section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title"><?= pc_h($pc['ms_cta_title']) ?></h2>
                        <p class="cta-subtitle"><?= pc_h($pc['ms_cta_subtitle']) ?></p>
                        <a href="<?= pc_h($pc['ms_cta_btn1_url']) ?>" class="btn-cta"><?= pc_h($pc['ms_cta_btn1_text']) ?></a>
                        <a href="<?= pc_h($pc['ms_cta_btn2_url']) ?>" class="btn-cta"><?= pc_h($pc['ms_cta_btn2_text']) ?></a>
                        <a href="<?= pc_h($pc['ms_cta_btn3_url']) ?>" class="btn-cta"><?= pc_h($pc['ms_cta_btn3_text']) ?></a>
                    </div>
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

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';

$cal_keys = [
    // Breadcrumb
    'cal_breadcrumb_title',

    // Section 1 — About Us
    'cal_about_title',
    'cal_about_body',

    // Section 2 — Products & Services
    'cal_services_title',
    'cal_services_1_label', 'cal_services_1_desc',
    'cal_services_2_label', 'cal_services_2_desc',
    'cal_services_3_label', 'cal_services_3_desc',
    'cal_services_4_label', 'cal_services_4_desc',
    'cal_services_5_label', 'cal_services_5_desc',

    // Section 3 — What is Calibration
    'cal_whatis_title',
    'cal_whatis_body',

    // Section 4 — Purpose
    'cal_purpose_title',
    'cal_purpose_intro',
    'cal_purpose_1', 'cal_purpose_2', 'cal_purpose_3',
    'cal_purpose_4', 'cal_purpose_5', 'cal_purpose_6',

    // Section 5 — Brands
    'cal_brands_title',
    'cal_brand_1_image',  'cal_brand_1_alt',
    'cal_brand_2_image',  'cal_brand_2_alt',
    'cal_brand_3_image',  'cal_brand_3_alt',
    'cal_brand_4_image',  'cal_brand_4_alt',
    'cal_brand_5_image',  'cal_brand_5_alt',
    'cal_brand_6_image',  'cal_brand_6_alt',
    'cal_brand_7_image',  'cal_brand_7_alt',
    'cal_brand_8_image',  'cal_brand_8_alt',
    'cal_brand_9_image',  'cal_brand_9_alt',
    'cal_brand_10_image', 'cal_brand_10_alt',
    'cal_brand_11_image', 'cal_brand_11_alt',
    'cal_brand_12_image', 'cal_brand_12_alt',

    // Section 6 — FAQ
    'cal_faq_title',
    'cal_faq_1_question', 'cal_faq_1_intro',
    'cal_faq_1_1', 'cal_faq_1_2', 'cal_faq_1_3', 'cal_faq_1_4', 'cal_faq_1_5', 'cal_faq_1_6',
    'cal_faq_2_question', 'cal_faq_2_intro',
    'cal_faq_2_1', 'cal_faq_2_2', 'cal_faq_2_3', 'cal_faq_2_4', 'cal_faq_2_5', 'cal_faq_2_6', 'cal_faq_2_7',

    // CTA
    'cal_cta_title',
    'cal_cta_subtitle',
    'cal_cta_btn1_label', 'cal_cta_btn1_url',
    'cal_cta_btn2_label', 'cal_cta_btn2_url',
];

$cal_defaults = [
    'cal_breadcrumb_title' => 'Scales & Metrology Services',

    'cal_about_title' => 'About Us',
    'cal_about_body'  => "We understand the importance of accurate and reliable measurements for your business. That is why we strive to provide the best calibration services available.\n\nOur team of highly skilled technicians provide you with exceptional scale sales, service, repairs, installations, and calibration of all weighing equipment, weighbridges, and metrology equipment.\n\nWe utilise only the finest industry-standard equipment and procedures to ensure that all your measuring and testing instruments consistently function at their best, remaining accurate and reliable.",

    'cal_services_title'   => 'Our Products and Services Include:',
    'cal_services_1_label' => 'Scale Sales',
    'cal_services_1_desc'  => 'Supplying high-quality scales, hoppers, and weighbridges for any application.',
    'cal_services_2_label' => 'Servicing and Repairs',
    'cal_services_2_desc'  => 'Expert maintenance and repair for scales, hoppers, and weighbridges to extend their lifespan and ensure performance.',
    'cal_services_3_label' => 'In-house and On-site Calibration',
    'cal_services_3_desc'  => 'Comprehensive calibration services for all types of weighing, temperature, and pressure instruments, performed at our Matsapha laboratory or at your premises.',
    'cal_services_4_label' => 'Preventative Maintenance Programmes',
    'cal_services_4_desc'  => 'Scheduled maintenance to prevent failures and ensure continuous accuracy.',
    'cal_services_5_label' => 'Installation',
    'cal_services_5_desc'  => 'Professional installation of scales, hoppers, and weighbridges to guarantee optimal setup and performance from day one.',

    'cal_whatis_title' => 'What is Calibration?',
    'cal_whatis_body'  => "Calibration is the process of checking, by comparison with a standard, the accuracy of measuring instruments of any type, such as a pressure gauge or an industrial thermometer. It may also include adjustments to the instrument to match the standard.\n\nCalibration of your temperature and pressure instruments has two objectives. It checks the accuracy of the instrument, and it determines the traceability of the measurement. In practice, calibration may also include repair of the device if it is out of calibration. A report is provided by the calibration technician, which shows the error in measurements with the measuring device before and after the calibration.",

    'cal_purpose_title' => 'Purpose of Calibration',
    'cal_purpose_intro' => 'Correct and reliable measurements are prerequisites for all high-quality industrial production.',
    'cal_purpose_1'     => 'To ensure Measurement Accuracy',
    'cal_purpose_2'     => 'To comply with Industrial Standards',
    'cal_purpose_3'     => 'To ensure Equipment Traceability',
    'cal_purpose_4'     => 'To improve Instrument Reliability',
    'cal_purpose_5'     => 'To enhance Product Quality and Safety',
    'cal_purpose_6'     => 'To reduce Downtime',

    'cal_brands_title'    => 'We Also Supply and Service the Following Brands:',
    'cal_brand_1_image'   => 'assets/img/brand/lmi.PNG',
    'cal_brand_1_alt'     => 'LMI',
    'cal_brand_2_image'   => 'assets/img/brand/mass.PNG',
    'cal_brand_2_alt'     => 'Massamatic',
    'cal_brand_3_image'   => 'assets/img/brand/ishida.PNG',
    'cal_brand_3_alt'     => 'Ishida',
    'cal_brand_4_image'   => 'assets/img/brand/zemic.PNG',
    'cal_brand_4_alt'     => 'Zemic',
    'cal_brand_5_image'   => 'assets/img/brand/avery.PNG',
    'cal_brand_5_alt'     => 'Avery Weigh-Tronix',
    'cal_brand_6_image'   => 'assets/img/brand/trek.PNG',
    'cal_brand_6_alt'     => 'Trek',
    'cal_brand_7_image'   => 'assets/img/brand/syslec.PNG',
    'cal_brand_7_alt'     => 'Systec',
    'cal_brand_8_image'   => 'assets/img/brand/shekel.PNG',
    'cal_brand_8_alt'     => 'Shekel',
    'cal_brand_9_image'   => 'assets/img/brand/laumus.PNG',
    'cal_brand_9_alt'     => 'Laumas',
    'cal_brand_10_image'  => 'assets/img/brand/adam.PNG',
    'cal_brand_10_alt'    => 'Adam Equipment',
    'cal_brand_11_image'  => 'assets/img/brand/mattler.PNG',
    'cal_brand_11_alt'    => 'Mettler Toledo',
    'cal_brand_12_image'  => 'assets/img/brand/digi.PNG',
    'cal_brand_12_alt'    => 'Digi',

    'cal_faq_title'       => 'Calibration FAQ',
    'cal_faq_1_question'  => '1. What are the benefits of ESWASA calibration services?',
    'cal_faq_1_intro'     => 'Calibration services help organisations to:',
    'cal_faq_1_1'         => 'Improve measurement accuracy and reliability.',
    'cal_faq_1_2'         => 'Ensure compliance with applicable standards and regulatory requirements.',
    'cal_faq_1_3'         => 'Reduce product rejection and process errors.',
    'cal_faq_1_4'         => 'Enhance customer confidence and product quality.',
    'cal_faq_1_5'         => 'Support traceability to recognised measurement standards.',
    'cal_faq_1_6'         => 'Improve operational efficiency and safety.',
    'cal_faq_2_question'  => '2. Who can use ESWASA calibration services?',
    'cal_faq_2_intro'     => 'Calibration services are available to:',
    'cal_faq_2_1'         => 'Industry and manufacturers.',
    'cal_faq_2_2'         => 'Laboratories.',
    'cal_faq_2_3'         => 'Retailers and wholesalers.',
    'cal_faq_2_4'         => 'Government institutions.',
    'cal_faq_2_5'         => 'Energy and petroleum sectors.',
    'cal_faq_2_6'         => 'SMEs and commercial enterprises.',
    'cal_faq_2_7'         => 'Any organisation requiring reliable and traceable measurements.',

    'cal_cta_title'       => 'Get Calibrations',
    'cal_cta_subtitle'    => 'Submit an application or request a preliminary consultation with our calibration team.',
    'cal_cta_btn1_label'  => 'Request Calibration Quote',
    'cal_cta_btn1_url'    => 'qoute_calibration.php',
    'cal_cta_btn2_label'  => 'Contact Metrology Unit',
    'cal_cta_btn2_url'    => 'contactcalibration.php',
];

$pc = pc_get_many($conn, $cal_keys, $cal_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Scales and Metrology Services - ESWASA</title>
    <meta name="description" content="ESWASA's Legal Metrology services: verification and calibration of weighing and measuring instruments in Eswatini.">
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

        .btn-service {
            background-color: #2B3388;
            color: white;
            border-color: #2B3388;
            margin: 5px;
            font-weight: 600;
            padding: 10px 22px;
        }
        .btn-service:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: white;
        }
        /* Enlarged action buttons */
        .btn-metrology-action {
            font-size: 1.25rem !important;
            padding: 18px 50px !important;
            margin: 0 15px 15px !important;
            font-weight: 700 !important;
            display: inline-block;
        }
        /* Clean sections — borders over shadows (DIN/BIS restrained aesthetic) */
        .highlighted-section {
            background-color: rgba(43, 51, 136, 0.04);
            padding: 25px;
            margin: 30px 0;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
        }
        .highlighted-section h2 {
            color: #2B3388;
            margin-top: 0;
            font-weight: 700;
            font-size: 32px;
            line-height: 1.2;
        }
        .service-process-steps {
            list-style-type: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .service-process-steps li {
            counter-increment: step-counter;
            margin-bottom: 22px;
            padding-left: 45px;
            position: relative;
        }
        .service-process-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 2px;
            background-color: #2B3388;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .metrology-category {
            text-align: center;
            padding: 20px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            background-color: rgba(43, 51, 136, 0.04);
            margin-bottom: 20px;
        }
        .metrology-category h4 {
            color: #2B3388;
            font-weight: 600;
        }
        .metrology-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .metrology-info-table th,
        .metrology-info-table td {
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 14px;
            text-align: left;
        }
        .metrology-info-table th {
            background-color: #2B3388;
            color: white;
            font-weight: 600;
        }
        .metrology-info-table tr:nth-child(even) {
            background-color: rgba(43, 51, 136, 0.04);
        }

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            .service-process-steps li {
                padding-left: 40px;
            }
            .service-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
            }
            .btn-metrology-action {
                font-size: 1.15rem !important;
                padding: 16px 40px !important;
                display: block;
                margin: 10px auto !important;
                width: auto;
            }
        }


        .cert-section {
    padding: 60px 0;
}

.section-title {
    color: #2B3388;
    margin-bottom: 30px;
    font-weight: 700;
    text-align: center;
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
    transition: background 0.3s ease;
}

.btn-cert:hover {
    background: rgba(43, 51, 136, 0.85);
    color: white;
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
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('calibration', 'assets/img/bg/calibrationbg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Services</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Scales & Metrology</span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['cal_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- 1. Introduction & Commitment -->
                <div class="highlighted-section">
                    <h2 style="text-align: center;"><?= pc_h($pc['cal_about_title']) ?></h2>
                    <div class="section-divider"></div>
                    <div class="mt-3">
                        <?= pc_paragraphs_html($pc['cal_about_body']) ?>
                    </div>
                </div>

                <!-- 2. Our Products and Services -->
                <div class="highlighted-section">
                    <h2 style="text-align: center;"><?= pc_h($pc['cal_services_title']) ?></h2>
                    <div class="section-divider mb-3"></div>
                    <ul>
                        <li><strong><?= pc_h($pc['cal_services_1_label']) ?></strong>: <?= pc_h($pc['cal_services_1_desc']) ?></li>
                        <li><strong><?= pc_h($pc['cal_services_2_label']) ?></strong>: <?= pc_h($pc['cal_services_2_desc']) ?></li>
                        <li><strong><?= pc_h($pc['cal_services_3_label']) ?></strong>: <?= pc_h($pc['cal_services_3_desc']) ?></li>
                        <li><strong><?= pc_h($pc['cal_services_4_label']) ?></strong>: <?= pc_h($pc['cal_services_4_desc']) ?></li>
                        <li><strong><?= pc_h($pc['cal_services_5_label']) ?></strong>: <?= pc_h($pc['cal_services_5_desc']) ?></li>
                    </ul>
                </div>

                <!-- 3. What is Calibration? -->
                <div class="highlighted-section">
                    <h2 style="text-align: center;"><?= pc_h($pc['cal_whatis_title']) ?></h2>
                    <div class="section-divider"></div>
                    <div class="mt-3">
                        <?= pc_paragraphs_html($pc['cal_whatis_body']) ?>
                    </div>
                </div>

                <!-- 4. Purpose of Calibration -->
                <div class="highlighted-section">
                    <h2 style="text-align: center;"><?= pc_h($pc['cal_purpose_title']) ?></h2>
                    <div class="section-divider"></div>
                    <p class="mt-3">
                        <?= pc_h($pc['cal_purpose_intro']) ?>
                    </p>
                    <ul>
                        <li><?= pc_h($pc['cal_purpose_1']) ?></li>
                        <li><?= pc_h($pc['cal_purpose_2']) ?></li>
                        <li><?= pc_h($pc['cal_purpose_3']) ?></li>
                        <li><?= pc_h($pc['cal_purpose_4']) ?></li>
                        <li><?= pc_h($pc['cal_purpose_5']) ?></li>
                        <li><?= pc_h($pc['cal_purpose_6']) ?></li>
                    </ul>
                </div>

               <!-- 5. Brands We Supply and Service -->
<div class="highlighted-section">
    <h2 style="text-align: center;"><?= pc_h($pc['cal_brands_title']) ?></h2>
    <div class="section-divider mb-4"></div>
    <div class="row g-4 justify-content-center">
        <!-- Row 1 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_1_image'], 'assets/img/brand/lmi.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_1_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_2_image'], 'assets/img/brand/mass.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_2_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_3_image'], 'assets/img/brand/ishida.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_3_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_4_image'], 'assets/img/brand/zemic.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_4_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>

        <!-- Row 2 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_5_image'], 'assets/img/brand/avery.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_5_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_6_image'], 'assets/img/brand/trek.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_6_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_7_image'], 'assets/img/brand/syslec.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_7_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_8_image'], 'assets/img/brand/shekel.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_8_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>

        <!-- Row 3 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_9_image'], 'assets/img/brand/laumus.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_9_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_10_image'], 'assets/img/brand/adam.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_10_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_11_image'], 'assets/img/brand/mattler.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_11_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="<?= pc_h(pc_image_src($pc['cal_brand_12_image'], 'assets/img/brand/digi.PNG')) ?>" alt="<?= pc_h($pc['cal_brand_12_alt']) ?>" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
    </div>
</div>

                <!-- 6. Calibration FAQ -->
                <div class="highlighted-section">
                    <h2 style="text-align: center;"><?= pc_h($pc['cal_faq_title']) ?></h2>
                    <div class="section-divider mb-4"></div>

                    <h4 class="mt-3"><?= pc_h($pc['cal_faq_1_question']) ?></h4>
                    <p><?= pc_h($pc['cal_faq_1_intro']) ?></p>
                    <ul>
                        <li><?= pc_h($pc['cal_faq_1_1']) ?></li>
                        <li><?= pc_h($pc['cal_faq_1_2']) ?></li>
                        <li><?= pc_h($pc['cal_faq_1_3']) ?></li>
                        <li><?= pc_h($pc['cal_faq_1_4']) ?></li>
                        <li><?= pc_h($pc['cal_faq_1_5']) ?></li>
                        <li><?= pc_h($pc['cal_faq_1_6']) ?></li>
                    </ul>

                    <h4 class="mt-4"><?= pc_h($pc['cal_faq_2_question']) ?></h4>
                    <p><?= pc_h($pc['cal_faq_2_intro']) ?></p>
                    <ul>
                        <li><?= pc_h($pc['cal_faq_2_1']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_2']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_3']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_4']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_5']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_6']) ?></li>
                        <li><?= pc_h($pc['cal_faq_2_7']) ?></li>
                    </ul>
                </div>


        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title"><?= pc_h($pc['cal_cta_title']) ?></h2>
                        <p class="cta-subtitle"><?= pc_h($pc['cal_cta_subtitle']) ?></p>
                        <a href="<?= pc_h($pc['cal_cta_btn1_url']) ?>" class="btn-cta"><?= pc_h($pc['cal_cta_btn1_label']) ?></a>
                        <a href="<?= pc_h($pc['cal_cta_btn2_url']) ?>" class="btn-cta"><?= pc_h($pc['cal_cta_btn2_label']) ?></a>
                    </div>
                </div>
            </div>
        </section>


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

<?php
require_once __DIR__ . '/includes/env.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once 'includes/breadcrumb_helper.php';

$ingelo_keys = [
    // Hero / breadcrumb
    'ingelo_hero_title',
    // Intro section
    'ingelo_intro_image',
    'ingelo_intro_image_alt',
    'ingelo_intro_title',
    'ingelo_intro_body',
    // Section 1 - What is the scheme?
    'ingelo_section_what_title',
    'ingelo_section_what_lead',
    'ingelo_section_what_item_1_title',
    'ingelo_section_what_item_1_body',
    'ingelo_section_what_item_2_title',
    'ingelo_section_what_item_2_body',
    // Section 2 - Benefits
    'ingelo_benefits_title',
    'ingelo_benefit_1_title', 'ingelo_benefit_1_body',
    'ingelo_benefit_2_title', 'ingelo_benefit_2_body',
    'ingelo_benefit_3_title', 'ingelo_benefit_3_body',
    'ingelo_benefit_4_title', 'ingelo_benefit_4_body',
    'ingelo_benefit_5_title', 'ingelo_benefit_5_body',
    'ingelo_benefit_6_title', 'ingelo_benefit_6_body',
    'ingelo_benefit_7_title', 'ingelo_benefit_7_body',
    'ingelo_benefit_8_title', 'ingelo_benefit_8_body',
    // Section 3 - Who can apply?
    'ingelo_who_title',
    'ingelo_who_item_1',
    'ingelo_who_item_2',
    'ingelo_who_item_3',
    // Section 4 - Standards
    'ingelo_certified_title',
    // Section 5 - Application
    'ingelo_apply_title',
    'ingelo_apply_lead',
    'ingelo_apply_step_1',
    'ingelo_apply_step_2',
    'ingelo_apply_button_text',
    'ingelo_apply_button_url',
    'ingelo_apply_support_note',
    // CTA
    'ingelo_cta_title',
    'ingelo_cta_subtitle',
    'ingelo_cta_btn_1_text', 'ingelo_cta_btn_1_url',
    'ingelo_cta_btn_2_text', 'ingelo_cta_btn_2_url',
];

$ingelo_defaults = [
    'ingelo_hero_title'                 => 'Ingelo Certification Scheme',
    'ingelo_intro_image'                => 'assets/img/quality/ingelo-certification-black.png',
    'ingelo_intro_image_alt'            => 'Ingelo MSME Product Certification Mark',
    'ingelo_intro_title'                => 'Eswatini Standards Authority (ESWASA) Invites MSMEs to Participate',
    'ingelo_intro_body'                 => "The Eswatini Standards Authority (ESWASA) invites Micro, Small and Medium Enterprises (MSMEs) to apply for participation in the Ingelo Certification Scheme — a programme developed to support local businesses in achieving product and system certification.\n\nThis initiative is designed to empower Emaswati entrepreneurs by providing them with the tools and recognition needed to compete effectively in both local and international markets.",
    'ingelo_section_what_title'         => 'What is the Ingelo Certification Scheme?',
    'ingelo_section_what_lead'          => 'The Ingelo Certification Scheme is a national initiative launched by the Ministry of Commerce, Industry and Trade to support local producers through:',
    'ingelo_section_what_item_1_title'  => 'System and Product Certification',
    'ingelo_section_what_item_1_body'   => 'Supporting cottage producers in achieving certification for both their production systems and end products.',
    'ingelo_section_what_item_2_title'  => 'Market Requirements Compliance',
    'ingelo_section_what_item_2_body'   => 'Assisting producers of products and services in meeting market requirements on quality and safety based on established standards.',
    'ingelo_benefits_title'             => 'Benefits of Participation',
    'ingelo_benefit_1_title'            => 'Improved Product Quality & Safety',
    'ingelo_benefit_1_body'             => 'Enhance your product quality and safety through standardised processes and compliance with national and international standards.',
    'ingelo_benefit_2_title'            => 'Market Access & Growth',
    'ingelo_benefit_2_body'             => 'Gain access to local and regional markets including AfCFTA. Many markets mandate certifications as a condition for entry, reducing trade barriers and opening global opportunities.',
    'ingelo_benefit_3_title'            => 'Recognition Through ESWASA Approved Mark',
    'ingelo_benefit_3_body'             => 'Display the trusted ESWASA Approved mark on your products, signalling compliance and quality to consumers and business partners.',
    'ingelo_benefit_4_title'            => 'Technical Support',
    'ingelo_benefit_4_body'             => 'Receive technical guidance throughout the certification process from ESWASA experts to help you meet all requirements successfully.',
    'ingelo_benefit_5_title'            => 'Customer Trust & Brand Value',
    'ingelo_benefit_5_body'             => 'Certifications provide independent validation, signalling reliability and safety to customers. They strengthen brand reputation and can differentiate SMEs from uncertified competitors.',
    'ingelo_benefit_6_title'            => 'Risk Management & Compliance',
    'ingelo_benefit_6_body'             => 'Certifications help SMEs minimise legal and regulatory risks. They serve as documented proof of compliance in case of disputes, audits or liability claims.',
    'ingelo_benefit_7_title'            => 'Financing & Investment Appeal',
    'ingelo_benefit_7_body'             => 'Investors and lenders increasingly assess intangibles when evaluating SMEs. Certifications add credibility to business operations, boosting investor confidence and enhancing company valuation.',
    'ingelo_benefit_8_title'            => 'Building Long-Term Competitive Advantage',
    'ingelo_benefit_8_body'             => "Unlike machinery or stock, certifications don't depreciate overnight — they compound business credibility over time. When integrated into IP and brand strategy, certifications become part of the SME's unique intangible asset portfolio.",
    'ingelo_who_title'                  => 'Who Can Apply?',
    'ingelo_who_item_1'                 => 'Emaswati (Swazi citizens) — local entrepreneurs and business owners.',
    'ingelo_who_item_2'                 => 'Local MSMEs involved in the production of any products or offering services.',
    'ingelo_who_item_3'                 => 'Producers willing to scale up — those who are willing to increase production to meet export quota requirements by local and regional markets, through compliance with certification requirements.',
    'ingelo_certified_title'            => 'Ingelo Certified Producers',
    'ingelo_apply_title'                => 'How to Apply',
    'ingelo_apply_lead'                 => 'To begin the Ingelo Certification process, please:',
    'ingelo_apply_step_1'               => 'Download and complete the official application form.',
    'ingelo_apply_step_2'               => 'Submit via email to certification@eswasa.co.sz or in person at ESWASA offices, Matsapha.',
    'ingelo_apply_button_text'          => 'Download Application Form (PDF)',
    'ingelo_apply_button_url'           => 'admin/uploads/ingelo_application_form.pdf',
    'ingelo_apply_support_note'         => 'Support available: ESWASA offers free pre-application consultations and gap-analysis workshops for MSMEs. Contact us to schedule.',
    'ingelo_cta_title'                  => 'Ready to Get Ingelo Certified?',
    'ingelo_cta_subtitle'               => 'Request a certification quote or speak to an Ingelo officer to get started.',
    'ingelo_cta_btn_1_text'             => 'Request Certification Quote',
    'ingelo_cta_btn_1_url'              => 'qoute_certification.php',
    'ingelo_cta_btn_2_text'             => 'Speak to an Ingelo Officer',
    'ingelo_cta_btn_2_url'              => 'contact.php',
];

// ── Ingelo certified producers (DB-driven; edited via admin → Ingelo) ────────
// Replaces the former "Available Standards" list. Shares the
// certified_organisations table with the Management Systems and Product
// grids, scoped by scheme.
$ingelo_producers = [];
$ipres = $conn->query("SELECT name, product, standard, logo_path FROM certified_organisations WHERE scheme = 'ingelo' AND is_active = 1 ORDER BY sort_order ASC, id ASC");
if ($ipres) {
    while ($iprow = $ipres->fetch_assoc()) $ingelo_producers[] = $iprow;
}

$pc = pc_get_many($conn, $ingelo_keys, $ingelo_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['ingelo_hero_title']) ?> - ESWASA</title>
    <meta name="description" content="ESWASA's Ingelo Quality Mark: Voluntary certification for locally manufactured products in Eswatini. Promotes quality, supports local industry, and builds consumer trust.">
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

        /* Clean highlight - no icons, no blue border */
        .highlighted-section {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 25px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .highlighted-section h3 {
            color: #2B3388;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .ingelo-application-form {
            background-color: rgba(43, 51, 136, 0.04);
            padding: 25px;
            border-radius: 4px;
            margin-top: 30px;
            border: 1px solid rgba(43, 51, 136, 0.15);
        }
        .ingelo-application-form h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.4rem;
        }

        /* Additional styling for benefits section */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .benefit-card {
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .benefit-card:hover {
            border-color: #2B3388;
            box-shadow: 0 2px 6px rgba(43, 51, 136, 0.07);
        }

        .benefit-card h4 {
            color: #2B3388;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .producer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin: 20px 0 0;
        }
        .producer-tile {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            min-height: 130px;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 8px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .producer-tile:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .producer-tile img {
            max-width: 100%;
            max-height: 66px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .producer-tile .producer-wordmark {
            color: #2B3388;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.4px;
            line-height: 1.3;
        }
        .producer-tile .producer-product {
            color: #2B3388;
            font-size: 0.86rem;
        }
        .producer-tile .producer-standard {
            color: #6c757d;
            font-size: 0.76rem;
        }

        /* Make image closer to text */
        .img-close-to-text {
            margin-right: 15px;
        }

        /* Improved first section styling */
        .intro-section {
            background: rgba(43, 51, 136, 0.04);
            padding: 30px;
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            margin-bottom: 30px;
        }

        .intro-section h2 {
            color: #2B3388;
            font-weight: 700;
            font-size: 32px;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .intro-section p {
            font-size: 15px;
            line-height: 1.6;
            color: #2B3388;
        }

        @media (max-width: 767.98px) {
            .highlighted-section, .ingelo-application-form {
                padding: 20px 15px;
            }
            .producer-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            .intro-section h2 {
                font-size: 1.3rem;
            }
            .intro-section {
                padding: 20px 15px;
            }
            .benefits-grid {
                grid-template-columns: 1fr;
            }
            .breadcrumb-content .title { font-size: 1.5rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('ingelo', 'assets/img/bg/Ingelo.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Certification</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['ingelo_hero_title']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['ingelo_hero_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- 1. Introduction Section (Improved) -->
                <div class="intro-section">
                    <div class="row align-items-start g-3">
                        <!-- Image Column -->
                        <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                            <img src="<?= pc_h(pc_image_src($pc['ingelo_intro_image'], 'assets/img/quality/ingelo-certification-black.png')) ?>" alt="<?= pc_h($pc['ingelo_intro_image_alt']) ?>" class="img-fluid rounded img-close-to-text" style="object-fit: contain; min-width: 120px; max-width: 100%;">
                        </div>

                        <!-- Content Column -->
                        <div class="col-lg-9 col-md-8">
                            <h2><?= pc_h($pc['ingelo_intro_title']) ?></h2>
                            <div class="section-divider" style="margin-left: 0; margin-right: 0;"></div>
                            <div class="mt-3"><?= pc_paragraphs_html($pc['ingelo_intro_body']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- 2. What is the Ingelo Certification Scheme? -->
                <div class="highlighted-section">
                    <h3><?= pc_h($pc['ingelo_section_what_title']) ?></h3>
                    <p><?= pc_h($pc['ingelo_section_what_lead']) ?></p>
                    <ul>
                        <li><strong><?= pc_h($pc['ingelo_section_what_item_1_title']) ?>:</strong> <?= pc_h($pc['ingelo_section_what_item_1_body']) ?></li>
                        <li><strong><?= pc_h($pc['ingelo_section_what_item_2_title']) ?>:</strong> <?= pc_h($pc['ingelo_section_what_item_2_body']) ?></li>
                    </ul>
                </div>

                <!-- 3. Benefits of Participation -->
                <div class="highlighted-section">
                    <h3><?= pc_h($pc['ingelo_benefits_title']) ?></h3>
                    <div class="benefits-grid">
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_1_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_1_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_2_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_2_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_3_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_3_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_4_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_4_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_5_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_5_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_6_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_6_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_7_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_7_body']) ?></p>
                        </div>
                        <div class="benefit-card">
                            <h4><?= pc_h($pc['ingelo_benefit_8_title']) ?></h4>
                            <p><?= pc_h($pc['ingelo_benefit_8_body']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- 4. Who Can Apply? -->
                <div class="highlighted-section">
                    <h3><?= pc_h($pc['ingelo_who_title']) ?></h3>
                    <ul>
                        <li><?= pc_h($pc['ingelo_who_item_1']) ?></li>
                        <li><?= pc_h($pc['ingelo_who_item_2']) ?></li>
                        <li><?= pc_h($pc['ingelo_who_item_3']) ?></li>
                    </ul>
                </div>

                <!-- 5. Ingelo Certified Producers -->
                <?php if ($ingelo_producers): ?>
                <div class="highlighted-section">
                    <h3><?= pc_h($pc['ingelo_certified_title']) ?></h3>
                    <div class="producer-grid">
                        <?php foreach ($ingelo_producers as $ip):
                            $ip_logo = trim((string)($ip['logo_path'] ?? ''));
                        ?>
                        <div class="producer-tile">
                            <?php if ($ip_logo !== ''): ?>
                                <img src="<?= pc_h($ip_logo) ?>" alt="<?= pc_h($ip['name']) ?> logo">
                            <?php else: ?>
                                <div class="producer-wordmark"><?= pc_h($ip['name']) ?></div>
                            <?php endif; ?>
                            <?php if (trim((string)($ip['product'] ?? '')) !== ''): ?>
                                <div class="producer-product"><?= pc_h($ip['product']) ?></div>
                            <?php endif; ?>
                            <?php if (trim((string)($ip['standard'] ?? '')) !== ''): ?>
                                <div class="producer-standard"><?= pc_h($ip['standard']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- 6. Application Form -->
                <div class="ingelo-application-form">
                    <h3><?= pc_h($pc['ingelo_apply_title']) ?></h3>
                    <p><?= pc_h($pc['ingelo_apply_lead']) ?></p>
                    <ol>
                        <li><?= pc_h($pc['ingelo_apply_step_1']) ?></li>
                        <li><?= pc_h($pc['ingelo_apply_step_2']) ?></li>
                    </ol>
                    <div class="mt-3">
                        <a href="<?= pc_h($pc['ingelo_apply_button_url']) ?>" class="btn-cta" target="_blank">
                            <?= pc_h($pc['ingelo_apply_button_text']) ?>
                        </a>
                    </div>
                    <p class="mt-3 small">
                        <?= pc_h($pc['ingelo_apply_support_note']) ?>
                    </p>
                </div>

            </div>
        </section>

        <!-- CTA Section - same style as all other pages -->
        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title"><?= pc_h($pc['ingelo_cta_title']) ?></h2>
                        <p class="cta-subtitle"><?= pc_h($pc['ingelo_cta_subtitle']) ?></p>
                        <a href="<?= pc_h($pc['ingelo_cta_btn_1_url']) ?>" class="btn-cta"><?= pc_h($pc['ingelo_cta_btn_1_text']) ?></a>
                        <a href="<?= pc_h($pc['ingelo_cta_btn_2_url']) ?>" class="btn-cta"><?= pc_h($pc['ingelo_cta_btn_2_text']) ?></a>
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

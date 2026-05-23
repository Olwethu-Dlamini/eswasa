<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Certification Services - ESWASA</title>
    <meta name="description" content="ESWASA certification services — independent third-party conformity assessment for management systems, products, testing, and metrology.">
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
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Canonical section title — matches training-*, qoute, services pages */
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

        /* Intro card — wraps important descriptive paragraphs */
        .intro-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            padding: 26px 28px;
            max-width: 920px;
            margin: 0 auto;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .intro-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }

        /* Featured intro card — first card under the title.
           Drops the blue left accent and uses a stronger always-on shadow. */
        .intro-card.intro-card-featured {
            border: 1px solid rgba(43, 51, 136, 0.10);
            border-left: 1px solid rgba(43, 51, 136, 0.10);
            box-shadow: 0 10px 28px rgba(43, 51, 136, 0.22);
        }
        .intro-card.intro-card-featured:hover {
            border-color: rgba(43, 51, 136, 0.25);
            box-shadow: 0 16px 36px rgba(43, 51, 136, 0.28);
        }
        .intro-card p {
            margin: 0;
            color: #2B3388;
            font-size: 15px;
            line-height: 1.7;
        }

        .cert-section {
            padding: 50px 0;
        }
        .cert-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 32px 28px;
            margin: 10px 0;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.06);
            transition: border-color .25s ease, box-shadow .25s ease;
            position: relative;
            overflow: hidden;
        }
        .cert-card .card-mark {
            position: absolute;
            bottom: 12px;
            right: 12px;
            width: 210px;
            height: 210px;
            object-fit: contain;
            opacity: 1;
            pointer-events: none;
        }
        .cert-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .cert-card h3 {
            color: #2B3388;
            margin-bottom: 20px;
            font-size: 1.6rem;
            font-weight: 700;
        }
        .cert-card p {
            font-size: 15px;
            line-height: 1.65;
            margin-bottom: 16px;
            color: #2B3388;
        }
        .btn-cert {
            background: #2B3388;
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 10px 0 0;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-cert:hover {
            background: rgba(43, 51, 136, 0.85);
            color: #fff;
        }
        /* Certification Marks grid — mirrors index.php pattern */
        .marks-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }
        .mark-item {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 30px 24px 22px;
            display: flex;
            flex-direction: column;
            text-align: center;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .mark-item:hover {
            border-color: #2B3388;
            box-shadow: 0 8px 22px rgba(43, 51, 136, 0.10);
            transform: translateY(-3px);
        }
        .mark-image {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
        }
        .mark-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .mark-title {
            color: #2B3388;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 14px;
        }
        .mark-desc {
            color: #2B3388;
            font-size: 13.5px;
            line-height: 1.65;
            margin: 0 0 22px;
            flex: 1;
        }
        .mark-actions {
            display: flex;
            justify-content: center;
            gap: 22px;
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid rgba(43, 51, 136, 0.10);
        }
        .mark-actions a {
            color: #2B3388;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .2s ease, color .2s ease;
        }
        .mark-actions a:hover { text-decoration: underline; }
        .mark-actions a i { font-size: 11px; transition: transform .2s ease; }
        .mark-actions a:hover i { transform: translateX(2px); }
        @media (max-width: 991.98px) {
            .marks-grid { grid-template-columns: repeat(2, 1fr); gap: 22px; }
            .mark-image { height: 180px; }
        }
        @media (max-width: 575.98px) {
            .marks-grid { grid-template-columns: 1fr; gap: 16px; }
            .mark-item { padding: 24px 20px 18px; }
            .mark-image { height: 170px; margin-bottom: 18px; }
            .mark-title { font-size: 15px; }
            .mark-desc { font-size: 13px; margin-bottom: 18px; }
            .mark-actions { gap: 18px; }
            .mark-actions a { font-size: 12.5px; }
        }
        /* ── Steps to certification image section ── */
        .steps-img-section {
            text-align: center;
        }
        .steps-img-section img {
            max-width: 100%;
            height: auto;
        }

        /* Cert-split cards — "Why Certify" + "Our Focus Areas" */
        .cert-split-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 26px 26px 24px;
            height: 100%;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .cert-split-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .cert-split-card-title {
            color: #2B3388;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0 0 14px;
            line-height: 1.3;
            position: relative;
            padding-bottom: 12px;
        }
        .cert-split-card-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 36px;
            height: 2px;
            background: #2B3388;
        }
        .cert-split-card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
            margin: 0;
        }
        @media (max-width: 991.98px) {
            .cert-split-left .cert-split-card {
                margin-bottom: 16px;
            }
        }
        @media (max-width: 767.98px) {
            .cert-split-card { padding: 22px 20px; }
            .cert-split-card-title { font-size: 1.05rem; }
            .cert-split-card p { font-size: 0.95rem; }
        }

        /* ── Page-wide mobile layout ── */
        @media (max-width: 991.98px) {
            .cert-section { padding: 40px 0; }
            .cert-card { padding: 26px 22px; }
            .cert-card h3 { font-size: 1.35rem; }
            .cert-card p { font-size: 0.98rem; }
            .cert-card .card-mark { width: 150px; height: 150px; opacity: 0.6; }
            .cert-images { gap: 22px; }
            .cert-image-item { max-width: 170px; }
        }
        @media (max-width: 767.98px) {
            section.breadcrumb-area .title { font-size: 1.6rem; }
            .cert-section { padding: 32px 0; }
            section h2 { font-size: 1.45rem !important; }
            section h4 { font-size: 1.05rem !important; }
            .display-6 { font-size: 1.55rem !important; }
            .intro-card { padding: 20px 18px; }
            .intro-card p { font-size: 0.95rem; line-height: 1.6; }
            .cert-images { gap: 16px; }
            .cert-image-item { max-width: 45%; }
            .cert-image-item img { max-height: 90px; width: auto; }
            .cert-image-item p { font-size: 0.88rem; }
            .cert-image-item small { font-size: 0.78rem; line-height: 1.35; display: inline-block; }
            .cert-card { padding: 22px 18px; }
            .cert-card h3 { font-size: 1.2rem; }
            .cert-card p, .cert-card ul li { font-size: 0.95rem; }
            .cert-card .card-mark {
                position: static;
                display: block;
                width: 130px;
                height: 130px;
                opacity: 1;
                margin: 8px auto 18px;
            }
            .btn-cert { width: 100%; text-align: center; margin-right: 0; padding: 12px 20px; }
            .steps-img-section img { max-width: 100%; }
        }
        @media (max-width: 575.98px) {
            section h2 { font-size: 1.3rem !important; }
            .cert-card .card-mark { width: 110px; height: 110px; }
            .cert-image-item { max-width: 47%; }
            .cert-image-item img { max-height: 75px; }
        }

        /* Subtitle / lead — align with site spec (16px) */
        body .lead { font-size: 16px; line-height: 1.7; font-weight: 400; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('certification', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                            </nav>
                            <h1 class="title">Certification Services</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Certification Images with Meaning -->
        <section class="cert-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">Your Path to Quality Excellence</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="intro-card intro-card-featured mb-5">
                    <p>The core business of the ESWASA Certification department is the provision of an independent, third-party conformity assessment service for systems and products, in accordance with requirements of ISO/IEC 17021 for management systems certification and ISO/IEC 17065 for product certification.</p>
                </div>
                <div class="row mb-4 cert-split g-3">
                    <div class="col-lg-6 cert-split-left">
                        <div class="cert-split-card">
                            <h4 class="cert-split-card-title">Why Certify</h4>
                            <p>Businesses with ESWASA Certification benefit from a competitive edge, greater access to local and international trade opportunities and increased market access. They achieve organisational objectives and manage their risks.</p>
                        </div>
                    </div>
                    <div class="col-lg-6 cert-split-right">
                        <div class="cert-split-card">
                            <h4 class="cert-split-card-title">Our Focus Areas</h4>
                            <p>The department mainly focuses on Management Systems Certification, Ingelo Certification, Product Certification (ESWASA Mark), Testing Services, and Scales and Metrology Services.</p>
                        </div>
                    </div>
                </div>
                <div class="marks-grid">
                    <div class="mark-item">
                        <div class="mark-image">
                            <img src="assets/img/quality/management-mark-black.png" alt="Management Systems Certification Mark">
                        </div>
                        <h3 class="mark-title">Management Systems Certification Mark</h3>
                        <p class="mark-desc">Awarded to organisations whose quality, environmental, food safety or occupational health management systems have been independently audited and proven to meet recognised international standards. Provides for continuous, systematic verification of effectiveness.</p>
                        <div class="mark-actions">
                            <a href="managementsystems.php">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            <a href="certification-status.php">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="mark-item">
                        <div class="mark-image">
                            <img src="assets/img/quality/product-certification-black.png" alt="Product Certification Mark">
                        </div>
                        <h3 class="mark-title">Product Certification Mark</h3>
                        <p class="mark-desc">A voluntary product certification scheme operated by the Eswatini Standards Authority. Awarded to products manufactured to declared national and international standards and proven through rigorous, independent testing &mdash; giving buyers confidence in quality and safety.</p>
                        <div class="mark-actions">
                            <a href="Certification.php">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            <a href="certification-status.php">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="mark-item">
                        <div class="mark-image">
                            <img src="assets/img/quality/compulsory-standards-black.png" alt="Compulsory Standards Quality Mark">
                        </div>
                        <h3 class="mark-title">Compulsory Standards Quality Mark</h3>
                        <p class="mark-desc">A mandatory mark applied to products covered by compulsory technical regulations in Eswatini. Demonstrates compliance has been proven through comprehensive assessment and ongoing surveillance, protecting consumers and supporting fair trade.</p>
                        <div class="mark-actions">
                            <a href="Certification.php">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            <a href="certification-status.php">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="mark-item">
                        <div class="mark-image">
                            <img src="assets/img/quality/ingelo-certification-black.png" alt="Ingelo MSME Product Certification Mark">
                        </div>
                        <h3 class="mark-title">Ingelo MSME Product Certification Mark</h3>
                        <p class="mark-desc">A simplified, affordable certification scheme designed for micro, small and medium enterprises (MSMEs) and local producers &mdash; helping them prove product quality, access new markets and grow with credibility.</p>
                        <div class="mark-actions">
                            <a href="ingelo.php">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                            <a href="certification-status.php">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Benefits -->
        <section class="cert-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">What Certification Can Do For Your Business</h2>
                    <div class="section-divider"></div>
                </div>
                <div class="intro-card mb-5">
                    <p>Businesses with ESWASA Certification benefit from a competitive edge, greater access to local and international trade opportunities and increased market access. They achieve organisational objectives and manage their risks.</p>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3>Boost Your Market Presence</h3>
                            <p>Imagine walking into new markets with confidence, knowing your products meet the highest standards. ESWASA certification opens doors to government tenders, international exports, and premium customers who demand quality assurance.</p>
                            <p><strong>You'll be able to:</strong></p>
                            <ul>
                                <li>Access lucrative government contracts</li>
                                <li>Export to regional markets seamlessly</li>
                                <li>Charge premium prices for certified quality</li>
                                <li>Stand out from your competitors</li>
                            </ul>
                            <img src="assets/img/quality/product-certification-blue.png" alt="Product Mark" class="card-mark">
                            <a href="product.php" class="btn-cert">Explore Product Certification</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3>Streamline Your Operations</h3>
                            <p>Stop wasting resources on inefficient processes. Our management system certification helps you create workflows that save time, reduce errors, and cut costs. Many businesses save up to 30% on operational expenses after certification.</p>
                            <p><strong>You'll experience:</strong></p>
                            <ul>
                                <li>Reduced product defects and returns</li>
                                <li>Faster response to customer needs</li>
                                <li>Improved employee productivity</li>
                                <li>Better resource utilisation</li>
                            </ul>
                            <img src="assets/img/quality/management-mark-blue.png" alt="Management Mark" class="card-mark">
                            <a href="managementsystems.php" class="btn-cert">Discover Management Systems</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Steps to Certification -->
        <section class="cert-section bg-light">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 style="color: #2B3388;">Your Certification Journey Made Simple</h2>
                    <div class="section-divider"></div>
                    <p class="lead mt-3">We guide you every step of the way - no stress, no surprises</p>
                </div>
                <div class="steps-img-section">
                    <img src="assets/img/steps-to-certification.jpg" alt="Steps to Certification: Gap Analysis, Training and Documentation, Internal Audit and MRM, Audit and Certification, ISO Certified">
                </div>
            </div>
        </section>


        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title">Begin Your Certification Journey</h2>
                        <p class="cta-subtitle">Begin your certification journey with ESWASA today.</p>
                        <a href="contact.php" class="btn-cta">Contact Us</a>
                        <a href="qoute_certification.php" class="btn-cta">Request Quote</a>
                        <a href="training-about.php" class="btn-cta">Training Programs</a>
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
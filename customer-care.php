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
    <title>Customer Care - ESWASA</title>
    <meta name="description" content="ESWASA Customer Care — service charter, customer feedback and complaints, and our public policies. We are committed to serving you with transparency, responsiveness and professionalism.">
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

    <style>
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 16px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
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
            margin: 0 auto 50px;
        }
        .intro-card p {
            margin: 0; color: rgba(43, 51, 136, 0.85);
            font-size: 1rem; line-height: 1.7;
        }

        .care-section { padding: 60px 0 80px; }
        .care-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .care-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 32px 28px 28px;
            text-decoration: none !important;
            color: inherit;
            display: flex;
            flex-direction: column;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .care-card:hover {
            border-color: #2B3388;
            box-shadow: 0 8px 22px rgba(43, 51, 136, 0.10);
            transform: translateY(-3px);
        }
        .care-card .icon-wrap {
            width: 64px; height: 64px;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 22px;
        }
        .care-card .icon-wrap i { font-size: 26px; }
        .care-card h3 {
            color: #2B3388;
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 12px;
        }
        .care-card p {
            color: rgba(43, 51, 136, 0.82);
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0 0 22px;
            flex: 1;
        }
        .care-card .card-cta {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: transform .2s ease;
        }
        .care-card:hover .card-cta i { transform: translateX(3px); }

        @media (max-width: 991.98px) {
            .care-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 575.98px) {
            .care-section { padding: 40px 0 50px; }
            .care-grid { grid-template-columns: 1fr; gap: 16px; }
            .care-card { padding: 26px 22px 22px; }
            .breadcrumb-content .title { font-size: 1.6rem; }
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
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('customer-care', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Customer Care</span>
                            </nav>
                            <h3 class="title">Customer Care</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="care-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">We Are Here To Serve You</h2>
                    <div class="section-divider"></div>
                </div>

                <div class="intro-card">
                    <p>At ESWASA, customer care is at the heart of everything we do. We are committed to transparency, responsiveness and professional service. Whether you need to understand our service commitments, share feedback or a complaint, or review our public policies — this is your starting point.</p>
                </div>

                <div class="care-grid">
                    <a href="service-charter.php" class="care-card">
                        <div class="icon-wrap"><i class="fas fa-handshake"></i></div>
                        <h3>Service Charter</h3>
                        <p>Our public commitments to you — service standards, turnaround times, and how we measure ourselves on quality, accessibility and responsiveness.</p>
                        <span class="card-cta">Read the charter <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </a>

                    <a href="customer-feedback.php" class="care-card">
                        <div class="icon-wrap"><i class="fas fa-comment-dots"></i></div>
                        <h3>Customer Feedback &amp; Complaints</h3>
                        <p>Tell us how we're doing. Submit a complaint, compliment, suggestion or appeal — we treat every submission seriously and respond promptly.</p>
                        <span class="card-cta">Open the form <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </a>

                    <a href="policies.php" class="care-card">
                        <div class="icon-wrap"><i class="fas fa-file-alt"></i></div>
                        <h3>Policies</h3>
                        <p>Our public policies and procedures — including impartiality, complaints handling, appeals, certification rules and information requests.</p>
                        <span class="card-cta">View policies <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
                    </a>
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

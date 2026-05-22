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
    <title>Service Charter - ESWASA</title>
    <meta name="description" content="The ESWASA Service Charter — our commitments to customers on accessibility, turnaround times, quality of service, impartiality and how to escalate complaints.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
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
            margin: 0 auto 40px;
        }
        .intro-card p {
            margin: 0; color: rgba(43, 51, 136, 0.85);
            font-size: 1rem; line-height: 1.7;
        }

        .charter-section { padding: 60px 0 80px; }
        .charter-block {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            padding: 28px 28px;
            margin-bottom: 22px;
        }
        .charter-block h3 {
            color: #2B3388;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 12px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(43, 51, 136, 0.20);
        }
        .charter-block p,
        .charter-block li {
            color: rgba(43, 51, 136, 0.85);
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 10px;
        }
        .charter-block ul { padding-left: 20px; margin: 0; }

        .commitment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin-top: 20px;
        }
        .commitment-item {
            background: rgba(43, 51, 136, 0.04);
            border-left: 3px solid #2B3388;
            padding: 16px 18px;
            border-radius: 0 4px 4px 0;
        }
        .commitment-item strong { display: block; color: #2B3388; margin-bottom: 4px; font-size: 0.95rem; }
        .commitment-item span { color: rgba(43, 51, 136, 0.82); font-size: 0.92rem; line-height: 1.5; }

        .contact-cta {
            background: #2B3388;
            color: #fff;
            padding: 32px 32px;
            border-radius: 4px;
            text-align: center;
            margin-top: 30px;
        }
        .contact-cta h3 { color: #fff; font-weight: 700; margin: 0 0 10px; }
        .contact-cta p { color: rgba(255,255,255,0.9); margin: 0 0 18px; line-height: 1.6; }
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
        .contact-cta .btn-charter:hover { background: rgba(255,255,255,0.9); }

        @media (max-width: 767.98px) {
            .charter-section { padding: 40px 0 50px; }
            .commitment-grid { grid-template-columns: 1fr; }
            .breadcrumb-content .title { font-size: 1.6rem; }
            .charter-block { padding: 22px 20px; }
        }
    </style>
</head>
<body>
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

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('service-charter', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                                    <a href="customer-care.php">Customer Care</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Service Charter</span>
                            </nav>
                            <h3 class="title">ESWASA Service Charter</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="charter-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">Our Commitments To You</h2>
                    <div class="section-divider"></div>
                </div>

                <div class="intro-card">
                    <p>The ESWASA Service Charter sets out the standards of service you can expect from the Eswatini Standards Authority. It is our public statement of what we will deliver, how we will deliver it, and how you can hold us accountable when we fall short.</p>
                </div>

                <div class="row">
                    <div class="col-lg-12">

                        <div class="charter-block">
                            <h3>Who We Are</h3>
                            <p>The Eswatini Standards Authority (ESWASA) is the national standards body of the Kingdom of Eswatini. We develop national standards, operate certification and testing services, provide metrology and calibration support, and represent Eswatini in regional and international standardisation bodies.</p>
                        </div>

                        <div class="charter-block">
                            <h3>Our Service Standards</h3>
                            <p>We commit to the following service standards across all interactions:</p>
                            <div class="commitment-grid">
                                <div class="commitment-item">
                                    <strong>Acknowledgement</strong>
                                    <span>We acknowledge written enquiries within 3 working days.</span>
                                </div>
                                <div class="commitment-item">
                                    <strong>Full response</strong>
                                    <span>We provide a substantive response within 14 working days, or update you if more time is needed.</span>
                                </div>
                                <div class="commitment-item">
                                    <strong>Quotation requests</strong>
                                    <span>Service quotations issued within 5 working days of receipt of complete information.</span>
                                </div>
                                <div class="commitment-item">
                                    <strong>Certification applications</strong>
                                    <span>Application receipt confirmed within 5 working days; audit scheduling within 30 working days.</span>
                                </div>
                                <div class="commitment-item">
                                    <strong>Testing turnaround</strong>
                                    <span>Standard test reports delivered within the timeframe agreed at sample acceptance.</span>
                                </div>
                                <div class="commitment-item">
                                    <strong>Complaints</strong>
                                    <span>Acknowledged within 3 working days, resolved within 30 working days where possible.</span>
                                </div>
                            </div>
                        </div>

                        <div class="charter-block">
                            <h3>Our Core Values</h3>
                            <ul>
                                <li><strong>Transparency</strong> — clear, accessible information about our processes, fees and decisions.</li>
                                <li><strong>Responsiveness</strong> — we listen, we act, and we communicate progress.</li>
                                <li><strong>People-Centricity</strong> — every customer receives respectful, professional attention.</li>
                                <li><strong>Innovation</strong> — we continuously improve our services and adopt better practice.</li>
                                <li><strong>Professionalism</strong> — competence, impartiality and integrity in everything we do.</li>
                            </ul>
                        </div>

                        <div class="charter-block">
                            <h3>What We Ask Of You</h3>
                            <p>To help us deliver these commitments, we ask that you:</p>
                            <ul>
                                <li>Provide accurate and complete information when making requests.</li>
                                <li>Respect our staff and treat them with courtesy.</li>
                                <li>Honour scheduled appointments, audits and sample submission dates.</li>
                                <li>Pay applicable fees on time.</li>
                                <li>Notify us promptly of any change in your details or scope.</li>
                            </ul>
                        </div>

                        <div class="charter-block">
                            <h3>If We Fall Short</h3>
                            <p>If our service does not meet the standards set out in this charter, we want to know. You can:</p>
                            <ul>
                                <li>Submit feedback or a complaint through our online <a href="customer-feedback.php">Customer Feedback form</a>.</li>
                                <li>Write to us at <a href="mailto:info@swasa.co.sz">info@swasa.co.sz</a>.</li>
                                <li>Call us on <strong>(+268) 2518 4633 / 4610</strong>.</li>
                                <li>Visit our offices in Matsapha during working hours.</li>
                            </ul>
                            <p>For matters relating to certification decisions, our <a href="CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf" target="_blank">Appeals Handling Procedure</a> sets out a formal route.</p>
                        </div>

                        <div class="contact-cta">
                            <h3>Have feedback for us?</h3>
                            <p>Whether it is a complaint, a compliment or a suggestion — we want to hear from you.</p>
                            <a href="customer-feedback.php" class="btn-charter">Submit Feedback</a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

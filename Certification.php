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
    <title>Get Certified with ESWASA - Your Gateway to Quality Excellence</title>
    <meta name="description" content="Transform your business with ESWASA certification. Build trust, access new markets, and demonstrate your commitment to quality standards in Eswatini.">
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
        .cert-section {
            padding: 50px 0;
        }
        .cert-card {
            background: white;
            border-radius: 10px;
            padding: 35px 30px;
            margin: 10px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
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
            transform: translateY(-5px);
        }
        .cert-card h3 {
            color: #2e3191;
            margin-bottom: 20px;
            font-size: 1.6rem;
            font-weight: 700;
        }
        .cert-card p {
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 25px;
            color: #555;
        }
        .btn-cert {
            background: #2e3191;
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
            background: #1a1f71;
            color: white;
        }
        .cert-images {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin: 25px 0 10px;
        }
        .cert-image-item {
            text-align: center;
            max-width: 200px;
        }
        .cert-image-item img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }
        .cert-image-item p {
            font-weight: 600;
            color: #2e3191;
            margin: 0;
        }
        .cert-image-item small {
            color: #666;
            font-size: 0.9rem;
        }
        /* ── Steps to certification image section ── */
        .steps-img-section {
            text-align: center;
        }
        .steps-img-section img {
            max-width: 100%;
            height: auto;
        }

        /* Side-by-side paragraphs with subtle divider */
        @media (min-width: 992px) {
            .cert-split-left {
                border-right: 1px solid rgba(0,0,0,0.12);
                padding-right: 30px;
            }
            .cert-split-right {
                padding-left: 30px;
            }
        }
        @media (max-width: 991.98px) {
            .cert-split-left {
                border-bottom: 1px solid rgba(0,0,0,0.12);
                padding-bottom: 20px;
                margin-bottom: 20px;
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
                            <h3 class="title">Certification Services</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Certification Images with Meaning -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-3">
                        <h2 style="color: #2e3191;">Your Path to Quality Excellence</h2>
                        <p class="lead">The core business of the ESWASA Certification department is the provision of an independent, third-party conformity assessment service for systems and products, in accordance with requirements of ISO/IEC 17021 for management systems certification and ISO/IEC 17065 for product certification.</p>
                    </div>
                </div>
                <div class="row mb-4 cert-split">
                    <div class="col-lg-6 cert-split-left">
                        <p class="lead" style="text-align: left;">Businesses with ESWASA Certification benefit from a competitive edge, greater access to local and international trade opportunities and increased market access. They achieve organisational objectives and manage their risks.</p>
                    </div>
                    <div class="col-lg-6 cert-split-right">
                        <p class="lead" style="text-align: left;">The department mainly focuses on Management Systems Certification, Ingelo Certification, Product Certification (ESWASA Mark), Testing Services, and Scales and Metrology Services.</p>
                    </div>
                </div>
                <div class="text-center mb-3">
                    <h4 style="color: #2e3191; font-weight: 600;">Each ESWASA mark represents a commitment to quality, trust, and excellence in Eswatini</h4>
                </div>
                <div class="cert-images">
                    <div class="cert-image-item">
                        <img src="assets/img/management.png" alt="Quality Certification Mark" class="img-fluid">
                        <p>Management Systems Certification Mark</p>
                        <small>Provides for continuous systematic verification of effectiveness</small>
                    </div>
                    <div class="cert-image-item">
                        <img src="assets/img/product.png" alt="Standards Compliance" class="img-fluid">
                        <p>Product Certification Mark</p>
                        <small>Shows you meet national & international standards</small>
                    </div>
                    <div class="cert-image-item">
                        <img src="assets/img/compulsory.png" alt="Rigorous Testing" class="img-fluid">
                        <p>Compulsory Standards Quality Mark</p>
                        <small>Proven through comprehensive assessment</small>
                    </div>
                    <div class="cert-image-item">
                        <img src="assets/img/Ingelo.png" alt="Approved Products" class="img-fluid">
                        <p>Ingelo Certification Scheme Mark</p>
                        <small>Scheme for local producers</small>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Benefits -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center mb-3">
                        <h2 style="color: #2e3191;">What Certification Can Do For Your Business</h2>
                        <p class="lead">Businesses with ESWASA Certification benefit from a competitive edge, greater access to local and international trade opportunities and increased market access. They achieve organisational objectives and manage their risks.</p>
                    </div>
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
                            <a href="product.php" class="btn-cert">Explore Product Certification</a>
                            <img src="assets/img/product.png" alt="Product Mark" class="card-mark">
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
                                <li>Better resource utilization</li>
                            </ul>
                            <a href="managementsystems.php" class="btn-cert">Discover Management Systems</a>
                            <img src="assets/img/management.png" alt="Management Mark" class="card-mark">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Steps to Certification -->
        <section class="cert-section bg-light">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 style="color: #2e3191;">Your Certification Journey Made Simple</h2>
                    <p class="lead">We guide you every step of the way - no stress, no surprises</p>
                </div>
                <div class="steps-img-section">
                    <img src="assets/img/steps-to-certification.jpg" alt="Steps to Certification: Gap Analysis, Training and Documentation, Internal Audit and MRM, Audit and Certification, ISO Certified">
                </div>
            </div>
        </section>


        <!-- Get Started -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 style="color: #2e3191;">Ready to Get Started?</h2>
                        <p class="mb-4">Begin your certification journey with ESWASA today</p>
                        <a href="contact.php" class="btn-cert">Contact Us</a>
                        <a href="qoute_certification.php" class="btn-cert">Request Quote</a>
                        <a href="training-about.php" class="btn-cert">Training Programs</a>
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
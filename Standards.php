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
    <meta http-equiv="x ua-compatible" content="ie=edge">
    <title>Standards Development - ESWASA</title>
    <meta name="description" content="ESWASA's standards development process under the Standards Act, 1968. Learn how Eswatini National Standards (SZNS) are developed and how to participate.">
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
        .btn-dev {
            background-color: #2E3191;
            color: white;
            border-color: #2E3191;
            margin: 5px;
            font-weight: 600;
            padding: 10px 22px;
        }
        .btn-dev:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            color: white;
        }
        /* Clean sections — no blue borders, no icons */
        .highlighted-section {
            background-color: #f8f9fd;
            padding: 25px;
            margin: 30px 0;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(46, 49, 145, 0.04);
        }
        .highlighted-section h3 {
            color: #2E3191;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .dev-process-steps {
            list-style-type: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .dev-process-steps li {
            counter-increment: step-counter;
            margin-bottom: 22px;
            padding-left: 45px;
            position: relative;
        }
        .dev-process-steps li::before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 2px;
            background-color: #2E3191;
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

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            .dev-process-steps li {
                padding-left: 40px;
            }
            .dev-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
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
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('standards', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Development</span>
                            </nav>
                            <h3 class="title">Standards Development</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- 1. About Standards Development -->
                <div class="highlighted-section">
                    <h3>Standards Development</h3>
                    <p>
                        The ESWASA Standards Department has vast experience in its core function, namely, the development of Eswatini National Standards (SZNS) and maximising the benefits of international standards through adoption, which enhances the competitiveness of the Eswatini industry and advances regional and international trade.
                    </p>
                    <p>
                        Standards are a collective work, whereby Committees of manufacturers, users, research organizations, government departments and consumers work together to draw up standards that evolve to meet the demands of society and technology.
                    </p>
                    <h3>Benefits of Standards</h3>
                    <p>Standards:</p>
                    <ul>
                        <li>ensure that consumers have easier access to and greater choice in goods and services.</li>
                        <li>ensure improved quality and reliability.</li>
                        <li>ensure better operation and compatibility between products and services.</li>
                    </ul>
                    <p>
                        Standards are designed for voluntary use and do not impose any regulations. However, laws and regulations may reference certain standards and make compliance with them compulsory.
                    </p>
                </div>

                <!-- Process -->
                <div class="highlighted-section">
                    <h3>Standards Development Process</h3>
                    <p>The Standards Development Process follows 9 stages:</p>
                    <ol class="dev-process-steps">
                        <li><strong>STAGE 0 - Preliminary Stage</strong></li>
                        <li><strong>STAGE 1 - Proposal Stage</strong></li>
                        <li><strong>STAGE 2 - Preparatory Stage</strong></li>
                        <li><strong>STAGE 3 - Committee Stage</strong></li>
                        <li><strong>STAGE 4 - Enquiry Stage</strong></li>
                        <li><strong>STAGE 5 - Disposal of Comments Stage</strong></li>
                        <li><strong>STAGE 6 - Approval Stage</strong></li>
                        <li><strong>STAGE 7 - Endorsement Stage</strong></li>
                        <li><strong>STAGE 8 - Publication Stage</strong></li>
                    </ol>
                </div>

                <!-- Proposal Form -->
                <div class="highlighted-section">
                    <h3>Submitting a Standards Proposal</h3>
                    <p>To propose a new standard or revision of an existing SZNS, please:</p>
                    <ol>
                        <li>Complete the official <strong>Standards Development Proposal Form (SD-01)</strong></li>
                        <li>Email the completed form to <strong><a href="mailto:standards@eswasa.org.sz">standards@eswasa.org.sz</a></strong></li>
                        <li>ESWASA will acknowledge receipt within 5 working days</li>
                    </ol>
                    <div class="mt-3">
                        <a href="admin/uploads/standards_proposal_form.pdf" class="btn btn-dev" target="_blank">
                            Download Proposal Form (PDF)
                        </a>
                    </div>
                    <p class="mt-3 small">
                        <strong>Note:</strong> Proposals must include justification, scope, and potential impact. Priority is given to standards supporting national priorities (e.g., food security, infrastructure, MSME competitiveness).
                    </p>
                </div>

            </div>
        </section>

        <!-- New Section: Implementation Of Standards -->
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="text-center mb-4">Implementation Of Standards</h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <h4>Buy the Standards:</h4>
                        <ul>
                            <li>QUALITY MANAGEMENT SYSTEMS SZNS ISO 9001:2015 – E486,25</li>
                            <li>OCCUPATIONAL HEALTH & SAFETY MANAGEMENT SYSTEMS SZNS ISO 45001:2018 – E742,91</li>
                            <li>ENVIRONMENTAL MANAGEMENT SYSTEMS SZNS ISO 14001:2015 – E599,93</li>
                        </ul>
                        
                        <h4>Attend Standards-based Training:</h4>
                        <ul>
                            <li>E5 500 per person – Understanding & Implementation</li>
                            <li>E2 500 per person – Management Awareness</li>
                        </ul>
                    </div>
                    
                    <div class="col-md-6">
                        <img src="assets/img/implementation_flow.png" alt="Implementation Process Flow" class="img-fluid" />
                        <!-- Note: You would need to create or upload this image file -->
                        <p class="text-center mt-3"><small>#ImprovingTheWorldWeLiveIn</small></p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title">Get Involved in Standards Development</h2>
                        <p class="cta-subtitle">Contact our Standards Unit or register for a Technical Committee to contribute to national standards.</p>
                        <a href="contact.php" class="btn-cta">Contact Standards Unit</a>
                        <a href="tcp.php" class="btn-cta">Register for a Technical Committee</a>
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
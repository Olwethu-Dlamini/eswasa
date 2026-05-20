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
    <title>Terms & Conditions - ESWASA</title>
    <meta name="description" content="ESWASA Terms and Conditions governing use of training programs, services, and website. Eswatini Standards Authority.">

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
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        .legal-content {
            background: #fff;
            padding: 60px 0;
        }
        .legal-section {
            margin-bottom: 50px;
        }
        .legal-section h2 {
            color: #2B3388;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 3px solid #2B3388;
            padding-bottom: 15px;
        }
        .legal-section h3 {
            color: #2B3388;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
        }
        .legal-section p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #2B3388;
        }
        .legal-section ul {
            margin-bottom: 25px;
            padding-left: 20px;
        }
        .legal-section li {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 10px;
            color: #2B3388;
        }
        .highlight-box {
            background: rgba(43, 51, 136, 0.04);
            border-left: 4px solid #2B3388;
            padding: 25px;
            margin: 25px 0;
            border-radius: 0 4px 4px 0;
        }
        .bank-details {
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 20px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .breadcrumb-nav {
            background: rgba(43, 51, 136, 0.04);
            padding: 15px 0;
            margin-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .legal-content { padding: 40px 0; }
            .legal-section { margin-bottom: 30px; }
            .legal-section h2 { font-size: 1.7rem; margin-bottom: 20px; padding-bottom: 10px; }
            .legal-section h3 { font-size: 1.2rem; margin: 22px 0 10px 0; }
            .legal-section p,
            .legal-section li { font-size: 1rem; line-height: 1.7; }
            .highlight-box,
            .bank-details { padding: 16px; margin: 18px 0; }
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
    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    
    <!-- header-area -->
    <?php include("includes/header.php")?>
    
    <!-- main-area -->
    <main class="main-area fix">
        
        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('terms', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Terms and Conditions</span>
                            </nav>
                            <h3 class="title">Terms and Conditions</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Legal Content -->
        <section class="legal-content">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="legal-section">
                            <h2>Terms & Conditions</h2>
                            
                            <div class="highlight-box">
                                <p><strong>Last Updated:</strong> January 2025</p>
                                <p>These Terms and Conditions govern your use of ESWASA services, training programs, and website. By accessing our services, you agree to be bound by these terms.</p>
                            </div>

                            <h3>1. Training Applications and Registration</h3>
                            <p>All training applications must be submitted through the official ESWASA application form available on our website or at our offices. Applications must be received at least 10 working days before the course commencement date.</p>

                            <h3>2. Acceptance and Confirmation</h3>
                            <p>Applicants will be notified of application outcomes. Acceptance must be confirmed in writing via email or fax at least 7 days before training commencement for registration purposes.</p>

                            <h3>3. Payment Terms</h3>
                            <p>Course fees are payable in full and in advance by cash deposit or bank transfer to the ESWASA account. Proof of payment must be submitted to the course administrator 7 working days before classes commence.</p>
                            
                            <div class="bank-details">
                                <h4>Banking Details:</h4>
                                <ul>
                                    <li><strong>Bank Name:</strong> Standard Bank</li>
                                    <li><strong>Account Name:</strong> Eswatini Standards Authority</li>
                                    <li><strong>Account Number:</strong> 9110002956732</li>
                                    <li><strong>Branch Code:</strong> 663164</li>
                                    <li><strong>Branch Name:</strong> Matsapha</li>
                                </ul>
                            </div>

                            <h3>4. Cancellation Policy</h3>
                            <p>A cancellation fee of 50% of the course fee will be charged for cancellations made after confirmation and registration.</p>

                            <h3>5. Certification Requirements</h3>
                            <p>100% attendance is required for certificate issuance. For courses requiring examinations, the pass mark is 60%. Certificates of attendance are issued for non-examination courses.</p>

                            <h3>6. Course Modifications</h3>
                            <p>ESWASA reserves the right to cancel, postpone, or modify courses due to unforeseen circumstances. In such cases, participants will be notified, and fees will be refunded or transferred to alternative courses.</p>

                            <h3>7. Intellectual Property</h3>
                            <p>All training materials, documents, and content provided during ESWASA courses are the intellectual property of ESWASA and may not be reproduced or distributed without prior written consent.</p>

                            <h3>8. In-House Training</h3>
                            <p>Organizations may request in-house training for groups of 5-20 participants. The organization must provide suitable training facilities with audio-visual equipment and refreshments.</p>

                            <h3>9. Governing Law</h3>
                            <p>These terms and conditions are governed by and construed in accordance with the laws of the Kingdom of Eswatini.</p>

                            <div class="highlight-box">
                                <p><strong>Note:</strong> ESWASA reserves the right to modify these terms and conditions at any time. Continued use of our services constitutes acceptance of the modified terms.</p>
                            </div>
                        </div>
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
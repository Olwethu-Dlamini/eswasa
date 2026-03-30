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
    <title>Disclaimer - ESWASA</title>
    <meta name="description" content="">
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
        .legal-content {
            background: #fff;
            padding: 60px 0;
        }
        .legal-section {
            margin-bottom: 50px;
        }
        .legal-section h2 {
            color: #2e3191;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 3px solid #2e3191;
            padding-bottom: 15px;
        }
        .legal-section h3 {
            color: #2e3191;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 30px 0 15px 0;
        }
        .legal-section p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
            color: #333;
        }
        .legal-section ul {
            margin-bottom: 25px;
            padding-left: 20px;
        }
        .legal-section li {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 10px;
            color: #333;
        }
        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #2e3191;
            padding: 25px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        .contact-box {
            background: #e8f4ff;
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .breadcrumb-nav {
            background: #f8f9fa;
            padding: 15px 0;
            margin-bottom: 0;
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('disclaimer', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Disclaimer</span>
                            </nav>
                            <h3 class="title">Website Disclaimer</h3>
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
                            <h2>Website Disclaimer</h2>
                            
                            <div class="highlight-box">
                                <p><strong>Important Notice:</strong> Please read this disclaimer carefully before using our website. By using this website, you accept this disclaimer in full.</p>
                            </div>

                            <h3>1. General Information</h3>
                            <p>The information contained on this website is for general information purposes only. The information is provided by the Eswatini Standards Authority (ESWASA) and while we endeavor to keep the information up to date and correct, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability or availability with respect to the website or the information, products, services, or related graphics contained on the website for any purpose. Any reliance you place on such information is therefore strictly at your own risk.</p>

                            <h3>2. Limitation of Liability</h3>
                            <p>In no event will ESWASA be liable for any loss or damage including without limitation, indirect or consequential loss or damage, or any loss or damage whatsoever arising from loss of data or profits arising out of, or in connection with, the use of this website.</p>

                            <h3>3. External Links</h3>
                            <p>Through this website you are able to link to other websites which are not under the control of ESWASA. We have no control over the nature, content and availability of those sites. The inclusion of any links does not necessarily imply a recommendation or endorse the views expressed within them.</p>

                            <h3>4. Website Availability</h3>
                            <p>Every effort is made to keep the website up and running smoothly. However, ESWASA takes no responsibility for, and will not be liable for, the website being temporarily unavailable due to technical issues beyond our control.</p>

                            <h3>5. Training Information</h3>
                            <p>While we strive to provide accurate and current information about our training programs, course details, schedules, and fees are subject to change without notice. Please contact our training department for the most up-to-date information.</p>

                            <h3>6. Standards and Certification</h3>
                            <p>Information regarding standards, certification processes, and requirements is provided for general guidance. For official standards documents and certification procedures, please consult the relevant official publications or contact ESWASA directly.</p>

                            <h3>7. Copyright and Intellectual Property</h3>
                            <p>All content on this website, including text, graphics, logos, and images, is the property of ESWASA unless otherwise stated. Unauthorized use of any materials may violate copyright, trademark, and other laws.</p>

                            <div class="contact-box">
                                <h3>Contact Information</h3>
                                <p>For clarification on any information provided on this website or for official documentation, please contact:</p>
                                <ul>
                                    <li><strong>ESWATINI STANDARDS AUTHORITY</strong></li>
                                    <li>P.O. Box 1399, Matsapha, Eswatini</li>
                                    <li>Tel: +268 2518 4610</li>
                                    <li>Fax: +268 2518 4526</li>
                                    <li>Email: info@eswasa.co.sz</li>
                                </ul>
                            </div>

                            <div class="highlight-box">
                                <p><strong>Note:</strong> This disclaimer may be updated from time to time. Please check this page regularly to ensure you are familiar with the current version.</p>
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
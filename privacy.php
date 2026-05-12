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
    <title>Privacy Policy - ESWASA</title>
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
        .legal-section h4 {
            color: #2e3191;
            font-size: 1.3rem;
            font-weight: 600;
            margin: 25px 0 15px 0;
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
        .data-types {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('privacy', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Privacy Policy</span>
                            </nav>
                            <h3 class="title">Privacy Policy</h3>
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
                            <h2>Privacy Policy</h2>
                            
                            <div class="highlight-box">
                                <p><strong>Last Updated:</strong> January 2025</p>
                                <p>At Eswatini Standards Authority (ESWASA), we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our services, website, and training programs.</p>
                            </div>

                            <h3>1. Information We Collect</h3>
                            <p>We collect information that you provide directly to us, including:</p>
                            
                            <div class="data-types">
                                <h4>Personal Information:</h4>
                                <ul>
                                    <li>Full name, identification number, and contact details</li>
                                    <li>Email address, phone number, and physical address</li>
                                    <li>Organization name and position</li>
                                    <li>Payment information for training courses</li>
                                    <li>Educational and professional background</li>
                                </ul>
                                
                                <h4>Automatically Collected Information:</h4>
                                <ul>
                                    <li>IP address and browser type</li>
                                    <li>Website usage data and cookies</li>
                                    <li>Device information and operating system</li>
                                </ul>
                            </div>

                            <h3>2. How We Use Your Information</h3>
                            <p>We use the information we collect for the following purposes:</p>
                            <ul>
                                <li>To process training applications and registrations</li>
                                <li>To provide certification services and issue certificates</li>
                                <li>To communicate with you about our services and updates</li>
                                <li>To process payments and maintain financial records</li>
                                <li>To improve our website and services</li>
                                <li>To comply with legal obligations and regulatory requirements</li>
                                <li>To send important notices about course changes or cancellations</li>
                            </ul>

                            <h3>3. Legal Basis for Processing</h3>
                            <p>We process your personal information based on:</p>
                            <ul>
                                <li><strong>Contractual Necessity:</strong> To fulfill our training and certification services</li>
                                <li><strong>Legal Obligations:</strong> To comply with Eswatini laws and regulations</li>
                                <li><strong>Legitimate Interests:</strong> To improve our services and operations</li>
                                <li><strong>Consent:</strong> Where required by law, we obtain your explicit consent</li>
                            </ul>

                            <h3>4. Information Sharing and Disclosure</h3>
                            <p>We may share your information with:</p>
                            <ul>
                                <li>Accredited training partners and instructors</li>
                                <li>Government authorities as required by law</li>
                                <li>Payment processors for transaction purposes</li>
                                <li>Service providers who assist in our operations</li>
                            </ul>
                            <p>We do not sell your personal information to third parties.</p>

                            <h3>5. Data Retention</h3>
                            <p>We retain your personal information for as long as necessary to:</p>
                            <ul>
                                <li>Fulfill the purposes for which it was collected</li>
                                <li>Comply with legal and regulatory requirements</li>
                                <li>Maintain accurate certification records</li>
                                <li>Resolve disputes and enforce our agreements</li>
                            </ul>
                            <p>Training and certification records are typically retained for a minimum of 7 years.</p>

                            <h3>6. Data Security</h3>
                            <p>We implement appropriate technical and organizational measures to protect your personal information, including:</p>
                            <ul>
                                <li>Secure servers and encrypted data storage</li>
                                <li>Access controls and authentication procedures</li>
                                <li>Regular security assessments and updates</li>
                                <li>Staff training on data protection</li>
                            </ul>

                            <h3>7. Your Rights</h3>
                            <p>Under Eswatini data protection laws, you have the right to:</p>
                            <ul>
                                <li>Access your personal information</li>
                                <li>Correct inaccurate or incomplete data</li>
                                <li>Request deletion of your personal information</li>
                                <li>Object to processing of your data</li>
                                <li>Request restriction of processing</li>
                                <li>Data portability where applicable</li>
                            </ul>

                            <h3>8. Cookies and Tracking Technologies</h3>
                            <p>Our website uses cookies to:</p>
                            <ul>
                                <li>Enhance user experience and website functionality</li>
                                <li>Analyze website traffic and usage patterns</li>
                                <li>Remember your preferences and settings</li>
                            </ul>
                            <p>You can control cookie settings through your browser preferences.</p>

                            <h3>9. International Data Transfers</h3>
                            <p>As the national standards body, we primarily process data within Eswatini. Any international transfers of personal data will be conducted in compliance with Eswatini data protection laws.</p>

                            <h3>10. Changes to This Policy</h3>
                            <p>We may update this Privacy Policy from time to time. We will notify you of any significant changes by posting the new policy on our website and updating the "Last Updated" date.</p>

                            <h3>11. Children's Privacy</h3>
                            <p>Our services are not directed to individuals under the age of 18. We do not knowingly collect personal information from children without parental consent.</p>

                            <div class="contact-box">
                                <h3>Contact Information</h3>
                                <p>If you have any questions, concerns, or requests regarding this Privacy Policy or your personal information, please contact us:</p>
                                <ul>
                                    <li><strong>ESWATINI STANDARDS AUTHORITY</strong></li>
                                    <li>Data Protection Officer</li>
                                    <li>P.O. Box 1399, Matsapha, Eswatini</li>
                                    <li>Tel: +268 2518 4610</li>
                                    <li>Fax: +268 2518 4526</li>
                                    <li>Email: privacy@eswasa.co.sz</li>
                                </ul>
                            </div>

                            <div class="highlight-box">
                                <p><strong>Complaints:</strong> If you are not satisfied with how we handle your personal information, you have the right to lodge a complaint with the Eswatini Data Protection Authority.</p>
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
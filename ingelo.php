<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Ingelo Certification - ESWASA</title>
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
    
    <style>
        .btn-cert {
            background-color: #2E3191;
            color: white;
            border-color: #2E3191;
            margin: 5px;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .btn-cert:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 49, 145, 0.15);
        }
        /* Enlarged primary action buttons */
        .btn-ingelo-action {
            font-size: 1.3rem !important; /* Smaller than before but still prominent */
            padding: 20px 40px !important; /* Reduced padding */
            margin: 0 15px 15px !important; /* Reduced margins */
            font-weight: 700;
            border-radius: 6px !important; /* Standard border-radius */
            box-shadow: 0 4px 12px rgba(46, 49, 145, 0.2) !important; /* Reduced shadow */
            text-transform: uppercase; /* Make text uppercase */
            letter-spacing: 0.5px; /* Reduced letter spacing */
            min-width: 280px; /* Reduced minimum width */
            transition: all 0.3s ease !important; /* Smooth transition */
        }
        .btn-ingelo-action:hover {
            background-color: #1a1f71 !important;
            border-color: #1a1f71 !important;
            color: white !important;
            transform: translateY(-2px) !important; /* Reduced lift */
            box-shadow: 0 6px 16px rgba(46, 49, 145, 0.25) !important; /* Reduced shadow */
        }
        /* Clean highlight — no icons, no blue border */
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
        .ingelo-process-steps {
            list-style-type: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .ingelo-process-steps li {
            counter-increment: step-counter;
            margin-bottom: 22px;
            padding-left: 45px;
            position: relative;
        }
        .ingelo-process-steps li::before {
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
        .ingelo-application-form {
            background-color: #f0f8ff;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
            border: 1px solid #d0d9f0;
        }
        .ingelo-application-form h3 {
            color: #2E3191;
            font-weight: 700;
            font-size: 1.4rem;
        }

        @media (max-width: 768px) {
            .highlighted-section, .ingelo-application-form {
                padding: 20px 15px;
            }
            .ingelo-process-steps li {
                padding-left: 40px;
            }
            .ingelo-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
            }
            .btn-ingelo-action {
                font-size: 1.1rem !important;
                padding: 16px 25px !important;
                margin: 8px auto !important;
                width: 90%;
                min-width: auto;
                display: block;
            }
        }
        
        /* Additional styling for benefits section */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .benefit-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            /* Removed blue lining - using subtle border instead */
            border: 1px solid #e0e0e0;
        }
        
        .benefit-card h4 {
            color: #2E3191;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .standards-list {
            columns: 2;
            column-gap: 30px;
            margin: 20px 0;
        }
        
        .standards-list li {
            margin-bottom: 8px;
        }
        
        /* Make image closer to text */
        .img-close-to-text {
            margin-right: 15px;
        }
        
        /* Improved first section styling */
        .intro-section {
            background: linear-gradient(135deg, #f8f9fd 0%, #ffffff 100%);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(46, 49, 145, 0.08);
            margin-bottom: 30px;
        }
        
        .intro-section h2 {
            color: #2E3191;
            font-weight: 700;
            font-size: 1.6rem; /* Smaller on mobile */
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .intro-section p {
            font-size: 1rem; /* Smaller on mobile */
            line-height: 1.6;
            color: #333;
        }
        
        /* Action buttons section */
        .action-buttons-section {
            background-color: #f8f9fd;
            padding: 40px 20px;
            border-radius: 8px;
            margin: 40px 0;
            text-align: center;
        }
        
        .action-buttons-section h3 {
            color: #2E3191;
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 25px;
        }
        
        .action-buttons-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .standards-list {
                columns: 1;
            }
            .intro-section h2 {
                font-size: 1.4rem;
            }
            .intro-section {
                padding: 20px 15px;
            }
            .benefits-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons-section {
                padding: 30px 15px;
                margin: 30px 0;
            }
            .action-buttons-container {
                flex-direction: column;
                gap: 15px;
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
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/Ingelo.png">
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
                                <span property="itemListElement" typeof="ListItem">Ingelo Certification Scheme</span>
                            </nav>
                            <h3 class="title">Ingelo Certification Scheme</h3>
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
                            <img src="assets/img/Ingelo.png" alt="Ingelo Quality Mark" class="img-fluid rounded img-close-to-text" style="object-fit: contain; min-width: 120px; max-width: 100%;">
                        </div>
                        
                        <!-- Content Column -->
                        <div class="col-lg-9 col-md-8">
                            <h2>Eswatini Standards Authority (ESWASA) Invites MSMEs to Participate</h2>
                            <p>The Eswatini Standards Authority (ESWASA) invites Micro, Small & Medium Enterprises (MSMEs) to apply for participation in the <strong>Ingelo Certification Scheme</strong> – a program developed to support local businesses in achieving product and system certification.</p>
                            <p>This initiative is designed to empower Emaswati entrepreneurs by providing them with the tools and recognition needed to compete effectively in both local and international markets.</p>
                        </div>
                    </div>
                </div>

                <!-- 2. What is the Ingelo Certification Scheme? -->
                <div class="highlighted-section">
                    <h3>What is the Ingelo Certification Scheme?</h3>
                    <p>The Ingelo Certification Scheme is a national initiative launched by the Ministry of Commerce, Industry and Trade to support local producers through:</p>
                    <ul>
                        <li><strong>System and Product Certification:</strong> Supporting cottage producers in achieving certification for both their production systems and end products.</li>
                        <li><strong>Market Requirements Compliance:</strong> Assisting producers of products and services in meeting market requirements on quality and safety based on established standards.</li>
                    </ul>
                </div>

                <!-- 3. Benefits of Participation -->
                <div class="highlighted-section">
                    <h3>Benefits of Participation</h3>
                    <div class="benefits-grid">
                        <div class="benefit-card">
                            <h4>Improved Product Quality & Safety</h4>
                            <p>Enhance your product quality and safety through standardized processes and compliance with national and international standards.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Market Access & Growth</h4>
                            <p>Gain access to local and regional markets including AfCFTA. Many markets mandate certifications as a condition for entry, reducing trade barriers and opening global opportunities.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Recognition Through ESWASA Approved Mark</h4>
                            <p>Display the trusted ESWASA Approved mark on your products, signaling compliance and quality to consumers and business partners.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Technical Support</h4>
                            <p>Receive technical guidance throughout the certification process from ESWASA experts to help you meet all requirements successfully.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Customer Trust & Brand Value</h4>
                            <p>Certifications provide independent validation, signaling reliability and safety to customers. They strengthen brand reputation and can differentiate SMEs from uncertified competitors.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Risk Management & Compliance</h4>
                            <p>Certifications help SMEs minimize legal and regulatory risks. They serve as documented proof of compliance in case of disputes, audits, or liability claims.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Financing & Investment Appeal</h4>
                            <p>Investors and lenders increasingly assess intangibles when evaluating SMEs. Certifications add credibility to business operations, boosting investor confidence and enhancing company valuation.</p>
                        </div>
                        <div class="benefit-card">
                            <h4>Building Long-Term Competitive Advantage</h4>
                            <p>Unlike machinery or stock, certifications don't depreciate overnight—they compound business credibility over time. When integrated into IP and brand strategy, certifications become part of the SME's unique intangible asset portfolio.</p>
                        </div>
                    </div>
                </div>

                <!-- 4. Who Can Apply? -->
                <div class="highlighted-section">
                    <h3>Who Can Apply?</h3>
                    <ul>
                        <li><strong>Emaswati (Swazi citizens)</strong> - Local entrepreneurs and business owners</li>
                        <li><strong>Local MSMEs</strong> involved in the production of any products or offering services</li>
                        <li><strong>Producers willing to scale up</strong> - Those who are willing to increase production to meet export quota requirements by local and regional markets, through compliance with certification requirements</li>
                    </ul>
                </div>

                <!-- 5. Available Standards -->
                <div class="highlighted-section">
                    <h3>Available Standards</h3>
                    <ul class="standards-list">
                        <li>SZNS 025: Poultry processing - Hygiene requirements</li>
                        <li>SZNS 058: Sweet potato - Grading requirements</li>
                        <li>SZNS 049: Maize grains - Specification</li>
                        <li>SZNS BOS 43: Onion - Grading requirements</li>
                        <li>SZNS 037: Organic fertilizer - Specification</li>
                        <li>SZNS 060: Banana - Grading Requirements</li>
                        <li>SZNS KS 052: Fresh Courgettes/Baby marrow - Specification and grading</li>
                        <li>SZNS 031: Cattle feeds - Specification</li>
                        <li>SZNS 035: Peanut butter - Specification</li>
                        <li>SZNS SANS 1199: Production of Mageu</li>
                        <li>SZNS SANS 1679: Pasteurised Milk</li>
                        <li>SZNS CODEX STAN 296: Jam</li>
                        <li>SZNS CODEX STAN 306: Chilli Sauce</li>
                    </ul>
                </div>

                <!-- 6. Application Form -->
                <div class="ingelo-application-form">
                    <h3>How to Apply</h3>
                    <p>To begin the Ingelo Certification process, please:</p>
                    <ol>
                        <li>Download and complete the official application form</li>
                        
                        <li>Submit via email to <strong>certification@eswasa.org.sz</strong> or in person at ESWASA offices, Matsapha</li>
                    </ol>
                    <div class="mt-3">
                        <a href="admin/uploads/ingelo_application_form.pdf" class="btn btn-cert" target="_blank">
                            Download Application Form (PDF)
                        </a>
                        
                    </div>
                    <p class="mt-3 small">
                        <strong>Support available:</strong> ESWASA offers free pre-application consultations and gap-analysis workshops for MSMEs. Contact us to schedule.
                    </p>
                </div>

                <!-- Action Buttons Section (Properly sized and mobile-friendly) -->
                <div class="action-buttons-section">
                    <h3>Ready to Get Started?</h3>
                    <div class="action-buttons-container">
                        <a href="qoute_certification.php" class="btn btn-cert btn-ingelo-action">
                            Request Certification Quote
                        </a>
                        <a href="contact.php" class="btn btn-cert btn-ingelo-action">
                            Speak to an Ingelo Officer
                        </a>
                    </div>
                </div>

                <div class="alert alert-success text-center" role="alert">
                    <strong>Did you know?</strong> Products with the Ingelo Mark are prioritised for procurement by government institutions and featured in ESWASA’s “Quality Local Products” directory.
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
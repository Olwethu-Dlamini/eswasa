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
    <title>Management Systems Certification - ESWASA</title>
    <meta name="description" content="Accredited certification of ISO and food safety management systems by the Eswatini Standards Authority (ESWASA). SANAS-accredited, SADC-recognised.">
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
            padding: 60px 0;
        }
        .section-title {
            color: #2e3191;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .cert-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }
        .cert-card h3 {
            color: #2e3191;
            margin-bottom: 15px;
        }
        .btn-cert {
            background: #2e3191;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            font-weight: 600;
        }
        .btn-cert:hover {
            background: #1a1f71;
            color: white;
        }
        .scheme-item {
            background: #f9f9f9;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .scheme-item h5 {
            color: #2e3191;
            margin-bottom: 10px;
        }
        .process-step {
            padding: 20px;
            text-align: center;
        }
        .step-number {
            background: #2e3191;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: 600;
        }
        .standards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        .standard-block {
            background: white;
            padding: 25px;
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .standard-block:hover {
            box-shadow: 0 4px 12px rgba(46, 49, 145, 0.08);
            border-color: #2e3191;
        }
        .standard-code {
            color: #2e3191;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .standard-name {
            font-weight: 600;
            margin-bottom: 12px;
            color: #111;
        }

        @media (max-width: 768px) {
            .standards-grid {
                grid-template-columns: 1fr;
            }
        }


        .cert-process-section .process-row {
    margin-bottom: 40px;
}

.process-circle {
    background: #1d2d70;
    color: #fff;
    width: 165px;
    height: 165px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 15px;
    font-weight: 700;
    font-size: 15px;
    line-height: 1.3;
    margin: 10px;
}

.process-circle span {
    font-weight: 400;
    font-size: 13px;
}

.process-circle.highlight {
    background: #f0b835;
    color: #000;
    border: 3px solid #e6a900;
    font-weight: 700;
}

.process-arrow {
    font-size: 40px;
    font-weight: 700;
    margin: 0 10px;
    color: #d19d27;
}

.process-divider {
    width: 80%;
    height: 4px;
    background: #d9d9d9;
    margin: 20px auto;
    border-radius: 10px;
}

@media (max-width: 768px) {
    .process-circle {
        width: 140px;
        height: 140px;
    }
    .process-arrow {
        display: none;
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
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
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
                                <span property="itemListElement" typeof="ListItem">Management Systems</span>
                            </nav>
                            <h3 class="title">Management Systems Certification</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Introduction -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="cert-card">
                            <h2 class="section-title">Management Systems Certification</h2>
                            <p>
                                The core business of the ESWASA Certification department is the provision of an independent, third-party conformity assessment service for systems and products, in accordance with requirements of ISO/IEC 17021 for management systems certification and ISO/IEC 17065 for product certification.
                            </p>
                           
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Available Standards -->
        <section class="cert-section bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="section-title text-center">Certification Schemes Offered</h2>
                        <p class="text-center mb-4">Eswatini Standards Authority Management Systems Certification Services is accredited by the Southern African Development Community Accreditation Service (SADCAS).</p>
                        <p class="text-center mb-4">Scopes: Quality Management Systems to ISO/IEC 17021-1:2015 and ISO/IEC 17021-3:2017 (Certification to ISO 9001:2015), IAF Codes 3, 12, 13 and 38</p>
                        <div class="standards-grid">
                            <div class="standard-block">
                                <div class="standard-code">SZNS ISO 9001:2015</div>
                                <div class="standard-name">Quality Management Systems</div>
                                <p>For organisations seeking to ensure consistent product/service quality and continual improvement.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS ISO 14001:2015</div>
                                <div class="standard-name">Environmental Management Systems</div>
                                <p>Supports compliance with Eswatini’s Environmental Management Act and promotes sustainable operations.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS ISO 45001:2018</div>
                                <div class="standard-name">Occupational Health & Safety Management Systems</div>
                                <p>Aligns with the Occupational Safety and Health Act, No. 5 of 2001 (as amended).</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS ISO 22000:2018</div>
                                <div class="standard-name">Food Safety Management Systems</div>
                                <p>Covers the full food chain—processing, handling, storage, and distribution.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS SANS 10330:2020</div>
                                <div class="standard-name">Hazard Analysis and Critical Control Points</div>
                                <p>Requirements for a Hazard Analysis and Critical Control Point (HACCP) system</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS SANS 542:2020</div>
                                <div class="standard-name">Concrete Roofing Tiles</div>
                                <p>Certification for concrete roofing tiles.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS CODEXSTAN 306:2015</div>
                                <div class="standard-name">Chilli Sauce Specifications</div>
                                <p>Certification for chilli sauce specifications.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS CODEXSTAN 12:1981</div>
                                <div class="standard-name">Honey</div>
                                <p>Certification for honey.</p>
                            </div>
                            <div class="standard-block">
                                <div class="standard-code">SZNS 006:2011</div>
                                <div class="standard-name">Liquid Soap Specification</div>
                                <p>Certification for liquid soap specification.</p>
                            </div>
                        </div>
                        <p class="text-center mt-3">More certifications can be added to the portfolio informed by interest indicated by clients.</p>
                    </div>
                </div>
            </div>
        </section>

<section class="cert-section py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Certify with ESWASA?</h2>
            <p class="section-subtitle">We provide reliable, efficient and results-driven certification services.</p>
        </div>

        <div class="row g-4">
            <!-- CARD 1 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Demonstrate Competence</h3>
                    <p>
                        To meet your goals, lower risk, and provide added value, we have skilled personnel from 
                        various business areas. ESWASA offers competent, experienced, and academically qualified experts.
                    </p>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Integrated Approach</h3>
                    <p>
                        We guide you on the most economical path to certification and provide integrated audits 
                        where practical for efficiency and consistency.
                    </p>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Prompt Support</h3>
                    <p>
                        We help your institution obtain certification quickly and without red tape. 
                        You receive fast, reliable communication and dedicated support.
                    </p>
                </div>
            </div>

            <!-- CARD 4 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Committed</h3>
                    <p>
                        We actively collaborate with your Quality Assurance team or external consultants, ensuring 
                        smooth preparation and successful certification.
                    </p>
                </div>
            </div>

            <!-- CARD 5 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Competitive Price</h3>
                    <p>
                        Our pricing is transparent and competitive. No annual fees, no hidden charges—just clear, 
                        straightforward professional service.
                    </p>
                </div>
            </div>

            <!-- ORIGINAL CARD 6 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Local Expertise, Global Standards</h3>
                    <p>
                        We combine international accreditation (ISO/IEC 17021) with deep local industry knowledge 
                        across agriculture, manufacturing, and public services.
                    </p>
                </div>
            </div>

            <!-- ORIGINAL CARD 7 -->
            <div class="col-lg-6">
                <div class="cert-card">
                    <h3>Continual Support</h3>
                    <p>
                        Beyond certification, we provide support with implementation, gap analysis, and 
                        integration with national quality initiatives.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- Certification Process -->
<section class="cert-process-section py-5 bg-light">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">How Certification Works</h2>
          
        </div>

        <!-- ROW 1 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 1<br><span>Initial Enquiry</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 2<br><span>Promotional Visit & Application</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 3<br><span>Quote Provided, Contract & Payment Commitment</span></div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 2 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 4<br><span>Stage 1 Initial Audit / Factory Evaluation & Sampling</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 5<br><span>Stage 2 Initial Audit / Product Testing</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle highlight">Certification<br>Decision</div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 3 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 6<br><span>Issue of Certificate</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 7<br><span>2 Surveillance Audits</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 8<br><span>Recertification Audit</span></div>
        </div>

    </div>
</section>



        <!-- Get Started -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="section-title">Begin Your Certification</h2>
                        <p class="mb-4">Submit an application or request a preliminary consultation with our certification team.</p>
                        <a href="qoute_certification.php" class="btn-cert">Submit Application</a>
                        <a href="contact.php" class="btn-cert">Speak to an Auditor</a>
                        <a href="training-about.php" class="btn-cert">Attend Implementation Training</a>
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
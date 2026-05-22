<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Our Services - ESWASA</title>
    <meta name="description" content="Explore the range of services offered by the Eswatini Standards and Quality Assurance Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

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
        .text-muted {
            color: rgba(43, 51, 136, 0.7) !important;
        }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        .breadcrumb-separator i { color: #fff !important; }

        /* Section divider — matches index/about-us/meetourteam */
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }

        /* Services grid cards — institutional restraint */
        .service-card {
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
            border: 1px solid rgba(43, 51, 136, 0.12) !important;
            border-radius: 4px !important;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.06);
        }
        .service-card:hover {
            transform: translateY(-3px);
            border-color: rgba(43, 51, 136, 0.40) !important;
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.12);
        }
        .service-icon {
            width: 68px;
            height: 68px;
            background: #2B3388;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .25s ease, background-color .25s ease;
            margin: 0 auto 20px auto;
        }
        .service-icon svg {
            width: 36px;
            height: 36px;
            display: block;
        }
        .service-card:hover .service-icon {
            transform: scale(1.08);
        }
        .service-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: #2B3388;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        .service-description {
            color: rgba(43, 51, 136, 0.78);
            font-size: 14px;
            line-height: 1.55;
            margin: 0;
        }

        /* Info sections — Information Centre / WTO Enquiry Point */
        .info-section {
            background-color: rgba(43, 51, 136, 0.04);
            padding: 32px 28px;
            margin: 30px 0;
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.10);
        }
        .info-section h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 18px;
            margin: 0 0 14px;
        }
        .info-section p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
            margin: 0 0 10px;
        }
        .info-section p:last-child { margin-bottom: 0; }

        /* Affiliations section background */
        .bg_color3 {
            background-color: rgba(43, 51, 136, 0.04);
        }

        .affiliations-slider {
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .affiliations-slider::-webkit-scrollbar {
            display: none;
        }
        .slider-track {
            animation: scroll 20s linear infinite;
            min-width: 100%;
        }
        .slider-item {
            flex: 0 0 auto;
            width: 200px;
            text-align: center;
        }
        .affiliation-logo {
            width: 150px;
            height: 100px;
            object-fit: contain;
            transition: transform .25s ease, box-shadow .25s ease;
            background-color: #fff;
            padding: 15px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.08);
            border: 1px solid rgba(43, 51, 136, 0.10);
            display: block;
        }
        .slider-item:hover .affiliation-logo {
            transform: scale(1.06);
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.16);
            border-color: rgba(43, 51, 136, 0.30);
        }
        .slider-item a {
            display: block;
            text-decoration: none;
        }
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .affiliations-slider:hover .slider-track {
            animation-play-state: paused;
            -webkit-animation-play-state: paused;
        }

        /* Section heading hierarchy */
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .display-6 { font-size: 1.9rem !important; }
        }
        @media (max-width: 767.98px) {
            .service-card { padding: 1.5rem !important; }
            .service-title { font-size: 1.05rem; }
            .service-description { font-size: 13px; }
            .info-section { padding: 24px 20px; }
            .info-section h3 { font-size: 16px; }
            .info-section p { font-size: 14px; }
            .display-6 { font-size: 1.55rem !important; }
            .affiliation-logo { width: 130px; height: 90px; padding: 12px; }
            .slider-item { width: 170px; }
        }
        @media (max-width: 575.98px) {
            .info-section { padding: 20px 16px; }
            .affiliation-logo { width: 110px; height: 80px; padding: 10px; }
            .slider-item { width: 140px; }
        }
    </style>
</head>

<body>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('services', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Our Services</span>
                            </nav>
                            <h1 class="title">Our Services</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container icons_spacer py-5">

            <!-- Services Grid -->
            <div>
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold">Our Services</h2>
                    <div class="section-divider"></div>
                    <p class="text-muted mt-3 mb-0">Empowering Excellence Through Standards</p>
                </div>

                <!-- Services Grid – FULL CARD CLICKABLE -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                    <!-- Certification -->
                    <div class="col">
                        <a href="Certification.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M24 4 L42 11 V24 C42 34 34 42 24 44 C14 42 6 34 6 24 V11 Z"/>
                                        <path d="M16 24 L22 30 L33 18"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Certification</h3>
                                <p class="service-description">Certification to management systems and products. Let us assist you in demonstrating your organisation's ability to meet requirements and needs.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Product Testing -->
                    <div class="col">
                        <a href="product.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 6 H30"/>
                                        <path d="M20 6 V20 L10 38 C8.5 41 10.5 44 13.5 44 H34.5 C37.5 44 39.5 41 38 38 L28 20 V6"/>
                                        <path d="M14 32 H34"/>
                                        <circle cx="20" cy="36" r="1.4" fill="currentColor"/>
                                        <circle cx="27" cy="38" r="1.4" fill="currentColor"/>
                                        <circle cx="24" cy="33" r="1.2" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Product Testing</h3>
                                <p class="service-description">Food and product testing in microbiology. Testing performed in accordance with international standards.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Calibration Services -->
                    <div class="col">
                        <a href="Calibration.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 34 A 18 18 0 0 1 42 34"/>
                                        <line x1="6" y1="34" x2="10" y2="34"/>
                                        <line x1="42" y1="34" x2="38" y2="34"/>
                                        <line x1="24" y1="16" x2="24" y2="20"/>
                                        <line x1="13" y1="22" x2="16" y2="24"/>
                                        <line x1="35" y1="22" x2="32" y2="24"/>
                                        <line x1="24" y1="34" x2="33" y2="22"/>
                                        <circle cx="24" cy="34" r="2.6" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Calibration Services</h3>
                                <p class="service-description">Calibration services based on accuracy, trust, and consistency.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Standards Development -->
                    <div class="col">
                        <a href="Standards.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                        <path d="M28 6 V16 H38"/>
                                        <line x1="16" y1="26" x2="32" y2="26"/>
                                        <line x1="16" y1="32" x2="32" y2="32"/>
                                        <line x1="16" y1="38" x2="26" y2="38"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Standards Development</h3>
                                <p class="service-description">Bringing together different expertise and experiences to develop mutually accepted solutions to common challenges.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Standards Sales -->
                    <div class="col">
                        <a href="Standards.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="24" y1="10" x2="24" y2="40"/>
                                        <path d="M24 10 C 18 7, 12 7, 8 9 L 8 38 C 12 36, 18 36, 24 39"/>
                                        <path d="M24 10 C 30 7, 36 7, 40 9 L 40 38 C 36 36, 30 36, 24 39"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Standards Sales</h3>
                                <p class="service-description">Sale of national, regional and international standards.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Training Academy -->
                    <div class="col">
                        <a href="training-about.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 20 L24 10 L44 20 L24 30 Z"/>
                                        <path d="M12 24 V34 C12 36.5 17 40 24 40 C31 40 36 36.5 36 34 V24"/>
                                        <path d="M44 20 V32"/>
                                        <circle cx="44" cy="34" r="1.6" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="service-title">Training Academy</h3>
                                <p class="service-description">We enable organisations and individuals to continuously improve, innovate and transform.</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

            <!-- Information Centre Section -->
            <div class="info-section mt-5">
                <h3>Information Centre / WTO Enquiry Point</h3>
                <p>The ESWASA National Enquiry Point holds the collection of national, regional and international standards. We welcome students, researchers, industry professionals and the general public to make use of our centre. The enquiry point is a channel for mitigating problems businesses face in obtaining information on standards, technical regulations and conformity assessment procedures for the access of their products in international markets, thus eliminating technical barriers to trade.</p>
            </div>

            <!-- Information Centre Details Section -->
            <div class="info-section mt-4">
                <h3>The National Enquiry Point</h3>
                <p>The World Trade Organization (WTO) Agreement on Technical Barriers to Trade (the TBT Agreement) requires WTO members to establish a National Enquiry Point (NEP) as a way of mitigating problems business enterprises face in obtaining information on technical regulations, standards and conformity assessment procedures applicable to their products in international markets. The Eswatini Standards Authority (ESWASA) is the TBT National Enquiry Point for Eswatini.</p>
            </div>

        </div>

        <!-- FIXED Affiliations Section -->
        <section class="bg_color3 py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold">Our Affiliations</h2>
                    <div class="section-divider"></div>
                    <p class="text-muted mt-3 mb-0">Partnering for Excellence</p>
                </div>

                <!-- Horizontal Slider -->
                <div class="affiliations-slider position-relative overflow-hidden">
                    <div class="slider-track d-flex flex-nowrap">
                        <div class="slider-item px-3">
                            <a href="https://www.itu.int/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/itu.png" alt="ITU" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.iso.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/iso.png" alt="ISO" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.iec.ch/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/iec.png" alt="IEC" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.sabs.co.za/" target="_blank" rel="noopener noreferrer">
                                <img src="assets/img/SABS.png" alt="SABS" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.arso-org.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/arso.png" alt="ARSO" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.astm.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/astm.png" alt="ASTM" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <!-- Duplicate items for infinite scroll effect -->
                        <div class="slider-item px-3">
                            <a href="https://www.itu.int/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/itu.png" alt="ITU" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.iso.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/iso.png" alt="ISO" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.iec.ch/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/iec.png" alt="IEC" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.sabs.co.za/" target="_blank" rel="noopener noreferrer">
                                <img src="assets/img/SABS.png" alt="SABS" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.arso-org.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/arso.png" alt="ARSO" class="img-fluid affiliation-logo">
                            </a>
                        </div>
                        <div class="slider-item px-3">
                            <a href="https://www.astm.org/" target="_blank" rel="noopener noreferrer">
                                <img src="admin/uploads/astm.png" alt="ASTM" class="img-fluid affiliation-logo">
                            </a>
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
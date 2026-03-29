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
        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .service-icon {
            width: 60px;
            height: 60px;
            line-height: 60px;
            background: #2E3191;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
            margin: 0 auto 20px auto;
        }
        .service-card:hover .service-icon {
            transform: scale(1.2);
        }
        .service-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }
        .service-description {
            color: #666;
            font-size: 0.9rem;
        }
        .info-section {
            background-color: #f9f9f9;
            padding: 40px 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .info-section h3 {
            color: #2E3191;
            margin-bottom: 15px;
        }
        .info-section p {
            margin-bottom: 10px;
        }

        /* Fixed Affiliations Styling */
        .bg_color3 {
            background-color: #e6f0fa;
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
            transition: transform 0.3s ease, filter 0.3s ease;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: block;
        }
        
        .slider-item:hover .affiliation-logo {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('assets/img/bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Our Services</span>
                            </nav>
                            <h3 class="title">Our Services</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container icons_spacer py-5">

            <!-- Services Grid -->
            <div class="container icons_spacer py-5">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5">
                    <h2 class="display-6 fw-bold text-center">
                        Our Services
                        <span class="d-block fs-5 text-muted mt-2">Empowering Excellence Through Standards</span>
                        <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
                    </h2>
                </div>

                <!-- Services Grid – FULL CARD CLICKABLE -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                    <!-- Certification -->
                    <div class="col">
                        <a href="Certification.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-certificate fa-2x"></i></span>
                                <h3 class="service-title">Certification</h3>
                                <p class="service-description">Certification to Management Systems and products. Let us assist you in demonstrating your organization's ability to meet requirements and needs.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Product Testing -->
                    <div class="col">
                        <a href="product.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-vial fa-2x"></i></span>
                                <h3 class="service-title">Product Testing</h3>
                                <p class="service-description">Food and product testing in microbiology. Testing performed in accordance to international standards.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Calibration Services -->
                    <div class="col">
                        <a href="Calibration.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-weight-hanging fa-2x"></i></span>
                                <h3 class="service-title">Calibration Services</h3>
                                <p class="service-description">Calibration services based on accuracy, trust, and consistency.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Standards Development -->
                    <div class="col">
                        <a href="Standards.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-file-contract fa-2x"></i></span>
                                <h3 class="service-title">Standards Development</h3>
                                <p class="service-description">Bringing together different expertise and experiences to develop mutually accepted solutions to common challenges.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Standards Sales -->
                    <div class="col">
                        <a href="Standards.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-book fa-2x"></i></span>
                                <h3 class="service-title">Standards Sales</h3>
                                <p class="service-description">Sale of National, Regional, and International Standards.</p>
                            </div>
                        </a>
                    </div>

                    <!-- Training Academy -->
                    <div class="col">
                        <a href="training-about.php" class="text-decoration-none">
                            <div class="card service-card border-0 shadow-sm rounded-3 p-4 h-100">
                                <span class="service-icon"><i class="fas fa-graduation-cap fa-2x"></i></span>
                                <h3 class="service-title">Training Academy</h3>
                                <p class="service-description">We enable organizations and individuals to continuously improve, innovate, and transform.</p>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

            <!-- Information Centre Section -->
            <div class="info-section mt-5">
                <h3>Information Centre / WTO Enquiry Point</h3>
                <p>The ESWASA National Enquiry Point holds the collection of national, regional and international standards. We welcome Students, Researchers, Industry professionals and the general public to make use of our center. The enquiry point is a channel for mitigating problems businesses face in obtaining information on standards, technical regulations, and conformity assessment procedures for the access of their products in international markets thus eliminating Technical Barriers to Trade.</p>
            </div>

            <!-- Information Centre Details Section -->
            <div class="info-section mt-4">
                <h3>The National Enquiry Point</h3>
                <p>The World Trade Organization (WTO) Agreement on Technical Barriers to Trade (The TBT Agreement) requires WTO members to establish a National Enquiry Point (NEP) as a way of mitigating problems business enterprises face in obtaining information on technical regulations, standards and conformity assessment procedures applicable to their products in international markets. The Eswatini Standards Authority (ESWASA) is the TBT National Enquiry Point for Eswatini.</p>
            </div>

        </div>

        <!-- FIXED Affiliations Section -->
        <section class="bg_color3 py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold">
                        Our Affiliations
                    </h2>
                    <p class="fs-5 text-muted mt-2">Partnering for Excellence</p>
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
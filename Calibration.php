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
    <title>Scales and Metrology Services - ESWASA</title>
    <meta name="description" content="ESWASA's Legal Metrology services: verification and calibration of weighing and measuring instruments in Eswatini.">
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
        .btn-service {
            background-color: #2E3191;
            color: white;
            border-color: #2E3191;
            margin: 5px;
            font-weight: 600;
            padding: 10px 22px;
        }
        .btn-service:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            color: white;
        }
        /* Enlarged action buttons */
        .btn-metrology-action {
            font-size: 1.25rem !important;
            padding: 18px 50px !important;
            margin: 0 15px 15px !important;
            font-weight: 700 !important;
            display: inline-block;
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
        .service-process-steps {
            list-style-type: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .service-process-steps li {
            counter-increment: step-counter;
            margin-bottom: 22px;
            padding-left: 45px;
            position: relative;
        }
        .service-process-steps li::before {
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
        .metrology-category {
            text-align: center;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
        .metrology-category h4 {
            color: #2E3191;
            font-weight: 600;
        }
        .metrology-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .metrology-info-table th,
        .metrology-info-table td {
            border: 1px solid #dee2e6;
            padding: 14px;
            text-align: left;
        }
        .metrology-info-table th {
            background-color: #2E3191;
            color: white;
            font-weight: 600;
        }
        .metrology-info-table tr:nth-child(even) {
            background-color: #fbfcff;
        }

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            .service-process-steps li {
                padding-left: 40px;
            }
            .service-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
            }
            .btn-metrology-action {
                font-size: 1.15rem !important;
                padding: 16px 40px !important;
                display: block;
                margin: 10px auto !important;
                width: auto;
            }
        }


        .cert-section {
    padding: 60px 0;
}

.section-title {
    color: #2e3191;
    margin-bottom: 30px;
    font-weight: 700;
    text-align: center;
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
    transition: background 0.3s ease;
}

.btn-cert:hover {
    background: #1a1f71;
    color: white;
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('calibration', 'assets/img/bg/calibrationbg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Services</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Scales & Metrology</span>
                            </nav>
                            <h3 class="title">Scales & Metrology Services</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- 1. Introduction & Commitment -->
                <div class="highlighted-section">
                    <h3>About Us</h3>
                    <p>
                        We understand the importance of accurate and reliable measurements for your business. That is why we strive to provide the best calibration services available.
                    </p>
                    <p>
                        Our team of highly skilled technicians provide you with exceptional scale sales, service, repairs, installations, and calibration of all weighing equipment, weighbridges, and metrology equipment.
                    </p>
                    <p>
                        We utilise only the finest industry-standard equipment and procedures to ensure that all your measuring and testing instruments consistently function at their best, remaining accurate and reliable.
                    </p>
                </div>

                <!-- 2. Our Products and Services -->
                <div class="highlighted-section">
                    <h3>Our Products and Services Include:</h3>
                    <ul>
                        <li><strong>Scale Sales</strong>: Supplying high-quality scales, hoppers, and weighbridges for any application.</li>
                        <li><strong>Servicing and Repairs</strong>: Expert maintenance and repair for scales, hoppers, and weighbridges to extend their lifespan and ensure performance.</li>
                        <li><strong>In-house and On-site Calibration</strong>: Comprehensive calibration services for all types of weighing, temperature, and pressure instruments, performed at our Matsapha laboratory or at your premises.</li>
                        <li><strong>Preventative Maintenance Programmes</strong>: Scheduled maintenance to prevent failures and ensure continuous accuracy.</li>
                        <li><strong>Installation</strong>: Professional installation of scales, hoppers, and weighbridges to guarantee optimal setup and performance from day one.</li>
                    </ul>
                </div>

                <!-- 3. What is Calibration? -->
                <div class="highlighted-section">
                    <h3>What is Calibration?</h3>
                    <p>
                        Calibration is the process of checking, by comparison with a standard, the accuracy of measuring instruments of any type, such as a pressure gauge or an industrial thermometer. It may also include adjustments to the instrument to match the standard.
                    </p>
                    <p>
                        Calibration of your temperature and pressure instruments has two objectives. It checks the accuracy of the instrument, and it determines the traceability of the measurement. In practice, calibration may also include repair of the device if it is out of calibration. A report is provided by the calibration technician, which shows the error in measurements with the measuring device before and after the calibration.
                    </p>
                </div>

                <!-- 4. Purpose of Calibration -->
                <div class="highlighted-section">
                    <h3>Purpose of Calibration</h3>
                    <p>
                        Correct and reliable measurements are prerequisites for all high-quality industrial production.
                    </p>
                    <ul>
                        <li>To ensure Measurement Accuracy</li>
                        <li>To comply with Industrial Standards</li>
                        <li>To ensure Equipment Traceability</li>
                        <li>To improve Instrument Reliability</li>
                        <li>To enhance Product Quality and Safety</li>
                        <li>To reduce Downtime</li>
                    </ul>
                </div>

               <!-- 5. Brands We Supply and Service -->
<div class="highlighted-section">
    <h3>We Also Supply and Service the Following Brands:</h3>
    <div class="row g-4 justify-content-center">
        <!-- Row 1 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/lmi.PNG" alt="LMI" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/mass.PNG" alt="Massamatic" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/ishida.PNG" alt="Ishida" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/zemic.PNG" alt="Zemic" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>

        <!-- Row 2 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/avery.PNG" alt="Avery Weigh-Tronix" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/trek.PNG" alt="Trek" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/syslec.PNG" alt="Systec" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/shekel.PNG" alt="Shekel" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>

        <!-- Row 3 -->
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/laumus.PNG" alt="Laumas" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/adam.PNG" alt="Adam Equipment" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/mattler.PNG" alt="Mettler Toledo" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
        <div class="col-6 col-md-3 d-flex justify-content-center align-items-center">
            <img src="assets/img/brand/digi.PNG" alt="Digi" class="img-fluid" style="max-height: 80px; object-fit: contain;">
        </div>
    </div>
</div>

                

              
        <!-- Action Buttons (ENLARGED) -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="section-title">Get Calibrations</h2>
                        <p class="mb-4">Submit an application or request a preliminary consultation with our calibration team.</p>
                        <a href="qoute_calibration.php" class="btn-cert">Submit Application</a>
                        <a href="contactcalibration.php" class="btn-cert">Contact Metrology Unit</a>
                       
                    </div>
                </div>
            </div>
        </section>
                

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
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
    <title>Product Certification - ESWASA</title>
    <meta name="description" content="ESWASA Product Certification: Compulsory and voluntary certification of goods against SANS, ISO and regional standards. SANAS-accredited.">
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
        }
        .btn-cert:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            color: white;
        }
        /* Removed blue left border — now clean highlight */
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
        }
        .highlighted-section h3 i {
            margin-right: 8px;
        }
        .prod-process-steps {
            list-style-type: none;
            padding: 0;
            counter-reset: step-counter;
        }
        .prod-process-steps li {
            counter-increment: step-counter;
            margin-bottom: 22px;
            padding-left: 45px;
            position: relative;
        }
        .prod-process-steps li::before {
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
        .prod-schemes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .prod-schemes-table, .prod-schemes-table th, .prod-schemes-table td {
            border: 1px solid #dee2e6;
        }
        .prod-schemes-table th, .prod-schemes-table td {
            padding: 14px;
            text-align: left;
        }
        .prod-schemes-table th {
            background-color: #2E3191;
            color: white;
            font-weight: 600;
        }
        .prod-schemes-table tr:nth-child(even) {
            background-color: #fbfcff;
        }
        .prod-schemes-table tr:hover {
            background-color: #f0f3fb;
        }

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            .prod-process-steps li {
                padding-left: 40px;
            }
            .prod-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
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
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('product', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                                <span property="itemListElement" typeof="ListItem">Product Certification</span>
                            </nav>
                            <h3 class="title">Product Certification</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- 1. About Product Certification -->
                <div class="highlighted-section">
                    <h3>About Product Certification</h3>
                    <p>ESWASA implemented the ISO 17021 and 17065 Standards on our management systems and product certification schemes in order to provide trusted certification services and assurance that products and services meet customer expectations.</p>
                    <p>Product certification demonstrates commitment to safety, quality and performance standards set at an organizational, local or international level.</p>
            </div>

                

                <!-- 3. Certification Process -->
                <!-- Product Certification Process -->
<section class="py-5" style="background: #eaf1f5;">
    <div class="container">
        <h2 class="section-title mb-4">Product Certification Process</h2>
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="admin/uploads/image26.jpg" alt="Six-step certification process" class="img-fluid" style="border-radius: 8px;">
            </div>
            <div class="col-lg-7">
                <ol style="font-size: 0.95rem; color: #333; line-height: 2; padding-left: 20px;">
                    <li>Application/ Quote/ Planning and Scheduling</li>
                    <li>Initial Assessment (Process& Systems at Factory/ Plant)</li>
                    <li>Sampling& Testing of Product (sourcing of an Accredited lab)</li>
                    <li>Submission to Certification Approval Committee (CAC)</li>
                    <li>Awarding of Permit/ Certification for 3 years</li>
                    <li>Post permit inspection/ Audits& sampling/ Product testing</li>
                </ol>
                <div class="text-center mt-3">
                    <img src="assets/img/product.png" alt="SWASA Product Certification Mark" style="height: 100px;">
                </div>
            </div>
        </div>
    </div>
</section>

                <!-- Action Buttons -->
                <div class="text-center my-5">
                    <a href="qoute_certification.php" class="btn btn-cert btn-lg me-3">Submit Application</a>
                    <a href="contact.php" class="btn btn-cert btn-lg">Contact Certification Team</a>
                </div>

                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Need urgent certification for export or regulatory compliance?</strong> 
                    ESWASA offers expedited assessment for priority sectors. Enquire via certification@eswasa.org.sz
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
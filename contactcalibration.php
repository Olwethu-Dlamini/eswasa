<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">



<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Contact Calibration Services - Eswatini Standards Authority (ESWASA)</title>
    <meta name="description" content="Contact the ESWASA Metrology and Calibration laboratory at Matsapha Industrial Site for accredited calibration services and enquiries.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

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
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* ========== Contact info list ========== */
        .contact-info-wrap .title { color: #2B3388; }
        .contact-info-wrap > p { color: rgba(43, 51, 136, 0.75); margin-bottom: 24px; }
        .contact-info-wrap .list-wrap { list-style: none; padding: 0; margin: 0; }
        .contact-info-wrap .list-wrap li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .contact-info-wrap .list-wrap li:last-child { border-bottom: none; }
        .contact-info-wrap .list-wrap .icon {
            width: 38px;
            height: 38px;
            border-radius: 4px;
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2B3388;
            flex-shrink: 0;
        }
        .contact-info-wrap .list-wrap .content { color: #2B3388; }
        .contact-info-wrap .list-wrap .content p { color: #2B3388; margin: 0; line-height: 1.5; }
        .contact-info-wrap .list-wrap .content a {
            display: block;
            color: #2B3388;
            text-decoration: none;
            line-height: 1.6;
        }
        .contact-info-wrap .list-wrap .content a:hover { text-decoration: underline; }

        /* ========== Form inputs ========== */
        .form-control, input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select {
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            color: #2B3388;
            background: #fff;
        }
        .form-control:focus, input:focus, textarea:focus, select:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
            outline: none;
        }
        .form-label, label { color: #2B3388; font-weight: 600; }
        .form-grp input,
        .form-grp textarea {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 16px;
            font-family: Arial, sans-serif;
        }
        .form-grp textarea { min-height: 140px; }
        .form-grp input::placeholder,
        .form-grp textarea::placeholder { color: rgba(43, 51, 136, 0.6); }

        /* ========== Submit button ========== */
        .btn-contact-bg,
        input[type="submit"],
        button[type="submit"] {
            background-color: #2B3388 !important;
            color: #fff !important;
            border: 1px solid #2B3388 !important;
            border-radius: 4px !important;
            padding: 12px 32px;
            font-family: Arial, sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .btn-contact-bg:hover,
        input[type="submit"]:hover,
        button[type="submit"]:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            color: #fff !important;
            border-color: rgba(43, 51, 136, 0.85) !important;
        }
        .ajax-response { color: #2B3388; }

        /* ========== Mobile responsive ========== */
        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .contact-info-wrap .title { font-size: 1.4rem; }
            .contact-info-wrap .list-wrap li { padding: 12px 0; gap: 10px; }
            .contact-info-wrap .list-wrap .icon { width: 34px; height: 34px; }
            .col-lg-5 + .col-lg-7 { margin-top: 30px; }
            .btn-contact-bg,
            input[type="submit"] { width: 100%; padding: 12px 20px; }
            .contact-map iframe { height: 320px; }
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    <!--<div id="preloader">
        <div class="loadingio-spinner-bean-eater-qhqowfrhb1p">
          <div class="ldio-j4ty2hdtztp">
            <div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </div>
        </div>
    </div>-->
    <!-- Preloader-end -->



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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('contactcalibration', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Contact</span>
                            </nav>
                            <h3 class="title">Contact Us Calibration</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- contact-area -->
        <section class="contact-area section-py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="contact-info-wrap">
                            <h2 class="title">Get In Touch With Us</h2>
                            <p>Contact us anytime for support, we are always just 1 click away from you.</p>
                            <ul class="list-wrap">
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="content">
                                        <p>King Sobhuza II Avenue <br>
                                            Matsapha Crescent <br>
                                            Opposite YKK Zippers <br> 
                                            Matsapha Industrial Site <br>

                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="content">
                                        <a href="tel:+26825186633">(+268) 2518 6633</a>
                                        <a href="tel:+26825184610">(+268) 2518 4610</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <div class="content">
                                        <p>P.O. Box 1399 <br>
                                            Matsapha, Eswatini
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="far fa-envelope"></i>
                                    </div>
                                    <div class="content">
                                        <a href="mailto:pmkhwanazi@swasa.co.sz">	pmkhwanazi@swasa.co.sz </a>
                                        <a href="mailto:info@swasa.co.sz">	info@swasa.co.sz</a>
                                        <a href="http://www.swasa.co.sz">	www.swasa.co.sz</a>

                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-info-wrap">
                            <h4 class="title">Fill Up The Contact Form</h4>
                        </div>
                        <div class="contact-form-wrap">
                            <form id="contact-form" action="https://bazardeal.com.bd/biz/biztek-preview-3/biztek/form-process" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <input name="name" type="text" placeholder="Name *" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <input name="email" type="email" placeholder="E-mail *" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <input name="phone" type="number" placeholder="Phone *" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-grp">
                                            <input name="subject" type="text" value="Calibration Services" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-grp">
                                    <textarea name="message" placeholder="Message" required></textarea>
                                </div>
                                <div class="form-grp col-10 mx-auto text-center">
                                    <div class="actions">
                                        <input value="Submit Message" name="submit" id="submitButton" class="btn btn-contact-bg" title="Click here to submit your message!" type="submit">
                                        <img src="assets/img/ajax-loader.gif" id="loader" style="display:none" alt="loading" width="16" height="16">
                                    </div>
                                </div>
                            </form>
                            <p class="ajax-response mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- contact-area-end -->

        <!-- contact-map -->
        <div class="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d14282.718543167279!2d31.303588!3d-26.498258!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjbCsDI5JzUzLjciUyAzMcKwMTgnMTIuOSJF!5e0!3m2!1sen!2sus!4v1750623979471!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <!-- contact-map-end -->

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
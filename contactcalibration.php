<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">



<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ESWASA - Contact Us Calibration</title>
    <meta name="description" content="">
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
                                        <a href="tel:+268 25184610">+268 2518 6633 </a>
                                        <a href="tel:+268 25184526">+268 2518 4610</a>
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
                                            <input name="subject" type="text" placeholder="Calibration Services *" required>
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
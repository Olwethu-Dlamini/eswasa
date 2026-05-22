<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Members of the Council - ESWASA</title>
    <meta name="description" content="The ESWASA Members of the Council — strategic oversight and governance of the Eswatini Standards Authority.">

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

        /* Board cards — restrained DIN/BIS aesthetic */
        .board-member-card {
            border: 1px solid rgba(43, 51, 136, 0.15);
            background-color: #fff;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 16px;
            border-radius: 4px;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .board-member-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .board-img-container {
            position: relative;
            width: 100%;
            padding-top: 100%;
            overflow: hidden;
            border: 1px solid rgba(43, 51, 136, 0.15);
            margin-bottom: 15px;
        }
        .board-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .board-member-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .board-member-name {
            font-weight: 700;
            font-size: 1.15rem;
            color: #2B3388;
            margin-bottom: 0.25rem;
        }
        .board-member-role {
            font-weight: 600;
            color: rgba(43, 51, 136, 0.75);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        .board-member-bio {
            color: rgba(43, 51, 136, 0.8);
            font-size: 0.9rem;
            margin-bottom: 1rem;
            flex-grow: 1;
        }
        .board-member-social { margin-top: auto; }
        .social-icon {
            display: inline-block;
            width: 36px;
            height: 36px;
            line-height: 34px;
            text-align: center;
            background-color: #fff;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 50%;
            margin: 0 4px;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .social-icon:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }

        /* Section divider — flatten the bg-primary rounded-pill to a brand line */
        .bg-primary { background-color: #2B3388 !important; }

        @media (max-width: 767.98px) {
            .board-member-card { padding: 12px; }
            .board-member-name { font-size: 1.05rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('board', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Members of the Council</span>
                            </nav>
                            <h3 class="title">Members of the Council</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5">
                <h2 class="display-6 fw-bold text-center">
                    Members of the Council
                    <span class="d-block fs-5 text-muted mt-2">Strategic Oversight &amp; Governance</span>
                    <span class="section-divider d-block" style="margin-top: 14px;"></span>
                </h2>
                
            </div>

            <!-- Board Members Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                
                <!-- Board Member 1 -->
                <div class="col">
                    <div class="card board-member-card h-100 p-4">
                        <div class="board-img-container">
                            <img 
                                src="admin/uploads/dumile.png" 
                                alt="Mrs. Dumile Sibandze" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Mrs. Dumile Sibandze</h4>
                            <p class="board-member-role">Chairperson</p>
                            
                            <div class="board-member-social">
                             
                                
                            </div>
                        </div>
                    </div>
                </div>

             

                <!-- Board Member 3 -->
                <div class="col">
                    <div class="card board-member-card h-100 p-4">
                        <div class="board-img-container">
                            <img 
                                src="admin/uploads/cebile.jpg" 
                                alt="Ms. Cebile Nhlabatsi" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Ms. Cebile Nhlabatsi</h4>
                            <p class="board-member-role">Council Member</p>
                           
                            <div class="board-member-social">
                                
                           
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Board Member 4 -->
                <div class="col">
                    <div class="card board-member-card h-100 p-4">
                        <div class="board-img-container">
                            <img 
                                src="admin/uploads/Dladla.jpg" 
                                alt="Ms. Nompumelelo Dladla" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Ms. Nompumelelo Dladla</h4>
                            <p class="board-member-role">Council Member</p>
                            
                            <div class="board-member-social">
                               
                          
                            </div>
                        </div>
                    </div>
                </div>

        

                <!-- Board Member 6 -->
                <div class="col">
                    <div class="card board-member-card h-100 p-4">
                        <div class="board-img-container">
                            <img 
                                src="admin/uploads/Tania.jpg" 
                                alt="Ms. Tania Fyfe" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Ms. Tania Fyfe</h4>
                            <p class="board-member-role">Council Member</p>
                            
                            <div class="board-member-social">
                               
                               
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card board-member-card h-100 p-4">
                        <div class="board-img-container">
                            <img 
                                src="admin/uploads/sukati.png" 
                                alt="Ms. Sipholesihle Sukati" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Ms. Sipholesihle Sukati</h4>
                            <p class="board-member-role">Council Member</p>
                            
                            <div class="board-member-social">
                                
                               
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

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
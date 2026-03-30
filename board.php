<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Biztek - Corporate & Business Template</title>
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
    <style>
        /* Additional styles for the Board page */
        .board-member-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            height: 100%; /* Ensure card takes full height of grid item */
        }
        .board-member-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .board-img-container {
            position: relative;
            width: 100%; /* Fill card width */
            padding-top: 100%; /* Maintain 1:1 aspect ratio (square) */
            overflow: hidden; /* Crop the image */
            border: 5px solid #f0f0f0; /* Light border matching template style */
            margin-bottom: 15px; /* Space below image */
        }
        .board-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Crop the image to fill the container */
        }
        .board-member-info { /* New container for text content */
            flex: 1; /* Take up remaining space */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center text content */
            text-align: center; /* Center text alignment */
        }
        .board-member-name {
            font-weight: 700;
            font-size: 1.25rem;
            color: #333; /* Darker color for better contrast */
            margin-bottom: 0.25rem; /* Space below name */
        }
        .board-member-role {
            font-weight: 600;
            color: #2E3191; /* Primary template color */
            font-size: 1rem;
            margin-bottom: 0.5rem; /* Space below role */
        }
        .board-member-bio {
            color: #666; /* Muted color for bio */
            font-size: 0.9rem;
            margin-bottom: 1rem; /* Space below bio */
            flex-grow: 1; /* Push bio up but allow it to grow */
        }
        .board-member-social {
            margin-top: auto; /* Push social icons to the bottom */
        }
        .social-icon {
            display: inline-block;
            width: 36px;
            height: 36px;
            line-height: 36px;
            text-align: center;
            background-color: #f0f0f0;
            border-radius: 50%;
            margin: 0 5px;
            transition: background-color 0.3s ease;
        }
        .social-icon:hover {
            background-color: #2E3191; /* Primary template color */
            color: white;
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
                                <span property="itemListElement" typeof="ListItem">Board of Directors</span>
                            </nav>
                            <h3 class="title">Board of Directors</h3>
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
                    Board of Directors
                    <span class="d-block fs-5 text-muted mt-2">Strategic Oversight & Governance</span>
                    <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
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
                                alt="Ms Nompumelelo Dladla" 
                                class="board-img"
                            >
                        </div>
                        <div class="board-member-info">
                            <h4 class="board-member-name">Ms Nompumelelo Dladla</h4>
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
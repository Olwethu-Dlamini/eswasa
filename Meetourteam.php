<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ESWASA - Meet Our Team</title>
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
        /* Combined styles for the Meet Our Team page */
        .team-section {
            margin-bottom: 60px;
        }
        .team-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .team-layout {
            display: flex;
            align-items: flex-start;
            gap: 40px;
        }
        .team-leader {
            flex: 0 0 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .team-members {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        .team-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 15px;
        }
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .team-img-container {
            position: relative;
            overflow: hidden;
            border: 5px solid #f0f0f0;
            margin-bottom: 15px;
        }
        .team-leader .team-img-container {
            width: 250px;
            padding-top: 250px; /* Square aspect ratio */
        }
        .team-members .team-img-container {
            width: 150px;
            padding-top: 150px; /* Square aspect ratio */
        }
        .team-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .team-name {
            font-weight: 700;
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 0.25rem;
        }
        .team-role {
            font-weight: 600;
            color: #2E3191;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        .team-bio {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .team-social {
            margin-top: auto;
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
            background-color: #2E3191;
            color: white;
        }

        /* Section title separator */
        .section-title {
            text-align: center;
            margin: 50px 0 30px;
            font-weight: 700;
            color: #2E3191;
            position: relative;
        }
        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #2E3191;
            margin: 15px auto;
            border-radius: 2px;
        }

        /* Staff Photo Section */
        .staff-photo-container {
            width: 100%;
            max-width: 900px;
            height: 0;
            padding-bottom: 40%; /* 5:2 aspect ratio (e.g., 900×360) */
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        .staff-photo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        .staff-photo[src]:not([src=""]) {
            display: block !important;
        }
        .staff-photo-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-style: italic;
            font-size: 1rem;
            text-align: center;
            padding: 0 20px;
        }
        .staff-photo[src=""] + .staff-photo-placeholder {
            display: flex;
        }

        @media (max-width: 768px) {
            .team-layout {
                flex-direction: column;
                align-items: center;
            }
            .team-leader .team-img-container {
                width: 200px;
                padding-top: 200px;
            }
            .team-members .team-img-container {
                width: 120px;
                padding-top: 120px;
            }
            .staff-photo-container {
                padding-bottom: 50%; /* More vertical on mobile */
            }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('meetourteam', 'assets/img/bg/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Meet Our Team</span>
                            </nav>
                            <h3 class="title">Meet Our Team</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->
        <div class="container py-5">
            <!-- Main Header -->
            <div class="team-header">
                <h2 class="display-6 fw-bold">MEET OUR TEAM</h2>
                <h4>Meet our Board and Management</h4>
                <p class="text-muted">Meet our leadership team, dedicated to helping you achieve compliance, ensure quality, and promote the sustainability of our nation's industries.</p>
            </div>

            <!-- Board Section -->
            <div class="team-section">
                <h2 class="section-title">BOARD OF DIRECTORS</h2>
                <div class="team-layout">
                    <div class="team-leader">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/dumile.png" alt="Mrs. Dumile Sibandze" class="team-img">
                            </div>
                            <h4 class="team-name">Mrs. Dumile Sibandze</h4>
                            <p class="team-role">Board Chair</p>
                            <div class="team-social"></div>
                        </div>
                    </div>
                    <div class="team-members">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/cebile.jpg" alt="Ms. Cebile Nhlabatsi" class="team-img">
                            </div>
                            <h4 class="team-name">Ms. Cebile Nhlabatsi</h4>
                            <p class="team-role">Council Member</p>
                            <div class="team-social"></div>
                        </div>
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/Dladla.jpg" alt="Ms Nompumelelo Dladla" class="team-img">
                            </div>
                            <h4 class="team-name">Ms Nompumelelo Dladla</h4>
                            <p class="team-role">Council Member</p>
                            <div class="team-social"></div>
                        </div>
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/Tania.jpg" alt="Ms. Tania Fyfe" class="team-img">
                            </div>
                            <h4 class="team-name">Ms. Tania Fyfe</h4>
                            <p class="team-role">Council Member</p>
                            <div class="team-social"></div>
                        </div>
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/sukati.png" alt="Ms. Sipholesihle Sukati" class="team-img">
                            </div>
                            <h4 class="team-name">Ms. Sipholesihle Sukati</h4>
                            <p class="team-role">Council Member</p>
                            <div class="team-social"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Section -->
            <div class="team-section">
                <h2 class="section-title">MANAGEMENT TEAM</h2>
                <div class="team-layout">
                    <div class="team-leader">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/Ncamiso.jpg" alt="Mr Ncamiso K. Mhlanga" class="team-img">
                            </div>
                            <h4 class="team-name">Mr Ncamiso K. Mhlanga</h4>
                            <p class="team-role">Executive Director</p>
                            <div class="team-social"></div>
                        </div>
                    </div>
                    <div class="team-members">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/masina.jpg" alt="Ms Dumsile Masina" class="team-img">
                            </div>
                            <h4 class="team-name">Ms Dumsile Masina</h4>
                            <p class="team-role">CFO</p>
                            <div class="team-social"></div>
                        </div>
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/philip.jpg" alt="Mr Phillip G. Mndawe" class="team-img">
                            </div>
                            <h4 class="team-name">Mr Phillip G. Mndawe</h4>
                            <p class="team-role">Technical Manager</p>
                            <div class="team-social"></div>
                        </div>
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="admin/uploads/management/director_finance.jpg" alt="Vacant" class="team-img">
                            </div>
                            <h4 class="team-name">Vacant</h4>
                            <p class="team-role">Quality Assurance Manager</p>
                            <div class="team-social"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ESWASA STAFF Section (now at bottom) -->
            <div class="team-section">
                <h2 class="section-title">ESWASA STAFF</h2>
                <div class="eswasa-staff-content text-center mb-4">
                    <p>
                        The Eswatini Standards Authority (ESWASA) operates through a dedicated team of professionals committed to upholding national and international standards. Our staff spans disciplines in standardisation, metrology, testing, certification, and quality assurance—working collaboratively to support industry growth, consumer protection, and regional trade compliance.
                    </p>
                </div>

                <!-- Rectangular Group Photo -->
                <div class="text-center">
                    <div class="staff-photo-container mx-auto">
                        <img src="admin/uploads/staff_group_photo.jpg" alt="ESWASA Staff Group Photo" class="staff-photo">
                        <div class="staff-photo-placeholder">
                            Staff Group Photo<br><em>(900 × 360 px recommended)</em>
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
<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Work Programmes - ESWASA</title>
    <meta name="description" content="View ESWASA's current and past Work Programmes for Standards Development.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    
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
    <link rel="stylesheet" href="includes/cta-section.css">
    <style>
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 16px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Button Style */
        .btn-wp {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
            margin: 5px;
            padding: 10px 30px;
            font-weight: 600;
            text-transform: uppercase;
            transition: background-color 0.3s;
        }
        .btn-wp:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Introduction Section (Clean Professional Box) */
        .intro-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 40px;
            margin: 40px 0 60px 0;
            border-radius: 4px;
        }
        .intro-box h3 {
            color: #2B3388;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 700;
            border-bottom: 3px solid rgba(43, 51, 136, 0.15);
            padding-bottom: 15px;
        }
        .intro-box p {
            font-size: 16px;
            line-height: 1.6;
            color: #2B3388;
        }

        /* Work Programme List Styling - Card Look */
        .wp-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            margin-bottom: 12px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.04);
            transition: box-shadow 0.3s, border-color 0.3s;
        }
        .wp-list-item:hover {
            box-shadow: 0 4px 8px rgba(43, 51, 136, 0.07);
            border-color: #2B3388;
        }

        .wp-content {
            flex-grow: 1;
        }
        .wp-title {
            font-weight: 600;
            color: #2B3388;
            font-size: 1.1em;
            margin-bottom: 5px;
            transition: color 0.2s;
        }
        .wp-title a {
            color: inherit;
            text-decoration: none;
        }
        .wp-title a:hover {
            color: rgba(43, 51, 136, 0.85);
            text-decoration: underline;
        }
        .wp-details {
            font-size: 0.9em;
            color: rgba(43, 51, 136, 0.75);
        }
        .wp-status {
            text-align: right;
            padding-left: 30px;
            min-width: 150px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.85em;
            text-transform: uppercase;
            background-color: #2B3388;
            color: #fff;
        }
        .status-published {
            background-color: #2B3388;
            color: #fff;
        }
        .status-underdev {
            background-color: #fff;
            color: #2B3388;
            border: 1px solid #2B3388;
        }
        .status-revision {
            background-color: rgba(43, 51, 136, 0.15);
            color: #2B3388;
        }

        @media (max-width: 767.98px) {
            .intro-box { padding: 24px; margin: 24px 0 32px 0; }
            .intro-box h3 { font-size: 1.2rem; }
            .wp-list-item { flex-direction: column; align-items: flex-start; padding: 16px; }
            .wp-status { text-align: left; padding-left: 0; min-width: 0; margin-top: 10px; }
            .breadcrumb-content .title { font-size: 1.5rem; }
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>
    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('work', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Work Programmes</span>
                            </nav>
                            <h3 class="title">Standards Work Programmes</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <div class="container">
                <div class="intro-box">
                    <h3>ESWASA Standards Development Work Programmes</h3>
                    <p>The **ESWASA Work Programme** details all current and scheduled standards development and revision projects. This program is derived from national needs assessments and stakeholder requests, ensuring that the standards developed align with Eswatini's economic and regulatory priorities.</p>
                    <p>Interested stakeholders are invited to review the programme and provide feedback. For more information on specific projects, please contact us directly.</p>
                </div>

                <h4 class="mb-4" style="color: #2B3388; font-weight: 600;">Current and Recent Projects</h4>

                <div class="wp-list-container">
                    <div class="wp-list-item">
                        <div class="wp-content">
                            <div class="wp-title">
                                <a href="standard-detail-2552.php">Development of SZNS for Non-Medical Face Masks</a>
                            </div>
                            <div class="wp-details">Approved: 2020 | Reference: **SZNS US 2552: 2020**</div>
                        </div>
                        <div class="wp-status">
                            <span class="status-badge status-published">Published</span>
                        </div>
                    </div>
                    <div class="wp-list-item">
                        <div class="wp-content">
                            <div class="wp-title">
                                <a href="standard-detail-revision.php">Revision of SZNS for Solid Waste Disposal Sites</a>
                            </div>
                            <div class="wp-details">Approved: 2019 | Technical Committee: **TC 03 Environment**</div>
                        </div>
                        <div class="wp-status">
                            <span class="status-badge status-published">Published</span>
                        </div>
                    </div>
                    <div class="wp-list-item">
                        <div class="wp-content">
                            <div class="wp-title">
                                <a href="standard-detail-1470.php">New Standard for Hand Sanitizers (Alcohol-Based)</a>
                            </div>
                            <div class="wp-details">Approved: 2019 | Reference: **SZNS ARS 1470: 2019**</div>
                        </div>
                        <div class="wp-status">
                            <span class="status-badge status-published">Published</span>
                        </div>
                    </div>
                    <div class="wp-list-item">
                        <div class="wp-content">
                            <div class="wp-title">
                                <a href="standard-detail-45001.php">Adoption of ISO 45001 for Occupational Health and Safety</a>
                            </div>
                            <div class="wp-details">Approved: 2018 | Reference: **SZNS ISO 45001: 2018**</div>
                        </div>
                        <div class="wp-status">
                            <span class="status-badge status-published">Published</span>
                        </div>
                    </div>
                    <div class="wp-list-item">
                        <div class="wp-content">
                            <div class="wp-title">
                                <a href="standard-detail-033.php">Revision of SZNS for Packaged Water</a>
                            </div>
                            <div class="wp-details">Approved: 2014 | Reference: **SZNS 033: 2014**</div>
                        </div>
                        <div class="wp-status">
                            <span class="status-badge status-published">Published</span>
                        </div>
                    </div>
                    </div>

                <div class="text-center my-5 pt-4">
                    <a href="Standards.php" class="btn-cta">Propose a Standard Project</a>
                    <a href="contact.php" class="btn-cta">General Enquiries</a>
                </div>

            </div>
        </section>

    </main>
    <?php include("includes/footer.php")?>
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
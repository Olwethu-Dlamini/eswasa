<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Training - About - ESWASA</title>
    <meta name="description" content="Discover SWASA's comprehensive training programs designed to empower excellence through knowledge.">
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
        .text-muted {
            color: #2B3388 !important;
        }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        .breadcrumb-separator i { color: #fff !important; }

        /* Section divider — matches other pages */
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }

        /* Heading hierarchy */
        .display-6, .display-4 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .display-4 { font-size: 2.5rem; }

        /* Old gradient text class — flatten to solid brand blue */
        .text-gradient-primary {
            color: #2B3388 !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
        }

        /* Cards — restrained hover */
        .card {
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.12);
        }

        /* Training course cards */
        .add2cart_image {
            background: rgba(43, 51, 136, 0.04);
            overflow: hidden;
        }
        .add2cart_image img {
            max-height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform .25s ease;
        }
        /* ISO certificate SVG cards — contain, don't crop; padded so the badge sits centered */
        .add2cart_image img[src$=".svg"] {
            object-fit: contain;
            padding: 16px;
            background: #fff;
            height: 200px;
        }
        .hover-lift:hover .add2cart_image img {
            transform: scale(1.04);
        }
        .add2cart_prod_name {
            color: #2B3388;
            text-decoration: none;
        }
        .add2cart_prod_name:hover {
            color: #2B3388;
        }
        .add2cart_btn,
        .add2cart_btn.btn-primary,
        .btn.add2cart_btn {
            background-color: #2B3388 !important;
            border-color: #2B3388 !important;
            color: #fff !important;
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        .add2cart_btn:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            box-shadow: 0 4px 12px rgba(43, 51, 136, 0.20);
        }

        /* Modal styling */
        .modal-content {
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.18);
            box-shadow: 0 10px 30px rgba(43, 51, 136, 0.20);
        }
        .modal-header {
            background: #fff;
            color: #2B3388;
            padding: 18px 22px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.12);
        }
        .modal-header .modal-title {
            color: #2B3388;
            font-weight: 700;
            font-size: 18px;
        }
        .modal-header .btn-close { opacity: 0.65; }
        .modal-header .btn-close:hover { opacity: 1; }
        .modal-body { padding: 22px; color: #2B3388; }
        .modal-body p, .modal-body li { color: #2B3388; font-size: 15px; line-height: 1.6; }
        .course-details { margin-bottom: 20px; }
        .course-details h5 {
            color: #2B3388;
            margin: 18px 0 10px;
            font-weight: 700;
            font-size: 16px;
        }
        .course-details ul { padding-left: 20px; }
        .course-details li { margin-bottom: 8px; }
        .modal-footer {
            border-top: 1px solid rgba(43, 51, 136, 0.12);
            padding: 14px 22px;
        }
        .modal-footer .btn-secondary {
            background: #fff !important;
            border: 1px solid rgba(43, 51, 136, 0.30) !important;
            color: #2B3388 !important;
        }
        .btn-enroll {
            background: #2B3388;
            border: none;
            color: #fff;
            padding: 10px 22px;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color .2s ease;
        }
        .btn-enroll:hover {
            background: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Why Train With ESWASA — uniform 3×2 grid, no rainbow gradients */
        .why-train-section {
            background: rgba(43, 51, 136, 0.04);
            padding: 70px 0 80px;
            position: relative;
        }
        .why-train-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            max-width: 1180px;
            margin: 0 auto;
        }
        .why-train-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            padding: 32px 26px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
            height: 100%;
        }
        .why-train-card:hover {
            border-color: rgba(43, 51, 136, 0.40);
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.12);
            transform: translateY(-3px);
        }
        .why-train-icon {
            width: 64px;
            height: 64px;
            background: #2B3388;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: transform .25s ease;
        }
        .why-train-icon svg {
            width: 32px;
            height: 32px;
        }
        .why-train-card:hover .why-train-icon {
            transform: scale(1.08);
        }
        .why-train-card h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 17px;
            margin: 0 0 12px;
            line-height: 1.3;
        }
        .why-train-card p {
            color: #2B3388;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        /* Training policies — tabs */
        .bg_gray {
            background-color: rgba(43, 51, 136, 0.04);
        }
        .nav-tabs {
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .nav-tabs .nav-link {
            color: #2B3388;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            padding: 10px 18px;
            font-weight: 600;
            transition: color .2s ease, border-color .2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #2B3388;
            border-bottom-color: rgba(43, 51, 136, 0.30);
        }
        .nav-tabs .nav-link.active {
            color: #2B3388;
            border-bottom-color: #2B3388;
            background: transparent;
        }
        .policy-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #2B3388;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .policy-icon svg {
            width: 32px;
            height: 32px;
        }
        .tab-pane .card h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 20px;
            margin-top: 16px;
        }
        .tab-pane .card {
            border: 1px solid rgba(43, 51, 136, 0.12) !important;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.06) !important;
        }
        .tab-pane .card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
        }
        .tab-pane.show .card {
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .display-6 { font-size: 1.9rem !important; }
            .display-4 { font-size: 2rem; }
            .why-train-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 767.98px) {
            .display-6 { font-size: 1.55rem !important; }
            .display-4 { font-size: 1.65rem; }
            .why-train-section { padding: 50px 0 60px; }
            .why-train-card { padding: 24px 18px; }
            .why-train-icon { width: 56px; height: 56px; font-size: 20px; margin-bottom: 16px; }
            .why-train-card h4 { font-size: 15px; }
            .why-train-card p { font-size: 13px; }
            .nav-tabs .nav-link { padding: 8px 12px; font-size: 14px; }
            .modal-header { padding: 14px 18px; }
            .modal-body { padding: 18px; }
        }
        @media (max-width: 575.98px) {
            .why-train-grid { grid-template-columns: 1fr; gap: 14px; }
        }

        /* Training-format cards (Awareness / Full / Sector-based) */
        .training-format-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 20px;
            height: 100%;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .training-format-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .training-format-card .format-tag {
            display: inline-block;
            background-color: #2B3388;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 3px;
            margin-bottom: 8px;
        }
        .training-format-card .format-duration {
            font-weight: 700;
            color: #2B3388;
            margin: 0 0 6px;
            font-size: 0.95rem;
        }
        .training-format-card .format-audience {
            color: #2B3388;
            font-size: 0.92rem;
            line-height: 1.55;
            margin: 0;
        }

        /* Banking details block inside Course Fees policy tab */
        .bank-details {
            text-align: left;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 16px 20px;
            margin: 16px auto 0;
            max-width: 520px;
        }
        .bank-details h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1rem;
            margin: 0 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.18);
        }
        .bank-details dl {
            display: grid;
            grid-template-columns: max-content 1fr;
            gap: 6px 16px;
            margin: 0 0 12px;
        }
        .bank-details dt {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .bank-details dd {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
        }
        .bank-details p {
            color: #2B3388;
            font-size: 0.9rem;
            margin: 0;
        }

        @media (max-width: 575.98px) {
            .training-format-card { padding: 14px 16px; }
            .bank-details { padding: 14px; }
            .bank-details dl { grid-template-columns: 1fr; gap: 2px 0; }
            .bank-details dt { font-size: 0.82rem; margin-top: 8px; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('training_about', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Training</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                             
                            </nav>
                            <h1 class="title">About Our Training</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Training Courses Section -->
        <section id="training_section" class="content_section py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold">Our Training Programmes</h2>
                    <p class="text-muted mt-2 mb-0">Empowering Excellence Through Knowledge</p>
                    <div class="section-divider"></div>
                </div>

                <!-- Training Introduction -->
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-10">
                        <p class="text-muted text-center">We understand the unique needs of each business, which is why we offer tailor-made training solutions to industry, individuals, government agencies and other institutions in Management Systems, allowing organisations to choose a convenient location or host the training at our training centre in Matsapha.</p>
                        <p class="text-muted text-center">At ESWASA Training Academy, we are proud to work with facilitators who are industry experts in various fields, Lead Auditors, and major contributors to the development of Eswatini National Standards (SZNS).</p>
                    </div>
                </div>

                <!-- Training Formats -->
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-md-5">
                        <div class="training-format-card">
                            <div class="format-tag">Awareness Training</div>
                            <p class="format-duration">½ day · 1 day · 2 days</p>
                            <p class="format-audience">Suitable for management, supervisors and teams needing a working introduction to a standard.</p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="training-format-card">
                            <div class="format-tag">Full Training</div>
                            <p class="format-duration">3 – 5 days</p>
                            <p class="format-audience">Understanding &amp; Implementation, Auditing and Customised training for practitioners taking the standard into operation.</p>
                        </div>
                    </div>
                </div>
                <p class="text-muted text-center small mb-5">
                    Both formats are delivered as <strong>standard-based courses across all sectors</strong> — see the full course catalogue below.
                </p>

                <!-- Training Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/certificate-iso-9001-colored.svg" alt="ISO 9001 — Quality Management System" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#qualityModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#qualityModal" class="add2cart_prod_name d-block mb-2 fw-bold">Quality Management System Courses</a>
                                    <a data-bs-toggle="modal" data-bs-target="#qualityModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/certificate-iso-45001-colored.svg" alt="ISO 45001 — Health and Safety Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#healthModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#healthModal" class="add2cart_prod_name d-block mb-2 fw-bold">Health and Safety Management</a>
                                    <a data-bs-toggle="modal" data-bs-target="#healthModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/certificate-iso-14001-colored.svg" alt="ISO 14001 — Environmental Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#environmentModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#environmentModal" class="add2cart_prod_name d-block mb-2 fw-bold">Environmental Management</a>
                                    <a data-bs-toggle="modal" data-bs-target="#environmentModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/course-globalgap.svg" alt="GLOBALG.A.P. — Good Agricultural Practices" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#agricultureModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#agricultureModal" class="add2cart_prod_name d-block mb-2 fw-bold">Good Agricultural Practices</a>
                                    <a data-bs-toggle="modal" data-bs-target="#agricultureModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/course-wellness.svg" alt="Wellness Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#wellnessModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#wellnessModal" class="add2cart_prod_name d-block mb-2 fw-bold">Wellness Management</a>
                                    <a data-bs-toggle="modal" data-bs-target="#wellnessModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/course-iso-22000.svg" alt="ISO 22000 — Food Safety Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#foodModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#foodModal" class="add2cart_prod_name d-block mb-2 fw-bold">Food Safety Management</a>
                                    <a data-bs-toggle="modal" data-bs-target="#foodModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/course-iso-19011.svg" alt="ISO 19011 — Auditing" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#auditingModal">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#auditingModal" class="add2cart_prod_name d-block mb-2 fw-bold">Auditing</a>
                                    <a data-bs-toggle="modal" data-bs-target="#auditingModal" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Training Modals -->
        <!-- Quality Management Modal -->
        <div class="modal fade" id="qualityModal" tabindex="-1" aria-labelledby="qualityModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qualityModalLabel">Quality Management System Courses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Quality Management System courses are designed to help organisations implement and maintain effective quality management systems based on international standards.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Improved product and service quality</li>
                                <li>Enhanced customer satisfaction</li>
                                <li>Streamlined processes and reduced waste</li>
                                <li>Increased operational efficiency</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>ISO 9001:2015 Foundation</li>
                                <li>ISO 9001:2015 Internal Auditor</li>
                                <li>ISO 9001:2015 Lead Auditor</li>
                                <li>Quality Management System Implementation</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Health and Safety Modal -->
        <div class="modal fade" id="healthModal" tabindex="-1" aria-labelledby="healthModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="healthModalLabel">Health and Safety Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Health and Safety Management courses provide comprehensive training on occupational health and safety standards to create safer work environments.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Reduced workplace accidents and incidents</li>
                                <li>Compliance with legal requirements</li>
                                <li>Improved employee morale and productivity</li>
                                <li>Enhanced corporate reputation</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>ISO 45001:2018 Foundation</li>
                                <li>ISO 45001:2018 Internal Auditor</li>
                                <li>ISO 45001:2018 Lead Auditor</li>
                                <li>Risk Assessment and Management</li>
                                <li>Incident Investigation and Reporting</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 1-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Environmental Management Modal -->
        <div class="modal fade" id="environmentModal" tabindex="-1" aria-labelledby="environmentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="environmentModalLabel">Environmental Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Environmental Management courses help organisations implement sustainable practices and comply with environmental regulations.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Reduced environmental impact</li>
                                <li>Compliance with environmental regulations</li>
                                <li>Cost savings through resource efficiency</li>
                                <li>Enhanced corporate social responsibility</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>ISO 14001:2015 Foundation</li>
                                <li>ISO 14001:2015 Internal Auditor</li>
                                <li>ISO 14001:2015 Lead Auditor</li>
                                <li>Environmental Impact Assessment</li>
                                <li>Sustainability Reporting</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Good Agricultural Practices Modal -->
        <div class="modal fade" id="agricultureModal" tabindex="-1" aria-labelledby="agricultureModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agricultureModalLabel">Good Agricultural Practices</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Good Agricultural Practices courses focus on sustainable farming methods to ensure food safety, environmental protection, and worker welfare.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Improved crop quality and yield</li>
                                <li>Reduced environmental impact in agriculture</li>
                                <li>Enhanced food safety standards</li>
                                <li>Better market access and compliance</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>GLOBALG.A.P. Foundation</li>
                                <li>Farm Assurance Implementation</li>
                                <li>Sustainable Farming Practices</li>
                                <li>Agricultural Risk Management</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 2-4 days, available in both in-person and virtual formats. Customised training options are available for agricultural organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wellness Management Modal -->
        <div class="modal fade" id="wellnessModal" tabindex="-1" aria-labelledby="wellnessModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="wellnessModalLabel">Wellness Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Wellness Management courses promote holistic health approaches for individuals and organisations to improve overall well-being.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Improved employee health and productivity</li>
                                <li>Reduced absenteeism and healthcare costs</li>
                                <li>Enhanced work-life balance</li>
                                <li>Stronger organizational culture</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>Workplace Wellness Foundation</li>
                                <li>Stress Management Techniques</li>
                                <li>Health Promotion Strategies</li>
                                <li>Wellness Programme Implementation</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 1-3 days, available in both in-person and virtual formats. Customised training options are available for organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Food Safety Management Modal -->
        <div class="modal fade" id="foodModal" tabindex="-1" aria-labelledby="foodModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="foodModalLabel">Food Safety Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Food Safety Management courses provide essential training on maintaining hygiene and safety standards in food production and handling.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Prevention of foodborne illnesses</li>
                                <li>Compliance with food safety regulations</li>
                                <li>Improved product quality and shelf life</li>
                                <li>Enhanced consumer trust</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>ISO 22000:2018 Foundation</li>
                                <li>HACCP Principles and Application</li>
                                <li>Food Safety Internal Auditor</li>
                                <li>Food Hygiene Management</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for food industry organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auditing Modal -->
        <div class="modal fade" id="auditingModal" tabindex="-1" aria-labelledby="auditingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="auditingModalLabel">Auditing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p>Our Auditing courses train professionals in effective auditing techniques for various management systems to ensure compliance and continuous improvement.</p>
                            
                            <h5>Key Benefits</h5>
                            <ul>
                                <li>Improved system compliance and effectiveness</li>
                                <li>Identification of improvement opportunities</li>
                                <li>Enhanced risk management</li>
                                <li>Professional certification pathways</li>
                            </ul>
                            
                            <h5>Available Courses</h5>
                            <ul>
                                <li>ISO 19011:2018 Auditing Guidelines</li>
                                <li>Integrated Management System Auditor</li>
                                <li>Lead Auditor Training</li>
                                <li>Audit Reporting and Follow-up</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 3-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Train With ESWASA Section -->
        <section class="why-train-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold">Why Train With ESWASA?</h2>
                    <p class="text-muted mt-2 mb-0">Discover the unique advantages of choosing ESWASA for your professional development</p>
                    <div class="section-divider"></div>
                </div>

                <div class="why-train-grid">
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M24 14 V40"/>
                                <path d="M24 14 C18 11 12 11 8 13 L8 38 C12 36 18 36 24 40"/>
                                <path d="M24 14 C30 11 36 11 40 13 L40 38 C36 36 30 36 24 40"/>
                                <path d="M30 6 L33 9 L38 4" stroke-width="2.5"/>
                            </svg>
                        </div>
                        <h4>Standard-based Training</h4>
                        <p>Our training courses are based on international standards, ensuring high-quality content and delivery. Course modules are developed in cooperation with recognised standards experts to provide current, relevant knowledge.</p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="24" cy="10" r="4" fill="currentColor"/>
                                <circle cx="10" cy="32" r="4" fill="currentColor"/>
                                <circle cx="38" cy="32" r="4" fill="currentColor"/>
                                <line x1="14" y1="30" x2="20" y2="14"/>
                                <line x1="34" y1="30" x2="28" y2="14"/>
                                <line x1="15" y1="32" x2="33" y2="32"/>
                            </svg>
                        </div>
                        <h4>Highly Interactive Sessions</h4>
                        <p>Individualised attention through small interactive training sessions. Hands-on courses designed to help you acquire skills quickly and in depth, with room for questions and practical exercises.</p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 4 L20 14"/>
                                <path d="M34 4 L28 14"/>
                                <path d="M20 4 L24 12"/>
                                <path d="M28 4 L24 12"/>
                                <circle cx="24" cy="28" r="12"/>
                                <polygon points="24,22 26,27 31,27 27,30 28,35 24,33 20,35 21,30 17,27 22,27" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4>Quality Training</h4>
                        <p>Quality training that is relevant to the needs of our society, delivered by experienced instructors across every course we offer, ensuring practical knowledge that can be immediately applied.</p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="6" y1="14" x2="42" y2="14"/>
                                <circle cx="34" cy="14" r="3.5" fill="currentColor"/>
                                <line x1="6" y1="24" x2="42" y2="24"/>
                                <circle cx="14" cy="24" r="3.5" fill="currentColor"/>
                                <line x1="6" y1="34" x2="42" y2="34"/>
                                <circle cx="26" cy="34" r="3.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4>Flexibility of Course Content</h4>
                        <p>We tailor course content to your specific needs to meet your business objectives, whether customised training for your team or specialised content for your industry.</p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 42 H42"/>
                                <path d="M6 42 V8"/>
                                <polyline points="10 32 18 24 26 28 38 12"/>
                                <polyline points="32 12 38 12 38 18"/>
                                <circle cx="18" cy="24" r="2" fill="currentColor"/>
                                <circle cx="26" cy="28" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4>Return on Investment</h4>
                        <p>Our courses help industry, commerce and the public sector maximise return on investment, with practical skills that deliver measurable improvements in performance and efficiency.</p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="24" cy="14" r="5" fill="currentColor"/>
                                <path d="M14 36 V32 C14 26 18 22 24 22 C30 22 34 26 34 32 V36"/>
                                <line x1="14" y1="36" x2="34" y2="36"/>
                                <path d="M40 18 Q44 24 40 30"/>
                                <path d="M8 18 Q4 24 8 30"/>
                            </svg>
                        </div>
                        <h4>Certified Facilitators</h4>
                        <p>We are passionate about sharing knowledge and skills on the principles and practices of standards. Our facilitators are not just experts in their fields — they are dedicated, certified, and committed to your success.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Training Policies Section -->
        <section id="training_policies_section" class="content_section bg_fixed bg11 bg_gray border_b_n py-5">
            <div class="content row_spacer clearfix">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold">Training Academy — General Information</h2>
                    <p class="text-muted mt-2 mb-0">Ensuring a Smooth Training Experience</p>
                    <div class="section-divider"></div>
                </div>

                <!-- Tabs Navigation -->
                <div class="container">
                    <ul class="nav nav-tabs justify-content-center mb-4" id="policiesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" type="button" role="tab" aria-controls="application" aria-selected="true">Application</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="acceptance-tab" data-bs-toggle="tab" data-bs-target="#acceptance" type="button" role="tab" aria-controls="acceptance" aria-selected="false">Acceptance</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cancellations-tab" data-bs-toggle="tab" data-bs-target="#cancellations" type="button" role="tab" aria-controls="cancellations" aria-selected="false">Cancellations</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab" aria-controls="fees" aria-selected="false">Course Fees</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="travel-tab" data-bs-toggle="tab" data-bs-target="#travel" type="button" role="tab" aria-controls="travel" aria-selected="false">Travel</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inhouse-tab" data-bs-toggle="tab" data-bs-target="#inhouse" type="button" role="tab" aria-controls="inhouse" aria-selected="false">Training Venues</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assessments-tab" data-bs-toggle="tab" data-bs-target="#assessments" type="button" role="tab" aria-controls="assessments" aria-selected="false">Assessments</button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="policiesTabContent">
                        <div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                        <path d="M28 6 V16 H38"/>
                                        <line x1="16" y1="24" x2="32" y2="24"/>
                                        <line x1="16" y1="30" x2="32" y2="30"/>
                                        <line x1="16" y1="36" x2="26" y2="36"/>
                                        <path d="M30 36 L36 30" stroke-width="2.5"/>
                                        <circle cx="36" cy="30" r="1.5" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Application</h3>
                                <p>Application forms and course-related information can be accessed through this website under <strong>Training</strong>, or requested from the Training Unit — call <a href="tel:+26876027306" style="color:#2B3388; text-decoration:underline; font-weight:600;">7602 7306</a> or email <a href="mailto:info@eswasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">info@eswasa.co.sz</a> / <a href="mailto:training@eswasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">training@eswasa.co.sz</a>. Applications should reach ESWASA at least <strong>10 working days</strong> before the course commencement date. If the number of paid applicants has not reached the minimum required for a class (<strong>5 delegates</strong>), ESWASA reserves the right to postpone the course but undertakes to inform participants promptly of such developments.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="acceptance" role="tabpanel" aria-labelledby="acceptance-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                        <path d="M28 6 V16 H38"/>
                                        <circle cx="24" cy="30" r="9"/>
                                        <path d="M20 30 L23 33 L29 26" stroke-width="2.5"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Acceptance</h3>
                                <p>Applicants will be notified of the outcome of their applications soon thereafter. Acceptance of the offer has to be acknowledged by the participant in writing and sent by email or fax to the office as soon as possible or 7 days before the start of the training for registration.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="cancellations" role="tabpanel" aria-labelledby="cancellations-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="6" y="10" width="36" height="32" rx="2"/>
                                        <line x1="6" y1="18" x2="42" y2="18"/>
                                        <line x1="14" y1="6" x2="14" y2="14"/>
                                        <line x1="34" y1="6" x2="34" y2="14"/>
                                        <line x1="18" y1="26" x2="30" y2="36" stroke-width="2.5"/>
                                        <line x1="30" y1="26" x2="18" y2="36" stroke-width="2.5"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Cancellations</h3>
                                <p>A cancellation fee of <strong>50%</strong> of the course fee will be deducted from participants who cancel after registration / confirmation or on the date of commencement of the training course. ESWASA reserves the right to postpone any course (typically due to insufficient enrolment — see Application for class minimums) and undertakes to inform participants promptly of such developments.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="12" width="40" height="24" rx="3"/>
                                        <circle cx="9" cy="17" r="1" fill="currentColor"/>
                                        <circle cx="39" cy="31" r="1" fill="currentColor"/>
                                        <circle cx="24" cy="24" r="7"/>
                                        <line x1="24" y1="19" x2="24" y2="29"/>
                                        <path d="M27 21 Q25 20 22.5 22 Q23 24 25 24 Q27 24 27.5 26 Q26 28 21 27"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Course Fees and Payments</h3>
                                <p>Course fees are charged per person and are inclusive of meals and refreshments for the duration of the training. Applicants should pay in full and submit <strong>proof of payment or a purchase order</strong> at least <strong>7 working days</strong> before the course commencement date.</p>
                                <div class="bank-details">
                                    <h4>Banking Details</h4>
                                    <dl>
                                        <dt>Bank Name</dt><dd>Standard Bank Eswatini</dd>
                                        <dt>Account Name</dt><dd>Eswatini Standards Authority — ESWASA</dd>
                                        <dt>Account Number</dt><dd>9110002956732</dd>
                                        <dt>Branch Code</dt><dd>663164</dd>
                                        <dt>Branch Name</dt><dd>Matsapha</dd>
                                    </dl>
                                    <p class="mb-0"><strong>Mobile Money</strong> and a <strong>Speedpoint machine</strong> are also available at the ESWASA office for ease of payment.</p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="travel" role="tabpanel" aria-labelledby="travel-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="6" y="14" width="36" height="28" rx="2"/>
                                        <path d="M18 14 V10 C18 8.9 18.9 8 20 8 H28 C29.1 8 30 8.9 30 10 V14"/>
                                        <line x1="6" y1="26" x2="42" y2="26"/>
                                        <rect x="22" y="24" width="4" height="4" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Travel and Accommodation</h3>
                                <p>Participants are responsible for their own travel and accommodation arrangements. All courses offered are day courses.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="inhouse" role="tabpanel" aria-labelledby="inhouse-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 42 V14 L24 6 L40 14 V42 Z"/>
                                        <rect x="13" y="20" width="5" height="5"/>
                                        <rect x="30" y="20" width="5" height="5"/>
                                        <rect x="13" y="28" width="5" height="5"/>
                                        <rect x="30" y="28" width="5" height="5"/>
                                        <rect x="21" y="34" width="6" height="8"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Training Venues</h3>
                                <p>The venue for most courses is the <strong>ESWASA Training Academy</strong>, unless prior arrangements are made for in-house and/or customised training. In-house training will be presented to a <strong>minimum of 11 delegates</strong>. The organisation shall be responsible for providing a suitable training room with audiovisual equipment as well as refreshments.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="assessments" role="tabpanel" aria-labelledby="assessments-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H32 L40 14 V42 H10 Z"/>
                                        <path d="M32 6 V14 H40"/>
                                        <path d="M16 22 H34"/>
                                        <path d="M16 28 H34"/>
                                        <path d="M16 34 H26"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold">Assessments</h3>
                                <p>Participant performance is evaluated through:</p>
                                <ul class="text-start mx-auto" style="max-width: 520px;">
                                    <li>Continuous assessments.</li>
                                    <li>Practical exercises.</li>
                                    <li>Group activities.</li>
                                    <li>Final examinations.</li>
                                </ul>
                                <p>The minimum passing mark is generally <strong>70%</strong>. Participants may receive:</p>
                                <ul class="text-start mx-auto" style="max-width: 520px;">
                                    <li><strong>Certificate of Competence</strong> &mdash; on successful completion and passing of assessments.</li>
                                    <li><strong>Certificate of Attendance</strong> &mdash; for awareness trainings or participation-only sessions.</li>
                                </ul>
                            </div>
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
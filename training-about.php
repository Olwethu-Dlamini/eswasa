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
        .text-gradient-primary {
            background: linear-gradient(45deg, #2E3191, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-lift:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .add2cart_image img {
            max-height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .hover-lift:hover .add2cart_image img {
            transform: scale(1.05);
        }
        .add2cart_prod_name {
            color: #2E3191;
            text-decoration: none;
        }
        .add2cart_prod_name:hover {
            color: #00c6ff;
        }
        .add2cart_btn {
            transition: background-color 0.3s ease;
        }
        .add2cart_btn:hover {
            background-color: #2E3191;
        }
        
        /* Modal Styling */
        .modal-content {
            border-radius: 0;
            border: 1px solid #dee2e6;
            box-shadow: none;
        }
        .modal-header {
            background: #f8f9fa;
            color: #333;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .modal-header .btn-close {
            color: #333;
            opacity: 0.8;
        }
        .modal-header .btn-close:hover {
            opacity: 1;
        }
        .modal-body {
            padding: 15px;
        }
        .course-details {
            margin-bottom: 20px;
        }
        .course-details h5 {
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .course-details ul {
            padding-left: 20px;
        }
        .course-details li {
            margin-bottom: 8px;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
            padding: 15px;
        }
        .btn-enroll {
            background: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
        }
        
        /* Enhanced Why Train With SWASA Section */
        .tree-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        .tree-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #2E3191, #00c6ff);
        }
        .tree-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .tree-sides-wrapper {
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }
        .tree-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .tree-center {
            width: 4px;
            background: linear-gradient(to bottom, #2E3191, #00c6ff);
            margin: 0 20px;
            min-height: 700px;
            border-radius: 2px;
            position: relative;
        }
        .tree-center::before, .tree-center::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #2E3191;
        }
        .tree-center::before {
            top: -10px;
        }
        .tree-center::after {
            bottom: -10px;
            background: #00c6ff;
        }
        .tree-card {
            padding: 30px;
            color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            display: flex;
            align-items: flex-start;
            gap: 20px;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .tree-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
        }
        .tree-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        /* Left side cards - icons on left (outer edge) */
        .tree-card-left {
            text-align: left;
            flex-direction: row;
        }
        /* Right side cards - icons on right (outer edge) */
        .tree-card-right {
            text-align: right;
            flex-direction: row-reverse;
        }
        .tree-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }
        .tree-card:hover .tree-icon {
            transform: scale(1.1);
            background: rgba(255, 255, 255, 0.35);
        }
        .tree-content {
            flex: 1;
        }
        .tree-content h4 {
            font-family: 'Oswald', sans-serif;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 15px;
            color: #fff;
            line-height: 1.2;
        }
        .tree-content p {
            font-size: 15px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.95);
            margin: 0;
            font-weight: 400;
        }
        /* Colors */
        .bg-blue { background: linear-gradient(135deg, #0072A5, #005a87); }
        .bg-green { background: linear-gradient(135deg, #1BBC9B, #15997d); }
        .bg-salmon { background: linear-gradient(135deg, #F36A71, #e0555c); }
        .bg-purple { background: linear-gradient(135deg, #B853A3, #9d458d); }
        .bg-darkblue { background: linear-gradient(135deg, #4D4294, #3d3578); }
        .bg-cyan { background: linear-gradient(135deg, #0CAEBF, #0a8d9a); }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .tree-sides-wrapper {
                flex-direction: column;
                gap: 20px;
            }
            .tree-center {
                display: none;
            }
            .tree-side {
                gap: 20px;
            }
            .tree-card-left, .tree-card-right {
                text-align: center;
                flex-direction: column;
            }
            .tree-icon {
                margin-bottom: 15px;
            }
        }
        
        /* Training Policies Section */
        .timeline {
            position: relative;
            padding-left: 40px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #2E3191;
        }
        .timeline-item {
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -28px;
            top: 20px;
            width: 16px;
            height: 16px;
            background: #2E3191;
            border-radius: 50%;
            border: 2px solid #fff;
        }
        .timeline-item:hover .card {
            transform: translateX(10px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .leaf_icon {
            width: 40px;
            height: 40px;
            line-height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .bg_gray {
            background-color: #f8f9fa;
        }
        .nav-tabs .nav-link {
            color: #2E3191;
            border: none;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s ease;
        }
        .nav-tabs .nav-link.active, .nav-tabs .nav-link:hover {
            border-bottom: 2px solid #2E3191;
            color: #2E3191;
        }
        .policy-icon {
            width: 60px;
            height: 60px;
            line-height: 60px;
            border-radius: 50%;
            background: #2E3191;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .tab-pane.show .card {
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Training</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                             
                            </nav>
                            <h3 class="title">About Our Training</h3>
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
                <div class="main_title centered upper mb-5">
                    <h2 class="display-6 fw-bold text-center text-gradient-primary">
                        Our Training Programs
                        <span class="d-block fs-5 text-muted mt-2">Empowering Excellence Through Knowledge</span>
                        <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
                    </h2>
                </div>

                <!-- Training Introduction -->
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10">
                        <p class="text-muted text-center">We understand the unique needs of each business, which is why we offer tailor-made training solutions to industry, individuals, government agencies and other Institutions in Management Systems, allowing organisations to choose a convenient location or host the training at our training centre in Matsapha.</p>
                        <p class="text-muted text-center">At ESWASA Training Academy, we are proud to work with facilitators who are industry experts in various fields, Lead Auditors, and major contributors to the development of Eswatini National Standards (SZNS).</p>
                    </div>
                </div>

                <!-- Training Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="admin/uploads/qm.jpg" alt="Quality Management System Courses" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#qualityModal">
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
                                <img src="admin/uploads/hm.jfif" alt="Health and Safety Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#healthModal">
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
                                <img src="admin/uploads/em.jpg" alt="Environmental Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#environmentModal">
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
                                <img src="admin/uploads/gap.jpg" alt="Good Agricultural Practices" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#agricultureModal">
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
                                <img src="admin/uploads/w.png" alt="Wellness Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#wellnessModal">
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
                                <img src="admin/uploads/fm.webp" alt="Food Safety Management" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#foodModal">
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
                                <img src="admin/uploads/a.jpg" alt="Auditing" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#auditingModal">
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
                            <p>Our Quality Management System courses are designed to help organizations implement and maintain effective quality management systems based on international standards.</p>
                            
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
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customized training options are available for organizations.</p>
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
                            <p>Courses range from 1-5 days, available in both in-person and virtual formats. Customized training options are available for organizations.</p>
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
                            <p>Our Environmental Management courses help organizations implement sustainable practices and comply with environmental regulations.</p>
                            
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
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customized training options are available for organizations.</p>
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
                            <p>Courses range from 2-4 days, available in both in-person and virtual formats. Customized training options are available for agricultural organizations.</p>
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
                            <p>Our Wellness Management courses promote holistic health approaches for individuals and organizations to improve overall well-being.</p>
                            
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
                                <li>Wellness Program Implementation</li>
                            </ul>
                            
                            <h5>Duration & Format</h5>
                            <p>Courses range from 1-3 days, available in both in-person and virtual formats. Customized training options are available for organizations.</p>
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
                            <p>Courses range from 2-5 days, available in both in-person and virtual formats. Customized training options are available for food industry organizations.</p>
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
                            <p>Courses range from 3-5 days, available in both in-person and virtual formats. Customized training options are available for organizations.</p>
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
        <section class="tree-section py-5">
            <div class="container">
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-4 fw-bold text-gradient-primary">Why Train With ESWASA?</h2>
                    <p class="lead mt-3">Discover the unique advantages of choosing ESWASA for your professional development</p>
                </div>

                <div class="tree-container">
                    <div class="tree-sides-wrapper">
                        <!-- Left Side - 3 Cards -->
                        <div class="tree-side tree-side-left">
                            <div class="tree-card tree-card-left bg-blue">
                                <div class="tree-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Standards Based Training</h4>
                                    <p>Our training courses are based on international standards ensuring high quality content and delivery. Course modules are developed in cooperation with recognized standards experts to provide you with the most current and relevant knowledge.</p>
                                </div>
                            </div>

                            <div class="tree-card tree-card-left bg-salmon">
                                <div class="tree-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Quality Training</h4>
                                    <p>We offer quality training that is relevant to the needs of our society. Delivered by experienced instructors for each every course we offer, ensuring you receive practical knowledge that can be immediately applied in your workplace.</p>
                                </div>
                            </div>

                            <div class="tree-card tree-card-left bg-darkblue">
                                <div class="tree-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Return on Investment</h4>
                                    <p>Our courses are geared towards helping industry, commerce and the public sector to maximize their return on investment. We focus on practical skills that deliver measurable improvements in performance and efficiency.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Central Spacer -->
                        <div class="tree-center"></div>

                        <!-- Right Side - 3 Cards -->
                        <div class="tree-side tree-side-right">
                            <div class="tree-card tree-card-right bg-green">
                                <div class="tree-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Small, Highly Interactive Sessions</h4>
                                    <p>Individualised attention is ensured through small interactive training sessions. Our hands-on courses are designed to help you acquire the skills you need quickly and in-depth, with plenty of opportunities for questions and practical exercises.</p>
                                </div>
                            </div>

                            <div class="tree-card tree-card-right bg-purple">
                                <div class="tree-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Flexibility of Course Content</h4>
                                    <p>We can tailor our course content to your specific needs, to meet your business objectives. Whether you need customized training for your team or specialized content for your industry, we can adapt our programs accordingly.</p>
                                </div>
                            </div>

                            <div class="tree-card tree-card-right bg-cyan">
                                <div class="tree-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="tree-content">
                                    <h4>Passionate Instructors</h4>
                                    <p>We are passionate about sharing knowledge and skills on the principles and practices of standards. Our instructors are not just experts in their fields - they're dedicated educators committed to your success.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Training Policies Section -->
        <section id="training_policies_section" class="content_section bg_fixed bg11 bg_gray border_b_n py-5">
            <div class="content row_spacer clearfix">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5">
                    <h2 class="display-6 fw-bold text-center text-gradient-primary">
                        Our Training Policies
                        <span class="d-block fs-5 text-muted mt-2">Ensuring a Smooth Training Experience</span>
                    </h2>
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
                            <button class="nav-link" id="inhouse-tab" data-bs-toggle="tab" data-bs-target="#inhouse" type="button" role="tab" aria-controls="inhouse" aria-selected="false">In-House Training</button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="policiesTabContent">
                        <div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-file-alt fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">Application</h3>
                                <p class="text-muted">The maximum number of delegates per course is limited due to seating capacity and our pursuit of effective instruction by ensuring a good instructor to delegate ratio. You will find our Application form at the end of this document and is also available by request through email, fax or can be collected at our offices. All applications are to be completed and received by our office 10 working days before the commencement of each course.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="acceptance" role="tabpanel" aria-labelledby="acceptance-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-check-circle fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">Acceptance</h3>
                                <p class="text-muted">Applicants will be notified of the outcome of their applications soon thereafter. Acceptance of the offer has to be acknowledged by the participant in writing and sent by email or fax to the office as soon as possible or 7 days before the start of the training for registration.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="cancellations" role="tabpanel" aria-labelledby="cancellations-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-times-circle fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">Cancellations</h3>
                                <p class="text-muted">A cancellation fee of 50% of the course fee will be deducted to participants who cancel after confirmation and registration. NB. SWASA reserves the right to cancel any course, but undertake to inform participants promptly of such developments. The main reason for such cancellation or postponement would be insufficient number of participants. The minimum number of delegates per course shall be 10 and the maximum shall be 20 delegates.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-money-check-alt fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">Course Fees and Payments</h3>
                                <p class="text-muted">Course fees are charged per person and are inclusive of meals and refreshments for the duration of the training. Fees are payable in full and in advance by cash or cheque deposit to the Eswatini Standards Authority account. Proof of payment must be submitted to the course administrator 7 working days before classes commence.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="travel" role="tabpanel" aria-labelledby="travel-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-plane fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">Travel and Accommodation</h3>
                                <p class="text-muted">Participants are responsible for their own travel and accommodation arrangements. All courses offered are day courses.</p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="inhouse" role="tabpanel" aria-labelledby="inhouse-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3"><i class="fas fa-building fa-3x text-primary"></i></span>
                                <h3 class="fw-bold">In-House Training</h3>
                                <p class="text-muted">On special written request, and if possible, SWASA shall provide in-house training for organizations. In house training will be presented to a minimum of 5 and a maximum of 20 participants. The organization shall be responsible for providing a suitable training room with audiovisual equipment as well as refreshments.</p>
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
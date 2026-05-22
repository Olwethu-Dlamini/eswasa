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
    <meta name="description" content="ESWASA Product Certification: Compulsory and voluntary certification of goods against SZNS, ISO and regional standards.">
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
    <link rel="stylesheet" href="includes/cta-section.css">

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
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        .btn-cert {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
            margin: 5px;
            font-weight: 600;
        }
        .btn-cert:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }
        /* Clean highlight section */
        .highlighted-section {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 25px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .highlighted-section h3 {
            color: #2B3388;
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
            background-color: #2B3388;
            color: #fff;
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
            border: 1px solid rgba(43, 51, 136, 0.15);
        }
        .prod-schemes-table th, .prod-schemes-table td {
            padding: 14px;
            text-align: left;
        }
        .prod-schemes-table th {
            background-color: #2B3388;
            color: #fff;
            font-weight: 600;
        }
        .prod-schemes-table tr:nth-child(even) {
            background-color: rgba(43, 51, 136, 0.04);
        }
        .prod-schemes-table tr:hover {
            background-color: rgba(43, 51, 136, 0.08);
        }

        .cert-process-section .process-row {
            margin-bottom: 40px;
        }
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }
        .cert-process-svg {
            max-width: 1000px;
            margin: 0 auto;
        }
        .cert-process-svg svg {
            width: 100%;
            height: auto;
            display: block;
        }
        .cert-process-list {
            max-width: 620px;
            margin: 0 auto;
        }

        .process-circle {
            background: #2B3388;
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
            background: #fff;
            color: #2B3388;
            border: 3px solid #2B3388;
            font-weight: 700;
        }

        .process-arrow {
            font-size: 40px;
            font-weight: 700;
            margin: 0 10px;
            color: #2B3388;
        }

        .process-divider {
            width: 80%;
            height: 4px;
            background: rgba(43, 51, 136, 0.15);
            margin: 20px auto;
            border-radius: 4px;
        }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            .highlighted-section h3 {
                font-size: 1.2rem;
            }
            .prod-process-steps li {
                padding-left: 40px;
            }
            .prod-process-steps li::before {
                width: 28px;
                height: 28px;
                font-size: 0.85rem;
            }
            /* Process images stack on mobile */
            .col-lg-7.mb-4.mb-lg-0,
            .col-lg-5 {
                text-align: center;
            }
            .col-lg-5 img {
                width: 60% !important;
                margin-top: 20px;
            }
            /* Certified Products grid smaller */
            .certified-products-img {
                height: 160px !important;
            }
            /* Section title */
            .section-title {
                font-size: 1.3rem;
            }
            .process-circle {
                width: 140px;
                height: 140px;
            }
            .process-arrow {
                display: none;
            }
            .prod-schemes-table th, .prod-schemes-table td {
                padding: 8px;
                font-size: 0.9em;
            }
        }

        /* ===== Certified Product Producers grid (mirrors managementsystems.php) ===== */
        .producers-wrap .cw-header {
            text-align: center;
            padding: 30px 20px 15px;
        }
        .producers-wrap .cw-header h3 {
            font-size: 1.3rem;
            letter-spacing: 2px;
            color: #2B3388;
            margin-bottom: 10px;
        }
        .producers-wrap .cw-header .cw-divider {
            width: 60px;
            height: 3px;
            background: #2B3388;
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
        }
        .producers-wrap .cw-header .cw-divider::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            border: 2px solid #2B3388;
            border-radius: 50%;
            background: #fff;
        }
        .producer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            padding: 10px 24px 30px;
        }
        .producer-tile {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            min-height: 140px;
            padding: 18px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 8px;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .producer-tile:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .producer-tile img {
            max-width: 100%;
            max-height: 70px;
            width: auto;
            height: auto;
            object-fit: contain;
        }
        .producer-tile .producer-wordmark {
            color: #2B3388;
            font-weight: 700;
            font-size: 0.98rem;
            letter-spacing: 0.4px;
            line-height: 1.3;
        }
        .producer-tile .producer-product {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.85rem;
            line-height: 1.4;
        }
        .producer-tile .producer-standard {
            color: #2B3388;
            font-size: 0.76rem;
            letter-spacing: 0.3px;
        }
        @media (max-width: 575.98px) {
            .producers-wrap .cw-header { padding: 20px 14px 10px; }
            .producers-wrap .cw-header h3 { font-size: 1.05rem; letter-spacing: 1.5px; }
            .producer-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 8px 12px 22px;
            }
            .producer-tile {
                min-height: 120px;
                padding: 14px 10px;
                gap: 6px;
            }
            .producer-tile .producer-wordmark { font-size: 0.85rem; }
            .producer-tile .producer-product { font-size: 0.78rem; }
            .producer-tile .producer-standard { font-size: 0.7rem; }
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
                    <p>Product certification demonstrates commitment to safety, quality and performance standards set at an organisational, local or international level.</p>
            </div>

                

                <!-- 3. Certification Process -->
                <!-- Product Certification Process -->
<section class="py-5" style="background: #fff;">
    <div class="container">
        <div class="main_title centered upper mb-4 text-center">
            <h2 class="display-6 fw-bold">Product Certification Process</h2>
            <div class="section-divider"></div>
        </div>

        <!-- SVG diagram: tablet & desktop -->
        <div class="cert-process-svg d-none d-md-block">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 720" role="img" aria-label="Product certification process — six steps" preserveAspectRatio="xMidYMid meet">
                <title>Product Certification Process</title>
                <defs>
                    <marker id="cpAr" viewBox="0 0 10 10" refX="9" refY="5" markerWidth="8" markerHeight="8" orient="auto">
                        <path d="M0,0 L10,5 L0,10 Z" fill="#2B3388"/>
                    </marker>
                </defs>

                <!-- Decorative outer semicircles (each on the outward-facing side) -->
                <!-- Step 1: LEFT semicircle -->
                <path d="M 200 80 A 100 100 0 0 0 200 280" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="200" cy="80" r="5" fill="#2B3388"/>
                <circle cx="200" cy="280" r="5" fill="#2B3388"/>

                <!-- Step 2: TOP semicircle -->
                <path d="M 500 180 A 100 100 0 0 0 700 180" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="500" cy="180" r="5" fill="#2B3388"/>
                <circle cx="700" cy="180" r="5" fill="#2B3388"/>

                <!-- Step 3: RIGHT semicircle -->
                <path d="M 1000 80 A 100 100 0 0 1 1000 280" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="1000" cy="80" r="5" fill="#2B3388"/>
                <circle cx="1000" cy="280" r="5" fill="#2B3388"/>

                <!-- Step 4: RIGHT semicircle -->
                <path d="M 1000 440 A 100 100 0 0 1 1000 640" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="1000" cy="440" r="5" fill="#2B3388"/>
                <circle cx="1000" cy="640" r="5" fill="#2B3388"/>

                <!-- Step 5: BOTTOM semicircle -->
                <path d="M 500 540 A 100 100 0 0 1 700 540" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="500" cy="540" r="5" fill="#2B3388"/>
                <circle cx="700" cy="540" r="5" fill="#2B3388"/>

                <!-- Step 6: LEFT semicircle -->
                <path d="M 200 440 A 100 100 0 0 0 200 640" fill="none" stroke="#2B3388" stroke-width="2"/>
                <circle cx="200" cy="440" r="5" fill="#2B3388"/>
                <circle cx="200" cy="640" r="5" fill="#2B3388"/>

                <!-- Flow arrows -->
                <line x1="284" y1="180" x2="516" y2="180" stroke="#2B3388" stroke-width="3" marker-end="url(#cpAr)"/>
                <line x1="684" y1="180" x2="916" y2="180" stroke="#2B3388" stroke-width="3" marker-end="url(#cpAr)"/>
                <line x1="1000" y1="280" x2="1000" y2="440" stroke="#2B3388" stroke-width="3" marker-end="url(#cpAr)"/>
                <line x1="916" y1="540" x2="684" y2="540" stroke="#2B3388" stroke-width="3" marker-end="url(#cpAr)"/>
                <line x1="516" y1="540" x2="284" y2="540" stroke="#2B3388" stroke-width="3" marker-end="url(#cpAr)"/>

                <!-- Step 1 -->
                <circle cx="200" cy="180" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="200" y="160" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">1</text>
                <text x="200" y="188" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="200" dy="0">Application /</tspan>
                    <tspan x="200" dy="14">Quote / Planning</tspan>
                    <tspan x="200" dy="14">and Scheduling</tspan>
                </text>

                <!-- Step 2 -->
                <circle cx="600" cy="180" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="600" y="160" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">2</text>
                <text x="600" y="188" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="600" dy="0">Initial Assessment</tspan>
                    <tspan x="600" dy="14">(Process &amp; Systems</tspan>
                    <tspan x="600" dy="14">at Factory / Plant)</tspan>
                </text>

                <!-- Step 3 -->
                <circle cx="1000" cy="180" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="1000" y="160" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">3</text>
                <text x="1000" y="188" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="1000" dy="0">Sampling &amp; Testing</tspan>
                    <tspan x="1000" dy="14">of Product</tspan>
                    <tspan x="1000" dy="14">(Accredited Lab)</tspan>
                </text>

                <!-- Step 4 -->
                <circle cx="1000" cy="540" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="1000" y="520" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">4</text>
                <text x="1000" y="548" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="1000" dy="0">Submission to</tspan>
                    <tspan x="1000" dy="14">Certification Approval</tspan>
                    <tspan x="1000" dy="14">Committee (CAC)</tspan>
                </text>

                <!-- Step 5 -->
                <circle cx="600" cy="540" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="600" y="520" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">5</text>
                <text x="600" y="548" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="600" dy="0">Awarding of Permit /</tspan>
                    <tspan x="600" dy="14">Certification</tspan>
                    <tspan x="600" dy="14">for 3 years</tspan>
                </text>

                <!-- Step 6 -->
                <circle cx="200" cy="540" r="80" fill="#fff" stroke="#2B3388" stroke-width="7"/>
                <text x="200" y="520" text-anchor="middle" font-family="Arial, sans-serif" font-size="32" font-weight="700" fill="#2B3388">6</text>
                <text x="200" y="548" text-anchor="middle" font-family="Arial, sans-serif" font-size="11.5" font-weight="600" fill="#2B3388">
                    <tspan x="200" dy="0">Post-permit Inspection,</tspan>
                    <tspan x="200" dy="14">Audits &amp; Sampling,</tspan>
                    <tspan x="200" dy="14">Product Testing</tspan>
                </text>
            </svg>
        </div>

        <!-- Stacked list: mobile -->
        <ol class="prod-process-steps cert-process-list d-md-none">
            <li>Application, Quote, Planning &amp; Scheduling</li>
            <li>Initial Assessment (Process &amp; Systems at Factory / Plant)</li>
            <li>Sampling &amp; Testing of Product (Accredited Laboratory)</li>
            <li>Submission to Certification Approval Committee (CAC)</li>
            <li>Awarding of Permit / Certification (valid 3 years)</li>
            <li>Post-permit Inspection, Audits &amp; Sampling, Product Testing</li>
        </ol>
    </div>
</section>

            </div>
        </section>

        <!-- ESWASA Certified Products -->
        <section class="py-5" style="background: rgba(43, 51, 136, 0.04);">
            <div class="container">
                <h2 class="text-center" style="color: #2B3388; font-weight: 700;">ESWASA Certified Products</h2>
                <div class="section-divider mb-5"></div>
                <div class="row g-3">
                    <div class="col-6">
                        <img src="admin/uploads/image28.jpg" alt="Certified Product" class="certified-products-img" style="width: 100%; height: 280px; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img src="admin/uploads/image29.jpg" alt="Certified Product" class="certified-products-img" style="width: 100%; height: 280px; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img src="admin/uploads/image30.jpg" alt="Certified Product" class="certified-products-img" style="width: 100%; height: 280px; object-fit: cover;">
                    </div>
                    <div class="col-6">
                        <img src="admin/uploads/image31.jpg" alt="Certified Product" class="certified-products-img" style="width: 100%; height: 280px; object-fit: cover;">
                    </div>
                </div>
            </div>
        </section>

        <!-- Certified Product Producers -->
        <section class="py-5">
            <div class="container">
                <div class="producers-wrap">
                    <div class="cw-header">
                        <h3>Some of the Certified Product Producers</h3>
                        <div class="cw-divider"></div>
                    </div>
                    <?php
                    // Source: ESWASA Certified Clients List (May 2026) — product certification entries.
                    $producers = [
                        ['slug'=>'swazi-tiles',         'name'=>'Swazi Tiles Investments',                'product'=>'Concrete Roof Tiles',  'standard'=>'SZNS SANS 542:2020'],
                        ['slug'=>'lubombo-asiphile',    'name'=>'Lubombo Eco Products — Asiphile Bomake', 'product'=>'Chilli Sauce',         'standard'=>'SZNS CODEXSTAN 306:2015'],
                        ['slug'=>'lubombo-spice-girls', 'name'=>'Lubombo Eco Products — Spice Girls',     'product'=>'Chilli Sauce',         'standard'=>'SZNS CODEXSTAN 306:2015'],
                    ];
                    ?>
                    <div class="producer-grid">
                        <?php foreach ($producers as $p):
                            $logo = null;
                            foreach (['png','jpg','jpeg','webp','svg'] as $ext) {
                                $candidate = 'assets/img/clients/'.$p['slug'].'.'.$ext;
                                if (file_exists(__DIR__.'/'.$candidate)) { $logo = $candidate; break; }
                            }
                        ?>
                        <div class="producer-tile">
                            <?php if ($logo): ?>
                                <img src="<?= htmlspecialchars($logo) ?>" alt="<?= htmlspecialchars($p['name']) ?> logo">
                            <?php else: ?>
                                <div class="producer-wordmark"><?= htmlspecialchars($p['name']) ?></div>
                            <?php endif; ?>
                            <div class="producer-product"><?= htmlspecialchars($p['product']) ?></div>
                            <div class="producer-standard"><?= htmlspecialchars($p['standard']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title">Begin Your Product Certification Journey</h2>
                        <p class="cta-subtitle">Get your Product ESWASA Certified today!</p>
                        <a href="qoute_certification.php" class="btn-cta">Submit Application</a>
                        <a href="contact.php" class="btn-cta">Contact Certification Team</a>
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
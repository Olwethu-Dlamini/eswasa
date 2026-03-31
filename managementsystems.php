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
    <title>Management Systems Certification - ESWASA</title>
    <meta name="description" content="Accredited certification of ISO and food safety management systems by the Eswatini Standards Authority (ESWASA). SANAS-accredited, SADC-recognised.">
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
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen">
    <link rel="stylesheet" type="text/css" href="assets/css/extralayers.css" media="screen">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="includes/cta-section.css">

    <style>
        .cert-section {
            padding: 60px 0;
        }
        .section-title {
            color: #2e3191;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .cert-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
        }
        .cert-card h3 {
            color: #2e3191;
            margin-bottom: 15px;
        }
        .btn-cert {
            background: #2e3191;
            color: white;
            padding: 10px 25px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            border: none;
            font-weight: 600;
        }
        .btn-cert:hover {
            background: #1a1f71;
            color: white;
        }
        .scheme-card {
            background: white;
            border: 1px solid #e3e6ea;
            border-radius: 8px;
            padding: 25px 15px;
            text-align: center;
            height: 100%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            transition: all 0.2s ease;
        }
        .scheme-card:hover {
            border-color: #2e3191;
        }
        .scheme-card img {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }
        .certified-wrap {
            background: #fff;
            padding: 0;
            overflow: hidden;
        }
        .certified-wrap .cw-header {
            text-align: center;
            padding: 30px 20px 15px;
        }
        .certified-wrap .cw-header h3 {
            font-size: 1.3rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #222;
            margin-bottom: 10px;
        }
        .certified-wrap .cw-header .cw-divider {
            width: 60px;
            height: 3px;
            background: #2e3191;
            margin: 0 auto;
            border-radius: 2px;
            position: relative;
        }
        .certified-wrap .cw-header .cw-divider::after {
            content: '';
            width: 10px;
            height: 10px;
            border: 2px solid #2e3191;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
        }
        .certified-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }
        .certified-table thead th {
            background: #2e3191;
            color: #fff;
            font-weight: 600;
            padding: 13px 18px;
            border: none;
        }
        .certified-table thead th:nth-child(1) { width: 28%; }
        .certified-table thead th:nth-child(2) { width: 30%; text-align: center; }
        .certified-table thead th:nth-child(3) { width: 42%; }
        .certified-table tbody tr:nth-child(odd) {
            background: #fff;
        }
        .certified-table tbody tr:nth-child(even) {
            background: #e8f0f8;
        }
        .certified-table tbody td {
            padding: 12px 18px;
            border: none;
            color: #444;
            border-bottom: 1px solid #e0e6ed;
        }
        .certified-table tbody td:nth-child(2) {
            text-align: center;
        }
        .doc-card {
            display: block;
            background: #fff;
            border: 1px solid #e0e4ea;
            border-radius: 6px;
            padding: 25px 15px 20px;
            text-align: center;
            height: 100%;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .doc-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #2e3191;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        .doc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .doc-card:hover::after {
            transform: scaleX(1);
        }
        .doc-card:hover .doc-icon {
            border-radius: 8px 8px 0 8px;
            transform: scale(1.1) rotate(-5deg);
        }
        .doc-card:hover .doc-icon i {
            animation: bounce-down 0.5s ease;
        }
        @keyframes bounce-down {
            0%, 100% { transform: translateY(0); }
            40% { transform: translateY(5px); }
            60% { transform: translateY(-2px); }
        }
        .doc-icon {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }
        .doc-icon svg {
            width: 28px;
            height: 28px;
        }
        .doc-card p {
            color: #333;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            line-height: 1.4;
            margin: 0;
        }
        .benefits-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .benefits-list li {
            padding: 8px 0 8px 25px;
            position: relative;
            color: #333;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        .benefits-list li::before {
            content: '\f105';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            color: #2e3191;
        }
        .process-step {
            padding: 20px;
            text-align: center;
        }
        .step-number {
            background: #2e3191;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-weight: 600;
        }
        .standards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin: 40px 0;
        }
        .standard-block {
            background: white;
            padding: 25px;
            border: 1px solid #e3e6ea;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .standard-block:hover {
            box-shadow: 0 4px 12px rgba(46, 49, 145, 0.08);
            border-color: #2e3191;
        }
        .standard-code {
            color: #2e3191;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        .standard-name {
            font-weight: 600;
            margin-bottom: 12px;
            color: #111;
        }

        @media (max-width: 768px) {
            .standards-grid {
                grid-template-columns: 1fr;
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
    
    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('managementsystems', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                                <span property="itemListElement" typeof="ListItem">Management Systems</span>
                            </nav>
                            <h3 class="title">Management Systems Certification</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Introduction -->
        <section class="cert-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="cert-card">
                            <h2 class="section-title">Management Systems Certification</h2>
                            <p>Our certification services enable you to demonstrate that your products, processes, systems or services conform to national and international standards.</p>
                            <p>By gaining recognition from an international certification body relevant to your industry, you ensure your compliance with regulatory bodies and nurture a culture of continuous improvement.</p>
                            <p>Prove your commitment to quality through ESWASA's certification.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Schemes Offered -->
        <section class="cert-section bg-light">
            <div class="container">
                <h2 class="section-title text-center">Certification Schemes Offered</h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="iso9001.png" alt="SZNS ISO 9001">
                            <div class="standard-code">SZNS ISO 9001:2015</div>
                            <div class="standard-name">Quality Management Systems - Requirements</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="iso14001.png" alt="SZNS ISO 14001">
                            <div class="standard-code">SZNS ISO 14001:2015</div>
                            <div class="standard-name">Environmental Management Systems - Requirements with Guidance for Use</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="iso22000.png" alt="SZNS ISO 22000">
                            <div class="standard-code">SZNS ISO 22000:2018</div>
                            <div class="standard-name">Food Safety Management Systems - Requirements for any organization in the food chain</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="iso45001.png" alt="SZNS ISO 45001">
                            <div class="standard-code">SZNS ISO 45001:2018</div>
                            <div class="standard-name">Occupational Health and Safety Management Systems - Requirements with guidance for use</div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="scheme-card">
                            <img src="haccp.png" alt="HACCP">
                            <div class="standard-code">SZNS SANS 10330:2020</div>
                            <div class="standard-name">Hazard Analysis and Critical Control Point (HACCP)</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Accreditation & Portfolio side by side -->
        <section class="cert-section">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3>Accreditation</h3>
                            <p>Eswatini Standards Authority Management Systems Certification Services is accredited by the Southern African Development Community Accreditation Service (SADCAS).</p>
                            <p>Scopes: Quality Management Systems to ISO/IEC 17021-1:2015 and ISO/IEC 17021-3:2017 (Certification to ISO 9001:2015), IAF Codes 3, 12, 13 and 38</p>
                            <div class="mt-3">
                                <img src="admin/uploads/image12.png" alt="SADCAS Accreditation Logos" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="cert-card">
                            <h3>ESWASA’s Certifications Portfolio</h3>
                            <table class="table table-sm mb-3">
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS ISO 9001</td><td>Quality Management Systems</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS ISO 14001</td><td>Environmental Management Systems</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS ISO 22000</td><td>Food Safety Management Systems</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS ISO 45001</td><td>Occupational Health &amp; Safety Management Systems</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS SANS 10330</td><td>Hazard Analysis and Critical Control Points</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS SANS 542:2020</td><td>Concrete Roofing Tiles</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS CODEXSTAN 306:2015</td><td>Chilli Sauce Specifications</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS CODEXSTAN 12:1981</td><td>Honey</td></tr>
                                <tr><td class="fw-bold" style="color:#2e3191;">SZNS 006:2011</td><td>Liquid Soap Specification</td></tr>
                            </table>
                            <p class="text-muted" style="font-size:0.9rem;">More certifications can be added to the portfolio informed by interest indicated by clients.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ESWASA Certified Organisations -->
        <section class="cert-section" style="background: #eaf1f5;">
            <div class="container">
                <div class="certified-wrap">
                    <div class="cw-header">
                        <h3>Certified Organisations</h3>
                        <div class="cw-divider"></div>
                    </div>
                    <div class="table-responsive">
                        <table class="certified-table">
                            <thead>
                                <tr>
                                    <th>Name of Clients</th>
                                    <th>Standard</th>
                                    <th>Client Physical Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Eagles Nest</td><td>SZNS SANS 10330:2007</td><td>Portion 132, Malkerns, Eswatini.</td></tr>
                                <tr><td>Umbuluzi Farm Chickens</td><td>SZNS ISO 22000:2005</td><td>Portion 6 of farm 668 Mafutseni, Eswatini</td></tr>
                                <tr><td>MP Foods</td><td>SZNS SANS 10330:2007</td><td>Plot 645, 5th Avenue Matsapha Industrial, Eswatini</td></tr>
                                <tr><td>Galp Petroleum</td><td>SZNS ISO 9001:2015</td><td>King Sobhuza 11 Avenue, Industrial Site Matsapha</td></tr>
                                <tr><td>Swazi Wire Industries</td><td>SZNS ISO 9001:2015</td><td>1st Avenue, Matsapha Industrial Site</td></tr>
                                <tr><td>ASD Medicals</td><td>SZNS ISO 9001:2015</td><td>Lot 689 Tabankulu Street, Mvakaza Park, Matsapha Industrial Estate</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certification Documents -->
        <section class="cert-section">
            <div class="container">
                <div class="cw-header" style="padding: 0 0 30px;">
                    <h3>Certification Documents</h3>
                    <div class="cw-divider"></div>
                </div>
                <div class="row g-4">
                    <div class="col-lg col-md-4 col-6">
                        <a href="CER_RU_028 RULES FOR THE USE OF THE CERTIFICATION MARK.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #3b3583;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Rules for the Use of the Certification Mark</p>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #8ab030;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Procedure for Appeals Handling</p>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="CER_PR_006 PROCEDURE FOR COMPLAINTS HANDLING.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #1a8a9a;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Procedure for Complaints Handling</p>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="CER_PR_026 PROCEDURE FOR SUSPENSION WITHDRAWAL REDUCED SCOPE OF CERTIFICATION.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #e05a2b;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Procedure for Suspension/ Withdrawal/ Reduced Scope of Certification</p>
                        </a>
                    </div>
                    <div class="col-lg col-md-4 col-6">
                        <a href="Impartiality Policy - SWASA Certification.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #2e3191;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Impartiality Policy</p>
                        </a>
                    </div>
                </div>
                <div class="row g-4 mt-0">
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="CER_PR_020 PROCEDURE FOR MANAGEMENT SYSTEMS CERTIFICATION AUDITS.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #3b3583;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Procedure for Management Systems Certification Audits</p>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="CER_PR_014 GRANT OF CERTIFICATION PROCEDURE.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #1a8a9a;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Grant of Certification Procedure</p>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="CER_FO_ 028 CLIENT NOTICE OF CHANGES.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #e05a2b;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Client Notice of Changes</p>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <a href="CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf" target="_blank" class="doc-card">
                            <div class="doc-icon" style="background: #2e3191;"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17v2h16v-2" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p>Handling Requests for Information</p>
                        </a>
                    </div>
                </div>
            </div>
        </section>

<section class="cert-section py-5" style="background: #f4f6fa;">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Why Certify with ESWASA?</h2>
            <p class="section-subtitle">We provide reliable, efficient and results-driven certification services.</p>
        </div>
        <div class="text-center">
            <img src="whycertify.webp" alt="Why Certify with ESWASA - Demonstrate Competence, Prompt Support, Competitive Price, Integrated Approach, Committed, Local Expertise Global Standards" class="img-fluid" style="max-width: 900px;">
        </div>
    </div>
</section>

<!-- Certification Process -->
<section class="cert-process-section py-5 bg-light">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">How Certification Works</h2>
          
        </div>

        <!-- ROW 1 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 1<br><span>Initial Enquiry</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 2<br><span>Promotional Visit & Application</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 3<br><span>Quote Provided, Contract & Payment Commitment</span></div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 2 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 4<br><span>Stage 1 Initial Audit / Factory Evaluation & Sampling</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 5<br><span>Stage 2 Initial Audit / Product Testing</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle highlight">Certification<br>Decision</div>
        </div>

        <div class="process-divider"></div>

        <!-- ROW 3 -->
        <div class="process-row d-flex justify-content-center flex-wrap align-items-center">
            <div class="process-circle">STEP 6<br><span>Issue of Certificate</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 7<br><span>2 Surveillance Audits</span></div>
            <div class="process-arrow">›</div>
            <div class="process-circle">STEP 8<br><span>Recertification Audit</span></div>
        </div>

    </div>
</section>

<!-- Benefits of Certification -->
<section class="cert-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h2 class="section-title">Benefits of Certification</h2>
                <ul class="benefits-list">
                    <li>Improvement in Reputation and Credibility</li>
                    <li>Improvement in Customer Satisfaction</li>
                    <li>Improved Business Processes and Efficiency</li>
                    <li>Opens New Markets and Business Opportunities</li>
                    <li>Compliance with Regulations and Managing Risks</li>
                    <li>Employee Engagement through Accountability</li>
                    <li>Cost Savings After Waste Reduction</li>
                    <li>Greater Supplier Relationships</li>
                    <li>Framework for Continual Improvement</li>
                    <li>Competitive Advantage Over Non-Certified Businesses</li>
                </ul>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <img src="admin/uploads/image18.jpg" alt="Professional in suit" class="img-fluid" style="border-radius: 10px; max-height: 400px; object-fit: cover;">
            </div>
        </div>

    </div>
</section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title">Begin Your Certification Journey</h2>
                        <p class="cta-subtitle">Submit an application or request a preliminary consultation with our certification team.</p>
                        <a href="qoute_certification.php" class="btn-cta">Request Quote</a>
                        <a href="contact.php" class="btn-cta">Contact Us Now</a>
                        <a href="training-about.php" class="btn-cta">Attend Implementation Training</a>
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
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
    <meta http-equiv="x ua-compatible" content="ie=edge">
    <title>Standards Development - ESWASA</title>
    <meta name="description" content="ESWASA standards development under the Standards and Quality Act No. 10 of 2003 — Eswatini National Standards (SZNS), Technical Committees, standards purchase via the ESWASA estore, and the National Enquiry Point (WTO/TBT).">
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
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }

        /* bg-light → faint brand tint instead of grey */
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Primary buttons — same brand-blue styling used on training-about.php cards */
        .btn.btn-primary,
        .btn-primary {
            background-color: #2B3388 !important;
            color: #fff !important;
            border-color: #2B3388 !important;
            font-weight: 600;
            padding: 10px 22px;
            margin: 5px;
            border-radius: 4px;
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        .btn.btn-primary:hover,
        .btn-primary:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            border-color: rgba(43, 51, 136, 0.85) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(43, 51, 136, 0.20);
        }
        .btn.btn-primary:focus { box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.35); }
        /* Clean sections — borders over shadows (DIN/BIS restrained aesthetic) */
        .highlighted-section {
            background-color: rgba(43, 51, 136, 0.04);
            padding: 25px;
            margin: 30px 0;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
        }
        .highlighted-section h3 {
            color: #2B3388;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        /* === Infographic timeline for the 9 Standards Development stages === */
        .process-timeline {
            position: relative;
            max-width: 1000px;
            margin: 30px auto 0;
            padding: 10px 0 5px;
        }
        .process-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, rgba(43, 51, 136, 0) 0%, rgba(43, 51, 136, 0.25) 6%, rgba(43, 51, 136, 0.25) 94%, rgba(43, 51, 136, 0) 100%);
            transform: translateX(-50%);
        }
        .timeline-stage {
            position: relative;
            display: flex;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .timeline-stage[data-side="left"] { flex-direction: row-reverse; }
        .timeline-stage[data-side="left"] .stage-card { text-align: right; }

        .timeline-stage .stage-marker {
            flex: 0 0 96px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        .timeline-stage .stage-number {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #2B3388;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            font-family: Arial, sans-serif;
            border: 4px solid #fff;
            box-shadow: 0 0 0 2px rgba(43, 51, 136, 0.25);
        }

        .timeline-stage .stage-card {
            flex: 1;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 22px;
            margin: 0 22px;
            position: relative;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .timeline-stage:hover .stage-card {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .timeline-stage .stage-card::before {
            content: '';
            position: absolute;
            top: 22px;
            width: 0;
            height: 0;
            border-style: solid;
        }
        .timeline-stage[data-side="right"] .stage-card::before {
            left: -10px;
            border-width: 10px 10px 10px 0;
            border-color: transparent rgba(43, 51, 136, 0.15) transparent transparent;
        }
        .timeline-stage[data-side="left"] .stage-card::before {
            right: -10px;
            border-width: 10px 0 10px 10px;
            border-color: transparent transparent transparent rgba(43, 51, 136, 0.15);
        }

        .timeline-stage .stage-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .timeline-stage[data-side="left"] .stage-head { justify-content: flex-end; }
        .timeline-stage .stage-icon {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .timeline-stage .stage-title {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0;
            line-height: 1.3;
        }
        .timeline-stage .stage-description {
            color: rgba(43, 51, 136, 0.85);
            font-size: 0.93rem;
            line-height: 1.55;
            margin: 0;
        }
        .timeline-stage .stage-pill {
            display: inline-block;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 2px 9px;
            border-radius: 10px;
            margin-top: 8px;
        }


        /* Section anchor nav (pills at top of body) */
        .standards-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px;
            background-color: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }
        .standards-nav a {
            color: #2B3388;
            background-color: rgba(43, 51, 136, 0.06);
            border: 1px solid rgba(43, 51, 136, 0.18);
            padding: 6px 14px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .standards-nav a:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }

        .section-anchor { scroll-margin-top: 100px; }

        /* Canonical title pattern reused for new section headers */
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 14px auto 0;
            border-radius: 0;
        }

        /* Sector list — two-column on desktop */
        .sectors-list {
            columns: 2;
            column-gap: 32px;
            margin-top: 8px;
        }

        /* Technical Committee benefit cards */
        .tc-benefit-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 20px;
            height: 100%;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .tc-benefit-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .tc-benefit-card h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0 0 8px;
        }
        .tc-benefit-card p {
            color: rgba(43, 51, 136, 0.85);
            font-size: 0.95rem;
            line-height: 1.55;
            margin: 0;
        }

        /* Most Popular Standards — matches training-about.php card pattern */
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
        .add2cart_image img[src$=".svg"] {
            object-fit: contain;
            padding: 16px;
            background: #fff;
            height: 200px;
        }
        .hover-lift {
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(43, 51, 136, 0.12) !important;
        }
        .hover-lift:hover .add2cart_image img {
            transform: scale(1.04);
        }
        .add2cart_prod_name {
            color: #2B3388;
            text-decoration: none;
            font-size: 13px;
        }
        .add2cart_prod_name:hover { color: rgba(43, 51, 136, 0.75); }
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
            color: #fff !important;
        }
        .popular-code {
            display: block;
            color: #2B3388;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        /* Affiliations grid */
        .affiliation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-top: 14px;
        }
        .affiliation-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 14px 14px;
            text-align: center;
            text-decoration: none;
            color: #2B3388;
            transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease;
        }
        .affiliation-tile:hover {
            border-color: #2B3388;
            color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
            text-decoration: none;
        }
        .affiliation-tile img {
            max-height: 56px;
            max-width: 100%;
            width: auto;
            object-fit: contain;
        }
        .affiliation-tile .affiliation-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #2B3388;
        }
        .affiliation-tile .affiliation-full {
            font-size: 0.75rem;
            color: rgba(43, 51, 136, 0.7);
            line-height: 1.35;
        }

        /* Information Centre image */
        .info-centre-img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 12px auto 0;
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.15);
        }

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            /* Timeline collapses to single side on mobile */
            .process-timeline::before {
                left: 32px;
                transform: none;
            }
            .timeline-stage,
            .timeline-stage[data-side="left"] {
                flex-direction: row;
            }
            .timeline-stage[data-side="left"] .stage-card,
            .timeline-stage[data-side="left"] .stage-head {
                text-align: left;
                justify-content: flex-start;
            }
            .timeline-stage .stage-marker {
                flex: 0 0 64px;
            }
            .timeline-stage .stage-number {
                width: 48px;
                height: 48px;
                font-size: 1.15rem;
                border-width: 3px;
            }
            .timeline-stage .stage-card {
                margin: 0 0 0 16px;
                padding: 14px 16px;
            }
            .timeline-stage[data-side="left"] .stage-card::before,
            .timeline-stage[data-side="right"] .stage-card::before {
                left: -10px;
                right: auto;
                border-width: 10px 10px 10px 0;
                border-color: transparent rgba(43, 51, 136, 0.15) transparent transparent;
            }
            .timeline-stage .stage-title { font-size: 0.98rem; }
            .timeline-stage .stage-description { font-size: 0.88rem; }
            .standards-nav { padding: 10px; }
            .standards-nav a { font-size: 0.8rem; padding: 5px 10px; }
            .sectors-list { columns: 1; }
            .tc-benefit-card { padding: 14px 16px; }
            .display-6 { font-size: 1.55rem !important; }
            .add2cart_image img { max-height: 160px; }
            .add2cart_image img[src$=".svg"] { height: 160px; padding: 12px; }
            .affiliation-grid { grid-template-columns: repeat(2, 1fr); }
            .affiliation-tile { padding: 14px 10px 12px; }
            .affiliation-tile img { max-height: 44px; }
            .affiliation-tile .affiliation-full { font-size: 0.7rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('standards', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Development</span>
                            </nav>
                            <h3 class="title">Standards Development</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- Section nav (anchor-jump) -->
                <nav class="standards-nav mb-4">
                    <a href="#standards-development">Standards Development</a>
                    <a href="#technical-committees">Technical Committees &amp; Work Programmes</a>
                    <a href="#purchase-standards">Purchase Standards</a>
                    <a href="#information-centre">Information Centre</a>
                </nav>

                <!-- ============ STANDARDS DEVELOPMENT ============ -->
                <div id="standards-development">

                    <!-- 1. About Standards Development -->
                    <div class="highlighted-section">
                        <h3>Standards Development</h3>
                        <p>
                            The Standards Development Unit, in accordance with the <strong>Standards and Quality Act No. 10 of 2003, as amended</strong>, is mandated to facilitate the development of standards for the different sectors of the economy, publish, and maintain the <strong>Eswatini National Standards (SZNS)</strong> and related normative publications serving the standardisation needs for Eswatini.
                        </p>
                        <p>
                            These standards are developed to help industry produce quality products that meet the expectations of consumers and comply with environmental, health and safety regulations.
                        </p>

                        <h4 class="mt-4">Industry Sectors</h4>
                        <p>ESWASA has developed standards across the following sectors:</p>
                        <ul class="sectors-list">
                            <li>Food and Agriculture</li>
                            <li>Building and Construction</li>
                            <li>Information Communication Technology</li>
                            <li>Chemicals and Textiles</li>
                            <li>Electrical and Mechanical Engineering</li>
                            <li>Health and Safety</li>
                            <li>Environment</li>
                            <li>General and Services</li>
                        </ul>
                        <p class="small text-muted mt-2">
                            See the full <a href="purchase.php" style="color:#2B3388; text-decoration:underline;">Standards Catalogue</a>.
                        </p>
                    </div>

                    <!-- What is a Standard -->
                    <div class="highlighted-section">
                        <h3>What is a Standard?</h3>
                        <p>
                            Standards are the outcome of a consultative process involving the experience and knowledge of interested parties — key industry stakeholders, consumers and their relevant associations, academic and research institutions, government ministries and regulators — who are brought together to agree on the technical contents of a standard through <strong>consensus</strong>.
                        </p>
                        <p>
                            They are developed on a need basis, and the need for a new standard can be initiated by industry stakeholders, an individual, a manufacturer, or a government institution through a standards proposal.
                        </p>
                        <p>
                            Standards are designed for <strong>voluntary use</strong> and do not impose any regulations. However, laws and regulations may reference certain standards and make compliance with them mandatory.
                        </p>
                    </div>

                    <!-- Benefits -->
                    <div class="highlighted-section">
                        <h3>Benefits of Standards</h3>
                        <ul>
                            <li>Increased profitability through cost reduction and increased sales.</li>
                            <li>Ensure consumers are protected from hazards to their health and safety.</li>
                            <li>Inspire trust and consumer confidence in your business.</li>
                            <li>Assist businesses in meeting regulatory requirements and provide access to national and international markets.</li>
                            <li>Create a competitive advantage by improving the quality of goods and services.</li>
                        </ul>
                    </div>

                    <!-- Process -->
                    <div class="highlighted-section">
                        <h3>Standards Development Process</h3>
                        <p>The Standards Development Process follows 9 stages — from an early idea to a published Eswatini National Standard.</p>

                        <?php
                        $stages = [
                            ['n'=>'0', 'icon'=>'fa-lightbulb',       'title'=>'Preliminary Stage',
                             'desc'=>'An opportunity to introduce proposals (Preliminary Work Items, or PWIs) for projects that are not yet mature enough for processing — for example, an emerging-technology standard for which no reference document yet exists.'],
                            ['n'=>'1', 'icon'=>'fa-file-alt',        'title'=>'Proposal Stage',
                             'desc'=>'ESWASA / SAC receives — and accepts or rejects — a <strong>New Work Item Proposal (NWIP)</strong> for: a new standard, a new part of an existing standard, a revision, or an amendment.'],
                            ['n'=>'2', 'icon'=>'fa-pencil-ruler',    'title'=>'Preparatory Stage',
                             'desc'=>'Preparation of a Working Draft (WD), if necessary. The stage concludes when the first Committee Draft (CD) is available for submission to the full Technical Committee or Sub-Committee.'],
                            ['n'=>'3', 'icon'=>'fa-users',           'title'=>'Committee Stage',
                             'desc'=>'Comments from members are received, consensus is built and voting is requested for progression of the draft to the Enquiry stage. The cycle may repeat if the CD needs further significant development.'],
                            ['n'=>'4', 'icon'=>'fa-bullhorn',        'title'=>'Enquiry Stage',
                             'desc'=>'Comments are sought from individuals or organisations not participating directly in the ESWASA committee — i.e. from the <strong>wider public</strong>. Availability of the text for enquiry is notified to the appropriate authorities.'],
                            ['n'=>'5', 'icon'=>'fa-comment-dots',    'title'=>'Disposal of Comments Stage',
                             'desc'=>'Within <strong>30 days</strong> of the end of the voting period, the Committee Secretariat prepares a report indicating comments received and the response on each. Every attempt is made to resolve negative votes.', 'pill'=>'within 30 days'],
                            ['n'=>'6', 'icon'=>'fa-clipboard-check', 'title'=>'Approval Stage',
                             'desc'=>'The ESWASA Standards Approvals Committee (SAC) reviews the Final Draft Standard (FDS) on technical grounds and determines whether it may advance to publication.'],
                            ['n'=>'7', 'icon'=>'fa-gavel',           'title'=>'Endorsement Stage',
                             'desc'=>'Final approval as an Eswatini National Standard rests with the <strong>ESWASA Council</strong>. The availability of the approved standard is notified in the <strong>Government Gazette</strong>.', 'pill'=>'≤ 30 days'],
                            ['n'=>'8', 'icon'=>'fa-book-open',       'title'=>'Publication Stage',
                             'desc'=>'Once endorsed by the ESWASA Council, the text is ready for publication as a published Eswatini National Standard (SZNS).', 'pill'=>'≤ 60 days'],
                        ];
                        ?>
                        <div class="process-timeline">
                            <?php foreach ($stages as $i => $s):
                                $side = ($i % 2 === 0) ? 'right' : 'left';
                            ?>
                                <div class="timeline-stage" data-side="<?= $side ?>">
                                    <div class="stage-marker">
                                        <div class="stage-number"><?= htmlspecialchars($s['n']) ?></div>
                                    </div>
                                    <div class="stage-card">
                                        <div class="stage-head">
                                            <span class="stage-icon"><i class="fas <?= htmlspecialchars($s['icon']) ?>"></i></span>
                                            <h4 class="stage-title"><?= htmlspecialchars($s['title']) ?></h4>
                                        </div>
                                        <p class="stage-description"><?= $s['desc'] ?></p>
                                        <?php if (!empty($s['pill'])): ?>
                                            <span class="stage-pill"><i class="far fa-clock me-1"></i><?= htmlspecialchars($s['pill']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Proposal Form -->
                    <div class="highlighted-section">
                        <h3>Submitting a Standards Proposal</h3>
                        <p>To propose a new standard or revision of an existing SZNS, please:</p>
                        <ol>
                            <li>Complete the official <strong>Standards Development Proposal Form</strong> — either the downloadable form below, or via the online portal: <a href="https://tc.swasa.co.sz/proposal.php" target="_blank" rel="noopener" style="color:#2B3388; text-decoration:underline; font-weight:600;">tc.swasa.co.sz/proposal.php</a></li>
                            <li>Email the completed form to <a href="mailto:standards@eswasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">standards@eswasa.co.sz</a> or <a href="mailto:info@swasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">info@swasa.co.sz</a></li>
                        </ol>
                        <div class="mt-3">
                            <a href="admin/uploads/standards_proposal_form.pdf" class="btn btn-primary" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>Download Proposal Form (PDF)
                            </a>
                        </div>
                        <p class="mt-3 small">
                            <strong>Note:</strong> Proposals should include the title and scope of the standard, socio-economic impacts, intended uses, and justification. Priority is given to standards supporting national priorities (e.g. food security, infrastructure, MSME competitiveness, emerging technologies).
                        </p>
                    </div>
                </div>

                <!-- ============ TECHNICAL COMMITTEES & WORK PROGRAMMES ============ -->
                <div id="technical-committees" class="section-anchor">
                    <h2 class="display-6 fw-bold text-center mt-5 mb-3">Technical Committees &amp; Work Programmes</h2>
                    <div class="section-divider mb-4"></div>

                    <div class="highlighted-section">
                        <h3>About Technical Committees (TCs)</h3>
                        <p>
                            Technical Committees (TCs) are the cornerstone of the ESWASA standards development process. They are composed of volunteers who are qualified in the subject matter and represent a balance of interested parties — including producers, users, consumers, government, and other relevant stakeholders.
                        </p>
                        <p>
                            TCs are responsible for developing, maintaining, and revising Eswatini National Standards (SZNS) within their specific technical areas. They ensure that standards are developed through a consensus-based process, reflecting the needs and expertise of all relevant parties.
                        </p>
                    </div>

                    <div class="highlighted-section">
                        <h3>Key Benefits of Joining an ESWASA TC</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="tc-benefit-card">
                                    <h4>Market Expansion</h4>
                                    <p>Contribute to standards that facilitate trade and regional integration. Participation ensures your products and services meet international benchmarks, opening doors to new domestic and global markets.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="tc-benefit-card">
                                    <h4>Operational Optimisation</h4>
                                    <p>Gain early access to best practices in Quality &amp; Management Systems (e.g. ISO 9001, ISO 45001). Implement efficient, safety-focused processes before they become mandatory — reducing waste and costs.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="tc-benefit-card">
                                    <h4>Customer Trust Building</h4>
                                    <p>Shape standards for critical areas like Food Safety and Product Quality. Demonstrating commitment to Eswatini National Standards (SZNS) strengthens brand reputation and consumer confidence.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="tc-benefit-card">
                                    <h4>Regulatory Compliance</h4>
                                    <p>Influence the technical requirements that may become government regulations. By contributing, you ensure standards are practical and achievable for your sector, easing future compliance burdens.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="highlighted-section">
                        <h3>Apply to be a TC Member</h3>
                        <p>
                            Becoming a member of an ESWASA Technical Committee is a great way to contribute to the development of standards that impact your industry and society. Members gain valuable insights, network with experts, and help shape the future of their technical field.
                        </p>
                        <p>
                            <strong>Eligibility:</strong> Membership is open to Eswatini citizens with relevant expertise and a commitment to the standards development process.
                        </p>
                        <p>
                            Submit completed applications to <a href="mailto:info@swasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">info@swasa.co.sz</a> or <a href="mailto:standards@eswasa.co.sz" style="color:#2B3388; text-decoration:underline; font-weight:600;">standards@eswasa.co.sz</a>.
                        </p>
                        <div class="mt-3">
                            <a href="https://tc.swasa.co.sz/" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-external-link-alt me-2"></i>Visit the TC Portal</a>
                            <a href="tcp.php" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Register Interest</a>
                        </div>
                    </div>

                    <div class="highlighted-section">
                        <h3>ESWASA Work Programmes</h3>
                        <p>
                            The ESWASA Work Programme details all current and scheduled standards development and revision projects. The programme is derived from national needs assessments and stakeholder requests, ensuring the standards developed align with Eswatini's economic and regulatory priorities. Interested stakeholders are invited to review the programme and provide feedback.
                        </p>
                        <div class="mt-3">
                            <a href="https://tc.swasa.co.sz/work-programme.php" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-calendar-alt me-2"></i>View Work Programme</a>
                        </div>
                    </div>
                </div>

                <!-- ============ PURCHASE STANDARDS ============ -->
                <div id="purchase-standards" class="section-anchor">
                    <h2 class="display-6 fw-bold text-center mt-5 mb-3">Purchase Standards</h2>
                    <div class="section-divider mb-4"></div>

                    <div class="highlighted-section">
                        <h3>Standards Sales</h3>
                        <p>
                            Purchase your SZNS Standards through the ESWASA office or conveniently online via the ESWASA estore.
                        </p>
                        <p>
                            ESWASA sells SZNS as well as related documents and specifications. Our services extend to sourcing other international and regional standards for you, such as <strong>ISO, IEC, ARSO, SADCSTAN, SANS and ASTM</strong>.
                        </p>
                        <div class="mt-3">
                            <a href="https://estore.swasa.co.sz/" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-shopping-cart me-2"></i>Visit the ESWASA estore</a>
                            <a href="purchase.php" class="btn btn-primary"><i class="fas fa-book me-2"></i>View Standards Catalogue</a>
                        </div>
                    </div>

                    <!-- Most Popular Standards (training-page card pattern) -->
                    <div class="highlighted-section">
                        <h3>Most Popular Standards</h3>
                        <p>The standards most frequently purchased from ESWASA across our certification client base:</p>
                        <?php
                        $popular = [
                            ['code'=>'SZNS ISO 9001:2015',  'name'=>'Quality Management Systems',           'img'=>'admin/uploads/certificate-iso-9001-colored.svg'],
                            ['code'=>'SZNS ISO 14001:2015', 'name'=>'Environmental Management Systems',     'img'=>'admin/uploads/certificate-iso-14001-colored.svg'],
                            ['code'=>'SZNS ISO 22000:2018', 'name'=>'Food Safety Management Systems',       'img'=>'admin/uploads/course-iso-22000.svg'],
                            ['code'=>'SZNS ISO 45001:2018', 'name'=>'Occupational Health &amp; Safety',     'img'=>'admin/uploads/certificate-iso-45001-colored.svg'],
                            ['code'=>'SZNS ISO 19011:2018', 'name'=>'Guidelines for Auditing Management Systems', 'img'=>'admin/uploads/course-iso-19011.svg'],
                            ['code'=>'SZNS ISO 27001',      'name'=>'Information Security Management Systems',   'img'=>'admin/uploads/certificate-iso-27001-colored.svg'],
                        ];
                        ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center mt-2">
                            <?php foreach ($popular as $s): ?>
                            <div class="col">
                                <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift h-100">
                                    <div class="add2cart_image">
                                        <img src="<?= htmlspecialchars($s['img']) ?>" alt="<?= htmlspecialchars($s['code']) ?> — <?= htmlspecialchars($s['name']) ?>" class="img-fluid rounded-top">
                                    </div>
                                    <div class="add2cart_details p-4">
                                        <div class="con_cont">
                                            <span class="popular-code"><?= htmlspecialchars($s['code']) ?></span>
                                            <a class="add2cart_prod_name d-block mb-3 fw-bold"><?= $s['name'] ?></a>
                                            <a href="https://estore.swasa.co.sz/" target="_blank" rel="noopener" class="add2cart_btn btn btn-primary btn-sm">
                                                <i class="fas fa-shopping-cart me-1"></i>Purchase
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="small text-muted mt-4 mb-0 text-center">
                            For the complete list, browse the <a href="purchase.php" style="color:#2B3388; text-decoration:underline; font-weight:600;">Standards Catalogue</a> or visit the <a href="https://estore.swasa.co.sz/" target="_blank" rel="noopener" style="color:#2B3388; text-decoration:underline; font-weight:600;">estore</a>.
                        </p>
                    </div>

                    <!-- Our Affiliations -->
                    <div class="highlighted-section">
                        <h3>Our Affiliations</h3>
                        <p>
                            ESWASA collaborates with international and regional standards bodies to source standards and harmonise national requirements with global best practice.
                        </p>
                        <?php
                        $affiliations = [
                            ['name'=>'ISO',      'full'=>'International Organization for Standardization', 'img'=>'admin/uploads/iso.png',  'url'=>'https://www.iso.org'],
                            ['name'=>'IEC',      'full'=>'International Electrotechnical Commission',      'img'=>'admin/uploads/iec.png',  'url'=>'https://www.iec.ch'],
                            ['name'=>'ARSO',     'full'=>'African Organisation for Standardisation',       'img'=>'admin/uploads/arso.png', 'url'=>'#'],
                            ['name'=>'SADCSTAN', 'full'=>'SADC Cooperation in Standardization',            'img'=>'assets/img/sadcstan.jpg','url'=>'#'],
                            ['name'=>'SANS / SABS','full'=>'South African National Standards',            'img'=>'assets/img/SABS.png',    'url'=>'https://www.sabs.co.za'],
                            ['name'=>'ASTM',     'full'=>'ASTM International',                             'img'=>'admin/uploads/astm.png', 'url'=>'https://www.astm.org'],
                        ];
                        ?>
                        <div class="affiliation-grid">
                            <?php foreach ($affiliations as $a): ?>
                                <a class="affiliation-tile" href="<?= htmlspecialchars($a['url']) ?>" <?= $a['url'] === '#' ? 'title="Link coming soon"' : 'target="_blank" rel="noopener"' ?>>
                                    <img src="<?= htmlspecialchars($a['img']) ?>" alt="<?= htmlspecialchars($a['name']) ?> logo">
                                    <div class="affiliation-name"><?= htmlspecialchars($a['name']) ?></div>
                                    <div class="affiliation-full"><?= htmlspecialchars($a['full']) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ============ INFORMATION CENTRE ============ -->
                <div id="information-centre" class="section-anchor">
                    <h2 class="display-6 fw-bold text-center mt-5 mb-3">Information Centre</h2>
                    <div class="section-divider mb-4"></div>

                    <div class="highlighted-section">
                        <h3>About the Information Centre</h3>
                        <p>The Information Unit holds the database of:</p>
                        <ul>
                            <li>Information on technical specifications to manufacturers and traders.</li>
                            <li>Information relating to national, regional and international standards.</li>
                            <li>Information to exporters and importers on the technical regulations and requirements of importing and exporting countries.</li>
                            <li>Information on ESWASA Certified Products and Services.</li>
                        </ul>
                        <p class="mt-3">
                            Students, researchers, industry professionals and the general public are welcome to make use of our centre.
                        </p>
                    </div>

                    <div class="highlighted-section">
                        <h3>AfCFTA Annex 6 — Technical Barriers to Trade</h3>
                        <p>
                            The African Continental Free Trade Area (AfCFTA) Annex 6 on Technical Barriers to Trade facilitates trade through cooperation in the areas of <strong>standards, technical regulations, conformity assessment, accreditation and metrology</strong>.
                        </p>
                    </div>

                    <div class="highlighted-section">
                        <h3>National Enquiry Point (WTO/TBT)</h3>
                        <p>
                            ESWASA serves as the National Enquiry Point (NEP) for Technical Barriers to Trade (WTO/TBT) information. ESWASA receives notifications on technical regulations from the WTO and disseminates them to stakeholders.
                        </p>
                        <img src="assets/img/WTO.png" alt="World Trade Organization — Technical Barriers to Trade" class="info-centre-img" style="max-width: 480px;">
                    </div>
                </div>

            </div>
        </section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title">Get Involved in Standards Development</h2>
                        <p class="cta-subtitle">Contact our Standards Unit, register for a Technical Committee, or purchase a standard online.</p>
                        <a href="contact.php" class="btn-cta">Contact Standards Unit</a>
                        <a href="#technical-committees" class="btn-cta">Join a Technical Committee</a>
                        <a href="https://estore.swasa.co.sz/" target="_blank" rel="noopener" class="btn-cta">Visit estore</a>
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
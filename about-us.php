<?php
// about-us.php — pulls content from page_content table
require_once('includes/db_connect.php');

// ── Load all about_* keys in one query ───────────────────────
$keys = [
    'about_intro', 'about_vision', 'about_mission', 'about_history',
    'about_val_transparency', 'about_val_people', 'about_val_responsiveness',
    'about_val_innovation', 'about_val_professionalism',
    'about_img_vision', 'about_img_mission', 'about_img_team', 'about_img_banner',
    'about_breadcrumb_title', 'about_breadcrumb_bg'
];
$placeholders = implode(',', array_fill(0, count($keys), '?'));
$types = str_repeat('s', count($keys));

$stmt = $conn->prepare("SELECT page_key, content FROM page_content WHERE page_key IN ($placeholders)");
$stmt->bind_param($types, ...$keys);
$stmt->execute();
$res = $stmt->get_result();
$pc = [];
while ($row = $res->fetch_assoc()) {
    $pc[$row['page_key']] = $row['content'];
}
$stmt->close();

// Fallback defaults
$defaults = [
    'about_intro'              => 'The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023. ESWASA is a national standards body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.',
    'about_vision'             => 'A competitive and Sustainable Trade Environment informed by effective standardization and conformity assurance in Eswatini.',
    'about_mission'            => 'We provide and promote internationally recognized quality standards and conformity assessment services to improve business performance, minimize health and safety risks and ensure environmental integrity in collaboration with regulators.',
    'about_history'            => "The Eswatini Standards Authority (ESWASA) is a parastatal organisation within the Ministry of Commerce, Industry, and Trade established by the Eswatini government under the Standards and Quality Act (10) of 2003, amended in 2023.\n\nESWASA is mandated by this Act to promote quality and standards in local businesses, government, and industry.",
    'about_val_transparency'   => 'We conduct our business with honesty, openness, and integrity in all standardization processes.',
    'about_val_people'         => 'We prioritize people—building trust, collaboration, and mutually beneficial relationships with stakeholders.',
    'about_val_responsiveness' => 'We act promptly and effectively to meet the evolving needs of our customers, markets, and partners.',
    'about_val_innovation'     => 'We embrace creative thinking and continuous improvement to enhance our standards and services.',
    'about_val_professionalism'=> 'We uphold the highest standards of competence, reliability, and accountability in all our operations.',
    'about_img_vision'          => 'assets/img/maguga.jpg',
    'about_img_mission'         => 'assets/img/vision.jpg',
    'about_img_team'            => 'assets/img/blog_thumb10.jpg',
    'about_img_banner'          => 'assets/img/blog_thumb11.jpg',
    'about_breadcrumb_title'    => 'Who We Are',
    'about_breadcrumb_bg'       => 'assets/img/bg.png',
];
foreach ($defaults as $k => $v) {
    if (empty($pc[$k])) $pc[$k] = $v;
}

// Helper: render history paragraphs
function render_paragraphs($text) {
    $paras = preg_split('/\r?\n\r?\n/', trim($text));
    $out = '';
    foreach ($paras as $p) {
        $p = trim($p);
        if ($p !== '') $out .= '<p>' . nl2br(htmlspecialchars($p)) . '</p>';
    }
    return $out ?: '<p>' . htmlspecialchars($text) . '</p>';
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= htmlspecialchars($pc['about_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Learn about the Eswatini Standards and Quality Assurance Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
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
        .text-muted {
            color: rgba(43, 51, 136, 0.7) !important;
        }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        .breadcrumb-separator i { color: #fff !important; }

        /* Section heading divider */
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }

        /* Page section titles — wider gap above vm-card h3 for clearer hierarchy */
        .about-eswasa-area h2.title,
        .container h2.fw-bold {
            font-size: 2.15rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin: 0;
        }

        /* Restrict big images to content width */
        .main-area img { max-width: 100%; height: auto; }

        /* Banner image wrapper */
        .banner-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(43, 51, 136, 0.06);
        }
        .banner-wrapper img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        /* Core Values — wheel layout (cards wrap around small center picture) */
        .values-diagram-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 0;
        }
        /* Pull side cards in toward the centre image on desktop so the wheel reads as one cohesive group */
        @media (min-width: 992px) {
            .values-diagram-container > .row > .col-lg-3:first-child .value-card-custom {
                margin-left: auto;
                margin-right: 0;
            }
            .values-diagram-container > .row > .col-lg-3:nth-child(3) .value-card-custom {
                margin-left: 0;
                margin-right: auto;
            }
        }
        img.values-center-image {
            max-width: 300px !important;
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            margin: 0 auto;
            display: block;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.20);
        }
        @media (max-width: 991.98px) {
            img.values-center-image { max-width: 240px !important; margin: 16px auto; }
        }
        @media (max-width: 575.98px) {
            img.values-center-image { max-width: 200px !important; }
        }

        /* Team figure caption */
        .team-figure { margin: 0; }
        .team-figure figcaption {
            margin-top: 14px;
            text-align: center;
            color: rgba(43, 51, 136, 0.75);
            font-size: 14px;
            font-style: italic;
            line-height: 1.5;
        }

        .value-card-custom {
            background: #fff;
            border-radius: 50%;
            aspect-ratio: 1 / 1;
            max-width: 280px;
            width: 100%;
            margin: 0 auto;
            padding: 32px 34px;
            box-shadow: 0 2px 8px rgba(43, 51, 136, 0.10);
            text-align: center;
            border: 1px solid rgba(43, 51, 136, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .value-card-custom:hover {
            border-color: rgba(43, 51, 136, 0.45);
            box-shadow: 0 10px 24px rgba(43, 51, 136, 0.18);
            transform: translateY(-2px);
        }
        .value-card-custom h4 {
            font-weight: 700;
            font-size: 17px;
            margin: 0 0 8px;
            color: #2B3388;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        .value-card-custom p {
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            color: rgba(43, 51, 136, 0.78);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .value-icon-circle {
            width: 76px;
            height: 76px;
            background: rgba(43, 51, 136, 0.10);
            color: #2B3388;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            flex-shrink: 0;
        }
        .value-icon-circle svg {
            width: 44px;
            height: 44px;
        }
        .value-icon-circle i.fa-icon,
        .value-icon-circle i.fas,
        .value-icon-circle .fa-stack {
            font-size: 40px;
            line-height: 1;
            color: #2B3388;
        }
        .value-icon-circle .fa-stack {
            width: 1.5em;
            height: 1.5em;
            font-size: 22px;
        }
        .value-icon-circle .fa-stack .fa-globe {
            font-size: 0.85em;
            margin-top: -0.35em;
        }

        /* Section backgrounds — bg_color3 (affiliations) + Bootstrap bg-light (accreditation) */
        .bg_color3 { background-color: rgba(43, 51, 136, 0.04) !important; }
        section.bg-light { background-color: #fff !important; }

        /* Affiliations / Accreditation sliders */
        .affiliations-slider {
            overflow: hidden;
            white-space: nowrap;
            padding: 20px 0;
            -webkit-overflow-scrolling: touch;
        }
        .slider-track {
            display: flex;
            min-width: 100%;
            animation: scroll 20s linear infinite;
            -webkit-animation: scroll 20s linear infinite;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }
        /* Pause both sliders on hover — matches services.php */
        .affiliations-slider:hover .slider-track {
            animation-play-state: paused;
            -webkit-animation-play-state: paused;
        }
        .slider-item {
            width: 280px;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            padding: 0 15px;
        }
        .logo-card-fixed {
            width: 250px;
            height: 150px;
            background: #fff;
            padding: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.06);
            border: 1px solid rgba(43, 51, 136, 0.10);
            transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
        }
        .logo-card-fixed:hover {
            transform: translateY(-6px) scale(1.04);
            border-color: rgba(43, 51, 136, 0.45);
            box-shadow: 0 10px 24px rgba(43, 51, 136, 0.18);
        }
        .logo-card-fixed img {
            transition: transform .25s ease;
        }
        .logo-card-fixed:hover img {
            transform: scale(1.05);
        }
        .logo-card-fixed img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* Vision & Mission infographic cards */
        .vm-section { background: #fff; }
        .vm-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            padding: 40px 36px;
            height: 100%;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .vm-card:hover {
            border-color: rgba(43, 51, 136, 0.40);
            box-shadow: 0 6px 16px rgba(43, 51, 136, 0.10);
        }
        .vm-icon {
            width: 64px;
            height: 64px;
            color: #2B3388;
            margin: 0 0 26px;
        }
        .vm-icon svg { width: 100%; height: 100%; }
        .vm-card h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 20px;
            margin: 0 0 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid #2B3388;
            display: inline-block;
            line-height: 1.2;
        }
        .vm-card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.7;
            margin: 0;
            font-weight: 400;
        }

        /* Info section (History only now) */
        .info-section {
            background: rgba(43, 51, 136, 0.04);
            padding: 28px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.10);
            box-shadow: none;
        }
        .info-section h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 18px;
            margin: 0 0 14px;
        }
        .info-section p {
            color: #2B3388;
            font-size: 16px;
            line-height: 1.65;
            margin: 0 0 10px;
        }
        .info-section p:last-child { margin-bottom: 0; }
        .info-section p strong { font-weight: 700; }

        /* Bootstrap `lead` class — scale relative to our 12px base */
        body .lead { font-size: 18px; line-height: 1.7; font-weight: 400; }

        /* Slider keyframes — translate by exactly half so the duplicated set loops seamlessly */
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @-webkit-keyframes scroll {
            0% { -webkit-transform: translateX(0); }
            100% { -webkit-transform: translateX(-50%); }
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .banner-wrapper img { height: 280px; }
            .info-section { padding: 22px; }
            .about-eswasa-area h2.title,
            .container h2.fw-bold { font-size: 1.8rem; }
            .vm-card { padding: 32px 28px; }
        }
        @media (max-width: 767.98px) {
            .banner-wrapper img { height: 200px !important; }
            .logo-card-fixed { width: 180px !important; height: 110px !important; padding: 14px; }
            .slider-item { width: 200px !important; padding: 0 10px; }
            /* Mobile keyframes also use -50% — content is still duplicated 1:1 so half is one full set */
            .info-section h3 { font-size: 17px !important; }
            .info-section p { font-size: 15px; }
            .about-eswasa-area h2.title,
            .container h2.fw-bold { font-size: 1.55rem; }
            .display-6 { font-size: 1.6rem !important; }
            .value-card-custom { max-width: 240px; padding: 28px 30px; }
            .value-card-custom h4 { font-size: 16px; margin-bottom: 8px; }
            .value-card-custom p { font-size: 13px; }
            .value-icon-circle { width: 60px; height: 60px; margin-bottom: 10px; }
            .value-icon-circle svg { width: 34px; height: 34px; }
            body .lead { font-size: 16px; }
            .vm-card { padding: 28px 22px; }
            .vm-icon { width: 52px; height: 52px; margin-bottom: 20px; }
            .vm-card h3 { font-size: 18px; margin-bottom: 14px; }
            .vm-card p { font-size: 14px; }
        }
        @media (max-width: 575.98px) {
            .info-section { padding: 18px; }
            .value-card-custom { max-width: 210px; padding: 24px 26px; }
            .value-card-custom h4 { font-size: 14px; }
            .value-card-custom p { font-size: 12px; -webkit-line-clamp: 2; }
            .value-icon-circle { width: 52px; height: 52px; margin-bottom: 8px; }
            .value-icon-circle svg { width: 28px; height: 28px; }
            .banner-wrapper img { height: 170px !important; }
        }
    </style>
</head>
<body>
    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

<main class="main-area fix">
    <!-- breadcrumb-area -->
    <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= htmlspecialchars($pc['about_breadcrumb_bg']) ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-content text-center text-md-start">
                        <nav class="breadcrumb justify-content-center justify-content-md-start">
                            <span><a href="index.php">Home</a></span>
                            <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                            <span>About Us</span>
                        </nav>
                        <h1 class="title"><?= htmlspecialchars($pc['about_breadcrumb_title']) ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section Center Title -->
    <section class="about-eswasa-area py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <div class="about-content">
                        <div class="section__title mb-4">
                            <h2 class="title" style="color: #2B3388;">About Us</h2>
                            <div class="section-divider"></div>
                        </div>
                        <div class="mt-4 lead px-2">
                            <?= render_paragraphs($pc['about_intro']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <!-- VISION AND MISSION -->
            <div class="text-center mt-3 mb-4">
                <h2 class="fw-bold" style="color: #2B3388;">Vision &amp; Mission</h2>
                <div class="section-divider"></div>
            </div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="vm-card">
                        <div class="vm-icon" aria-hidden="true">
                            <!-- Eye — universal "vision" symbol, drawn in the homepage family -->
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 24 Q14 10 24 10 Q34 10 44 24 Q34 38 24 38 Q14 38 4 24 Z"/>
                                <circle cx="24" cy="24" r="8"/>
                                <circle cx="24" cy="24" r="4" fill="currentColor"/>
                            </svg>
                        </div>
                        <h3>Vision</h3>
                        <p><?= htmlspecialchars($pc['about_vision']) ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="vm-card">
                        <div class="vm-icon" aria-hidden="true">
                            <!-- Mountain peak with planted flag — new glyph, same drawing family -->
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 40 L18 22 L26 32 L34 16 L44 40 Z"/>
                                <line x1="34" y1="16" x2="34" y2="4"/>
                                <path d="M34 4 L44 8 L34 12 Z" fill="currentColor"/>
                                <line x1="14" y1="40" x2="34" y2="40"/>
                            </svg>
                        </div>
                        <h3>Mission</h3>
                        <p><?= htmlspecialchars($pc['about_mission']) ?></p>
                    </div>
                </div>
            </div>

            <!-- CORE VALUES -->
            <div class="text-center mt-5 mb-4">
                <h2 class="fw-bold" style="color: #2B3388;">Our Core Values</h2>
                <div class="section-divider"></div>
            </div>

            <div class="values-diagram-container">
                <!-- Row 1: Transparency · Image · Responsiveness -->
                <div class="row g-2 align-items-center">
                    <div class="col-lg-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle" aria-hidden="true">
                                <svg viewBox="0 0 48 48" fill="currentColor">
                                    <path d="M24 4 L40 10 V22 C40 32 33 40 24 44 C15 40 8 32 8 22 V10 Z"/>
                                    <polyline points="16 24 22 30 33 18" fill="none" stroke="#fff" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4>Transparency</h4>
                            <p><?= htmlspecialchars($pc['about_val_transparency']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="about core.jpg" alt="Core Values" class="values-center-image">
                    </div>
                    <div class="col-lg-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle" aria-hidden="true">
                                <i class="fas fa-handshake fa-icon"></i>
                            </div>
                            <h4>Responsiveness</h4>
                            <p><?= htmlspecialchars($pc['about_val_responsiveness']) ?></p>
                        </div>
                    </div>
                </div>
                <!-- Row 2: People-Centricity · Innovation · Professionalism -->
                <div class="row g-2 mt-2 align-items-center">
                    <div class="col-lg-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle" aria-hidden="true">
                                <svg viewBox="0 0 48 48" fill="currentColor">
                                    <!-- Circular ring (the joined-hands loop) -->
                                    <circle cx="24" cy="24" r="13" fill="none" stroke="currentColor" stroke-width="2"/>
                                    <!-- 4 prominent figures sitting on the ring at the diagonals -->
                                    <circle cx="34" cy="14" r="5.5"/>
                                    <circle cx="34" cy="34" r="5.5"/>
                                    <circle cx="14" cy="34" r="5.5"/>
                                    <circle cx="14" cy="14" r="5.5"/>
                                </svg>
                            </div>
                            <h4>People-Centricity</h4>
                            <p><?= htmlspecialchars($pc['about_val_people']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <div class="value-card-custom mx-auto">
                            <div class="value-icon-circle" aria-hidden="true">
                                <span class="fa-stack">
                                    <i class="fas fa-hand-holding fa-stack-2x"></i>
                                    <i class="fas fa-globe fa-stack-1x"></i>
                                </span>
                            </div>
                            <h4>Innovation</h4>
                            <p><?= htmlspecialchars($pc['about_val_innovation']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle" aria-hidden="true">
                                <svg viewBox="0 0 48 48" fill="currentColor">
                                    <circle cx="24" cy="10" r="5"/>
                                    <path d="M14 40 V24 C14 19 18 16 24 16 C30 16 34 19 34 24 V40 Z"/>
                                    <circle cx="8" cy="16" r="3.5"/>
                                    <path d="M1 40 V28 C1 24 3 22 8 22 C10 22 12 23 13 24 V40 Z"/>
                                    <circle cx="40" cy="16" r="3.5"/>
                                    <path d="M47 40 V28 C47 24 45 22 40 22 C38 22 36 23 35 24 V40 Z"/>
                                </svg>
                            </div>
                            <h4>Professionalism</h4>
                            <p><?= htmlspecialchars($pc['about_val_professionalism']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORY -->
            <div class="info-section mt-5 mb-5">
                <h3>Brief History</h3>
                <?= render_paragraphs($pc['about_history']) ?>
            </div>

        </div>
    </section>

    <!-- Affiliations -->
    <section class="bg_color3 py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: #2B3388;">Our Affiliations</h2>
                <div class="section-divider"></div>
            </div>
            <div class="affiliations-slider overflow-hidden">
                <div class="slider-track d-flex flex-nowrap">
                    <?php
                    $affs = [
                        ['src'=>'admin/uploads/itu.png',  'alt'=>'ITU',  'href'=>'https://www.itu.int/'],
                        ['src'=>'admin/uploads/iso.png',  'alt'=>'ISO',  'href'=>'https://www.iso.org/'],
                        ['src'=>'admin/uploads/iec.png',  'alt'=>'IEC',  'href'=>'https://www.iec.ch/'],
                        ['src'=>'admin/uploads/arso.png', 'alt'=>'ARSO', 'href'=>'https://www.arso-org.org/'],
                        ['src'=>'assets/img/SABS.png',   'alt'=>'SABS', 'href'=>'https://www.sabs.co.za'],
                        ['src'=>'admin/uploads/astm.png', 'alt'=>'ASTM', 'href'=>'https://www.astm.org/'],
                        ['src'=>'assets/img/WTO.png',    'alt'=>'WTO',  'href'=>'https://www.wto.org'],
                        ['src'=>'assets/img/AP.png',    'alt'=>'AP',  'href'=>''],
                        ['src'=>'assets/img/sadcstan.jpg',    'alt'=>'sadcstan',  'href'=>''],
                    ];
                    foreach (array_merge($affs, $affs) as $a): ?>
                    <div class="slider-item">
                        <a href="<?= $a['href'] ?>" target="_blank" rel="noopener" class="logo-card-fixed">
                            <img src="<?= $a['src'] ?>" alt="<?= $a['alt'] ?>">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Accreditation -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5 px-3">
                <h2 class="fw-bold" style="color: #2B3388;">ESWASA ACCREDITATION</h2>
                <div class="section-divider"></div>
                <p class="text-muted mt-3">Eswatini Standards Authority is accredited by SADCAS.</p>
            </div>
            <div class="affiliations-slider overflow-hidden">
                <div class="slider-track d-flex flex-nowrap">
                    <?php
                    $accs = [
                        ['src'=>'assets/img/SADCAS.png', 'href'=>'https://www.sadcas.org', 'alt'=>'SADCAS'],
                        ['src'=>'assets/img/ILAC.JPG',  'href'=>'', 'alt'=>'ILAC'],
                        ['src'=>'assets/img/iaf.webp',   'href'=>'https://www.iaf.nu/', 'alt'=>'IAF'],
                    ];
                    foreach (array_merge($accs, $accs) as $a): ?>
                    <div class="slider-item">
                        <a href="<?= $a['href'] ?>" target="_blank" rel="noopener" class="logo-card-fixed">
                            <img src="<?= $a['src'] ?>" alt="<?= $a['alt'] ?>">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';

// ── CMS keys for index page (Discover, Marks, Affiliations + headings) ──
$pc_keys = [
    // Section headings
    'index_discover_heading',
    'index_marks_heading',
    'index_affiliations_heading',
    // Discover cards (1..4)
    'index_discover_1_title','index_discover_1_desc','index_discover_1_url',
    'index_discover_2_title','index_discover_2_desc','index_discover_2_url',
    'index_discover_3_title','index_discover_3_desc','index_discover_3_url',
    'index_discover_4_title','index_discover_4_desc','index_discover_4_url',
    // Certification Marks (1..3)
    'index_mark_1_title','index_mark_1_desc','index_mark_1_image','index_mark_1_explore_url','index_mark_1_verify_url',
    'index_mark_2_title','index_mark_2_desc','index_mark_2_image','index_mark_2_explore_url','index_mark_2_verify_url',
    'index_mark_3_title','index_mark_3_desc','index_mark_3_image','index_mark_3_explore_url','index_mark_3_verify_url',
    // Affiliations (1..11)
    'index_affiliation_1_logo','index_affiliation_1_url','index_affiliation_1_alt',
    'index_affiliation_2_logo','index_affiliation_2_url','index_affiliation_2_alt',
    'index_affiliation_3_logo','index_affiliation_3_url','index_affiliation_3_alt',
    'index_affiliation_4_logo','index_affiliation_4_url','index_affiliation_4_alt',
    'index_affiliation_5_logo','index_affiliation_5_url','index_affiliation_5_alt',
    'index_affiliation_6_logo','index_affiliation_6_url','index_affiliation_6_alt',
    'index_affiliation_7_logo','index_affiliation_7_url','index_affiliation_7_alt',
    'index_affiliation_8_logo','index_affiliation_8_url','index_affiliation_8_alt',
    'index_affiliation_9_logo','index_affiliation_9_url','index_affiliation_9_alt',
    'index_affiliation_10_logo','index_affiliation_10_url','index_affiliation_10_alt',
    'index_affiliation_11_logo','index_affiliation_11_url','index_affiliation_11_alt',
];
$pc_defaults = [
    'index_discover_heading'     => 'Discover',
    'index_marks_heading'        => 'Certification Marks',
    'index_affiliations_heading' => 'Our Affiliations',

    'index_discover_1_title' => 'Certification',
    'index_discover_1_desc'  => 'Independent certification of management systems and products, audited to recognised international standards.',
    'index_discover_1_url'   => 'Certification.php',
    'index_discover_2_title' => 'Product Testing',
    'index_discover_2_desc'  => 'Food, microbiology and product testing carried out to recognised international standards.',
    'index_discover_2_url'   => 'product.php',
    'index_discover_3_title' => 'Standards Development',
    'index_discover_3_desc'  => 'National standards developed with industry, government and consumer input to protect health and enable trade.',
    'index_discover_3_url'   => 'Standards.php',
    'index_discover_4_title' => 'Training & Development',
    'index_discover_4_desc'  => 'Quality management, internal auditing and standards training aligned to international best practice.',
    'index_discover_4_url'   => 'training-about.php',

    'index_mark_1_title'       => 'Management Systems Certification Mark',
    'index_mark_1_desc'        => 'Awarded to organisations whose quality, environmental, food safety or occupational health management systems have been independently audited and proven to meet recognised international standards. Provides for continuous, systematic verification of effectiveness.',
    'index_mark_1_image'       => 'assets/img/quality/management-mark-black.png',
    'index_mark_1_explore_url' => 'managementsystems.php',
    'index_mark_1_verify_url'  => 'certification-status.php',
    'index_mark_2_title'       => 'Product Certification Mark',
    'index_mark_2_desc'        => 'A voluntary product certification scheme operated by the Eswatini Standards Authority. Awarded to products manufactured to declared national and international standards and proven through rigorous, independent testing — giving buyers confidence in quality and safety.',
    'index_mark_2_image'       => 'assets/img/quality/product-certification-black.png',
    'index_mark_2_explore_url' => 'Certification.php',
    'index_mark_2_verify_url'  => 'certification-status.php',
    'index_mark_3_title'       => 'Ingelo MSME Product Certification Mark',
    'index_mark_3_desc'        => 'A simplified, affordable certification scheme designed for micro, small and medium enterprises (MSMEs) and local producers — helping them prove product quality, access new markets and grow with credibility.',
    'index_mark_3_image'       => 'assets/img/quality/ingelo-certification-black.png',
    'index_mark_3_explore_url' => 'ingelo.php',
    'index_mark_3_verify_url'  => 'certification-status.php',

    'index_affiliation_1_logo'  => 'admin/uploads/iso.png',
    'index_affiliation_1_url'   => 'https://www.iso.org/',
    'index_affiliation_1_alt'   => 'ISO',
    'index_affiliation_2_logo'  => 'admin/uploads/iec.png',
    'index_affiliation_2_url'   => 'https://www.iec.ch/',
    'index_affiliation_2_alt'   => 'IEC',
    'index_affiliation_3_logo'  => 'admin/uploads/itu.png',
    'index_affiliation_3_url'   => 'https://www.itu.int/',
    'index_affiliation_3_alt'   => 'ITU',
    'index_affiliation_4_logo'  => 'assets/img/iaf.webp',
    'index_affiliation_4_url'   => 'https://iaf.nu/',
    'index_affiliation_4_alt'   => 'IAF',
    'index_affiliation_5_logo'  => 'assets/img/ILAC.jpg',
    'index_affiliation_5_url'   => 'https://ilac.org/',
    'index_affiliation_5_alt'   => 'ILAC',
    'index_affiliation_6_logo'  => 'admin/uploads/arso-2024.png',
    'index_affiliation_6_url'   => 'https://www.arso-org.org/',
    'index_affiliation_6_alt'   => 'ARSO',
    'index_affiliation_7_logo'  => 'assets/img/SADCAS.png',
    'index_affiliation_7_url'   => 'https://www.sadcas.org/',
    'index_affiliation_7_alt'   => 'SADCAS',
    'index_affiliation_8_logo'  => 'assets/img/sadc.webp',
    'index_affiliation_8_url'   => 'https://www.sadc.int/',
    'index_affiliation_8_alt'   => 'SADC',
    'index_affiliation_9_logo'  => 'assets/img/sadcstan.jpg',
    'index_affiliation_9_url'   => 'https://www.sadcstan.org/',
    'index_affiliation_9_alt'   => 'SADCSTAN',
    'index_affiliation_10_logo' => 'admin/uploads/arso-2024.png',
    'index_affiliation_10_url'  => 'https://www.arso-org.org/',
    'index_affiliation_10_alt'  => 'ARSO',
    'index_affiliation_11_logo' => 'admin/uploads/astm.png',
    'index_affiliation_11_url'  => 'https://www.astm.org/',
    'index_affiliation_11_alt'  => 'ASTM',
];
$pc = pc_get_many($conn, $pc_keys, $pc_defaults);

// Fetch banners for slider
$banners = mysqli_query($conn, "SELECT * FROM banners");
if (!$banners) {
    die("Banner query failed: " . mysqli_error($conn));
}

// Fetch events
$events = mysqli_query($conn, "SELECT * FROM eswasa_events ORDER BY event_date DESC LIMIT 3");
if (!$events) {
    die("Events query failed: " . mysqli_error($conn));
}

// Fetch latest announcements for the home page marquee strip (degrade silently if table missing)
$announcement_marquee_items = [];
$am_result = @mysqli_query($conn, "SELECT id, title FROM eswasa_announcements ORDER BY published_date DESC LIMIT 8");
if ($am_result) {
    while ($r = mysqli_fetch_assoc($am_result)) {
        $announcement_marquee_items[] = $r;
    }
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ESWASA - Eswatini Standards Authority</title>
    <meta name="description" content="">
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
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Mobile Responsive Styles -->
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
    /* Slider captions stay white over the dark image overlay */
    .slider-area, .slider-area .tp-caption,
    .slider-area .tp-caption h2, .slider-area .tp-caption p {
        color: #fff;
    }
    /* Slider bullets — minimal white dots */
    .tp-bullets.round .bullet,
    .tp-bullets .bullet {
        width: 10px !important;
        height: 10px !important;
        background: rgba(255, 255, 255, 0.45) !important;
        border: none !important;
        border-radius: 50% !important;
        margin: 0 5px !important;
        box-shadow: none !important;
        transition: background .2s ease, transform .2s ease;
    }
    .tp-bullets.round .bullet:hover,
    .tp-bullets .bullet:hover {
        background: rgba(255, 255, 255, 0.75) !important;
    }
    .tp-bullets.round .bullet.selected,
    .tp-bullets .bullet.selected {
        background: #fff !important;
        transform: scale(1.2);
    }

    /* ========== TABLET (max 991px) ========== */
    @media (max-width: 991.98px) {
        .section__title .title { font-size: 1.6rem !important; }
    }

    /* ========== MOBILE (max 767px) ========== */
    @media (max-width: 767.98px) {
        .slider-area .tp-caption h2 { font-size: 1.2rem !important; }
        .slider-area .tp-caption p { font-size: 0.85rem !important; }
        .slider-btn { padding: 8px 16px !important; font-size: 0.8rem !important; }
        .section__title .title { font-size: 1.4rem !important; }

        /* Facebook sticky — slightly smaller circle on mobile */
        .fb-sticky { width: 42px; height: 42px; }
        .fb-icon { font-size: 17px; }
    }

    /* ========== SMALL MOBILE (max 480px) ========== */
    @media (max-width: 480px) {
        .slider-area .tp-caption h2 { font-size: 1rem !important; }
        .slider-area .tp-caption p { font-size: 0.75rem !important; max-width: 90%; }

        /* Swiper navigation arrows smaller */
        .swiper-button-next, .swiper-button-prev { transform: scale(0.7); }
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
   

<!-- Slider Area -->
<section class="slider-area">
    <div class="tp-banner-container">
        <div class="tp-banner">
            <ul>
                <?php
                if ($banners && mysqli_num_rows($banners) > 0) {
                     while($row = mysqli_fetch_assoc($banners)) {
                         $image_path_from_db = $row['file'] ?? '';
                         $display_path = '';

                         if (!empty($image_path_from_db)) {
                             if (strpos($image_path_from_db, 'admin/') === 0) {
                                 $display_path = $image_path_from_db;
                             } else if (strpos($image_path_from_db, 'uploads/') === 0) {
                                 $display_path = 'admin/' . $image_path_from_db;
                             } else {
                                 $display_path = 'admin/uploads/' . basename($image_path_from_db);
                             }
                         }
                ?>
                <li data-transition="slideright" data-slotamount="1" data-masterspeed="1000" data-delay="5000" data-saveperformance="off" data-title="Slide">
                    <!-- MAIN IMAGE -->
                    <?php if (!empty($display_path) && file_exists($display_path)): ?>
                        <img src="<?php echo htmlspecialchars($display_path); ?>"
                            alt="<?php echo htmlspecialchars($row['caption'] ?? 'Banner'); ?>"
                            data-bgposition="center center"
                            data-bgfit="cover"
                            data-bgrepeat="no-repeat">
                    <?php else: ?>
                        <img src="assets/img/slider/default-banner.jpg"
                            alt="<?php echo htmlspecialchars($row['caption'] ?? 'ESWASA Banner'); ?>"
                            data-bgposition="center center"
                            data-bgfit="cover"
                            data-bgrepeat="no-repeat">
                    <?php endif; ?>

                    <!-- LAYER NR. 1 (Caption) -->
                    <div class="tp-caption sft sfb tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="-100"
                        data-speed="1000"
                        data-start="1000"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <div><h2><?php echo htmlspecialchars($row['caption'] ?? 'ESWASA Banner'); ?></h2></div>
                    </div>

                    <!-- LAYER NR. 2 (Read More button) -->
                    <div class="tp-caption lfb ltt tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="20"
                        data-speed="1200"
                        data-start="1200"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <?php if (!empty($row['url'])) { ?>
                        <a href="<?php echo htmlspecialchars($row['url']); ?>" class="slider-btn slider-btn-1" target="_blank" rel="noopener">Read More</a>
                        <?php } ?>
                    </div>
                </li>
                <?php
                     }
                } else {
                    echo "<!-- No banners found in database -->\n";
                }
                ?>
            </ul>
            <div class="tp-bannertimer"></div>
        </div>
    </div>
</section>


<!-- announcement-strip -->
<style>
    .announcement-strip {
        background: #2B3388;
        color: #fff;
        overflow: hidden;
        border-bottom: 3px solid #ffd34d;
        position: relative;
        z-index: 5;
    }
    .announcement-strip .strip-inner {
        display: flex;
        align-items: stretch;
        width: 100%;
    }
    .announcement-strip .strip-label {
        background: #ffd34d;
        color: #2B3388;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 14px 36px 14px 22px;
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        font-size: .9rem;
        flex-shrink: 0;
        clip-path: polygon(0 0, 100% 0, calc(100% - 18px) 100%, 0 100%);
    }
    .announcement-strip .strip-label i { font-size: 1rem; }
    .announcement-strip .strip-track {
        flex: 1;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .announcement-strip .strip-content {
        display: flex;
        gap: 60px;
        white-space: nowrap;
        padding-left: 30px;
        animation: strip-scroll 50s linear infinite;
    }
    .announcement-strip:hover .strip-content { animation-play-state: paused; }
    .announcement-strip .strip-item {
        color: #fff;
        text-decoration: none;
        font-size: .95rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: color .2s ease;
    }
    .announcement-strip .strip-item:hover { color: #ffd34d; }
    .announcement-strip .strip-item::before {
        content: '';
        width: 6px;
        height: 6px;
        background: #ffd34d;
        border-radius: 50%;
        flex-shrink: 0;
    }
    @keyframes strip-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }
    @media (max-width: 575.98px) {
        .announcement-strip .strip-label {
            padding: 10px 24px 10px 14px;
            font-size: .75rem;
            clip-path: polygon(0 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
        }
        .announcement-strip .strip-item { font-size: .82rem; }
        .announcement-strip .strip-content { gap: 40px; animation-duration: 35s; }
    }
</style>
<section class="announcement-strip" aria-label="Latest announcements">
    <div class="strip-inner">
        <div class="strip-label">
            <i class="fas fa-bullhorn"></i> Latest
        </div>
        <div class="strip-track">
            <?php if (!empty($announcement_marquee_items)): ?>
            <div class="strip-content">
                <?php foreach ($announcement_marquee_items as $a): ?>
                    <a href="announcements.php" class="strip-item"><?= htmlspecialchars($a['title']) ?></a>
                <?php endforeach; ?>
                <?php /* duplicated set for seamless loop */ ?>
                <?php foreach ($announcement_marquee_items as $a): ?>
                    <a href="announcements.php" class="strip-item" aria-hidden="true" tabindex="-1"><?= htmlspecialchars($a['title']) ?></a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <?php
                $fallback_messages = [
                    'Welcome to ESWASA &mdash; Eswatini Standards Authority',
                    'Visit the Standards catalogue to browse current ESWASA-approved standards',
                    'Suggest a new standard or join a Technical Committee',
                    'Apply for certification through our Management Systems and Product schemes',
                    'Calibration services available for industry and regulators',
                    'See our Announcements page for the latest news and updates',
                ];
            ?>
            <div class="strip-content">
                <?php foreach ($fallback_messages as $msg): ?>
                    <span class="strip-item"><?= $msg ?></span>
                <?php endforeach; ?>
                <?php foreach ($fallback_messages as $msg): ?>
                    <span class="strip-item" aria-hidden="true"><?= $msg ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<!-- announcement-strip-end -->


<!-- course-area -->
<style>
    .coursesSlider .swiper-slide { height: auto; }
    .coursesSlider .swiper-slide .blog__post-item { height: 100%; }
    .coursesSlider .swiper-slide .blog__post-content { padding: 15px 20px 20px; }
    .coursesSlider .swiper-slide .blog__post-content .title { font-size: 18px; }
    .coursesSlider .swiper-slide .blog__post-content p { font-size: 13px; }
    .coursesSlider .swiper-slide .blog__post-content .cat img { width: 22px; }
    .coursesSlider .swiper-slide .blog__post-content .cat { font-size: 12px; padding: 4px 12px; }

    /* Discover section background design */
    .courses-area.bg-gray {
        position: relative;
        overflow: hidden;
    }
    .courses-area.bg-gray > .container {
        position: relative;
        z-index: 1;
    }

    .discover-bg-elements {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    /* Decorative infographic palette — muted complementary hues at low opacity */
    .discover-bg-elements .bg-network {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 350px;
        height: 350px;
        color: #0E7C7B;          /* teal */
        opacity: 0.22;
    }
    .discover-bg-elements .bg-hexagons {
        position: absolute;
        bottom: -40px;
        left: -20px;
        width: 300px;
        height: 300px;
        color: #2B3388;          /* brand blue */
        opacity: 0.20;
    }
    .discover-bg-elements .bg-flow {
        position: absolute;
        top: 50%;
        left: 5%;
        transform: translateY(-50%);
        width: 160px;
        height: 300px;
        color: #C28B1F;          /* amber */
        opacity: 0.22;
    }
    .discover-bg-elements .bg-molecule {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 400px;
        height: 400px;
        color: #5B6478;          /* slate (keeps centre quiet) */
        opacity: 0.14;
    }
    .discover-bg-elements .bg-barcode {
        position: absolute;
        bottom: 20px;
        right: 40px;
        width: 120px;
        height: 80px;
        color: #1F7A4D;          /* forest green */
        opacity: 0.30;
    }

    /* Card icon sizing (replaces missing PNGs) */
    .courses-area .blog__post-content .discover-icon {
        width: 70px;
        height: 70px;
        color: #2B3388;
        display: inline-block;
    }

    @media (max-width: 991.98px) {
        .discover-bg-elements .bg-molecule { width: 280px; height: 280px; }
        .discover-bg-elements .bg-network { width: 240px; height: 240px; }
        .discover-bg-elements .bg-hexagons { width: 220px; height: 220px; }
    }
    @media (max-width: 575.98px) {
        .discover-bg-elements .bg-flow,
        .discover-bg-elements .bg-barcode { display: none; }
        .discover-bg-elements .bg-molecule { width: 220px; height: 220px; opacity: 0.10; }
    }

    /* Discover cards — refined spacing, equal heights, hover */
    .courses-area .row > [class*="col-"] { display: flex; }
    .courses-area .blog__post-item {
        background: #fff;
        border: 1px solid rgba(43, 51, 136, 0.12);
        border-radius: 4px;
        width: 100%;
        display: flex;
        flex-direction: column;
        transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .courses-area .blog__post-item:hover {
        border-color: #2B3388;
        box-shadow: 0 8px 22px rgba(43, 51, 136, 0.10);
        transform: translateY(-3px);
    }
    .courses-area .blog__post-content {
        padding: 34px 26px 26px;
        text-align: center;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .courses-area .blog__post-content > a:first-child {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 88px;
        margin-bottom: 22px;
    }
    .courses-area .blog__post-content .title {
        margin: 0 0 14px;
        font-size: 18px;
        line-height: 1.3;
        font-weight: 700;
    }
    .courses-area .blog__post-content .title a {
        color: #2B3388;
        text-decoration: none;
    }
    .courses-area .blog__post-content .card-desc {
        margin: 0 0 26px;
        font-size: 14px;
        line-height: 1.65;
        color: #2B3388;
        flex: 1;
    }
    .courses-area .blog__post-content .cat {
        margin-top: auto;
        align-self: center;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 20px;
        border: 1px solid #2B3388;
        color: #2B3388;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border-radius: 2px;
        background: transparent;
        transition: background .2s ease, color .2s ease;
    }
    .courses-area .blog__post-content .cat:hover {
        background: #2B3388;
        color: #fff;
    }
    .courses-area .blog__post-content .cat i {
        font-size: 12px;
        transition: transform .2s ease;
    }
    .courses-area .blog__post-content .cat:hover i { transform: translateX(3px); }

    @media (max-width: 767.98px) {
        .courses-area .blog__post-content { padding: 28px 22px 22px; }
        .courses-area .blog__post-content .title { font-size: 17px; }
        .courses-area .blog__post-content .card-desc { font-size: 13.5px; margin-bottom: 22px; }
    }
</style>
<section class="courses-area bg-gray" style="padding-top: 15px; padding-bottom: 15px;">
    <!-- Discover section decorative background -->
    <div class="discover-bg-elements">
        <!-- Network / connected nodes (top right) -->
        <svg class="bg-network" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="40" cy="40" r="6"/>
            <circle cx="140" cy="30" r="6"/>
            <circle cx="170" cy="100" r="6"/>
            <circle cx="100" cy="90" r="6"/>
            <circle cx="60" cy="140" r="6"/>
            <circle cx="150" cy="160" r="6"/>
            <line x1="40" y1="40" x2="100" y2="90"/>
            <line x1="140" y1="30" x2="100" y2="90"/>
            <line x1="170" y1="100" x2="100" y2="90"/>
            <line x1="60" y1="140" x2="100" y2="90"/>
            <line x1="150" y1="160" x2="100" y2="90"/>
            <line x1="40" y1="40" x2="140" y2="30"/>
            <line x1="60" y1="140" x2="150" y2="160"/>
        </svg>

        <!-- Honeycomb hexagons (bottom left) -->
        <svg class="bg-hexagons" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round">
            <polygon points="30,40 55,25 80,40 80,70 55,85 30,70"/>
            <polygon points="80,40 105,25 130,40 130,70 105,85 80,70"/>
            <polygon points="130,40 155,25 180,40 180,70 155,85 130,70"/>
            <polygon points="55,85 80,70 105,85 105,115 80,130 55,115"/>
            <polygon points="105,85 130,70 155,85 155,115 130,130 105,115"/>
            <polygon points="30,130 55,115 80,130 80,160 55,175 30,160"/>
            <polygon points="80,130 105,115 130,130 130,160 105,175 80,160"/>
            <polygon points="130,130 155,115 180,130 180,160 155,175 130,160"/>
        </svg>

        <!-- Flow process arrows (mid left) -->
        <svg class="bg-flow" viewBox="0 0 80 200" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="20" y="10" width="40" height="28" rx="2"/>
            <line x1="40" y1="38" x2="40" y2="60"/>
            <polyline points="32,54 40,62 48,54"/>
            <rect x="20" y="62" width="40" height="28" rx="2"/>
            <line x1="40" y1="90" x2="40" y2="112"/>
            <polyline points="32,106 40,114 48,106"/>
            <rect x="20" y="114" width="40" height="28" rx="2"/>
            <line x1="40" y1="142" x2="40" y2="164"/>
            <polyline points="32,158 40,166 48,158"/>
            <rect x="20" y="166" width="40" height="28" rx="2"/>
        </svg>

        <!-- Molecule / atom structure (center) -->
        <svg class="bg-molecule" viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="1.2">
            <ellipse cx="100" cy="100" rx="85" ry="32"/>
            <ellipse cx="100" cy="100" rx="85" ry="32" transform="rotate(60 100 100)"/>
            <ellipse cx="100" cy="100" rx="85" ry="32" transform="rotate(120 100 100)"/>
            <circle cx="100" cy="100" r="10" fill="currentColor"/>
            <circle cx="185" cy="100" r="6" fill="currentColor"/>
            <circle cx="15" cy="100" r="6" fill="currentColor"/>
            <circle cx="142.5" cy="173.6" r="6" fill="currentColor"/>
            <circle cx="57.5" cy="26.4" r="6" fill="currentColor"/>
            <circle cx="142.5" cy="26.4" r="6" fill="currentColor"/>
            <circle cx="57.5" cy="173.6" r="6" fill="currentColor"/>
        </svg>

        <!-- Barcode / standards code (bottom right) -->
        <svg class="bg-barcode" viewBox="0 0 120 80" fill="currentColor">
            <rect x="0"   y="0" width="3" height="60"/>
            <rect x="6"   y="0" width="1" height="60"/>
            <rect x="10"  y="0" width="4" height="60"/>
            <rect x="17"  y="0" width="2" height="60"/>
            <rect x="22"  y="0" width="1" height="60"/>
            <rect x="26"  y="0" width="3" height="60"/>
            <rect x="32"  y="0" width="2" height="60"/>
            <rect x="37"  y="0" width="5" height="60"/>
            <rect x="45"  y="0" width="1" height="60"/>
            <rect x="49"  y="0" width="3" height="60"/>
            <rect x="55"  y="0" width="2" height="60"/>
            <rect x="60"  y="0" width="4" height="60"/>
            <rect x="67"  y="0" width="1" height="60"/>
            <rect x="71"  y="0" width="3" height="60"/>
            <rect x="77"  y="0" width="2" height="60"/>
            <rect x="82"  y="0" width="1" height="60"/>
            <rect x="86"  y="0" width="4" height="60"/>
            <rect x="93"  y="0" width="2" height="60"/>
            <rect x="98"  y="0" width="3" height="60"/>
            <rect x="104" y="0" width="1" height="60"/>
            <rect x="108" y="0" width="5" height="60"/>
            <rect x="116" y="0" width="2" height="60"/>
        </svg>
    </div>

    <div class="container">
        <div class="section__title-wrap mb-55">
            <div class="row align-items-center gap-4 gap-md-0">
                <div class="col-md-8">
                    <div class="section__title text-center text-md-start">
                        <h2 class="title tg-svg" style="color: #2B3388;"><?= pc_h($pc['index_discover_heading']) ?></h2>
                        <div class="section-divider" style="margin-left: 0; margin-right: 0;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="blog__post-item shine__animate-item">
                    <div class="blog__post-content">
                        <a href="<?= pc_h($pc['index_discover_1_url']) ?>">
                            <svg class="discover-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M24 4 L42 11 V24 C42 34 34 42 24 44 C14 42 6 34 6 24 V11 Z"/>
                                <path d="M16 24 L22 30 L33 18"/>
                            </svg>
                        </a>
                        <h4 class="title"><a href="<?= pc_h($pc['index_discover_1_url']) ?>"><b><?= pc_h($pc['index_discover_1_title']) ?></b></a></h4>
                        <p class="card-desc"><?= pc_h($pc['index_discover_1_desc']) ?></p>
                        <a href="<?= pc_h($pc['index_discover_1_url']) ?>" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="blog__post-item shine__animate-item">
                    <div class="blog__post-content">
                        <a href="<?= pc_h($pc['index_discover_2_url']) ?>">
                            <svg class="discover-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 H30"/>
                                <path d="M20 6 V20 L10 38 C8.5 41 10.5 44 13.5 44 H34.5 C37.5 44 39.5 41 38 38 L28 20 V6"/>
                                <path d="M14 32 H34"/>
                                <circle cx="20" cy="36" r="1.4" fill="currentColor"/>
                                <circle cx="27" cy="38" r="1.4" fill="currentColor"/>
                                <circle cx="24" cy="33" r="1.2" fill="currentColor"/>
                            </svg>
                        </a>
                        <h4 class="title"><a href="<?= pc_h($pc['index_discover_2_url']) ?>"><b><?= pc_h($pc['index_discover_2_title']) ?></b></a></h4>
                        <p class="card-desc"><?= pc_h($pc['index_discover_2_desc']) ?></p>
                        <a href="<?= pc_h($pc['index_discover_2_url']) ?>" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="blog__post-item shine__animate-item">
                    <div class="blog__post-content">
                        <a href="<?= pc_h($pc['index_discover_3_url']) ?>">
                            <svg class="discover-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                <path d="M28 6 V16 H38"/>
                                <line x1="16" y1="22" x2="32" y2="22"/>
                                <line x1="16" y1="28" x2="32" y2="28"/>
                                <line x1="16" y1="34" x2="26" y2="34"/>
                            </svg>
                        </a>
                        <h4 class="title"><a href="<?= pc_h($pc['index_discover_3_url']) ?>"><b><?= pc_h($pc['index_discover_3_title']) ?></b></a></h4>
                        <p class="card-desc"><?= pc_h($pc['index_discover_3_desc']) ?></p>
                        <a href="<?= pc_h($pc['index_discover_3_url']) ?>" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="blog__post-item shine__animate-item">
                    <div class="blog__post-content">
                        <a href="<?= pc_h($pc['index_discover_4_url']) ?>">
                            <svg class="discover-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 20 L24 10 L44 20 L24 30 Z"/>
                                <path d="M12 24 V34 C12 36.5 17 40 24 40 C31 40 36 36.5 36 34 V24"/>
                                <path d="M44 20 V32"/>
                                <circle cx="44" cy="34" r="1.6" fill="currentColor"/>
                            </svg>
                        </a>
                        <h4 class="title"><a href="<?= pc_h($pc['index_discover_4_url']) ?>"><b><?= pc_h($pc['index_discover_4_title']) ?></b></a></h4>
                        <p class="card-desc"><?= pc_h($pc['index_discover_4_desc']) ?></p>
                        <a href="<?= pc_h($pc['index_discover_4_url']) ?>" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- course-area-end -->


<!-- ESWASA Section -->
<style>
    .trust-eswasa-section {
        background: #fff;
        padding: 70px 0 80px;
        border-top: 1px solid rgba(43, 51, 136, 0.10);
    }
    .trust-eswasa-section .section-heading {
        text-align: center;
        margin-bottom: 56px;
    }
    .trust-eswasa-section .section-heading h2 {
        color: #2B3388;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    .marks-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }
    .mark-item {
        background: #fff;
        border: 1px solid rgba(43, 51, 136, 0.15);
        border-radius: 4px;
        padding: 30px 24px 22px;
        display: flex;
        flex-direction: column;
        text-align: center;
        transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
    }
    .mark-item:hover {
        border-color: #2B3388;
        box-shadow: 0 8px 22px rgba(43, 51, 136, 0.10);
        transform: translateY(-3px);
    }
    .mark-image {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 22px;
    }
    .mark-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .mark-title {
        color: #2B3388;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 14px;
    }
    .mark-desc {
        color: #2B3388;
        font-size: 13.5px;
        line-height: 1.65;
        margin: 0 0 22px;
        flex: 1;
    }
    .mark-actions {
        display: flex;
        justify-content: center;
        gap: 22px;
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid rgba(43, 51, 136, 0.10);
    }
    .mark-actions a {
        color: #2B3388;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: opacity .2s ease, color .2s ease;
    }
    .mark-actions a:hover { text-decoration: underline; }
    .mark-actions a i { font-size: 11px; transition: transform .2s ease; }
    .mark-actions a:hover i { transform: translateX(2px); }

    @media (max-width: 991.98px) {
        .trust-eswasa-section { padding: 50px 0 60px; }
        .trust-eswasa-section .section-heading { margin-bottom: 40px; }
        .marks-grid { grid-template-columns: repeat(2, 1fr); gap: 22px; }
        .mark-image { height: 180px; }
    }
    @media (max-width: 575.98px) {
        .trust-eswasa-section { padding: 40px 0 48px; }
        .trust-eswasa-section .section-heading { margin-bottom: 32px; }
        .trust-eswasa-section .section-heading h2 { font-size: 1.4rem; }
        .marks-grid { grid-template-columns: 1fr; gap: 16px; }
        .mark-item { padding: 24px 20px 18px; }
        .mark-image { height: 170px; margin-bottom: 18px; }
        .mark-title { font-size: 15px; }
        .mark-desc { font-size: 13px; margin-bottom: 18px; }
        .mark-actions { gap: 18px; }
        .mark-actions a { font-size: 12.5px; }
    }
</style>

<section class="trust-eswasa-section">
    <div class="container">
        <div class="section-heading">
            <h2><?= pc_h($pc['index_marks_heading']) ?></h2>
            <div class="section-divider"></div>
        </div>
        <div class="marks-grid">
            <div class="mark-item">
                <div class="mark-image">
                    <img src="<?= pc_h(pc_image_src($pc['index_mark_1_image'], 'assets/img/quality/management-mark-black.png')) ?>" alt="<?= pc_h($pc['index_mark_1_title']) ?>">
                </div>
                <h3 class="mark-title"><?= pc_h($pc['index_mark_1_title']) ?></h3>
                <p class="mark-desc"><?= pc_h($pc['index_mark_1_desc']) ?></p>
                <div class="mark-actions">
                    <a href="<?= pc_h($pc['index_mark_1_explore_url']) ?>">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= pc_h($pc['index_mark_1_verify_url']) ?>">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="mark-item">
                <div class="mark-image">
                    <img src="<?= pc_h(pc_image_src($pc['index_mark_2_image'], 'assets/img/quality/product-certification-black.png')) ?>" alt="<?= pc_h($pc['index_mark_2_title']) ?>">
                </div>
                <h3 class="mark-title"><?= pc_h($pc['index_mark_2_title']) ?></h3>
                <p class="mark-desc"><?= pc_h($pc['index_mark_2_desc']) ?></p>
                <div class="mark-actions">
                    <a href="<?= pc_h($pc['index_mark_2_explore_url']) ?>">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= pc_h($pc['index_mark_2_verify_url']) ?>">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="mark-item">
                <div class="mark-image">
                    <img src="<?= pc_h(pc_image_src($pc['index_mark_3_image'], 'assets/img/quality/ingelo-certification-black.png')) ?>" alt="<?= pc_h($pc['index_mark_3_title']) ?>">
                </div>
                <h3 class="mark-title"><?= pc_h($pc['index_mark_3_title']) ?></h3>
                <p class="mark-desc"><?= pc_h($pc['index_mark_3_desc']) ?></p>
                <div class="mark-actions">
                    <a href="<?= pc_h($pc['index_mark_3_explore_url']) ?>">Explore <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    <a href="<?= pc_h($pc['index_mark_3_verify_url']) ?>">Verify <i class="fa fa-check-circle" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End ESWASA Section -->

<!-- Our Affiliations -->
<style>
    .affiliations-area { background-color: rgba(43, 51, 136, 0.04); padding: 56px 0 64px; }
    .affiliations-area .section-heading { text-align: center; margin-bottom: 32px; }
    .affiliations-area .section-heading h2 {
        color: #2B3388;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        letter-spacing: 0.02em;
    }

    .affiliations-slider {
        overflow: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
        position: relative;
    }
    .affiliations-slider::-webkit-scrollbar { display: none; }
    .affiliations-slider .slider-track {
        animation: affiliationsScroll 36s linear infinite;
        min-width: 100%;
    }
    .affiliations-slider:hover .slider-track {
        animation-play-state: paused;
        -webkit-animation-play-state: paused;
    }
    .affiliations-slider .slider-item {
        flex: 0 0 auto;
        width: 220px;
        text-align: center;
    }
    .affiliations-slider .slider-item a { display: block; text-decoration: none; }
    .affiliations-slider .affiliation-logo {
        width: 180px;
        height: 130px;
        object-fit: contain;
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(43, 51, 136, 0.08);
        border: 1px solid rgba(43, 51, 136, 0.10);
        display: block;
        margin: 0 auto;
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    }
    .affiliations-slider .slider-item:hover .affiliation-logo {
        transform: scale(1.06);
        box-shadow: 0 8px 20px rgba(43, 51, 136, 0.16);
        border-color: rgba(43, 51, 136, 0.30);
    }
    @keyframes affiliationsScroll {
        0%   { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    @media (max-width: 767.98px) {
        .affiliations-area { padding: 44px 0 52px; }
        .affiliations-area .section-heading { margin-bottom: 26px; }
        .affiliations-area .section-heading h2 { font-size: 24px; }
        .affiliations-slider .slider-item { width: 180px; }
        .affiliations-slider .affiliation-logo { width: 150px; height: 110px; padding: 16px; }
    }
    @media (max-width: 575.98px) {
        .affiliations-slider .slider-item { width: 150px; }
        .affiliations-slider .affiliation-logo { width: 120px; height: 90px; padding: 12px; }
    }
</style>

<section class="affiliations-area">
    <div class="container">
        <div class="section-heading">
            <h2><?= pc_h($pc['index_affiliations_heading']) ?></h2>
            <div class="section-divider"></div>
        </div>

        <div class="affiliations-slider">
            <div class="slider-track d-flex flex-nowrap">
                <?php
                $affiliation_count = 11;
                // Render twice: original set + duplicate for seamless infinite scroll
                for ($pass = 0; $pass < 2; $pass++):
                    for ($i = 1; $i <= $affiliation_count; $i++):
                        $logo = $pc['index_affiliation_' . $i . '_logo'];
                        $url  = $pc['index_affiliation_' . $i . '_url'];
                        $alt  = $pc['index_affiliation_' . $i . '_alt'];
                ?>
                <div class="slider-item px-3">
                    <a href="<?= pc_h($url) ?>" target="_blank" rel="noopener noreferrer"<?= $pass === 1 ? ' aria-hidden="true" tabindex="-1"' : '' ?>>
                        <img src="<?= pc_h(pc_image_src($logo, 'assets/img/logo/ESWASA_LOGO.jpg')) ?>" alt="<?= pc_h($alt) ?>" class="affiliation-logo">
                    </a>
                </div>
                <?php
                    endfor;
                endfor;
                ?>
            </div>
        </div>
    </div>
</section>
<!-- End Affiliations -->

    </main>
    <!-- main-area-end -->
 <!-- Sticky Facebook Feed Toggle -->
<div class="sticky-wrapper">
  <div class="fb-sticky" onclick="toggleFeed()">
    <i class="fab fa-facebook-f fb-icon" aria-hidden="true"></i> <span class="fb-label">Facebook Feed</span>
  </div>
</div>

<!-- Facebook Feed Panel with Close Button OUTSIDE the iframe -->
<div class="fb-feed" id="fbFeed">
  <!-- Close Button Outside the iframe -->
  <div class="close-btn" onclick="toggleFeed()">&times;</div>

  <!-- Facebook Page Plugin -->
  <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v23.0&appId=395042390636886"></script>

<div class="fb-page" data-href="https://www.facebook.com/eswasaupdates" data-tabs="timeline" data-width="" data-height="900" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/eswasaupdates" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/eswasaupdates">Eswatini Standards Authority - SWASA</a></blockquote></div>

</div>

<style>
  .fb-feed {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    background-color: transparent;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    z-index: 9999;
    display: flex;
    flex-direction: column;
  }

  .fb-feed.open {
    transform: translateX(0);
  }

  .close-btn {
    background-color: #2B3388;
    color: white;
    text-align: center;
    cursor: pointer;
    font-size: 24px;
    padding: 5px 20px;
    font-weight: bold;
    align-self: flex-end;
    z-index: 10000;
  }

.sticky-wrapper {
    position: fixed;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 9998;
}

.fb-sticky {
    background-color: #3b5998;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(43, 51, 136, 0.18);
}
.fb-icon {
    color: #fff;
    font-size: 20px;
    line-height: 1;
    transform: rotate(90deg);
}
.fb-label { display: none; }
</style>

<script>
  function toggleFeed() {
    const feed = document.getElementById('fbFeed');
    feed.classList.toggle('open');
  }
</script>

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
    <!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
    <script src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
    <script src="rs-plugin/js/jquery.themepunch.plugins.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-animation.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/form-contact.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <script>
            jQuery(document).ready(function() {         
                jQuery('.tp-banner').show().revolution(
                {
                    dottedOverlay:"none",
                    delay:7000,
                    startwidth:1170,
                    startheight:550,
                    hideThumbs:200,
                    thumbWidth:100,
                    thumbHeight:50,
                    thumbAmount:5,
                    navigationType:"bullet",
                    navigationArrows:"none",
                    navigationStyle:"round",
                    touchenabled:"on",
                    onHoverStop:"on",
                    swipe_velocity: 0.7,
                    swipe_min_touches: 1,
                    swipe_max_touches: 1,
                    drag_block_vertical: false,
                    parallax:"mouse",
                    parallaxBgFreeze:"on",
                    parallaxLevels:[7,4,3,2,5,4,3,2,1,0],
                    keyboardNavigation:"off",
                    navigationHAlign:"center",
                    navigationVAlign:"bottom",
                    navigationHOffset:0,
                    navigationVOffset:20,
                    soloArrowLeftHalign:"left",
                    soloArrowLeftValign:"center",
                    soloArrowLeftHOffset:20,
                    soloArrowLeftVOffset:0,
                    soloArrowRightHalign:"right",
                    soloArrowRightValign:"center",
                    soloArrowRightHOffset:20,
                    soloArrowRightVOffset:0,
                    shadow:0,
                    fullWidth:"on",
                    fullScreen:"off",
                    spinner:"spinner4",
                    stopLoop:"off",
                    stopAfterLoops:-1,
                    stopAtSlide:-1,
                    shuffle:"off",
                    autoHeight:"off",                       
                    forceFullWidth:"off",                       
                    hideThumbsOnMobile:"off",
                    hideNavDelayOnMobile:1500,                      
                    hideBulletsOnMobile:"off",
                    hideArrowsOnMobile:"off",
                    hideThumbsUnderResolution:0,
                    hideSliderAtLimit:0,
                    hideCaptionAtLimit:0,
                    hideAllCaptionAtLilmit:0,
                    startWithSlide:0,
                    fullScreenOffsetContainer: ""   
                });             
            }); //ready
       </script>
</body>
</html>
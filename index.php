<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';

// Fetch banners for slider
$banners = mysqli_query($conn, "SELECT * FROM banners");
if (!$banners) {
    die("Banner query failed: " . mysqli_error($conn));
}

// Fetch statistics - handle both old and new column structures
$stats = [];
$result = mysqli_query($conn, "SELECT * FROM site_statistics");
if (!$result) {
    die("Statistics query failed: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($result)) {
    // Handle missing columns gracefully
    $row['stat_key'] = $row['stat_key'] ?? ($row['stat_name'] ?? 'stat_' . $row['id']);
    $row['stat_label'] = $row['stat_label'] ?? ($row['stat_name'] ?? 'Statistic');
    $row['stat_value'] = $row['stat_value'] ?? 0;
    $stats[$row['stat_key']] = $row;
}

// Fetch events
$events = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date DESC LIMIT 3");
if (!$events) {
    die("Events query failed: " . mysqli_error($conn));
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
    /* ========== TABLET (max 991px) ========== */
    @media (max-width: 991.98px) {
        /* About Us - stack image above text */
        .about-eswasa-area .about-image {
            margin-bottom: 30px;
        }
        .about-eswasa-area .about-content {
            text-align: center;
        }

        /* Trust ESWASA - stack heading above marks */
        .trust-eswasa-section {
            padding: 60px 0 !important;
        }
        .trust-eswasa-section .col-lg-4 {
            margin-bottom: 40px;
        }
        .trust-eswasa-section .col-lg-4 h2 {
            font-size: 2.2rem !important;
        }
        /* 4 marks become 2x2 grid */
        .trust-eswasa-section .col-lg-8 .row .col-3 {
            flex: 0 0 50%;
            max-width: 50%;
            margin-bottom: 30px;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:first-child {
            height: 160px !important;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:last-child {
            font-size: 1rem !important;
        }

        /* CTA adjust gradient for taller layout */
        .cta-area-three {
            background: linear-gradient(to bottom, #e8e3f7 30%, #2B3388 30%) !important;
        }

        /* Hide some background decorations on tablet to reduce clutter */
        .trust-bg-elements .bg-laurel,
        .trust-bg-elements .bg-ribbon,
        .about-bg-elements .bg-doc,
        .about-bg-elements .bg-lines {
            display: none;
        }
    }

    /* ========== MOBILE (max 767px) ========== */
    @media (max-width: 767.98px) {
        /* Slider captions smaller */
        .slider-area .tp-caption h2 {
            font-size: 1.2rem !important;
        }
        .slider-area .tp-caption p {
            font-size: 0.85rem !important;
        }
        .slider-btn {
            padding: 8px 16px !important;
            font-size: 0.8rem !important;
        }

        /* Discover section */
        .courses-area.bg-gray {
            padding-top: 10px !important;
            padding-bottom: 10px !important;
        }
        .courses-area.bg-gray .section__title-wrap.mb-55 {
            margin-bottom: 10px !important;
        }
        .courses-area.bg-gray .swiper-button-next,
        .courses-area.bg-gray .swiper-button-prev {
            display: none;
        }
        .courses-area.bg-gray .swiper-pagination {
            position: relative;
            margin-top: 5px;
        }
        .section__title .title {
            font-size: 1.5rem !important;
        }

        /* About Us section */
        .about-eswasa-area {
            padding-top: 40px !important;
            padding-bottom: 40px !important;
        }
        .about-eswasa-area .about-image img {
            max-height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .about-eswasa-area .about-content .title {
            font-size: 1.5rem !important;
        }
        .about-eswasa-area .about-content p {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Trust ESWASA section */
        .trust-eswasa-section {
            padding: 40px 0 !important;
        }
        .trust-eswasa-section .col-lg-4 h2 {
            font-size: 1.6rem !important;
            margin-bottom: 30px;
        }
        /* 4 marks become 2x2 grid on mobile */
        .trust-eswasa-section .col-lg-8 .row .col-3 {
            flex: 0 0 50%;
            max-width: 50%;
            margin-bottom: 20px;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:first-child {
            height: 120px !important;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:last-child {
            font-size: 0.85rem !important;
            margin-top: 8px !important;
        }

        /* CTA section */
        .cta-area-three {
            background: linear-gradient(to bottom, #e8e3f7 25%, #2B3388 25%) !important;
        }
        .cta__wrapper {
            flex-direction: column;
            text-align: center;
            padding: 30px 20px !important;
        }
        .cta__wrapper .section__title .title {
            font-size: 1.4rem !important;
        }
        .cta__desc p {
            font-size: 0.85rem;
        }

        /* Hide decorative background elements on mobile */
        .discover-bg-elements .bg-network,
        .discover-bg-elements .bg-hexagons,
        .discover-bg-elements .bg-flow,
        .discover-bg-elements .bg-molecule,
        .discover-bg-elements .bg-barcode,
        .trust-bg-elements .bg-starburst,
        .trust-bg-elements .bg-laurel,
        .trust-bg-elements .bg-stamp,
        .trust-bg-elements .bg-shield,
        .trust-bg-elements .bg-ribbon,
        .about-bg-elements .bg-badge,
        .about-bg-elements .bg-gear,
        .about-bg-elements .bg-lines,
        .about-bg-elements .bg-doc,
        .about-bg-elements .bg-circle-pattern {
            display: none;
        }

        /* Facebook sticky - icon only on mobile */
        .fb-sticky .fb-label {
            display: none;
        }
        .fb-sticky {
            writing-mode: horizontal-tb !important;
            text-orientation: initial !important;
            width: 44px !important;
            height: 44px !important;
            border-radius: 50% !important;
            padding: 0 !important;
            rotate: 0deg !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .fb-sticky .fb-icon {
            rotate: 0deg !important;
            margin-bottom: 0 !important;
            background: none !important;
            padding: 0 !important;
            color: white !important;
            font-size: 20px;
        }
    }

    /* ========== SMALL MOBILE (max 480px) ========== */
    @media (max-width: 480px) {
        /* Slider even smaller */
        .slider-area .tp-caption h2 {
            font-size: 1rem !important;
        }
        .slider-area .tp-caption p {
            font-size: 0.75rem !important;
            max-width: 90%;
        }

        /* Trust ESWASA - tighter */
        .trust-eswasa-section .col-lg-4 h2 {
            font-size: 1.3rem !important;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:first-child {
            height: 100px !important;
        }
        .trust-eswasa-section .col-lg-8 .row .col-3 > div:last-child {
            font-size: 0.75rem !important;
        }

        /* About section */
        .about-eswasa-area .about-content .title {
            font-size: 1.3rem !important;
        }

        /* Swiper navigation arrows smaller */
        .swiper-button-next,
        .swiper-button-prev {
            transform: scale(0.7);
        }

        /* CTA */
        .cta-area-three {
            background: linear-gradient(to bottom, #e8e3f7 20%, #2B3388 20%) !important;
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

                    <!-- OVERLAY LAYER -->
                    <div class="tp-caption tp-overlay-layer tp-resizeme"
                        data-x="center" data-hoffset="0"
                        data-y="center" data-voffset="0"
                        data-width="full"
                        data-height="full"
                        data-basealign="slide"
                        data-transform_idle="o:1;"
                        data-start="0"
                        style="z-index: 1;
                                background: linear-gradient(to top, rgba(0, 0, 0, 0.5), rgba(255, 255, 255, 0)); 
                                width: 100%;
                                height: 100%;
                                position: absolute;">
                    </div>

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

                    <!-- LAYER NR. 2 (Description) -->
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
                        <div><p><?php echo htmlspecialchars($row['description'] ?? ''); ?></p></div>
                    </div>

                    <!-- LAYER NR. 3 (Button) -->
                    <div class="tp-caption lfb ltt tp-resizeme rs-parallaxlevel-10"
                        data-x="left" data-hoffset="10"
                        data-y="center" data-voffset="90"
                        data-speed="1400"
                        data-start="1400"
                        data-endspeed="1200"
                        data-easing="easeOutExpo"
                        data-elementdelay="0.01"
                        data-endelementdelay="0.1"
                        style="z-index: 5;">
                        <?php if (!empty($row['url'])) { ?>
                        <a href="<?php echo htmlspecialchars($row['url']); ?>" class="slider-btn slider-btn-1" target="_blank" rel="noopener">READ MORE</a>
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

    /* Hexagonal honeycomb pattern - represents structured systems */
    .discover-bg-elements {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    /* Flowing circuit paths */
    .discover-bg-elements .bg-circuit {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.20;
    }

    /* Network nodes cluster - top right */
    .discover-bg-elements .bg-network {
        position: absolute;
        top: -20px;
        right: -20px;
        width: 350px;
        height: 350px;
        opacity: 0.20;
    }

    /* Hexagon cluster - bottom left */
    .discover-bg-elements .bg-hexagons {
        position: absolute;
        bottom: -40px;
        left: -20px;
        width: 300px;
        height: 300px;
        opacity: 0.20;
    }

    /* Flow arrows - mid left */
    .discover-bg-elements .bg-flow {
        position: absolute;
        top: 50%;
        left: 5%;
        transform: translateY(-50%);
        width: 160px;
        height: 300px;
        opacity: 0.20;
    }

    /* Molecule / atom structure - center */
    .discover-bg-elements .bg-molecule {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 400px;
        height: 400px;
        opacity: 0.20;
    }

    /* Barcode / standards code - bottom right */
    .discover-bg-elements .bg-barcode {
        position: absolute;
        bottom: 20px;
        right: 40px;
        width: 120px;
        height: 80px;
        opacity: 0.20;
    }
</style>
<section class="courses-area bg-gray" style="padding-top: 15px; padding-bottom: 15px;">
    <!-- Discover section decorative background -->
    <div class="discover-bg-elements">
        <!-- Circuit board paths -->
        <svg class="bg-circuit" viewBox="0 0 1200 400" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Horizontal traces -->
            <path d="M0 80 H200 L220 60 H400 L420 80 H600" stroke="#fff" stroke-width="1.5" fill="none"/>
            <path d="M500 200 H700 L730 170 H900 L920 200 H1200" stroke="#fff" stroke-width="1.5" fill="none"/>
            <path d="M0 320 H150 L170 300 H350 L380 320 H550" stroke="#fff" stroke-width="1" fill="none"/>
            <path d="M700 340 H850 L870 310 H1050 L1080 340 H1200" stroke="#fff" stroke-width="1" fill="none"/>
            <!-- Vertical traces -->
            <path d="M300 0 V100 L320 120 V250" stroke="#fff" stroke-width="1" fill="none"/>
            <path d="M800 0 V80 L780 100 V200 L800 220 V400" stroke="#fff" stroke-width="1" fill="none"/>
            <path d="M1050 0 V150 L1030 170 V300" stroke="#fff" stroke-width="1" fill="none"/>
            <!-- Junction nodes -->
            <circle cx="200" cy="80" r="4" fill="#fff"/>
            <circle cx="420" cy="80" r="4" fill="#fff"/>
            <circle cx="700" cy="200" r="5" fill="#fff"/>
            <circle cx="920" cy="200" r="4" fill="#fff"/>
            <circle cx="300" cy="100" r="3" fill="#fff"/>
            <circle cx="800" cy="80" r="4" fill="#fff"/>
            <circle cx="800" cy="220" r="3" fill="#fff"/>
            <circle cx="150" cy="320" r="3" fill="#fff"/>
            <circle cx="380" cy="320" r="3" fill="#fff"/>
            <circle cx="1050" cy="150" r="4" fill="#fff"/>
            <!-- Small square IC chips -->
            <rect x="410" y="70" width="20" height="20" rx="2" stroke="#fff" stroke-width="1.5" fill="none"/>
            <rect x="690" y="190" width="22" height="22" rx="2" stroke="#fff" stroke-width="1.5" fill="none"/>
            <rect x="1040" y="140" width="18" height="18" rx="2" stroke="#fff" stroke-width="1.5" fill="none"/>
        </svg>

        <!-- Network / connected nodes -->
        <svg class="bg-network" viewBox="0 0 350 350" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Connections -->
            <line x1="175" y1="175" x2="80" y2="60" stroke="#fff" stroke-width="1.2"/>
            <line x1="175" y1="175" x2="280" y2="80" stroke="#fff" stroke-width="1.2"/>
            <line x1="175" y1="175" x2="300" y2="220" stroke="#fff" stroke-width="1.2"/>
            <line x1="175" y1="175" x2="120" y2="290" stroke="#fff" stroke-width="1.2"/>
            <line x1="175" y1="175" x2="50" y2="180" stroke="#fff" stroke-width="1"/>
            <line x1="80" y1="60" x2="280" y2="80" stroke="#fff" stroke-width="0.8" stroke-dasharray="4 4"/>
            <line x1="280" y1="80" x2="300" y2="220" stroke="#fff" stroke-width="0.8" stroke-dasharray="4 4"/>
            <line x1="120" y1="290" x2="50" y2="180" stroke="#fff" stroke-width="0.8" stroke-dasharray="4 4"/>
            <!-- Outer ring nodes -->
            <circle cx="80" cy="60" r="14" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="80" cy="60" r="6" fill="#fff" opacity="0.4"/>
            <circle cx="280" cy="80" r="12" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="280" cy="80" r="5" fill="#fff" opacity="0.4"/>
            <circle cx="300" cy="220" r="14" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="300" cy="220" r="6" fill="#fff" opacity="0.4"/>
            <circle cx="120" cy="290" r="11" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="120" cy="290" r="5" fill="#fff" opacity="0.4"/>
            <circle cx="50" cy="180" r="10" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="50" cy="180" r="4" fill="#fff" opacity="0.4"/>
            <!-- Central hub -->
            <circle cx="175" cy="175" r="22" stroke="#fff" stroke-width="2" fill="none"/>
            <circle cx="175" cy="175" r="14" stroke="#fff" stroke-width="1" fill="none"/>
            <circle cx="175" cy="175" r="7" fill="#fff" opacity="0.5"/>
        </svg>

        <!-- Honeycomb hexagons -->
        <svg class="bg-hexagons" viewBox="0 0 300 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="75,25 112,45 112,85 75,105 38,85 38,45" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="150,25 187,45 187,85 150,105 113,85 113,45" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="112,85 149,105 149,145 112,165 75,145 75,105" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="187,85 224,105 224,145 187,165 150,145 150,105" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="75,145 112,165 112,205 75,225 38,205 38,165" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="150,145 187,165 187,205 150,225 113,205 113,165" stroke="#fff" stroke-width="1.5" fill="none"/>
            <polygon points="225,25 262,45 262,85 225,105 188,85 188,45" stroke="#fff" stroke-width="1.2" fill="none"/>
            <polygon points="37,85 74,105 74,145 37,165 0,145 0,105" stroke="#fff" stroke-width="1.2" fill="none"/>
            <!-- Small dots at hex centers -->
            <circle cx="75" cy="65" r="3" fill="#fff" opacity="0.3"/>
            <circle cx="150" cy="65" r="3" fill="#fff" opacity="0.3"/>
            <circle cx="112" cy="125" r="3" fill="#fff" opacity="0.3"/>
            <circle cx="187" cy="125" r="3" fill="#fff" opacity="0.3"/>
            <circle cx="75" cy="185" r="3" fill="#fff" opacity="0.3"/>
            <circle cx="150" cy="185" r="3" fill="#fff" opacity="0.3"/>
        </svg>

        <!-- Flow process arrows -->
        <svg class="bg-flow" viewBox="0 0 160 300" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Arrow 1 -->
            <path d="M40 30 L40 80" stroke="#fff" stroke-width="2" fill="none"/>
            <polygon points="30,75 40,90 50,75" fill="#fff" opacity="0.5"/>
            <!-- Node 1 -->
            <circle cx="40" cy="30" r="8" stroke="#fff" stroke-width="1.5" fill="none"/>
            <!-- Arrow 2 -->
            <path d="M40 90 L80 120 L80 170" stroke="#fff" stroke-width="2" fill="none"/>
            <polygon points="70,165 80,180 90,165" fill="#fff" opacity="0.5"/>
            <!-- Node 2 -->
            <rect x="68" y="108" width="24" height="24" rx="4" stroke="#fff" stroke-width="1.5" fill="none"/>
            <!-- Arrow 3 -->
            <path d="M80 180 L50 210 L50 260" stroke="#fff" stroke-width="2" fill="none"/>
            <polygon points="40,255 50,270 60,255" fill="#fff" opacity="0.5"/>
            <!-- Node 3 -->
            <polygon points="50,200 62,210 62,225 50,235 38,225 38,210" stroke="#fff" stroke-width="1.5" fill="none"/>
        </svg>

        <!-- Molecule / interconnected atoms -->
        <svg class="bg-molecule" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Orbital rings -->
            <ellipse cx="200" cy="200" rx="180" ry="70" stroke="#fff" stroke-width="0.8" fill="none" transform="rotate(-30 200 200)"/>
            <ellipse cx="200" cy="200" rx="180" ry="70" stroke="#fff" stroke-width="0.8" fill="none" transform="rotate(30 200 200)"/>
            <ellipse cx="200" cy="200" rx="180" ry="70" stroke="#fff" stroke-width="0.8" fill="none" transform="rotate(90 200 200)"/>
            <!-- Center nucleus -->
            <circle cx="200" cy="200" r="16" stroke="#fff" stroke-width="1.5" fill="none"/>
            <circle cx="200" cy="200" r="6" fill="#fff" opacity="0.3"/>
            <!-- Electrons on orbits -->
            <circle cx="100" cy="130" r="5" fill="#fff" opacity="0.3"/>
            <circle cx="310" cy="260" r="5" fill="#fff" opacity="0.3"/>
            <circle cx="140" cy="310" r="4" fill="#fff" opacity="0.3"/>
            <circle cx="270" cy="100" r="4" fill="#fff" opacity="0.3"/>
            <circle cx="200" cy="70" r="4" fill="#fff" opacity="0.3"/>
            <circle cx="200" cy="330" r="4" fill="#fff" opacity="0.3"/>
        </svg>

        <!-- Barcode -->
        <svg class="bg-barcode" viewBox="0 0 120 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="5" width="3" height="50" fill="#fff"/>
            <rect x="11" y="5" width="2" height="50" fill="#fff"/>
            <rect x="16" y="5" width="4" height="50" fill="#fff"/>
            <rect x="23" y="5" width="1" height="50" fill="#fff"/>
            <rect x="27" y="5" width="3" height="50" fill="#fff"/>
            <rect x="33" y="5" width="2" height="50" fill="#fff"/>
            <rect x="38" y="5" width="1" height="50" fill="#fff"/>
            <rect x="42" y="5" width="4" height="50" fill="#fff"/>
            <rect x="49" y="5" width="2" height="50" fill="#fff"/>
            <rect x="54" y="5" width="3" height="50" fill="#fff"/>
            <rect x="60" y="5" width="1" height="50" fill="#fff"/>
            <rect x="64" y="5" width="2" height="50" fill="#fff"/>
            <rect x="69" y="5" width="4" height="50" fill="#fff"/>
            <rect x="76" y="5" width="1" height="50" fill="#fff"/>
            <rect x="80" y="5" width="3" height="50" fill="#fff"/>
            <rect x="86" y="5" width="2" height="50" fill="#fff"/>
            <rect x="91" y="5" width="4" height="50" fill="#fff"/>
            <rect x="98" y="5" width="1" height="50" fill="#fff"/>
            <rect x="102" y="5" width="3" height="50" fill="#fff"/>
            <rect x="108" y="5" width="2" height="50" fill="#fff"/>
            <rect x="113" y="5" width="3" height="50" fill="#fff"/>
            <!-- Number below barcode -->
            <text x="60" y="72" text-anchor="middle" fill="#fff" font-size="9" font-family="monospace" opacity="0.6">SZNS 001:2024</text>
        </svg>
    </div>

    <div class="container">
        <div class="section__title-wrap mb-55">
            <div class="row align-items-center gap-4 gap-md-0">
                <div class="col-md-8">
                    <div class="section__title text-center text-md-start">
                        <h2 class="title tg-svg" style="color: #2e3191;">Discover</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Swiper -->
        <div class="swiper coursesSlider">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="Certification.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Certification</b></a></h4>
                            <p>Certification to Management Systems and products. Let us assist you in demonstrating your organization's ability to meet requirements and needs.</p>
                            <a href="Certification.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="product.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Product Testing </b></a></h4>
                            <p>Food and product testing in microbiology. Testing performed in accordance to international standards. </p>
                            <a href="product.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="Standards.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Standards Development </b></a></h4>
                            <p>Bringing together different expertise and experiences, to develop mutually accepted solutions to common challenges. </p>
                            <a href="Standards.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Added Fourth Slide -->
                <div class="swiper-slide">
                    <div class="blog__post-item shine__animate-item">
                        <div class="blog__post-content">
                            <a href="training-about.php" class="cat"><img src="assets/img/logo/verify.png" width="27px"></a>
                            <h4 class="title"><a href="#"><b>Training & Development </b></a></h4>
                            <p>Enhance your knowledge and skills with our specialized training programs, including Quality Management Systems Internal Auditing (SZNS ISO 19011:2018).</p>
                            <a href="training-about.php" class="cat">Read More <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <!-- End of Added Fourth Slide -->

            </div>
            
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
            
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
</section>

<!-- course-area-end -->

<!-- About Us Section -->
<style>
.about-eswasa-area {
    position: relative;
    overflow: hidden;
    background: #fff;
}

/* Blueprint-style grid pattern */
.about-eswasa-area::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image:
        /* Main grid */
        linear-gradient(rgba(46, 49, 145, 0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(46, 49, 145, 0.04) 1px, transparent 1px),
        /* Sub-grid */
        linear-gradient(rgba(46, 49, 145, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(46, 49, 145, 0.02) 1px, transparent 1px);
    background-size:
        80px 80px,
        80px 80px,
        20px 20px,
        20px 20px;
    z-index: 0;
}

/* Faded measurement ruler along top edge */
.about-eswasa-area::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 30px;
    background-image:
        repeating-linear-gradient(90deg,
            rgba(46, 49, 145, 0.08) 0px, rgba(46, 49, 145, 0.08) 1px, transparent 1px, transparent 10px
        ),
        repeating-linear-gradient(90deg,
            rgba(46, 49, 145, 0.12) 0px, rgba(46, 49, 145, 0.12) 2px, transparent 2px, transparent 80px
        );
    background-size: 10px 10px, 80px 20px;
    background-position: 0 0, 0 0;
    z-index: 0;
}

/* Decorative SVG elements */
.about-bg-elements {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    z-index: 0;
    pointer-events: none;
}

/* Faded checkmark / certification badge - top right */
.about-bg-elements .bg-badge {
    position: absolute;
    top: 30px;
    right: 40px;
    width: 180px;
    height: 180px;
    opacity: 0.04;
}

/* Faded gear / cog - bottom left */
.about-bg-elements .bg-gear {
    position: absolute;
    bottom: -30px;
    left: -30px;
    width: 220px;
    height: 220px;
    opacity: 0.03;
}

/* Diagonal measurement lines - corner accent */
.about-bg-elements .bg-lines {
    position: absolute;
    top: 50px;
    left: 30px;
    width: 120px;
    height: 120px;
    opacity: 0.06;
}

/* ISO-style document watermark */
.about-bg-elements .bg-doc {
    position: absolute;
    bottom: 40px;
    right: 15%;
    width: 140px;
    height: 180px;
    opacity: 0.03;
}

/* Dotted circle pattern */
.about-bg-elements .bg-circle-pattern {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 500px;
    height: 500px;
    opacity: 0.025;
}

.about-eswasa-area .container {
    position: relative;
    z-index: 1;
}
</style>

<section class="about-eswasa-area section-pt-120 section-pb-90">
    <!-- Faded background decorative elements -->
    <div class="about-bg-elements">
        <!-- Certification badge / shield -->
        <svg class="bg-badge" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 10L120 40H160L140 70L150 110L100 90L50 110L60 70L40 40H80L100 10Z" stroke="#2e3191" stroke-width="3" fill="none"/>
            <circle cx="100" cy="68" r="30" stroke="#2e3191" stroke-width="2.5" fill="none"/>
            <path d="M85 68L95 78L118 55" stroke="#2e3191" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="100" cy="68" r="50" stroke="#2e3191" stroke-width="1.5" fill="none" stroke-dasharray="4 4"/>
        </svg>

        <!-- Gear / precision cog -->
        <svg class="bg-gear" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 30 L108 50 L130 42 L125 65 L148 65 L135 82 L155 95 L135 102 L145 125 L122 118 L118 140 L100 128 L82 140 L78 118 L55 125 L65 102 L45 95 L65 82 L52 65 L75 65 L70 42 L92 50 Z" stroke="#2e3191" stroke-width="2" fill="none"/>
            <circle cx="100" cy="90" r="25" stroke="#2e3191" stroke-width="2" fill="none"/>
            <circle cx="100" cy="90" r="12" stroke="#2e3191" stroke-width="1.5" fill="none"/>
        </svg>

        <!-- Technical measurement lines -->
        <svg class="bg-lines" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="0" y1="0" x2="120" y2="0" stroke="#2e3191" stroke-width="1"/>
            <line x1="0" y1="0" x2="0" y2="120" stroke="#2e3191" stroke-width="1"/>
            <!-- Tick marks horizontal -->
            <line x1="20" y1="0" x2="20" y2="8" stroke="#2e3191" stroke-width="1"/>
            <line x1="40" y1="0" x2="40" y2="12" stroke="#2e3191" stroke-width="1.5"/>
            <line x1="60" y1="0" x2="60" y2="8" stroke="#2e3191" stroke-width="1"/>
            <line x1="80" y1="0" x2="80" y2="12" stroke="#2e3191" stroke-width="1.5"/>
            <line x1="100" y1="0" x2="100" y2="8" stroke="#2e3191" stroke-width="1"/>
            <!-- Tick marks vertical -->
            <line x1="0" y1="20" x2="8" y2="20" stroke="#2e3191" stroke-width="1"/>
            <line x1="0" y1="40" x2="12" y2="40" stroke="#2e3191" stroke-width="1.5"/>
            <line x1="0" y1="60" x2="8" y2="60" stroke="#2e3191" stroke-width="1"/>
            <line x1="0" y1="80" x2="12" y2="80" stroke="#2e3191" stroke-width="1.5"/>
            <line x1="0" y1="100" x2="8" y2="100" stroke="#2e3191" stroke-width="1"/>
            <!-- Diagonal precision line -->
            <line x1="10" y1="10" x2="110" y2="110" stroke="#2e3191" stroke-width="0.8" stroke-dasharray="6 4"/>
            <line x1="10" y1="30" x2="90" y2="110" stroke="#2e3191" stroke-width="0.5" stroke-dasharray="3 5"/>
        </svg>

        <!-- Document / standards document -->
        <svg class="bg-doc" viewBox="0 0 140 180" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="5" width="110" height="150" rx="4" stroke="#2e3191" stroke-width="2" fill="none"/>
            <rect x="15" y="20" width="60" height="6" rx="2" fill="#2e3191" opacity="0.3"/>
            <rect x="15" y="35" width="85" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <rect x="15" y="44" width="85" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <rect x="15" y="53" width="70" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <rect x="15" y="68" width="85" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <rect x="15" y="77" width="85" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <rect x="15" y="86" width="50" height="3" rx="1.5" fill="#2e3191" opacity="0.2"/>
            <!-- Stamp / seal -->
            <circle cx="85" cy="120" r="22" stroke="#2e3191" stroke-width="2" fill="none"/>
            <circle cx="85" cy="120" r="17" stroke="#2e3191" stroke-width="1" fill="none"/>
            <path d="M78 120L83 125L93 115" stroke="#2e3191" stroke-width="2" fill="none" stroke-linecap="round"/>
        </svg>

        <!-- Concentric dotted circles -->
        <svg class="bg-circle-pattern" viewBox="0 0 500 500" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="250" cy="250" r="80" stroke="#2e3191" stroke-width="1" fill="none" stroke-dasharray="2 6"/>
            <circle cx="250" cy="250" r="130" stroke="#2e3191" stroke-width="1" fill="none" stroke-dasharray="3 8"/>
            <circle cx="250" cy="250" r="180" stroke="#2e3191" stroke-width="0.8" fill="none" stroke-dasharray="4 10"/>
            <circle cx="250" cy="250" r="230" stroke="#2e3191" stroke-width="0.6" fill="none" stroke-dasharray="5 14"/>
        </svg>
    </div>

    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-image">
                    <img src="admin/uploads/image33.jpg" alt="ESWASA Building" class="img-fluid rounded shadow">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content">
                    <div class="section__title mb-4">
                        <h2 class="title tg-svg" style="color: #2e3191;">About Us</h2>
                    </div>
                    <p>
                        The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023.
                    </p>
                    <p>
                        ESWASA is a national standards body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Us Section End -->


<!-- ESWASA Section -->
<style>
    .trust-eswasa-section {
        position: relative;
        overflow: hidden;
        background: #e8e3f7;
        color: #000;
        padding: 100px 0;
    }
    .trust-eswasa-section > .container {
        position: relative;
        z-index: 1;
    }

    /* Decorative elements wrapper */
    .trust-bg-elements {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    /* Radial starburst behind section */
    .trust-bg-elements .bg-starburst {
        position: absolute;
        top: 50%;
        left: 20%;
        transform: translate(-50%, -50%);
        width: 600px;
        height: 600px;
        opacity: 0.06;
    }

    /* Large laurel wreath - right side */
    .trust-bg-elements .bg-laurel {
        position: absolute;
        top: 50%;
        right: -40px;
        transform: translateY(-50%);
        width: 400px;
        height: 400px;
        opacity: 0.07;
    }

    /* Stamp seal - top left */
    .trust-bg-elements .bg-stamp {
        position: absolute;
        top: 20px;
        left: 40px;
        width: 200px;
        height: 200px;
        opacity: 0.05;
    }

    /* Shield - bottom right */
    .trust-bg-elements .bg-shield {
        position: absolute;
        bottom: 10px;
        right: 25%;
        width: 180px;
        height: 220px;
        opacity: 0.05;
    }

    /* Award ribbon - top right */
    .trust-bg-elements .bg-ribbon {
        position: absolute;
        top: -10px;
        right: 15%;
        width: 140px;
        height: 200px;
        opacity: 0.06;
    }

    /* Diamond grid pattern overlay */
    .trust-eswasa-section::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image:
            repeating-linear-gradient(
                45deg,
                transparent,
                transparent 40px,
                rgba(46, 49, 145, 0.025) 40px,
                rgba(46, 49, 145, 0.025) 41px
            ),
            repeating-linear-gradient(
                -45deg,
                transparent,
                transparent 40px,
                rgba(46, 49, 145, 0.025) 40px,
                rgba(46, 49, 145, 0.025) 41px
            );
        z-index: 0;
    }

    /* Subtle gradient glow behind marks */
    .trust-eswasa-section::after {
        content: '';
        position: absolute;
        top: 50%;
        right: 10%;
        transform: translateY(-50%);
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(46, 49, 145, 0.06) 0%, transparent 70%);
        z-index: 0;
    }
</style>

<section class="trust-eswasa-section">
    <!-- Trust section decorative background -->
    <div class="trust-bg-elements">
        <!-- Starburst / rosette -->
        <svg class="bg-starburst" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g transform="translate(300,300)">
                <!-- Outer rays -->
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(0)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(15)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(30)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(45)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(60)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(75)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(90)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(105)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(120)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(135)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(150)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(165)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(180)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(195)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(210)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(225)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(240)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(255)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(270)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(285)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(300)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(315)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(330)"/>
                <line x1="0" y1="0" x2="0" y2="-280" stroke="#2e3191" stroke-width="1" transform="rotate(345)"/>
                <!-- Concentric circles -->
                <circle cx="0" cy="0" r="60" stroke="#2e3191" stroke-width="1.5" fill="none"/>
                <circle cx="0" cy="0" r="120" stroke="#2e3191" stroke-width="1" fill="none"/>
                <circle cx="0" cy="0" r="180" stroke="#2e3191" stroke-width="0.8" fill="none" stroke-dasharray="6 4"/>
                <circle cx="0" cy="0" r="240" stroke="#2e3191" stroke-width="0.5" fill="none" stroke-dasharray="4 6"/>
            </g>
        </svg>

        <!-- Laurel wreath -->
        <svg class="bg-laurel" viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left branch -->
            <path d="M180 350 Q140 300 130 250 Q120 200 140 150 Q155 110 175 80 Q185 60 200 40" stroke="#2e3191" stroke-width="2" fill="none"/>
            <!-- Left leaves -->
            <ellipse cx="150" cy="280" rx="25" ry="10" transform="rotate(-50 150 280)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="135" cy="240" rx="25" ry="10" transform="rotate(-40 135 240)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="130" cy="200" rx="25" ry="10" transform="rotate(-30 130 200)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="138" cy="160" rx="25" ry="10" transform="rotate(-20 138 160)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="155" cy="125" rx="22" ry="9" transform="rotate(-10 155 125)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="175" cy="95" rx="20" ry="8" transform="rotate(0 175 95)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="188" cy="68" rx="18" ry="7" transform="rotate(10 188 68)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <!-- Right branch -->
            <path d="M220 350 Q260 300 270 250 Q280 200 260 150 Q245 110 225 80 Q215 60 200 40" stroke="#2e3191" stroke-width="2" fill="none"/>
            <!-- Right leaves -->
            <ellipse cx="250" cy="280" rx="25" ry="10" transform="rotate(50 250 280)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="265" cy="240" rx="25" ry="10" transform="rotate(40 265 240)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="270" cy="200" rx="25" ry="10" transform="rotate(30 270 200)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="262" cy="160" rx="25" ry="10" transform="rotate(20 262 160)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="245" cy="125" rx="22" ry="9" transform="rotate(10 245 125)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="225" cy="95" rx="20" ry="8" transform="rotate(0 225 95)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <ellipse cx="212" cy="68" rx="18" ry="7" transform="rotate(-10 212 68)" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <!-- Star at top -->
            <polygon points="200,25 205,38 220,38 208,47 213,60 200,52 187,60 192,47 180,38 195,38" fill="#2e3191" opacity="0.3"/>
        </svg>

        <!-- Official stamp seal -->
        <svg class="bg-stamp" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Outer scalloped edge -->
            <path d="M100 10 L108 25 L120 15 L122 32 L138 27 L135 44 L150 44 L142 58 L158 64 L146 75 L158 86 L142 92 L150 106 L135 106 L138 123 L122 118 L120 135 L108 125 L100 140 L92 125 L80 135 L78 118 L62 123 L65 106 L50 106 L58 92 L42 86 L54 75 L42 64 L58 58 L50 44 L65 44 L62 27 L78 32 L80 15 L92 25 Z" stroke="#2e3191" stroke-width="1.5" fill="none"/>
            <!-- Inner circles -->
            <circle cx="100" cy="75" r="45" stroke="#2e3191" stroke-width="1.5" fill="none"/>
            <circle cx="100" cy="75" r="38" stroke="#2e3191" stroke-width="1" fill="none"/>
            <!-- Approved text arc (simplified as lines) -->
            <path d="M65 60 Q100 45 135 60" stroke="#2e3191" stroke-width="1" fill="none"/>
            <path d="M65 90 Q100 105 135 90" stroke="#2e3191" stroke-width="1" fill="none"/>
            <!-- Center checkmark -->
            <path d="M82 75 L95 88 L120 62" stroke="#2e3191" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            <!-- Small stars -->
            <circle cx="72" cy="75" r="3" fill="#2e3191" opacity="0.4"/>
            <circle cx="128" cy="75" r="3" fill="#2e3191" opacity="0.4"/>
        </svg>

        <!-- Shield with banner -->
        <svg class="bg-shield" viewBox="0 0 180 220" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Shield shape -->
            <path d="M90 10 L160 40 L160 120 Q160 170 90 210 Q20 170 20 120 L20 40 Z" stroke="#2e3191" stroke-width="2" fill="none"/>
            <!-- Inner shield -->
            <path d="M90 30 L145 55 L145 115 Q145 158 90 190 Q35 158 35 115 L35 55 Z" stroke="#2e3191" stroke-width="1.2" fill="none"/>
            <!-- Horizontal dividers -->
            <line x1="50" y1="80" x2="130" y2="80" stroke="#2e3191" stroke-width="1"/>
            <line x1="55" y1="130" x2="125" y2="130" stroke="#2e3191" stroke-width="1"/>
            <!-- Star in upper section -->
            <polygon points="90,50 95,63 110,63 98,72 103,85 90,77 77,85 82,72 70,63 85,63" stroke="#2e3191" stroke-width="1" fill="none"/>
            <!-- Bars in lower section -->
            <rect x="60" y="90" width="60" height="6" rx="2" fill="#2e3191" opacity="0.15"/>
            <rect x="65" y="102" width="50" height="6" rx="2" fill="#2e3191" opacity="0.15"/>
            <rect x="70" y="114" width="40" height="6" rx="2" fill="#2e3191" opacity="0.15"/>
        </svg>

        <!-- Award ribbon -->
        <svg class="bg-ribbon" viewBox="0 0 140 200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Rosette circle -->
            <circle cx="70" cy="70" r="50" stroke="#2e3191" stroke-width="1.5" fill="none"/>
            <circle cx="70" cy="70" r="40" stroke="#2e3191" stroke-width="1" fill="none"/>
            <circle cx="70" cy="70" r="30" stroke="#2e3191" stroke-width="1" fill="none" stroke-dasharray="3 3"/>
            <!-- Petal scallops around rosette -->
            <circle cx="70" cy="15" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="108" cy="30" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="125" cy="65" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="110" cy="102" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="70" cy="125" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="30" cy="102" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="15" cy="65" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <circle cx="32" cy="30" r="8" stroke="#2e3191" stroke-width="0.8" fill="none"/>
            <!-- Center number -->
            <text x="70" y="76" text-anchor="middle" fill="#2e3191" font-size="22" font-weight="bold" font-family="serif" opacity="0.4">1</text>
            <!-- Hanging ribbons -->
            <path d="M55 118 L35 190 L55 170 L70 195 L70 118" stroke="#2e3191" stroke-width="1.5" fill="none"/>
            <path d="M85 118 L105 190 L85 170 L70 195 L70 118" stroke="#2e3191" stroke-width="1.5" fill="none"/>
        </svg>
    </div>

    <div class="container">
        <div class="row align-items-center">
            <!-- Text – Left side -->
            <div class="col-lg-4 text-center text-lg-start">
                <h2 style="
                    font-size:2.9rem; 
                    font-weight:900; 
                    line-height:1.2; 
                    margin:0;
                    color: #000;
                ">
                    Trust the ESWASA<br>
                    Approved Quality Assurance<br>
                    Mark of Excellence
                </h2>
            </div>

            <!-- Photos – Right side -->
            <div class="col-lg-8">
                <div class="row">
                    <!-- Photo 1 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/management.png" 
                                 alt="ESWASA Certification" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Management Systems Certification Mark</div>
                    </div>
                    
                    <!-- Photo 2 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/product.png" 
                                 alt="ESWASA Standards" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Product Certification Mark</div>
                    </div>
                    
                    <!-- Photo 3 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/compulsory.png" 
                                 alt="ESWASA Testing" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Compulsory Standards Quality Mark</div>
                    </div>
                    
                    <!-- Photo 4 -->
                    <div class="col-3 text-center" style="color: #000;">
                        <div style="height:220px; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            <img src="assets/img/Ingelo.png" 
                                 alt="ESWASA Approved" 
                                 class="img-fluid" 
                                 style="max-height:100%; max-width:100%; object-fit:contain;">
                        </div>
                        <div style="font-size:1.2rem; font-weight:500; margin-top:15px;">Ingelo Certification Scheme Mark</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End ESWASA Section -->

        <!-- cta-area -->
        <section class="cta-area-three" style="background: linear-gradient(to bottom, #e8e3f7 40%, #2B3388 40%);">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="cta__wrapper">
                            <div class="section__title white-title">
                                <h2 class="title tg-svg">Get Involved</h2>
                            </div>
                            <div class="cta__desc">
                                <p>Suggest a standard / Sign up to be a Technical Committee member / Get my organisation certified / Buy a Standard </p>
                            </div>
                            <div class="tg-button-wrap justify-content-center justify-content-md-end">
                                <a href="#" class="btn white-btn tg-svg"><span class="text">Learn More</span> <i class="fas fa-long-arrow-alt-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- cta-area-end -->


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

<div class="fb-page" data-href="    https://www.facebook.com/eswasaupdates    " data-tabs="timeline" data-width="" data-height="900" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/eswasaupdates    " class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/eswasaupdates    ">Eswatini Standards Authority - SWASA</a></blockquote></div>

</div>

<style>
  .fb-feed {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    background-color: #fff0;
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
    background-color: #2a3288;
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
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  z-index: 9998;
}

.fb-sticky { 
	background-color: #3b5998;
	color: white;
	padding: 10px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	border-radius: 0px 10px 10px 0px;
	width: 50px;
	height: 200px;
	writing-mode: vertical-rl;
	text-orientation: mixed;
	font-weight: bold;
	text-align: center;
	rotate: -90deg;
}
.fb-icon {
	rotate: 90deg;
	background-color: white;
	padding: 10px;
	border-radius: 50px;
	color: #3b5998;
	margin-bottom: 10px;
}
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
        // Wait for DOM to load completely
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper after all other scripts have run
            setTimeout(function() {
                const swiper = new Swiper('.coursesSlider', {
                    // Optional parameters
                    loop: true,
                    
                    // If we need pagination
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    
                    // Navigation arrows
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    
                    // Number of slides per view
                    slidesPerView: 1,
                    spaceBetween: 30,
                    
                    // Responsive breakpoints
                    breakpoints: {
                        640: {
                            slidesPerView: 1,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 30,
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                    },
                    
                    // Enable smooth transitions
                    speed: 800,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                });
            }, 100); // Small delay to ensure Revolution Slider is fully initialized
        });
    </script>
    
    <script>
            jQuery(document).ready(function() {         
                jQuery('.tp-banner').show().revolution(
                {
                    dottedOverlay:"none",
                    delay:5000,
                    startwidth:1170,
                    startheight:550,
                    hideThumbs:200,
                    thumbWidth:100,
                    thumbHeight:50,
                    thumbAmount:5,
                    navigationType:"bullet",
                    navigationArrows:"solo",
                    navigationStyle:"preview3",
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
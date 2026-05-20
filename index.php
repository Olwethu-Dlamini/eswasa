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
    /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 12px) ========== */
    body {
        font-family: Arial, sans-serif;
        font-size: 16px;
        color: #2B3388;
    }
    body h1, body h2, body h3, body h4, body h5, body h6 {
        font-family: Arial, sans-serif;
        color: #2B3388;
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


<!-- discover-infographic -->
<style>
    .discover-infographic {
        position: relative;
        background: rgba(43, 51, 136, 0.05);
        padding: 80px 0 90px;
    }

    .discover-header {
        text-align: center;
        margin-bottom: 56px;
    }
    .discover-header h2.discover-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2B3388;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.01em;
        display: inline-block;
        padding-bottom: 18px;
        border-bottom: 2px solid #2B3388;
    }

    .discover-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }
    .discover-card {
        background: #fff;
        border: none;
        border-radius: 6px;
        padding: 34px 30px 30px;
        text-decoration: none !important;
        color: #2B3388;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        min-height: 220px;
        box-shadow: 0 1px 3px rgba(43, 51, 136, 0.06);
        transition: background .25s ease, color .25s ease, transform .25s ease, box-shadow .25s ease;
    }
    .discover-card:hover {
        background: #2B3388;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(43, 51, 136, 0.22);
    }
    .discover-card .icon-wrap {
        width: 96px;
        height: 96px;
        margin: 0 0 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
    }
    .discover-card .icon-wrap svg {
        width: 92px;
        height: 92px;
        transition: color .25s ease;
    }
    .discover-card h4 {
        color: inherit;
        font-weight: 700;
        font-size: 18px;
        margin: 0;
        line-height: 1.3;
        letter-spacing: 0;
    }

    @media (max-width: 1199.98px) {
        .discover-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 991.98px) {
        .discover-card { padding: 28px 24px 24px; min-height: 200px; }
        .discover-card .icon-wrap { width: 80px; height: 80px; margin-bottom: 26px; }
        .discover-card .icon-wrap svg { width: 76px; height: 76px; }
        .discover-card h4 { font-size: 16px; }
    }
    @media (max-width: 767.98px) {
        .discover-header h2.discover-title { font-size: 1.65rem; }
    }
    @media (max-width: 575.98px) {
        .discover-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .discover-infographic { padding: 50px 0 60px; }
        .discover-card { padding: 22px 18px 20px; min-height: 170px; }
        .discover-card .icon-wrap { width: 64px; height: 64px; margin-bottom: 18px; }
        .discover-card .icon-wrap svg { width: 60px; height: 60px; }
        .discover-card h4 { font-size: 14px; }
        .discover-header { margin-bottom: 36px; }
        .discover-header h2.discover-title { font-size: 1.4rem; padding-bottom: 14px; }
    }
</style>
<section class="discover-infographic">
    <div class="container">
        <div class="discover-header">
            <h2 class="discover-title">Discover</h2>
        </div>
        <div class="discover-grid">
            <a href="services.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="6" y="14" width="36" height="26" rx="2"/>
                        <path d="M18 14 V11 C18 9.9 18.9 9 20 9 H28 C29.1 9 30 9.9 30 11 V14"/>
                        <line x1="6" y1="24" x2="42" y2="24"/>
                        <path d="M20 28 H28"/>
                        <circle cx="24" cy="24" r="2" fill="currentColor"/>
                    </svg>
                </div>
                <h4>Services</h4>
            </a>

            <a href="training-about.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20 L24 10 L44 20 L24 30 Z"/>
                        <path d="M12 24 V34 C12 36.5 17 40 24 40 C31 40 36 36.5 36 34 V24"/>
                        <path d="M44 20 V32"/>
                        <circle cx="44" cy="34" r="1.6" fill="currentColor"/>
                    </svg>
                </div>
                <h4>Training</h4>
            </a>

            <a href="Certification.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M24 4 L42 11 V24 C42 34 34 42 24 44 C14 42 6 34 6 24 V11 Z"/>
                        <path d="M16 24 L22 30 L33 18"/>
                    </svg>
                </div>
                <h4>Certification</h4>
            </a>

            <a href="Calibration.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 34 A 18 18 0 0 1 42 34"/>
                        <line x1="6" y1="34" x2="10" y2="34"/>
                        <line x1="42" y1="34" x2="38" y2="34"/>
                        <line x1="24" y1="16" x2="24" y2="20"/>
                        <line x1="13" y1="22" x2="16" y2="24"/>
                        <line x1="35" y1="22" x2="32" y2="24"/>
                        <line x1="24" y1="34" x2="33" y2="22"/>
                        <circle cx="24" cy="34" r="2.6" fill="currentColor"/>
                    </svg>
                </div>
                <h4>Calibration</h4>
            </a>

            <a href="Standards.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                        <path d="M28 6 V16 H38"/>
                        <circle cx="22" cy="30" r="5"/>
                        <path d="M22 22 V25 M22 35 V38 M14 30 H17 M27 30 H30 M16.3 24.3 L18.3 26.3 M25.7 33.7 L27.7 35.7 M16.3 35.7 L18.3 33.7 M25.7 26.3 L27.7 24.3"/>
                    </svg>
                </div>
                <h4>Standards</h4>
            </a>

            <a href="product.php" class="discover-card">
                <div class="icon-wrap">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 H30"/>
                        <path d="M20 6 V20 L10 38 C8.5 41 10.5 44 13.5 44 H34.5 C37.5 44 39.5 41 38 38 L28 20 V6"/>
                        <path d="M14 32 H34"/>
                        <circle cx="20" cy="36" r="1.4" fill="currentColor"/>
                        <circle cx="27" cy="38" r="1.4" fill="currentColor"/>
                        <circle cx="24" cy="33" r="1.2" fill="currentColor"/>
                    </svg>
                </div>
                <h4>Product Testing</h4>
            </a>
        </div>
    </div>
</section>

<!-- discover-infographic-end -->


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
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        padding-bottom: 18px;
        display: inline-block;
        border-bottom: 2px solid #2B3388;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    .marks-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }
    .mark-item {
        text-align: center;
        padding: 28px 18px 24px;
        background: #fff;
        border: 1px solid rgba(43, 51, 136, 0.15);
        border-radius: 4px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }
    .mark-item:hover {
        border-color: rgba(43, 51, 136, 0.45);
        box-shadow: 0 4px 12px rgba(43, 51, 136, 0.08);
    }
    .mark-image {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
    }
    .mark-image img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .mark-label {
        color: #2B3388;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.45;
        margin: 0;
    }
    @media (max-width: 991.98px) {
        .trust-eswasa-section { padding: 50px 0 60px; }
        .trust-eswasa-section .section-heading { margin-bottom: 40px; }
        .marks-grid { grid-template-columns: repeat(2, 1fr); gap: 18px; }
        .mark-image { height: 130px; }
    }
    @media (max-width: 575.98px) {
        .trust-eswasa-section { padding: 40px 0 48px; }
        .trust-eswasa-section .section-heading { margin-bottom: 32px; }
        .trust-eswasa-section .section-heading h2 { font-size: 1.4rem; padding-bottom: 14px; }
        .marks-grid { gap: 12px; }
        .mark-item { padding: 22px 12px 18px; }
        .mark-image { height: 110px; margin-bottom: 14px; }
        .mark-label { font-size: 12px; }
    }
</style>

<section class="trust-eswasa-section">
    <div class="container">
        <div class="section-heading">
            <h2>Certification Marks</h2>
        </div>
        <div class="marks-grid">
            <div class="mark-item">
                <div class="mark-image">
                    <img src="assets/img/management.png" alt="Management Systems Certification Mark">
                </div>
                <p class="mark-label">Management Systems Certification Mark</p>
            </div>
            <div class="mark-item">
                <div class="mark-image">
                    <img src="assets/img/product.png" alt="Product Certification Mark">
                </div>
                <p class="mark-label">Product Certification Mark</p>
            </div>
            <div class="mark-item">
                <div class="mark-image">
                    <img src="assets/img/compulsory.png" alt="Compulsory Standards Quality Mark">
                </div>
                <p class="mark-label">Compulsory Standards Quality Mark</p>
            </div>
            <div class="mark-item">
                <div class="mark-image">
                    <img src="assets/img/Ingelo.png" alt="Ingelo Certification Scheme Mark">
                </div>
                <p class="mark-label">Ingelo Certification Scheme Mark</p>
            </div>
        </div>
    </div>
</section>
<!-- End ESWASA Section -->

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
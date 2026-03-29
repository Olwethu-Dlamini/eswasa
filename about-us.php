<?php
// about-us.php — pulls content from page_content table
require_once('includes/db_connect.php');

// ── Load all about_* keys in one query ───────────────────────
$keys = [
    'about_intro', 'about_vision', 'about_mission', 'about_history',
    'about_val_transparency', 'about_val_people', 'about_val_responsiveness',
    'about_val_innovation', 'about_val_professionalism',
    'about_img_vision', 'about_img_mission', 'about_img_team', 'about_img_banner'
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
    <title>Who We Are - ESWASA</title>
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
        /* Breadcrumb White Text Override */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        
        .breadcrumb-separator i {
            color: #fff !important;
        }
        
        /* Restricted Mobile Fix for Images - Only affects main content */
        .main-area img {
            max-width: 100%;
            height: auto;
        }

        /* Responsive Banner Height */
        .banner-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .banner-wrapper img {
            width: 100%;
            height: 400px; /* Default desktop height */
            object-fit: cover;
        }

        /* Core Values Responsive Grid */
        .values-diagram-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px 0;
        }
        
        .values-center-image {
            max-width: 450px;
            width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
            border-radius: 15px;
        }

        .value-card-custom {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #f0f0f0;
            height: 100%;
        }

        .value-icon-circle {
            width: 50px;
            height: 50px;
            background: rgba(46, 49, 145, 0.1);
            color: #2E3191;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.3rem;
        }

        /* Sliders Styling - Optimized for Android */
        .bg_color3 { background-color: #e6f0fa; }
        .affiliations-slider {
            overflow: hidden;
            white-space: nowrap;
            padding: 20px 0;
            -webkit-overflow-scrolling: touch;
        }
        .slider-track {
            display: flex;
            width: calc(280px * 12); 
            animation: scroll 25s linear infinite;
            -webkit-animation: scroll 25s linear infinite;
            transform: translateZ(0); /* Hardware acceleration */
            -webkit-transform: translateZ(0);
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
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .logo-card-fixed img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        /* MISSION/VISION IMAGES */
        .mission-vision-img-wrapper {
            height: 300px;
            overflow: hidden;
            border-radius: 8px;
        }
        .mission-vision-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Android Scroll Keyframes */
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(calc(-280px * 6)); }
        }
        @-webkit-keyframes scroll {
            0% { -webkit-transform: translateX(0); }
            100% { -webkit-transform: translateX(calc(-280px * 6)); }
        }

        /* MOBILE OVERRIDES (Android/iOS) */
        @media (max-width: 768px) {
            .banner-wrapper img {
                height: 200px !important; /* Shorter banner on mobile */
            }
            .mission-vision-img-wrapper {
                height: 200px !important;
            }
            .logo-card-fixed {
                width: 180px !important;
                height: 110px !important;
            }
            .slider-item {
                width: 200px !important;
            }
            .slider-track {
                width: calc(200px * 12) !important;
            }
            @keyframes scroll {
                0% { transform: translateX(0); }
                100% { transform: translateX(calc(-200px * 6)); }
            }
            @-webkit-keyframes scroll {
                0% { -webkit-transform: translateX(0); }
                100% { -webkit-transform: translateX(calc(-200px * 6)); }
            }
            .values-center-image {
                max-width: 300px !important;
                margin: 20px auto !important;
            }
            .info-section h3 {
                font-size: 1.2rem !important;
            }
            .display-6 {
                font-size: 1.8rem !important;
            }
        }

        .section-divider {
            width: 100px;
            height: 4px;
            background: #2E3191;
            margin: 20px auto 0;
            border-radius: 2px;
        }
        
        .info-section {
            background: #f9f9f9;
            padding: 20px;
            margin: 10px 0;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        h2, h3, p { color: #333 !important; }
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
    <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('assets/img/bg.png'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb-content text-center text-md-start">
                        <nav class="breadcrumb justify-content-center justify-content-md-start">
                            <span><a href="index.php">Home</a></span>
                            <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                            <span>About Us</span>
                        </nav>
                        <h3 class="title">Who We Are</h3>
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
                            <h2 class="title" style="color: #2e3191;">About Us</h2>
                            <div class="section-divider"></div>
                        </div>
                        <p class="mt-4 lead px-2">
                            The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023.
                        </p>
                        <p class="lead px-2">
                            ESWASA is a national standards body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Image -->
    <div class="container px-3 mb-5">
        <div class="banner-wrapper">
            <img src="<?= htmlspecialchars($pc['about_img_banner']) ?>" alt="ESWASA Banner">
        </div>
    </div>
    
    <section class="py-4">
        <div class="container">
            <!-- VISION AND MISSION -->
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="info-section h-100 d-flex flex-column">
                        <h3>Vision</h3>
                        <p><strong><?= htmlspecialchars($pc['about_vision']) ?></strong></p>
                        <div class="mt-auto">
                            <div class="mission-vision-img-wrapper">
                                <img src="<?= htmlspecialchars($pc['about_img_vision']) ?>" alt="Vision">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section h-100 d-flex flex-column">
                        <h3>Mission</h3>
                        <p><strong><?= htmlspecialchars($pc['about_mission']) ?></strong></p>
                        <div class="mt-auto">
                            <div class="mission-vision-img-wrapper">
                                <img src="<?= htmlspecialchars($pc['about_img_mission']) ?>" alt="Mission">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CORE VALUES -->
            <div class="text-center mt-5 mb-4">
                <h2 class="fw-bold" style="color: #2E3191;">Our Core Values</h2>
                <div class="section-divider"></div>
            </div>

            <div class="values-diagram-container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-3 d-flex flex-column gap-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle"><i class="fas fa-eye"></i></div>
                            <h4>Transparency</h4>
                            <p><?= htmlspecialchars($pc['about_val_transparency']) ?></p>
                        </div>
                        <div class="value-card-custom">
                            <div class="value-icon-circle"><i class="fas fa-users"></i></div>
                            <h4>People-Centricity</h4>
                            <p><?= htmlspecialchars($pc['about_val_people']) ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <img src="COre Values.PNG" alt="Core Values" class="values-center-image shadow-sm border img-fluid">
                    </div>
                    <div class="col-lg-3 d-flex flex-column gap-3">
                        <div class="value-card-custom">
                            <div class="value-icon-circle"><i class="fas fa-bolt"></i></div>
                            <h4>Responsiveness</h4>
                            <p><?= htmlspecialchars($pc['about_val_responsiveness']) ?></p>
                        </div>
                        <div class="value-card-custom">
                            <div class="value-icon-circle"><i class="fas fa-award"></i></div>
                            <h4>Professionalism</h4>
                            <p><?= htmlspecialchars($pc['about_val_professionalism']) ?></p>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="value-card-custom mx-auto" style="max-width: 350px;">
                            <div class="value-icon-circle"><i class="fas fa-lightbulb"></i></div>
                            <h4>Innovation</h4>
                            <p><?= htmlspecialchars($pc['about_val_innovation']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- HISTORY -->
            <div class="info-section mt-5 mb-5">
                <h3>Brief History</h3>
                <?= render_paragraphs($pc['about_history']) ?>
            </div>
            
            <!-- TEAM IMAGE -->
            <div class="my-5">
                <div class="banner-wrapper" style="height: auto;">
                    <img src="<?= htmlspecialchars($pc['about_img_team']) ?>" alt="Team" style="height: 350px; width: 100%; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Affiliations -->
    <section class="bg_color3 py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" style="color: #2E3191;">Our Affiliations</h2>
            </div>
            <div class="affiliations-slider overflow-hidden">
                <div class="slider-track d-flex flex-nowrap">
                    <?php
                    $affs = [
                        ['src'=>'admin/uploads/itu.png',  'alt'=>'ITU',  'href'=>'https://www.itu.int/'],
                        ['src'=>'admin/uploads/iso.png',  'alt'=>'ISO',  'href'=>'https://www.iso.org/'],
                        ['src'=>'admin/uploads/iec.png',  'alt'=>'IEC',  'href'=>'https://www.iec.ch/'],
                        ['src'=>'admin/uploads/arso.png', 'alt'=>'ARSO', 'href'=>'https://www.arso-org.org/'],
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
                <h2 class="fw-bold" style="color: #2E3191;">ESWASA ACCREDITATION</h2>
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
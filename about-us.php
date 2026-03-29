<?php
// about-us.php — pulls content from page_content table
require_once('includes/db_connect.php');

// ── Load all about_* keys in one query ───────────────────────
$keys = [
    'about_intro', 'about_vision', 'about_mission', 'about_history',
    'about_val_transparency', 'about_val_people', 'about_val_responsiveness',
    'about_val_innovation', 'about_val_professionalism',
    'about_img_vision', 'about_img_mission', 'about_img_team',
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
    'about_intro'              => 'The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023. ESWASA, being the National Standards Body of Eswatini, is mandated by this Act to advance quality and standards in local businesses, government, and industry.',
    'about_vision'             => 'A competitive and Sustainable Trade Environment informed by effective standardization and conformity assurance in Eswatini.',
    'about_mission'            => 'We provide and promote internationally recognized quality standards and conformity assessment services to improve business performance, minimize health and safety risks and ensure environmental integrity in collaboration with regulators.',
    'about_history'            => "The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023.\n\nESWASA, being the National Standards Body of Eswatini, is mandated by this Act to advance quality and standards in local businesses, government, and industry.\n\nThe decision to establish ESWASA was in line with regional and international trends initiated by World Trade Organisation (WTO) efforts aimed at removing tariff and non-tariff barriers to trade and creating a neutral platform that promotes the trade of quality goods and services across nations and economic blocs.\n\nIn addition to opening up global trade opportunities, standardisation also ensures that imported and locally manufactured goods are safe for human and animal life and do not harm the environment.",
    'about_val_transparency'   => 'We conduct our business with honesty, openness, and integrity in all standardization processes.',
    'about_val_people'         => 'We prioritize people—building trust, collaboration, and mutually beneficial relationships with stakeholders.',
    'about_val_responsiveness' => 'We act promptly and effectively to meet the evolving needs of our customers, markets, and partners.',
    'about_val_innovation'     => 'We embrace creative thinking and continuous improvement to enhance our standards and services.',
    'about_val_professionalism'=> 'We uphold the highest standards of competence, reliability, and accountability in all our operations.',
    'about_img_vision'          => 'assets/img/maguga.jpg',
    'about_img_mission'         => 'assets/img/vision.jpg',
    'about_img_team'            => 'assets/img/blog_thumb10.jpg',
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
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }

        .strategy-card, .value-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            border: none; background: #fff;
            display: flex; flex-direction: column; height: 100%;
        }
        .strategy-card:hover, .value-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.12);
        }
        .value-icon {
            width: 70px; height: 70px;
            background: rgba(46,49,145,0.1); color: #2E3191;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            transition: transform 0.3s ease, background 0.3s ease, color 0.3s ease;
            margin: 0 auto 20px auto;
        }
        .value-card:hover .value-icon {
            transform: scale(1.15) rotate(10deg);
            background: #dc3545; color: #fff;
        }
        .value-title  { font-weight: 700; font-size: 1.1rem; color: #333; margin-bottom: 8px; }
        .value-description { color: #666; font-size: 0.9rem; }

        .info-section {
            background: #f9f9f9;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .info-section h3 { color: #333; margin-bottom: 12px; }

        .section-divider {
            width: 100px; height: 4px;
            background: #2E3191;
            margin: 16px auto 0; border-radius: 2px;
        }

        /* Affiliation slider */
        .affiliations-slider { overflow-x: hidden; }
        .slider-track { animation: scroll 22s linear infinite; }
        .slider-track:hover { animation-play-state: paused; }
        .slider-item { flex: 0 0 auto; width: 200px; text-align: center; }
        .affiliation-logo {
            width: 150px; height: 100px; object-fit: contain;
            background: #fff; padding: 10px; border-radius: 6px;
            display: block; margin: 0 auto;
            transition: transform 0.3s ease, filter 0.3s ease;
        }
        .slider-item:hover .affiliation-logo {
            transform: scale(1.1);
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.15));
        }
        @keyframes scroll {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .card, .info-section { border-radius: 12px; }
        @media (max-width: 768px) { .section-divider { width: 70px; } }
    </style>
</head>
<body>
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php"); ?>

    <main class="main-area fix">

        <!-- Breadcrumb -->
        <section class="breadcrumb-area breadcrumb-bg"
                 style="background-image:url('assets/img/bg.png');background-size:cover;background-position:center;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
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

        <!-- Main content -->
        <section class="py-5">
            <div class="container">

                <!-- Intro -->
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold" style="color:#2E3191;">Eswatini Standards Authority (ESWASA)</h2>
                    <div class="section-divider"></div>
                    <p class="mt-4 lead" style="max-width:820px;margin:20px auto 0;">
                        <?= htmlspecialchars($pc['about_intro']) ?>
                    </p>
                </div>

                <!-- Vision & Mission -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="info-section h-100 d-flex flex-column">
                            <h3>Vision</h3>
                            <p class="mb-4"><strong><?= htmlspecialchars($pc['about_vision']) ?></strong></p>
                            <div class="mt-auto">
                                <div style="height:280px;overflow:hidden;border-radius:8px;background:#f0f0f0;">
                                    <img src="<?= htmlspecialchars($pc['about_img_vision']) ?>" alt="Vision" class="img-fluid w-100 h-100" style="object-fit:cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section h-100 d-flex flex-column">
                            <h3>Mission</h3>
                            <p class="mb-4"><strong><?= htmlspecialchars($pc['about_mission']) ?></strong></p>
                            <div class="mt-auto">
                                <div style="height:280px;overflow:hidden;border-radius:8px;background:#f0f0f0;">
                                    <img src="<?= htmlspecialchars($pc['about_img_mission']) ?>" alt="Mission" class="img-fluid w-100 h-100" style="object-fit:cover;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Values -->
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color:#2E3191;">Our Core Values</h3>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <?php
                    $values = [
                        ['key' => 'about_val_transparency',    'icon' => 'fa-eye',        'title' => 'Transparency'],
                        ['key' => 'about_val_people',          'icon' => 'fa-users',      'title' => 'People-Centricity'],
                        ['key' => 'about_val_responsiveness',  'icon' => 'fa-bolt',       'title' => 'Responsiveness'],
                        ['key' => 'about_val_innovation',      'icon' => 'fa-lightbulb',  'title' => 'Innovation'],
                        ['key' => 'about_val_professionalism', 'icon' => 'fa-award',      'title' => 'Professionalism'],
                    ];
                    foreach ($values as $v): ?>
                    <div class="col">
                        <div class="card value-card border-0 shadow-sm rounded-3 p-4 h-100 text-center">
                            <span class="value-icon"><i class="fas <?= $v['icon'] ?> fa-2x"></i></span>
                            <h3 class="value-title"><?= htmlspecialchars($v['title']) ?></h3>
                            <p class="value-description"><?= htmlspecialchars($pc[$v['key']]) ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Brief History -->
                <div class="info-section mb-5">
                    <h3>Brief History</h3>
                    <?= render_paragraphs($pc['about_history']) ?>
                </div>

                <!-- Team image -->
                <div class="my-5">
                    <div class="rounded-3 overflow-hidden shadow" style="max-width:1200px;height:350px;margin:0 auto;">
                        <img src="<?= htmlspecialchars($pc['about_img_team']) ?>" alt="ESWASA Team" class="w-100 h-100" style="object-fit:cover;">
                    </div>
                </div>

            </div>
        </section>

        <!-- Affiliations -->
        <section class="content_section white_section bg_color3 py-5" style="background-color:#e6f0fa;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold" style="color:#2E3191;">
                        Our Affiliations
                        <span class="d-block fs-5 text-muted mt-2">Partnering for Excellence</span>
                    </h2>
                </div>
                <div class="affiliations-slider overflow-hidden">
                    <div class="slider-track d-flex flex-nowrap">
                        <?php
                        $affiliations = [
                            ['src'=>'admin/uploads/itu.png',  'alt'=>'ITU',  'href'=>'https://www.itu.int/'],
                            ['src'=>'admin/uploads/iso.png',  'alt'=>'ISO',  'href'=>'https://www.iso.org/'],
                            ['src'=>'admin/uploads/iec.png',  'alt'=>'IEC',  'href'=>'https://www.iec.ch/'],
                            ['src'=>'admin/uploads/arso.png', 'alt'=>'ARSO', 'href'=>'https://www.arso-org.org/'],
                            ['src'=>'admin/uploads/astm.png', 'alt'=>'ASTM', 'href'=>'https://www.astm.org/'],
                        ];
                        // Duplicate for seamless loop
                        $all = array_merge($affiliations, $affiliations);
                        foreach ($all as $a): ?>
                        <div class="slider-item px-3">
                            <a href="<?= $a['href'] ?>" target="_blank" rel="noopener noreferrer">
                                <img src="<?= $a['src'] ?>" alt="<?= $a['alt'] ?>" class="affiliation-logo">
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
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold" style="color:#2E3191;">ESWASA ACCREDITATION</h2>
                    <p class="text-muted mt-3" style="max-width:700px;margin:0 auto;">
                        Eswatini Standards Authority Management Systems Certification Services is accredited by the Southern African Development Community Accreditation Service (SADCAS).
                    </p>
                    <p class="lead mt-3">
                        <strong>Scopes:</strong> Quality Management Systems to ISO/IEC 17021-1:2015 and ISO/IEC 17021-3:2017 (Certification to ISO 9001:2015), IAF Codes 3, 12, 13 and 38
                    </p>
                </div>
                <div class="affiliations-slider overflow-hidden">
                    <div class="slider-track d-flex flex-nowrap align-items-center">
                        <?php
                        $accreditations = [
                            ['src'=>'assets/img/SADCAS.png', 'alt'=>'SADCAS', 'href'=>'https://www.sadcas.org'],
                            ['src'=>'assets/img/iaf.webp',   'alt'=>'IAF',    'href'=>'https://www.iaf.nu/'],
                            ['src'=>'assets/img/sadc.webp',  'alt'=>'SADC',   'href'=>'https://www.sadc.org'],
                            ['src'=>'assets/img/WTO.png',    'alt'=>'WTO',    'href'=>'https://www.wto.org'],
                        ];
                        $all2 = array_merge($accreditations, $accreditations);
                        foreach ($all2 as $a): ?>
                        <div class="slider-item px-3">
                            <a href="<?= $a['href'] ?>" target="_blank" rel="noopener noreferrer">
                                <div class="d-flex align-items-center justify-content-center" style="width:180px;height:120px;">
                                    <img src="<?= $a['src'] ?>" alt="<?= $a['alt'] ?>" class="img-fluid" style="max-width:100%;max-height:100%;object-fit:contain;">
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include("includes/footer.php"); ?>

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
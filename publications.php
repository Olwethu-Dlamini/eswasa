<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Publications - ESWASA</title>
    <meta name="description" content="Access publications, reports, and documents from the Eswatini Standards Authority (ESWASA).">
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

        /* Brand Colors */
        :root {
            --eswasa-blue: #2B3388;
            --eswasa-dark: rgba(43, 51, 136, 0.85);
            --eswasa-light-gray: rgba(43, 51, 136, 0.04);
        }

        /* Publication Card */
        .publication-card {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .publication-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .publication-title {
            color: #2B3388;
            font-weight: 600;
            font-size: 1.25rem;
            margin: 0 0 10px 0;
        }
        .publication-meta {
            font-size: 0.875rem;
            color: #2B3388;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .publication-meta span:not(:last-child)::after {
            content: " | ";
            margin: 0 8px;
            color: #2B3388;
        }
        .publication-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            background-color: #fff;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
        }
        .type-standard,
        .type-report,
        .type-guidance,
        .type-newsletter,
        .type-annual-report,
        .type-annual_report {
            background-color: #fff;
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
        }
        .publication-description {
            margin-bottom: 15px;
            color: #2B3388;
        }
        .publication-link a {
            color: #2B3388;
            text-decoration: none;
            font-weight: 500;
        }
        .publication-link a:hover {
            color: #2B3388;
            text-decoration: underline;
        }

        /* Info & Links Box */
        .info-box, .related-links-section {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h3, .related-links-section h3 {
            color: #2B3388;
            margin-top: 0;
            font-size: 1.25rem;
        }
        .related-links-section ul {
            list-style: none;
            padding-left: 0;
        }
        .related-links-section ul li {
            margin-bottom: 10px;
        }
        .related-links-section ul li a {
            color: #2B3388;
            text-decoration: none;
        }
        .related-links-section ul li a:hover {
            color: #2B3388;
            text-decoration: underline;
        }

        /* Filter buttons */
        .filter-buttons {
            margin: 20px 0 25px 0;
        }
        .filter-btn {
            margin: 0 8px 10px 0;
            padding: 8px 16px;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
            color: #2B3388;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.95rem;
            font-family: Arial, sans-serif;
        }
        .filter-btn.active, .filter-btn:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }

        @media (max-width: 767.98px) {
            .main-area h2 {
                font-size: 26px;
                line-height: 1.25;
                overflow-wrap: anywhere;
            }
            .publication-card {
                padding: 15px;
            }
            .publication-title {
                font-size: 1.1rem;
            }
            .publication-meta span:not(:last-child)::after {
                content: "";
                margin: 0;
            }
            .publication-meta span {
                display: block;
                margin-bottom: 4px;
            }
            .filter-btn {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
            .info-box, .related-links-section {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'eswasa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Fetch all publications
    $result = $conn->query("SELECT * FROM eswasa_publications ORDER BY published_date DESC");
    ?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('publications', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php">Home</a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span>Publications</span>
                            </nav>
                            <h3 class="title">Publications</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="info-box">
                    <h3>About ESWASA Publications</h3>
                    <p>This section provides access to various publications produced by the Eswatini Standards Authority (ESWASA). These include official standards documents, annual reports, technical guidelines, newsletters, and other relevant reports.</p>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All Types</button>
                    <button class="filter-btn" data-filter="annual_report">Annual Reports</button>
                    <button class="filter-btn" data-filter="newsletter">Newsletters</button>
                    <button class="filter-btn" data-filter="guidance">Guidance</button>
                    <button class="filter-btn" data-filter="report">Reports</button>
                    <button class="filter-btn" data-filter="standard">Standards</button>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h2>Available Publications</h2>
                        <div class="section-divider mb-4" style="margin-left: 0; margin-right: 0;"></div>
                        
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($pub = $result->fetch_assoc()): ?>
                                <?php
                                // Determine type class and label
                                $type = $pub['pub_type'];
                                $typeClass = 'type-' . $type;
                                $typeName = match($type) {
                                    'standard' => 'Standard',
                                    'report' => 'Report',
                                    'guidance' => 'Guidance Document',
                                    'newsletter' => 'Newsletter',
                                    'annual_report' => 'Annual Report',
                                    default => ucfirst($type)
                                };

                                // Build file path
                                $filePath = 'admin/uploads/' . $pub['file_path'];
                                
                                // Get file size in MB (if file exists)
                                $fullPath = __DIR__ . '/admin/uploads/' . $pub['file_path'];
                                $fileSize = file_exists($fullPath) 
                                    ? round(filesize($fullPath) / (1024 * 1024), 1) 
                                    : 'N/A';
                                ?>
                                <div class="publication-card" data-type="<?= htmlspecialchars($type) ?>">
                                    <h4 class="publication-title"><?= htmlspecialchars($pub['title']) ?></h4>
                                    <div class="publication-meta">
                                        <span>Published: <?= date('Y-m-d', strtotime($pub['published_date'])) ?></span>
                                        <span>Format: PDF (<?= $fileSize ?> MB)</span>
                                        <span class="publication-type <?= htmlspecialchars($typeClass) ?>"><?= htmlspecialchars($typeName) ?></span>
                                    </div>
                                    <div class="publication-description">
                                        <p><?= nl2br(htmlspecialchars($pub['description'])) ?></p>
                                    </div>
                                    <div class="publication-link">
                                        <a href="<?= htmlspecialchars($filePath) ?>" target="_blank">Download Publication</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p>No publications are currently available.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="related-links-section">
                    <h3>Related Links</h3>
                    <ul>
                        <li><a href="standards.php">View Eswatini National Standards (SZNS)</a></li>
                        <li><a href="workprogrammes.php">ESWASA Work Programmes</a></li>
                        <li><a href="tcplatform.php">Technical Committee Information</a></li>
                        <li><a href="https://www.iso.org/" target="_blank">International Organization for Standardization (ISO)</a></li>
                        <li><a href="https://www.iec.ch/" target="_blank">International Electrotechnical Commission (IEC)</a></li>
                        <li><a href="https://www.itu.int/" target="_blank">International Telecommunication Union (ITU)</a></li>
                    </ul>
                </div>

            </div>
        </section>

    </main>

    <?php include("includes/footer.php")?>

    <!-- Filtering Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const cards = document.querySelectorAll('.publication-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const filterType = this.getAttribute('data-filter');

                cards.forEach(card => {
                    if (filterType === 'all' || card.getAttribute('data-type') === filterType) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
    </script>

    <!-- Scripts -->
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
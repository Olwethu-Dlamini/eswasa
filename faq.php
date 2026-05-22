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
    <title>Frequently Asked Questions - ESWASA</title>
    <meta name="description" content="Frequently Asked Questions about ESWASA training, standards, certification and general information.">

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
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        .faq-category {
            background: rgba(43, 51, 136, 0.04);
            padding: 40px 0;
        }
        .category-title {
            color: #2B3388;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        .faq-section {
            padding: 60px 0;
        }
        .accordion-button {
            font-weight: 600;
            color: #2B3388;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            margin-bottom: 10px;
            border-radius: 4px !important;
        }
        .accordion-button:not(.collapsed) {
            background: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }
        .accordion-button:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
        }
        .accordion-body {
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-top: none;
            border-radius: 0 0 4px 4px;
            color: #2B3388;
        }
        .contact-info-box {
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 30px;
            border-radius: 4px;
            margin-top: 40px;
            text-align: center;
            color: #2B3388;
        }
        .contact-info-box h4 { color: #2B3388; font-weight: 700; }

        @media (max-width: 767.98px) {
            .category-title { font-size: 1.4rem; margin-bottom: 20px; }
            .faq-section { padding: 36px 0; }
            .accordion-button { font-size: 0.95rem; padding: 12px 14px; }
            .accordion-body { padding: 14px; font-size: 0.95rem; }
            .contact-info-box { padding: 20px; }
            .breadcrumb-content .title { font-size: 1.5rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('faq', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">FAQ</span>
                            </nav>
                            <h3 class="title">Frequently Asked Questions</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <?php
        // Fetch FAQs by category
        $faqs = [
            'training' => [],
            'standards' => [],
            'general' => []
        ];
        $result = $conn->query("SELECT * FROM eswasa_faq ORDER BY sort_order ASC");
        while ($row = $result->fetch_assoc()) {
            $faqs[$row['category']][] = $row;
        }
        ?>

        <!-- Training FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12">
                        <h2 class="category-title">Training & Certification FAQs</h2>
                        <div class="faq-wrap">
                            <div class="accordion" id="trainingAccordion">
                                <?php if (!empty($faqs['training'])): ?>
                                    <?php foreach ($faqs['training'] as $index => $faq): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseTraining<?= $faq['id'] ?>"
                                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                                        aria-controls="collapseTraining<?= $faq['id'] ?>">
                                                    <?= htmlspecialchars($faq['question']) ?>
                                                </button>
                                            </h2>
                                            <div id="collapseTraining<?= $faq['id'] ?>" 
                                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                                 data-bs-parent="#trainingAccordion">
                                                <div class="accordion-body">
                                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center py-3">No FAQs available in this category.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Standards & Certification FAQ Section -->
        <section class="faq-section bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12">
                        <h2 class="category-title">Standards & Certification FAQs</h2>
                        <div class="faq-wrap">
                            <div class="accordion" id="standardsAccordion">
                                <?php if (!empty($faqs['standards'])): ?>
                                    <?php foreach ($faqs['standards'] as $index => $faq): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseStandards<?= $faq['id'] ?>"
                                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                                        aria-controls="collapseStandards<?= $faq['id'] ?>">
                                                    <?= htmlspecialchars($faq['question']) ?>
                                                </button>
                                            </h2>
                                            <div id="collapseStandards<?= $faq['id'] ?>" 
                                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                                 data-bs-parent="#standardsAccordion">
                                                <div class="accordion-body">
                                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center py-3">No FAQs available in this category.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- General Information FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12">
                        <h2 class="category-title">General Information</h2>
                        <div class="faq-wrap">
                            <div class="accordion" id="generalAccordion">
                                <?php if (!empty($faqs['general'])): ?>
                                    <?php foreach ($faqs['general'] as $index => $faq): ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" 
                                                        type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#collapseGeneral<?= $faq['id'] ?>"
                                                        aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                                        aria-controls="collapseGeneral<?= $faq['id'] ?>">
                                                    <?= htmlspecialchars($faq['question']) ?>
                                                </button>
                                            </h2>
                                            <div id="collapseGeneral<?= $faq['id'] ?>" 
                                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                                 data-bs-parent="#generalAccordion">
                                                <div class="accordion-body">
                                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center py-3">No FAQs available in this category.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Contact Information Box -->
                        <div class="contact-info-box">
                            <h4>Still Have Questions?</h4>
                            <p>If you couldn't find the answer to your question, please don't hesitate to contact us directly. Our team is ready to assist you with any inquiries regarding our services, training programs, or certification processes.</p>
                            <p><strong>Contact Us:</strong><br>
                            Tel: +268 2518 4610 | Email: info@eswasa.co.sz</p>
                        </div>

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
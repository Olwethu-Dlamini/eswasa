<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_faq.php';

$pc = pc_get_many($conn, $faq_keys, $faq_defaults);

// Group FAQs by category enum
$faqs = ['training' => [], 'standards' => [], 'general' => []];
if ($res = $conn->query('SELECT * FROM eswasa_faq ORDER BY sort_order ASC, id ASC')) {
    while ($row = $res->fetch_assoc()) {
        if (isset($faqs[$row['category']])) {
            $faqs[$row['category']][] = $row;
        }
    }
}

$categories = [
    ['key' => 'training',  'slug' => 'training',  'title' => $pc['faq_category_training_title'],  'accordion_id' => 'faqTraining',  'item_prefix' => 'collapseTraining'],
    ['key' => 'standards', 'slug' => 'standards', 'title' => $pc['faq_category_standards_title'], 'accordion_id' => 'faqStandards', 'item_prefix' => 'collapseStandards'],
    ['key' => 'general',   'slug' => 'general',   'title' => $pc['faq_category_general_title'],   'accordion_id' => 'faqGeneral',   'item_prefix' => 'collapseGeneral'],
];
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['faq_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Frequently asked questions about ESWASA training, standards, certification, and general information.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        /* ESWASA theme base — locked spec (#2B3388, #fff, Arial 15px) */
        body { font-family: Arial, sans-serif; font-size: 15px; color: #2B3388; }
        body h1, body h2, body h3, body h4, body h5, body h6 { font-family: Arial, sans-serif; color: #2B3388; }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td { font-family: Arial, sans-serif; }
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Intro info-box (canonical centered title + 60px divider) */
        .info-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 25px;
            margin-bottom: 40px;
        }
        .info-box h3 { color: #2B3388; font-weight: 700; margin: 0; }
        .info-box.is-intro { text-align: center; }
        .info-box.is-intro .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 24px;
            border-radius: 0;
        }
        .info-box.is-intro p { text-align: left; margin-bottom: 12px; }
        .info-box.is-intro p:last-child { margin-bottom: 0; }

        /* Category sections */
        .faq-section { padding: 50px 0; }
        .faq-section + .faq-section { padding-top: 10px; }
        .faq-category-title {
            color: #2B3388;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
        }
        .faq-category-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 14px auto 30px;
            border-radius: 0;
        }

        /* Accordion — restrained, theme-aligned */
        .accordion-item {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px !important;
            margin-bottom: 10px;
            background: #fff;
            overflow: hidden;
        }
        .accordion-item + .accordion-item { margin-top: 0; }
        .accordion-button {
            font-size: 15px;
            font-weight: 600;
            color: #2B3388;
            background: #fff;
            border: none;
            box-shadow: none;
            padding: 14px 18px;
            line-height: 1.45;
        }
        .accordion-button:not(.collapsed) {
            background-color: #2B3388 !important;
            color: #fff !important;
            box-shadow: none;
        }
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") !important;
        }
        .accordion-button:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
        }
        .accordion-body {
            background: #fff;
            color: #2B3388 !important;
            padding: 16px 18px;
            line-height: 1.7;
            font-size: 15px;
            border-top: 1px solid rgba(43, 51, 136, 0.10);
        }
        .accordion-body, .accordion-body p, .accordion-body li, .accordion-body span, .accordion-body strong {
            color: #2B3388 !important;
        }
        .faq-empty {
            padding: 28px 20px;
            text-align: center;
            color: rgba(43, 51, 136, 0.75);
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }

        /* Contact box at the bottom */
        .faq-contact {
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 30px;
            text-align: center;
            margin-top: 40px;
        }
        .faq-contact h4 { color: #2B3388; font-weight: 700; margin-bottom: 12px; }
        .faq-contact p { color: #2B3388; margin-bottom: 10px; }
        .faq-contact a { color: #2B3388; font-weight: 600; text-decoration: none; }
        .faq-contact a:hover { text-decoration: underline; }
        .faq-contact .details { margin-top: 16px; display: inline-flex; flex-wrap: wrap; gap: 8px 22px; justify-content: center; }
        .faq-contact .details span i { margin-right: 6px; }

        @media (max-width: 767.98px) {
            .faq-section { padding: 30px 0; }
            .faq-category-title { font-size: 1.25rem; }
            .accordion-button { font-size: 0.95rem; padding: 12px 14px; }
            .accordion-body { padding: 14px; font-size: 0.95rem; }
            .faq-contact { padding: 22px; }
            .breadcrumb-content .title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('faq', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['faq_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['faq_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['faq_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12">

                        <div class="info-box is-intro">
                            <h3><?= pc_h($pc['faq_intro_title']) ?></h3>
                            <div class="section-divider"></div>
                            <?= pc_paragraphs_html($pc['faq_intro_body']) ?>
                        </div>

                        <?php foreach ($categories as $cat): ?>
                            <section class="faq-section">
                                <h2 class="faq-category-title"><?= pc_h($cat['title']) ?></h2>
                                <div class="faq-category-divider"></div>

                                <?php if (!empty($faqs[$cat['key']])): ?>
                                    <div class="accordion" id="<?= htmlspecialchars($cat['accordion_id']) ?>">
                                        <?php foreach ($faqs[$cat['key']] as $idx => $faq): ?>
                                            <?php
                                            $itemId = $cat['item_prefix'] . (int)$faq['id'];
                                            $isOpen = $idx === 0;
                                            ?>
                                            <div class="accordion-item">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button <?= $isOpen ? '' : 'collapsed' ?>"
                                                            type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#<?= $itemId ?>"
                                                            aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                                                            aria-controls="<?= $itemId ?>">
                                                        <?= htmlspecialchars($faq['question']) ?>
                                                    </button>
                                                </h3>
                                                <div id="<?= $itemId ?>"
                                                     class="accordion-collapse collapse <?= $isOpen ? 'show' : '' ?>"
                                                     data-bs-parent="#<?= htmlspecialchars($cat['accordion_id']) ?>">
                                                    <div class="accordion-body">
                                                        <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="faq-empty"><?= pc_h($pc['faq_category_empty_state']) ?></div>
                                <?php endif; ?>
                            </section>
                        <?php endforeach; ?>

                        <div class="faq-contact">
                            <h4><?= pc_h($pc['faq_contact_title']) ?></h4>
                            <?= pc_paragraphs_html($pc['faq_contact_body']) ?>
                            <div class="details">
                                <?php if (!empty($pc['faq_contact_phone'])): ?>
                                    <span><i class="fas fa-phone-alt"></i><?= pc_h($pc['faq_contact_phone']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($pc['faq_contact_email'])): ?>
                                    <span><i class="fas fa-envelope"></i><a href="mailto:<?= pc_h($pc['faq_contact_email']) ?>"><?= pc_h($pc['faq_contact_email']) ?></a></span>
                                <?php endif; ?>
                            </div>
                        </div>

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

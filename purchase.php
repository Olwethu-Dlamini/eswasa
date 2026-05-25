<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once 'includes/breadcrumb_helper.php';

$purchase_keys = [
    // Breadcrumb
    'purchase_breadcrumb_home_label',
    'purchase_breadcrumb_parent_label',
    'purchase_breadcrumb_current_label',
    'purchase_breadcrumb_title',
    // Intro box
    'purchase_intro_title',
    'purchase_intro_body',
    // Catalogue action card
    'purchase_catalogue_title',
    'purchase_catalogue_body',
    'purchase_catalogue_link_text',
    'purchase_catalogue_link_url',
    // Webstore action card
    'purchase_webstore_title',
    'purchase_webstore_body',
    'purchase_webstore_link_text',
    'purchase_webstore_link_url',
    // Assistance card
    'purchase_assist_title',
    'purchase_assist_body',
    'purchase_contact_phone',
    'purchase_contact_fax',
    'purchase_contact_email_general',
    'purchase_contact_email_sales',
    // CTA buttons
    'purchase_cta_primary_text',
    'purchase_cta_primary_url',
    'purchase_cta_secondary_text',
    'purchase_cta_secondary_url',
];

$purchase_defaults = [
    'purchase_breadcrumb_home_label'   => 'Home',
    'purchase_breadcrumb_parent_label' => 'Standards',
    'purchase_breadcrumb_current_label'=> 'Purchase Standards',
    'purchase_breadcrumb_title'        => 'Purchase Standards',

    'purchase_intro_title' => 'Standard Sales',
    'purchase_intro_body'  => "SZNS Standard Sales through the Authority's office assistance or conveniently online.\n\nESWASA sells Eswatini National Standards (SZNS) as well as related documents and specifications. Our services extend to sourcing other international and/or foreign standards for you, such as SANS, ARSO, ISO and IEC standards.",

    'purchase_catalogue_title'     => 'Standards Catalogue',
    'purchase_catalogue_body'      => 'Browse our complete list of published national and adopted international standards to identify the specific documents you require.',
    'purchase_catalogue_link_text' => 'Download Latest Standards Catalogue (PDF)',
    'purchase_catalogue_link_url'  => 'admin/uploads/eswasa_standards_catalogue_latest.pdf',

    'purchase_webstore_title'     => 'Online Webstore',
    'purchase_webstore_body'      => 'Purchase standards conveniently online through our webstore.',
    'purchase_webstore_link_text' => 'Webstore - https://estore.eswasa.co.sz/',
    'purchase_webstore_link_url'  => 'https://estore.eswasa.co.sz/',

    'purchase_assist_title'         => 'Need Assistance with Purchasing?',
    'purchase_assist_body'          => 'Until our online platform is live, or if you require guidance on specific standards or bulk orders, please contact our Sales Department directly using the verified contact details below:',
    'purchase_contact_phone'        => '+268 2518 4610',
    'purchase_contact_fax'          => '+268 2518 4526',
    'purchase_contact_email_general'=> 'info@eswasa.co.sz',
    'purchase_contact_email_sales'  => 'sales@eswasa.co.sz',

    'purchase_cta_primary_text'   => 'Contact Sales Team',
    'purchase_cta_primary_url'    => 'contact.php',
    'purchase_cta_secondary_text' => 'View All Standards Areas',
    'purchase_cta_secondary_url'  => 'Standards.php',
];

$pc = pc_get_many($conn, $purchase_keys, $purchase_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Purchase Standards - ESWASA</title>
    <meta name="description" content="Purchase Eswatini National Standards (SZNS) and other relevant standards from ESWASA.">
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
    <link rel="stylesheet" href="includes/cta-section.css">
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

        /* Button Style */
        .btn-purchase {
            background-color: #2B3388; /* ESWASA Primary Blue */
            color: #fff;
            border-color: #2B3388;
            margin: 5px;
            padding: 10px 30px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .btn-purchase:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Introduction Box (SABS Style - Clean, No Blue Lining) */
        .intro-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 30px;
            margin: 25px 0 50px 0;
            border-radius: 4px;
        }
        .intro-box { text-align: center; }
        .intro-box h3 {
            color: #2B3388;
            margin-top: 0;
            margin-bottom: 0;
            font-weight: 700;
        }
        .intro-box .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 24px;
            border-radius: 0;
        }
        .intro-box p { text-align: left; }

        /* Action Card Style (For Catalogue and Store Link) */
        .action-card {
            background-color: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            padding: 30px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.04);
            min-height: 220px; /* Ensure cards are visually balanced */
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .action-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .action-card h4 {
            color: #2B3388;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        /* Catalogue Link Styling */
        .catalogue-link {
            display: inline-block;
            margin-top: 15px;
            font-weight: 600;
            color: #2B3388;
            text-decoration: none;
            font-size: 1.1em;
            transition: color 0.2s;
        }
        .catalogue-link:hover {
            text-decoration: underline;
            color: #2B3388;
        }
        .catalogue-link i {
            margin-right: 8px;
            font-size: 1.2em;
        }

        /* Coming Soon Style */
        .coming-soon-badge {
            display: inline-block;
            padding: 8px 15px;
            font-weight: 700;
            color: #2B3388;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            margin-top: 15px;
        }

        /* Contact List Icons */
        .contact-list li {
            margin-bottom: 8px;
        }
        .contact-list i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }

        /* Form inputs (theme-compliant defaults) */
        input, select, textarea {
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 4px;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
            outline: none;
        }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            .intro-box {
                padding: 20px 15px;
                margin: 20px 0 30px 0;
            }
            .intro-box h3 {
                font-size: 1.1rem;
            }
            .action-card {
                padding: 20px 15px;
                min-height: auto;
            }
            .action-card h4 {
                font-size: 1.1em;
            }
            .catalogue-link {
                font-size: 0.95em;
                word-break: break-word;
            }
            .contact-list li {
                font-size: 0.95em;
            }
        }

        /* Subtitle / lead — align with site spec (16px) */
        body .lead { font-size: 16px; line-height: 1.7; font-weight: 400; }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>
    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('purchase', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html"><?= pc_h($pc['purchase_breadcrumb_home_label']) ?></a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['purchase_breadcrumb_parent_label']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['purchase_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['purchase_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <div class="container">
                <div class="intro-box">
                    <h3><?= pc_h($pc['purchase_intro_title']) ?></h3>
                    <div class="section-divider"></div>
                    <?= pc_paragraphs_html($pc['purchase_intro_body']) ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="action-card">
                            <h4><i class="fas fa-search me-2"></i><?= pc_h($pc['purchase_catalogue_title']) ?></h4>
                            <p><?= pc_h($pc['purchase_catalogue_body']) ?></p>
                            <a href="<?= pc_h($pc['purchase_catalogue_link_url']) ?>" class="catalogue-link" target="_blank">
                                <i class="fas fa-file-pdf"></i> <?= pc_h($pc['purchase_catalogue_link_text']) ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <h4><i class="fas fa-shopping-cart me-2"></i><?= pc_h($pc['purchase_webstore_title']) ?></h4>
                            <p><?= pc_h($pc['purchase_webstore_body']) ?></p>
                            <a href="<?= pc_h($pc['purchase_webstore_link_url']) ?>" class="catalogue-link" target="_blank">
                                <i class="fas fa-external-link-alt"></i> <?= pc_h($pc['purchase_webstore_link_text']) ?>
                            </a>
                        </div>
                    </div>
                </div>

               <div class="card" style="border: 1px solid rgba(43, 51, 136, 0.15); box-shadow: 0 4px 12px rgba(43, 51, 136, 0.06); border-radius: 4px; padding: 25px;">
                    <h3 style="color: #2B3388; font-weight: 700; margin: 0; text-align: center;"><?= pc_h($pc['purchase_assist_title']) ?></h3>
                    <div class="section-divider" style="width:60px; height:2px; background:#2B3388; margin:16px auto 24px; border-radius:0;"></div>

                    <p class="lead">
                        <?= pc_h($pc['purchase_assist_body']) ?>
                    </p>

                    <ul class="list-unstyled mb-4 contact-list">
                        <li><i class="fas fa-phone-alt" style="color:#2B3388;"></i> Telephone: <?= pc_h($pc['purchase_contact_phone']) ?></li>
                        <li><i class="fas fa-fax" style="color:#2B3388;"></i> Fax: <?= pc_h($pc['purchase_contact_fax']) ?></li>
                        <li><i class="fas fa-envelope" style="color:#2B3388;"></i> Email (General):
                            <a href="mailto:<?= pc_h($pc['purchase_contact_email_general']) ?>" style="color:#2B3388;"><?= pc_h($pc['purchase_contact_email_general']) ?></a>
                        </li>
                        <li><i class="fas fa-envelope" style="color:#2B3388;"></i> Email (Sales):
                            <a href="mailto:<?= pc_h($pc['purchase_contact_email_sales']) ?>" style="color:#2B3388;"><?= pc_h($pc['purchase_contact_email_sales']) ?></a>
                        </li>
                    </ul>
                </div>



                <div class="text-center my-5 pt-3">
                    <a href="<?= pc_h($pc['purchase_cta_primary_url']) ?>" class="btn-cta"><?= pc_h($pc['purchase_cta_primary_text']) ?></a>
                    <a href="<?= pc_h($pc['purchase_cta_secondary_url']) ?>" class="btn-cta"><?= pc_h($pc['purchase_cta_secondary_text']) ?></a>
                </div>

            </div>
        </section>

    </main>
    <?php include("includes/footer.php")?>
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

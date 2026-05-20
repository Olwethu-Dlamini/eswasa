<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
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
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
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
            text-transform: uppercase;
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
        .intro-box h3 {
            color: #2B3388;
            margin-top: 0;
            margin-bottom: 15px;
            font-weight: 700;
            border-bottom: 2px solid rgba(43, 51, 136, 0.15);
            padding-bottom: 10px;
        }

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
            color: rgba(43, 51, 136, 0.85);
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
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Purchase Standards</span>
                            </nav>
                            <h3 class="title">Purchase Standards</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-5">
            <div class="container">
                <div class="intro-box">
                    <h3>Standard Sales</h3>
                    <p>SZNS Standard Sales through the Authority's office assistance or conveniently Online.</p>
                    <p>ESWASA sells Eswatini National Standards (SZNS) as well as related documents and specifications. Our services extend to sourcing other international and/or foreign standards for you, such as SANS, ARSO, ISO and IEC standards.</p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="action-card">
                            <h4><i class="fas fa-search me-2"></i>Standards Catalogue</h4>
                            <p>Browse our complete list of published national and adopted international standards to identify the specific documents you require.</p>
                            <a href="admin/uploads/eswasa_standards_catalogue_latest.pdf" class="catalogue-link" target="_blank"> 
                                <i class="fas fa-file-pdf"></i> Download Latest Standards Catalogue (PDF)
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="action-card">
                            <h4><i class="fas fa-shopping-cart me-2"></i>Online Webstore</h4>
                            <p>Purchase standards conveniently online through our webstore.</p>
                            <a href="https://estore.swasa.co.sz/" class="catalogue-link" target="_blank">
                                <i class="fas fa-external-link-alt"></i> WEBSTORE - https://estore.swasa.co.sz/
                            </a>
                        </div>
                    </div>
                </div>

               <div class="card" style="border: 1px solid rgba(43, 51, 136, 0.15); box-shadow: 0 4px 12px rgba(43, 51, 136, 0.06); border-radius: 4px; padding: 25px;">
                    <h3 style="color: #2B3388; font-weight: 700; margin-bottom: 20px;">Need Assistance with Purchasing?</h3>

                    <p class="lead">
                        Until our online platform is live, or if you require guidance on specific standards or bulk orders,
                        please contact our Sales Department directly using the verified contact details below:
                    </p>

                    <ul class="list-unstyled mb-4 contact-list">
                        <li><i class="fas fa-phone-alt" style="color:#2B3388;"></i> Telephone: +268 2518 4610</li>
                        <li><i class="fas fa-fax" style="color:#2B3388;"></i> Fax: +268 2518 4526</li>
                        <li><i class="fas fa-envelope" style="color:#2B3388;"></i> Email (General):
                            <a href="mailto:info@eswasa.co.sz" style="color:#2B3388;">info@eswasa.co.sz</a>
                        </li>
                        <li><i class="fas fa-envelope" style="color:#2B3388;"></i> Email (Sales):
                            <a href="mailto:sales@eswasa.co.sz" style="color:#2B3388;">sales@eswasa.co.sz</a>
                        </li>
                    </ul>
                </div>



                <div class="text-center my-5 pt-3">
                    <a href="contact.php" class="btn-cta">Contact Sales Team</a>
                    <a href="Standards.php" class="btn-cta">View All Standards Areas</a>
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
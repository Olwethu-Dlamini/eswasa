<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';

$policies = [
    [
        'title' => 'Impartiality Policy',
        'desc'  => 'Our public commitment to impartial, independent certification decisions and the safeguards we apply.',
        'file'  => 'Impartiality Policy - SWASA Certification.pdf',
        'icon'  => 'fa-balance-scale',
        'category' => 'Certification',
    ],
    [
        'title' => 'Complaints Handling Procedure',
        'desc'  => 'How we receive, investigate and resolve complaints — including timelines and the escalation path.',
        'file'  => 'CER_PR_006 PROCEDURE FOR COMPLAINTS HANDLING.pdf',
        'icon'  => 'fa-comment-alt',
        'category' => 'Customer Care',
    ],
    [
        'title' => 'Appeals Handling Procedure',
        'desc'  => 'The formal route to challenge a certification decision, including timeframes and the appeals committee.',
        'file'  => 'CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf',
        'icon'  => 'fa-gavel',
        'category' => 'Certification',
    ],
    [
        'title' => 'Rules for Use of the Certification Mark',
        'desc'  => 'Conditions, restrictions and obligations governing how certified clients may display the ESWASA mark.',
        'file'  => 'CER_RU_028 RULES FOR THE USE OF THE CERTIFICATION MARK.pdf',
        'icon'  => 'fa-certificate',
        'category' => 'Certification',
    ],
    [
        'title' => 'Handling Requests for Information',
        'desc'  => 'How we manage public information requests, including what is publicly available and what is confidential.',
        'file'  => 'CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf',
        'icon'  => 'fa-info-circle',
        'category' => 'Information',
    ],
    [
        'title' => 'Grant of Certification Procedure',
        'desc'  => 'The end-to-end process for granting initial certification, from application through audit to award.',
        'file'  => 'CER_PR_014 GRANT OF CERTIFICATION PROCEDURE.pdf',
        'icon'  => 'fa-clipboard-check',
        'category' => 'Certification',
    ],
    [
        'title' => 'Suspension, Withdrawal &amp; Reduction of Scope',
        'desc'  => 'When and how a certification may be suspended, withdrawn, or reduced in scope, and the client\'s rights.',
        'file'  => 'CER_PR_026 PROCEDURE FOR SUSPENSION WITHDRAWAL REDUCED SCOPE OF CERTIFICATION.pdf',
        'icon'  => 'fa-ban',
        'category' => 'Certification',
    ],
    [
        'title' => 'Extending Scope of Certification',
        'desc'  => 'How an existing certified client can apply to extend the scope of an issued certificate.',
        'file'  => 'CER_PR_012 EXTENDING SCOPE OF CERTIFICATION PROCEDURE.pdf',
        'icon'  => 'fa-expand-arrows-alt',
        'category' => 'Certification',
    ],
    [
        'title' => 'Management Systems Certification Audits',
        'desc'  => 'How audits are planned, conducted and reported under the ESWASA Management Systems Certification scheme.',
        'file'  => 'CER_PR_020 PROCEDURE FOR MANAGEMENT SYSTEMS CERTIFICATION AUDITS.pdf',
        'icon'  => 'fa-tasks',
        'category' => 'Certification',
    ],
    [
        'title' => 'Special Audits Procedure',
        'desc'  => 'When special audits are triggered, what they involve and what clients can expect.',
        'file'  => 'CER_PR_028 SPECIAL AUDITS PROCEDURE.pdf',
        'icon'  => 'fa-search',
        'category' => 'Certification',
    ],
    [
        'title' => 'Privacy Policy',
        'desc'  => 'How we collect, use and protect personal information submitted through our website and services.',
        'file'  => 'privacy.php',
        'icon'  => 'fa-user-shield',
        'category' => 'Information',
        'internal' => true,
    ],
    [
        'title' => 'Terms &amp; Conditions',
        'desc'  => 'The terms governing your use of the ESWASA website and online services.',
        'file'  => 'terms.php',
        'icon'  => 'fa-file-signature',
        'category' => 'Information',
        'internal' => true,
    ],
];
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Policies - ESWASA</title>
    <meta name="description" content="Public policies and procedures of the Eswatini Standards Authority — impartiality, complaints, appeals, certification rules, privacy and more.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
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

        .display-6 { color: #2B3388; font-weight: 700; letter-spacing: -0.01em; }
        .section-divider {
            width: 60px; height: 2px; background: #2B3388;
            margin: 16px auto 0; border-radius: 0;
        }
        .intro-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            padding: 22px 26px;
            max-width: 920px;
            margin: 0 auto 36px;
        }
        .intro-card p {
            margin: 0; color: rgba(43, 51, 136, 0.85);
            font-size: 1rem; line-height: 1.65;
        }

        .policies-section { padding: 60px 0 80px; }
        .policy-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }
        .policy-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 22px 24px;
            display: flex;
            gap: 18px;
            text-decoration: none !important;
            color: inherit;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .policy-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
            transform: translateY(-2px);
        }
        .policy-icon {
            flex-shrink: 0;
            width: 52px; height: 52px;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .policy-body { flex: 1; min-width: 0; }
        .policy-category {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(43, 51, 136, 0.65);
            margin-bottom: 6px;
        }
        .policy-title {
            color: #2B3388;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0 0 8px;
            line-height: 1.3;
        }
        .policy-desc {
            color: rgba(43, 51, 136, 0.78);
            font-size: 0.9rem;
            line-height: 1.55;
            margin: 0 0 10px;
        }
        .policy-cta {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .policy-cta i { font-size: 11px; transition: transform .2s ease; }
        .policy-card:hover .policy-cta i { transform: translateX(3px); }

        @media (max-width: 767.98px) {
            .policies-section { padding: 40px 0 50px; }
            .policy-grid { grid-template-columns: 1fr; }
            .policy-card { padding: 18px 18px; gap: 14px; }
            .policy-icon { width: 44px; height: 44px; font-size: 18px; }
            .breadcrumb-content .title { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="spinner">
            <div class="sk-dot1"></div><div class="sk-dot2"></div>
            <div class="rect3"></div><div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('policies', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Customer Care</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Policies</span>
                            </nav>
                            <h3 class="title">Policies &amp; Procedures</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="policies-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">Our Public Policies</h2>
                    <div class="section-divider"></div>
                </div>

                <div class="intro-card">
                    <p>These are the public policies and procedures that govern how ESWASA operates — covering impartiality, complaints, appeals, certification rules and information handling. Each document is downloadable. For clarification on any policy, contact us through the <a href="customer-feedback.php">Customer Feedback form</a>.</p>
                </div>

                <div class="policy-grid">
                    <?php foreach ($policies as $p): ?>
                        <a class="policy-card" href="<?= htmlspecialchars($p['file']) ?>" <?= empty($p['internal']) ? 'target="_blank" rel="noopener"' : '' ?>>
                            <div class="policy-icon"><i class="fas <?= htmlspecialchars($p['icon']) ?>"></i></div>
                            <div class="policy-body">
                                <span class="policy-category"><?= htmlspecialchars($p['category']) ?></span>
                                <h4 class="policy-title"><?= $p['title'] ?></h4>
                                <p class="policy-desc"><?= $p['desc'] ?></p>
                                <span class="policy-cta">
                                    <?= empty($p['internal']) ? 'Open PDF' : 'Open page' ?>
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
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

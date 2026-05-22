<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';

/*
 * Public Certification Status Register
 * Published in accordance with CER_PR_026 §6.1.3 and §6.2.2, which require
 * suspension and withdrawal information to be made publicly accessible
 * through the ESWASA website.
 *
 * Until a CMS table is wired, edit the arrays below to publish status changes.
 */

$suspended = [
    // Example row — remove or replace when there is real data:
    // ['client' => 'Example Co.', 'cert_no' => 'MS-2024-001', 'scope' => 'SZNS ISO 9001:2015 — Manufacture of plastic containers', 'effective' => '2026-02-14', 'reason' => 'Missed surveillance audit'],
];

$withdrawn = [
    // ['client' => '...', 'cert_no' => '...', 'scope' => '...', 'effective' => '...'],
];

$reduced = [
    // ['client' => '...', 'cert_no' => '...', 'scope' => '...', 'effective' => '...', 'note' => 'Scope reduced — see notes'],
];
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Certification Status Register - ESWASA</title>
    <meta name="description" content="Public register of suspended, withdrawn and reduced-scope certifications issued under the ESWASA Management Systems Certification Scheme.">
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
            padding: 26px 28px;
            max-width: 920px;
            margin: 0 auto 40px;
        }
        .intro-card p {
            margin: 0; color: rgba(43, 51, 136, 0.85);
            font-size: 15px; line-height: 1.7;
        }

        .status-block { margin-bottom: 42px; }
        .status-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #2B3388;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 0 0 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #2B3388;
        }
        .status-title .count {
            display: inline-block;
            background: #2B3388;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 12px;
            letter-spacing: 0.4px;
        }

        .status-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            overflow: hidden;
        }
        .status-table th,
        .status-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(43, 51, 136, 0.10);
            font-size: 0.95rem;
            vertical-align: top;
        }
        .status-table th {
            background-color: #2B3388;
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-table tr:last-child td { border-bottom: none; }
        .status-table tr:nth-child(even) td {
            background-color: rgba(43, 51, 136, 0.03);
        }
        .status-table .cert-no { font-weight: 600; white-space: nowrap; }
        .status-table .date    { white-space: nowrap; color: rgba(43, 51, 136, 0.8); }

        .empty-state {
            background: #fff;
            border: 1px dashed rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            padding: 22px 24px;
            color: rgba(43, 51, 136, 0.75);
            font-style: italic;
        }

        .footer-note {
            margin-top: 50px;
            padding: 22px 24px;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            font-size: 0.95rem;
            color: rgba(43, 51, 136, 0.85);
            line-height: 1.7;
        }
        .footer-note strong { color: #2B3388; }
        .footer-note a {
            color: #2B3388;
            text-decoration: underline;
            font-weight: 600;
        }
        .footer-note a:hover { color: rgba(43, 51, 136, 0.75); }

        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .display-6 { font-size: 1.55rem !important; }
            .intro-card { padding: 20px 18px; }
            .status-title { font-size: 1.1rem; flex-wrap: wrap; }
            .status-table { display: block; overflow-x: auto; }
            .status-table th, .status-table td { font-size: 0.88rem; padding: 10px; }
            .footer-note { padding: 16px 18px; font-size: 0.9rem; }
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
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('certification_status', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="Certification.php">Certification</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Certification Status</span>
                            </nav>
                            <h1 class="title">Certification Status Register</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- Section title -->
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">Certification Status Register</h2>
                    <p class="text-muted mt-2 mb-0">Public record of suspended, withdrawn and reduced-scope certifications</p>
                    <div class="section-divider"></div>
                </div>

                <!-- Intro / legal basis -->
                <div class="intro-card">
                    <p>
                        In accordance with the <strong>Suspension / Withdrawal / Reduced Scope of Certification Procedure
                        (CER_PR_026)</strong>, ESWASA publishes information on the certified status of clients whose
                        certification has been suspended, withdrawn or reduced in scope. This register is updated as
                        decisions are taken by the Certification Approvals Committee. The current status of an active
                        certificate may be confirmed by contacting the ESWASA Certification Unit.
                    </p>
                </div>

                <!-- ===== Suspended ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span>Currently Suspended Certifications</span>
                        <span class="count"><?= count($suspended) ?></span>
                    </h3>
                    <?php if (empty($suspended)): ?>
                        <div class="empty-state">No certifications are currently under suspension.</div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Certificate&nbsp;No.</th>
                                    <th>Standard / Scope</th>
                                    <th>Suspended On</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suspended as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['client']) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective']) ?></td>
                                        <td><?= htmlspecialchars($row['reason']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- ===== Withdrawn ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span>Withdrawn / Cancelled Certifications</span>
                        <span class="count"><?= count($withdrawn) ?></span>
                    </h3>
                    <?php if (empty($withdrawn)): ?>
                        <div class="empty-state">No certifications have been withdrawn or cancelled.</div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Certificate&nbsp;No.</th>
                                    <th>Standard / Scope</th>
                                    <th>Withdrawn On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawn as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['client']) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- ===== Reduced Scope ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span>Reduced-Scope Certifications</span>
                        <span class="count"><?= count($reduced) ?></span>
                    </h3>
                    <?php if (empty($reduced)): ?>
                        <div class="empty-state">No certifications are currently operating under a reduced scope.</div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Certificate&nbsp;No.</th>
                                    <th>Standard / Original Scope</th>
                                    <th>Effective</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reduced as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['client']) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective']) ?></td>
                                        <td><?= htmlspecialchars($row['note']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Footer note: appeals + info requests -->
                <div class="footer-note">
                    <p class="mb-2">
                        <strong>Disputing a decision.</strong> A client whose certification has been suspended,
                        withdrawn or reduced in scope may submit a written appeal to ESWASA within
                        <strong>90 days</strong> of the decision, accompanied by the prescribed fee
                        (<a href="CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf" target="_blank">CER_PR_002 — Appeals Handling Procedure</a>).
                    </p>
                    <p class="mb-2">
                        <strong>Lodging a complaint.</strong> Complaints regarding a certified client or
                        any aspect of the ESWASA Management Systems Certification Scheme may be sent in
                        writing to the Marketing &amp; Sales Officer
                        (<a href="CER_PR_006 PROCEDURE FOR COMPLAINTS HANDLING.pdf" target="_blank">CER_PR_006 — Complaints Handling Procedure</a>).
                    </p>
                    <p class="mb-0">
                        <strong>Requesting information.</strong> For verification of certified status or any
                        other public information, contact the Marketing &amp; Sales Officer on
                        <a href="tel:+26825184633">(+268) 2518 4633</a> or
                        <a href="tel:+26878068944">(+268) 7806 8944</a>
                        (<a href="CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf" target="_blank">CER_PR_015 — Handling Requests for Information</a>).
                    </p>
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

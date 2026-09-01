<?php
/**
 * Shared body of the three public Certification Status Registers.
 *
 * Each certification mark on the home page verifies against its own register,
 * so there are three pages — Management Systems, Product and Ingelo — that
 * differ only in their scheme, hero copy and breadcrumb. Rather than three
 * copies of 550 lines of markup and CSS, the three thin page files set
 * $cert_scheme and include this.
 *
 * Published in accordance with CER_PR_026 §6.1.3 and §6.2.2, which require
 * suspension and withdrawal information to be made publicly accessible
 * through the ESWASA website.
 *
 * See docs/superpowers/specs/2026-09-01-cms-batch-d-design.md.
 */
if (!isset($cert_scheme) || !in_array($cert_scheme, ['ms', 'product', 'ingelo'], true)) {
    http_response_code(500);
    exit('cert_register_page.php included without a valid $cert_scheme.');
}

require_once __DIR__ . '/env.php';
include_once __DIR__ . '/db_connect.php';
include_once __DIR__ . '/breadcrumb_helper.php';
require_once __DIR__ . '/cms_helpers.php';

// Which mark this register covers. The breadcrumb parent differs because the
// three marks are explained on three different pages.
$CERT_SCHEMES = [
    'ms' => [
        'name'           => 'Management Systems',
        'parent_label'   => 'Management Systems',
        'parent_url'     => 'managementsystems.php',
        'breadcrumb'     => 'Management Systems Certification Status',
        'section_title'  => 'Management Systems Certification Status Register',
        'meta'           => 'Public register of suspended, withdrawn and reduced-scope certifications issued under the ESWASA Management Systems Certification Scheme.',
    ],
    'product' => [
        'name'           => 'Product',
        'parent_label'   => 'Product Certification',
        'parent_url'     => 'product.php',
        'breadcrumb'     => 'Product Certification Status',
        'section_title'  => 'Product Certification Status Register',
        'meta'           => 'Public register of suspended, withdrawn and reduced-scope permits issued under the ESWASA Product Certification Scheme.',
    ],
    'ingelo' => [
        'name'           => 'Ingelo',
        'parent_label'   => 'Ingelo Certification',
        'parent_url'     => 'ingelo.php',
        'breadcrumb'     => 'Ingelo Certification Status',
        'section_title'  => 'Ingelo Certification Status Register',
        'meta'           => 'Public register of suspended, withdrawn and reduced-scope certifications issued under the Ingelo MSME Product Certification Scheme.',
    ],
];
$cs = $CERT_SCHEMES[$cert_scheme];

// ── Register entries (DB-driven; edited via admin → Certification Status) ────
// Replaces three hardcoded PHP arrays that could only be changed by deploying.
$suspended = $withdrawn = $reduced = [];
$cert_reg_stmt = $conn->prepare('
    SELECT status, client_name, logo_path, cert_no, scope, effective_date, reason_note
    FROM certification_register
    WHERE scheme = ? AND is_active = 1
    ORDER BY sort_order ASC, effective_date DESC, id ASC
');
$cert_reg_stmt->bind_param('s', $cert_scheme);
$cert_reg_stmt->execute();
$cert_reg_res = $cert_reg_stmt->get_result();
while ($cert_reg_row = $cert_reg_res->fetch_assoc()) {
    switch ($cert_reg_row['status']) {
        case 'suspended': $suspended[] = $cert_reg_row; break;
        case 'withdrawn': $withdrawn[] = $cert_reg_row; break;
        case 'reduced':   $reduced[]   = $cert_reg_row; break;
    }
}
$cert_reg_stmt->close();

/**
 * The Client cell: the company logo where one has been uploaded, with the
 * name beneath it, or the name alone where one hasn't.
 */
function cert_reg_client_cell(array $row): string
{
    $name = htmlspecialchars($row['client_name']);
    $logo = trim((string)($row['logo_path'] ?? ''));
    if ($logo === '') {
        return '<span class="client-name">' . $name . '</span>';
    }
    return '<span class="client-with-logo">'
         . '<img src="' . htmlspecialchars($logo) . '" alt="' . $name . ' logo" class="client-logo">'
         . '<span class="client-name">' . $name . '</span>'
         . '</span>';
}

// Hero, subtitle and intro are per-scheme; column headers, empty states and
// the appeals/complaints/information footer are shared across all three.
$k_breadcrumb = 'cert_status_' . $cert_scheme . '_breadcrumb_title';
$k_title      = 'cert_status_' . $cert_scheme . '_section_title';
$k_subtitle   = 'cert_status_' . $cert_scheme . '_section_subtitle';
$k_intro      = 'cert_status_' . $cert_scheme . '_intro';

$cert_status_keys = [
    // Hero / breadcrumb, section title block and intro card — per scheme
    $k_breadcrumb,
    $k_title,
    $k_subtitle,
    $k_intro,
    // Suspended block
    'cert_status_suspended_title',
    'cert_status_suspended_empty',
    'cert_status_suspended_col_client',
    'cert_status_suspended_col_cert_no',
    'cert_status_suspended_col_scope',
    'cert_status_suspended_col_date',
    'cert_status_suspended_col_reason',
    // Withdrawn block
    'cert_status_withdrawn_title',
    'cert_status_withdrawn_empty',
    'cert_status_withdrawn_col_client',
    'cert_status_withdrawn_col_cert_no',
    'cert_status_withdrawn_col_scope',
    'cert_status_withdrawn_col_date',
    // Reduced block
    'cert_status_reduced_title',
    'cert_status_reduced_empty',
    'cert_status_reduced_col_client',
    'cert_status_reduced_col_cert_no',
    'cert_status_reduced_col_scope',
    'cert_status_reduced_col_date',
    'cert_status_reduced_col_note',
    // Footer note — Appeals
    'cert_status_footer_appeals_label',
    'cert_status_footer_appeals_body',
    'cert_status_footer_appeals_link_label',
    'cert_status_footer_appeals_link_url',
    // Footer note — Complaints
    'cert_status_footer_complaints_label',
    'cert_status_footer_complaints_body',
    'cert_status_footer_complaints_link_label',
    'cert_status_footer_complaints_link_url',
    // Footer note — Information requests
    'cert_status_footer_info_label',
    'cert_status_footer_info_body',
    'cert_status_footer_info_phone_1',
    'cert_status_footer_info_phone_2',
    'cert_status_footer_info_link_label',
    'cert_status_footer_info_link_url',
];

$pc = pc_get_many($conn, $cert_status_keys, [
    $k_breadcrumb => $cs['section_title'],
    $k_title      => $cs['section_title'],
    $k_subtitle   => 'Public record of suspended, withdrawn and reduced-scope certifications',
    // Seeded with the Management Systems wording on all three registers so no
    // page reads as broken. Product and Ingelo are edited in admin once their
    // own procedure references are confirmed.
    $k_intro      => "In accordance with the Suspension / Withdrawal / Reduced Scope of Certification Procedure (CER_PR_026), ESWASA publishes information on the certified status of clients whose certification has been suspended, withdrawn or reduced in scope. This register is updated as decisions are taken by the Certification Approvals Committee. The current status of an active certificate may be confirmed by contacting the ESWASA Certification Unit.",
    'cert_status_suspended_title' => 'Currently Suspended Certifications',
    'cert_status_suspended_empty' => 'No certifications are currently under suspension.',
    'cert_status_suspended_col_client' => 'Client',
    'cert_status_suspended_col_cert_no' => 'Certificate No.',
    'cert_status_suspended_col_scope' => 'Standard / Scope',
    'cert_status_suspended_col_date' => 'Suspended On',
    'cert_status_suspended_col_reason' => 'Reason',
    'cert_status_withdrawn_title' => 'Withdrawn / Cancelled Certifications',
    'cert_status_withdrawn_empty' => 'No certifications have been withdrawn or cancelled.',
    'cert_status_withdrawn_col_client' => 'Client',
    'cert_status_withdrawn_col_cert_no' => 'Certificate No.',
    'cert_status_withdrawn_col_scope' => 'Standard / Scope',
    'cert_status_withdrawn_col_date' => 'Withdrawn On',
    'cert_status_reduced_title' => 'Reduced-Scope Certifications',
    'cert_status_reduced_empty' => 'No certifications are currently operating under a reduced scope.',
    'cert_status_reduced_col_client' => 'Client',
    'cert_status_reduced_col_cert_no' => 'Certificate No.',
    'cert_status_reduced_col_scope' => 'Standard / Original Scope',
    'cert_status_reduced_col_date' => 'Effective',
    'cert_status_reduced_col_note' => 'Notes',
    'cert_status_footer_appeals_label' => 'Disputing a decision.',
    'cert_status_footer_appeals_body' => 'A client whose certification has been suspended, withdrawn or reduced in scope may submit a written appeal to ESWASA within 90 days of the decision, accompanied by the prescribed fee',
    'cert_status_footer_appeals_link_label' => 'CER_PR_002 — Appeals Handling Procedure',
    'cert_status_footer_appeals_link_url' => 'CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf',
    'cert_status_footer_complaints_label' => 'Lodging a complaint.',
    'cert_status_footer_complaints_body' => 'Complaints regarding a certified client or any aspect of the ESWASA Management Systems Certification Scheme may be sent in writing to the Marketing & Sales Officer',
    'cert_status_footer_complaints_link_label' => 'CER_PR_006 — Complaints Handling Procedure',
    'cert_status_footer_complaints_link_url' => 'CER_PR_006 PROCEDURE FOR COMPLAINTS HANDLING.pdf',
    'cert_status_footer_info_label' => 'Requesting information.',
    'cert_status_footer_info_body' => 'For verification of certified status or any other public information, contact the Marketing & Sales Officer on',
    'cert_status_footer_info_phone_1' => '(+268) 2518 4633',
    'cert_status_footer_info_phone_2' => '(+268) 7806 8944',
    'cert_status_footer_info_link_label' => 'CER_PR_015 — Handling Requests for Information',
    'cert_status_footer_info_link_url' => 'CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf',
]);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc[$k_title]) ?> - ESWASA</title>
    <meta name="description" content="<?= pc_h($cs['meta']) ?>">
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
        .text-muted { color: #2B3388 !important; }
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
            margin: 0; color: #2B3388;
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
        .status-table .client-with-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .status-table .client-logo {
            max-width: 64px;
            max-height: 40px;
            width: auto;
            height: auto;
            object-fit: contain;
            flex: 0 0 auto;
        }
        .status-table .client-name { font-weight: 600; }
        .status-table .cert-no { font-weight: 600; white-space: nowrap; }
        .status-table .date    { white-space: nowrap; color: #2B3388; }

        .empty-state {
            background: #fff;
            border: 1px dashed rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            padding: 22px 24px;
            color: #2B3388;
            font-style: italic;
        }

        .footer-note {
            margin-top: 50px;
            padding: 22px 24px;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            font-size: 0.95rem;
            color: #2B3388;
            line-height: 1.7;
        }
        .footer-note strong { color: #2B3388; }
        .footer-note a {
            color: #2B3388;
            text-decoration: underline;
            font-weight: 600;
        }
        .footer-note a:hover { color: #2B3388; }

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
    <?php include(__DIR__ . "/header.php")?>
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
                                    <a href="<?= pc_h($cs['parent_url']) ?>"><?= pc_h($cs['parent_label']) ?></a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($cs['breadcrumb']) ?></span>
                            </nav>
                            <h1 class="title"><?= pc_h($pc[$k_breadcrumb]) ?></h1>
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
                    <h2 class="display-6 fw-bold"><?= pc_h($pc[$k_title]) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc[$k_subtitle]) ?></p>
                    <div class="section-divider"></div>
                </div>

                <!-- Intro / legal basis -->
                <div class="intro-card">
                    <?= pc_paragraphs_html($pc[$k_intro]) ?>
                </div>

                <!-- ===== Suspended ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span><?= pc_h($pc['cert_status_suspended_title']) ?></span>
                        <span class="count"><?= count($suspended) ?></span>
                    </h3>
                    <?php if (empty($suspended)): ?>
                        <div class="empty-state"><?= pc_h($pc['cert_status_suspended_empty']) ?></div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th><?= pc_h($pc['cert_status_suspended_col_client']) ?></th>
                                    <th><?= pc_h($pc['cert_status_suspended_col_cert_no']) ?></th>
                                    <th><?= pc_h($pc['cert_status_suspended_col_scope']) ?></th>
                                    <th><?= pc_h($pc['cert_status_suspended_col_date']) ?></th>
                                    <th><?= pc_h($pc['cert_status_suspended_col_reason']) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($suspended as $row): ?>
                                    <tr>
                                        <td><?= cert_reg_client_cell($row) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective_date']) ?></td>
                                        <td><?= htmlspecialchars($row['reason_note']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- ===== Withdrawn ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span><?= pc_h($pc['cert_status_withdrawn_title']) ?></span>
                        <span class="count"><?= count($withdrawn) ?></span>
                    </h3>
                    <?php if (empty($withdrawn)): ?>
                        <div class="empty-state"><?= pc_h($pc['cert_status_withdrawn_empty']) ?></div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th><?= pc_h($pc['cert_status_withdrawn_col_client']) ?></th>
                                    <th><?= pc_h($pc['cert_status_withdrawn_col_cert_no']) ?></th>
                                    <th><?= pc_h($pc['cert_status_withdrawn_col_scope']) ?></th>
                                    <th><?= pc_h($pc['cert_status_withdrawn_col_date']) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawn as $row): ?>
                                    <tr>
                                        <td><?= cert_reg_client_cell($row) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective_date']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- ===== Reduced Scope ===== -->
                <div class="status-block">
                    <h3 class="status-title">
                        <span><?= pc_h($pc['cert_status_reduced_title']) ?></span>
                        <span class="count"><?= count($reduced) ?></span>
                    </h3>
                    <?php if (empty($reduced)): ?>
                        <div class="empty-state"><?= pc_h($pc['cert_status_reduced_empty']) ?></div>
                    <?php else: ?>
                        <table class="status-table">
                            <thead>
                                <tr>
                                    <th><?= pc_h($pc['cert_status_reduced_col_client']) ?></th>
                                    <th><?= pc_h($pc['cert_status_reduced_col_cert_no']) ?></th>
                                    <th><?= pc_h($pc['cert_status_reduced_col_scope']) ?></th>
                                    <th><?= pc_h($pc['cert_status_reduced_col_date']) ?></th>
                                    <th><?= pc_h($pc['cert_status_reduced_col_note']) ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reduced as $row): ?>
                                    <tr>
                                        <td><?= cert_reg_client_cell($row) ?></td>
                                        <td class="cert-no"><?= htmlspecialchars($row['cert_no']) ?></td>
                                        <td><?= htmlspecialchars($row['scope']) ?></td>
                                        <td class="date"><?= htmlspecialchars($row['effective_date']) ?></td>
                                        <td><?= htmlspecialchars($row['reason_note']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Footer note: appeals + info requests -->
                <div class="footer-note">
                    <p class="mb-2">
                        <strong><?= pc_h($pc['cert_status_footer_appeals_label']) ?></strong> <?= pc_h($pc['cert_status_footer_appeals_body']) ?>
                        (<a href="<?= pc_h($pc['cert_status_footer_appeals_link_url']) ?>" target="_blank"><?= pc_h($pc['cert_status_footer_appeals_link_label']) ?></a>).
                    </p>
                    <p class="mb-2">
                        <strong><?= pc_h($pc['cert_status_footer_complaints_label']) ?></strong> <?= pc_h($pc['cert_status_footer_complaints_body']) ?>
                        (<a href="<?= pc_h($pc['cert_status_footer_complaints_link_url']) ?>" target="_blank"><?= pc_h($pc['cert_status_footer_complaints_link_label']) ?></a>).
                    </p>
                    <p class="mb-0">
                        <strong><?= pc_h($pc['cert_status_footer_info_label']) ?></strong> <?= pc_h($pc['cert_status_footer_info_body']) ?>
                        <a href="tel:<?= pc_h(preg_replace('/[^0-9+]/', '', $pc['cert_status_footer_info_phone_1'])) ?>"><?= pc_h($pc['cert_status_footer_info_phone_1']) ?></a> or
                        <a href="tel:<?= pc_h(preg_replace('/[^0-9+]/', '', $pc['cert_status_footer_info_phone_2'])) ?>"><?= pc_h($pc['cert_status_footer_info_phone_2']) ?></a>
                        (<a href="<?= pc_h($pc['cert_status_footer_info_link_url']) ?>" target="_blank"><?= pc_h($pc['cert_status_footer_info_link_label']) ?></a>).
                    </p>
                </div>
            </div>
        </section>

    </main>
    <!-- main-area-end -->

    <!-- footer-area -->
    <?php include(__DIR__ . "/footer.php")?>
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

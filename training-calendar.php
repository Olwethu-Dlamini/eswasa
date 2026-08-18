<?php
include_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require __DIR__ . '/includes/training_families.php';

// ── Training sessions + intakes (DB-driven; edited via admin) ────────────────
// Schema: training_sessions + training_intakes (see migration_2026_05_27.sql).
$train_cal_sessions = [];
$res = $conn->query('
    SELECT id, code, family, title, location, duration, price, sort_order
    FROM training_sessions
    WHERE is_active = 1
    ORDER BY sort_order ASC, id ASC
');
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $row['intakes'] = [];
        $train_cal_sessions[(int)$row['id']] = $row;
    }
}
if ($train_cal_sessions) {
    $ids = array_keys($train_cal_sessions);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("
        SELECT session_id, start_date, end_date, label
        FROM training_intakes
        WHERE session_id IN ($placeholders)
        ORDER BY session_id ASC, sort_order ASC, id ASC
    ");
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        $sid = (int)$row['session_id'];
        if (isset($train_cal_sessions[$sid])) {
            $train_cal_sessions[$sid]['intakes'][] = [
                'start' => $row['start_date'],
                'end'   => $row['end_date'],
                'label' => $row['label'],
            ];
        }
    }
    $stmt->close();
}

// ── Page-copy CMS keys (hero/breadcrumb/buttons/modal) ───────────────────────
$train_cal_defaults = [
    // Breadcrumb / hero
    'train_cal_breadcrumb_home'        => 'Home',
    'train_cal_breadcrumb_parent'      => 'Training',
    'train_cal_breadcrumb_current'     => 'Calendar',
    'train_cal_hero_title'             => 'Training Calendar',

    // Section heading
    'train_cal_section_title'          => 'Upcoming Training Sessions',
    'train_cal_section_subtitle'       => 'Plan Your Learning Journey',

    // Action buttons (Prospectus / Application / E-Learning)
    'train_cal_prospectus_label'       => 'Prospectus',
    // Was admin/downloads/, a directory that has never existed — the
    // Prospectus button 404'd. The file lives in admin/uploads/, which is
    // also where the admin's new PDF upload field writes.
    // See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md (A6).
    'train_cal_prospectus_url'         => 'admin/uploads/ESWASA TRAINING PROSPECTUS 2025-26.pdf',
    'train_cal_application_label'      => 'Application Form',
    'train_cal_application_url'        => 'qoute_training.php',
    'train_cal_elearning_label'        => 'E-Learning Platform',
    'train_cal_elearning_soon_badge'   => 'Coming soon',

    // Trainings list header
    'train_cal_year_label'             => '2026 Schedule',
    'train_cal_reset_filter_label'     => 'Show all',

    // Apply modal
    'train_cal_modal_title_prefix'     => 'Apply for Training:',
    'train_cal_modal_title_on'         => 'on',
    'train_cal_modal_intro'            => 'Please complete the form below to apply for the selected training session.',
    'train_cal_modal_label_name'       => 'Full Name *',
    'train_cal_modal_label_email'      => 'Email Address *',
    'train_cal_modal_label_phone'      => 'Phone Number',
    'train_cal_modal_label_company'    => 'Company/Organisation',
    'train_cal_modal_label_position'   => 'Position/Title',
    'train_cal_modal_label_comments'   => 'Comments or Questions',
    'train_cal_modal_consent'          => 'I agree to the Training Policies and consent to the processing of my personal data as described in the prospectus.',
    'train_cal_modal_submit_label'     => 'Submit Application',
];

$pc = pc_get_many($conn, array_keys($train_cal_defaults), $train_cal_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['train_cal_hero_title']) ?> - ESWASA</title>
    <meta name="description" content="View the upcoming training calendar for ESWASA. Access the prospectus and apply for courses.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

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
        .text-muted {
            color: #2B3388 !important;
        }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        .breadcrumb-separator i { color: #fff !important; }

        /* Section heading */
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .text-gradient-primary {
            color: #2B3388 !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
        }

        /* ===== Layout: trainings list + calendar sidebar ===== */
        .training-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
            gap: 32px;
            align-items: start;
        }
        .calendar-col {
            position: sticky;
            top: 100px;
        }

        /* ===== Trainings list (middle) ===== */
        .trainings-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 14px;
        }
        .trainings-header .year-label {
            color: #2B3388;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
        }
        .trainings-header .reset-filter {
            background: none;
            border: none;
            color: #2B3388;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            visibility: hidden;
        }
        .trainings-header .reset-filter.visible { visibility: visible; }
        .trainings-header .reset-filter:hover {
            background: rgba(43, 51, 136, 0.06);
        }

        /* ===== Pager (under the trainings list) ===== */
        .trainings-pager {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid rgba(43, 51, 136, 0.12);
        }
        .trainings-pager .page-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 3px;
            color: #2B3388;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease;
        }
        .trainings-pager .page-btn:hover:not(:disabled):not(.is-current) {
            background: rgba(43, 51, 136, 0.06);
            border-color: rgba(43, 51, 136, 0.45);
        }
        .trainings-pager .page-btn.is-current {
            background: #2B3388;
            border-color: #2B3388;
            color: #fff;
            cursor: default;
        }
        .trainings-pager .page-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .trainings-pager .page-ellipsis {
            color: #2B3388;
            font-size: 13px;
            padding: 0 4px;
        }
        .trainings-pager.is-hidden { display: none; }
        @media (max-width: 575.98px) {
            .trainings-pager .page-btn {
                min-width: 30px;
                height: 30px;
                font-size: 12px;
                padding: 0 8px;
            }
        }

        .training-card {
            --card-glow: rgba(43, 51, 136, 0.10);
            --card-glow-strong: rgba(43, 51, 136, 0.22);
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 16px 18px;
            margin-bottom: 14px;
            cursor: pointer;
            box-shadow: 0 2px 10px var(--card-glow);
            transition: border-color .2s ease, box-shadow .25s ease, background-color .2s ease;
        }
        .training-card:hover {
            border-color: rgba(43, 51, 136, 0.30);
            box-shadow: 0 6px 18px var(--card-glow-strong);
        }
        .training-card.is-active {
            border-color: #2B3388;
            background-color: rgba(43, 51, 136, 0.03);
            box-shadow: 0 8px 22px var(--card-glow-strong);
        }
        .training-card-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .training-code {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #2B3388;
            background-color: rgba(43, 51, 136, 0.08);
            padding: 3px 8px;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .training-count {
            font-size: 12px;
            color: #2B3388;
        }
        .training-title {
            font-size: 15px;
            font-weight: 600;
            color: #2B3388;
            line-height: 1.4;
            margin: 0 0 10px 0;
        }
        .training-dates {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #2B3388;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.25);
            padding: 4px 10px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .date-chip:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }
        .date-chip i {
            font-size: 10px;
        }

        /* ===== Calendar (sidebar) ===== */
        .calendar-grid {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 14px;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .calendar-header #current-month {
            color: #2B3388;
            font-weight: 700;
            font-size: 15px;
        }
        .calendar-header button.cal-nav {
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.30);
            background: #fff;
            width: 28px;
            height: 28px;
            border-radius: 3px;
            padding: 0;
            font-size: 14px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }
        .calendar-header button.cal-nav:hover {
            background: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-weight: 700;
            color: #2B3388;
            font-size: 11px;
            letter-spacing: 0.4px;
            padding: 6px 0;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
            margin-bottom: 4px;
        }
        .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
        }
        .day {
            aspect-ratio: 1 / 1;
            border: 1px solid transparent;
            border-radius: 3px;
            padding: 0;
            cursor: default;
            transition: background-color .2s ease, border-color .2s ease;
            font-size: 13px;
            color: #2B3388;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .day.empty { cursor: default; }
        /* Day pills — one per training running that day. A single-training day
           keeps its old appearance because the pill fills the cell; multiple
           trainings split the cell into horizontal bands.
           See docs/superpowers/specs/2026-08-18-cms-batch-c-design.md (C4). */
        .day .day-number {
            position: absolute; top: 2px; left: 4px;
            font-size: 11px; line-height: 1; z-index: 2;
            pointer-events: none; opacity: .85;
        }
        .day-pills {
            position: absolute; inset: 0;
            display: flex; flex-direction: column; gap: 1px;
            border-radius: 3px; overflow: hidden;
        }
        .day-pill {
            flex: 1 1 0; min-height: 0;
            border: none; padding: 0;
            font-size: 10px; font-weight: 700; line-height: 1; letter-spacing: .2px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            transition: filter .15s ease;
        }
        .day-pill:hover { filter: brightness(.88); }
        .day-pill:focus-visible { outline: 2px solid #2B3388; outline-offset: -2px; }
        /* With one training the code would collide with the day number, so it is
           hidden and the colour alone carries the meaning, exactly as before. */
        .day-pills:not(.day-pills--multi) .day-pill { font-size: 0; }
        /* Four or more: scroll inside the cell rather than squashing the pills
           into unreadable slivers or breaking the grid alignment. */
        .day-pills--multi { overflow-y: auto; scrollbar-width: none; }
        .day-pills--multi::-webkit-scrollbar { display: none; }
        .day-pills--multi .day-pill { min-height: 13px; }
        .day.has-event {
            cursor: pointer;
            font-weight: 700;
            /* color, background-color, border-color set inline per training */
        }
        .day.has-event:hover { filter: brightness(0.92); }
        /* Dimmed state (when another training is selected) overrides inline color */
        .day.has-event.dimmed {
            background-color: rgba(43, 51, 136, 0.08) !important;
            color: rgba(43, 51, 136, 0.55) !important;
            border: 1px solid rgba(43, 51, 136, 0.14) !important;
            font-weight: 600;
        }
        .day.has-event.dimmed:hover {
            background-color: rgba(43, 51, 136, 0.16) !important;
            border-color: rgba(43, 51, 136, 0.25) !important;
        }
        .day.today {
            border: 1.5px solid #2B3388;
            font-weight: 700;
            background-color: rgba(43, 51, 136, 0.08);
        }
        .day.has-event.today {
            box-shadow: 0 0 0 2px #fff inset;
        }
        .calendar-legend {
            font-size: 11px;
            color: #2B3388;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid rgba(43, 51, 136, 0.12);
        }
        .calendar-legend .legend-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
        }
        .calendar-legend .legend-item {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .calendar-legend .swatch {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 5px;
            vertical-align: middle;
        }
        .calendar-legend .swatch.today { background: #fff; border: 1.5px solid #2B3388; }
        .calendar-legend .legend-divider {
            height: 1px;
            background: rgba(43, 51, 136, 0.10);
            margin: 8px 0;
        }

        /* Calendar action tabs (Prospectus / Application / E-Learning) */
        .calendar-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }
        .prospectus-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            color: #2B3388;
            text-decoration: none;
            padding: 10px 18px;
            border: 1px solid rgba(43, 51, 136, 0.30);
            border-radius: 4px;
            background: #fff;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }
        .prospectus-link:hover {
            text-decoration: none;
            color: #fff;
            background: #2B3388;
            border-color: #2B3388;
        }
        .prospectus-link i {
            margin-right: 6px;
        }
        /* "Coming soon" variant — visually de-emphasised, not clickable */
        .prospectus-link.is-soon {
            color: rgba(43, 51, 136, 0.55);
            border-color: rgba(43, 51, 136, 0.18);
            background: rgba(43, 51, 136, 0.03);
            cursor: not-allowed;
            pointer-events: none;
        }
        .prospectus-link.is-soon:hover {
            color: rgba(43, 51, 136, 0.55);
            background: rgba(43, 51, 136, 0.03);
            border-color: rgba(43, 51, 136, 0.18);
        }
        .prospectus-link .soon-badge {
            display: inline-block;
            margin-left: 8px;
            background: rgba(43, 51, 136, 0.10);
            color: #2B3388;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 2px 8px;
            border-radius: 10px;
        }

        /* Apply Modal */
        .modal-content {
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.18);
            box-shadow: 0 10px 30px rgba(43, 51, 136, 0.20);
        }
        .modal-header {
            background: #fff;
            color: #2B3388;
            padding: 18px 22px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.12);
        }
        .modal-header .modal-title {
            color: #2B3388;
            font-weight: 700;
            font-size: 17px;
        }
        .modal-body { padding: 22px; color: #2B3388; }
        .modal-body .form-label {
            color: #2B3388;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
        }
        .modal-body .form-control {
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            color: #2B3388;
            font-size: 14px;
        }
        .modal-body .form-control:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
        }
        .modal-body .form-check-label,
        .modal-body .form-check-label a {
            color: #2B3388;
            font-size: 14px;
        }
        .modal-body .btn-primary {
            background-color: #2B3388 !important;
            border-color: #2B3388 !important;
            color: #fff !important;
            padding: 10px 18px;
            font-weight: 600;
            border-radius: 4px;
        }
        .modal-body .btn-primary:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .display-6 { font-size: 1.9rem !important; }
            .training-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .calendar-col {
                position: static;
                order: -1; /* calendar on top of list on tablet/mobile */
            }
            .calendar-grid { max-width: 520px; margin: 0 auto; }
        }
        @media (max-width: 767.98px) {
            .display-6 { font-size: 1.55rem !important; }
            .training-title { font-size: 14px; }
            .training-card { padding: 14px; }
            .day { font-size: 12px; }
            /* Codes will not fit in a mobile cell; fall back to colour bands. */
            .day-pills--multi .day-pill { font-size: 0; min-height: 10px; }
            .prospectus-link { font-size: 13px; padding: 8px 14px; }
        }
        @media (max-width: 575.98px) {
            .day { font-size: 11px; }
            .calendar-days div { font-size: 10px; }
            .calendar-grid { padding: 12px; }
            .date-chip { font-size: 11px; padding: 3px 8px; }
        }
    </style>
</head>

<body>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('training_calendar', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php"><?= pc_h($pc['train_cal_breadcrumb_home']) ?></a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['train_cal_breadcrumb_parent']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['train_cal_breadcrumb_current']) ?></span>
                            </nav>
                            <h1 class="title"><?= pc_h($pc['train_cal_hero_title']) ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Training Calendar Section -->
        <section id="training_calendar_section" class="content_section py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold"><?= pc_h($pc['train_cal_section_title']) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc['train_cal_section_subtitle']) ?></p>
                    <div class="section-divider"></div>
                </div>

                <!-- Calendar Action Tabs (Prospectus / Application / E-Learning) -->
                <div class="calendar-actions text-center mb-4">
                    <a href="<?= pc_h($pc['train_cal_prospectus_url']) ?>" class="prospectus-link" target="_blank" rel="noopener" download>
                        <i class="fas fa-file-pdf"></i> <?= pc_h($pc['train_cal_prospectus_label']) ?>
                    </a>
                    <a href="<?= pc_h($pc['train_cal_application_url']) ?>" class="prospectus-link">
                        <i class="fas fa-file-signature"></i> <?= pc_h($pc['train_cal_application_label']) ?>
                    </a>
                    <a href="#" class="prospectus-link is-soon" aria-disabled="true" title="Coming soon">
                        <i class="fas fa-laptop"></i> <?= pc_h($pc['train_cal_elearning_label']) ?> <span class="soon-badge"><?= pc_h($pc['train_cal_elearning_soon_badge']) ?></span>
                    </a>
                </div>

                <!-- Two-column layout: trainings list (middle) + calendar (side) -->
                <div class="training-layout">

                    <!-- Trainings list -->
                    <div class="trainings-col">
                        <div class="trainings-header">
                            <span class="year-label"><?= pc_h($pc['train_cal_year_label']) ?></span>
                            <button id="reset-filter" class="reset-filter" type="button">
                                <i class="fas fa-times me-1"></i> <?= pc_h($pc['train_cal_reset_filter_label']) ?>
                            </button>
                        </div>
                        <div id="trainings-list"></div>
                        <nav id="trainings-pager" class="trainings-pager is-hidden" aria-label="Trainings pagination"></nav>
                    </div>

                    <!-- Calendar sidebar -->
                    <div class="calendar-col">
                        <div class="calendar-grid">
                            <div class="calendar-header">
                                <button id="prev-month" class="cal-nav" aria-label="Previous month">&lt;</button>
                                <span id="current-month"></span>
                                <button id="next-month" class="cal-nav" aria-label="Next month">&gt;</button>
                            </div>
                            <div class="calendar-days">
                                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                            </div>
                            <div id="calendar-body" class="calendar-body"></div>
                            <div class="calendar-legend">
                                <div id="legend-families" class="legend-row"></div>
                                <div class="legend-divider"></div>
                                <div class="legend-row">
                                    <span class="legend-item"><span class="swatch today"></span>Today</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Modal -->
                <div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= pc_h($pc['train_cal_modal_title_prefix']) ?> <span id="modal-event"></span> <?= pc_h($pc['train_cal_modal_title_on']) ?> <span id="modal-date"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3"><?= pc_h($pc['train_cal_modal_intro']) ?></p>
                                <form id="applyForm">
                                    <div class="mb-3">
                                        <label for="name" class="form-label"><?= pc_h($pc['train_cal_modal_label_name']) ?></label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label"><?= pc_h($pc['train_cal_modal_label_email']) ?></label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label"><?= pc_h($pc['train_cal_modal_label_phone']) ?></label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="company" class="form-label"><?= pc_h($pc['train_cal_modal_label_company']) ?></label>
                                        <input type="text" class="form-control" id="company">
                                    </div>
                                    <div class="mb-3">
                                        <label for="position" class="form-label"><?= pc_h($pc['train_cal_modal_label_position']) ?></label>
                                        <input type="text" class="form-control" id="position">
                                    </div>
                                    <div class="mb-3">
                                        <label for="comments" class="form-label"><?= pc_h($pc['train_cal_modal_label_comments']) ?></label>
                                        <textarea class="form-control" id="comments" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="consentCheck" required>
                                        <label class="form-check-label" for="consentCheck"><?= pc_h($pc['train_cal_modal_consent']) ?></label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="ico-check3 me-2"></i><?= pc_h($pc['train_cal_modal_submit_label']) ?></button>
                                </form>
                            </div>
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

    <script>
        // Family colour map — single source of truth in includes/training_families.php,
        // injected so the admin family dropdown and this page stay in sync.
        const FAMILY_COLOURS = <?= json_encode($TRAINING_FAMILIES, JSON_UNESCAPED_UNICODE) ?>;

        // Trainings + intakes loaded from training_sessions / training_intakes tables.
        // Edited via admin/pages/training_calendar.php. is_active=0 rows are filtered server-side.
        const trainings = [
<?php foreach ($train_cal_sessions as $row):
    if (empty($row['intakes'])) continue; // a session with no intakes can't render meaningfully
?>
            { code: <?= json_encode($row['code'], JSON_UNESCAPED_UNICODE) ?>, family: <?= json_encode($row['family'], JSON_UNESCAPED_UNICODE) ?>, title: <?= json_encode($row['title'], JSON_UNESCAPED_UNICODE) ?>, sessions: [
<?php foreach ($row['intakes'] as $s): ?>
                { start: <?= json_encode($s['start']) ?>, end: <?= json_encode($s['end']) ?>, label: <?= json_encode($s['label'], JSON_UNESCAPED_UNICODE) ?> },
<?php endforeach; ?>
            ]},
<?php endforeach; ?>
        ];
        // Resolve colour per training from the family map
        trainings.forEach(t => { t.color = FAMILY_COLOURS[t.family]; });

        // Decide white vs near-black text on top of a swatch via relative luminance
        function textOn(hex) {
            const r = parseInt(hex.slice(1, 3), 16) / 255;
            const g = parseInt(hex.slice(3, 5), 16) / 255;
            const b = parseInt(hex.slice(5, 7), 16) / 255;
            const lin = c => (c <= 0.03928) ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
            const L = 0.2126 * lin(r) + 0.7152 * lin(g) + 0.0722 * lin(b);
            return L > 0.55 ? '#1a1a1a' : '#fff';
        }
        function hexToRgba(hex, a) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${a})`;
        }

        // Build a date → { training, session } lookup so every day of a session lights up on the calendar
        const eventByDate = {};
        function eachDateInRange(startStr, endStr, cb) {
            const [sy, sm, sd] = startStr.split('-').map(Number);
            const [ey, em, ed] = endStr.split('-').map(Number);
            const cur = new Date(sy, sm - 1, sd);
            const end = new Date(ey, em - 1, ed);
            while (cur <= end) {
                const key = `${cur.getFullYear()}-${String(cur.getMonth() + 1).padStart(2, '0')}-${String(cur.getDate()).padStart(2, '0')}`;
                cb(key);
                cur.setDate(cur.getDate() + 1);
            }
        }
        trainings.forEach((t, idx) => {
            t.id = idx;
            t.sessions.forEach(s => {
                eachDateInRange(s.start, s.end, (key) => {
                    // One date can carry several trainings. This used to be a
                    // plain assignment, so a later training silently overwrote an
                    // earlier one and only the last was ever shown or clickable.
                    // See docs/.../2026-08-18-cms-batch-c-design.md (C4).
                    (eventByDate[key] = eventByDate[key] || []).push({ training: t, session: s });
                });
            });
        });

        // Dynamic "today" — the calendar opens on the current month and highlights today's date
        const _now = new Date();
        const today = `${_now.getFullYear()}-${String(_now.getMonth() + 1).padStart(2, '0')}-${String(_now.getDate()).padStart(2, '0')}`;
        let currentDate = new Date(_now.getFullYear(), _now.getMonth(), 1);
        let selectedTrainingId = null;

        // Pager state — 6 trainings per page; pager hides itself when only 1 page exists.
        const PAGE_SIZE = 6;
        let currentPage = 1;
        function totalPages() { return Math.max(1, Math.ceil(trainings.length / PAGE_SIZE)); }
        function pageFor(trainingId) {
            const idx = trainings.findIndex(t => t.id === trainingId);
            return idx < 0 ? 1 : Math.floor(idx / PAGE_SIZE) + 1;
        }
        function goToPage(p) {
            const tp = totalPages();
            const next = Math.min(Math.max(1, p), tp);
            if (next === currentPage) return;
            currentPage = next;
            renderTrainingsList();
            renderPager();
        }

        function renderTrainingsList() {
            const list = document.getElementById('trainings-list');
            list.innerHTML = '';
            const start = (currentPage - 1) * PAGE_SIZE;
            const slice = trainings.slice(start, start + PAGE_SIZE);
            slice.forEach(t => {
                const card = document.createElement('div');
                card.className = 'training-card' + (selectedTrainingId === t.id ? ' is-active' : '');
                card.dataset.id = t.id;
                card.style.setProperty('--card-glow', hexToRgba(t.color, 0.20));
                card.style.setProperty('--card-glow-strong', hexToRgba(t.color, 0.38));

                const head = document.createElement('div');
                head.className = 'training-card-head';
                head.innerHTML = `
                    <span class="training-code" style="background-color:${t.color};color:${textOn(t.color)};">${t.code}</span>
                    <span class="training-count">${t.family} · ${t.sessions.length} intake${t.sessions.length > 1 ? 's' : ''}</span>
                `;

                const title = document.createElement('p');
                title.className = 'training-title';
                title.textContent = t.title;

                const datesRow = document.createElement('div');
                datesRow.className = 'training-dates';
                t.sessions.forEach(s => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'date-chip';
                    chip.dataset.date = s.start;
                    chip.innerHTML = `<i class="fas fa-calendar-day"></i>${s.label}`;
                    chip.addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        openApplyModal(t, s);
                    });
                    datesRow.appendChild(chip);
                });

                card.appendChild(head);
                card.appendChild(title);
                card.appendChild(datesRow);

                card.addEventListener('click', () => selectTraining(t.id, true));
                list.appendChild(card);
            });
        }

        function selectTraining(id, jumpCalendar) {
            selectedTrainingId = (selectedTrainingId === id) ? null : id;
            const resetBtn = document.getElementById('reset-filter');
            resetBtn.classList.toggle('visible', selectedTrainingId !== null);

            // Jump calendar to the first session of the selected training, and
            // make sure the selected card's page is the one being shown.
            if (jumpCalendar && selectedTrainingId !== null) {
                const t = trainings[selectedTrainingId];
                const first = t.sessions[0];
                if (first) {
                    const [y, m] = first.start.split('-').map(Number);
                    currentDate = new Date(y, m - 1, 1);
                }
                const targetPage = pageFor(selectedTrainingId);
                if (targetPage !== currentPage) currentPage = targetPage;
            }
            renderTrainingsList();
            renderPager();
            renderCalendar();
        }

        function renderPager() {
            const pager = document.getElementById('trainings-pager');
            const tp = totalPages();
            pager.innerHTML = '';
            if (tp <= 1) {
                pager.classList.add('is-hidden');
                return;
            }
            pager.classList.remove('is-hidden');

            function btn(label, page, opts) {
                opts = opts || {};
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'page-btn' + (opts.current ? ' is-current' : '');
                b.textContent = label;
                if (opts.aria) b.setAttribute('aria-label', opts.aria);
                if (opts.current) b.setAttribute('aria-current', 'page');
                if (opts.disabled) b.disabled = true;
                else b.addEventListener('click', () => goToPage(page));
                return b;
            }

            pager.appendChild(btn('‹', currentPage - 1, { disabled: currentPage === 1, aria: 'Previous page' }));

            // Number window: always show 1, last, current, neighbours; ellipsis for gaps.
            const pages = new Set([1, tp, currentPage, currentPage - 1, currentPage + 1]);
            const sorted = [...pages].filter(p => p >= 1 && p <= tp).sort((a, b) => a - b);
            let prev = 0;
            sorted.forEach(p => {
                if (p - prev > 1) {
                    const e = document.createElement('span');
                    e.className = 'page-ellipsis';
                    e.textContent = '…';
                    pager.appendChild(e);
                }
                pager.appendChild(btn(String(p), p, { current: p === currentPage }));
                prev = p;
            });

            pager.appendChild(btn('›', currentPage + 1, { disabled: currentPage === tp, aria: 'Next page' }));
        }

        function renderCalendar() {
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();
            document.getElementById('current-month').textContent =
                new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });

            const calendarBody = document.getElementById('calendar-body');
            calendarBody.innerHTML = '';

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Build the set of date-keys that belong to the selected training (across all its sessions)
            let selectedDates = null;
            if (selectedTrainingId !== null) {
                selectedDates = new Set();
                trainings[selectedTrainingId].sessions.forEach(s => {
                    eachDateInRange(s.start, s.end, (k) => selectedDates.add(k));
                });
            }

            let day = 1;
            for (let i = 0; i < 6; i++) {
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        const emptyDiv = document.createElement('div');
                        emptyDiv.className = 'day empty';
                        calendarBody.appendChild(emptyDiv);
                    } else if (day <= daysInMonth) {
                        const dayDiv = document.createElement('div');
                        dayDiv.className = 'day';
                        dayDiv.textContent = day;
                        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        if (dateKey === today) dayDiv.classList.add('today');

                        const entries = eventByDate[dateKey];
                        if (entries && entries.length) {
                            dayDiv.classList.add('has-event');
                            dayDiv.dataset.date = dateKey;
                            if (selectedDates && !selectedDates.has(dateKey)) {
                                dayDiv.classList.add('dimmed');
                            }

                            // One pill per training running that day, stacked inside
                            // the cell. Each pill is separately clickable; the cell
                            // itself is not, so a day with three trainings is
                            // unambiguous. A single-training day keeps its previous
                            // look because the pill fills the cell.
                            const stack = document.createElement('div');
                            stack.className = 'day-pills' + (entries.length > 1 ? ' day-pills--multi' : '');

                            entries.forEach(({ training, session }) => {
                                const pill = document.createElement('button');
                                pill.type = 'button';
                                pill.className = 'day-pill';
                                pill.style.backgroundColor = training.color;
                                pill.style.color = textOn(training.color);
                                pill.textContent = training.code;
                                pill.title = `${training.code} — ${training.title} (${session.label})`;
                                pill.setAttribute('aria-label',
                                    `Apply for ${training.code}, ${training.title}, ${session.label}`);
                                pill.addEventListener('click', (ev) => {
                                    ev.stopPropagation();
                                    openApplyModal(training, session);
                                });
                                stack.appendChild(pill);
                            });

                            const num = document.createElement('span');
                            num.className = 'day-number';
                            num.textContent = day;
                            dayDiv.textContent = '';
                            dayDiv.appendChild(num);
                            dayDiv.appendChild(stack);
                        }
                        calendarBody.appendChild(dayDiv);
                        day++;
                    }
                }
            }
        }

        function openApplyModal(training, session) {
            document.getElementById('modal-date').textContent = session.label;
            document.getElementById('modal-event').textContent = `${training.code} — ${training.title}`;
            new bootstrap.Modal(document.getElementById('applyModal')).show();
        }

        document.getElementById('prev-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        document.getElementById('next-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
        document.getElementById('reset-filter').addEventListener('click', () => {
            selectedTrainingId = null;
            document.getElementById('reset-filter').classList.remove('visible');
            renderTrainingsList();
            renderPager();
            renderCalendar();
        });

        document.getElementById('applyForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('name').value;
            alert(`Thank you, ${name}! Your application for the training on ${document.getElementById('modal-date').textContent} has been submitted. We will contact you soon.`);
            bootstrap.Modal.getInstance(document.getElementById('applyModal')).hide();
            e.target.reset();
        });

        function renderLegend() {
            const wrap = document.getElementById('legend-families');
            wrap.innerHTML = '';
            // Show only families that actually appear in this year's schedule
            const seen = new Set();
            trainings.forEach(t => {
                if (seen.has(t.family)) return;
                seen.add(t.family);
                const item = document.createElement('span');
                item.className = 'legend-item';
                item.innerHTML = `<span class="swatch" style="background:${t.color};"></span>${t.family}`;
                wrap.appendChild(item);
            });
        }

        renderLegend();
        renderTrainingsList();
        renderPager();
        renderCalendar();
    </script>

</body>
</html>
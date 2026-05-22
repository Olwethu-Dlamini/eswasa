<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Training - Calendar - SWASA</title>
    <meta name="description" content="View the upcoming training calendar for SWASA. Access the prospectus and apply for courses.">
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
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Training</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Calendar</span>
                            </nav>
                            <h1 class="title">Training Calendar</h1>
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
                    <h2 class="display-6 fw-bold">Upcoming Training Sessions</h2>
                    <p class="text-muted mt-2 mb-0">Plan Your Learning Journey</p>
                    <div class="section-divider"></div>
                </div>

                <!-- Calendar Action Tabs (Prospectus / Application / E-Learning) -->
                <div class="calendar-actions text-center mb-4">
                    <a href="admin/downloads/ESWASA TRAINING PROSPECTUS 2025-26.pdf" class="prospectus-link" target="_blank">
                        <i class="fas fa-file-pdf"></i> Prospectus
                    </a>
                    <a href="qoute_training.php" class="prospectus-link">
                        <i class="fas fa-file-signature"></i> Application Form
                    </a>
                    <a href="#" class="prospectus-link is-soon" aria-disabled="true" title="Coming soon">
                        <i class="fas fa-laptop"></i> E-Learning Platform <span class="soon-badge">Coming soon</span>
                    </a>
                </div>

                <!-- Two-column layout: trainings list (middle) + calendar (side) -->
                <div class="training-layout">

                    <!-- Trainings list -->
                    <div class="trainings-col">
                        <div class="trainings-header">
                            <span class="year-label">2026 Schedule</span>
                            <button id="reset-filter" class="reset-filter" type="button">
                                <i class="fas fa-times me-1"></i> Show all
                            </button>
                        </div>
                        <div id="trainings-list"></div>
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
                                <h5 class="modal-title">Apply for Training: <span id="modal-event"></span> on <span id="modal-date"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">Please complete the form below to apply for the selected training session.</p>
                                <form id="applyForm">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="company" class="form-label">Company/Organisation</label>
                                        <input type="text" class="form-control" id="company">
                                    </div>
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position/Title</label>
                                        <input type="text" class="form-control" id="position">
                                    </div>
                                    <div class="mb-3">
                                        <label for="comments" class="form-label">Comments or Questions</label>
                                        <textarea class="form-control" id="comments" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="consentCheck" required>
                                        <label class="form-check-label" for="consentCheck">I agree to the <a href="training_about.php#policiesTabContent">Training Policies</a> and consent to the processing of my personal data as described in the prospectus.</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="ico-check3 me-2"></i>Submit Application</button>
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
        // Colour palette pulled from admin/uploads/*-colored.svg + course-*.svg
        // Each training has a family (used for the legend) and a colour (used everywhere).
        const FAMILY_COLOURS = {
            'Quality':         '#99CC00', // ISO 9001
            'Auditing':        '#455A64', // ISO 19011
            'Food Safety':     '#00B5C5', // ISO 22000 / HACCP
            'Health & Safety': '#8C66DE', // ISO 45001
            'Environmental':   '#F24B4B', // ISO 14001
            'Hazmat':          '#FFC000', // hazard / warning
            'Risk':            '#809CD0', // ISO 31000 (light blue)
            'Wellness':        '#E85D75', // wellness
            'Agriculture':     '#2E7D32'  // Global GAP
        };

        // 2026 ESWASA training schedule — each training has 1–4 weekly intakes (start → end)
        const trainings = [
            { code: 'QMS 02',  family: 'Quality',         title: 'Quality Management Systems — SZNS ISO 9001:2015 — Understanding & Implementation', sessions: [
                { start: '2026-05-18', end: '2026-05-22', label: '18–22 May' },
                { start: '2026-07-13', end: '2026-07-17', label: '13–17 July' },
                { start: '2026-10-05', end: '2026-10-09', label: '5–9 October' },
                { start: '2026-12-07', end: '2026-12-11', label: '7–11 December' }
            ]},
            { code: 'QMS 03',  family: 'Auditing',        title: 'Quality Management Systems — Internal Auditing — SZNS ISO 19011:2018', sessions: [
                { start: '2026-07-20', end: '2026-07-24', label: '20–24 July' },
                { start: '2026-09-07', end: '2026-09-11', label: '7–11 September' }
            ]},
            { code: 'FSMS 02', family: 'Food Safety',     title: 'Food Safety Management Systems — SZNS ISO 22000:2018 — Understanding & Implementation', sessions: [
                { start: '2026-06-01', end: '2026-06-05', label: '1–5 June' },
                { start: '2026-08-17', end: '2026-08-21', label: '17–21 August' },
                { start: '2026-10-26', end: '2026-10-30', label: '26–30 October' },
                { start: '2026-12-14', end: '2026-12-18', label: '14–18 December' }
            ]},
            { code: 'FSMS 03', family: 'Auditing',        title: 'Food Safety Management Systems — Internal Auditing — SZNS ISO 19011:2018', sessions: [
                { start: '2026-07-27', end: '2026-07-31', label: '27–31 July' },
                { start: '2026-11-30', end: '2026-12-04', label: '30 November – 4 December' }
            ]},
            { code: 'FS 01',   family: 'Food Safety',     title: 'Hazard Analysis & Critical Control Points (HACCP) — SZNS ISO 10330:2020 — Understanding & Implementation', sessions: [
                { start: '2026-08-03', end: '2026-08-07', label: '3–7 August' }
            ]},
            { code: 'OHS 02',  family: 'Health & Safety', title: 'Occupational Health & Safety Management Systems — SZNS ISO 45001:2018 — Understanding & Implementation', sessions: [
                { start: '2026-06-08', end: '2026-06-12', label: '8–12 June' },
                { start: '2026-08-24', end: '2026-08-28', label: '24–28 August' },
                { start: '2026-11-02', end: '2026-11-06', label: '2–6 November' }
            ]},
            { code: 'OHS 01',  family: 'Health & Safety', title: 'SHE Rep — Safety, Health and Environment Representative', sessions: [
                { start: '2026-09-21', end: '2026-09-25', label: '21–25 September' }
            ]},
            { code: 'RCA 02',  family: 'Health & Safety', title: 'Root Cause Analysis / Incident Investigation — Understanding & Implementation', sessions: [
                { start: '2026-08-10', end: '2026-08-14', label: '10–14 August' }
            ]},
            { code: 'HM 02',   family: 'Hazmat',          title: 'Hazmat — Hazardous Material — Understanding & Implementation', sessions: [
                { start: '2026-05-25', end: '2026-05-29', label: '25–29 May' },
                { start: '2026-11-09', end: '2026-11-13', label: '9–13 November' }
            ]},
            { code: 'EMS 02',  family: 'Environmental',   title: 'Environmental Management Systems — SZNS ISO 14001:2015 — Understanding & Implementation', sessions: [
                { start: '2026-06-15', end: '2026-06-19', label: '15–19 June' },
                { start: '2026-09-14', end: '2026-09-18', label: '14–18 September' },
                { start: '2026-11-23', end: '2026-11-27', label: '23–27 November' }
            ]},
            { code: 'ERM 02',  family: 'Risk',            title: 'Enterprise Risk Management — SZNS ISO 31000:2018 — Understanding & Implementation', sessions: [
                { start: '2026-06-22', end: '2026-06-26', label: '22–26 June' },
                { start: '2026-10-19', end: '2026-10-23', label: '19–23 October' }
            ]},
            { code: 'WDM 02',  family: 'Wellness',        title: 'Wellness and Disease Management Systems — SZNS SANS 16001:2013 — Understanding & Implementation', sessions: [
                { start: '2026-06-29', end: '2026-07-03', label: '29 June – 3 July' }
            ]},
            { code: 'GAP 02',  family: 'Agriculture',     title: 'Global GAP — Integrated Farm Assurance', sessions: [
                { start: '2026-07-06', end: '2026-07-10', label: '6–10 July' },
                { start: '2026-10-12', end: '2026-10-16', label: '12–16 October' }
            ]}
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
                    eventByDate[key] = { training: t, session: s };
                });
            });
        });

        // Dynamic "today" — the calendar opens on the current month and highlights today's date
        const _now = new Date();
        const today = `${_now.getFullYear()}-${String(_now.getMonth() + 1).padStart(2, '0')}-${String(_now.getDate()).padStart(2, '0')}`;
        let currentDate = new Date(_now.getFullYear(), _now.getMonth(), 1);
        let selectedTrainingId = null;

        function renderTrainingsList() {
            const list = document.getElementById('trainings-list');
            list.innerHTML = '';
            trainings.forEach(t => {
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

            // Jump calendar to the first session of the selected training
            if (jumpCalendar && selectedTrainingId !== null) {
                const t = trainings[selectedTrainingId];
                const first = t.sessions[0];
                if (first) {
                    const [y, m] = first.start.split('-').map(Number);
                    currentDate = new Date(y, m - 1, 1);
                }
            }
            renderTrainingsList();
            renderCalendar();
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

                        if (eventByDate[dateKey]) {
                            const { training, session } = eventByDate[dateKey];
                            dayDiv.classList.add('has-event');
                            dayDiv.dataset.date = dateKey;
                            // Apply the training's colour inline (dimmed class overrides via !important)
                            dayDiv.style.backgroundColor = training.color;
                            dayDiv.style.borderColor = training.color;
                            dayDiv.style.color = textOn(training.color);
                            if (selectedDates && !selectedDates.has(dateKey)) {
                                dayDiv.classList.add('dimmed');
                            }
                            dayDiv.title = `${training.code} — ${training.title} (${session.label})`;
                            dayDiv.addEventListener('click', () => openApplyModal(training, session));
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
        renderCalendar();
    </script>

</body>
</html>
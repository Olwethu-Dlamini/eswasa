<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request a Training Quote - ESWASA</title>
    <meta name="description" content="Submit a request for quotation for ESWASA training services. Separate forms for companies/organisations and individual applicants.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

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

        /* Form controls */
        .form-control, .form-select,
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="file"], textarea, select {
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.25);
            padding: 0.75rem 1rem;
            color: #2B3388;
            background: #fff;
        }
        .form-control:focus, .form-select:focus,
        input:focus, textarea:focus, select:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
            outline: none;
        }
        .form-label, label {
            font-weight: 600;
            color: #2B3388;
        }
        .form-check-input:checked {
            background-color: #2B3388;
            border-color: #2B3388;
        }
        .form-text { color: rgba(43, 51, 136, 0.7); }
        .required::after {
            content: " *";
            color: #2B3388;
        }

        /* Form sections */
        .form-section {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: rgba(43, 51, 136, 0.04);
        }
        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2B3388;
            margin-bottom: 1rem;
            border-bottom: 2px solid #2B3388;
            padding-bottom: 0.5rem;
        }

        /* Applicant-type toggle */
        .applicant-toggle {
            display: inline-flex;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            padding: 4px;
            gap: 4px;
            margin: 0 auto 32px;
        }
        .applicant-toggle button {
            background: transparent;
            border: none;
            color: #2B3388;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease;
            font-family: Arial, sans-serif;
            font-size: 0.95rem;
        }
        .applicant-toggle button:hover { background-color: rgba(43, 51, 136, 0.06); }
        .applicant-toggle button.active {
            background-color: #2B3388;
            color: #fff;
        }
        .applicant-toggle button.active:hover { background-color: #2B3388; }

        /* CTA submit button */
        .btn-cta {
            background-color: #2B3388;
            border: 1px solid #2B3388;
            color: #fff;
            border-radius: 4px;
            padding: 14px 40px;
            font-weight: 600;
            font-family: Arial, sans-serif;
            font-size: 15px;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        .btn-cta:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        .display-6 { color: #2B3388; font-weight: 700; letter-spacing: -0.01em; }
        .section-divider {
            width: 60px; height: 2px; background: #2B3388;
            margin: 16px auto 0; border-radius: 0;
        }

        .applicant-form { display: none; }
        .applicant-form.active { display: block; }

        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .form-section { padding: 1rem; }
            .form-section-title { font-size: 1.1rem; }
            .btn-cta { width: 100%; padding: 0.85rem 1rem; }
            .applicant-toggle { display: flex; width: 100%; }
            .applicant-toggle button { flex: 1; padding: 10px 16px; font-size: 0.88rem; }
            .form-check-inline { display: block; margin-right: 0; margin-bottom: 0.5rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('qoute_training', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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
                                <span property="itemListElement" typeof="ListItem">Request a Quote</span>
                            </nav>
                            <h1 class="title">Request a Training Quote</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5 text-center">
                <h2 class="display-6 fw-bold">Request a Training Quote</h2>
                <p class="text-muted mt-2 mb-0">Get a personalised quotation for ESWASA training services</p>
                <div class="section-divider"></div>
                <p class="text-muted mt-4 mb-0">
                    Choose whether you are applying as a <strong>company / organisation</strong> or as an <strong>individual</strong>, then complete the relevant form below.
                    Bookings for in-house and customised training start at a <strong>minimum of 5 participants</strong>; scheduled courses run when at least <strong>5 delegates</strong> have paid.
                </p>
            </div>

            <!-- Applicant Type Toggle -->
            <div class="text-center">
                <div class="applicant-toggle" role="tablist" aria-label="Applicant type">
                    <button type="button" id="toggle-company" class="active" data-target="form-company" aria-selected="true" role="tab">Company / Organisation</button>
                    <button type="button" id="toggle-individual" data-target="form-individual" aria-selected="false" role="tab">Individual</button>
                </div>
            </div>

            <!-- ============ COMPANY / ORGANISATION FORM ============ -->
            <div class="row justify-content-center applicant-form active" id="form-company" role="tabpanel" aria-labelledby="toggle-company">
                <div class="col-lg-10">
                    <form id="companyRfqForm" action="process_quote.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="quote_type" value="training_company">

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Contact Information</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_companyName" class="form-label required">Company Name</label>
                                        <input type="text" class="form-control" id="c_companyName" name="company_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_tin" class="form-label">Company Tax Identification Number (TIN)</label>
                                        <input type="text" class="form-control" id="c_tin" name="company_tin">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_contactPerson" class="form-label required">Contact Person</label>
                                        <input type="text" class="form-control" id="c_contactPerson" name="contact_person" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_email" class="form-label required">Email Address</label>
                                        <input type="email" class="form-control" id="c_email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_phone" class="form-label required">Phone Number</label>
                                        <input type="tel" class="form-control" id="c_phone" name="phone" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_postal" class="form-label required">Postal Address</label>
                                        <input type="text" class="form-control" id="c_postal" name="postal_address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="c_physical" class="form-label required">Physical Address</label>
                                <textarea class="form-control" id="c_physical" name="physical_address" rows="2" required></textarea>
                            </div>
                        </div>

                        <!-- Training Requirements -->
                        <div class="form-section">
                            <h3 class="form-section-title">Training Requirements</h3>
                            <div class="mb-3">
                                <label class="form-label required">Type of Training</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="c_typeScheduled" value="scheduled" checked>
                                        <label class="form-check-label" for="c_typeScheduled">Scheduled Training</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="c_typeInhouse" value="inhouse">
                                        <label class="form-check-label" for="c_typeInhouse">In-house Training</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="c_typeCustom" value="customised">
                                        <label class="form-check-label" for="c_typeCustom">Customised Training</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="c_typeOther" value="other">
                                        <label class="form-check-label" for="c_typeOther">Other</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_courseName" class="form-label required">Course Name</label>
                                        <select class="form-select" id="c_courseName" name="course_name" required>
                                            <option value="" disabled selected>Select a course</option>
                                            <option>QMS 02 — Quality Management Systems (SZNS ISO 9001:2015)</option>
                                            <option>QMS 03 — QMS Internal Auditing (SZNS ISO 19011:2018)</option>
                                            <option>FSMS 02 — Food Safety Management Systems (SZNS ISO 22000:2018)</option>
                                            <option>FSMS 03 — FSMS Internal Auditing (SZNS ISO 19011:2018)</option>
                                            <option>FS 01 — HACCP (SZNS ISO 10330:2020)</option>
                                            <option>OHS 02 — Occupational Health &amp; Safety (SZNS ISO 45001:2018)</option>
                                            <option>OHS 01 — SHE Representative</option>
                                            <option>RCA 02 — Root Cause Analysis / Incident Investigation</option>
                                            <option>HM 02 — Hazardous Material (Hazmat)</option>
                                            <option>EMS 02 — Environmental Management Systems (SZNS ISO 14001:2015)</option>
                                            <option>ERM 02 — Enterprise Risk Management (SZNS ISO 31000:2018)</option>
                                            <option>WDM 02 — Wellness &amp; Disease Management (SZNS SANS 16001:2013)</option>
                                            <option>GAP 02 — Global GAP — Integrated Farm Assurance</option>
                                            <option>Other (specify in scope)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Course Type</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="c_courseAware" value="awareness" checked>
                                                <label class="form-check-label" for="c_courseAware">Awareness</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="c_courseImpl" value="implementation">
                                                <label class="form-check-label" for="c_courseImpl">Understanding &amp; Implementation</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="c_courseAudit" value="auditing">
                                                <label class="form-check-label" for="c_courseAudit">Auditing</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_numParticipants" class="form-label required">Number of Participants</label>
                                        <input type="number" class="form-control" id="c_numParticipants" name="num_participants" min="1" value="1" required>
                                        <div class="form-text" id="c_participantsHint">Scheduled / other: minimum 1. In-house or customised: minimum 5.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="c_preferredDates" class="form-label">Preferred Dates <span class="text-muted" style="font-weight:400;">(if in-house / customised)</span></label>
                                        <input type="text" class="form-control" id="c_preferredDates" name="preferred_dates" placeholder="e.g., Week of 10 March 2026">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="c_scope" class="form-label">Scope of Training / Detailed Requirements</label>
                                <textarea class="form-control" id="c_scope" name="scope" rows="3" placeholder="Describe the training needs, objectives, industry specifics, etc."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Venue Preference</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="venue_preference" id="c_venueEswasa" value="eswasa" checked>
                                        <label class="form-check-label" for="c_venueEswasa">ESWASA Premises</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="venue_preference" id="c_venueInhouse" value="inhouse">
                                        <label class="form-check-label" for="c_venueInhouse">In-House (your premises)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="venue_preference" id="c_venueOther" value="other">
                                        <label class="form-check-label" for="c_venueOther">Other (specify in comments)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Additional Information</h3>
                            <div class="mb-3">
                                <label for="c_attachments" class="form-label">Upload Supporting Documents (Optional)</label>
                                <input type="file" class="form-control" id="c_attachments" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                <div class="form-text">e.g., Company profile, specific requirements, previous training certificates, Trading Licence.</div>
                            </div>
                            <div class="mb-3">
                                <label for="c_comments" class="form-label">Comments or Questions</label>
                                <textarea class="form-control" id="c_comments" name="comments" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn-cta">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ============ INDIVIDUAL FORM ============ -->
            <div class="row justify-content-center applicant-form" id="form-individual" role="tabpanel" aria-labelledby="toggle-individual">
                <div class="col-lg-10">
                    <form id="individualRfqForm" action="process_quote.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="quote_type" value="training_individual">

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Contact Information</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_fullNames" class="form-label required">Full Names</label>
                                        <input type="text" class="form-control" id="i_fullNames" name="full_names" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_studentNo" class="form-label">Student Number</label>
                                        <input type="text" class="form-control" id="i_studentNo" name="student_number" placeholder="If applicable">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_email" class="form-label required">Email Address</label>
                                        <input type="email" class="form-control" id="i_email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_phone" class="form-label required">Phone Number</label>
                                        <input type="tel" class="form-control" id="i_phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_postal" class="form-label required">Postal Address</label>
                                        <input type="text" class="form-control" id="i_postal" name="postal_address" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_physical" class="form-label required">Physical Address</label>
                                        <input type="text" class="form-control" id="i_physical" name="physical_address" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Training Requirements -->
                        <div class="form-section">
                            <h3 class="form-section-title">Training Requirements</h3>
                            <div class="mb-3">
                                <label class="form-label required">Type of Training</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="i_typeScheduled" value="scheduled" checked>
                                        <label class="form-check-label" for="i_typeScheduled">Scheduled Training</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="i_typeCustom" value="customised">
                                        <label class="form-check-label" for="i_typeCustom">Customised Training</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="training_type" id="i_typeOther" value="other">
                                        <label class="form-check-label" for="i_typeOther">Other</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_courseName" class="form-label required">Course Name</label>
                                        <select class="form-select" id="i_courseName" name="course_name" required>
                                            <option value="" disabled selected>Select a course</option>
                                            <option>QMS 02 — Quality Management Systems (SZNS ISO 9001:2015)</option>
                                            <option>QMS 03 — QMS Internal Auditing (SZNS ISO 19011:2018)</option>
                                            <option>FSMS 02 — Food Safety Management Systems (SZNS ISO 22000:2018)</option>
                                            <option>FSMS 03 — FSMS Internal Auditing (SZNS ISO 19011:2018)</option>
                                            <option>FS 01 — HACCP (SZNS ISO 10330:2020)</option>
                                            <option>OHS 02 — Occupational Health &amp; Safety (SZNS ISO 45001:2018)</option>
                                            <option>OHS 01 — SHE Representative</option>
                                            <option>RCA 02 — Root Cause Analysis / Incident Investigation</option>
                                            <option>HM 02 — Hazardous Material (Hazmat)</option>
                                            <option>EMS 02 — Environmental Management Systems (SZNS ISO 14001:2015)</option>
                                            <option>ERM 02 — Enterprise Risk Management (SZNS ISO 31000:2018)</option>
                                            <option>WDM 02 — Wellness &amp; Disease Management (SZNS SANS 16001:2013)</option>
                                            <option>GAP 02 — Global GAP — Integrated Farm Assurance</option>
                                            <option>Other (specify in scope)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Course Type</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="i_courseAware" value="awareness" checked>
                                                <label class="form-check-label" for="i_courseAware">Awareness</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="i_courseImpl" value="implementation">
                                                <label class="form-check-label" for="i_courseImpl">Understanding &amp; Implementation</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="course_type" id="i_courseAudit" value="auditing">
                                                <label class="form-check-label" for="i_courseAudit">Auditing</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_numParticipants" class="form-label required">Number of Participants</label>
                                        <input type="number" class="form-control" id="i_numParticipants" name="num_participants" min="1" value="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="i_preferredDates" class="form-label required">Preferred Dates</label>
                                        <input type="text" class="form-control" id="i_preferredDates" name="preferred_dates" placeholder="e.g., Week of 10 March 2026" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="i_scope" class="form-label">Scope of Training / Detailed Requirements</label>
                                <textarea class="form-control" id="i_scope" name="scope" rows="3" placeholder="Describe what you want to learn, learning objectives, prior experience, etc."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Venue Preference</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="venue_preference" id="i_venueEswasa" value="eswasa" checked>
                                        <label class="form-check-label" for="i_venueEswasa">ESWASA Premises</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="venue_preference" id="i_venueOther" value="other">
                                        <label class="form-check-label" for="i_venueOther">Other (specify in comments)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Additional Information</h3>
                            <div class="mb-3">
                                <label for="i_attachments" class="form-label">Upload Supporting Documents (Optional)</label>
                                <input type="file" class="form-control" id="i_attachments" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                <div class="form-text">e.g., Specific requirements, previous training certificates, student verification documents.</div>
                            </div>
                            <div class="mb-3">
                                <label for="i_comments" class="form-label">Comments or Questions</label>
                                <textarea class="form-control" id="i_comments" name="comments" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn-cta">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

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
        // Applicant-type toggle
        const toggleBtns = document.querySelectorAll('.applicant-toggle button');
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                toggleBtns.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); });
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
                document.querySelectorAll('.applicant-form').forEach(f => f.classList.remove('active'));
                document.getElementById(btn.dataset.target).classList.add('active');
            });
        });

        // Company form: hint + min validation that adapts to training type
        const companyType = document.querySelectorAll('input[name="training_type"]');
        const companyNumInput = document.getElementById('c_numParticipants');
        const companyHint = document.getElementById('c_participantsHint');
        companyType.forEach(r => {
            r.addEventListener('change', () => {
                if (r.checked && (r.value === 'inhouse' || r.value === 'customised')) {
                    companyNumInput.min = 5;
                    if (parseInt(companyNumInput.value, 10) < 5) companyNumInput.value = 5;
                    companyHint.innerHTML = '<strong>In-house / customised:</strong> minimum 5 delegates.';
                } else if (r.checked) {
                    companyNumInput.min = 1;
                    companyHint.innerHTML = 'Scheduled / other: minimum 1. In-house or customised: minimum 5.';
                }
            });
        });

        // Light client-side validation on both forms
        ['companyRfqForm', 'individualRfqForm'].forEach(id => {
            const form = document.getElementById(id);
            if (!form) return;
            form.addEventListener('submit', e => {
                const email = form.querySelector('input[type="email"]').value;
                const phone = form.querySelector('input[type="tel"]').value;
                if (!/^\S+@\S+\.\S+$/.test(email)) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
                if (phone.replace(/\D/g, '').length < 8) {
                    e.preventDefault();
                    alert('Please enter a valid phone number (at least 8 digits).');
                    return false;
                }
            });
        });
    </script>

</body>
</html>

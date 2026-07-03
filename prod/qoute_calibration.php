<?php
require_once __DIR__ . '/includes/env.php';
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request a Calibration Quote - ESWASA</title>
    <meta name="description" content="Submit a request for quotation for ESWASA calibration and metrology services. Formal quotation issued within 2 working days.">
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

        /* Form controls */
        .form-control, .form-select,
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"], input[type="file"], textarea, select {
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
        .form-check-label { font-weight: 400; }
        .form-text { color: #2B3388; }
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

        /* SLA banner */
        .sla-banner {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(43, 51, 136, 0.06);
            border: 1px solid rgba(43, 51, 136, 0.20);
            border-left: 3px solid #2B3388;
            color: #2B3388;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 10px 16px;
            border-radius: 4px;
            margin-top: 18px;
        }
        .sla-banner i { color: #2B3388; }

        /* Primary buttons */
        .btn-primary {
            background-color: #2B3388;
            border-color: #2B3388;
            color: #fff;
            font-weight: 600;
            padding: 12px 32px;
            border-radius: 4px;
        }
        .btn-primary:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
            box-shadow: 0 2px 6px rgba(43, 51, 136, 0.18);
        }

        /* CTA submit button (inline in markup) */
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
        }
        .btn-cta:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Section title — canonical pattern */
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }
        .text-gradient-primary { color: #2B3388 !important; background: none !important; -webkit-background-clip: initial !important; -webkit-text-fill-color: initial !important; }
        .bg-primary { background-color: #2B3388 !important; }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .form-section { padding: 1rem; }
            .form-section-title { font-size: 1.1rem; }
            .btn-cta, .btn-primary { width: 100%; padding: 0.85rem 1rem; }
            .form-check-inline { display: block; margin-right: 0; margin-bottom: 0.5rem; }
            .sla-banner { font-size: 0.88rem; padding: 8px 12px; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('qoute_calibration', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Calibration</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Request a Quote</span>
                            </nav>
                            <h1 class="title">Request a Calibration Quote</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5 text-center">
                <h2 class="display-6 fw-bold">Request a Calibration Quote</h2>
                <p class="text-muted mt-2 mb-0">Get a formal quotation for ESWASA calibration and metrology services</p>
                <div class="section-divider"></div>
                <p class="text-muted mt-4 mb-0">
                    Provide details about your instruments and requirements. ESWASA will assess your request and issue a formal quotation within 2 working days.
                </p>
                <div class="sla-banner">
                    <i class="fas fa-clock"></i>
                    <span>Formal quotation issued within 2 working days</span>
                </div>
            </div>

            <!-- Request for Calibration Quotation Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="calQuoteForm" action="process_quote.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="quote_type" value="calibration">

                        <!-- Organisation Details -->
                        <div class="form-section">
                            <h3 class="form-section-title">Organisation Details</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="organisationName" class="form-label required">Organisation Name</label>
                                        <input type="text" class="form-control" id="organisationName" name="organisation_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="regNo" class="form-label">Company Registration Number</label>
                                        <input type="text" class="form-control" id="regNo" name="reg_no">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contactPerson" class="form-label required">Contact Person</label>
                                        <input type="text" class="form-control" id="contactPerson" name="contact_person" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position</label>
                                        <input type="text" class="form-control" id="position" name="position">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label required">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label required">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label required">Physical Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Include town/region (e.g., Matsapha, Manzini)" required></textarea>
                            </div>
                        </div>

                        <!-- Instruments to be Calibrated -->
                        <div class="form-section">
                            <h3 class="form-section-title">Instruments to be Calibrated</h3>

                            <div class="mb-3">
                                <label for="instrumentType" class="form-label required">Type of Instrument</label>
                                <input type="text" class="form-control" id="instrumentType" name="instrument_type" placeholder="e.g., Weighbridge, electronic balance, pressure gauge, thermometer" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="capacityRange" class="form-label">Capacity / Range</label>
                                        <input type="text" class="form-control" id="capacityRange" name="capacity_range" placeholder="e.g., 30 kg, 0–100°C, 0–10 MPa">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="accuracyClass" class="form-label">Accuracy Class / Tolerance</label>
                                        <input type="text" class="form-control" id="accuracyClass" name="accuracy_class" placeholder="e.g., Class III, ±0.5%, ±1°C">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="numInstruments" class="form-label required">Number of Instruments</label>
                                        <input type="number" class="form-control" id="numInstruments" name="num_instruments" min="1" value="1" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="makeModel" class="form-label">Make and Model (if known)</label>
                                <input type="text" class="form-control" id="makeModel" name="make_model" placeholder="e.g., Mettler Toledo PL3002, Avery Berkel XT3000">
                            </div>

                            <div class="mb-3">
                                <label for="purpose" class="form-label required">Purpose of Calibration</label>
                                <select class="form-select" id="purpose" name="purpose" required>
                                    <option value="" disabled selected>Select purpose</option>
                                    <option value="iso_compliance">ISO / management system compliance</option>
                                    <option value="regulatory">Regulatory compliance</option>
                                    <option value="trade_legal">Legal / trade metrology</option>
                                    <option value="commissioning">Equipment commissioning</option>
                                    <option value="periodic">Periodic / scheduled re-calibration</option>
                                    <option value="post_repair">Post-repair verification</option>
                                    <option value="research">Research / laboratory</option>
                                    <option value="other">Other (describe in Additional Information)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Service Type</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="service_type" id="serviceLab" value="laboratory">
                                        <label class="form-check-label" for="serviceLab">Laboratory Calibration (Bring to Matsapha)</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="service_type" id="serviceOnsite" value="onsite" checked>
                                        <label class="form-check-label" for="serviceOnsite">On-site Calibration (Field visit required)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logistics and Requirements -->
                        <div class="form-section">
                            <h3 class="form-section-title">Logistics and Requirements</h3>
                            <div class="mb-3">
                                <label for="completionDate" class="form-label">Required Completion Date</label>
                                <input type="text" class="form-control" id="completionDate" name="completion_date" placeholder="e.g., Before 15 June 2025">
                            </div>
                            <div class="mb-3">
                                <label for="prevCert" class="form-label">Previous Calibration Certificate (Optional)</label>
                                <input type="text" class="form-control" id="prevCert" name="previous_certificate" placeholder="Reference number or expiry date">
                            </div>
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Upload Documents (Optional)</label>
                                <input type="file" class="form-control" id="attachments" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                <div class="form-text">e.g., User manual, previous certificate, calibration procedure</div>
                            </div>
                            <div class="mb-3">
                                <label for="additionalInfo" class="form-label">Additional Information</label>
                                <textarea class="form-control" id="additionalInfo" name="additional_info" rows="3" placeholder="e.g., Access restrictions, special handling requirements"></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn-cta" style="cursor:pointer;">
                                Submit Request for Calibration Quote
                            </button>
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
        document.getElementById('calQuoteForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;

            if (!/^\S+@\S+\.\S+$/.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return false;
            }

            if (phone.length < 8) {
                e.preventDefault();
                alert('Please enter a valid phone number (minimum 8 digits).');
                return false;
            }
        });
    </script>

</body>
</html>

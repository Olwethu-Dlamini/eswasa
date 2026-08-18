<?php
// Start the session before any output: the quote result banner further down
// reads attachment-rejection messages left in the session by
// process_quote.php, and session_start() cannot run once headers are sent.
// See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A3.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/includes/env.php';
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request a Certification Quote - ESWASA</title>
    <meta name="description" content="Submit a request for quotation for ESWASA certification services including Management Systems, Product, and Ingelo Quality Mark.">
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
        input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea, select {
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
        .btn-primary:focus {
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.35);
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

        /* Enlarged submit button */
        .btn-submit-quote {
            font-size: 1.2rem;
            padding: 16px 50px !important;
            margin-top: 20px;
        }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .form-section { padding: 1rem; }
            .form-section-title { font-size: 1.1rem; }
            .btn-cta, .btn-primary { width: 100%; padding: 0.85rem 1rem; }
            .form-check-inline { display: block; margin-right: 0; margin-bottom: 0.5rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('qoute_certification', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Certification</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Request a Quote</span>
                            </nav>
                            <h1 class="title">Request a Certification Quote</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <?php /* Submission outcome. Without this the page redirected back
               with ?quote_sent=1 and showed the visitor nothing at all.
               See spec item A3. */ ?>
        <?php include __DIR__ . '/includes/quote_result_banner.php'; ?>


        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5 text-center">
                <h2 class="display-6 fw-bold">Request a Certification Quote</h2>
                <p class="text-muted mt-2 mb-0">Get a formal quotation for ESWASA certification services</p>
                <div class="section-divider"></div>
                <p class="text-muted mt-4 mb-0">
                    Provide details about your certification needs. ESWASA will assess your request and issue a formal quotation within 5 working days.
                </p>
            </div>

            <!-- Request for Quotation Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="certQuoteForm" action="process_quote.php" method="POST" enctype="multipart/form-data">
                        <?php /* Explicit source tag. process_quote.php otherwise has to
                               guess from HTTP_REFERER, which browsers and privacy
                               settings can strip — a stripped referrer files the
                               request under "other", where no inbox showed it.
                               See spec item A3. */ ?>
                        <input type="hidden" name="quote_source" value="certification">
                        <!-- Contact Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Organisation Details</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="companyName" class="form-label required">Organisation Name</label>
                                        <input type="text" class="form-control" id="companyName" name="organisation_name" required>
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
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Include region/tinkhundla if applicable" required></textarea>
                            </div>
                        </div>

                        <!-- Certification Requirements -->
                        <div class="form-section">
                            <h3 class="form-section-title">Certification Request</h3>
                            <div class="mb-3">
                                <label for="certType" class="form-label required">Type of Certification</label>
                                <select class="form-select" id="certType" name="certification_type" required>
                                    <option value="" disabled selected>Select certification type</option>
                                    <option value="management_systems">Management Systems (e.g., ISO 9001, ISO 14001)</option>
                                    <option value="product">Product Certification (e.g., electrical, food, building materials)</option>
                                    <option value="ingelo">Ingelo Quality Mark (Locally manufactured goods)</option>
                                    <option value="combined">Combined (e.g., ISO + Product)</option>
                                </select>
                            </div>

                            <div class="mb-3" id="standardsField">
                                <label for="standards" class="form-label">Applicable Standards (if known)</label>
                                <input type="text" class="form-control" id="standards" name="standards" placeholder="e.g., ISO 9001:2015, SANS 1853, FSSC 22000">
                            </div>

                            <div class="mb-3">
                                <label for="scope" class="form-label required">Scope of Certification</label>
                                <textarea class="form-control" id="scope" name="scope" rows="3" placeholder="Describe products, processes, locations, or departments to be certified" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="employees" class="form-label">Number of Employees</label>
                                        <input type="number" class="form-control" id="employees" name="employees" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sites" class="form-label">Number of Sites/Locations</label>
                                        <input type="number" class="form-control" id="sites" name="sites" min="1" value="1">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Is your organisation based in Eswatini?</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="local" id="localYes" value="yes" checked>
                                        <label class="form-check-label" for="localYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="local" id="localNo" value="no">
                                        <label class="form-check-label" for="localNo">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="localManufacturerField" style="display:none;">
                                <label class="form-label">For Ingelo: Is the product manufactured in Eswatini?</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="local_manufacturer" id="manuYes" value="yes">
                                        <label class="form-check-label" for="manuYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="local_manufacturer" id="manuNo" value="no">
                                        <label class="form-check-label" for="manuNo">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="form-section">
                            <h3 class="form-section-title">Supporting Information</h3>
                            <div class="mb-3">
                                <label for="existingCert" class="form-label">Existing Certifications (if any)</label>
                                <input type="text" class="form-control" id="existingCert" name="existing_certifications" placeholder="e.g., ISO 9001 (expired), SABS certified">
                            </div>
                            <div class="mb-3">
                                <label for="timeline" class="form-label">Desired Certification Timeline</label>
                                <input type="text" class="form-control" id="timeline" name="timeline" placeholder="e.g., By end of Q2 2025">
                            </div>
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Upload Documents (Optional)</label>
                                <input type="file" class="form-control" id="attachments" name="documents[]" multiple accept="application/pdf,.pdf">
                                <div class="form-text">e.g., Process flowcharts, product specs, previous audit reports <strong>PDF files only, up to 10&nbsp;MB each, maximum 5 files.</strong></div>
                            </div>
                            <div class="mb-3">
                                <label for="comments" class="form-label">Additional Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn-cta" style="cursor:pointer;">
                                Submit Request for Quotation
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
        // Show/hide local manufacturer field when Ingelo is selected
        document.getElementById('certType').addEventListener('change', function() {
            const localManuField = document.getElementById('localManufacturerField');
            if (this.value === 'ingelo') {
                localManuField.style.display = 'block';
            } else {
                localManuField.style.display = 'none';
            }
        });

        // Basic form validation (enhance with server-side)
        document.getElementById('certQuoteForm').addEventListener('submit', function(e) {
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
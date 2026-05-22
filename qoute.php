<?php include_once 'includes/db_connect.php'; include_once 'includes/breadcrumb_helper.php'; ?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request a Quote - ESWASA</title>
    <meta name="description" content="Submit a request for quotation for ESWASA services including certification, testing, calibration, standards, and training.">
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

        /* Primary buttons */
        .btn-primary {
            background-color: #2B3388;
            border-color: #2B3388;
            color: #fff;
            border-radius: 4px;
        }
        .btn-primary:hover {
            background-color: rgba(43, 51, 136, 0.85);
            border-color: rgba(43, 51, 136, 0.85);
            color: #fff;
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.35);
        }

        /* Section title — canonical pattern (matches training-calendar, training-about, board, etc.) */
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
        /* Legacy gradient class — flatten if anything still uses it */
        .text-gradient-primary { color: #2B3388 !important; background: none !important; -webkit-background-clip: initial !important; -webkit-text-fill-color: initial !important; }
        .bg-primary { background-color: #2B3388 !important; }

        /* Mobile responsive */
        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .form-section { padding: 1rem; }
            .form-section-title { font-size: 1.1rem; }
            .btn-primary.btn-lg { width: 100%; padding: 0.85rem 1rem; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('qoute', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Request a Quote</span>
                            </nav>
                            <h1 class="title">Request a Quote</h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5 text-center">
                <h2 class="display-6 fw-bold">Request a Quote</h2>
                <p class="text-muted mt-2 mb-0">Get a personalized quotation for our services</p>
                <div class="section-divider"></div>
                <p class="text-muted mt-4 mb-0">
                    Please fill out the form below with details about the services you require. We will provide you with a detailed quotation based on your specifications.
                </p>
            </div>

            <!-- Request for Quotation Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="rfqForm" action="#" method="POST"> <!-- Replace # with actual form processing script -->
                        <!-- Contact Information Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Contact Information</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="companyName" class="form-label required">Company Name</label>
                                        <input type="text" class="form-control" id="companyName" name="companyName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contactPerson" class="form-label required">Contact Person</label>
                                        <input type="text" class="form-control" id="contactPerson" name="contactPerson" required>
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
                                <label for="address" class="form-label required">Company Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Service Requirements Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Service Requirements</h3>
                            <div class="mb-3">
                                <label for="serviceType" class="form-label required">Type of Service Required</label>
                                <select class="form-select" id="serviceType" name="serviceType" required>
                                    <option value="" disabled selected>Select a service...</option>
                                    <option value="certification">Certification</option>
                                    <option value="product_testing">Product Testing</option>
                                    <option value="calibration">Calibration Services</option>
                                    <option value="standards_development">Standards Development</option>
                                    <option value="standards_sales">Standards Sales</option>
                                    <option value="training">Training Academy</option>
                                    <option value="technical_assistance">Technical Assistance</option>
                                    <option value="information_centre">Information Centre Access</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="specificStandard" class="form-label">Specific Standard/Requirement (if applicable)</label>
                                <input type="text" class="form-control" id="specificStandard" name="specificStandard">
                                <div class="form-text">e.g., ISO 9001, HACCP, Specific Product Standard</div>
                            </div>
                            <div class="mb-3">
                                <label for="scopeOfWork" class="form-label required">Scope of Work / Detailed Requirements</label>
                                <textarea class="form-control" id="scopeOfWork" name="scopeOfWork" rows="4" placeholder="Please describe the specific work needed, desired timeline, number of locations, etc." required></textarea>
                            </div>
                             <div class="mb-3">
                                <label for="estimatedTimeline" class="form-label">Estimated Timeline</label>
                                <input type="text" class="form-control" id="estimatedTimeline" name="estimatedTimeline" placeholder="e.g., Q1 2025, Within 3 months">
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Additional Information</h3>
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Upload Supporting Documents (Optional)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments" multiple>
                                <div class="form-text">e.g., Technical specifications, drawings, previous certificates.</div>
                            </div>
                            <div class="mb-3">
                                <label for="comments" class="form-label">Comments or Questions</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Any other details or questions?"></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3">Submit Request</button>
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
        // Example: Basic form submission handling (replace with actual logic)
        document.getElementById('rfqForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission for now

            // Example: Collect form data
            const formData = new FormData(this);
            const serviceType = formData.get('serviceType');
            const contactPerson = formData.get('contactPerson');

            // Example: Show a confirmation message
            alert(`Thank you, ${contactPerson}! Your request for a quotation for "${serviceType}" has been received. We will contact you soon.`);

            // Example: Reset the form after successful submission (if not handled by server)
            // this.reset();
        });
    </script>

</body>
</html>
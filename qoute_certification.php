<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request Certification Quote - ESWASA</title>
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
    
    <style>
        .form-control, .form-select {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2E3191;
            box-shadow: 0 0 0 0.2rem rgba(46, 49, 145, 0.25);
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .required::after {
            content: " *";
            color: #dc3545;
        }
        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #f8f9fa;
        }
        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2E3191;
            margin-bottom: 1rem;
            border-bottom: 2px solid #2E3191;
            padding-bottom: 0.5rem;
        }
        .btn-primary {
            background-color: #2E3191;
            border-color: #2E3191;
            font-weight: 600;
            padding: 12px 32px;
        }
        .btn-primary:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(46, 49, 145, 0.15);
        }
        .text-gradient-primary {
            background: linear-gradient(45deg, #2E3191, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        /* Enlarged submit button */
        .btn-submit-quote {
            font-size: 1.2rem;
            padding: 16px 50px !important;
            margin-top: 20px;
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
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
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
                                <span property="itemListElement" typeof="ListItem">Request Certification Quote</span>
                            </nav>
                            <h3 class="title">Request Certification Quote</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5">
                <h2 class="display-6 fw-bold text-center">
                    Request Certification Quote
                    <span class="d-block fs-5 text-muted mt-2">Get a formal quotation for ESWASA certification services</span>
                    <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
                </h2>
                <p class="text-muted text-center mt-4">
                    Provide details about your certification needs. ESWASA will assess your request and issue a formal quotation within 5 working days.
                </p>
            </div>

            <!-- Request for Quotation Form -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <form id="certQuoteForm" action="process_quote.php" method="POST" enctype="multipart/form-data">
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
                                <input type="file" class="form-control" id="attachments" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                <div class="form-text">e.g., Process flowcharts, product specs, previous audit reports</div>
                            </div>
                            <div class="mb-3">
                                <label for="comments" class="form-label">Additional Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-submit-quote">
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
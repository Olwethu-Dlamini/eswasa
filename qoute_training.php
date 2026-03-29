<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Request for Training Quotation - SWASA</title>
    <meta name="description" content="Submit a request for quotation for SWASA training services.">
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
        .form-control, .form-select {
            border-radius: 0.375rem; /* Match Bootstrap's default rounded corners */
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2E3191; /* Use primary color for focus */
            box-shadow: 0 0 0 0.2rem rgba(46, 49, 145, 0.25); /* Focus shadow */
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .required::after {
            content: " *";
            color: #dc3545; /* Bootstrap danger color for required fields */
        }
        .form-section {
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background-color: #f8f9fa; /* Light background for sections */
        }
        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2E3191; /* Primary color */
            margin-bottom: 1rem;
            border-bottom: 2px solid #2E3191;
            padding-bottom: 0.5rem;
        }
        .btn-primary {
            background-color: #2E3191; /* Use primary color */
            border-color: #2E3191;
        }
        .btn-primary:hover {
            background-color: #1a1f71; /* Darker shade on hover */
            border-color: #1a1f71;
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(46, 49, 145, 0.5); /* Focus shadow */
        }
        .text-gradient-primary {
            background: linear-gradient(45deg, #2E3191, #00c6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Training</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Request for Quotation</span>
                            </nav>
                            <h3 class="title">Request for Training Quotation</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <div class="container py-5">
            <!-- Section Header -->
            <div class="main_title centered upper mb-5">
                <h2 class="display-6 fw-bold text-center text-gradient-primary">
                    Request a Training Quote
                    <span class="d-block fs-5 text-muted mt-2">Get a personalized quotation for our training services</span>
                    <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
                </h2>
                <p class="text-muted text-center mt-4">
                    Please fill out the form below with details about the training you require. We will provide you with a detailed quotation based on your specifications. For in-house training, note that it is available for a minimum of 5 and maximum of 20 participants.
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
                                        <label for="companyName" class="form-label required">Company/Organization Name</label>
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

                        <!-- Training Requirements Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Training Requirements</h3>
                            <div class="mb-3">
                                <label for="trainingType" class="form-label required">Type of Training Required</label>
                                <select class="form-select" id="trainingType" name="trainingType" required>
                                    <option value="" disabled selected>Select a training type...</option>
                                    <option value="in-house">In-House Training</option>
                                    <option value="public">Public Training Course</option>
                                    <option value="customized">Customized Training</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="courseName" class="form-label required">Course Name/Standard</label>
                                <input type="text" class="form-control" id="courseName" name="courseName" placeholder="e.g., SZNS ISO 9001:2015 Quality Management Systems" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numParticipants" class="form-label required">Number of Participants</label>
                                        <input type="number" class="form-control" id="numParticipants" name="numParticipants" min="5" max="20" placeholder="Min 5, Max 20 for in-house" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="preferredDates" class="form-label">Preferred Dates</label>
                                        <input type="text" class="form-control" id="preferredDates" name="preferredDates" placeholder="e.g., Week of March 10, 2025">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="scopeOfTraining" class="form-label required">Scope of Training / Detailed Requirements</label>
                                <textarea class="form-control" id="scopeOfTraining" name="scopeOfTraining" rows="4" placeholder="Describe the training needs, objectives, industry specifics, etc." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="venuePreference" class="form-label">Venue Preference</label>
                                <select class="form-select" id="venuePreference" name="venuePreference">
                                    <option value="" disabled selected>Select venue...</option>
                                    <option value="eswasa">ESWASA Premises</option>
                                    <option value="in-house">In-House (Your Premises)</option>
                                    <option value="other">Other (Specify in Comments)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Information Section -->
                        <div class="form-section">
                            <h3 class="form-section-title">Additional Information</h3>
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Upload Supporting Documents (Optional)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments" multiple>
                                <div class="form-text">e.g., Company profile, specific requirements, previous training certificates.</div>
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
            const contactPerson = formData.get('contactPerson');
            const trainingType = formData.get('trainingType');

            // Example: Show a confirmation message
            alert(`Thank you, ${contactPerson}! Your request for a quotation for "${trainingType}" training has been received. We will contact you soon.`);

            // Example: Reset the form after successful submission (if not handled by server)
            // this.reset();
        });
    </script>

</body>
</html>
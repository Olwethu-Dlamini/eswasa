<?php
include_once 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
// Handle form submission at the very top
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    $error = '';
    
    // Validate
    if (!$name || !$email || !$phone || !$subject || !$message) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (!preg_match('/^[0-9+\s\-\(\)]{10,}$/', $phone)) {
        $error = 'Invalid phone number.';
    } else {
        // Save to database
        require_once 'includes/db_connect.php';
        
        $stmt = $conn->prepare("INSERT INTO eswasa_contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            // Send email
            $to = 'info@swasa.co.sz';
            $email_subject = "New Contact Form Submission: $subject";
            $email_body = "
                <h3>New Message from ESWASA Website</h3>
                <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <p><strong>Message:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
                <hr>
                <p><em>Sent on: " . date('F j, Y \a\t g:i A') . "</em></p>
            ";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: " . htmlspecialchars($name) . " <" . htmlspecialchars($email) . ">\r\n";
            
            mail($to, $email_subject, $email_body, $headers);
            
            // ✅ Redirect to prevent resubmission
            header("Location: contact.php?success=1");
            exit;
        } else {
            $error = 'Database error. Please try again later.';
        }
        $stmt->close();
    }
    
    // On error: store in session and redirect
    if ($error) {
        session_start();
        $_SESSION['contact_error'] = $error;
        $_SESSION['contact_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ];
        header("Location: contact.php");
        exit;
    }
}

// Retrieve success/error messages
$success = isset($_GET['success']);
$error = '';
$prefill = [];

if (isset($_SESSION['contact_error'])) {
    $error = $_SESSION['contact_error'];
    $prefill = $_SESSION['contact_data'] ?? [];
    unset($_SESSION['contact_error']);
    unset($_SESSION['contact_data']);
}
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Contact Us - Eswatini Standards Authority (ESWASA)</title>
    <meta name="description" content="Get in touch with the Eswatini Standards Authority. Reach our Head Office in Matsapha or contact us for standards, certification and metrology enquiries.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
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

        /* ========== Form inputs ========== */
        .form-control, input[type="text"], input[type="email"], input[type="tel"], textarea, select {
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 4px;
            color: #2B3388;
            background: #fff;
        }
        .form-control:focus, input:focus, textarea:focus, select:focus {
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.15);
            outline: none;
        }
        .form-label, label { color: #2B3388; font-weight: 600; }

        /* ========== Submit button ========== */
        .btn-cta, button[type="submit"] {
            background-color: #2B3388;
            color: #fff;
            border: 1px solid #2B3388;
            border-radius: 4px;
            padding: 12px 32px;
            font-family: Arial, sans-serif;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        .btn-cta:hover, button[type="submit"]:hover {
            background-color: rgba(43, 51, 136, 0.85);
            color: #fff;
            border-color: rgba(43, 51, 136, 0.85);
        }

        /* ========== Page-specific ========== */
        .contact-form-success {
            background-color: rgba(43, 51, 136, 0.04);
            color: #2B3388;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            border: 1px solid rgba(43, 51, 136, 0.25);
        }
        .contact-form-error {
            background-color: rgba(43, 51, 136, 0.04);
            color: #2B3388;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 25px;
            border: 1px solid #2B3388;
        }
        .location-card {
            background: #fff;
            border-radius: 4px;
            padding: 25px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            height: 100%;
        }
        .location-card h5 {
            color: #2B3388;
            font-weight: 700;
            margin-bottom: 18px;
            font-size: 1.1rem;
        }
        .location-card .loc-line {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
            color: #2B3388;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .location-card .loc-line i {
            color: #2B3388;
            margin-top: 4px;
            flex-shrink: 0;
            width: 16px;
            text-align: center;
        }
        .location-card .loc-line a {
            color: #2B3388;
            text-decoration: none;
        }
        .location-card .loc-line a:hover {
            text-decoration: underline;
        }
        .contact-bottom-row {
            border-top: 1px solid rgba(43, 51, 136, 0.15);
            padding-top: 20px;
            margin-top: 25px;
        }
        .contact-bottom-row h6 {
            color: #2B3388;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .contact-bottom-row p {
            color: #2B3388;
            margin-bottom: 4px;
            font-size: 0.95rem;
        }
        .contact-bottom-row a {
            color: #2B3388;
            text-decoration: none;
        }
        .contact-bottom-row a:hover {
            text-decoration: underline;
        }
        .contact-info-wrap .title { color: #2B3388; }
        .contact-info-wrap p { color: rgba(43, 51, 136, 0.75); }
        .form-grp input,
        .form-grp textarea {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-size: 15px;
            font-family: Arial, sans-serif;
        }
        .form-grp textarea { min-height: 140px; }
        .form-grp input::placeholder,
        .form-grp textarea::placeholder { color: rgba(43, 51, 136, 0.6); }

        /* ========== Mobile responsive ========== */
        @media (max-width: 767.98px) {
            body { font-size: 15px; }
            .contact-info-wrap .title { font-size: 1.4rem; }
            .location-card { padding: 18px; }
            .location-card h5 { font-size: 1rem; }
            .contact-bottom-row { margin-top: 18px; padding-top: 16px; }
            .col-lg-5 + .col-lg-7 { margin-top: 30px; }
            .btn-cta, button[type="submit"] { width: 100%; padding: 12px 20px; }
            .contact-map iframe { height: 320px; }
        }
    </style>
</head>
<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('contact', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <a href="index.php">Home</a>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span>Contact</span>
                            </nav>
                            <h3 class="title">Contact Us</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-area section-py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="contact-info-wrap">
                            <h2 class="title">Get In Touch With Us</h2>
                            <p>Contact us anytime for support. We are always just 1 click away from you.</p>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Head Office -->
                            <div class="col-md-6">
                                <div class="location-card">
                                    <h5>Head Office</h5>
                                    <div class="loc-line"><i class="fas fa-map-marker-alt"></i><span>Plot 247, Marbel Construction Premises,</span></div>
                                    <div class="loc-line"><span style="margin-left: 26px;">King Mswati III Avenue West</span></div>
                                    <div class="loc-line"><span style="margin-left: 26px;">Matsapha Industrial Site</span></div>
                                    <div class="loc-line mt-2"><i class="fas fa-phone-alt"></i><a href="tel:+26825184633">(+268) 2518 4633/ 4610</a></div>
                                    <div class="loc-line"><i class="fas fa-mobile-alt"></i><a href="tel:+26878068944">(+268) 7806 8944</a></div>
                                </div>
                            </div>
                            <!-- Metrology Laboratory -->
                            <div class="col-md-6">
                                <div class="location-card">
                                    <h5>Metrology Laboratory</h5>
                                    <div class="loc-line"><i class="fas fa-map-marker-alt"></i><span>King Sobhuza II Avenue</span></div>
                                    <div class="loc-line"><span style="margin-left: 26px;">Matsapha Crescent, Opposite YKK Zippers</span></div>
                                    <div class="loc-line"><span style="margin-left: 26px;">Matsapha Industrial Site</span></div>
                                    <div class="loc-line mt-2"><i class="fas fa-phone-alt"></i><a href="tel:+26825186633">(+268) 2518 6633</a></div>
                                </div>
                            </div>
                        </div>

                        <!-- Postal & Website row -->
                        <div class="row contact-bottom-row">
                            <div class="col-sm-6">
                                <h6><i class="fas fa-envelope me-2"></i>Postal Address:</h6>
                                <p>P.O. Box 1399,</p>
                                <p>Matsapha, Eswatini</p>
                            </div>
                            <div class="col-sm-6">
                                <h6><i class="fas fa-globe me-2"></i>Website:</h6>
                                <p><a href="https://www.swasa.co.sz" target="_blank">www.swasa.co.sz</a></p>
                                <p><a href="mailto:info@swasa.co.sz">info@swasa.co.sz</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-info-wrap">
                            <h4 class="title">Fill Up The Contact Form</h4>
                        </div>

                        <!-- Success Message -->
                        <?php if ($success): ?>
                            <div class="contact-form-success">
                                <strong>Thank you!</strong> Your message has been sent successfully. We'll contact you soon.
                            </div>
                        <?php endif; ?>

                        <!-- Error Message -->
                        <?php if ($error): ?>
                            <div class="contact-form-error">
                                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Contact Form (only show if no success) -->
                        <?php if (!$success): ?>
                            <div class="contact-form-wrap">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <input name="name" type="text" placeholder="Name *" required 
                                                       value="<?= htmlspecialchars($prefill['name'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <input name="email" type="email" placeholder="E-mail *" required
                                                       value="<?= htmlspecialchars($prefill['email'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <input name="phone" type="tel" placeholder="Phone *" required
                                                       value="<?= htmlspecialchars($prefill['phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-grp">
                                                <input name="subject" type="text" placeholder="Your Subject *" required
                                                       value="<?= htmlspecialchars($prefill['subject'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-grp">
                                        <textarea name="message" placeholder="Message" required><?= htmlspecialchars($prefill['message'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form-grp col-10 mx-auto text-center">
                                        <button type="submit" class="btn-cta" style="cursor:pointer;">Submit Message</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <div class="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d14282.718543167279!2d31.303588!3d-26.498258!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjbCsDI5JzUzLjciUyAzMcKwMTgnMTIuOSJF!5e0!3m2!1sen!2sus!4v1750623979471!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </main>

    <?php include("includes/footer.php")?>

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
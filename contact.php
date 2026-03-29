<?php
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
    <title>ESWASA - Contact Us</title>
    <meta name="description" content="">
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
    <style>
        .contact-form-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border: 1px solid #c3e6cb;
        }
        .contact-form-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
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
                            <p>Contact us anytime for support, we are always just 1 click away from you.</p>
                            <ul class="list-wrap">
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="content">
                                        <p><strong>Head Office:</strong> Plot 247, Marbel Construction Premises, King Mswati III Avenue West, Matsapha Industrial Site</p>
                                        <p><strong>Metrology Laboratory:</strong> King Sobhuza II Avenue, Matsapha Crescent, Opposite YKK Zippers, Matsapha Industrial Site</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="content">
                                        <p><strong>Head Office:</strong> <a href="tel:+26825184633">(+268) 2518 4633/ 4610</a></p>
                                        <p><strong>Metrology Lab:</strong> <a href="tel:+26825186633">(+268) 2518 6633</a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <div class="content">
                                        <p>P. O. Box 1399, Matsapha, Eswatini</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <i class="far fa-envelope"></i>
                                    </div>
                                    <div class="content">
                                        <a href="mailto:info@swasa.co.sz">info@swasa.co.sz</a>
                                    </div>
                                </li>
                            </ul>
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
                                        <button type="submit" class="btn btn-contact-bg">Submit Message</button>
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
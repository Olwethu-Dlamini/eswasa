<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';

$success = false;
$error = '';
$prefill = [
    'name' => '', 'email' => '', 'phone' => '',
    'department' => '', 'feedback_type' => '', 'service' => '',
    'resolved' => '', 'issue' => '', 'rating' => '', 'suggestion' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($prefill as $k => $_) {
        $prefill[$k] = trim($_POST[$k] ?? '');
    }

    // Required: feedback_type + issue + at least one identifier (email or phone)
    if (!$prefill['feedback_type']) {
        $error = 'Please select the type of feedback.';
    } elseif (!$prefill['issue']) {
        $error = 'Please tell us what the issue or comment is.';
    } elseif (!$prefill['email'] && !$prefill['phone']) {
        $error = 'Please provide either an email address or a phone number so we can respond.';
    } elseif ($prefill['email'] && !filter_var($prefill['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        // Attempt to store. Table is created lazily on first use.
        @mysqli_query($conn, "CREATE TABLE IF NOT EXISTS eswasa_customer_feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150),
            email VARCHAR(150),
            phone VARCHAR(50),
            department VARCHAR(100),
            feedback_type VARCHAR(50),
            service VARCHAR(150),
            resolved VARCHAR(20),
            issue TEXT,
            rating TINYINT,
            suggestion TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $conn->prepare("INSERT INTO eswasa_customer_feedback
            (name, email, phone, department, feedback_type, service, resolved, issue, rating, suggestion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $rating_i = (int)($prefill['rating'] ?: 0);
        $stmt->bind_param(
            'sssssssiis',
            $prefill['name'], $prefill['email'], $prefill['phone'],
            $prefill['department'], $prefill['feedback_type'], $prefill['service'],
            $prefill['resolved'], $prefill['issue'], $rating_i, $prefill['suggestion']
        );

        if ($stmt && $stmt->execute()) {
            // Send notification email
            $to = 'info@swasa.co.sz';
            $email_subject = "Customer Feedback: " . $prefill['feedback_type'];
            $body  = "<h3>New Customer Feedback</h3>";
            $body .= "<p><strong>Name:</strong> " . htmlspecialchars($prefill['name'] ?: '(not provided)') . "</p>";
            $body .= "<p><strong>Email:</strong> " . htmlspecialchars($prefill['email'] ?: '(not provided)') . "</p>";
            $body .= "<p><strong>Phone:</strong> " . htmlspecialchars($prefill['phone'] ?: '(not provided)') . "</p>";
            $body .= "<p><strong>Department:</strong> " . htmlspecialchars($prefill['department']) . "</p>";
            $body .= "<p><strong>Type:</strong> " . htmlspecialchars($prefill['feedback_type']) . "</p>";
            $body .= "<p><strong>Service:</strong> " . htmlspecialchars($prefill['service']) . "</p>";
            $body .= "<p><strong>Issue resolved:</strong> " . htmlspecialchars($prefill['resolved'] ?: 'N/A') . "</p>";
            $body .= "<p><strong>Rating:</strong> " . $rating_i . " / 5</p>";
            $body .= "<p><strong>Issue:</strong><br>" . nl2br(htmlspecialchars($prefill['issue'])) . "</p>";
            if ($prefill['suggestion']) {
                $body .= "<p><strong>Suggestions:</strong><br>" . nl2br(htmlspecialchars($prefill['suggestion'])) . "</p>";
            }
            $body .= "<hr><p><em>" . date('F j, Y \a\t g:i A') . "</em></p>";
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            @mail($to, $email_subject, $body, $headers);

            header("Location: customer-feedback.php?success=1");
            exit;
        } else {
            $error = 'Sorry — we could not save your feedback. Please try again or email info@swasa.co.sz directly.';
        }
    }
}

if (isset($_GET['success'])) {
    $success = true;
    $prefill = array_map(fn($_) => '', $prefill);
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Customer Feedback &amp; Complaints - ESWASA</title>
    <meta name="description" content="Share feedback, file a complaint, lodge a compliment or appeal a decision. The Eswatini Standards Authority listens to every submission.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
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
    <link rel="stylesheet" type="text/css" href="rs-plugin/css/settings.css" media="screen">
    <link rel="stylesheet" type="text/css" href="assets/css/extralayers.css" media="screen">
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

        .display-6 { color: #2B3388; font-weight: 700; letter-spacing: -0.01em; }
        .section-divider {
            width: 60px; height: 2px; background: #2B3388;
            margin: 16px auto 0; border-radius: 0;
        }
        .intro-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-left: 3px solid #2B3388;
            border-radius: 4px;
            padding: 22px 26px;
            max-width: 920px;
            margin: 0 auto 36px;
        }
        .intro-card p {
            margin: 0; color: rgba(43, 51, 136, 0.85);
            font-size: 1rem; line-height: 1.65;
        }

        .feedback-section { padding: 60px 0 80px; }

        .feedback-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 36px 36px 32px;
            max-width: 920px;
            margin: 0 auto;
        }
        .feedback-card .form-group { margin-bottom: 20px; }
        .feedback-card label {
            display: block;
            color: #2B3388;
            font-weight: 600;
            font-size: 0.92rem;
            margin-bottom: 6px;
        }
        .feedback-card label .req { color: #b00; margin-left: 2px; }
        .feedback-card .form-control,
        .feedback-card select,
        .feedback-card textarea {
            width: 100%;
            border: 1px solid rgba(43, 51, 136, 0.25);
            border-radius: 3px;
            padding: 10px 12px;
            font-size: 0.95rem;
            color: #2B3388;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .feedback-card .form-control:focus,
        .feedback-card select:focus,
        .feedback-card textarea:focus {
            outline: none;
            border-color: #2B3388;
            box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.12);
        }
        .feedback-card textarea { min-height: 110px; resize: vertical; }

        .radio-row { display: flex; flex-wrap: wrap; gap: 22px; margin-top: 6px; }
        .radio-row label {
            font-weight: 500;
            color: rgba(43, 51, 136, 0.88);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            margin-bottom: 0;
        }
        .radio-row input[type="radio"] { accent-color: #2B3388; transform: scale(1.1); }

        /* Rating stars */
        .rating-row {
            display: inline-flex;
            flex-direction: row-reverse;
            gap: 4px;
            margin-top: 6px;
        }
        .rating-row input[type="radio"] { display: none; }
        .rating-row label {
            color: rgba(43, 51, 136, 0.30);
            font-size: 1.8rem;
            cursor: pointer;
            margin-bottom: 0;
            transition: color .15s ease, transform .15s ease;
        }
        .rating-row label:hover,
        .rating-row label:hover ~ label,
        .rating-row input[type="radio"]:checked ~ label {
            color: #2B3388;
        }
        .rating-row label:hover { transform: scale(1.08); }

        .btn-submit {
            background: #2B3388;
            color: #fff;
            border: none;
            padding: 12px 32px;
            font-weight: 700;
            font-size: 0.95rem;
            border-radius: 3px;
            cursor: pointer;
            transition: background .2s ease;
            margin-top: 8px;
        }
        .btn-submit:hover { background: rgba(43, 51, 136, 0.88); }

        .alert-success,
        .alert-error {
            padding: 14px 18px;
            border-radius: 3px;
            margin-bottom: 24px;
            font-size: 0.95rem;
        }
        .alert-success {
            background: rgba(43, 51, 136, 0.06);
            border-left: 4px solid #2B3388;
            color: #2B3388;
        }
        .alert-error {
            background: rgba(176, 0, 0, 0.06);
            border-left: 4px solid #b00;
            color: #b00;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        @media (max-width: 767.98px) {
            .feedback-section { padding: 40px 0 50px; }
            .feedback-card { padding: 26px 22px 24px; }
            .form-grid-2 { grid-template-columns: 1fr; gap: 0; }
            .breadcrumb-content .title { font-size: 1.6rem; }
            .rating-row label { font-size: 1.55rem; }
        }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="spinner">
            <div class="sk-dot1"></div><div class="sk-dot2"></div>
            <div class="rect3"></div><div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('customer-feedback', 'assets/img/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="customer-care.php">Customer Care</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Feedback &amp; Complaints</span>
                            </nav>
                            <h3 class="title">Customer Feedback &amp; Complaint Form</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="feedback-section">
            <div class="container">
                <div class="main_title centered upper mb-4 text-center">
                    <h2 class="display-6 fw-bold">Tell Us How We're Doing</h2>
                    <div class="section-divider"></div>
                </div>

                <div class="intro-card">
                    <p>Kindly fill in the fields below. Every submission is logged, reviewed and routed to the relevant department. We acknowledge feedback within 3 working days and aim to provide a substantive response within 14 working days, in line with our <a href="service-charter.php">Service Charter</a>.</p>
                </div>

                <div class="feedback-card">

                    <?php if ($success): ?>
                        <div class="alert-success">
                            <strong>Thank you.</strong> Your feedback has been submitted successfully. We will be in touch.
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post" novalidate>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="name">Your Name <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($prefill['name']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($prefill['phone']) ?>" placeholder="e.g. +268 7612 3456">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($prefill['email']) ?>" placeholder="name@example.com">
                            <small class="text-muted" style="display:block; margin-top:4px;">Provide email or phone so we can respond.</small>
                        </div>

                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select id="department" name="department">
                                    <option value="">— Select department —</option>
                                    <?php
                                    $depts = [
                                        'Standards Development',
                                        'Quality Assurance & Inspection',
                                        'Testing Services',
                                        'Calibration & Metrology',
                                        'Certification',
                                        'Training',
                                        'Customer Service / Reception',
                                        'Not sure',
                                    ];
                                    foreach ($depts as $d) {
                                        $sel = $prefill['department'] === $d ? 'selected' : '';
                                        echo "<option value=\"$d\" $sel>$d</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="service">Service Sought</label>
                                <select id="service" name="service">
                                    <option value="">— Select service —</option>
                                    <?php
                                    $services = [
                                        'Standards Purchase',
                                        'Standards Development / Comment',
                                        'Management Systems Certification',
                                        'Product Certification (ESWASA Mark)',
                                        'Ingelo MSME Certification',
                                        'Testing Services',
                                        'Calibration / Metrology',
                                        'Training Programme',
                                        'Information Request',
                                        'Other',
                                    ];
                                    foreach ($services as $s) {
                                        $sel = $prefill['service'] === $s ? 'selected' : '';
                                        echo "<option value=\"$s\" $sel>$s</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>What type of feedback is this?<span class="req">*</span></label>
                            <div class="radio-row">
                                <?php
                                $types = ['Complaint', 'Compliment', 'Suggestion', 'Appeal'];
                                foreach ($types as $t) {
                                    $chk = $prefill['feedback_type'] === $t ? 'checked' : '';
                                    echo "<label><input type=\"radio\" name=\"feedback_type\" value=\"$t\" $chk> $t</label>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Was your issue resolved?</label>
                            <div class="radio-row">
                                <?php
                                $resolved = ['Yes', 'No', 'Partially', 'N/A'];
                                foreach ($resolved as $r) {
                                    $chk = $prefill['resolved'] === $r ? 'checked' : '';
                                    echo "<label><input type=\"radio\" name=\"resolved\" value=\"$r\" $chk> $r</label>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="issue">Please tell us what the issue, compliment or suggestion is<span class="req">*</span></label>
                            <textarea id="issue" name="issue" rows="5" placeholder="Describe in your own words..."><?= htmlspecialchars($prefill['issue']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>How would you rate our service?</label>
                            <div class="rating-row">
                                <?php
                                // Reversed (5 first in markup so CSS sibling-selector hover works).
                                for ($i = 5; $i >= 1; $i--) {
                                    $chk = (string)$prefill['rating'] === (string)$i ? 'checked' : '';
                                    echo "<input type=\"radio\" id=\"r$i\" name=\"rating\" value=\"$i\" $chk>";
                                    echo "<label for=\"r$i\" title=\"$i star".($i>1?'s':'')."\">&#9733;</label>";
                                }
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="suggestion">Suggest other ways we can improve <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                            <textarea id="suggestion" name="suggestion" rows="3" placeholder="Anything else you would like us to know..."><?= htmlspecialchars($prefill['suggestion']) ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Submit Feedback</button>
                    </form>
                </div>

            </div>
        </section>

    </main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

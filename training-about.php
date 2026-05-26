<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once __DIR__ . '/includes/breadcrumb_helper.php';

// All editable keys for the Training Academy (About) page.
$train_about_keys = [
    // breadcrumb
    'train_about_breadcrumb_title',
    // intro section
    'train_about_hero_title','train_about_hero_subtitle','train_about_intro_body',
    // training formats
    'train_about_format_1_tag','train_about_format_1_duration','train_about_format_1_audience',
    'train_about_format_2_tag','train_about_format_2_duration','train_about_format_2_audience',
    'train_about_format_note',
    // 7 course cards (name, image, alt) and their modal content
    'train_about_course_1_name','train_about_course_1_image','train_about_course_1_alt',
    'train_about_course_1_modal_title','train_about_course_1_overview','train_about_course_1_benefits','train_about_course_1_courses','train_about_course_1_duration',
    'train_about_course_2_name','train_about_course_2_image','train_about_course_2_alt',
    'train_about_course_2_modal_title','train_about_course_2_overview','train_about_course_2_benefits','train_about_course_2_courses','train_about_course_2_duration',
    'train_about_course_3_name','train_about_course_3_image','train_about_course_3_alt',
    'train_about_course_3_modal_title','train_about_course_3_overview','train_about_course_3_benefits','train_about_course_3_courses','train_about_course_3_duration',
    'train_about_course_4_name','train_about_course_4_image','train_about_course_4_alt',
    'train_about_course_4_modal_title','train_about_course_4_overview','train_about_course_4_benefits','train_about_course_4_courses','train_about_course_4_duration',
    'train_about_course_5_name','train_about_course_5_image','train_about_course_5_alt',
    'train_about_course_5_modal_title','train_about_course_5_overview','train_about_course_5_benefits','train_about_course_5_courses','train_about_course_5_duration',
    'train_about_course_6_name','train_about_course_6_image','train_about_course_6_alt',
    'train_about_course_6_modal_title','train_about_course_6_overview','train_about_course_6_benefits','train_about_course_6_courses','train_about_course_6_duration',
    'train_about_course_7_name','train_about_course_7_image','train_about_course_7_alt',
    'train_about_course_7_modal_title','train_about_course_7_overview','train_about_course_7_benefits','train_about_course_7_courses','train_about_course_7_duration',
    // Why Train With ESWASA
    'train_about_why_title','train_about_why_subtitle',
    'train_about_why_1_title','train_about_why_1_body',
    'train_about_why_2_title','train_about_why_2_body',
    'train_about_why_3_title','train_about_why_3_body',
    'train_about_why_4_title','train_about_why_4_body',
    'train_about_why_5_title','train_about_why_5_body',
    'train_about_why_6_title','train_about_why_6_body',
    // Policies
    'train_about_policies_title','train_about_policies_subtitle',
    'train_about_policy_application_tab','train_about_policy_application_title','train_about_policy_application_body',
    'train_about_policy_acceptance_tab','train_about_policy_acceptance_title','train_about_policy_acceptance_body',
    'train_about_policy_cancellations_tab','train_about_policy_cancellations_title','train_about_policy_cancellations_body',
    'train_about_policy_fees_tab','train_about_policy_fees_title','train_about_policy_fees_body',
    'train_about_bank_title',
    'train_about_bank_name','train_about_bank_account_name','train_about_bank_account_number','train_about_bank_branch_code','train_about_bank_branch_name','train_about_bank_swift','train_about_bank_note',
    'train_about_policy_travel_tab','train_about_policy_travel_title','train_about_policy_travel_body',
    'train_about_policy_inhouse_tab','train_about_policy_inhouse_title','train_about_policy_inhouse_body',
    'train_about_policy_assessments_tab','train_about_policy_assessments_title',
    'train_about_assess_eval_title','train_about_assess_eval_list',
    'train_about_assess_cert_title','train_about_assess_cert_list',
    'train_about_assess_pass_mark',
];

$train_about_defaults = [
    'train_about_breadcrumb_title' => 'About Our Training',
    'train_about_hero_title' => 'Our Training Programmes',
    'train_about_hero_subtitle' => 'Empowering Excellence Through Knowledge',
    'train_about_intro_body' => "We understand the unique needs of each business, which is why we offer tailor-made training solutions to industry, individuals, government agencies and other institutions in Management Systems, allowing organisations to choose a convenient location or host the training at our training centre in Matsapha.\n\nAt ESWASA Training Academy, we are proud to work with facilitators who are industry experts in various fields, Lead Auditors, and major contributors to the development of Eswatini National Standards (SZNS).",

    'train_about_format_1_tag' => 'Awareness Training',
    'train_about_format_1_duration' => '½ day · 1 day · 2 days',
    'train_about_format_1_audience' => 'Suitable for management, supervisors and teams needing a working introduction to a standard.',
    'train_about_format_2_tag' => 'Full Training',
    'train_about_format_2_duration' => '3 – 5 days',
    'train_about_format_2_audience' => 'Understanding & Implementation, Auditing and Customised training for practitioners taking the standard into operation.',
    'train_about_format_note' => 'Both formats are delivered as standard-based courses across all sectors — see the full course catalogue below.',

    // Course 1 — Quality Management
    'train_about_course_1_name' => 'Quality Management System Courses',
    'train_about_course_1_image' => 'admin/uploads/certificate-iso-9001-colored.svg',
    'train_about_course_1_alt' => 'ISO 9001 — Quality Management System',
    'train_about_course_1_modal_title' => 'Quality Management System Courses',
    'train_about_course_1_overview' => 'Our Quality Management System courses are designed to help organisations implement and maintain effective quality management systems based on international standards.',
    'train_about_course_1_benefits' => "Improved product and service quality\nEnhanced customer satisfaction\nStreamlined processes and reduced waste\nIncreased operational efficiency",
    'train_about_course_1_courses' => "ISO 9001:2015 Foundation\nISO 9001:2015 Internal Auditor\nISO 9001:2015 Lead Auditor\nQuality Management System Implementation",
    'train_about_course_1_duration' => 'Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.',

    // Course 2 — Health & Safety
    'train_about_course_2_name' => 'Health and Safety Management',
    'train_about_course_2_image' => 'admin/uploads/certificate-iso-45001-colored.svg',
    'train_about_course_2_alt' => 'ISO 45001 — Health and Safety Management',
    'train_about_course_2_modal_title' => 'Health and Safety Management',
    'train_about_course_2_overview' => 'Our Health and Safety Management courses provide comprehensive training on occupational health and safety standards to create safer work environments.',
    'train_about_course_2_benefits' => "Reduced workplace accidents and incidents\nCompliance with legal requirements\nImproved employee morale and productivity\nEnhanced corporate reputation",
    'train_about_course_2_courses' => "ISO 45001:2018 Foundation\nISO 45001:2018 Internal Auditor\nISO 45001:2018 Lead Auditor\nRisk Assessment and Management\nIncident Investigation and Reporting",
    'train_about_course_2_duration' => 'Courses range from 1-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.',

    // Course 3 — Environmental
    'train_about_course_3_name' => 'Environmental Management',
    'train_about_course_3_image' => 'admin/uploads/certificate-iso-14001-colored.svg',
    'train_about_course_3_alt' => 'ISO 14001 — Environmental Management',
    'train_about_course_3_modal_title' => 'Environmental Management',
    'train_about_course_3_overview' => 'Our Environmental Management courses help organisations implement sustainable practices and comply with environmental regulations.',
    'train_about_course_3_benefits' => "Reduced environmental impact\nCompliance with environmental regulations\nCost savings through resource efficiency\nEnhanced corporate social responsibility",
    'train_about_course_3_courses' => "ISO 14001:2015 Foundation\nISO 14001:2015 Internal Auditor\nISO 14001:2015 Lead Auditor\nEnvironmental Impact Assessment\nSustainability Reporting",
    'train_about_course_3_duration' => 'Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.',

    // Course 4 — GAP
    'train_about_course_4_name' => 'Good Agricultural Practices',
    'train_about_course_4_image' => 'admin/uploads/course-globalgap.svg',
    'train_about_course_4_alt' => 'GLOBALG.A.P. — Good Agricultural Practices',
    'train_about_course_4_modal_title' => 'Good Agricultural Practices',
    'train_about_course_4_overview' => 'Our Good Agricultural Practices courses focus on sustainable farming methods to ensure food safety, environmental protection, and worker welfare.',
    'train_about_course_4_benefits' => "Improved crop quality and yield\nReduced environmental impact in agriculture\nEnhanced food safety standards\nBetter market access and compliance",
    'train_about_course_4_courses' => "GLOBALG.A.P. Foundation\nFarm Assurance Implementation\nSustainable Farming Practices\nAgricultural Risk Management",
    'train_about_course_4_duration' => 'Courses range from 2-4 days, available in both in-person and virtual formats. Customised training options are available for agricultural organisations.',

    // Course 5 — Wellness
    'train_about_course_5_name' => 'Wellness Management',
    'train_about_course_5_image' => 'admin/uploads/course-wellness.svg',
    'train_about_course_5_alt' => 'Wellness Management',
    'train_about_course_5_modal_title' => 'Wellness Management',
    'train_about_course_5_overview' => 'Our Wellness Management courses promote holistic health approaches for individuals and organisations to improve overall well-being.',
    'train_about_course_5_benefits' => "Improved employee health and productivity\nReduced absenteeism and healthcare costs\nEnhanced work-life balance\nStronger organizational culture",
    'train_about_course_5_courses' => "Workplace Wellness Foundation\nStress Management Techniques\nHealth Promotion Strategies\nWellness Programme Implementation",
    'train_about_course_5_duration' => 'Courses range from 1-3 days, available in both in-person and virtual formats. Customised training options are available for organisations.',

    // Course 6 — Food Safety
    'train_about_course_6_name' => 'Food Safety Management',
    'train_about_course_6_image' => 'admin/uploads/course-iso-22000.svg',
    'train_about_course_6_alt' => 'ISO 22000 — Food Safety Management',
    'train_about_course_6_modal_title' => 'Food Safety Management',
    'train_about_course_6_overview' => 'Our Food Safety Management courses provide essential training on maintaining hygiene and safety standards in food production and handling.',
    'train_about_course_6_benefits' => "Prevention of foodborne illnesses\nCompliance with food safety regulations\nImproved product quality and shelf life\nEnhanced consumer trust",
    'train_about_course_6_courses' => "ISO 22000:2018 Foundation\nHACCP Principles and Application\nFood Safety Internal Auditor\nFood Hygiene Management",
    'train_about_course_6_duration' => 'Courses range from 2-5 days, available in both in-person and virtual formats. Customised training options are available for food industry organisations.',

    // Course 7 — Auditing
    'train_about_course_7_name' => 'Auditing',
    'train_about_course_7_image' => 'admin/uploads/course-iso-19011.svg',
    'train_about_course_7_alt' => 'ISO 19011 — Auditing',
    'train_about_course_7_modal_title' => 'Auditing',
    'train_about_course_7_overview' => 'Our Auditing courses train professionals in effective auditing techniques for various management systems to ensure compliance and continuous improvement.',
    'train_about_course_7_benefits' => "Improved system compliance and effectiveness\nIdentification of improvement opportunities\nEnhanced risk management\nProfessional certification pathways",
    'train_about_course_7_courses' => "ISO 19011:2018 Auditing Guidelines\nIntegrated Management System Auditor\nLead Auditor Training\nAudit Reporting and Follow-up",
    'train_about_course_7_duration' => 'Courses range from 3-5 days, available in both in-person and virtual formats. Customised training options are available for organisations.',

    // Why train
    'train_about_why_title' => 'Why Train With ESWASA?',
    'train_about_why_subtitle' => 'Discover the unique advantages of choosing ESWASA for your professional development',
    'train_about_why_1_title' => 'Standard-based Training',
    'train_about_why_1_body' => 'Our training courses are based on international standards, ensuring high-quality content and delivery. Course modules are developed in cooperation with recognised standards experts to provide current, relevant knowledge.',
    'train_about_why_2_title' => 'Highly Interactive Sessions',
    'train_about_why_2_body' => 'Individualised attention through small interactive training sessions. Hands-on courses designed to help you acquire skills quickly and in depth, with room for questions and practical exercises.',
    'train_about_why_3_title' => 'Quality Training',
    'train_about_why_3_body' => 'Quality training that is relevant to the needs of our society, delivered by experienced instructors across every course we offer, ensuring practical knowledge that can be immediately applied.',
    'train_about_why_4_title' => 'Flexibility of Course Content',
    'train_about_why_4_body' => 'We tailor course content to your specific needs to meet your business objectives, whether customised training for your team or specialised content for your industry.',
    'train_about_why_5_title' => 'Return on Investment',
    'train_about_why_5_body' => 'Our courses help industry, commerce and the public sector maximise return on investment, with practical skills that deliver measurable improvements in performance and efficiency.',
    'train_about_why_6_title' => 'Certified Facilitators',
    'train_about_why_6_body' => 'We are passionate about sharing knowledge and skills on the principles and practices of standards. Our facilitators are not just experts in their fields — they are dedicated, certified, and committed to your success.',

    // Policies
    'train_about_policies_title' => 'Training Academy — General Information',
    'train_about_policies_subtitle' => 'Ensuring a Smooth Training Experience',

    'train_about_policy_application_tab' => 'Application',
    'train_about_policy_application_title' => 'Application',
    'train_about_policy_application_body' => 'Application forms and course-related information can be accessed through this website under Training, or requested from the Training Unit — call 7602 7306 or email info@eswasa.co.sz / training@eswasa.co.sz. Applications should reach ESWASA at least 10 working days before the course commencement date. If the number of paid applicants has not reached the minimum required for a class (5 delegates), ESWASA reserves the right to postpone the course but undertakes to inform participants promptly of such developments.',

    'train_about_policy_acceptance_tab' => 'Acceptance',
    'train_about_policy_acceptance_title' => 'Acceptance',
    'train_about_policy_acceptance_body' => 'Applicants will be notified of the outcome of their applications soon thereafter. Acceptance of the offer has to be acknowledged by the participant in writing and sent by email or fax to the office as soon as possible or 7 days before the start of the training for registration.',

    'train_about_policy_cancellations_tab' => 'Cancellations',
    'train_about_policy_cancellations_title' => 'Cancellations',
    'train_about_policy_cancellations_body' => 'A cancellation fee of 50% of the course fee will be deducted from participants who cancel after registration / confirmation or on the date of commencement of the training course. ESWASA reserves the right to postpone any course (typically due to insufficient enrolment — see Application for class minimums) and undertakes to inform participants promptly of such developments.',

    'train_about_policy_fees_tab' => 'Course Fees',
    'train_about_policy_fees_title' => 'Course Fees and Payments',
    'train_about_policy_fees_body' => 'Course fees are charged per person and are inclusive of meals and refreshments for the duration of the training. Applicants should pay in full and submit proof of payment or a purchase order at least 7 working days before the course commencement date.',
    'train_about_bank_title' => 'Banking Details',
    'train_about_bank_name' => 'Standard Bank Eswatini',
    'train_about_bank_account_name' => 'Eswatini Standards Authority — ESWASA',
    'train_about_bank_account_number' => '9110002956732',
    'train_about_bank_branch_code' => '663164',
    'train_about_bank_branch_name' => 'Matsapha',
    'train_about_bank_swift' => 'SBICSZMX',
    'train_about_bank_note' => 'Mobile Money and a Speedpoint machine are also available at the ESWASA office for ease of payment.',

    'train_about_policy_travel_tab' => 'Travel',
    'train_about_policy_travel_title' => 'Travel and Accommodation',
    'train_about_policy_travel_body' => 'Participants are responsible for their own travel and accommodation arrangements. All courses offered are day courses.',

    'train_about_policy_inhouse_tab' => 'Training Venues',
    'train_about_policy_inhouse_title' => 'Training Venues',
    'train_about_policy_inhouse_body' => 'The venue for most courses is the ESWASA Training Academy, unless prior arrangements are made for in-house and/or customised training. In-house training will be presented to a minimum of 11 delegates. The organisation shall be responsible for providing a suitable training room with audiovisual equipment as well as refreshments.',

    'train_about_policy_assessments_tab' => 'Assessments',
    'train_about_policy_assessments_title' => 'Assessments',
    'train_about_assess_eval_title' => 'How performance is evaluated',
    'train_about_assess_eval_list' => "Continuous assessments.\nPractical exercises.\nGroup activities.\nFinal examinations.",
    'train_about_assess_cert_title' => 'Certificates awarded',
    'train_about_assess_cert_list' => "Certificate of Competence — for successful completion and passing of all assessments.\nCertificate of Attendance — for awareness trainings or participation-only sessions.",
    'train_about_assess_pass_mark' => 'Minimum passing mark: 70%',
];

$pc = pc_get_many($conn, $train_about_keys, $train_about_defaults);

// Helper to render a newline-separated list as <li> items (used for benefits/courses/eval/cert lists).
if (!function_exists('train_about_list_items')) {
    function train_about_list_items(string $text): string {
        $out = '';
        foreach (preg_split("/\n+/", trim($text)) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $out .= '<li>' . htmlspecialchars($line, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        return $out;
    }
}
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Training - About - ESWASA</title>
    <meta name="description" content="Discover SWASA's comprehensive training programs designed to empower excellence through knowledge.">
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

        /* Section divider — matches other pages */
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 0;
            border-radius: 0;
        }

        /* Heading hierarchy */
        .display-6, .display-4 {
            color: #2B3388;
            font-weight: 700;
            letter-spacing: -0.01em;
        }
        .display-4 { font-size: 2.5rem; }

        /* Old gradient text class — flatten to solid brand blue */
        .text-gradient-primary {
            color: #2B3388 !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
        }

        /* Cards — restrained hover */
        .card {
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.12);
        }

        /* Training course cards */
        .add2cart_image {
            background: rgba(43, 51, 136, 0.04);
            overflow: hidden;
        }
        .add2cart_image img {
            max-height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform .25s ease;
        }
        /* ISO certificate SVG cards — contain, don't crop; padded so the badge sits centered */
        .add2cart_image img[src$=".svg"] {
            object-fit: contain;
            padding: 16px;
            background: #fff;
            height: 200px;
        }
        .hover-lift:hover .add2cart_image img {
            transform: scale(1.04);
        }
        .add2cart_prod_name {
            color: #2B3388;
            text-decoration: none;
        }
        .add2cart_prod_name:hover {
            color: #2B3388;
        }
        .add2cart_btn,
        .add2cart_btn.btn-primary,
        .btn.add2cart_btn {
            background-color: #2B3388 !important;
            border-color: #2B3388 !important;
            color: #fff !important;
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        .add2cart_btn:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            box-shadow: 0 4px 12px rgba(43, 51, 136, 0.20);
        }

        /* Modal styling */
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
            font-size: 18px;
        }
        .modal-header .btn-close { opacity: 0.65; }
        .modal-header .btn-close:hover { opacity: 1; }
        .modal-body { padding: 22px; color: #2B3388; }
        .modal-body p, .modal-body li { color: #2B3388; font-size: 15px; line-height: 1.6; }
        .course-details { margin-bottom: 20px; }
        .course-details h5 {
            color: #2B3388;
            margin: 18px 0 10px;
            font-weight: 700;
            font-size: 16px;
        }
        .course-details ul { padding-left: 20px; }
        .course-details li { margin-bottom: 8px; }
        .modal-footer {
            border-top: 1px solid rgba(43, 51, 136, 0.12);
            padding: 14px 22px;
        }
        .modal-footer .btn-secondary {
            background: #fff !important;
            border: 1px solid rgba(43, 51, 136, 0.30) !important;
            color: #2B3388 !important;
        }
        .btn-enroll {
            background: #2B3388;
            border: none;
            color: #fff;
            padding: 10px 22px;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color .2s ease;
        }
        .btn-enroll:hover {
            background: rgba(43, 51, 136, 0.85);
            color: #fff;
        }

        /* Why Train With ESWASA — uniform 3×2 grid, no rainbow gradients */
        .why-train-section {
            background: rgba(43, 51, 136, 0.04);
            padding: 70px 0 80px;
            position: relative;
        }
        .why-train-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
            max-width: 1180px;
            margin: 0 auto;
        }
        .why-train-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            padding: 32px 26px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
            height: 100%;
        }
        .why-train-card:hover {
            border-color: rgba(43, 51, 136, 0.40);
            box-shadow: 0 8px 20px rgba(43, 51, 136, 0.12);
            transform: translateY(-3px);
        }
        .why-train-icon {
            width: 64px;
            height: 64px;
            background: #2B3388;
            color: #fff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: transform .25s ease;
        }
        .why-train-icon svg {
            width: 32px;
            height: 32px;
        }
        .why-train-card:hover .why-train-icon {
            transform: scale(1.08);
        }
        .why-train-card h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 17px;
            margin: 0 0 12px;
            line-height: 1.3;
        }
        .why-train-card p {
            color: #2B3388;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        /* Training policies — tabs */
        .bg_gray {
            background-color: rgba(43, 51, 136, 0.04);
        }
        .nav-tabs {
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .nav-tabs .nav-link {
            color: #2B3388;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            padding: 10px 18px;
            font-weight: 600;
            transition: color .2s ease, border-color .2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #2B3388;
            border-bottom-color: rgba(43, 51, 136, 0.30);
        }
        .nav-tabs .nav-link.active {
            color: #2B3388;
            border-bottom-color: #2B3388;
            background: transparent;
        }
        .policy-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #2B3388;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        .policy-icon svg {
            width: 32px;
            height: 32px;
        }
        .tab-pane .card h3 {
            color: #2B3388;
            font-weight: 700;
            font-size: 20px;
            margin-top: 16px;
        }
        .tab-pane .card {
            border: 1px solid rgba(43, 51, 136, 0.12) !important;
            border-radius: 4px !important;
            box-shadow: 0 1px 3px rgba(43, 51, 136, 0.06) !important;
        }
        .tab-pane .card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
        }
        .tab-pane.show .card {
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .display-6 { font-size: 1.9rem !important; }
            .display-4 { font-size: 2rem; }
            .why-train-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 767.98px) {
            .display-6 { font-size: 1.55rem !important; }
            .display-4 { font-size: 1.65rem; }
            .why-train-section { padding: 50px 0 60px; }
            .why-train-card { padding: 24px 18px; }
            .why-train-icon { width: 56px; height: 56px; font-size: 20px; margin-bottom: 16px; }
            .why-train-card h4 { font-size: 15px; }
            .why-train-card p { font-size: 13px; }
            .nav-tabs .nav-link { padding: 8px 12px; font-size: 14px; }
            .modal-header { padding: 14px 18px; }
            .modal-body { padding: 18px; }
        }
        @media (max-width: 575.98px) {
            .why-train-grid { grid-template-columns: 1fr; gap: 14px; }
        }

        /* Training-format cards (Awareness / Full / Sector-based) */
        .training-format-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 20px;
            height: 100%;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .training-format-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .training-format-card .format-tag {
            display: inline-block;
            background-color: #2B3388;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 3px;
            margin-bottom: 8px;
        }
        .training-format-card .format-duration {
            font-weight: 700;
            color: #2B3388;
            margin: 0 0 6px;
            font-size: 0.95rem;
        }
        .training-format-card .format-audience {
            color: #2B3388;
            font-size: 0.92rem;
            line-height: 1.55;
            margin: 0;
        }

        /* Banking details block inside Course Fees policy tab */
        .bank-details {
            text-align: left;
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 16px 20px;
            margin: 16px auto 0;
            max-width: 520px;
        }
        .bank-details h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1rem;
            margin: 0 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.18);
        }
        .bank-details dl {
            display: grid;
            grid-template-columns: max-content 1fr;
            gap: 6px 16px;
            margin: 0 0 12px;
        }
        .bank-details dt {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .bank-details dd {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
        }
        .bank-details p {
            color: #2B3388;
            font-size: 0.9rem;
            margin: 0;
        }

        @media (max-width: 575.98px) {
            .training-format-card { padding: 14px 16px; }
            .bank-details { padding: 14px; }
            .bank-details dl { grid-template-columns: 1fr; gap: 2px 0; }
            .bank-details dt { font-size: 0.82rem; margin-top: 8px; }
        }

        /* === Assessments tab — How evaluated + Certificates cards === */
        .assessment-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.10);
            border-radius: 8px;
            padding: 26px 24px 22px;
            height: 100%;
            box-shadow: 0 8px 22px rgba(43, 51, 136, 0.10);
            transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease;
            text-align: left;
        }
        .assessment-card:hover {
            border-color: rgba(43, 51, 136, 0.30);
            box-shadow: 0 14px 30px rgba(43, 51, 136, 0.16);
            transform: translateY(-2px);
        }
        .assessment-card-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #2B3388;
            color: #fff;
            margin-bottom: 14px;
        }
        .assessment-card-icon svg {
            width: 22px;
            height: 22px;
        }
        .assessment-card-title {
            color: #2B3388;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 14px;
            line-height: 1.3;
        }
        .assessment-list {
            margin: 0;
            padding-left: 20px;
            color: #2B3388;
            line-height: 1.65;
        }
        .assessment-list li + li { margin-top: 6px; }
        .assessment-list li::marker { color: #2B3388; }

        .passing-mark-pill {
            display: inline-block;
            margin: 28px auto 0;
            padding: 12px 24px;
            background: #2B3388;
            color: #fff !important;
            border-radius: 999px;
            font-weight: 700;
            letter-spacing: 0.02em;
            box-shadow: 0 6px 16px rgba(43, 51, 136, 0.20);
        }

        @media (max-width: 767.98px) {
            .assessment-card { padding: 22px 20px 20px; }
            .assessment-card-title { font-size: 17px; }
            .passing-mark-pill { padding: 10px 20px; }
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
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('training_about', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
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

                            </nav>
                            <h1 class="title"><?= pc_h($pc['train_about_breadcrumb_title']) ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Training Courses Section -->
        <section id="training_section" class="content_section py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold"><?= pc_h($pc['train_about_hero_title']) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc['train_about_hero_subtitle']) ?></p>
                    <div class="section-divider"></div>
                </div>

                <!-- Training Introduction -->
                <div class="row justify-content-center mb-4">
                    <div class="col-lg-10">
                        <?php
                        // Render intro paragraphs centered+muted (preserves original look)
                        foreach (preg_split("/\n{2,}/", trim((string)$pc['train_about_intro_body'])) as $para):
                            $para = trim($para);
                            if ($para === '') continue;
                        ?>
                        <p class="text-muted text-center"><?= pc_h($para) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Training Formats -->
                <div class="row g-3 justify-content-center mb-4">
                    <div class="col-md-5">
                        <div class="training-format-card">
                            <div class="format-tag"><?= pc_h($pc['train_about_format_1_tag']) ?></div>
                            <p class="format-duration"><?= pc_h($pc['train_about_format_1_duration']) ?></p>
                            <p class="format-audience"><?= pc_h($pc['train_about_format_1_audience']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="training-format-card">
                            <div class="format-tag"><?= pc_h($pc['train_about_format_2_tag']) ?></div>
                            <p class="format-duration"><?= pc_h($pc['train_about_format_2_duration']) ?></p>
                            <p class="format-audience"><?= pc_h($pc['train_about_format_2_audience']) ?></p>
                        </div>
                    </div>
                </div>
                <p class="text-muted text-center small mb-5">
                    <?= pc_h($pc['train_about_format_note']) ?>
                </p>

                <!-- Training Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 justify-content-center">
                    <?php
                    $course_modal_targets = [
                        1 => 'qualityModal',
                        2 => 'healthModal',
                        3 => 'environmentModal',
                        4 => 'agricultureModal',
                        5 => 'wellnessModal',
                        6 => 'foodModal',
                        7 => 'auditingModal',
                    ];
                    foreach ($course_modal_targets as $i => $modal_id):
                        $img_key = 'train_about_course_' . $i . '_image';
                        $alt_key = 'train_about_course_' . $i . '_alt';
                        $name_key = 'train_about_course_' . $i . '_name';
                        $img_default = $train_about_defaults[$img_key];
                    ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm rounded-3 text-center transition-all hover-lift">
                            <div class="add2cart_image">
                                <img src="<?= pc_h(pc_image_src($pc[$img_key], $img_default)) ?>" alt="<?= pc_h($pc[$alt_key]) ?>" class="img-fluid rounded-top" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                            </div>
                            <div class="add2cart_details p-4">
                                <div class="con_cont">
                                    <a style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>" class="add2cart_prod_name d-block mb-2 fw-bold"><?= pc_h($pc[$name_key]) ?></a>
                                    <a data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>" class="add2cart_btn btn btn-primary btn-sm"><i class="ico-cart me-1"></i>View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Training Modals -->
        <?php foreach ($course_modal_targets as $i => $modal_id):
            $title_key    = 'train_about_course_' . $i . '_modal_title';
            $overview_key = 'train_about_course_' . $i . '_overview';
            $benefits_key = 'train_about_course_' . $i . '_benefits';
            $courses_key  = 'train_about_course_' . $i . '_courses';
            $duration_key = 'train_about_course_' . $i . '_duration';
        ?>
        <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1" aria-labelledby="<?= $modal_id ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="<?= $modal_id ?>Label"><?= pc_h($pc[$title_key]) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="course-details">
                            <h5>Course Overview</h5>
                            <p><?= pc_h($pc[$overview_key]) ?></p>

                            <h5>Key Benefits</h5>
                            <ul>
                                <?= train_about_list_items($pc[$benefits_key]) ?>
                            </ul>

                            <h5>Available Courses</h5>
                            <ul>
                                <?= train_about_list_items($pc[$courses_key]) ?>
                            </ul>

                            <h5>Duration &amp; Format</h5>
                            <p><?= pc_h($pc[$duration_key]) ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-enroll">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Why Train With ESWASA Section -->
        <section class="why-train-section">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold"><?= pc_h($pc['train_about_why_title']) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc['train_about_why_subtitle']) ?></p>
                    <div class="section-divider"></div>
                </div>

                <div class="why-train-grid">
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M24 14 V40"/>
                                <path d="M24 14 C18 11 12 11 8 13 L8 38 C12 36 18 36 24 40"/>
                                <path d="M24 14 C30 11 36 11 40 13 L40 38 C36 36 30 36 24 40"/>
                                <path d="M30 6 L33 9 L38 4" stroke-width="2.5"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_1_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_1_body']) ?></p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="24" cy="10" r="4" fill="currentColor"/>
                                <circle cx="10" cy="32" r="4" fill="currentColor"/>
                                <circle cx="38" cy="32" r="4" fill="currentColor"/>
                                <line x1="14" y1="30" x2="20" y2="14"/>
                                <line x1="34" y1="30" x2="28" y2="14"/>
                                <line x1="15" y1="32" x2="33" y2="32"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_2_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_2_body']) ?></p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 4 L20 14"/>
                                <path d="M34 4 L28 14"/>
                                <path d="M20 4 L24 12"/>
                                <path d="M28 4 L24 12"/>
                                <circle cx="24" cy="28" r="12"/>
                                <polygon points="24,22 26,27 31,27 27,30 28,35 24,33 20,35 21,30 17,27 22,27" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_3_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_3_body']) ?></p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="6" y1="14" x2="42" y2="14"/>
                                <circle cx="34" cy="14" r="3.5" fill="currentColor"/>
                                <line x1="6" y1="24" x2="42" y2="24"/>
                                <circle cx="14" cy="24" r="3.5" fill="currentColor"/>
                                <line x1="6" y1="34" x2="42" y2="34"/>
                                <circle cx="26" cy="34" r="3.5" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_4_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_4_body']) ?></p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 42 H42"/>
                                <path d="M6 42 V8"/>
                                <polyline points="10 32 18 24 26 28 38 12"/>
                                <polyline points="32 12 38 12 38 18"/>
                                <circle cx="18" cy="24" r="2" fill="currentColor"/>
                                <circle cx="26" cy="28" r="2" fill="currentColor"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_5_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_5_body']) ?></p>
                    </div>
                    <div class="why-train-card">
                        <div class="why-train-icon" aria-hidden="true">
                            <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="24" cy="14" r="5" fill="currentColor"/>
                                <path d="M14 36 V32 C14 26 18 22 24 22 C30 22 34 26 34 32 V36"/>
                                <line x1="14" y1="36" x2="34" y2="36"/>
                                <path d="M40 18 Q44 24 40 30"/>
                                <path d="M8 18 Q4 24 8 30"/>
                            </svg>
                        </div>
                        <h4><?= pc_h($pc['train_about_why_6_title']) ?></h4>
                        <p><?= pc_h($pc['train_about_why_6_body']) ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Training Policies Section -->
        <section id="training_policies_section" class="content_section bg_fixed bg11 bg_gray border_b_n py-5">
            <div class="content row_spacer clearfix">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5 text-center">
                    <h2 class="display-6 fw-bold"><?= pc_h($pc['train_about_policies_title']) ?></h2>
                    <p class="text-muted mt-2 mb-0"><?= pc_h($pc['train_about_policies_subtitle']) ?></p>
                    <div class="section-divider"></div>
                </div>

                <!-- Tabs Navigation -->
                <div class="container">
                    <ul class="nav nav-tabs justify-content-center mb-4" id="policiesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="application-tab" data-bs-toggle="tab" data-bs-target="#application" type="button" role="tab" aria-controls="application" aria-selected="true"><?= pc_h($pc['train_about_policy_application_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="acceptance-tab" data-bs-toggle="tab" data-bs-target="#acceptance" type="button" role="tab" aria-controls="acceptance" aria-selected="false"><?= pc_h($pc['train_about_policy_acceptance_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cancellations-tab" data-bs-toggle="tab" data-bs-target="#cancellations" type="button" role="tab" aria-controls="cancellations" aria-selected="false"><?= pc_h($pc['train_about_policy_cancellations_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fees-tab" data-bs-toggle="tab" data-bs-target="#fees" type="button" role="tab" aria-controls="fees" aria-selected="false"><?= pc_h($pc['train_about_policy_fees_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="travel-tab" data-bs-toggle="tab" data-bs-target="#travel" type="button" role="tab" aria-controls="travel" aria-selected="false"><?= pc_h($pc['train_about_policy_travel_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="inhouse-tab" data-bs-toggle="tab" data-bs-target="#inhouse" type="button" role="tab" aria-controls="inhouse" aria-selected="false"><?= pc_h($pc['train_about_policy_inhouse_tab']) ?></button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assessments-tab" data-bs-toggle="tab" data-bs-target="#assessments" type="button" role="tab" aria-controls="assessments" aria-selected="false"><?= pc_h($pc['train_about_policy_assessments_tab']) ?></button>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content" id="policiesTabContent">
                        <div class="tab-pane fade show active" id="application" role="tabpanel" aria-labelledby="application-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                        <path d="M28 6 V16 H38"/>
                                        <line x1="16" y1="24" x2="32" y2="24"/>
                                        <line x1="16" y1="30" x2="32" y2="30"/>
                                        <line x1="16" y1="36" x2="26" y2="36"/>
                                        <path d="M30 36 L36 30" stroke-width="2.5"/>
                                        <circle cx="36" cy="30" r="1.5" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_application_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_application_body']) ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="acceptance" role="tabpanel" aria-labelledby="acceptance-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H28 L38 16 V42 H10 Z"/>
                                        <path d="M28 6 V16 H38"/>
                                        <circle cx="24" cy="30" r="9"/>
                                        <path d="M20 30 L23 33 L29 26" stroke-width="2.5"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_acceptance_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_acceptance_body']) ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="cancellations" role="tabpanel" aria-labelledby="cancellations-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="6" y="10" width="36" height="32" rx="2"/>
                                        <line x1="6" y1="18" x2="42" y2="18"/>
                                        <line x1="14" y1="6" x2="14" y2="14"/>
                                        <line x1="34" y1="6" x2="34" y2="14"/>
                                        <line x1="18" y1="26" x2="30" y2="36" stroke-width="2.5"/>
                                        <line x1="30" y1="26" x2="18" y2="36" stroke-width="2.5"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_cancellations_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_cancellations_body']) ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="fees" role="tabpanel" aria-labelledby="fees-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="4" y="12" width="40" height="24" rx="3"/>
                                        <circle cx="9" cy="17" r="1" fill="currentColor"/>
                                        <circle cx="39" cy="31" r="1" fill="currentColor"/>
                                        <circle cx="24" cy="24" r="7"/>
                                        <line x1="24" y1="19" x2="24" y2="29"/>
                                        <path d="M27 21 Q25 20 22.5 22 Q23 24 25 24 Q27 24 27.5 26 Q26 28 21 27"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_fees_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_fees_body']) ?></p>
                                <div class="bank-details">
                                    <h4><?= pc_h($pc['train_about_bank_title']) ?></h4>
                                    <dl>
                                        <dt>Bank Name</dt><dd><?= pc_h($pc['train_about_bank_name']) ?></dd>
                                        <dt>Account Name</dt><dd><?= pc_h($pc['train_about_bank_account_name']) ?></dd>
                                        <dt>Account Number</dt><dd><?= pc_h($pc['train_about_bank_account_number']) ?></dd>
                                        <dt>Branch Code</dt><dd><?= pc_h($pc['train_about_bank_branch_code']) ?></dd>
                                        <dt>Branch Name</dt><dd><?= pc_h($pc['train_about_bank_branch_name']) ?></dd>
                                        <dt>SWIFT Code</dt><dd><?= pc_h($pc['train_about_bank_swift']) ?></dd>
                                    </dl>
                                    <p class="mb-0"><?= pc_h($pc['train_about_bank_note']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="travel" role="tabpanel" aria-labelledby="travel-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="6" y="14" width="36" height="28" rx="2"/>
                                        <path d="M18 14 V10 C18 8.9 18.9 8 20 8 H28 C29.1 8 30 8.9 30 10 V14"/>
                                        <line x1="6" y1="26" x2="42" y2="26"/>
                                        <rect x="22" y="24" width="4" height="4" fill="currentColor"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_travel_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_travel_body']) ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="inhouse" role="tabpanel" aria-labelledby="inhouse-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 42 V14 L24 6 L40 14 V42 Z"/>
                                        <rect x="13" y="20" width="5" height="5"/>
                                        <rect x="30" y="20" width="5" height="5"/>
                                        <rect x="13" y="28" width="5" height="5"/>
                                        <rect x="30" y="28" width="5" height="5"/>
                                        <rect x="21" y="34" width="6" height="8"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_inhouse_title']) ?></h3>
                                <p><?= pc_h($pc['train_about_policy_inhouse_body']) ?></p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="assessments" role="tabpanel" aria-labelledby="assessments-tab">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-center">
                                <span class="policy-icon d-inline-block mb-3" aria-hidden="true">
                                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10 6 H32 L40 14 V42 H10 Z"/>
                                        <path d="M32 6 V14 H40"/>
                                        <path d="M16 22 H34"/>
                                        <path d="M16 28 H34"/>
                                        <path d="M16 34 H26"/>
                                    </svg>
                                </span>
                                <h3 class="fw-bold"><?= pc_h($pc['train_about_policy_assessments_title']) ?></h3>

                                <div class="row g-4 mt-2 text-start assessments-grid" style="max-width: 920px; margin: 0 auto;">
                                    <div class="col-md-6">
                                        <div class="assessment-card">
                                            <span class="assessment-card-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M9 11l3 3L22 4"/>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                                </svg>
                                            </span>
                                            <h5 class="assessment-card-title"><?= pc_h($pc['train_about_assess_eval_title']) ?></h5>
                                            <ul class="assessment-list">
                                                <?= train_about_list_items($pc['train_about_assess_eval_list']) ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="assessment-card">
                                            <span class="assessment-card-icon" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="8" r="6"/>
                                                    <path d="M15.5 13.5L17 22l-5-3-5 3 1.5-8.5"/>
                                                </svg>
                                            </span>
                                            <h5 class="assessment-card-title"><?= pc_h($pc['train_about_assess_cert_title']) ?></h5>
                                            <ul class="assessment-list">
                                                <?= train_about_list_items($pc['train_about_assess_cert_list']) ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="passing-mark-pill">
                                    <?= pc_h($pc['train_about_assess_pass_mark']) ?>
                                </div>
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

</body>
</html>

<?php
// admin/index.php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
error_reporting(E_ALL);

define('ADMIN_ROOT', __DIR__);

// Config
$configFile = ADMIN_ROOT . '/config.php';
if (!file_exists($configFile)) {
    error_log("Config file missing: $configFile");
    http_response_code(500);
    die("Internal server error. Config missing.");
}
require_once $configFile;

// Session + auth
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
requireLogin();

// Constant for includes security check
define('ESWASA_ADMIN', true);

// Allowed pages — must match actual files in admin/pages/.
// Stub pages (added by P2) cover sidebar links that previously 404'd.
$allowed_pages = [
    'about_edit.php',
    'about_team.php',
    'services_edit.php',
    'standards_edit.php',
    'certification_edit.php',
    'calibration_edit.php',
    'training_about.php',
    'training_calendar.php',
    'cert_status_edit.php',
    'contact_edit.php',
    'events_edit.php',
    'vacancies_edit.php',
    'tenders_edit.php',
    'announcements_edit.php',
    'faq_edit.php',
    'publications_edit.php',
    'breadcrumbs_edit.php',
    'service_charter.php',
    'customer_feedback.php',
    'policies_edit.php',
    'managementsystems.php',
    'product.php',
    'ingelo.php',
    'tcp.php',
    'work.php',
    'purchase.php',
    'qoute_training.php',
    'qoute_certification.php',
    'qoute_calibration.php',
    'users.php',
    'site_settings.php',
    'index_edit.php',
];

// Resolve page
$page = basename($_GET['page'] ?? 'index_edit.php');
if (!in_array($page, $allowed_pages)) {
    $page = 'index_edit.php';
}

// Load page content
$page_path = ADMIN_ROOT . '/pages/' . $page;
if (!file_exists($page_path)) {
    http_response_code(404);
    die('Page not found.');
}

ob_start();
include $page_path;
$content = ob_get_clean();

// Page title
$page_titles = [
    'about_edit.php'            => 'About Us',
    'about_team.php'            => 'Meet Our Team',
    'services_edit.php'         => 'Our Services',
    'standards_edit.php'        => 'Standards Development',
    'certification_edit.php'    => 'ESWASA Certification',
    'calibration_edit.php'      => 'Scales & Metrology',
    'training_about.php'        => 'About Trainings',
    'training_calendar.php'     => 'Training Calendar',
    'cert_status_edit.php'      => 'Certification Status Page',
    'contact_edit.php'          => 'Contact Us',
    'events_edit.php'           => 'Events',
    'vacancies_edit.php'        => 'Vacancies',
    'tenders_edit.php'          => 'Tenders',
    'announcements_edit.php'    => 'Announcements',
    'faq_edit.php'              => 'FAQ',
    'publications_edit.php'     => 'Publications',
    'breadcrumbs_edit.php'      => 'Breadcrumb Images',
    'service_charter.php'       => 'Service Charter',
    'customer_feedback.php'     => 'Customer Feedback',
    'policies_edit.php'         => 'Policies',
    'managementsystems.php'     => 'Management Systems',
    'product.php'               => 'Product Certification',
    'ingelo.php'                => 'Ingelo Certification',
    'tcp.php'                   => 'Technical Committee',
    'work.php'                  => 'Work Programmes',
    'purchase.php'              => 'Purchase Standards',
    'qoute_training.php'        => 'Training Quote Requests',
    'qoute_certification.php'   => 'Certification Quote Requests',
    'qoute_calibration.php'     => 'Calibration Quote Requests',
    'users.php'                 => 'Users',
    'site_settings.php'         => 'Site Settings',
    'index_edit.php'            => 'Home Page',
];
$page_title = $page_titles[$page] ?? ucfirst(str_replace(['.php','_'], ['', ' '], $page));

// Render layout
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<main id="mainContent" class="px-3 px-md-4 py-4">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?= $content ?>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
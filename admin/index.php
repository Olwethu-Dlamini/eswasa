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

// Allowed pages
$allowed_pages = [
    'dashboard.php',
    'about_edit.php',
    'services_edit.php',
    'standards_edit.php',
    'certification_edit.php',
    'calibration_edit.php',
    'training_edit.php',
    'updates_edit.php',
    'contact_edit.php',
    'events_edit.php',
    'vacancies_edit.php',
    'announcements_edit.php',
    'faq_edit.php',
    'publications_edit.php',
    'breadcrumbs_edit.php',
];

// Resolve page
$page = basename($_GET['page'] ?? 'dashboard.php');
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard.php';
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
    'dashboard.php'          => 'Dashboard',
    'about_edit.php'         => 'About Us',
    'services_edit.php'      => 'Our Services',
    'standards_edit.php'     => 'Standards',
    'certification_edit.php' => 'Certification',
    'calibration_edit.php'   => 'Calibration',
    'training_edit.php'      => 'Training',
    'updates_edit.php'       => 'Updates',
    'contact_edit.php'       => 'Contact Us',
    'events_edit.php'        => 'Events',
    'vacancies_edit.php'     => 'Vacancies',
    'announcements_edit.php' => 'Announcements',
    'faq_edit.php'           => 'FAQ',
    'publications_edit.php'  => 'Publications',
    'breadcrumbs_edit.php'   => 'Breadcrumb Images',
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
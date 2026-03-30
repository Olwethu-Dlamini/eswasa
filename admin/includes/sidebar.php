<?php
// admin/includes/sidebar.php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$current_page = basename($_GET['page'] ?? 'dashboard.php');

function nav_link($page, $current, $icon, $label) {
    $active = ($current === $page) ? 'active' : '';
    return '<a class="nav-link '.$active.'" href="index.php?page='.$page.'">
        <i class="fas '.$icon.' fa-fw me-2"></i><span>'.$label.'</span></a>';
}
function is_active_group($pages, $current) {
    return in_array($current, $pages) ? 'active' : '';
}
function submenu_open($pages, $current) {
    return in_array($current, $pages) ? 'show' : '';
}
?>
<nav id="sidebar" class="bg-body border-end">
    <div class="sidebar-inner">
        <ul class="nav flex-column pt-2">

            <li class="nav-item">
                <?= nav_link('dashboard.php', $current_page, 'fa-home', 'Dashboard') ?>
            </li>

            <li class="nav-item">
                <?= nav_link('breadcrumbs_edit.php', $current_page, 'fa-image', 'Breadcrumb Images') ?>
            </li>

            <li class="nav-item">
                <?= nav_link('services_edit.php', $current_page, 'fa-handshake', 'Our Services') ?>
            </li>

            <!-- About Us -->
            <li class="nav-item">
                <a class="nav-link <?= is_active_group(['about_edit.php','about_team.php'], $current_page) ?> d-flex justify-content-between"
                   href="#submenu-about" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['about_edit.php','about_team.php']) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-info-circle fa-fw me-2"></i>About Us</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open(['about_edit.php','about_team.php'], $current_page) ?>" id="submenu-about">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='about_edit.php'?'active':'' ?>" href="index.php?page=about_edit.php">Who Are We</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='about_team.php'?'active':'' ?>" href="index.php?page=about_team.php">Meet Our Team</a></li>
                </ul>
            </li>

            <!-- Training -->
            <li class="nav-item">
                <a class="nav-link <?= is_active_group(['training_about.php','training_calendar.php','qoute_training.php'], $current_page) ?> d-flex justify-content-between"
                   href="#submenu-training" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['training_about.php','training_calendar.php','qoute_training.php']) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-chalkboard-teacher fa-fw me-2"></i>Training</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open(['training_about.php','training_calendar.php','qoute_training.php'], $current_page) ?>" id="submenu-training">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='training_about.php'?'active':'' ?>" href="index.php?page=training_about.php">About Trainings</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='training_calendar.php'?'active':'' ?>" href="index.php?page=training_calendar.php">Training Calendar</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='qoute_training.php'?'active':'' ?>" href="index.php?page=qoute_training.php">Request Quotation</a></li>
                </ul>
            </li>

            <!-- Certification -->
            <?php $cert_pages = ['certification_edit.php','managementsystems.php','product.php','ingelo.php','qoute_certification.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($cert_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-cert" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $cert_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-award fa-fw me-2"></i>Certification</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($cert_pages, $current_page) ?>" id="submenu-cert">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='certification_edit.php'?'active':'' ?>" href="index.php?page=certification_edit.php">ESWASA Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='managementsystems.php'?'active':'' ?>" href="index.php?page=managementsystems.php">Management Systems</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='product.php'?'active':'' ?>" href="index.php?page=product.php">Product Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='ingelo.php'?'active':'' ?>" href="index.php?page=ingelo.php">Ingelo Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='qoute_certification.php'?'active':'' ?>" href="index.php?page=qoute_certification.php">Request Quotation</a></li>
                </ul>
            </li>

            <!-- Calibration -->
            <li class="nav-item">
                <a class="nav-link <?= is_active_group(['calibration_edit.php','qoute_calibration.php'], $current_page) ?> d-flex justify-content-between"
                   href="#submenu-cal" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['calibration_edit.php','qoute_calibration.php']) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-tools fa-fw me-2"></i>Calibration</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open(['calibration_edit.php','qoute_calibration.php'], $current_page) ?>" id="submenu-cal">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='calibration_edit.php'?'active':'' ?>" href="index.php?page=calibration_edit.php">Scales &amp; Metrology</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='qoute_calibration.php'?'active':'' ?>" href="index.php?page=qoute_calibration.php">Request Quotation</a></li>
                </ul>
            </li>

            <!-- Standards -->
            <?php $std_pages = ['standards_edit.php','tcp.php','work.php','purchase.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($std_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-standards" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $std_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-balance-scale fa-fw me-2"></i>Standards</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($std_pages, $current_page) ?>" id="submenu-standards">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='standards_edit.php'?'active':'' ?>" href="index.php?page=standards_edit.php">Standards Development</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='tcp.php'?'active':'' ?>" href="index.php?page=tcp.php">Technical Committee</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='work.php'?'active':'' ?>" href="index.php?page=work.php">Work Programmes</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='purchase.php'?'active':'' ?>" href="index.php?page=purchase.php">Purchase Standards</a></li>
                </ul>
            </li>

            <!-- Updates -->
            <?php $upd_pages = ['events_edit.php','vacancies_edit.php','publications_edit.php','announcements_edit.php','faq_edit.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($upd_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-updates" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $upd_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-bullhorn fa-fw me-2"></i>Updates</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($upd_pages, $current_page) ?>" id="submenu-updates">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='events_edit.php'?'active':'' ?>" href="index.php?page=events_edit.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='vacancies_edit.php'?'active':'' ?>" href="index.php?page=vacancies_edit.php">Vacancies</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='publications_edit.php'?'active':'' ?>" href="index.php?page=publications_edit.php">Publications</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='announcements_edit.php'?'active':'' ?>" href="index.php?page=announcements_edit.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='faq_edit.php'?'active':'' ?>" href="index.php?page=faq_edit.php">FAQ</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <?= nav_link('contact_edit.php', $current_page, 'fa-envelope', 'Contact Us') ?>
            </li>

            <li class="nav-item mt-3 border-top pt-3">
                <!-- Theme toggle -->
                <div class="px-3 d-flex align-items-center justify-content-between mb-2">
                    <small class="text-muted">Dark Mode</small>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" id="themeSwitch" role="switch">
                    </div>
                </div>
                <a class="nav-link text-danger" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-fw me-2"></i><span>Logout</span>
                </a>
            </li>

        </ul>
    </div>
</nav>
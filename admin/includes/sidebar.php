<?php
// admin/includes/sidebar.php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$current_page = basename($_GET['page'] ?? 'index_edit.php');

require_once __DIR__ . '/../../includes/form_inboxes.php';

function nav_link($page, $current, $icon, $label, $badge_html = '') {
    $active = ($current === $page) ? 'active' : '';
    return '<a class="nav-link d-flex align-items-center '.$active.'" href="index.php?page='.$page.'">
        <i class="fas '.$icon.' fa-fw me-2"></i><span>'.$label.'</span>'.$badge_html.'</a>';
}

// Unread counts for every form inbox (includes/form_inboxes.php). Best-effort:
// a missing table counts as 0 and never breaks the nav. Email is unreliable
// on shared hosting, so these badges are the dependable signal that something
// new has arrived. See spec item A2.
$inbox_counts = $inbox_counts ?? eswasa_inbox_counts($conn); // header.php has usually fetched them

// A red counter for one inbox, or the total of several (a menu group).
// Rendered even at 0, hidden, so admin/js/inbox.js and the notification bell
// can show it the moment something arrives or update it after one is opened.
function inbox_badge(array $counts, array $keys, string $extra = 'ms-auto') {
    $n = 0;
    foreach ($keys as $k) {
        $n += (int)($counts[$k] ?? 0);
    }
    $attr = count($keys) === 1
        ? 'data-inbox-count="' . htmlspecialchars($keys[0]) . '"'
        : 'data-inbox-count-sum="' . htmlspecialchars(implode(',', $keys)) . '"';
    return '<span class="badge bg-danger rounded-pill ' . $extra . ($n > 0 ? '' : ' d-none') . '" ' . $attr
        . ' title="New, not yet opened">' . $n . '</span>';
}

// A submenu link, with an optional unread counter.
function sub_link($page, $current, $label, $badge_html = '') {
    $active = $current === $page ? 'active' : '';
    return '<a class="nav-link d-flex align-items-center ' . $active . '" href="index.php?page=' . $page . '">'
        . '<span>' . $label . '</span>' . $badge_html . '</a>';
}

// The chevron end of a menu group's toggle, with the group's total.
function group_end(array $counts, array $keys = []) {
    return '<span class="d-flex align-items-center gap-2">'
        . ($keys ? inbox_badge($counts, $keys, '') : '')
        . '<i class="fas fa-chevron-down small"></i></span>';
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
                <?= nav_link('index_edit.php', $current_page, 'fa-house-user', 'Home Page') ?>
            </li>

            <li class="nav-item">
                <?= nav_link('breadcrumbs_edit.php', $current_page, 'fa-image', 'Breadcrumb Images') ?>
            </li>

            <!-- About Us -->
            <!-- Sidebar order follows the public nav: Home, About Us, Our
                 Services, Training, Certification, Calibration, Standards,
                 Updates, Customer Care, Contact Us. Our Services used to sit
                 above About Us here, which is the one place the two diverged.
                 Breadcrumb Images stays pinned under Home: it is a
                 cross-cutting tool rather than a page, so it has no
                 counterpart in the public nav.
                 See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (B1). -->
            <?php $about_pages = ['about_edit.php','about_team.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($about_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-about" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $about_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-info-circle fa-fw me-2"></i>About Us</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($about_pages, $current_page) ?>" id="submenu-about">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='about_edit.php'?'active':'' ?>" href="index.php?page=about_edit.php">Who Are We</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='about_team.php'?'active':'' ?>" href="index.php?page=about_team.php">Meet Our Team</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <?= nav_link('services_edit.php', $current_page, 'fa-handshake', 'Our Services') ?>
            </li>

            <!-- Training -->
            <li class="nav-item">
                <a class="nav-link <?= is_active_group(['training_about.php','training_calendar.php','training_applications.php','qoute_training.php'], $current_page) ?> d-flex justify-content-between"
                   href="#submenu-training" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['training_about.php','training_calendar.php','training_applications.php','qoute_training.php']) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-chalkboard-teacher fa-fw me-2"></i>Training</span>
                    <?= group_end($inbox_counts, ['training_application', 'quote_training']) ?>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open(['training_about.php','training_calendar.php','training_applications.php','qoute_training.php'], $current_page) ?>" id="submenu-training">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='training_about.php'?'active':'' ?>" href="index.php?page=training_about.php">About Trainings</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='training_calendar.php'?'active':'' ?>" href="index.php?page=training_calendar.php">Training Calendar</a></li>
                    <li class="nav-item"><?= sub_link('training_applications.php', $current_page, 'Applications', inbox_badge($inbox_counts, ['training_application'])) ?></li>
                    <li class="nav-item"><?= sub_link('qoute_training.php', $current_page, 'Request Quotation', inbox_badge($inbox_counts, ['quote_training'])) ?></li>
                </ul>
            </li>

            <!-- Certification -->
            <?php $cert_pages = ['certification_edit.php','managementsystems.php','product.php','ingelo.php','cert_status_edit.php','qoute_certification.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($cert_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-cert" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $cert_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-award fa-fw me-2"></i>Certification</span>
                    <?= group_end($inbox_counts, ['quote_certification']) ?>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($cert_pages, $current_page) ?>" id="submenu-cert">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='certification_edit.php'?'active':'' ?>" href="index.php?page=certification_edit.php">ESWASA Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='managementsystems.php'?'active':'' ?>" href="index.php?page=managementsystems.php">Management Systems</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='product.php'?'active':'' ?>" href="index.php?page=product.php">Product Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='ingelo.php'?'active':'' ?>" href="index.php?page=ingelo.php">Ingelo Certification</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='cert_status_edit.php'?'active':'' ?>" href="index.php?page=cert_status_edit.php">Certification Status Registers</a></li>
                    <li class="nav-item"><?= sub_link('qoute_certification.php', $current_page, 'Request Quotation', inbox_badge($inbox_counts, ['quote_certification'])) ?></li>
                </ul>
            </li>

            <!-- Calibration -->
            <li class="nav-item">
                <a class="nav-link <?= is_active_group(['calibration_edit.php','qoute_calibration.php'], $current_page) ?> d-flex justify-content-between"
                   href="#submenu-cal" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, ['calibration_edit.php','qoute_calibration.php']) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-tools fa-fw me-2"></i>Calibration</span>
                    <?= group_end($inbox_counts, ['quote_calibration']) ?>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open(['calibration_edit.php','qoute_calibration.php'], $current_page) ?>" id="submenu-cal">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='calibration_edit.php'?'active':'' ?>" href="index.php?page=calibration_edit.php">Scales &amp; Metrology</a></li>
                    <li class="nav-item"><?= sub_link('qoute_calibration.php', $current_page, 'Request Quotation', inbox_badge($inbox_counts, ['quote_calibration'])) ?></li>
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
            <?php $upd_pages = ['events_edit.php','vacancies_edit.php','tenders_edit.php','publications_edit.php','announcements_edit.php','faq_edit.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($upd_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-updates" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $upd_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-bullhorn fa-fw me-2"></i>Updates</span>
                    <i class="fas fa-chevron-down small mt-1"></i>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($upd_pages, $current_page) ?>" id="submenu-updates">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='events_edit.php'?'active':'' ?>" href="index.php?page=events_edit.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='vacancies_edit.php'?'active':'' ?>" href="index.php?page=vacancies_edit.php">Vacancies</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='tenders_edit.php'?'active':'' ?>" href="index.php?page=tenders_edit.php">Tenders</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='publications_edit.php'?'active':'' ?>" href="index.php?page=publications_edit.php">Publications</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='announcements_edit.php'?'active':'' ?>" href="index.php?page=announcements_edit.php">Announcements</a></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='faq_edit.php'?'active':'' ?>" href="index.php?page=faq_edit.php">FAQ</a></li>
                </ul>
            </li>

            <!-- Customer Care -->
            <?php $cc_pages = ['service_charter.php','customer_feedback.php','policies_edit.php']; ?>
            <li class="nav-item">
                <a class="nav-link <?= is_active_group($cc_pages, $current_page) ?> d-flex justify-content-between"
                   href="#submenu-cc" data-bs-toggle="collapse" aria-expanded="<?= in_array($current_page, $cc_pages) ? 'true' : 'false' ?>">
                    <span><i class="fas fa-headset fa-fw me-2"></i>Customer Care</span>
                    <?= group_end($inbox_counts, ['feedback']) ?>
                </a>
                <ul class="nav flex-column ms-3 collapse <?= submenu_open($cc_pages, $current_page) ?>" id="submenu-cc">
                    <li class="nav-item"><a class="nav-link <?= $current_page==='service_charter.php'?'active':'' ?>" href="index.php?page=service_charter.php">Service Charter</a></li>
                    <li class="nav-item"><?= sub_link('customer_feedback.php', $current_page, 'Customer Feedback', inbox_badge($inbox_counts, ['feedback'])) ?></li>
                    <li class="nav-item"><a class="nav-link <?= $current_page==='policies_edit.php'?'active':'' ?>" href="index.php?page=policies_edit.php">Policies</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <?= nav_link('contact_edit.php', $current_page, 'fa-envelope', 'Contact Us', inbox_badge($inbox_counts, ['contact'])) ?>
            </li>

            <li class="nav-item">
                <?= nav_link('qoute_other.php', $current_page, 'fa-inbox', 'General Quotes', inbox_badge($inbox_counts, ['quote_other'])) ?>
            </li>

            <li class="nav-item">
                <?= nav_link('users.php', $current_page, 'fa-user-shield', 'Users') ?>
            </li>

            <li class="nav-item">
                <?= nav_link('activity_log.php', $current_page, 'fa-clipboard-list', 'Activity Log') ?>
            </li>

            <li class="nav-item">
                <?= nav_link('site_settings.php', $current_page, 'fa-gear', 'Site Settings') ?>
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
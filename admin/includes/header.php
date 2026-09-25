<?php
// admin/header.php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$current_user = getCurrentUser($conn);

// Unread submissions, for the notification bell below and the sidebar.
require_once __DIR__ . '/../../includes/form_inboxes.php';
$inbox_counts = eswasa_inbox_counts($conn);
$inbox_total  = array_sum($inbox_counts);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Dashboard') ?> — ESWASA Admin</title>
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/favicon.png">
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Cropper.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <!-- Shared cropper modal (binds to any input.crop-input) -->
    <script src="js/cropper-modal.js" defer></script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top border-bottom">
        <div class="container-fluid">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-outline-secondary me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars fa-lg"></i>
            </button>

            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php?page=index_edit.php">
                <img src="../assets/img/logo/ESWASALOGO.png"
                     alt="ESWASA"
                     class="navbar-logo"
                     onerror="this.src='../assets/img/logo/ESWASA_LOGO.jpg'; this.onerror=null;">
            </a>

            <!-- Right nav -->
            <div class="ms-auto d-flex align-items-center">
                <!-- Notification bell: new form submissions, checked about once
                     a minute by js/notifier.js. -->
                <div class="dropdown me-2" id="notifier">
                    <button class="btn nav-link position-relative px-2" type="button" id="notifierToggle"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static" aria-expanded="false"
                            aria-label="New submissions: <?= (int)$inbox_total ?>">
                        <i class="fas fa-bell fa-lg"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?= $inbox_total > 0 ? '' : ' d-none' ?>"
                              data-notifier-total><?= (int)$inbox_total ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0 notifier-menu" aria-labelledby="notifierToggle">
                        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                            <strong>New submissions</strong>
                            <button type="button" class="btn btn-sm btn-link p-0 d-none" data-notifier-desktop>Desktop alerts: off</button>
                        </div>
                        <div class="notifier-list" data-notifier-list>
                            <div class="px-3 py-4 text-center text-muted small">Loading&hellip;</div>
                        </div>
                        <div class="px-3 py-2 border-top small text-muted" data-notifier-status>Checked every minute.</div>
                    </div>
                </div>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2"
                             style="width:40px;height:40px;flex-shrink:0;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <span class="d-none d-md-inline fs-6"><?= htmlspecialchars($current_user['username'] ?? 'Admin') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                Signed in as<br>
                                <strong><?= htmlspecialchars($current_user['username'] ?? 'Admin') ?></strong>
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page wrapper -->
    <div class="container-fluid">
        <div class="row flex-nowrap">
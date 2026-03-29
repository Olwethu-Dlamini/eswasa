<?php
// admin/header.php
if (!defined('ESWASA_ADMIN')) {
    exit('Direct access not permitted.');
}
$current_user = getCurrentUser($conn);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Dashboard') ?> — ESWASA Admin</title>
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="css/style.css" rel="stylesheet">
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
            <a class="navbar-brand d-flex align-items-center" href="index.php?page=dashboard.php">
                <img src="../assets/img/logo/ESWASALOGO.png"
                     alt="ESWASA"
                     class="navbar-logo"
                     onerror="this.src='../assets/img/logo/ESWASA_LOGO.jpg'; this.onerror=null;">
            </a>

            <!-- Right nav -->
            <div class="ms-auto d-flex align-items-center">
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
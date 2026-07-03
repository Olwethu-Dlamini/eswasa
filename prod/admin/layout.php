<?php
// Prevent direct access
if (!defined('ESWASA_ADMIN') || !defined('ADMIN_ROOT')) {
    exit('Direct access not permitted.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($page_title ?? 'Admin Dashboard') ?> - ESWASA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .square-img-preview {
      width: 120px;
      height: 120px;
      border-radius: 10px;
      background: #f2f2f2 center center/cover no-repeat;
      display: inline-block;
      margin-top: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .table-img {
      max-width: 120px;
      max-height: 70px;
      border-radius: 6px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<?php require_once ADMIN_ROOT . '/includes/header.php'; ?>

<div class="container-fluid">
  <div class="row">
    <?php require_once ADMIN_ROOT . '/includes/sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <!-- Global Flash Message (from set_flash()) -->
      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_SESSION['flash']['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <!-- Page-specific content -->
      <?= $content ?? '' ?>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-dismiss flash after 4 seconds -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const alertEl = document.querySelector('.alert[role="alert"]');
    if (alertEl) {
      setTimeout(() => {
        const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
        alert.close();
      }, 4000);
    }
  });
</script>

</body>
</html>
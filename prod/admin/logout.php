<?php
require_once 'config.php'; // starts session + DB connection
if (isset($_SESSION['user_id'])) {
    log_activity($conn, 'logout', 'users#' . (int)$_SESSION['user_id'], 'Signed out');
}
session_destroy();
header('Location: login.php');
exit();

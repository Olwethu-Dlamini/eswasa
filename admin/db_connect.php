<?php
// admin/db_connect.php — admin database connection.
// Credentials and environment come from includes/env.php (one place to edit).
require_once __DIR__ . '/../includes/env.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    if (APP_ENV === 'development') {
        die("Connection failed: " . $conn->connect_error);
    }
    error_log('DB connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('The admin area is temporarily unavailable. Please try again shortly.');
}
$conn->set_charset('utf8mb4');

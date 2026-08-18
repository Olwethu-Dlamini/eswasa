<?php
// admin/config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration + environment (errors, credentials) live in one
// place: includes/env.php. It defines DB_HOST / DB_USER / DB_PASS / DB_NAME.
require_once __DIR__ . '/../includes/env.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    if (APP_ENV === 'development') {
        die("Connection failed: " . $conn->connect_error);
    }
    error_log('DB connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('The admin area is temporarily unavailable. Please try again shortly.');
}
$conn->set_charset("utf8mb4");

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Function to redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Function to get current user info
function getCurrentUser($conn) {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    return null;
}

// Set a flash message in session
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Redirect back to the current admin page (preserves the ?page= and any
// other safe GET params). Built from SCRIPT_NAME instead of REQUEST_URI
// because some hosts strip the query string from REQUEST_URI after a POST.
function redirect_self() {
    $url = $_SERVER['SCRIPT_NAME']; // e.g. /admin/index.php
    $params = $_GET;

    // Drop one-shot action params so refreshing the redirect target doesn't
    // re-trigger them.
    //
    // This used to be a hand-maintained list, and delete_user was missing from
    // it. The consequence was not a missed cleanup but an infinite redirect:
    // users.php deleted the row, called redirect_self(), and the redirect
    // target still carried delete_user=N — so the handler ran again, and again,
    // until the browser gave up with ERR_TOO_MANY_REDIRECTS. The user was
    // deleted on the first pass, but the page never came back, so it looked
    // like deletion was broken. The production audit log shows 80 identical
    // user.delete rows written across two attempts, seconds apart.
    //
    // Matching any delete* parameter closes the whole class rather than this
    // one instance. Verified safe: of the 17 delete* handlers in the admin,
    // only two finish via redirect_self() (delete_quote and delete_user); the
    // rest use an explicit header('Location: ...') that never carries the
    // parameter forward.
    // See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A7.
    foreach (array_keys($params) as $key) {
        if (strpos($key, 'delete') === 0) {
            unset($params[$key]);
        }
    }
    unset($params['quote_sent']);
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}

// Record an entry in the audit trail (activity_log table). Best-effort:
// never throws and never blocks the action if logging fails (e.g. the table
// hasn't been migrated yet on a fresh host).
//   $action  short verb, e.g. 'login', 'user.create', 'content.save'
//   $entity  what was acted on, e.g. 'users#5' or 'Home Page'
//   $details optional human-readable extra context
function log_activity(mysqli $conn, string $action, ?string $entity = null, ?string $details = null) {
    $uid   = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $uname = $_SESSION['username'] ?? null;
    $ip    = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt  = @$conn->prepare(
        "INSERT INTO activity_log (user_id, username, action, entity, details, ip_address)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('isssss', $uid, $uname, $action, $entity, $details, $ip);
    $ok = @$stmt->execute();
    $stmt->close();
    return $ok;
}
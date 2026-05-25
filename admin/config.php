<?php
// admin/config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', "");
define('DB_NAME', 'eswasa');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    // Drop one-shot action params so refreshing the redirect target
    // doesn't re-trigger them.
    unset(
        $params['delete'],
        $params['delete_banner'],
        $params['delete_quote'],
        $params['quote_sent']
    );
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    header('Location: ' . $url);
    exit;
}
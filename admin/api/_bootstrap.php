<?php
/**
 * admin/api/_bootstrap.php — shared set-up for the admin's small JSON
 * endpoints, which are called by the admin's own scripts (admin/js/).
 *
 * Signed-in admins only. A signed-out caller gets 401 rather than the
 * redirect to the login page the admin pages use, so a script can tell an
 * expired session from a real answer.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../includes/form_inboxes.php';

// A PHP notice printed into the response would corrupt the JSON.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error.log');

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function api_reply(int $status, array $body): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if (!isLoggedIn()) {
    api_reply(401, ['ok' => false, 'error' => 'signed_out']);
}

// Only the admin's own scripts send this header. A page on another site
// cannot add it to a request without a CORS permission these endpoints never
// grant, so this stops a third-party page from driving them with the
// administrator's cookies.
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest') {
    api_reply(400, ['ok' => false, 'error' => 'bad_request']);
}

// The bell (notifier.js) asks every minute, and every request refreshes the
// session, so on its own it would keep a forgotten admin tab signed in for
// ever — on a shared computer, too. Only real use counts as activity: a page
// load (admin/index.php records it) or an endpoint that declares itself a
// user action. Past the idle limit the session is ended here, and the bell
// says the admin has been signed out.
$idle_limit = max(3600, (int)ini_get('session.gc_maxlifetime'));
if (isset($_SESSION['last_activity']) && time() - (int)$_SESSION['last_activity'] > $idle_limit) {
    $_SESSION = [];
    session_destroy();
    api_reply(401, ['ok' => false, 'error' => 'signed_out']);
}
if (defined('API_IS_USER_ACTION')) {
    $_SESSION['last_activity'] = time();
}

$api_user = (string)($_SESSION['username'] ?? 'admin');

// Nothing below writes to the session. Releasing it now means a slow reply
// here never holds up the admin page the person is loading at the same time.
session_write_close();

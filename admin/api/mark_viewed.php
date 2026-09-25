<?php
/**
 * admin/api/mark_viewed.php — POST inbox=<key>&id=<n>
 *
 * Called by admin/js/inbox.js when someone opens a submission, so its status
 * changes from New to viewed without anyone having to set it by hand. Only a
 * still-unread submission changes; anything further along is left alone.
 *
 * Replies with the fresh unread counts so the page can update its badges.
 */

require __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    api_reply(405, ['ok' => false, 'error' => 'method_not_allowed']);
}

$key = (string)($_POST['inbox'] ?? '');
$id  = (int)($_POST['id'] ?? 0);
if (!eswasa_inbox($key) || $id < 1) {
    api_reply(400, ['ok' => false, 'error' => 'unknown_submission']);
}

$changed = eswasa_mark_viewed($conn, $key, $id, $api_user);
$info = $changed ? eswasa_viewed_info($conn, $key, $id) : null;

api_reply(200, [
    'ok'        => true,
    'changed'   => $changed,
    'viewed_by' => $info ? (string)$info['read_by'] : $api_user,
    'viewed_at' => $info ? date('j M Y, H:i', strtotime($info['read_at'])) : '',
    'counts'    => eswasa_inbox_counts($conn),
]);

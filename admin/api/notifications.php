<?php
/**
 * admin/api/notifications.php — GET
 *
 * What the notification bell in the admin's top bar shows: how many
 * submissions are waiting in each inbox, and the newest of them. Polled
 * about once a minute by admin/js/notifier.js while the admin is open.
 */

require __DIR__ . '/_bootstrap.php';

$counts = eswasa_inbox_counts($conn);

api_reply(200, [
    'ok'     => true,
    'total'  => array_sum($counts),
    'counts' => $counts,
    'items'  => eswasa_inbox_latest($conn, 10),
]);

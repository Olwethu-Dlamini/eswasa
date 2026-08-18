<?php
/**
 * admin/pages/qoute_other.php — inbox for quote requests that could not be
 * attributed to one of the three services.
 *
 * process_quote.php files a request under "other" when it cannot determine the
 * source. Until now nothing listed those rows: the three inboxes each filter on
 * their own source, so an unattributed request was stored correctly and was
 * still completely invisible to staff.
 *
 * The forms now send an explicit quote_source field, which should keep this
 * inbox empty. It exists so that if attribution ever fails again, the request
 * surfaces somewhere instead of disappearing. The sidebar only links here while
 * there is something to see.
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A3.
 */
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

$quote_source_filter = 'other';
$quote_page_label    = 'Unsorted Quote Requests';
include __DIR__ . '/_quote_inbox.php';

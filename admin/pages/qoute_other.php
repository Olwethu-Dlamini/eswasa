<?php
/**
 * admin/pages/qoute_other.php — General Quote Requests.
 *
 * Two kinds of request land here:
 *   - requests from the general "Request a Quote" form (qoute.php) for a
 *     service without an inbox of its own — product testing, standards,
 *     technical assistance and so on. That form used to post nowhere; see
 *     process_quote.php.
 *   - any request process_quote.php could not attribute to a service. Before
 *     this inbox existed those were stored and then invisible, because the
 *     three service inboxes each filter on their own source.
 *
 * It used to be called "Unsorted Quote Requests" and was linked only while it
 * had something in it. Now that it receives real requests it is always
 * linked.
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A3.
 */
if (!defined('ESWASA_ADMIN')) exit('Direct access not permitted.');

$quote_source_filter = 'other';
$quote_page_label    = 'General Quote Requests';
include __DIR__ . '/_quote_inbox.php';

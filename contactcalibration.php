<?php
/**
 * contactcalibration.php — RETIRED, redirects to qoute_calibration.php
 *
 * This page used to carry its own "contact us" form. That form was left over
 * from the purchased site template and posted to a third-party endpoint
 * (bazardeal.com.bd), so every calibration enquiry submitted through it was
 * sent to an external server and never reached ESWASA.
 *
 * Calibration quote requests are handled by qoute_calibration.php, which
 * posts to process_quote.php and lands in the Calibration Quote Requests
 * inbox in the admin. The two pages duplicated each other, so this one is
 * retired rather than repaired.
 *
 * Kept as a 301 (rather than deleted) so existing bookmarks, printed
 * material and search-engine results continue to work.
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A1.
 */

header('Location: qoute_calibration.php', true, 301);
exit;

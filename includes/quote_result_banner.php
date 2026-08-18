<?php
/**
 * includes/quote_result_banner.php
 *
 * Renders the outcome of a quote-request submission at the top of the three
 * RFQ pages (qoute_training.php, qoute_certification.php,
 * qoute_calibration.php).
 *
 * Why this exists: process_quote.php always redirected back with
 * ?quote_sent=1, but none of the three pages ever read it. A visitor filled in
 * the form, submitted, and landed back on the same empty form with no
 * acknowledgement of any kind — so the reasonable conclusion was that the form
 * was broken, even though the submission had been stored correctly. This is
 * the "training quote page not working" report.
 *
 * Include once, immediately inside the page's main content area:
 *     <?php include __DIR__ . '/includes/quote_result_banner.php'; ?>
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md, item A3.
 */

if (!isset($_GET['quote_sent'])) {
    return;
}
?>
<style>
/* The site-wide theme sets `a, button { color: #fff }`, so links inside these
   pale Bootstrap alerts would render white on a light background — invisible
   until hovered. Take the alert's own text colour instead, underlined so they
   still read as links. Scoped to this banner so nothing else is affected. */
.quote-result a {
    color: inherit;
    text-decoration: underline;
    text-underline-offset: 2px;
    font-weight: 600;
}
.quote-result a:hover,
.quote-result a:focus {
    color: inherit;
    opacity: .78;
}
</style>
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$quote_ok = $_GET['quote_sent'] === '1';

// Attachment rejections are stashed in the session by process_quote.php,
// because the messages embed user-supplied filenames.
$quote_attach_errors = $_SESSION['quote_attachment_errors'] ?? [];
unset($_SESSION['quote_attachment_errors']);
?>
<div class="container quote-result">
    <?php if ($quote_ok): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading mb-1">
                <i class="fas fa-check-circle me-2"></i>Thank you &mdash; your request has been received
            </h5>
            <p class="mb-0">
                Our team will review it and get back to you with a quotation.
                If your enquiry is urgent, call us on
                <a href="tel:+26824041251">+268 2404 1251</a>.
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php else: ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading mb-1">
                <i class="fas fa-exclamation-triangle me-2"></i>We could not save your request
            </h5>
            <p class="mb-0">
                Something went wrong at our end. Please try again, or email us directly at
                <a href="mailto:info@eswasa.co.sz">info@eswasa.co.sz</a>.
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($quote_attach_errors)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Some attachments were not accepted:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($quote_attach_errors as $qerr): ?>
                    <li><?= htmlspecialchars((string)$qerr, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
            <p class="mb-0 mt-2 small">
                Everything else was submitted. Please email any missing documents to
                <a href="mailto:info@eswasa.co.sz">info@eswasa.co.sz</a>.
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

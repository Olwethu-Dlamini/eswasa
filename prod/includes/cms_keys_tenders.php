<?php
/**
 * Shared page_content key registry for tenders.php and its admin editor.
 *
 * Included by:
 *   - tenders.php (frontend)
 *   - admin/pages/tenders_edit.php (Page Content tab)
 */

$tenders_keys = [
    // Breadcrumb
    'tenders_breadcrumb_home_label',
    'tenders_breadcrumb_current_label',
    'tenders_breadcrumb_title',
    // Intro info-box
    'tenders_intro_title',
    'tenders_intro_body',
    // Section headings
    'tenders_open_title',
    'tenders_closed_title',
    // Empty state
    'tenders_empty_state',
];

$tenders_defaults = [
    'tenders_breadcrumb_home_label'    => 'Home',
    'tenders_breadcrumb_current_label' => 'Tenders',
    'tenders_breadcrumb_title'         => 'Tenders',

    'tenders_intro_title' => 'Procurement & Tenders',
    'tenders_intro_body'  => "The Eswatini Standards Authority (ESWASA) advertises its current procurement opportunities here. Each tender lists the submission deadline and the bid documents you need to download and complete.\n\nPlease ensure submissions reach us before the stated closing date. Late submissions will not be considered.",

    'tenders_open_title'   => 'Open Tenders',
    'tenders_closed_title' => 'Closed Tenders',

    'tenders_empty_state' => 'There are no open tenders at this time. Please check back later.',
];

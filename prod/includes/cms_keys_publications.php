<?php
/**
 * Shared page_content key registry for publications.php and its admin editor.
 *
 * Included by:
 *   - publications.php (frontend)
 *   - admin/pages/publications_edit.php (Page Content tab)
 */

$publications_keys = [
    // Breadcrumb
    'publications_breadcrumb_home_label',
    'publications_breadcrumb_current_label',
    'publications_breadcrumb_title',
    // Intro info-box
    'publications_intro_title',
    'publications_intro_body',
    // Section heading above the documents list
    'publications_section_title',
    // Empty state
    'publications_empty_state',
];

$publications_defaults = [
    'publications_breadcrumb_home_label'    => 'Home',
    'publications_breadcrumb_current_label' => 'Publications',
    'publications_breadcrumb_title'         => 'Publications',

    'publications_intro_title' => 'About ESWASA Publications',
    'publications_intro_body'  => "This section provides access to publications produced by the Eswatini Standards Authority (ESWASA). These include official standards documents, annual reports, technical guidelines, newsletters, and other relevant reports.\n\nAll documents are provided as PDF downloads.",

    'publications_section_title' => 'Available Documents',

    'publications_empty_state' => 'No publications are currently available.',
];

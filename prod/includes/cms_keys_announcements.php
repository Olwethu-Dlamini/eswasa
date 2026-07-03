<?php
/**
 * Shared page_content key registry for announcements.php and its admin editor.
 *
 * Included by:
 *   - announcements.php (frontend archive page)
 *   - admin/pages/announcements_edit.php (Page Content tab)
 */

$announcements_keys = [
    // Breadcrumb
    'announcements_breadcrumb_home_label',
    'announcements_breadcrumb_current_label',
    'announcements_breadcrumb_title',
    // Intro info-box
    'announcements_intro_title',
    'announcements_intro_body',
    // Section heading above the history list
    'announcements_section_title',
    // Empty state
    'announcements_empty_state',
];

$announcements_defaults = [
    'announcements_breadcrumb_home_label'    => 'Home',
    'announcements_breadcrumb_current_label' => 'Announcements',
    'announcements_breadcrumb_title'         => 'Announcements',

    'announcements_intro_title' => 'Announcement History',
    'announcements_intro_body'  => "A complete archive of news, updates, public-consultation notices, office closures, events, and policy announcements published by the Eswatini Standards Authority (ESWASA).\n\nThe headlines on the homepage strip link here so visitors can read the full text or download any referenced documents.",

    'announcements_section_title' => 'All Announcements',

    'announcements_empty_state' => 'No announcements have been published yet.',
];

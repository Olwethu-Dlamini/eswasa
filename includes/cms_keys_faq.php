<?php
/**
 * Shared page_content key registry for faq.php and its admin editor.
 *
 * Included by:
 *   - faq.php (frontend)
 *   - admin/pages/faq_edit.php (Page Content tab)
 *
 * The FAQ questions/answers themselves live in the dedicated eswasa_faq
 * table — this file is only for the surrounding static page text.
 */

$faq_keys = [
    // Breadcrumb
    'faq_breadcrumb_home_label',
    'faq_breadcrumb_current_label',
    'faq_breadcrumb_title',
    // Intro info-box
    'faq_intro_title',
    'faq_intro_body',
    // Empty state for the whole list
    'faq_empty_state',
    // Contact box at the bottom
    'faq_contact_title',
    'faq_contact_body',
    'faq_contact_phone',
    'faq_contact_email',
];

$faq_defaults = [
    'faq_breadcrumb_home_label'    => 'Home',
    'faq_breadcrumb_current_label' => 'FAQ',
    'faq_breadcrumb_title'         => 'Frequently Asked Questions',

    'faq_intro_title' => 'How can we help?',
    'faq_intro_body'  => "Answers to the questions ESWASA receives most often. If you can't find what you need below, the contact details at the bottom of the page will get you in touch with the right team.",

    'faq_empty_state' => 'No questions have been published yet.',

    'faq_contact_title' => 'Still have questions?',
    'faq_contact_body'  => "If you couldn't find the answer you needed, our team is ready to assist with any enquiry about ESWASA services, training, or certification.",
    'faq_contact_phone' => '+268 2518 4610',
    'faq_contact_email' => 'info@eswasa.co.sz',
];

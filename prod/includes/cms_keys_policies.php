<?php
/**
 * Shared page_content key registry for policies.php and its admin editor.
 *
 * The 12+ policy entries themselves live in eswasa_policies (CRUD via admin).
 * This file is only for the surrounding static page text.
 */

$policies_keys = [
    'policies_breadcrumb_home_label',
    'policies_breadcrumb_parent_label',
    'policies_breadcrumb_current_label',
    'policies_breadcrumb_title',

    'policies_section_title',
    'policies_intro_body',

    'policies_empty_state',
];

$policies_defaults = [
    'policies_breadcrumb_home_label'    => 'Home',
    'policies_breadcrumb_parent_label'  => 'Customer Care',
    'policies_breadcrumb_current_label' => 'Policies',
    'policies_breadcrumb_title'         => 'Policies & Procedures',

    'policies_section_title' => 'Our Public Policies',
    'policies_intro_body'    => 'These are the public policies and procedures that govern how ESWASA operates — covering impartiality, complaints, appeals, certification rules and information handling. Each document is downloadable. For clarification on any policy, contact us through the Customer Feedback form.',

    'policies_empty_state' => 'No policies have been published yet.',
];

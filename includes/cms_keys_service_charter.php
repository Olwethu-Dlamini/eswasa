<?php
/**
 * Shared page_content key registry for service-charter.php and its admin editor.
 */

$service_charter_keys = [
    // Breadcrumb
    'service_charter_breadcrumb_home_label',
    'service_charter_breadcrumb_parent_label',
    'service_charter_breadcrumb_current_label',
    'service_charter_breadcrumb_title',
    // Top section heading + intro card
    'service_charter_section_title',
    'service_charter_intro_body',
    // Bottom Contact CTA
    'service_charter_cta_title',
    'service_charter_cta_body',
    'service_charter_cta_button_text',
    'service_charter_cta_button_url',
    // ── Charter blocks ────────────────────────────────────────────────
    // These five blocks used to be hardcoded in service-charter.php, and the
    // editor said so on screen ("remain in code — request a code edit"). They
    // are now editable. Bullet lists are one item per line; the commitments
    // grid keeps discrete label/description fields because the two halves
    // render into different elements.
    // See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (B4).
    'charter_who_title',
    'charter_who_body',
    'charter_standards_title',
    'charter_standards_intro',
    'charter_values_title',
    'charter_values_items',
    'charter_ask_title',
    'charter_ask_intro',
    'charter_ask_items',
    'charter_short_title',
    'charter_short_intro',
    'charter_short_outro',
];

// Commitments grid — 8 slots so a seventh can be added without a code change.
// Empty slots render nothing.
for ($i = 1; $i <= 8; $i++) {
    $service_charter_keys[] = "charter_commit_{$i}_label";
    $service_charter_keys[] = "charter_commit_{$i}_body";
}
// "If We Fall Short" routes — text plus an optional link target.
for ($i = 1; $i <= 6; $i++) {
    $service_charter_keys[] = "charter_short_{$i}_text";
    $service_charter_keys[] = "charter_short_{$i}_url";
}

$service_charter_defaults = [
    'service_charter_breadcrumb_home_label'    => 'Home',
    'service_charter_breadcrumb_parent_label'  => 'Customer Care',
    'service_charter_breadcrumb_current_label' => 'Service Charter',
    'service_charter_breadcrumb_title'         => 'ESWASA Service Charter',

    'service_charter_section_title' => 'Our Commitments To You',
    'service_charter_intro_body'    => 'The ESWASA Service Charter sets out the standards of service you can expect from the Eswatini Standards Authority. It is our public statement of what we will deliver, how we will deliver it, and how you can hold us accountable when we fall short.',

    'service_charter_cta_title'       => 'Have feedback for us?',
    'service_charter_cta_body'        => 'Whether it is a complaint, a compliment or a suggestion — we want to hear from you.',
    'service_charter_cta_button_text' => 'Submit Feedback',
    'service_charter_cta_button_url'  => 'customer-feedback.php',

    // ── Charter blocks (current on-page wording) ──────────────────────
    'charter_who_title' => 'Who We Are',
    'charter_who_body'  => 'The Eswatini Standards Authority (ESWASA) is the national standards body of the Kingdom of Eswatini. We develop national standards, operate certification and testing services, provide metrology and calibration support, and represent Eswatini in regional and international standardisation bodies.',

    'charter_standards_title' => 'Our Service Standards',
    'charter_standards_intro' => 'We commit to the following service standards across all interactions:',
    'charter_commit_1_label' => 'Acknowledgement',
    'charter_commit_1_body'  => 'We acknowledge written enquiries within 3 working days.',
    'charter_commit_2_label' => 'Full response',
    'charter_commit_2_body'  => 'We provide a substantive response within 14 working days, or update you if more time is needed.',
    'charter_commit_3_label' => 'Quotation requests',
    'charter_commit_3_body'  => 'Service quotations issued within 5 working days of receipt of complete information.',
    'charter_commit_4_label' => 'Certification applications',
    'charter_commit_4_body'  => 'Application receipt confirmed within 5 working days; audit scheduling within 30 working days.',
    'charter_commit_5_label' => 'Testing turnaround',
    'charter_commit_5_body'  => 'Standard test reports delivered within the timeframe agreed at sample acceptance.',
    'charter_commit_6_label' => 'Complaints',
    'charter_commit_6_body'  => 'Acknowledged within 3 working days, resolved within 30 working days where possible.',

    'charter_values_title' => 'Our Core Values',
    'charter_values_items' => "Transparency — clear, accessible information about our processes, fees and decisions.\nResponsiveness — we listen, we act, and we communicate progress.\nPeople-Centricity — every customer receives respectful, professional attention.\nInnovation — we continuously improve our services and adopt better practice.\nProfessionalism — competence, impartiality and integrity in everything we do.",

    'charter_ask_title' => 'What We Ask Of You',
    'charter_ask_intro' => 'To help us deliver these commitments, we ask that you:',
    'charter_ask_items' => "Provide accurate and complete information when making requests.\nRespect our staff and treat them with courtesy.\nHonour scheduled appointments, audits and sample submission dates.\nPay applicable fees on time.\nNotify us promptly of any change in your details or scope.",

    'charter_short_title' => 'If We Fall Short',
    'charter_short_intro' => 'If our service does not meet the standards set out in this charter, we want to know. You can:',
    'charter_short_1_text' => 'Submit feedback or a complaint through our online Customer Feedback form.',
    'charter_short_1_url'  => 'customer-feedback.php',
    'charter_short_2_text' => 'Write to us at info@eswasa.co.sz.',
    'charter_short_2_url'  => 'mailto:info@eswasa.co.sz',
    'charter_short_3_text' => 'Call us on (+268) 2518 4633 / 4610.',
    'charter_short_3_url'  => 'tel:+26825184633',
    'charter_short_4_text' => 'Visit our offices in Matsapha during working hours.',
    'charter_short_4_url'  => '',
    'charter_short_outro'  => 'For matters relating to certification decisions, our Appeals Handling Procedure sets out a formal route.',
];


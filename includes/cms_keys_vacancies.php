<?php
/**
 * Shared page_content key registry for vacancies.php and its admin editor.
 *
 * Included by:
 *   - vacancies.php (frontend)
 *   - admin/pages/vacancies_edit.php (Page Content tab)
 *
 * Both call pc_get_many($conn, $vacancies_keys, $vacancies_defaults) so empty
 * DB rows fall back to the defaults below — keeping the admin form prefilled
 * with the live default copy that ships on the public page.
 */

$vacancies_keys = [
    // Breadcrumb
    'vacancies_breadcrumb_home_label',
    'vacancies_breadcrumb_current_label',
    'vacancies_breadcrumb_title',
    // Intro info-box
    'vacancies_intro_title',
    'vacancies_intro_body',
    // Section heading
    'vacancies_section_title',
    // How to apply info-box
    'vacancies_apply_title',
    'vacancies_apply_body',
    'vacancies_hr_email',
    // Empty state
    'vacancies_empty_state',
];

$vacancies_defaults = [
    'vacancies_breadcrumb_home_label'    => 'Home',
    'vacancies_breadcrumb_current_label' => 'Vacancies',
    'vacancies_breadcrumb_title'         => 'Current Vacancies',

    'vacancies_intro_title' => 'Working at ESWASA',
    'vacancies_intro_body'  => "The Eswatini Standards Authority (ESWASA) is committed to attracting and retaining talented individuals who are passionate about standards, quality, and making a difference in Eswatini. We offer a dynamic and professional work environment where you can grow your career and contribute to the nation's development.\n\nWe offer competitive packages and a supportive work environment. Find our available positions below.",

    'vacancies_section_title' => 'Available Positions',

    'vacancies_apply_title' => 'How to Apply',
    'vacancies_apply_body'  => "Click on any vacancy above to view complete details. When you click \"Apply for this Position\", your email client will open with the job title pre-filled. Please attach your cover letter and CV and send to [email].\n\nEnsure you quote the position title in the subject line of your email.\n\nOnly shortlisted candidates will be contacted for interviews.",
    'vacancies_hr_email'    => 'hr@eswasa.co.sz',

    'vacancies_empty_state' => 'There are no current vacancies available at this time.',
];

<?php
/**
 * Shared page_content key registry for about-us.php and its admin editor.
 *
 * Both files previously carried their own copy of this list, so a key added to
 * one could silently be missing from the other — which is how the About Us
 * editor came to show blank logo fields for affiliations the page was actually
 * displaying from its own defaults. One registry, both sides.
 *
 * Included by:
 *   - about-us.php (frontend)
 *   - admin/pages/about_edit.php
 *
 * See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (B2).
 */

$about_keys = [
    'about_intro', 'about_vision', 'about_mission', 'about_history',
    'about_val_transparency', 'about_val_people', 'about_val_responsiveness',
    'about_val_innovation', 'about_val_professionalism',
    'about_img_vision', 'about_img_mission', 'about_img_team', 'about_img_banner',
    'about_breadcrumb_title',
    // Section headings. These were hardcoded until Batch B, which meant the
    // editor could change every paragraph on the page but none of the titles
    // above them. See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (B2).
    'about_heading_main', 'about_heading_visionmission', 'about_heading_vision',
    'about_heading_mission', 'about_heading_values', 'about_heading_history',
    'about_val_transparency_title', 'about_val_responsiveness_title',
    'about_val_people_title', 'about_val_innovation_title',
    'about_val_professionalism_title',
    // Affiliations + accreditation logo strips, previously hardcoded PHP arrays.
    'about_affiliations_title', 'about_accreditation_title', 'about_accreditation_body',
    // The picture at the centre of the Core Values wheel. It was hardcoded as
    // "about core.jpg", so the one image actually on the page was the one image
    // the CMS could not change.
    'about_values_image',
];
for ($i = 1; $i <= 10; $i++) {
    $about_keys[] = "about_affiliation_{$i}_logo";
    $about_keys[] = "about_affiliation_{$i}_alt";
    $about_keys[] = "about_affiliation_{$i}_url";
}
for ($i = 1; $i <= 4; $i++) {
    $about_keys[] = "about_accreditation_{$i}_logo";
    $about_keys[] = "about_accreditation_{$i}_alt";
    $about_keys[] = "about_accreditation_{$i}_url";
}
$about_defaults = [
    'about_intro'              => 'The Eswatini Standards Authority (ESWASA) is a government parastatal organisation within the Ministry of Commerce, Industry, and Trade (MCIT) that was established under the Standards and Quality Act (10) 2003, amended in 2023. ESWASA is a national standards body mandated to develop, promote, and enforce standards and quality assurance in Eswatini.',
    'about_vision'             => 'A competitive and Sustainable Trade Environment informed by effective standardization and conformity assurance in Eswatini.',
    'about_mission'            => 'We provide and promote internationally recognized quality standards and conformity assessment services to improve business performance, minimize health and safety risks and ensure environmental integrity in collaboration with regulators.',
    'about_history'            => "The Eswatini Standards Authority (ESWASA) is a parastatal organisation within the Ministry of Commerce, Industry, and Trade established by the Eswatini government under the Standards and Quality Act (10) of 2003, amended in 2023.\n\nESWASA is mandated by this Act to promote quality and standards in local businesses, government, and industry.",
    'about_val_transparency'   => 'We conduct our business with honesty, openness, and integrity in all standardization processes.',
    'about_val_people'         => 'We prioritize people—building trust, collaboration, and mutually beneficial relationships with stakeholders.',
    'about_val_responsiveness' => 'We act promptly and effectively to meet the evolving needs of our customers, markets, and partners.',
    'about_val_innovation'     => 'We embrace creative thinking and continuous improvement to enhance our standards and services.',
    'about_val_professionalism'=> 'We uphold the highest standards of competence, reliability, and accountability in all our operations.',
    'about_img_vision'          => 'assets/img/maguga.jpg',
    'about_img_mission'         => 'assets/img/vision.jpg',
    'about_img_team'            => 'assets/img/blog_thumb10.jpg',
    'about_img_banner'          => 'assets/img/blog_thumb11.jpg',
    'about_breadcrumb_title'    => 'Who We Are',

    'about_heading_main'            => 'About Us',
    'about_heading_visionmission'   => 'Vision & Mission',
    'about_heading_vision'          => 'Vision',
    'about_heading_mission'         => 'Mission',
    'about_heading_values'          => 'Our Core Values',
    'about_heading_history'         => 'Brief History',
    'about_val_transparency_title'    => 'Transparency',
    'about_val_responsiveness_title'  => 'Responsiveness',
    'about_val_people_title'          => 'People-Centricity',
    'about_val_innovation_title'      => 'Innovation',
    'about_val_professionalism_title' => 'Professionalism',

    'about_affiliations_title'  => 'Our Affiliations',
    'about_accreditation_title' => 'ESWASA Accreditation',
    'about_accreditation_body'  => 'Eswatini Standards Authority is accredited by SADCAS.',

    'about_affiliation_1_logo' => 'admin/uploads/itu.png',       'about_affiliation_1_alt' => 'ITU',      'about_affiliation_1_url' => 'https://www.itu.int/',
    'about_affiliation_2_logo' => 'admin/uploads/iso.png',       'about_affiliation_2_alt' => 'ISO',      'about_affiliation_2_url' => 'https://www.iso.org/',
    'about_affiliation_3_logo' => 'admin/uploads/iec.png',       'about_affiliation_3_alt' => 'IEC',      'about_affiliation_3_url' => 'https://www.iec.ch/',
    'about_affiliation_4_logo' => 'admin/uploads/arso-2024.png', 'about_affiliation_4_alt' => 'ARSO',     'about_affiliation_4_url' => 'https://www.arso-org.org/',
    'about_affiliation_5_logo' => 'admin/uploads/astm.png',      'about_affiliation_5_alt' => 'ASTM',     'about_affiliation_5_url' => 'https://www.astm.org/',
    'about_affiliation_6_logo' => 'assets/img/WTO.png',          'about_affiliation_6_alt' => 'WTO',      'about_affiliation_6_url' => 'https://www.wto.org',
    'about_affiliation_7_logo' => 'assets/img/AP.png',           'about_affiliation_7_alt' => 'AP',       'about_affiliation_7_url' => '',
    'about_affiliation_8_logo' => 'assets/img/sadcstan.jpg',     'about_affiliation_8_alt' => 'sadcstan', 'about_affiliation_8_url' => '',

    'about_values_image' => 'admin/uploads/about-core.jpg',

    'about_accreditation_1_logo' => 'assets/img/SADCAS.png', 'about_accreditation_1_alt' => 'SADCAS', 'about_accreditation_1_url' => 'https://www.sadcas.org',
];

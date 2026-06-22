<?php
require_once __DIR__ . '/includes/env.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/cms_helpers.php';
include_once 'includes/breadcrumb_helper.php';

// ── CMS keys for the Standards page ──────────────────────────────────────────
// Reused legacy keys (already present in DB from older editor):
//   standards_mandate, standards_process_desc, standards_proposal
// All new keys use the `std_` prefix per pagebase convention.
$std_keys = [
    // Breadcrumb / page title
    'std_breadcrumb_title',
    'std_meta_description',

    // Section 1 — Standards Development (intro)
    'std_about_title',
    'standards_mandate',                 // reused legacy key — opening paragraphs
    'std_sectors_title',
    'std_sectors_intro',
    'std_sector_1', 'std_sector_2', 'std_sector_3', 'std_sector_4',
    'std_sector_5', 'std_sector_6', 'std_sector_7', 'std_sector_8',
    'std_catalogue_note',

    // What is a Standard
    'std_what_title',
    'std_what_body',

    // Benefits of Standards
    'std_benefits_title',
    'std_benefit_1', 'std_benefit_2', 'std_benefit_3', 'std_benefit_4', 'std_benefit_5',

    // Process
    'std_process_title',
    'standards_process_desc',            // reused legacy key — process intro line

    // 9-stage process cards (stages 0–8)
    'std_process_step_0_title', 'std_process_step_0_body',
    'std_process_step_1_title', 'std_process_step_1_body',
    'std_process_step_2_title', 'std_process_step_2_body',
    'std_process_step_3_title', 'std_process_step_3_body',
    'std_process_step_4_title', 'std_process_step_4_body',
    'std_process_step_5_title', 'std_process_step_5_body', 'std_process_step_5_pill',
    'std_process_step_6_title', 'std_process_step_6_body',
    'std_process_step_7_title', 'std_process_step_7_body', 'std_process_step_7_pill',
    'std_process_step_8_title', 'std_process_step_8_body', 'std_process_step_8_pill',

    // Proposal Form
    'std_proposal_title',
    'standards_proposal',                // reused legacy key — proposal lead
    'std_proposal_portal_url',
    'std_proposal_email_primary',
    'std_proposal_email_secondary',
    'std_proposal_form_url',
    'std_proposal_form_label',
    'std_proposal_note',

    // ─── Technical Committees & Work Programmes ───
    'std_tc_section_title',
    'std_tc_about_title',
    'std_tc_about_body',
    'std_tc_benefits_title',
    'std_tc_benefit_1_title', 'std_tc_benefit_1_body',
    'std_tc_benefit_2_title', 'std_tc_benefit_2_body',
    'std_tc_benefit_3_title', 'std_tc_benefit_3_body',
    'std_tc_benefit_4_title', 'std_tc_benefit_4_body',
    'std_tc_apply_title',
    'std_tc_apply_body',
    'std_tc_portal_url',
    'std_tc_register_url',
    'std_workprog_title',
    'std_workprog_body',
    'std_workprog_url',

    // ─── Purchase Standards ───
    'std_purchase_section_title',
    'std_sales_title',
    'std_sales_body',
    'std_estore_url',
    'std_catalogue_url',

    'std_popular_title',
    'std_popular_intro',
    'std_popular_1_code', 'std_popular_1_name', 'std_popular_1_image',
    'std_popular_2_code', 'std_popular_2_name', 'std_popular_2_image',
    'std_popular_3_code', 'std_popular_3_name', 'std_popular_3_image',
    'std_popular_4_code', 'std_popular_4_name', 'std_popular_4_image',
    'std_popular_5_code', 'std_popular_5_name', 'std_popular_5_image',
    'std_popular_6_code', 'std_popular_6_name', 'std_popular_6_image',

    'std_copyright_title',
    'std_copyright_body',

    'std_affiliations_title',
    'std_affiliations_intro',
    'std_aff_1_name', 'std_aff_1_full', 'std_aff_1_image', 'std_aff_1_url',
    'std_aff_2_name', 'std_aff_2_full', 'std_aff_2_image', 'std_aff_2_url',
    'std_aff_3_name', 'std_aff_3_full', 'std_aff_3_image', 'std_aff_3_url',
    'std_aff_4_name', 'std_aff_4_full', 'std_aff_4_image', 'std_aff_4_url',
    'std_aff_5_name', 'std_aff_5_full', 'std_aff_5_image', 'std_aff_5_url',
    'std_aff_6_name', 'std_aff_6_full', 'std_aff_6_image', 'std_aff_6_url',

    // ─── Information Centre ───
    'std_info_section_title',
    'std_info_about_title',
    'std_info_about_intro',
    'std_info_item_1', 'std_info_item_2', 'std_info_item_3', 'std_info_item_4',
    'std_info_about_outro',

    'std_afcfta_title',
    'std_afcfta_body',

    'std_nep_title',
    'std_nep_body',
    'std_nep_image',
    'std_nep_image_alt',

    // CTA
    'std_cta_title',
    'std_cta_subtitle',
    'std_cta_btn_1_label', 'std_cta_btn_1_url',
    'std_cta_btn_2_label', 'std_cta_btn_2_url',
    'std_cta_btn_3_label', 'std_cta_btn_3_url',
];

$std_defaults = [
    'std_breadcrumb_title' => 'Standards Development',
    'std_meta_description' => 'ESWASA standards development under the Standards and Quality Act No. 10 of 2003 — Eswatini National Standards (SZNS), Technical Committees, standards purchase via the ESWASA estore, and the National Enquiry Point (WTO/TBT).',

    'std_about_title' => 'Standards Development',
    'standards_mandate' => "The Standards Development Unit, in accordance with the Standards and Quality Act No. 10 of 2003, as amended, is mandated to facilitate the development of standards for the different sectors of the economy, publish, and maintain the Eswatini National Standards (SZNS) and related normative publications serving the standardisation needs for Eswatini.\n\nThese standards are developed to help industry produce quality products that meet the expectations of consumers and comply with environmental, health and safety regulations.",
    'std_sectors_title' => 'Industry Sectors',
    'std_sectors_intro' => 'ESWASA has developed standards across the following sectors:',
    'std_sector_1' => 'Food and Agriculture',
    'std_sector_2' => 'Building and Construction',
    'std_sector_3' => 'Information Communication Technology',
    'std_sector_4' => 'Chemicals and Textiles',
    'std_sector_5' => 'Electrical and Mechanical Engineering',
    'std_sector_6' => 'Health and Safety',
    'std_sector_7' => 'Environment',
    'std_sector_8' => 'General and Services',
    'std_catalogue_note' => 'See the full Standards Catalogue.',

    'std_what_title' => 'What is a Standard?',
    'std_what_body' => "Standards are the outcome of a consultative process involving the experience and knowledge of interested parties — key industry stakeholders, consumers and their relevant associations, academic and research institutions, government ministries and regulators — who are brought together to agree on the technical contents of a standard through consensus.\n\nThey are developed on a need basis, and the need for a new standard can be initiated by industry stakeholders, an individual, a manufacturer, or a government institution through a standards proposal.\n\nStandards are designed for voluntary use and do not impose any regulations. However, laws and regulations may reference certain standards and make compliance with them mandatory.",

    'std_benefits_title' => 'Benefits of Standards',
    'std_benefit_1' => 'Increased profitability through cost reduction and increased sales.',
    'std_benefit_2' => 'Ensure consumers are protected from hazards to their health and safety.',
    'std_benefit_3' => 'Inspire trust and consumer confidence in your business.',
    'std_benefit_4' => 'Assist businesses in meeting regulatory requirements and provide access to national and international markets.',
    'std_benefit_5' => 'Create a competitive advantage by improving the quality of goods and services.',

    'std_process_title' => 'Standards Development Process',
    'standards_process_desc' => 'The Standards Development Process follows 9 stages — from an early idea to a published Eswatini National Standard.',

    'std_process_step_0_title' => 'Preliminary Stage',
    'std_process_step_0_body'  => 'An opportunity to introduce proposals (Preliminary Work Items, or PWIs) for projects that are not yet mature enough for processing — for example, an emerging-technology standard for which no reference document yet exists.',
    'std_process_step_1_title' => 'Proposal Stage',
    'std_process_step_1_body'  => 'ESWASA / SAC receives — and accepts or rejects — a New Work Item Proposal (NWIP) for: a new standard, a new part of an existing standard, a revision, or an amendment.',
    'std_process_step_2_title' => 'Preparatory Stage',
    'std_process_step_2_body'  => 'Preparation of a Working Draft (WD), if necessary. The stage concludes when the first Committee Draft (CD) is available for submission to the full Technical Committee or Sub-Committee.',
    'std_process_step_3_title' => 'Committee Stage',
    'std_process_step_3_body'  => 'Comments from members are received, consensus is built and voting is requested for progression of the draft to the Enquiry stage. The cycle may repeat if the CD needs further significant development.',
    'std_process_step_4_title' => 'Enquiry Stage',
    'std_process_step_4_body'  => 'Comments are sought from individuals or organisations not participating directly in the ESWASA committee — i.e. from the wider public. Availability of the text for enquiry is notified to the appropriate authorities. Public review allows stakeholders and the public to review draft standards and submit comments before approval, ensuring transparency, inclusiveness, consensus, and national relevance.',
    'std_process_step_5_title' => 'Disposal of Comments Stage',
    'std_process_step_5_body'  => 'Within 30 days of the end of the voting period, the Committee Secretariat prepares a report indicating comments received and the response on each. Every attempt is made to resolve negative votes.',
    'std_process_step_5_pill'  => 'within 30 days',
    'std_process_step_6_title' => 'Approval Stage',
    'std_process_step_6_body'  => 'The ESWASA Standards Approvals Committee (SAC) reviews the Final Draft Standard (FDS) on technical grounds and determines whether it may advance to publication.',
    'std_process_step_7_title' => 'Endorsement Stage',
    'std_process_step_7_body'  => 'Final approval as an Eswatini National Standard rests with the ESWASA Council. The availability of the approved standard is notified in the Government Gazette.',
    'std_process_step_7_pill'  => '≤ 30 days',
    'std_process_step_8_title' => 'Publication Stage',
    'std_process_step_8_body'  => 'Once endorsed by the ESWASA Council, the text is ready for publication as a published Eswatini National Standard (SZNS).',
    'std_process_step_8_pill'  => '≤ 60 days',

    'std_proposal_title'             => 'Submitting a Standards Proposal',
    'standards_proposal'             => 'To propose a new standard or revision of an existing SZNS, please:',
    'std_proposal_portal_url'        => 'https://tc.eswasa.co.sz/proposal.php',
    'std_proposal_email_primary'     => 'standards@eswasa.co.sz',
    'std_proposal_email_secondary'   => 'info@eswasa.co.sz',
    'std_proposal_form_url'          => 'admin/uploads/standards_proposal_form.pdf',
    'std_proposal_form_label'        => 'Download Proposal Form (PDF)',
    'std_proposal_note'              => 'Note: Proposals should include the title and scope of the standard, socio-economic impacts, intended uses, and justification. Priority is given to standards supporting national priorities (e.g. food security, infrastructure, MSME competitiveness, emerging technologies).',

    'std_tc_section_title'           => 'Technical Committees & Work Programmes',
    'std_tc_about_title'             => 'About Technical Committees (TCs)',
    'std_tc_about_body'              => "Technical Committees (TCs) are the cornerstone of the ESWASA standards development process. They are composed of volunteers who are qualified in the subject matter and represent a balance of interested parties — including producers, users, consumers, government, and other relevant stakeholders.\n\nTCs are responsible for developing, maintaining, and revising Eswatini National Standards (SZNS) within their specific technical areas. They ensure that standards are developed through a consensus-based process, reflecting the needs and expertise of all relevant parties.",
    'std_tc_benefits_title'          => 'Key Benefits of Joining an ESWASA TC',
    'std_tc_benefit_1_title'         => 'Market Expansion',
    'std_tc_benefit_1_body'          => 'Contribute to standards that facilitate trade and regional integration. Participation ensures your products and services meet international benchmarks, opening doors to new domestic and global markets.',
    'std_tc_benefit_2_title'         => 'Operational Optimisation',
    'std_tc_benefit_2_body'          => 'Gain early access to best practices in Quality & Management Systems (e.g. ISO 9001, ISO 45001). Implement efficient, safety-focused processes before they become mandatory — reducing waste and costs.',
    'std_tc_benefit_3_title'         => 'Customer Trust Building',
    'std_tc_benefit_3_body'          => 'Shape standards for critical areas like Food Safety and Product Quality. Demonstrating commitment to Eswatini National Standards (SZNS) strengthens brand reputation and consumer confidence.',
    'std_tc_benefit_4_title'         => 'Regulatory Compliance',
    'std_tc_benefit_4_body'          => 'Influence the technical requirements that may become government regulations. By contributing, you ensure standards are practical and achievable for your sector, easing future compliance burdens.',
    'std_tc_apply_title'             => 'Apply to be a TC Member',
    'std_tc_apply_body'              => "Becoming a member of an ESWASA Technical Committee is a great way to contribute to the development of standards that impact your industry and society. Members gain valuable insights, network with experts, and help shape the future of their technical field.\n\nEligibility: Membership is open to Eswatini citizens with relevant expertise and a commitment to the standards development process.\n\nSubmit completed applications to info@eswasa.co.sz or standards@eswasa.co.sz.",
    'std_tc_portal_url'              => 'https://tc.eswasa.co.sz/',
    'std_tc_register_url'            => 'tcp.php',
    'std_workprog_title'             => 'ESWASA Work Programmes',
    'std_workprog_body'              => "The ESWASA Work Programme details all current and scheduled standards development and revision projects. The programme is derived from national needs assessments and stakeholder requests, ensuring the standards developed align with Eswatini's economic and regulatory priorities. Interested stakeholders are invited to review the programme and provide feedback.",
    'std_workprog_url'               => 'https://tc.eswasa.co.sz/work-programme.php',

    'std_purchase_section_title'     => 'Purchase Standards',
    'std_sales_title'                => 'Standards Sales',
    'std_sales_body'                 => "Purchase your SZNS Standards through the ESWASA office or conveniently online via the ESWASA estore.\n\nESWASA sells SZNS as well as related documents and specifications. Our services extend to sourcing other international and regional standards for you, such as ISO, IEC, ARSO, SADCSTAN, SANS and ASTM.",
    'std_estore_url'                 => 'https://estore.eswasa.co.sz/',
    'std_catalogue_url'              => 'purchase.php',

    'std_popular_title'              => 'Most Popular Standards',
    'std_popular_intro'              => 'The standards most frequently purchased from ESWASA across our certification client base:',
    'std_popular_1_code'             => 'SZNS ISO 9001:2015',
    'std_popular_1_name'             => 'Quality Management Systems',
    'std_popular_1_image'            => 'admin/uploads/certificate-iso-9001-colored.svg',
    'std_popular_2_code'             => 'SZNS ISO 14001:2015',
    'std_popular_2_name'             => 'Environmental Management Systems',
    'std_popular_2_image'            => 'admin/uploads/certificate-iso-14001-colored.svg',
    'std_popular_3_code'             => 'SZNS ISO 22000:2018',
    'std_popular_3_name'             => 'Food Safety Management Systems',
    'std_popular_3_image'            => 'admin/uploads/course-iso-22000.svg',
    'std_popular_4_code'             => 'SZNS ISO 45001:2018',
    'std_popular_4_name'             => 'Occupational Health & Safety',
    'std_popular_4_image'            => 'admin/uploads/certificate-iso-45001-colored.svg',
    'std_popular_5_code'             => 'SZNS ISO 19011:2018',
    'std_popular_5_name'             => 'Guidelines for Auditing Management Systems',
    'std_popular_5_image'            => 'admin/uploads/course-iso-19011.svg',
    'std_popular_6_code'             => 'SZNS ISO 27001',
    'std_popular_6_name'             => 'Information Security Management Systems',
    'std_popular_6_image'            => 'admin/uploads/certificate-iso-27001-colored.svg',

    'std_copyright_title'            => 'Copyrights',
    'std_copyright_body'             => "Standards and publications are copyright-protected. Reproduction, copying, scanning, distribution, or unauthorised sharing without written permission from ESWASA is prohibited.\n\nCopyright protection preserves the integrity and authenticity of standards and protects intellectual property rights.",

    'std_affiliations_title'         => 'Our Affiliations',
    'std_affiliations_intro'         => 'ESWASA collaborates with international and regional standards bodies to source standards and harmonise national requirements with global best practice.',
    'std_aff_1_name'  => 'ISO',      'std_aff_1_full' => 'International Organization for Standardization', 'std_aff_1_image' => 'admin/uploads/iso.png',  'std_aff_1_url' => 'https://www.iso.org',
    'std_aff_2_name'  => 'IEC',      'std_aff_2_full' => 'International Electrotechnical Commission',      'std_aff_2_image' => 'admin/uploads/iec.png',  'std_aff_2_url' => 'https://www.iec.ch',
    'std_aff_3_name'  => 'ARSO',     'std_aff_3_full' => 'African Organisation for Standardisation',       'std_aff_3_image' => 'admin/uploads/arso-2024.png', 'std_aff_3_url' => '#',
    'std_aff_4_name'  => 'SADCSTAN', 'std_aff_4_full' => 'SADC Cooperation in Standardization',            'std_aff_4_image' => 'assets/img/sadcstan.jpg','std_aff_4_url' => '#',
    'std_aff_5_name'  => 'ASTM',     'std_aff_5_full' => 'ASTM International',                             'std_aff_5_image' => 'admin/uploads/astm.png', 'std_aff_5_url' => 'https://www.astm.org',

    'std_info_section_title'         => 'Information Centre',
    'std_info_about_title'           => 'About the Information Centre',
    'std_info_about_intro'           => 'The Information Unit holds the database of:',
    'std_info_item_1'                => 'Information on technical specifications to manufacturers and traders.',
    'std_info_item_2'                => 'Information relating to national, regional and international standards.',
    'std_info_item_3'                => 'Information to exporters and importers on the technical regulations and requirements of importing and exporting countries.',
    'std_info_item_4'                => 'Information on ESWASA Certified Products and Services.',
    'std_info_about_outro'           => 'Students, researchers, industry professionals and the general public are welcome to make use of our centre.',

    'std_afcfta_title'               => 'AfCFTA Annex 6 — Technical Barriers to Trade',
    'std_afcfta_body'                => 'The African Continental Free Trade Area (AfCFTA) Annex 6 on Technical Barriers to Trade facilitates trade through cooperation in the areas of standards, technical regulations, conformity assessment, accreditation and metrology.',

    'std_nep_title'                  => 'National Enquiry Point (WTO/TBT)',
    'std_nep_body'                   => 'ESWASA serves as the National Enquiry Point (NEP) for Technical Barriers to Trade (WTO/TBT) information. ESWASA receives notifications on technical regulations from the WTO and disseminates them to stakeholders.',
    'std_nep_image'                  => 'assets/img/WTO.png',
    'std_nep_image_alt'              => 'World Trade Organization — Technical Barriers to Trade',

    'std_cta_title'                  => 'Get Involved in Standards Development',
    'std_cta_subtitle'                => 'Contact our Standards Unit, register for a Technical Committee, or purchase a standard online.',
    'std_cta_btn_1_label'            => 'Contact Standards Unit',
    'std_cta_btn_1_url'              => 'contact.php',
    'std_cta_btn_2_label'            => 'Join a Technical Committee',
    'std_cta_btn_2_url'              => 'tcp.php',
    'std_cta_btn_3_label'            => 'Visit estore',
    'std_cta_btn_3_url'              => 'https://estore.eswasa.co.sz/',
];

$pc = pc_get_many($conn, $std_keys, $std_defaults);
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['std_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="<?= pc_h($pc['std_meta_description']) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="includes/cta-section.css">

    <style>
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 15px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: #2B3388 !important; }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }

        /* bg-light → faint brand tint instead of grey */
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Primary buttons — same brand-blue styling used on training-about.php cards */
        .btn.btn-primary,
        .btn-primary {
            background-color: #2B3388 !important;
            color: #fff !important;
            border-color: #2B3388 !important;
            font-weight: 600;
            padding: 10px 22px;
            margin: 5px;
            border-radius: 4px;
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        .btn.btn-primary:hover,
        .btn-primary:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            border-color: rgba(43, 51, 136, 0.85) !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(43, 51, 136, 0.20);
        }
        .btn.btn-primary:focus { box-shadow: 0 0 0 3px rgba(43, 51, 136, 0.35); }
        /* Clean sections — borders over shadows (DIN/BIS restrained aesthetic) */
        .highlighted-section {
            background-color: rgba(43, 51, 136, 0.04);
            padding: 25px;
            margin: 30px 0;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
        }
        .highlighted-section h3 {
            color: #2B3388;
            margin-top: 0;
            font-weight: 700;
            font-size: 1.5rem;
        }
        /* === Infographic timeline for the 9 Standards Development stages === */
        .process-timeline {
            position: relative;
            max-width: 1000px;
            margin: 30px auto 0;
            padding: 10px 0 5px;
        }
        .process-timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, rgba(43, 51, 136, 0) 0%, rgba(43, 51, 136, 0.25) 6%, rgba(43, 51, 136, 0.25) 94%, rgba(43, 51, 136, 0) 100%);
            transform: translateX(-50%);
        }
        .timeline-stage {
            position: relative;
            display: flex;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .timeline-stage[data-side="left"] { flex-direction: row-reverse; }
        .timeline-stage[data-side="left"] .stage-card { text-align: right; }

        .timeline-stage .stage-marker {
            flex: 0 0 96px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        .timeline-stage .stage-number {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: #2B3388;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            font-family: Arial, sans-serif;
            border: 4px solid #fff;
            box-shadow: 0 0 0 2px rgba(43, 51, 136, 0.25);
        }

        .timeline-stage .stage-card {
            flex: 1;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 22px;
            margin: 0 22px;
            position: relative;
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        .timeline-stage:hover .stage-card {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .timeline-stage .stage-card::before {
            content: '';
            position: absolute;
            top: 22px;
            width: 0;
            height: 0;
            border-style: solid;
        }
        .timeline-stage[data-side="right"] .stage-card::before {
            left: -10px;
            border-width: 10px 10px 10px 0;
            border-color: transparent rgba(43, 51, 136, 0.15) transparent transparent;
        }
        .timeline-stage[data-side="left"] .stage-card::before {
            right: -10px;
            border-width: 10px 0 10px 10px;
            border-color: transparent transparent transparent rgba(43, 51, 136, 0.15);
        }

        .timeline-stage .stage-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }
        .timeline-stage[data-side="left"] .stage-head { justify-content: flex-end; }
        .timeline-stage .stage-icon {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            flex-shrink: 0;
        }
        .timeline-stage .stage-title {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0;
            line-height: 1.3;
        }
        .timeline-stage .stage-description {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.55;
            margin: 0;
        }
        .timeline-stage .stage-pill {
            display: inline-block;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            padding: 2px 9px;
            border-radius: 10px;
            margin-top: 8px;
        }


        /* Section anchor nav (pills at top of body) */
        .standards-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px;
            background-color: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }
        .standards-nav a {
            color: #2B3388;
            background-color: rgba(43, 51, 136, 0.06);
            border: 1px solid rgba(43, 51, 136, 0.18);
            padding: 6px 14px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color .2s ease, color .2s ease, border-color .2s ease;
        }
        .standards-nav a:hover {
            background-color: #2B3388;
            color: #fff;
            border-color: #2B3388;
        }

        .section-anchor { scroll-margin-top: 100px; }

        /* Canonical section h2 — locked to spec 32px (overrides Bootstrap's
           responsive .display-6 default which lands at 40px on desktop). */
        .display-6 {
            color: #2B3388;
            font-weight: 700;
            font-size: 32px !important;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 14px auto 0;
            border-radius: 0;
        }

        /* Sector list — two-column on desktop */
        .sectors-list {
            columns: 2;
            column-gap: 32px;
            margin-top: 8px;
        }

        /* Technical Committee benefit cards */
        .tc-benefit-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 20px;
            height: 100%;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .tc-benefit-card:hover {
            border-color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
        }
        .tc-benefit-card h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.05rem;
            margin: 0 0 8px;
        }
        .tc-benefit-card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.55;
            margin: 0;
        }

        /* Most Popular Standards — matches training-about.php card pattern */
        .add2cart_image {
            background: rgba(43, 51, 136, 0.04);
            overflow: hidden;
        }
        .add2cart_image img {
            max-height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform .25s ease;
        }
        .add2cart_image img[src$=".svg"] {
            object-fit: contain;
            padding: 16px;
            background: #fff;
            height: 200px;
        }
        .hover-lift {
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 22px rgba(43, 51, 136, 0.12) !important;
        }
        .hover-lift:hover .add2cart_image img {
            transform: scale(1.04);
        }
        .add2cart_prod_name {
            color: #2B3388;
            text-decoration: none;
            font-size: 13px;
        }
        .add2cart_prod_name:hover { color: #2B3388; }
        .add2cart_btn,
        .add2cart_btn.btn-primary,
        .btn.add2cart_btn {
            background-color: #2B3388 !important;
            border-color: #2B3388 !important;
            color: #fff !important;
            transition: background-color .25s ease, box-shadow .25s ease;
        }
        .add2cart_btn:hover {
            background-color: rgba(43, 51, 136, 0.85) !important;
            box-shadow: 0 4px 12px rgba(43, 51, 136, 0.20);
            color: #fff !important;
        }
        .popular-code {
            display: block;
            color: #2B3388;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        /* Affiliations grid */
        .affiliation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 14px;
            margin-top: 14px;
        }
        .affiliation-tile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 18px 14px 14px;
            text-align: center;
            text-decoration: none;
            color: #2B3388;
            transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease;
        }
        .affiliation-tile:hover {
            border-color: #2B3388;
            color: #2B3388;
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.08);
            text-decoration: none;
        }
        .affiliation-tile img {
            max-height: 56px;
            max-width: 100%;
            width: auto;
            object-fit: contain;
        }
        .affiliation-tile .affiliation-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #2B3388;
        }
        .affiliation-tile .affiliation-full {
            font-size: 0.75rem;
            color: #2B3388;
            line-height: 1.35;
        }

        /* Related Standards services — teaser cards linking to dedicated pages */
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-top: 8px;
        }
        .related-card {
            display: flex;
            flex-direction: column;
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            padding: 22px 24px;
            text-decoration: none;
            color: #2B3388;
            box-shadow: 0 3px 12px rgba(43, 51, 136, 0.16);
            transition: border-color .2s ease, box-shadow .2s ease, transform .15s ease;
        }
        .related-card:hover {
            border-color: rgba(43, 51, 136, 0.25);
            box-shadow: 0 10px 26px rgba(43, 51, 136, 0.26);
            transform: translateY(-3px);
            text-decoration: none;
            color: #2B3388;
        }
        .related-card .related-icon {
            width: 44px;
            height: 44px;
            border-radius: 6px;
            background: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }
        .related-card h4 {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0 0 6px;
        }
        .related-card p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.5;
            margin: 0 0 14px;
            flex-grow: 1;
        }
        .related-card .related-link {
            font-weight: 600;
            color: #2B3388;
        }
        .related-card .related-link i { transition: transform .15s ease; }
        .related-card:hover .related-link i { transform: translateX(3px); }

        /* Information Centre image */
        .info-centre-img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 12px auto 0;
            border-radius: 4px;
            border: 1px solid rgba(43, 51, 136, 0.15);
        }

        @media (max-width: 768px) {
            .highlighted-section {
                padding: 20px 15px;
            }
            /* Timeline collapses to single side on mobile */
            .process-timeline::before {
                left: 32px;
                transform: none;
            }
            .timeline-stage,
            .timeline-stage[data-side="left"] {
                flex-direction: row;
            }
            .timeline-stage[data-side="left"] .stage-card,
            .timeline-stage[data-side="left"] .stage-head {
                text-align: left;
                justify-content: flex-start;
            }
            .timeline-stage .stage-marker {
                flex: 0 0 64px;
            }
            .timeline-stage .stage-number {
                width: 48px;
                height: 48px;
                font-size: 1.15rem;
                border-width: 3px;
            }
            .timeline-stage .stage-card {
                margin: 0 0 0 16px;
                padding: 14px 16px;
            }
            .timeline-stage[data-side="left"] .stage-card::before,
            .timeline-stage[data-side="right"] .stage-card::before {
                left: -10px;
                right: auto;
                border-width: 10px 10px 10px 0;
                border-color: transparent rgba(43, 51, 136, 0.15) transparent transparent;
            }
            .timeline-stage .stage-title { font-size: 0.98rem; }
            .timeline-stage .stage-description { font-size: 0.88rem; }
            .standards-nav { padding: 10px; }
            .standards-nav a { font-size: 0.8rem; padding: 5px 10px; }
            .sectors-list { columns: 1; }
            .tc-benefit-card { padding: 14px 16px; }
            .display-6 { font-size: 26px !important; }
            .add2cart_image img { max-height: 160px; }
            .add2cart_image img[src$=".svg"] { height: 160px; padding: 12px; }
            .affiliation-grid { grid-template-columns: repeat(2, 1fr); }
            .affiliation-tile { padding: 14px 10px 12px; }
            .affiliation-tile .affiliation-full { font-size: 0.7rem; }
        }
    </style>
</head>

<body>

    <!-- Preloader -->
    <div id="preloader">
        <div class="spinner">
            <div class="sk-dot1"></div><div class="sk-dot2"></div>
            <div class="rect3"></div><div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- header-area-end -->

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('standards', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Standards</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Development</span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['std_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="py-5">
            <div class="container">
                <!-- ============ STANDARDS DEVELOPMENT ============ -->
                <div id="standards-development">

                    <!-- 1. About Standards Development -->
                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_about_title']) ?></h3>
                        <?= pc_paragraphs_html($pc['standards_mandate']) ?>

                        <h4 class="mt-4"><?= pc_h($pc['std_sectors_title']) ?></h4>
                        <p><?= pc_h($pc['std_sectors_intro']) ?></p>
                        <ul class="sectors-list">
                            <li><?= pc_h($pc['std_sector_1']) ?></li>
                            <li><?= pc_h($pc['std_sector_2']) ?></li>
                            <li><?= pc_h($pc['std_sector_3']) ?></li>
                            <li><?= pc_h($pc['std_sector_4']) ?></li>
                            <li><?= pc_h($pc['std_sector_5']) ?></li>
                            <li><?= pc_h($pc['std_sector_6']) ?></li>
                            <li><?= pc_h($pc['std_sector_7']) ?></li>
                            <li><?= pc_h($pc['std_sector_8']) ?></li>
                        </ul>
                        <p class="small text-muted mt-2">
                            <?= pc_h($pc['std_catalogue_note']) ?> <a href="purchase.php" style="color:#2B3388; text-decoration:underline;">Standards Catalogue</a>.
                        </p>
                    </div>

                    <!-- What is a Standard -->
                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_what_title']) ?></h3>
                        <?= pc_paragraphs_html($pc['std_what_body']) ?>
                    </div>

                    <!-- Benefits -->
                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_benefits_title']) ?></h3>
                        <ul>
                            <li><?= pc_h($pc['std_benefit_1']) ?></li>
                            <li><?= pc_h($pc['std_benefit_2']) ?></li>
                            <li><?= pc_h($pc['std_benefit_3']) ?></li>
                            <li><?= pc_h($pc['std_benefit_4']) ?></li>
                            <li><?= pc_h($pc['std_benefit_5']) ?></li>
                        </ul>
                    </div>

                    <!-- Process -->
                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_process_title']) ?></h3>
                        <p><?= pc_h($pc['standards_process_desc']) ?></p>

                        <?php
                        $stages = [
                            ['n'=>'0', 'icon'=>'fa-lightbulb',       'title'=>$pc['std_process_step_0_title'], 'desc'=>$pc['std_process_step_0_body']],
                            ['n'=>'1', 'icon'=>'fa-file-alt',        'title'=>$pc['std_process_step_1_title'], 'desc'=>$pc['std_process_step_1_body']],
                            ['n'=>'2', 'icon'=>'fa-pencil-ruler',    'title'=>$pc['std_process_step_2_title'], 'desc'=>$pc['std_process_step_2_body']],
                            ['n'=>'3', 'icon'=>'fa-users',           'title'=>$pc['std_process_step_3_title'], 'desc'=>$pc['std_process_step_3_body']],
                            ['n'=>'4', 'icon'=>'fa-bullhorn',        'title'=>$pc['std_process_step_4_title'], 'desc'=>$pc['std_process_step_4_body']],
                            ['n'=>'5', 'icon'=>'fa-comment-dots',    'title'=>$pc['std_process_step_5_title'], 'desc'=>$pc['std_process_step_5_body'], 'pill'=>$pc['std_process_step_5_pill']],
                            ['n'=>'6', 'icon'=>'fa-clipboard-check', 'title'=>$pc['std_process_step_6_title'], 'desc'=>$pc['std_process_step_6_body']],
                            ['n'=>'7', 'icon'=>'fa-gavel',           'title'=>$pc['std_process_step_7_title'], 'desc'=>$pc['std_process_step_7_body'], 'pill'=>$pc['std_process_step_7_pill']],
                            ['n'=>'8', 'icon'=>'fa-book-open',       'title'=>$pc['std_process_step_8_title'], 'desc'=>$pc['std_process_step_8_body'], 'pill'=>$pc['std_process_step_8_pill']],
                        ];
                        ?>
                        <div class="process-timeline">
                            <?php foreach ($stages as $i => $s):
                                $side = ($i % 2 === 0) ? 'right' : 'left';
                            ?>
                                <div class="timeline-stage" data-side="<?= $side ?>">
                                    <div class="stage-marker">
                                        <div class="stage-number"><?= pc_h($s['n']) ?></div>
                                    </div>
                                    <div class="stage-card">
                                        <div class="stage-head">
                                            <span class="stage-icon"><i class="fas <?= pc_h($s['icon']) ?>"></i></span>
                                            <h4 class="stage-title"><?= pc_h($s['title']) ?></h4>
                                        </div>
                                        <p class="stage-description"><?= pc_h($s['desc']) ?></p>
                                        <?php if (!empty($s['pill'])): ?>
                                            <span class="stage-pill"><i class="far fa-clock me-1"></i><?= pc_h($s['pill']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Proposal Form -->
                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_proposal_title']) ?></h3>
                        <p><?= pc_h($pc['standards_proposal']) ?></p>
                        <ol>
                            <li>Complete the official <strong>Standards Development Proposal Form</strong> — either the downloadable form below, or via the online portal: <a href="<?= pc_h($pc['std_proposal_portal_url']) ?>" target="_blank" rel="noopener" style="color:#2B3388; text-decoration:underline; font-weight:600;"><?= pc_h(preg_replace('#^https?://#', '', $pc['std_proposal_portal_url'])) ?></a></li>
                            <li>Email the completed form to <a href="mailto:<?= pc_h($pc['std_proposal_email_primary']) ?>" style="color:#2B3388; text-decoration:underline; font-weight:600;"><?= pc_h($pc['std_proposal_email_primary']) ?></a> or <a href="mailto:<?= pc_h($pc['std_proposal_email_secondary']) ?>" style="color:#2B3388; text-decoration:underline; font-weight:600;"><?= pc_h($pc['std_proposal_email_secondary']) ?></a></li>
                        </ol>
                        <div class="mt-3">
                            <a href="<?= pc_h($pc['std_proposal_form_url']) ?>" class="btn btn-primary" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i><?= pc_h($pc['std_proposal_form_label']) ?>
                            </a>
                        </div>
                        <p class="mt-3 small">
                            <strong>Note:</strong> <?= pc_h(preg_replace('/^Note:\s*/i', '', $pc['std_proposal_note'])) ?>
                        </p>
                    </div>
                </div>

                <!-- ============ EXPLORE STANDARDS SERVICES (teasers → dedicated pages) ============ -->
                <div id="standards-services">
                    <h2 class="display-6 fw-bold text-center mt-5 mb-3">Explore Standards Services</h2>
                    <div class="section-divider mb-4"></div>
                    <p class="text-center mb-4" style="max-width:760px; margin-left:auto; margin-right:auto;">
                        Beyond developing standards, ESWASA runs Technical Committees, publishes Work Programmes, and sells national and international standards. Each has its own dedicated page.
                    </p>
                    <div class="related-grid">
                        <a class="related-card" href="tcp.php">
                            <span class="related-icon"><i class="fas fa-users"></i></span>
                            <h4>Technical Committees</h4>
                            <p>Join an ESWASA Technical Committee and help develop the Eswatini National Standards (SZNS) for your sector.</p>
                            <span class="related-link">Technical Committee Platform <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                        <a class="related-card" href="work.php">
                            <span class="related-icon"><i class="fas fa-calendar-alt"></i></span>
                            <h4><?= pc_h($pc['std_workprog_title']) ?></h4>
                            <p>Review current and scheduled standards development and revision projects, and submit public review comments.</p>
                            <span class="related-link">View Work Programmes <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                        <a class="related-card" href="purchase.php">
                            <span class="related-icon"><i class="fas fa-shopping-cart"></i></span>
                            <h4><?= pc_h($pc['std_purchase_section_title']) ?></h4>
                            <p>Purchase SZNS and international standards via the ESWASA office or the online estore, and browse our most popular standards.</p>
                            <span class="related-link">Purchase Standards <i class="fas fa-arrow-right ms-1"></i></span>
                        </a>
                    </div>
                </div>

                <!-- ============ INFORMATION CENTRE ============ -->
                <div id="information-centre" class="section-anchor">
                    <h2 class="display-6 fw-bold text-center mt-5 mb-3"><?= pc_h($pc['std_info_section_title']) ?></h2>
                    <div class="section-divider mb-4"></div>

                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_info_about_title']) ?></h3>
                        <p><?= pc_h($pc['std_info_about_intro']) ?></p>
                        <ul>
                            <li><?= pc_h($pc['std_info_item_1']) ?></li>
                            <li><?= pc_h($pc['std_info_item_2']) ?></li>
                            <li><?= pc_h($pc['std_info_item_3']) ?></li>
                            <li><?= pc_h($pc['std_info_item_4']) ?></li>
                        </ul>
                        <p class="mt-3">
                            <?= pc_h($pc['std_info_about_outro']) ?>
                        </p>
                    </div>

                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_afcfta_title']) ?></h3>
                        <p><?= pc_h($pc['std_afcfta_body']) ?></p>
                    </div>

                    <div class="highlighted-section">
                        <h3><?= pc_h($pc['std_nep_title']) ?></h3>
                        <p><?= pc_h($pc['std_nep_body']) ?></p>
                        <img src="<?= pc_h(pc_image_src($pc['std_nep_image'], 'assets/img/WTO.png')) ?>" alt="<?= pc_h($pc['std_nep_image_alt']) ?>" class="info-centre-img" style="max-width: 480px;">
                    </div>
                </div>

            </div>
        </section>

        <section class="cta-journey-section">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <h2 class="cta-title"><?= pc_h($pc['std_cta_title']) ?></h2>
                        <p class="cta-subtitle"><?= pc_h($pc['std_cta_subtitle']) ?></p>
                        <a href="<?= pc_h($pc['std_cta_btn_1_url']) ?>" class="btn-cta"><?= pc_h($pc['std_cta_btn_1_label']) ?></a>
                        <a href="<?= pc_h($pc['std_cta_btn_2_url']) ?>" class="btn-cta"><?= pc_h($pc['std_cta_btn_2_label']) ?></a>
                        <a href="<?= pc_h($pc['std_cta_btn_3_url']) ?>" target="_blank" rel="noopener" class="btn-cta"><?= pc_h($pc['std_cta_btn_3_label']) ?></a>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- main-area-end -->

    <!-- footer-area -->
    <?php include("includes/footer.php")?>
    <!-- footer-area-end -->

    <!-- JS here -->
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.odometer.min.js"></script>
    <script src="assets/js/jquery.appear.js"></script>
    <script src="assets/js/tween-max.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-animation.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/form-contact.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>

</body>
</html>

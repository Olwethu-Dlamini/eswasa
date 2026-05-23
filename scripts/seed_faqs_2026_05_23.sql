-- Seed FAQ entries from WEBSITE FAQ.docx (provided by user 2026-05-23).
-- Appends 15 new questions to existing eswasa_faq rows, continuing
-- sort_order from each category's current max.
-- Note: some questions overlap with existing rows (eg. participation in
-- standards development, product testing). Kept the new richer answers;
-- admin can prune duplicates via the FAQ admin page.

-- Run with:
--   "/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe" -u root eswasa < scripts/seed_faqs_2026_05_23.sql

-- ============================================================
-- STANDARDS  (existing max sort_order = 5, new entries 6..14)
-- ============================================================
INSERT INTO eswasa_faq (category, question, answer, sort_order) VALUES
('standards',
 'What is a Standard?',
 'A standard is a document developed through consensus and approved by a recognised body that provides rules, guidelines, specifications, or characteristics for products, services, systems, or processes to ensure quality, safety, efficiency, and consistency.\n\nFor more information, see the Standards Development page: Standards.php',
 6),

('standards',
 'Are standards mandatory in Eswatini?',
 'Generally, standards are voluntary unless referenced in legislation, technical regulations, contracts, or procurement requirements. ESWASA develops and publishes voluntary standards in accordance with the Standards and Quality Act, No. 10 of 2003 (as amended).\n\nFor more information, see the Standards Development page: Standards.php',
 7),

('standards',
 'How can I participate in standards development? (updated 2026)',
 'Stakeholders may participate through Technical Committees (TCs), public reviews, stakeholder consultations, workshops, and standards-commenting processes. Participation is voluntary and based on relevant expertise and stakeholder representation.\n\nFor more information, see the Standards Development page: Standards.php',
 8),

('standards',
 'How do I become a Technical Committee member?',
 'ESWASA periodically publishes Notices of Intent and calls for Technical Committee participation. Interested stakeholders may submit:\n\n• A curriculum vitae (CV)\n• Areas of expertise\n• Organisational affiliation (where applicable)\n\nMembership is assessed to ensure balanced stakeholder representation and technical competence.\n\nFor more information, see the Technical Committee Platform: Standards.php#technical-committees',
 9),

('standards',
 'Where can one purchase Eswatini National Standards?',
 'Hard copies of the Eswatini National Standards are sold at the ESWASA head office in Matsapha. Electronic copies are also available for sale at the head office. Enquiries and requests for quotations can be sent to info@eswasa.co.sz or standards@eswasa.co.sz. Online purchases can be made through the ESWASA Webstore: https://estore.eswasa.co.sz/\n\nFor more information, see the Purchase Standards page: purchase.php',
 10),

('standards',
 'What is the cost of Certification?',
 'The cost of certification depends on various factors, such as the company''s size and operations, as well as the parameters for product certification.\n\nGet your quote today: qoute_certification.php',
 11),

('standards',
 'What is ESWASA Certification and its benefits?',
 'ESWASA offers two types of certification: Management Systems Certification and the Product Certification Scheme. ESWASA provides third-party certification services to confirm that a product, process, or service conforms to specified requirements.\n\nBenefits of certification:\n\n• Gives testimony that customers are using a quality product or service.\n• Increases product or service marketability and competitiveness.\n• Gives assurance of product or service consistency.\n• Facilitates access to regional and international markets.\n• Cost reduction and increased profits.\n\nFor more information, see the Management Systems Certification page: managementsystems.php',
 12),

('standards',
 'Does ESWASA offer testing services for products, and at what cost? (updated 2026)',
 'Yes. ESWASA facilitates testing of various products and commodities in accredited laboratories in the areas of Chemistry, Microbiology, Textiles, Mechanical, Civil and Electrical Engineering, etc. The testing is performed to verify the quality of the product and conformity to the specified standards requirements. ESWASA testing services are available to the public, the industry, and various sectors of the economy.\n\nThe cost of testing differs from one product to the next, and depends on the parameters being tested.\n\nFor more information, see the Product Certification page: product.php',
 13),

('standards',
 'Does ESWASA provide calibration services?',
 'Yes. ESWASA provides calibration services to support measurement accuracy, traceability, quality assurance, and regulatory compliance across industry and commerce.\n\nCurrently, ESWASA provides calibration services primarily in the area of:\n\n• Mass Metrology (e.g. weighing scales, balances, industrial scales, and related measuring instruments)\n\nThese services support sectors such as:\n\n• Manufacturing\n• Retail and trade\n• Food processing\n• Petroleum and LPG operations\n• Laboratories\n• Industrial production and quality control\n\nESWASA is also strategically working towards expanding its metrology and calibration capabilities into additional areas including:\n\n• Volume Metrology\n• Temperature Metrology\n• Pressure Metrology\n\nFor more information, see the Scales and Metrology Services page: Calibration.php',
 14);

-- ============================================================
-- GENERAL  (existing max sort_order = 4, new entries 5..6)
-- ============================================================
INSERT INTO eswasa_faq (category, question, answer, sort_order) VALUES
('general',
 'Are ESWASA services recognised internationally?',
 'Yes. ESWASA services are recognised internationally. ESWASA participates in regional and international standardisation activities and maintains relationships with organisations such as the International Organization for Standardization (ISO), the International Electrotechnical Commission (IEC), the African Organisation for Standardisation (ARSO), and SADC Cooperation in Standardization (SADCSTAN).\n\nESWASA is a full member of ISO, ARSO and SADCSTAN, and an affiliate member of IEC. ESWASA also collaborates with other National Standards Bodies through Memoranda of Understanding (MoUs), including an MoU with the South African Bureau of Standards (SABS).\n\nFor more information, see the Who We Are page: about-us.php',
 5),

('general',
 'Is ESWASA competent to provide its services?',
 'Yes. Most of the services provided by ESWASA have been accredited and verified as competent:\n\n• ESWASA Management Systems Certification Services is accredited by the Southern African Development Community Accreditation Service (SADCAS). Scopes: Quality Management Systems to ISO/IEC 17021-1:2015 and ISO/IEC 17021-3:2017 (Certification to ISO 9001:2015), IAF Codes 3, 12, 13 and 38.\n\n• The ESWASA calibration laboratory is in the process of attaining accreditation and currently operates in full conformance with ISO/IEC 17025:2017 — General Requirements for the Competence of Testing and Calibration Laboratories — under SADCAS.\n\n• ESWASA Standards-based Training courses are based on internationally recognised standards (ISO, GLOBALG.A.P., SABS, etc.) and are therefore internationally recognised.',
 6);

-- ============================================================
-- TRAINING  (existing max sort_order = 6, new entries 7..10)
-- ============================================================
INSERT INTO eswasa_faq (category, question, answer, sort_order) VALUES
('training',
 'Does ESWASA offer training?',
 'ESWASA offers training in the areas of standardisation and quality assurance, such as Quality Management Systems, Occupational Health and Safety, Food Safety Management Systems, HACCP, and Environmental Management Systems, to mention a few. ESWASA courses are open to all professionals — including the private sector, parastatals, government, the public, academia, SMEs, and other interested parties.\n\nFor more information, see the Training - About page: training-about.php',
 7),

('training',
 'How much does each training cost?',
 'Training fees vary depending on:\n\n• Course type / duration:\n   - Awareness — 1 day @ E2,500.00 per person\n   - Understanding and Implementation — 3 to 4 days @ E5,500.00 per person\n• Number of participants: 11 and above incurs a 10% discount.\n• Students / MSMEs: @ E3,000.00 per person (evidence-based).\n\nFor more information, see the Training - About page: training-about.php',
 8),

('training',
 'How long do the trainings last?',
 'Training duration depends on the course being offered:\n\n• 1-day duration — Awareness courses\n• 3 to 4 days duration — Understanding & Implementation courses\n• 3 to 4 days duration — Auditing courses (have pre-requisites)\n\nFor more information, see the Training - About page: training-about.php',
 9),

('training',
 'What academic qualifications are required to enrol for ESWASA training?',
 'There are no specific academic qualifications required to enrol for ESWASA training. Training is open to anyone — individuals, students, graduates, MSMEs, government institutions and industry professionals. We encourage participants to have at least field experience and/or an academic background to better understand the concepts being discussed during training. Participants are also required to be able to read and write in English, as training and training materials are conducted in English.\n\nFor more information, see the Training - About page: training-about.php',
 10);

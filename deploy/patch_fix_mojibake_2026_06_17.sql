-- Patch — 2026-06-17
-- Repairs CP850 mojibake introduced by the 2026-05-25 mysqldump: special
-- characters (em/en dashes, <=, fractions, middot, curly quotes) were read
-- through the Windows CP850 console codepage, e.g. '—' became 'ÔÇö' and '≤'
-- became 'Ôëñ'. These statements set each affected key to its correct value.
-- Safe to re-run; a no-op on rows that are already correct.

UPDATE `page_content` SET `content`='We prioritize people—building trust, collaboration, and mutually beneficial relationships with stakeholders.' WHERE `page_key`='about_val_people';
UPDATE `page_content` SET `content`='A voluntary product certification scheme operated by the Eswatini Standards Authority. Awarded to products manufactured to declared national and international standards and proven through rigorous, independent testing — giving buyers confidence in quality and safety.' WHERE `page_key`='index_mark_2_desc';
UPDATE `page_content` SET `content`='A simplified, affordable certification scheme designed for micro, small and medium enterprises (MSMEs) and local producers — helping them prove product quality, access new markets and grow with credibility.' WHERE `page_key`='index_mark_3_desc';
UPDATE `page_content` SET `content`='A voluntary product certification scheme operated by the Eswatini Standards Authority. Awarded to products manufactured to declared national and international standards and proven through rigorous, independent testing — giving buyers confidence in quality and safety.' WHERE `page_key`='cert_mark_2_desc';
UPDATE `page_content` SET `content`='A simplified, affordable certification scheme designed for micro, small and medium enterprises (MSMEs) and local producers — helping them prove product quality, access new markets and grow with credibility.' WHERE `page_key`='cert_mark_4_desc';
UPDATE `page_content` SET `content`='The Eswatini Standards Authority (ESWASA) invites Micro, Small and Medium Enterprises (MSMEs) to apply for participation in the Ingelo Certification Scheme — a programme developed to support local businesses in achieving product and system certification.\n\nThis initiative is designed to empower Emaswati entrepreneurs by providing them with the tools and recognition needed to compete effectively in both local and international markets.' WHERE `page_key`='ingelo_intro_body';
UPDATE `page_content` SET `content`='Unlike machinery or stock, certifications don\'t depreciate overnight — they compound business credibility over time. When integrated into IP and brand strategy, certifications become part of the SME\'s unique intangible asset portfolio.' WHERE `page_key`='ingelo_benefit_8_body';
UPDATE `page_content` SET `content`='Emaswati (Swazi citizens) — local entrepreneurs and business owners.' WHERE `page_key`='ingelo_who_item_1';
UPDATE `page_content` SET `content`='Producers willing to scale up — those who are willing to increase production to meet export quota requirements by local and regional markets, through compliance with certification requirements.' WHERE `page_key`='ingelo_who_item_3';
UPDATE `page_content` SET `content`='SZNS 025: Poultry processing — Hygiene requirements\nSZNS 058: Sweet potato — Grading requirements\nSZNS 049: Maize grains — Specification\nSZNS BOS 43: Onion — Grading requirements\nSZNS 037: Organic fertiliser — Specification\nSZNS 060: Banana — Grading requirements\nSZNS KS 052: Fresh courgettes/baby marrow — Specification and grading\nSZNS 031: Cattle feeds — Specification\nSZNS 035: Peanut butter — Specification\nSZNS SANS 1199: Production of Mageu\nSZNS SANS 1679: Pasteurised Milk\nSZNS CODEX STAN 296: Jam\nSZNS CODEX STAN 306: Chilli Sauce' WHERE `page_key`='ingelo_standards_list';
UPDATE `page_content` SET `content`='Stage 1 Audit — Documentation & Site Readiness' WHERE `page_key`='ms_step_4_body';
UPDATE `page_content` SET `content`='Stage 2 Audit — Implementation Effectiveness' WHERE `page_key`='ms_step_5_body';
UPDATE `page_content` SET `content`='ESWASA standards development under the Standards and Quality Act No. 10 of 2003 — Eswatini National Standards (SZNS), Technical Committees, standards purchase via the ESWASA estore, and the National Enquiry Point (WTO/TBT).' WHERE `page_key`='std_meta_description';
UPDATE `page_content` SET `content`='Standards are the outcome of a consultative process involving the experience and knowledge of interested parties — key industry stakeholders, consumers and their relevant associations, academic and research institutions, government ministries and regulators — who are brought together to agree on the technical contents of a standard through consensus.\n\nThey are developed on a need basis, and the need for a new standard can be initiated by industry stakeholders, an individual, a manufacturer, or a government institution through a standards proposal.\n\nStandards are designed for voluntary use and do not impose any regulations. However, laws and regulations may reference certain standards and make compliance with them mandatory.' WHERE `page_key`='std_what_body';
UPDATE `page_content` SET `content`='An opportunity to introduce proposals (Preliminary Work Items, or PWIs) for projects that are not yet mature enough for processing — for example, an emerging-technology standard for which no reference document yet exists.' WHERE `page_key`='std_process_step_0_body';
UPDATE `page_content` SET `content`='ESWASA / SAC receives — and accepts or rejects — a New Work Item Proposal (NWIP) for: a new standard, a new part of an existing standard, a revision, or an amendment.' WHERE `page_key`='std_process_step_1_body';
UPDATE `page_content` SET `content`='Comments are sought from individuals or organisations not participating directly in the ESWASA committee — i.e. from the wider public. Availability of the text for enquiry is notified to the appropriate authorities. Public review allows stakeholders and the public to review draft standards and submit comments before approval, ensuring transparency, inclusiveness, consensus, and national relevance.' WHERE `page_key`='std_process_step_4_body';
UPDATE `page_content` SET `content`='≤ 30 days' WHERE `page_key`='std_process_step_7_pill';
UPDATE `page_content` SET `content`='≤ 60 days' WHERE `page_key`='std_process_step_8_pill';
UPDATE `page_content` SET `content`='Technical Committees (TCs) are the cornerstone of the ESWASA standards development process. They are composed of volunteers who are qualified in the subject matter and represent a balance of interested parties — including producers, users, consumers, government, and other relevant stakeholders.\n\nTCs are responsible for developing, maintaining, and revising Eswatini National Standards (SZNS) within their specific technical areas. They ensure that standards are developed through a consensus-based process, reflecting the needs and expertise of all relevant parties.' WHERE `page_key`='std_tc_about_body';
UPDATE `page_content` SET `content`='Gain early access to best practices in Quality & Management Systems (e.g. ISO 9001, ISO 45001). Implement efficient, safety-focused processes before they become mandatory — reducing waste and costs.' WHERE `page_key`='std_tc_benefit_2_body';
UPDATE `page_content` SET `content`='AfCFTA Annex 6 — Technical Barriers to Trade' WHERE `page_key`='std_afcfta_title';
UPDATE `page_content` SET `content`='World Trade Organization — Technical Barriers to Trade' WHERE `page_key`='std_nep_image_alt';
UPDATE `page_content` SET `content`='½ day · 1 day · 2 days' WHERE `page_key`='train_about_format_1_duration';
UPDATE `page_content` SET `content`='3 – 5 days' WHERE `page_key`='train_about_format_2_duration';
UPDATE `page_content` SET `content`='Both formats are delivered as standard-based courses across all sectors — see the full course catalogue below.' WHERE `page_key`='train_about_format_note';
UPDATE `page_content` SET `content`='ISO 9001 — Quality Management System' WHERE `page_key`='train_about_course_1_alt';
UPDATE `page_content` SET `content`='ISO 45001 — Health and Safety Management' WHERE `page_key`='train_about_course_2_alt';
UPDATE `page_content` SET `content`='ISO 14001 — Environmental Management' WHERE `page_key`='train_about_course_3_alt';
UPDATE `page_content` SET `content`='GLOBALG.A.P. — Good Agricultural Practices' WHERE `page_key`='train_about_course_4_alt';
UPDATE `page_content` SET `content`='ISO 22000 — Food Safety Management' WHERE `page_key`='train_about_course_6_alt';
UPDATE `page_content` SET `content`='ISO 19011 — Auditing' WHERE `page_key`='train_about_course_7_alt';
UPDATE `page_content` SET `content`='A cancellation fee of 50% of the course fee will be deducted from participants who cancel after registration / confirmation or on the date of commencement of the training course. ESWASA reserves the right to postpone any course (typically due to insufficient enrolment — see Application for class minimums) and undertakes to inform participants promptly of such developments.' WHERE `page_key`='train_about_policy_cancellations_body';
UPDATE `page_content` SET `content`='Eswatini Standards Authority — ESWASA' WHERE `page_key`='train_about_bank_account_name';
UPDATE `page_content` SET `content`='Quality Management Systems — SZNS ISO 9001:2015 — Understanding & Implementation' WHERE `page_key`='train_cal_session_1_title';
UPDATE `page_content` SET `content`='18–22 May; 13–17 July; 5–9 October; 7–11 December 2026' WHERE `page_key`='train_cal_session_1_date';
UPDATE `page_content` SET `content`='Quality Management Systems — Internal Auditing — SZNS ISO 19011:2018' WHERE `page_key`='train_cal_session_2_title';
UPDATE `page_content` SET `content`='20–24 July; 7–11 September 2026' WHERE `page_key`='train_cal_session_2_date';
UPDATE `page_content` SET `content`='Food Safety Management Systems — SZNS ISO 22000:2018 — Understanding & Implementation' WHERE `page_key`='train_cal_session_3_title';
UPDATE `page_content` SET `content`='1–5 June; 17–21 August; 26–30 October; 14–18 December 2026' WHERE `page_key`='train_cal_session_3_date';
UPDATE `page_content` SET `content`='Food Safety Management Systems — Internal Auditing — SZNS ISO 19011:2018' WHERE `page_key`='train_cal_session_4_title';
UPDATE `page_content` SET `content`='27–31 July; 30 November – 4 December 2026' WHERE `page_key`='train_cal_session_4_date';
UPDATE `page_content` SET `content`='Hazard Analysis & Critical Control Points (HACCP) — SZNS ISO 10330:2020 — Understanding & Implementation' WHERE `page_key`='train_cal_session_5_title';
UPDATE `page_content` SET `content`='3–7 August 2026' WHERE `page_key`='train_cal_session_5_date';
UPDATE `page_content` SET `content`='Occupational Health & Safety Management Systems — SZNS ISO 45001:2018 — Understanding & Implementation' WHERE `page_key`='train_cal_session_6_title';
UPDATE `page_content` SET `content`='8–12 June; 24–28 August; 2–6 November 2026' WHERE `page_key`='train_cal_session_6_date';
UPDATE `page_content` SET `content`='SHE Rep — Safety, Health and Environment Representative' WHERE `page_key`='train_cal_session_7_title';
UPDATE `page_content` SET `content`='21–25 September 2026' WHERE `page_key`='train_cal_session_7_date';
UPDATE `page_content` SET `content`='Root Cause Analysis / Incident Investigation — Understanding & Implementation' WHERE `page_key`='train_cal_session_8_title';
UPDATE `page_content` SET `content`='10–14 August 2026' WHERE `page_key`='train_cal_session_8_date';
UPDATE `page_content` SET `content`='Hazmat — Hazardous Material — Understanding & Implementation' WHERE `page_key`='train_cal_session_9_title';
UPDATE `page_content` SET `content`='25–29 May; 9–13 November 2026' WHERE `page_key`='train_cal_session_9_date';
UPDATE `page_content` SET `content`='Environmental Management Systems — SZNS ISO 14001:2015 — Understanding & Implementation' WHERE `page_key`='train_cal_session_10_title';
UPDATE `page_content` SET `content`='15–19 June; 14–18 September; 23–27 November 2026' WHERE `page_key`='train_cal_session_10_date';
UPDATE `page_content` SET `content`='Enterprise Risk Management — SZNS ISO 31000:2018 — Understanding & Implementation' WHERE `page_key`='train_cal_session_11_title';
UPDATE `page_content` SET `content`='22–26 June; 19–23 October 2026' WHERE `page_key`='train_cal_session_11_date';
UPDATE `page_content` SET `content`='Wellness and Disease Management Systems — SZNS SANS 16001:2013 — Understanding & Implementation' WHERE `page_key`='train_cal_session_12_title';
UPDATE `page_content` SET `content`='29 June – 3 July 2026' WHERE `page_key`='train_cal_session_12_date';
UPDATE `page_content` SET `content`='Global GAP — Integrated Farm Assurance' WHERE `page_key`='train_cal_session_13_title';
UPDATE `page_content` SET `content`='6–10 July; 12–16 October 2026' WHERE `page_key`='train_cal_session_13_date';
UPDATE `page_content` SET `content`='CER_PR_002 — Appeals Handling Procedure' WHERE `page_key`='cert_status_footer_appeals_link_label';
UPDATE `page_content` SET `content`='CER_PR_006 — Complaints Handling Procedure' WHERE `page_key`='cert_status_footer_complaints_link_label';
UPDATE `page_content` SET `content`='CER_PR_015 — Handling Requests for Information' WHERE `page_key`='cert_status_footer_info_link_label';

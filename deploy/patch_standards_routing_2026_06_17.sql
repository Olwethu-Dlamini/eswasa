-- Patch — 2026-06-17
-- Standards content routing: the "Join a Technical Committee" CTA on
-- Standards.php previously scrolled to the in-page #technical-committees
-- anchor. That section has been moved to its dedicated page (tcp.php), so the
-- button must now link there. Idempotent (safe to re-run).

UPDATE `page_content`
   SET `content` = 'tcp.php'
 WHERE `page_key` = 'std_cta_btn_2_url'
   AND `content`  = '#technical-committees';

-- FAQ #19 linked to the same removed anchor in its answer text. Repoint it to
-- the dedicated Technical Committee page. Idempotent.
UPDATE `eswasa_faq`
   SET `answer` = REPLACE(`answer`, 'Standards.php#technical-committees', 'tcp.php')
 WHERE `answer` LIKE '%Standards.php#technical-committees%';

-- Fix corrupted process-timeline pill text. The 2026-05-25 dump double-encoded
-- the "≤" symbol, so production currently shows "Ôëñ 30 days" / "Ôëñ 60 days"
-- on Standards.php. Restore the intended values. Idempotent.
UPDATE `page_content` SET `content` = '≤ 30 days'
 WHERE `page_key` = 'std_process_step_7_pill' AND `content` <> '≤ 30 days';
UPDATE `page_content` SET `content` = '≤ 60 days'
 WHERE `page_key` = 'std_process_step_8_pill' AND `content` <> '≤ 60 days';

-- Verify:
-- SELECT page_key, content FROM page_content WHERE page_key = 'std_cta_btn_2_url';
-- SELECT id, RIGHT(answer,60) FROM eswasa_faq WHERE id = 19;
-- SELECT page_key, content FROM page_content WHERE page_key IN ('std_process_step_7_pill','std_process_step_8_pill');

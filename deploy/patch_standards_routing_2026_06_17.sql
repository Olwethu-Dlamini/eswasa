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

-- Verify:
-- SELECT page_key, content FROM page_content WHERE page_key = 'std_cta_btn_2_url';
-- SELECT id, RIGHT(answer,60) FROM eswasa_faq WHERE id = 19;

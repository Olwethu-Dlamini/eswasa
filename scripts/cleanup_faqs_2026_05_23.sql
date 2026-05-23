-- Post-seed FAQ cleanup (2026-05-23, after 12e50c6).
-- 1) Delete the two pre-seed rows whose content was superseded by the
--    richer docx-sourced answers added in scripts/seed_faqs_2026_05_23.sql.
-- 2) Strip the temporary "(updated 2026)" question suffix that distinguished
--    the new versions while both old and new rows still existed.
--
-- Run with:
--   "/c/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe" -u root eswasa < scripts/cleanup_faqs_2026_05_23.sql

DELETE FROM eswasa_faq WHERE id IN (10, 11);
-- id 10: 'Does ESWASA provide product testing services?'   (replaced by new id 23)
-- id 11: 'How can I participate in standards development?' (replaced by new id 18)

UPDATE eswasa_faq
   SET question = 'How can I participate in standards development?'
 WHERE question = 'How can I participate in standards development? (updated 2026)';

UPDATE eswasa_faq
   SET question = 'Does ESWASA offer testing services for products, and at what cost?'
 WHERE question = 'Does ESWASA offer testing services for products, and at what cost? (updated 2026)';

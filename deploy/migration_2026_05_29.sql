-- ESWASA — Production migration for the 2026-05-29 deploy round.
-- Idempotent — safe to re-run (upserts via ON DUPLICATE KEY UPDATE,
-- DELETE is key-scoped).
--
-- Apply via phpMyAdmin Import, or:
--   mysql -u <user> -p <db> < migration_2026_05_29.sql
--
-- Sections:
--   1. Home "Certification Marks" — remove the Compulsory Standards Quality
--      Mark card. The grid drops from 4 cards to 3; the Ingelo MSME mark
--      (was slot 4) is promoted into slot 3, and slot 4 is deleted.

SET NAMES utf8mb4;

-- =====================================================================
-- 1. Home Certification Marks — drop Compulsory Standards Quality Mark
-- =====================================================================
-- Promote the Ingelo MSME mark into slot 3 (overwrites the old Compulsory
-- Standards content that used to live there).
INSERT INTO page_content (page_key, content) VALUES
  ('index_mark_3_title',       'Ingelo MSME Product Certification Mark'),
  ('index_mark_3_desc',        'A simplified, affordable certification scheme designed for micro, small and medium enterprises (MSMEs) and local producers — helping them prove product quality, access new markets and grow with credibility.'),
  ('index_mark_3_image',       'assets/img/quality/ingelo-certification-black.png'),
  ('index_mark_3_explore_url', 'ingelo.php'),
  ('index_mark_3_verify_url',  'certification-status.php')
ON DUPLICATE KEY UPDATE content = VALUES(content);

-- Remove the now-unused 4th mark slot.
DELETE FROM page_content WHERE page_key REGEXP '^index_mark_4_';

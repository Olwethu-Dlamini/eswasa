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
--   2. Home "Our Affiliations" — replace the SABS logo (slot 6) with the
--      ARSO 2024 logo, drop the duplicate ARSO (old slot 10), and renumber
--      ASTM (was slot 11) up into slot 10. Slider goes from 11 logos to 10.
--   3. Services page "Our Affiliations" slider — remove SABS (slot 4),
--      renumber ARSO into slot 4 (on the 2024 logo) and ASTM into slot 5.
--      Slider goes from 6 logos to 5.
--   4. Standards page "Our Affiliations" grid — remove the SANS / SABS tile
--      (slot 5), renumber ASTM into slot 5, and refresh the ARSO tile
--      (slot 3) to the 2024 logo. Grid goes from 6 tiles to 5.

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


-- =====================================================================
-- 2. Home Affiliations — SABS replaced by ARSO; ARSO logo refreshed
-- =====================================================================
-- Slot 6 was SABS — repoint it to ARSO with the 2024 logo. The old ARSO
-- (slot 10) is dropped to avoid a duplicate, and ASTM (was slot 11) is
-- renumbered up into slot 10.
INSERT INTO page_content (page_key, content) VALUES
  ('index_affiliation_6_logo',  'admin/uploads/arso-2024.png'),
  ('index_affiliation_6_url',   'https://www.arso-org.org/'),
  ('index_affiliation_6_alt',   'ARSO'),
  ('index_affiliation_10_logo', 'admin/uploads/astm.png'),
  ('index_affiliation_10_url',  'https://www.astm.org/'),
  ('index_affiliation_10_alt',  'ASTM')
ON DUPLICATE KEY UPDATE content = VALUES(content);

-- Remove the now-unused 11th affiliation slot.
DELETE FROM page_content WHERE page_key REGEXP '^index_affiliation_11_';


-- =====================================================================
-- 3. Services page Affiliations slider — SABS removed, single ARSO
-- =====================================================================
-- Slot 4 was SABS — replace with ARSO (2024 logo). Slot 5 was ARSO —
-- renumber ASTM (was slot 6) up into slot 5. Slot 6 is dropped.
INSERT INTO page_content (page_key, content) VALUES
  ('services_affil_4_img', 'admin/uploads/arso-2024.png'),
  ('services_affil_4_alt', 'ARSO'),
  ('services_affil_4_url', 'https://www.arso-org.org/'),
  ('services_affil_5_img', 'admin/uploads/astm.png'),
  ('services_affil_5_alt', 'ASTM'),
  ('services_affil_5_url', 'https://www.astm.org/')
ON DUPLICATE KEY UPDATE content = VALUES(content);

-- Remove the now-unused 6th affiliation slot.
DELETE FROM page_content WHERE page_key REGEXP '^services_affil_6_';


-- =====================================================================
-- 4. Standards page Affiliations grid — drop SANS / SABS tile
-- =====================================================================
-- Slot 5 was "SANS / SABS" — replace with ASTM (was slot 6). Refresh the
-- ARSO tile (slot 3) to the 2024 logo. Slot 6 is dropped.
INSERT INTO page_content (page_key, content) VALUES
  ('std_aff_3_image', 'admin/uploads/arso-2024.png'),
  ('std_aff_5_name',  'ASTM'),
  ('std_aff_5_full',  'ASTM International'),
  ('std_aff_5_image', 'admin/uploads/astm.png'),
  ('std_aff_5_url',   'https://www.astm.org')
ON DUPLICATE KEY UPDATE content = VALUES(content);

-- Remove the now-unused 6th affiliation slot.
DELETE FROM page_content WHERE page_key REGEXP '^std_aff_6_';

-- ESWASA — Production migration for the 2026-06-01 deploy round.
-- Idempotent — safe to re-run (upserts via ON DUPLICATE KEY UPDATE).
--
-- Apply via phpMyAdmin Import, or:
--   mysql -u <user> -p <db> < migration_2026_06_01.sql
--
-- Sections:
--   1. Home "Discover" — rename the 4th card from "Training & Development"
--      to "Training Academy" (title only; description and link unchanged).
--   2. Home "Discover" — add a 5th card, "Scales and Metrology Services",
--      linking to Calibration.php. The grid goes from 4 cards to 5.
--      NOTE: the matching front-end markup (index.php) and admin editor
--      (admin/pages/index_edit.php) ship in the same deploy and already
--      render/manage 5 cards (one row, row-cols-lg-5).
--   3. Home "Discover" — shorten the card 3 (Standards Development) and
--      card 5 (Scales and Metrology) descriptions so the cards sit shorter.

SET NAMES utf8mb4;

-- =====================================================================
-- 1. Home Discover — rename card 4 to "Training Academy"
-- =====================================================================
INSERT INTO page_content (page_key, content) VALUES
  ('index_discover_4_title', 'Training Academy')
ON DUPLICATE KEY UPDATE content = VALUES(content);


-- =====================================================================
-- 2. Home Discover — add card 5 "Scales and Metrology Services"
-- =====================================================================
INSERT INTO page_content (page_key, content) VALUES
  ('index_discover_5_title', 'Scales and Metrology Services'),
  ('index_discover_5_desc',  'Calibration of scales and measuring instruments to ensure accuracy and fair trade.'),
  ('index_discover_5_url',   'Calibration.php')
ON DUPLICATE KEY UPDATE content = VALUES(content);


-- =====================================================================
-- 3. Home Discover — shorten card 3 & card 5 descriptions
-- =====================================================================
INSERT INTO page_content (page_key, content) VALUES
  ('index_discover_3_desc', 'National standards developed to protect public health and enable trade.')
ON DUPLICATE KEY UPDATE content = VALUES(content);

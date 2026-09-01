-- =====================================================================
--  ESWASA CMS — production upgrade, 2026-09-01  (batch D)
--
--  Run this ONCE against the live database after deploying the code.
--
--    mysql -u eswasa1 -p admin_eswasa < admin/sql/upgrade_2026_09_01.sql
--
--  TAKE A DUMP FIRST:
--    mysqldump -u eswasa1 -p admin_eswasa > eswasa_pre_upgrade_$(date +%F).sql
--
--  Additive and idempotent: every column is guarded on whether it already
--  exists, every seed on whether the row is already there. Running it twice
--  is harmless. It drops nothing — the retired page_content keys are left in
--  place, unread, so the old values remain recoverable.
--
--  Background: docs/superpowers/specs/2026-09-01-cms-batch-d-design.md
-- =====================================================================

-- ── 1 ─────────────────────────────────────────────────────────────────
-- Affiliation logos move out of page_content
--
-- The home-page slider was ten fixed key triplets
-- (index_affiliation_1..10_logo/url/alt), so adding an eleventh logo meant a
-- code change. They become rows, and the ten current ones are copied across.
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `index_affiliations` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `logo_path`  varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url`        varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alt`        varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active`  tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sort` (`sort_order`,`id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed from page_content where it exists, falling back to the code defaults
-- for any key that was never saved through the CMS. Guarded on the table
-- being empty so a second run doesn't duplicate the set.
INSERT INTO `index_affiliations` (`logo_path`, `url`, `alt`, `sort_order`, `is_active`)
SELECT * FROM (
    SELECT
        COALESCE((SELECT content FROM page_content WHERE page_key = CONCAT('index_affiliation_', n, '_logo')), d.logo) AS logo_path,
        COALESCE((SELECT content FROM page_content WHERE page_key = CONCAT('index_affiliation_', n, '_url')),  d.url)  AS url,
        COALESCE((SELECT content FROM page_content WHERE page_key = CONCAT('index_affiliation_', n, '_alt')),  d.alt)  AS alt,
        n * 10 AS sort_order,
        1      AS is_active
    FROM (
        SELECT  1 AS n, 'admin/uploads/iso.png'      AS logo, 'https://www.iso.org/'         AS url, 'ISO'      AS alt
        UNION ALL SELECT  2, 'admin/uploads/iec.png',       'https://www.iec.ch/',          'IEC'
        UNION ALL SELECT  3, 'admin/uploads/itu.png',       'https://www.itu.int/',         'ITU'
        UNION ALL SELECT  4, 'assets/img/iaf.webp',         'https://iaf.nu/',              'IAF'
        UNION ALL SELECT  5, 'assets/img/ILAC.jpg',         'https://ilac.org/',            'ILAC'
        UNION ALL SELECT  6, 'admin/uploads/arso-2024.png', 'https://www.arso-org.org/',    'ARSO'
        UNION ALL SELECT  7, 'assets/img/SADCAS.png',       'https://www.sadcas.org/',      'SADCAS'
        UNION ALL SELECT  8, 'assets/img/sadc.webp',        'https://www.sadc.int/',        'SADC'
        UNION ALL SELECT  9, 'assets/img/sadcstan.jpg',     'https://www.sadcstan.org/',    'SADCSTAN'
        UNION ALL SELECT 10, 'admin/uploads/astm.png',      'https://www.astm.org/',        'ASTM'
    ) AS d
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM `index_affiliations` LIMIT 1) AS probe);

-- ── 2 ─────────────────────────────────────────────────────────────────
-- certified_organisations serves three logo grids, not one
--
-- `scheme` says which page a row belongs to; `product` is the extra line the
-- Product and Ingelo tiles show and the Management Systems tiles don't.
-- Existing rows default to 'ms', which is where they already appear.
-- ----------------------------------------------------------------------
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'certified_organisations' AND COLUMN_NAME = 'scheme') > 0,
    'SELECT ''column certified_organisations.scheme already present''',
    'ALTER TABLE `certified_organisations`
        ADD COLUMN `scheme` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''ms'' AFTER `id`,
        ADD KEY `idx_scheme` (`scheme`, `sort_order`, `id`)'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'certified_organisations' AND COLUMN_NAME = 'product') > 0,
    'SELECT ''column certified_organisations.product already present''',
    'ALTER TABLE `certified_organisations`
        ADD COLUMN `product` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `standard`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- The three producers that were hardcoded in product.php. Their logos were
-- resolved by filename from assets/img/clients/; the same paths are stored
-- here, and any that don't exist on disk simply render as a wordmark.
INSERT INTO `certified_organisations` (`scheme`, `name`, `standard`, `product`, `logo_path`, `sort_order`, `is_active`)
SELECT * FROM (
    SELECT 'product' AS scheme, 'Swazi Tiles Investments' AS name, 'SZNS SANS 542:2020' AS standard,
           'Concrete Roof Tiles' AS product, 'assets/img/clients/swazi-tiles.png' AS logo_path, 10 AS sort_order, 1 AS is_active
    UNION ALL SELECT 'product', 'Lubombo Eco Products — Asiphile Bomake', 'SZNS CODEXSTAN 306:2015',
           'Chilli Sauce', 'assets/img/clients/lubombo-asiphile.png', 20, 1
    UNION ALL SELECT 'product', 'Lubombo Eco Products — Spice Girls', 'SZNS CODEXSTAN 306:2015',
           'Chilli Sauce', 'assets/img/clients/lubombo-spice-girls.png', 30, 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM (SELECT id FROM `certified_organisations` WHERE scheme = 'product' LIMIT 1) AS probe);

-- ── 3 ─────────────────────────────────────────────────────────────────
-- Per-training calendar colour
--
-- NULL means "follow the family palette in includes/training_families.php",
-- so trainings that were never recoloured keep tracking the palette.
-- ----------------------------------------------------------------------
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'training_sessions' AND COLUMN_NAME = 'colour') > 0,
    'SELECT ''column training_sessions.colour already present''',
    'ALTER TABLE `training_sessions`
        ADD COLUMN `colour` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `price`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ── 4 ─────────────────────────────────────────────────────────────────
-- Advert PDF on a vacancy
-- ----------------------------------------------------------------------
SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'eswasa_vacancies' AND COLUMN_NAME = 'pdf_path') > 0,
    'SELECT ''column eswasa_vacancies.pdf_path already present''',
    'ALTER TABLE `eswasa_vacancies`
        ADD COLUMN `pdf_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `responsibilities`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ── 5 ─────────────────────────────────────────────────────────────────
-- Ingelo: "Available Standards" gives way to the certified-producer logos
--
-- The old heading is carried over to the new section so an edited title is
-- not lost, unless it still says "Available Standards", which would be wrong
-- above a grid of logos. ingelo_standards_list is deliberately left in
-- page_content: it stops rendering, but the thirteen standards remain
-- recoverable from the database.
-- ----------------------------------------------------------------------
-- MySQL refuses a subquery on page_content inside an INSERT that targets
-- page_content (error 1093), so the old values are snapshotted first.
DROP TEMPORARY TABLE IF EXISTS `pc_seed`;
CREATE TEMPORARY TABLE `pc_seed` AS
SELECT page_key, content FROM page_content
 WHERE page_key IN ('ingelo_standards_title', 'cert_status_breadcrumb_title',
                    'cert_status_section_title', 'cert_status_section_subtitle',
                    'cert_status_intro');

INSERT IGNORE INTO page_content (page_key, content)
SELECT 'ingelo_certified_title',
       COALESCE(NULLIF((SELECT content FROM `pc_seed` WHERE page_key = 'ingelo_standards_title'),
                       'Available Standards'),
                'Ingelo Certified Producers');

-- ── 6 ─────────────────────────────────────────────────────────────────
-- The three certification status registers
--
-- Each certification mark on the home page now verifies against its own
-- register. The entries were three hardcoded PHP arrays in
-- certification-status.php that could only be changed by deploying; they
-- become rows, with a client logo per entry.
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `certification_register` (
  `id`             int(11) NOT NULL AUTO_INCREMENT,
  `scheme`         varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status`         varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name`    varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path`      varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cert_no`        varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope`          varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `effective_date` date NOT NULL,
  `reason_note`    varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order`     int(11) NOT NULL DEFAULT 0,
  `is_active`      tinyint(1) NOT NULL DEFAULT 1,
  `created_at`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_register` (`scheme`,`status`,`sort_order`,`id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- No seed: all three arrays in the old page were empty, so all three
-- registers legitimately start with no entries and show their empty states.

-- ── 7 ─────────────────────────────────────────────────────────────────
-- Register page copy: one shared set becomes one set per register
--
-- The hero, subtitle and intro exist once per scheme now. All three are
-- seeded from whatever the single old set holds, so an edited intro survives,
-- and the two default titles are specialised per register. The retired
-- cert_status_breadcrumb_title / _section_title / _section_subtitle / _intro
-- keys are left in page_content, unread.
-- ----------------------------------------------------------------------
INSERT IGNORE INTO page_content (page_key, content)
SELECT CONCAT('cert_status_', s.k, '_', f.field),
       CASE
           WHEN f.field IN ('breadcrumb_title', 'section_title')
                AND COALESCE(old.content, 'Certification Status Register') = 'Certification Status Register'
               THEN s.label
           ELSE COALESCE(old.content, f.fallback)
       END
FROM
    (          SELECT 'ms'      AS k, 'Management Systems Certification Status Register' AS label
     UNION ALL SELECT 'product',      'Product Certification Status Register'
     UNION ALL SELECT 'ingelo',       'Ingelo Certification Status Register'
    ) AS s
CROSS JOIN
    (          SELECT 'breadcrumb_title' AS field, 'Certification Status Register' AS fallback
     UNION ALL SELECT 'section_title',             'Certification Status Register'
     UNION ALL SELECT 'section_subtitle',          'Public record of suspended, withdrawn and reduced-scope certifications'
     UNION ALL SELECT 'intro',                     'In accordance with the Suspension / Withdrawal / Reduced Scope of Certification Procedure (CER_PR_026), ESWASA publishes information on the certified status of clients whose certification has been suspended, withdrawn or reduced in scope. This register is updated as decisions are taken by the Certification Approvals Committee. The current status of an active certificate may be confirmed by contacting the ESWASA Certification Unit.'
    ) AS f
LEFT JOIN `pc_seed` AS old
    ON old.page_key = CONCAT('cert_status_', f.field);

-- The hub page's own copy, seeded from the old shared set where it exists.
INSERT IGNORE INTO page_content (page_key, content)
SELECT 'cert_status_hub_title',
       COALESCE((SELECT content FROM `pc_seed` WHERE page_key = 'cert_status_section_title'),
                'Certification Status Register');
INSERT IGNORE INTO page_content (page_key, content)
SELECT 'cert_status_hub_subtitle',
       COALESCE((SELECT content FROM `pc_seed` WHERE page_key = 'cert_status_section_subtitle'),
                'Public record of suspended, withdrawn and reduced-scope certifications');
INSERT IGNORE INTO page_content (page_key, content)
SELECT 'cert_status_hub_intro',
       'In accordance with the Suspension / Withdrawal / Reduced Scope of Certification Procedure (CER_PR_026), ESWASA publishes information on the certified status of clients whose certification has been suspended, withdrawn or reduced in scope. A separate register is published for each certification mark. Choose the register for the mark you are verifying.';

DROP TEMPORARY TABLE IF EXISTS `pc_seed`;

-- ── 8 ─────────────────────────────────────────────────────────────────
-- Each mark verifies against its own register
--
-- Only rewritten where the stored value is still the old shared page, so a
-- deliberately redirected mark is left alone.
-- ----------------------------------------------------------------------
UPDATE page_content SET content = 'certification-status-management-systems.php'
 WHERE page_key = 'index_mark_1_verify_url' AND content = 'certification-status.php';
UPDATE page_content SET content = 'certification-status-product.php'
 WHERE page_key = 'index_mark_2_verify_url' AND content = 'certification-status.php';
UPDATE page_content SET content = 'certification-status-ingelo.php'
 WHERE page_key = 'index_mark_3_verify_url' AND content = 'certification-status.php';

-- ── 9 ─────────────────────────────────────────────────────────────────
-- Repair logo paths broken by the upload helper
--
-- pc_upload_image() / pc_save_base64_image() / pc_upload_document() returned
-- a hardcoded 'admin/uploads/' . $name whatever directory they were handed,
-- so admin/pages/managementsystems.php — which has always uploaded certified
-- organisation logos into uploads/orgs/ — wrote the file to the subdirectory
-- but stored a path one level up. The image 404s with no error anywhere.
-- The helper now derives the path from the directory; this fixes rows already
-- stored. The 'org_' filename prefix is what that uploader produces, so it
-- identifies exactly the affected rows and nothing else.
-- ----------------------------------------------------------------------
UPDATE certified_organisations
   SET logo_path = CONCAT('admin/uploads/orgs/', SUBSTRING(logo_path, 15))
 WHERE logo_path LIKE 'admin/uploads/org\_%'
   AND logo_path NOT LIKE 'admin/uploads/orgs/%';

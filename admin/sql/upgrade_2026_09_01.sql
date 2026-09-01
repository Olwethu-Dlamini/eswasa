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
-- Logo strips move out of page_content
--
-- Five strips around the site were each a fixed run of page_content keys —
-- index_affiliation_1..10, services_affil_1..5, about_affiliation_1..10,
-- about_accreditation_1..4, cal_brand_1..20. Adding one more logo meant
-- editing PHP, and the editor faced a wall of mostly-empty slots. They become
-- rows in one table, keyed by which strip they belong to, all managed by the
-- same admin partial.
--
-- The old keys are left in page_content, unread, so the values stay
-- recoverable.
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `logo_lists` (
  `id`         int(11) NOT NULL AUTO_INCREMENT,
  `list_key`   varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_path`  varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url`        varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alt`        varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active`  tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_list` (`list_key`,`sort_order`,`id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed every strip from the keys it used to live in, falling back to the
-- code defaults for any key never saved through the CMS. Each strip is
-- guarded on being empty, so a second run adds nothing and an editor's later
-- changes are never overwritten.
-- Which strips already hold rows, snapshotted before any insert so a second
-- run adds nothing. A correlated NOT EXISTS can't be used here: the subquery
-- would have to reference the table being inserted into.
DROP TEMPORARY TABLE IF EXISTS `logo_existing`;
CREATE TEMPORARY TABLE `logo_existing` (list_key varchar(32) PRIMARY KEY);
INSERT INTO `logo_existing` SELECT DISTINCT list_key FROM `logo_lists`;

DROP TEMPORARY TABLE IF EXISTS `logo_seed`;
CREATE TEMPORARY TABLE `logo_seed` (
  list_key varchar(32), old_prefix varchar(40), n int,
  d_logo varchar(500), d_url varchar(500), d_alt varchar(200)
);
INSERT INTO `logo_seed` VALUES
 ('index_affiliations','index_affiliation_', 1,'admin/uploads/iso.png','https://www.iso.org/','ISO'),
 ('index_affiliations','index_affiliation_', 2,'admin/uploads/iec.png','https://www.iec.ch/','IEC'),
 ('index_affiliations','index_affiliation_', 3,'admin/uploads/itu.png','https://www.itu.int/','ITU'),
 ('index_affiliations','index_affiliation_', 4,'assets/img/iaf.webp','https://iaf.nu/','IAF'),
 ('index_affiliations','index_affiliation_', 5,'assets/img/ILAC.jpg','https://ilac.org/','ILAC'),
 ('index_affiliations','index_affiliation_', 6,'admin/uploads/arso-2024.png','https://www.arso-org.org/','ARSO'),
 ('index_affiliations','index_affiliation_', 7,'assets/img/SADCAS.png','https://www.sadcas.org/','SADCAS'),
 ('index_affiliations','index_affiliation_', 8,'assets/img/sadc.webp','https://www.sadc.int/','SADC'),
 ('index_affiliations','index_affiliation_', 9,'assets/img/sadcstan.jpg','https://www.sadcstan.org/','SADCSTAN'),
 ('index_affiliations','index_affiliation_',10,'admin/uploads/astm.png','https://www.astm.org/','ASTM');

-- services_affil_N uses _img rather than _logo; handled by its own pass below.
INSERT INTO `logo_seed` VALUES
 ('about_affiliations','about_affiliation_', 1,'admin/uploads/itu.png','https://www.itu.int/','ITU'),
 ('about_affiliations','about_affiliation_', 2,'admin/uploads/iso.png','https://www.iso.org/','ISO'),
 ('about_affiliations','about_affiliation_', 3,'admin/uploads/iec.png','https://www.iec.ch/','IEC'),
 ('about_affiliations','about_affiliation_', 4,'admin/uploads/arso-2024.png','https://www.arso-org.org/','ARSO'),
 ('about_affiliations','about_affiliation_', 5,'admin/uploads/astm.png','https://www.astm.org/','ASTM'),
 ('about_affiliations','about_affiliation_', 6,'assets/img/WTO.png','https://www.wto.org','WTO'),
 ('about_affiliations','about_affiliation_', 7,'assets/img/AP.png','','AP'),
 ('about_affiliations','about_affiliation_', 8,'assets/img/sadcstan.jpg','','sadcstan'),
 ('about_accreditation','about_accreditation_',1,'assets/img/SADCAS.png','https://www.sadcas.org','SADCAS');

-- Strips whose logo key ends in _logo.
INSERT INTO `logo_lists` (list_key, logo_path, url, alt, sort_order, is_active)
SELECT * FROM (
    SELECT s.list_key,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT(s.old_prefix, s.n, '_logo')), ''), s.d_logo) AS logo_path,
           COALESCE((SELECT content FROM page_content WHERE page_key = CONCAT(s.old_prefix, s.n, '_url')), s.d_url) AS url,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT(s.old_prefix, s.n, '_alt')), ''), s.d_alt) AS alt,
           s.n * 10 AS sort_order,
           1 AS is_active
    FROM `logo_seed` s
) AS seed
WHERE seed.logo_path <> ''
  AND seed.list_key NOT IN (SELECT list_key FROM `logo_existing`);

-- Services uses _img for the logo key.
INSERT INTO `logo_lists` (list_key, logo_path, url, alt, sort_order, is_active)
SELECT * FROM (
    SELECT 'services_affiliations' AS list_key,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT('services_affil_', n.n, '_img')), ''), '') AS logo_path,
           COALESCE((SELECT content FROM page_content WHERE page_key = CONCAT('services_affil_', n.n, '_url')), '') AS url,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT('services_affil_', n.n, '_alt')), ''), '') AS alt,
           n.n * 10 AS sort_order, 1 AS is_active
    FROM (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5) AS n
) AS seed
WHERE seed.logo_path <> ''
  AND 'services_affiliations' NOT IN (SELECT list_key FROM `logo_existing`);

-- Calibration brands: logo only, no link.
INSERT INTO `logo_lists` (list_key, logo_path, url, alt, sort_order, is_active)
SELECT * FROM (
    SELECT 'cal_brands' AS list_key,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT('cal_brand_', n.n, '_image')), ''), '') AS logo_path,
           '' AS url,
           COALESCE(NULLIF((SELECT content FROM page_content WHERE page_key = CONCAT('cal_brand_', n.n, '_alt')), ''), '') AS alt,
           n.n * 10 AS sort_order, 1 AS is_active
    FROM (SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
          UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
          UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
          UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20) AS n
) AS seed
WHERE seed.logo_path <> ''
  AND 'cal_brands' NOT IN (SELECT list_key FROM `logo_existing`);

DROP TEMPORARY TABLE IF EXISTS `logo_seed`;
DROP TEMPORARY TABLE IF EXISTS `logo_existing`;

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

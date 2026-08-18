-- =====================================================================
--  ESWASA CMS — production upgrade, 2026-08-18
--
--  Run this ONCE against the live database after deploying the code.
--  It supersedes batch_a_data_fixes.sql and batch_b_data_fixes.sql, which
--  are kept in git history only; this is the single file to run.
--
--    mysql -u eswasa1 -p admin_eswasa < admin/sql/upgrade_2026_08_18.sql
--
--  TAKE A DUMP FIRST:
--    mysqldump -u eswasa1 -p admin_eswasa > eswasa_pre_upgrade_$(date +%F).sql
--
--  Every statement is idempotent and guarded on the value it changes, so
--  running the file twice is harmless and running it against a database
--  that has already had part of it applied is also harmless.
--
--  This file does NOT drop anything. The only destructive migration is
--  admin/sql/drop_dead_tables.sql, deliberately kept separate so it cannot
--  be run by accident. It is optional — the site behaves identically
--  either way.
--
--  Background: docs/superpowers/specs/2026-08-18-cms-batch-{a,b,c}-design.md
-- =====================================================================

-- ── 1 ─────────────────────────────────────────────────────────────────
-- Calibration call-to-action
--
-- contactcalibration.php is retired: its form posted to a third-party
-- endpoint (bazardeal.com.bd) and no enquiry ever reached ESWASA. The page
-- now 301s to the working quote form. The stored CTA still points at the
-- retired page and would override the corrected code default, so it moves
-- to the general contact page — the first CTA on that page already covers
-- quote requests.
-- ----------------------------------------------------------------------
UPDATE page_content
   SET content = 'contact.php'
 WHERE page_key = 'cal_cta_btn2_url'
   AND content  = 'contactcalibration.php';

-- ── 2 ─────────────────────────────────────────────────────────────────
-- Training prospectus download
--
-- Stored path pointed at admin/downloads/, a directory that has never
-- existed, so the Prospectus button returned 404. The file is in
-- admin/uploads/.
-- ----------------------------------------------------------------------
UPDATE page_content
   SET content = REPLACE(content, 'admin/downloads/', 'admin/uploads/')
 WHERE page_key = 'train_cal_prospectus_url'
   AND content LIKE 'admin/downloads/%';

-- ── 3 ─────────────────────────────────────────────────────────────────
-- Recover requester names on existing quote requests
--
-- process_quote.php looked for "full_name" while the individual training
-- form posts "full_names", so those submissions stored contact_name as
-- NULL and the admin inbox showed a dash. The alias list is fixed in code;
-- this recovers the names already captured in the stored raw_form JSON.
-- Guarded to touch only rows actually missing a name that have one to
-- recover.
-- ----------------------------------------------------------------------
UPDATE eswasa_quote_requests
   SET contact_name = JSON_UNQUOTE(JSON_EXTRACT(raw_form, '$.full_names'))
 WHERE (contact_name IS NULL OR contact_name = '')
   AND raw_form IS NOT NULL
   AND JSON_VALID(raw_form)
   AND JSON_EXTRACT(raw_form, '$.full_names') IS NOT NULL
   AND JSON_UNQUOTE(JSON_EXTRACT(raw_form, '$.full_names')) <> '';

UPDATE eswasa_quote_requests
   SET organization = JSON_UNQUOTE(JSON_EXTRACT(raw_form, '$.company_name'))
 WHERE (organization IS NULL OR organization = '')
   AND raw_form IS NOT NULL
   AND JSON_VALID(raw_form)
   AND JSON_EXTRACT(raw_form, '$.company_name') IS NOT NULL
   AND JSON_UNQUOTE(JSON_EXTRACT(raw_form, '$.company_name')) <> '';

-- ── 4 ─────────────────────────────────────────────────────────────────
-- Clear the redirect-loop noise from the audit trail
--
-- Deleting a user looped because redirect_self() did not strip
-- delete_user, so each attempt wrote dozens of identical rows in the same
-- second. Keeps the earliest row per (entity, second) so the trail stays
-- truthful rather than being wiped.
-- ----------------------------------------------------------------------
DELETE FROM activity_log
 WHERE action = 'user.delete'
   AND id NOT IN (
       SELECT keep_id FROM (
           SELECT MIN(id) AS keep_id
             FROM activity_log
            WHERE action = 'user.delete'
            GROUP BY entity, created_at
       ) AS keepers
   );

-- ── 5 ─────────────────────────────────────────────────────────────────
-- Move the About Us breadcrumb onto the shared system
--
-- about-us.php used a private about_breadcrumb_bg key instead of the
-- breadcrumb_bg_{slug} convention every other page follows, which is why
-- it was the one page missing from the Breadcrumb Images screen. Carries
-- the existing image across so the page looks unchanged, then drops the
-- old key.
-- ----------------------------------------------------------------------
INSERT INTO page_content (page_key, content)
SELECT 'breadcrumb_bg_about-us', content
  FROM page_content
 WHERE page_key = 'about_breadcrumb_bg'
   AND content IS NOT NULL
   AND content <> ''
ON DUPLICATE KEY UPDATE content = VALUES(content);

DELETE FROM page_content WHERE page_key = 'about_breadcrumb_bg';

-- ── 6 ─────────────────────────────────────────────────────────────────
-- Remove content superseded by real tables
--
-- ms_doc_1..11_title/_url (22 rows) were Management Systems documents
-- stored as flat key-value URLs; they now live in certification_documents
-- as uploaded PDFs. train_cal_session_1..13_* (52 rows) were the flat
-- training calendar; it now uses training_sessions and training_intakes.
-- Both sets verified unreferenced by any PHP file. 74 rows total.
-- ----------------------------------------------------------------------
DELETE FROM page_content
 WHERE page_key REGEXP '^ms_doc_[0-9]+_(title|url)$';

DELETE FROM page_content
 WHERE page_key REGEXP '^train_cal_session_[0-9]+_(title|date|duration|location)$';

-- ── Verification ──────────────────────────────────────────────────────
-- Read this output. It should show no remaining stale rows, the About Us
-- breadcrumb moved, and no quote request left without a name.
-- ----------------------------------------------------------------------
SELECT 'stale ms_doc / train_cal_session rows (expect 0)' AS check_name,
       COUNT(*) AS value
  FROM page_content
 WHERE page_key REGEXP '^(ms_doc_[0-9]+_(title|url)|train_cal_session_[0-9]+_(title|date|duration|location))$'
UNION ALL
SELECT 'old about_breadcrumb_bg key (expect 0)',
       COUNT(*) FROM page_content WHERE page_key = 'about_breadcrumb_bg'
UNION ALL
SELECT 'new breadcrumb_bg_about-us key (expect 0 or 1)',
       COUNT(*) FROM page_content WHERE page_key = 'breadcrumb_bg_about-us'
UNION ALL
SELECT 'CTA still pointing at the retired page (expect 0)',
       COUNT(*) FROM page_content
 WHERE page_key = 'cal_cta_btn2_url' AND content = 'contactcalibration.php'
UNION ALL
SELECT 'prospectus still on admin/downloads (expect 0)',
       COUNT(*) FROM page_content
 WHERE page_key = 'train_cal_prospectus_url' AND content LIKE 'admin/downloads/%'
UNION ALL
SELECT 'quote requests with no contact name (expect 0)',
       COUNT(*) FROM eswasa_quote_requests
 WHERE contact_name IS NULL OR contact_name = '';

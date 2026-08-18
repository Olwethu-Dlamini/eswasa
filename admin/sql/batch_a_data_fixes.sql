-- admin/sql/batch_a_data_fixes.sql
--
-- Data changes accompanying the Batch A code fixes.
-- See docs/superpowers/specs/2026-08-18-cms-batch-a-design.md
--
-- Every statement is idempotent: re-running this file is harmless.
-- Take a database dump before running it in production.

-- ─────────────────────────────────────────────────────────────────────
-- A1 — Calibration RFQ consolidation
--
-- contactcalibration.php is retired (its form posted to a third-party
-- endpoint and never reached ESWASA). The stored CTA on the Calibration
-- page still points at it, which overrides the corrected code default,
-- so the stored value has to move too.
-- ─────────────────────────────────────────────────────────────────────
-- btn1 on that page already links to the quote form, so this second CTA
-- ("Contact Metrology Unit") goes to the general contact page instead of
-- duplicating btn1's destination.
UPDATE page_content
   SET content = 'contact.php'
 WHERE page_key = 'cal_cta_btn2_url'
   AND content  = 'contactcalibration.php';

-- ─────────────────────────────────────────────────────────────────────
-- A6 — Prospectus download
--
-- The stored path points at admin/downloads/, a directory that has never
-- existed, so the Prospectus button 404s. The file lives in admin/uploads/.
-- ─────────────────────────────────────────────────────────────────────
UPDATE page_content
   SET content = REPLACE(content, 'admin/downloads/', 'admin/uploads/')
 WHERE page_key = 'train_cal_prospectus_url'
   AND content LIKE 'admin/downloads/%';

-- ─────────────────────────────────────────────────────────────────────
-- A7 — Purge the redirect-loop noise from the audit trail
--
-- Deleting a user looped (redirect_self() did not strip delete_user), so
-- each attempt wrote dozens of identical rows in the same second. Keep the
-- earliest row per (entity, second) and drop the duplicates, so the audit
-- trail stays truthful rather than being wiped.
-- ─────────────────────────────────────────────────────────────────────
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

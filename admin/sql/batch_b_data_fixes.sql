-- admin/sql/batch_b_data_fixes.sql
--
-- Data changes accompanying the Batch B code changes.
-- See docs/superpowers/specs/2026-08-18-cms-batch-b-design.md
--
-- Every statement is idempotent: re-running this file is harmless.
-- TAKE A DATABASE DUMP BEFORE RUNNING THIS — it deletes rows.

-- ─────────────────────────────────────────────────────────────────────
-- B2 — Move the About Us breadcrumb onto the shared system
--
-- about-us.php used a private about_breadcrumb_bg key instead of the
-- breadcrumb_bg_{slug} convention every other page follows, which is why it
-- was the one page missing from the Breadcrumb Images screen. Carry the
-- existing image across so the page looks unchanged, then drop the old key.
-- ─────────────────────────────────────────────────────────────────────
INSERT INTO page_content (page_key, content)
SELECT 'breadcrumb_bg_about-us', content
  FROM page_content
 WHERE page_key = 'about_breadcrumb_bg'
   AND content IS NOT NULL
   AND content <> ''
ON DUPLICATE KEY UPDATE content = VALUES(content);

DELETE FROM page_content WHERE page_key = 'about_breadcrumb_bg';

-- ─────────────────────────────────────────────────────────────────────
-- B6 — Remove stale rows superseded by real tables
--
-- ms_doc_1..11_title/_url (22 rows): Management Systems documents used to be
-- stored as flat key-value pairs holding URLs. They now live in the
-- certification_documents table with real uploaded PDFs, and no PHP file
-- reads the old keys.
--
-- train_cal_session_1..13_* (52 rows): the training calendar used to be flat
-- keys. It now uses the training_sessions and training_intakes tables, and no
-- PHP file reads the old keys.
--
-- Both verified unreferenced by grep across the entire codebase before
-- deletion. 74 rows total.
-- ─────────────────────────────────────────────────────────────────────
DELETE FROM page_content
 WHERE page_key REGEXP '^ms_doc_[0-9]+_(title|url)$';

DELETE FROM page_content
 WHERE page_key REGEXP '^train_cal_session_[0-9]+_(title|date|duration|location)$';

-- Patch: clear the sample-standard rows from the Work Programmes page
-- ====================================================================
-- The earlier migration's REPLACE INTO dumped sample data (Face Masks,
-- Solid Waste, ISO 45001, etc.) into the work_item_1..5_* rows. The
-- public Work Programmes page rendered them as if they were real
-- current projects. Wiping the rows makes the page show its empty
-- state ("No projects to display yet") until admin populates real
-- projects via the Work Programmes editor.
--
-- Idempotent: re-running is a no-op once the rows are gone.

DELETE FROM page_content
WHERE page_key REGEXP '^work_item_[1-5]_';

-- Verify
SELECT COUNT(*) AS work_item_rows_left
  FROM page_content
  WHERE page_key REGEXP '^work_item_[1-5]_';

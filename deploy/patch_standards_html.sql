-- Patch: drop 3 HTML-stub rows on the Standards page
-- ===================================================
-- These rows came from a pre-session seed and contained truncated
-- HTML markup like "<p>Under Section 5 of the <em>Standards Act,
-- 1968...</em></p>" — pc_h() escapes the tags so they rendered as
-- literal text on the live page.
--
-- Standards.php has proper full-text defaults baked into its
-- pc_get_many() call. Deleting these rows makes those defaults
-- take effect immediately. Admin can still edit the fields in the
-- Standards editor — saving creates fresh, clean rows.
--
-- Idempotent: re-running on an already-cleaned DB is a no-op.

DELETE FROM page_content
WHERE page_key IN ('standards_mandate', 'standards_process_desc', 'standards_proposal');

-- Verify
SELECT COUNT(*) AS html_stubs_left
  FROM page_content
  WHERE page_key IN ('standards_mandate', 'standards_process_desc', 'standards_proposal');

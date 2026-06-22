-- patch_banner_urls_2026_06_22.sql
-- Go-live fix: the home-page slider banners linked to the old staging domain
-- (http://demo.swasa.co.sz/...). Rewrite any such absolute URL to be
-- domain-relative so the banners work on the production domain.
-- Idempotent: only touches rows that still contain the staging host.

UPDATE banners
SET url = REGEXP_REPLACE(url, '^https?://[^/]*demo\\.swasa\\.co\\.sz/', '')
WHERE url LIKE '%demo.swasa.co.sz%';

-- Verify (expect 0):
-- SELECT COUNT(*) FROM banners WHERE url LIKE '%demo.swasa.co.sz%';

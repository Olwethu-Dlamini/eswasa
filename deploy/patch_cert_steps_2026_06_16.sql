-- ESWASA patch 2026-06-16 — Certification Journey picture
-- The 'Your Certification Journey' infographic was edited to remove the
-- "Gap Analysis" and "Internal Auditing" steps (now 3 steps: Documentation,
-- Implementation, Certification Audit). The edited graphic is the SVG, so point
-- the stored image at it and refresh the alt text. Safe to re-run.
UPDATE page_content SET content = 'assets/img/steps-to-certification.svg'
  WHERE page_key = 'cert_steps_image';
UPDATE page_content SET content = 'Steps to Certification: Documentation, Implementation, Certification Audit'
  WHERE page_key = 'cert_steps_image_alt';

-- ESWASA — Production migration for the 2026-05-27 deploy round.
-- Removes 7 certification policies from the public Policies page.
-- Idempotent — safe to re-run (a second run deletes nothing).
--
-- Apply via phpMyAdmin Import, or:
--   mysql -u <user> -p <db> < migration_2026_05_27.sql
--
-- Sections:
--   1. eswasa_policies — DELETE 7 rows so the Policies page lists only
--      the 5 remaining entries (Impartiality, Complaints Handling,
--      Handling Requests for Information, Privacy, Terms & Conditions).
--      The matching seed in admin/sql/migrate_policies.sql has already
--      been trimmed so fresh installs land in the same state.


-- =====================================================================
-- 1. eswasa_policies — drop 7 certification policies
-- =====================================================================
-- Matched by title (stable across environments — id values differ
-- between dev and prod). The PDFs themselves are NOT removed from the
-- filesystem here; if they should also be deleted from
-- admin/uploads/policies/ or the web root, do that manually after
-- confirming nothing else links to them.
DELETE FROM eswasa_policies WHERE title IN (
    'Appeals Handling Procedure',
    'Rules for Use of the Certification Mark',
    'Grant of Certification Procedure',
    'Suspension, Withdrawal & Reduction of Scope',
    'Extending Scope of Certification',
    'Management Systems Certification Audits',
    'Special Audits Procedure'
);


-- =====================================================================
-- Verification — should print 5 rows, all from the kept set
-- =====================================================================
SELECT id, sort_order, category, title
FROM eswasa_policies
ORDER BY sort_order ASC, id ASC;

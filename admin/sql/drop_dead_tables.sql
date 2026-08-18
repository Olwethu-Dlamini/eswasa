-- admin/sql/drop_dead_tables.sql
--
-- Removes four tables that no code reads or writes.
--
-- ⚠ THIS IS THE ONLY DESTRUCTIVE MIGRATION IN THE SET, AND IT IS DELIBERATELY
--   KEPT SEPARATE FROM THE CONTENT FIXES SO IT CANNOT BE RUN BY ACCIDENT.
--   TAKE A DATABASE DUMP FIRST. Dropped tables cannot be recovered from the
--   application — only from that dump.
--
-- Nothing else depends on running this. The site behaves identically with these
-- tables present; they are dead weight that makes the schema misleading to read.
-- Skipping it is a perfectly reasonable choice.
--
-- Identified during the Batch A audit and scheduled in
-- docs/superpowers/specs/2026-08-18-cms-batch-b-design.md (§5, §6).

-- ─────────────────────────────────────────────────────────────────────
-- Why each one is safe to drop
--
--   eswasa_board_members (5 rows)
--     Superseded by eswasa_team_members. All five people were migrated across
--     as section='council' (ids 12-16) with identical names, roles, photos and
--     ordering, and the reporter confirmed the content now lives entirely in
--     Meet Our Team. No page renders this table and no board-members page
--     exists.
--
--   admin (1 row)
--     Legacy login table holding a single username and bcrypt hash. Replaced by
--     `users`, which is what admin/login.php authenticates against. Nothing
--     queries it.
--
--   site_statistics (3 rows)
--     Placeholder counters — members 1000, events 999, partners 999. Never
--     rendered anywhere.
--
--   blogs (0 rows)
--     Empty, never used.
--
-- Verified by grepping the entire codebase for each table name: the only
-- occurrence of "admin" near a SQL keyword is an HTML comment in
-- Meetourteam.php, and the other three appear nowhere in any PHP file.
-- ─────────────────────────────────────────────────────────────────────

-- Run this first and read the output. Every count should be 0. If any row
-- appears, stop — something references a table this script is about to remove.
SELECT TABLE_NAME, TABLE_ROWS
  FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME IN ('eswasa_board_members', 'admin', 'site_statistics', 'blogs');

-- Then the drops. IF EXISTS so a re-run is harmless.
DROP TABLE IF EXISTS `eswasa_board_members`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `site_statistics`;
DROP TABLE IF EXISTS `blogs`;

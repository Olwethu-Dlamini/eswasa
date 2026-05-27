-- ESWASA — Production migration for the 2026-05-27 deploy round.
-- Idempotent — safe to re-run (DELETE is title-scoped, table creates
-- use IF NOT EXISTS, seeds use INSERT IGNORE on explicit IDs).
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
--   2. training_sessions + training_intakes — new tables backing the
--      Training Calendar admin (replaces the 13 hard-coded slots).
--      Seeded with the existing 13 sessions and 28 intakes so the public
--      page renders unchanged on day-zero.


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


-- =====================================================================
-- 2. training_sessions / training_intakes — new tables
-- =====================================================================
-- Replaces the 13 hard-coded slots in training-calendar.php and the
-- limited 13-slot CMS form in admin/pages/training_calendar.php.
-- The admin gets a proper add/edit/delete UI; the public page reads
-- straight from these tables.

CREATE TABLE IF NOT EXISTS training_sessions (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(32)  NOT NULL,
    family      VARCHAR(64)  NOT NULL,
    title       VARCHAR(500) NOT NULL,
    location    VARCHAR(128) NOT NULL DEFAULT 'Mbabane',
    duration    VARCHAR(64)  NOT NULL DEFAULT '5 days',
    price       VARCHAR(64)  NOT NULL DEFAULT '',
    sort_order  INT          NOT NULL DEFAULT 0,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sort (sort_order, id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS training_intakes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    session_id  INT          NOT NULL,
    start_date  DATE         NOT NULL,
    end_date    DATE         NOT NULL,
    label       VARCHAR(128) NOT NULL,
    sort_order  INT          NOT NULL DEFAULT 0,
    INDEX idx_session (session_id, sort_order, id),
    INDEX idx_start (start_date),
    CONSTRAINT fk_intake_session
        FOREIGN KEY (session_id) REFERENCES training_sessions(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Seed: 13 sessions ────────────────────────────────────────────────
INSERT IGNORE INTO training_sessions (id, code, family, title, location, duration, price, sort_order, is_active) VALUES
 (1,  'QMS 02',  'Quality',         'Quality Management Systems — SZNS ISO 9001:2015 — Understanding & Implementation', 'Mbabane', '5 days', '', 1,  1),
 (2,  'QMS 03',  'Auditing',        'Quality Management Systems — Internal Auditing — SZNS ISO 19011:2018',             'Mbabane', '5 days', '', 2,  1),
 (3,  'FSMS 02', 'Food Safety',     'Food Safety Management Systems — SZNS ISO 22000:2018 — Understanding & Implementation', 'Mbabane', '5 days', '', 3,  1),
 (4,  'FSMS 03', 'Auditing',        'Food Safety Management Systems — Internal Auditing — SZNS ISO 19011:2018',         'Mbabane', '5 days', '', 4,  1),
 (5,  'FS 01',   'Food Safety',     'Hazard Analysis & Critical Control Points (HACCP) — SZNS ISO 10330:2020 — Understanding & Implementation', 'Mbabane', '5 days', '', 5,  1),
 (6,  'OHS 02',  'Health & Safety', 'Occupational Health & Safety Management Systems — SZNS ISO 45001:2018 — Understanding & Implementation', 'Mbabane', '5 days', '', 6,  1),
 (7,  'OHS 01',  'Health & Safety', 'SHE Rep — Safety, Health and Environment Representative',                          'Mbabane', '5 days', '', 7,  1),
 (8,  'RCA 02',  'Health & Safety', 'Root Cause Analysis / Incident Investigation — Understanding & Implementation',    'Mbabane', '5 days', '', 8,  1),
 (9,  'HM 02',   'Hazmat',          'Hazmat — Hazardous Material — Understanding & Implementation',                     'Mbabane', '5 days', '', 9,  1),
 (10, 'EMS 02',  'Environmental',   'Environmental Management Systems — SZNS ISO 14001:2015 — Understanding & Implementation', 'Mbabane', '5 days', '', 10, 1),
 (11, 'ERM 02',  'Risk',            'Enterprise Risk Management — SZNS ISO 31000:2018 — Understanding & Implementation', 'Mbabane', '5 days', '', 11, 1),
 (12, 'WDM 02',  'Wellness',        'Wellness and Disease Management Systems — SZNS SANS 16001:2013 — Understanding & Implementation', 'Mbabane', '5 days', '', 12, 1),
 (13, 'GAP 02',  'Agriculture',     'Global GAP — Integrated Farm Assurance',                                           'Mbabane', '5 days', '', 13, 1);

-- Re-affirm sort_order on the seeded rows by `code` (stable across envs).
-- Idempotent — and renumbers any dev DB that already received the original
-- 10/20/30 seed values to the cleaner 1..13 sequence.
UPDATE training_sessions SET sort_order =  1 WHERE code = 'QMS 02';
UPDATE training_sessions SET sort_order =  2 WHERE code = 'QMS 03';
UPDATE training_sessions SET sort_order =  3 WHERE code = 'FSMS 02';
UPDATE training_sessions SET sort_order =  4 WHERE code = 'FSMS 03';
UPDATE training_sessions SET sort_order =  5 WHERE code = 'FS 01';
UPDATE training_sessions SET sort_order =  6 WHERE code = 'OHS 02';
UPDATE training_sessions SET sort_order =  7 WHERE code = 'OHS 01';
UPDATE training_sessions SET sort_order =  8 WHERE code = 'RCA 02';
UPDATE training_sessions SET sort_order =  9 WHERE code = 'HM 02';
UPDATE training_sessions SET sort_order = 10 WHERE code = 'EMS 02';
UPDATE training_sessions SET sort_order = 11 WHERE code = 'ERM 02';
UPDATE training_sessions SET sort_order = 12 WHERE code = 'WDM 02';
UPDATE training_sessions SET sort_order = 13 WHERE code = 'GAP 02';


-- ── Seed: 28 intakes ─────────────────────────────────────────────────
INSERT IGNORE INTO training_intakes (id, session_id, start_date, end_date, label, sort_order) VALUES
 -- QMS 02 (4)
 (1,  1, '2026-05-18', '2026-05-22', '18–22 May',                    10),
 (2,  1, '2026-07-13', '2026-07-17', '13–17 July',                   20),
 (3,  1, '2026-10-05', '2026-10-09', '5–9 October',                  30),
 (4,  1, '2026-12-07', '2026-12-11', '7–11 December',                40),
 -- QMS 03 (2)
 (5,  2, '2026-07-20', '2026-07-24', '20–24 July',                   10),
 (6,  2, '2026-09-07', '2026-09-11', '7–11 September',               20),
 -- FSMS 02 (4)
 (7,  3, '2026-06-01', '2026-06-05', '1–5 June',                     10),
 (8,  3, '2026-08-17', '2026-08-21', '17–21 August',                 20),
 (9,  3, '2026-10-26', '2026-10-30', '26–30 October',                30),
 (10, 3, '2026-12-14', '2026-12-18', '14–18 December',               40),
 -- FSMS 03 (2)
 (11, 4, '2026-07-27', '2026-07-31', '27–31 July',                   10),
 (12, 4, '2026-11-30', '2026-12-04', '30 November – 4 December',     20),
 -- FS 01 (1)
 (13, 5, '2026-08-03', '2026-08-07', '3–7 August',                   10),
 -- OHS 02 (3)
 (14, 6, '2026-06-08', '2026-06-12', '8–12 June',                    10),
 (15, 6, '2026-08-24', '2026-08-28', '24–28 August',                 20),
 (16, 6, '2026-11-02', '2026-11-06', '2–6 November',                 30),
 -- OHS 01 (1)
 (17, 7, '2026-09-21', '2026-09-25', '21–25 September',              10),
 -- RCA 02 (1)
 (18, 8, '2026-08-10', '2026-08-14', '10–14 August',                 10),
 -- HM 02 (2)
 (19, 9, '2026-05-25', '2026-05-29', '25–29 May',                    10),
 (20, 9, '2026-11-09', '2026-11-13', '9–13 November',                20),
 -- EMS 02 (3)
 (21, 10, '2026-06-15', '2026-06-19', '15–19 June',                  10),
 (22, 10, '2026-09-14', '2026-09-18', '14–18 September',             20),
 (23, 10, '2026-11-23', '2026-11-27', '23–27 November',              30),
 -- ERM 02 (2)
 (24, 11, '2026-06-22', '2026-06-26', '22–26 June',                  10),
 (25, 11, '2026-10-19', '2026-10-23', '19–23 October',               20),
 -- WDM 02 (1)
 (26, 12, '2026-06-29', '2026-07-03', '29 June – 3 July',            10),
 -- GAP 02 (2)
 (27, 13, '2026-07-06', '2026-07-10', '6–10 July',                   10),
 (28, 13, '2026-10-12', '2026-10-16', '12–16 October',               20);


-- =====================================================================
-- Verification — should print 13 sessions and 28 intakes
-- =====================================================================
SELECT 'training_sessions' AS table_name, COUNT(*) AS row_count FROM training_sessions
UNION ALL
SELECT 'training_intakes', COUNT(*) FROM training_intakes;

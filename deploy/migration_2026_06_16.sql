-- ESWASA migration 2026-06-16
-- Adds grouped "folders" to Publications: a groups table (system type groups
-- + custom folders) and a group_id column on publications.
-- Safe to re-run: uses IF NOT EXISTS / ON DUPLICATE KEY / information_schema guards.

-- ── 1. NEW TABLE: eswasa_publication_groups ───────────────────
-- One row per section shown on publications.php. type_key set => automatic
-- group collecting all publications of that pub_type; type_key NULL => custom
-- folder (publications opt in via eswasa_publications.group_id). is_system = 1
-- marks the seeded type groups (admin may rename/reorder, not delete).
CREATE TABLE IF NOT EXISTS eswasa_publication_groups (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    type_key    VARCHAR(30) DEFAULT NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    is_system   TINYINT NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_type_key (type_key),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ── 2. SEED the 5 system type groups (idempotent) ─────────────
-- NULL type_keys (custom folders) are exempt from the unique key, so this only
-- ever inserts/keeps these five rows. Re-running leaves existing names intact.
INSERT INTO eswasa_publication_groups (name, type_key, sort_order, is_system) VALUES
    ('Annual Reports',     'annual_report', 10, 1),
    ('Reports',            'report',        20, 1),
    ('Guidance Documents', 'guidance',      30, 1),
    ('Newsletters',        'newsletter',    40, 1),
    ('Standards',          'standard',      50, 1)
ON DUPLICATE KEY UPDATE is_system = 1;

-- ── 3. ADD COLUMN eswasa_publications.group_id (guarded) ──────
-- Holds the custom-folder assignment. NULL => publication is auto-grouped by
-- its pub_type. (No FK: folder deletion clears this in app code, matching the
-- codebase's manual-delete style.)
SET @col_exists := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'eswasa_publications'
      AND COLUMN_NAME = 'group_id'
);
SET @sql := IF(@col_exists = 0,
    'ALTER TABLE eswasa_publications ADD COLUMN group_id INT DEFAULT NULL AFTER pub_type, ADD INDEX idx_group_id (group_id)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

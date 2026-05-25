-- Patch: move Work Programmes from flat page_content slots to a real table
-- =========================================================================
-- The Work Programmes page no longer has 5 fixed slots. Admin can add as
-- many programmes as they want via a CRUD editor backed by this table.
-- Idempotent: CREATE TABLE IF NOT EXISTS + DELETE of the old flat rows.

CREATE TABLE IF NOT EXISTS eswasa_work_programmes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    url VARCHAR(500) DEFAULT NULL,
    details TEXT DEFAULT NULL,
    status_label VARCHAR(100) DEFAULT 'Published',
    status_class ENUM('status-published','status-underdev','status-revision') NOT NULL DEFAULT 'status-published',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sort (sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Drop legacy flat slots — superseded by the table above.
DELETE FROM page_content WHERE page_key REGEXP '^work_item_[1-5]_';

SELECT COUNT(*) AS work_programme_rows FROM eswasa_work_programmes;
SELECT COUNT(*) AS leftover_flat_slots FROM page_content WHERE page_key REGEXP '^work_item_[1-5]_';

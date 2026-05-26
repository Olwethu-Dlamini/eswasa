-- ESWASA — Production migration for the 2026-05-26 deploy round.
-- Covers every DB change since the previous deploy (migration_2026_05_25.sql
-- + patch_standards_html.sql). Idempotent — safe to re-run.
--
-- Apply via phpMyAdmin Import, or:
--   mysql -u <user> -p <db> < migration_2026_05_26.sql
--
-- Sections:
--   1. eswasa_work_programmes — new table backing the rebuilt Work Programmes
--      page + admin CRUD. Wipes the old flat page_content slots.
--   2. eswasa_policies — new table backing the rebuilt Policies page.
--      Seeded with the 12 entries that used to be a hardcoded array.
--   3. eswasa_customer_feedback — new table for public feedback submissions
--      (plus the is_read column the admin Inbox depends on).
--
-- Tables that auto-create themselves on first request (no migration needed,
-- listed here for documentation):
--   - eswasa_event_images  (created lazily by includes/event_images.php
--                           when an event page is viewed or edited)


-- =====================================================================
-- 1. eswasa_work_programmes
-- =====================================================================
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

-- Drop the old flat page_content slots — superseded by the table above.
DELETE FROM page_content WHERE page_key REGEXP '^work_item_[1-5]_';


-- =====================================================================
-- 2. eswasa_policies (+ 12-row seed)
-- =====================================================================
CREATE TABLE IF NOT EXISTS eswasa_policies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    icon VARCHAR(50) NOT NULL DEFAULT 'fa-file-alt',
    category VARCHAR(100) NOT NULL DEFAULT 'General',
    sort_order INT NOT NULL DEFAULT 0,
    is_internal TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sort (sort_order),
    INDEX idx_category (category)
);

-- Seed the 12 hardcoded entries — only inserts if the table is empty,
-- so re-running this migration won't duplicate rows.
INSERT INTO eswasa_policies (title, description, file_path, icon, category, sort_order, is_internal)
SELECT * FROM (
    SELECT 'Impartiality Policy' AS title,
           'Our public commitment to impartial, independent certification decisions and the safeguards we apply.' AS description,
           'Impartiality Policy - SWASA Certification.pdf' AS file_path,
           'fa-balance-scale' AS icon,
           'Certification' AS category,
           10 AS sort_order,
           0 AS is_internal
    UNION ALL SELECT 'Complaints Handling Procedure',
              'How we receive, investigate and resolve complaints — including timelines and the escalation path.',
              'CER_PR_006 PROCEDURE FOR COMPLAINTS HANDLING.pdf',
              'fa-comment-alt', 'Customer Care', 20, 0
    UNION ALL SELECT 'Appeals Handling Procedure',
              'The formal route to challenge a certification decision, including timeframes and the appeals committee.',
              'CER_PR_002 PROCEDURE FOR APPEALS HANDLING.pdf',
              'fa-gavel', 'Certification', 30, 0
    UNION ALL SELECT 'Rules for Use of the Certification Mark',
              'Conditions, restrictions and obligations governing how certified clients may display the ESWASA mark.',
              'CER_RU_028 RULES FOR THE USE OF THE CERTIFICATION MARK.pdf',
              'fa-certificate', 'Certification', 40, 0
    UNION ALL SELECT 'Handling Requests for Information',
              'How we manage public information requests, including what is publicly available and what is confidential.',
              'CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf',
              'fa-info-circle', 'Information', 50, 0
    UNION ALL SELECT 'Grant of Certification Procedure',
              'The end-to-end process for granting initial certification, from application through audit to award.',
              'CER_PR_014 GRANT OF CERTIFICATION PROCEDURE.pdf',
              'fa-clipboard-check', 'Certification', 60, 0
    UNION ALL SELECT 'Suspension, Withdrawal & Reduction of Scope',
              'When and how a certification may be suspended, withdrawn, or reduced in scope, and the client''s rights.',
              'CER_PR_026 PROCEDURE FOR SUSPENSION WITHDRAWAL REDUCED SCOPE OF CERTIFICATION.pdf',
              'fa-ban', 'Certification', 70, 0
    UNION ALL SELECT 'Extending Scope of Certification',
              'How an existing certified client can apply to extend the scope of an issued certificate.',
              'CER_PR_012 EXTENDING SCOPE OF CERTIFICATION PROCEDURE.pdf',
              'fa-expand-arrows-alt', 'Certification', 80, 0
    UNION ALL SELECT 'Management Systems Certification Audits',
              'How audits are planned, conducted and reported under the ESWASA Management Systems Certification scheme.',
              'CER_PR_020 PROCEDURE FOR MANAGEMENT SYSTEMS CERTIFICATION AUDITS.pdf',
              'fa-tasks', 'Certification', 90, 0
    UNION ALL SELECT 'Special Audits Procedure',
              'When special audits are triggered, what they involve and what clients can expect.',
              'CER_PR_028 SPECIAL AUDITS PROCEDURE.pdf',
              'fa-search', 'Certification', 100, 0
    UNION ALL SELECT 'Privacy Policy',
              'How we collect, use and protect personal information submitted through our website and services.',
              'privacy.php', 'fa-user-shield', 'Information', 110, 1
    UNION ALL SELECT 'Terms & Conditions',
              'The terms governing your use of the ESWASA website and online services.',
              'terms.php', 'fa-file-signature', 'Information', 120, 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM eswasa_policies);


-- =====================================================================
-- 3. eswasa_customer_feedback (with is_read column for the admin Inbox)
-- =====================================================================
CREATE TABLE IF NOT EXISTS eswasa_customer_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service VARCHAR(150),
    feedback_type VARCHAR(50),
    resolved VARCHAR(20),
    issue TEXT,
    rating TINYINT,
    suggestion TEXT,
    email VARCHAR(150),
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- If a deploy of customer-feedback.php somehow created the table BEFORE this
-- migration ran (i.e. without the is_read column), patch it in. This block is
-- wrapped in a procedure so it tolerates older MySQL versions that don't
-- support ADD COLUMN IF NOT EXISTS.
DROP PROCEDURE IF EXISTS _eswasa_add_is_read;
DELIMITER //
CREATE PROCEDURE _eswasa_add_is_read()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = 'eswasa_customer_feedback'
          AND column_name = 'is_read'
    ) THEN
        ALTER TABLE eswasa_customer_feedback
            ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0,
            ADD INDEX idx_is_read (is_read);
    END IF;
END //
DELIMITER ;
CALL _eswasa_add_is_read();
DROP PROCEDURE _eswasa_add_is_read;


-- =====================================================================
-- Verification — should be safe to ignore if the counts look reasonable
-- =====================================================================
SELECT
    (SELECT COUNT(*) FROM eswasa_work_programmes)   AS work_programme_rows,
    (SELECT COUNT(*) FROM page_content WHERE page_key REGEXP '^work_item_[1-5]_') AS leftover_flat_work_slots,
    (SELECT COUNT(*) FROM eswasa_policies)          AS policy_rows,
    (SELECT COUNT(*) FROM eswasa_customer_feedback) AS feedback_rows;

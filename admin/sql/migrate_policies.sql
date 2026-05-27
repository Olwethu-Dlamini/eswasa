-- One-time migration: move policies.php's hardcoded array into a dedicated table.
-- Idempotent: CREATE TABLE IF NOT EXISTS + INSERT only if the table is empty.

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
    UNION ALL SELECT 'Handling Requests for Information',
              'How we manage public information requests, including what is publicly available and what is confidential.',
              'CER_PR_015 HANDLING REQUESTS FOR INFORMATION.pdf',
              'fa-info-circle', 'Information', 50, 0
    UNION ALL SELECT 'Privacy Policy',
              'How we collect, use and protect personal information submitted through our website and services.',
              'privacy.php', 'fa-user-shield', 'Information', 110, 1
    UNION ALL SELECT 'Terms & Conditions',
              'The terms governing your use of the ESWASA website and online services.',
              'terms.php', 'fa-file-signature', 'Information', 120, 1
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM eswasa_policies);

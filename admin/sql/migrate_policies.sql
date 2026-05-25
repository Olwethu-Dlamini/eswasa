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

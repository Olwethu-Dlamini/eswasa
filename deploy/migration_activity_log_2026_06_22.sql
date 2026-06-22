-- migration_activity_log_2026_06_22.sql
-- Audit trail: records who did what in the admin (logins, user changes,
-- content saves). Read-only history; safe to re-run.

CREATE TABLE IF NOT EXISTS activity_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NULL,
    username    VARCHAR(50) NULL,
    action      VARCHAR(100) NOT NULL,
    entity      VARCHAR(190) NULL,
    details     TEXT NULL,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_created (created_at),
    INDEX idx_activity_user (user_id),
    INDEX idx_activity_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

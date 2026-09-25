-- =====================================================================
--  ESWASA CMS — production upgrade, 2026-09-25  (form notifications)
--
--  Run this ONCE against the live database after deploying the code.
--
--    mysql -u eswasa1 -p admin_eswasa < admin/sql/upgrade_2026_09_25.sql
--
--  TAKE A DUMP FIRST:
--    mysqldump -u eswasa1 -p admin_eswasa > eswasa_pre_upgrade_$(date +%F).sql
--
--  Additive and idempotent: every table is CREATE ... IF NOT EXISTS and
--  every column change is guarded on whether it has already been made.
--  Running it twice is harmless. It drops nothing.
-- =====================================================================

-- ── 1 ─────────────────────────────────────────────────────────────────
-- mail_log — one row per email the site tries to send
--
-- Form notifications used to be sent with @mail(), so a failure left no
-- trace. Every attempt is now recorded, with the error when there is one,
-- and the latest are listed in Admin › Site Settings.
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mail_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `context` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recipients` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `transport` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mail',
  `status` enum('sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `error` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 2 ─────────────────────────────────────────────────────────────────
-- eswasa_training_applications — the training calendar's "Apply" form
--
-- The form had no action and its inputs had no names: a script showed
-- "your application has been submitted" and threw the application away.
-- Applications are now stored here and listed under Training › Applications.
--
-- training_code / training_title / intake_label are copied at the time of
-- applying, so an application still reads correctly after the calendar is
-- edited or the training is removed. session_id is kept for filtering only
-- and deliberately has no foreign key: deleting a training must not delete
-- the people who applied for it.
-- ----------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `eswasa_training_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) DEFAULT NULL,
  `training_code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `intake_start` date DEFAULT NULL,
  `intake_label` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','viewed','contacted','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `read_at` datetime DEFAULT NULL,
  `read_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`, `created_at`),
  KEY `idx_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── 3 ─────────────────────────────────────────────────────────────────
-- Opening a submission in the admin marks it as viewed
--
-- read_at / read_by record when, and by whom, a submission was first opened.
-- Quote requests gain a 'viewed' status between 'new' and 'in_progress'.
--
-- ORDER MATTERS. The enum change runs before read_at is added, and the
-- admin only sets 'viewed' in the same UPDATE that sets read_at. Production
-- runs MariaDB 10.1, which is not in strict mode by default, so an UPDATE to
-- an enum value that does not exist yet would silently store '' instead of
-- failing. With this order, read_at existing means 'viewed' exists too.
-- ----------------------------------------------------------------------

-- Customer feedback is created on first use by the public page, so on a
-- fresh host it may not exist yet. Create it with its full shape first so the
-- column changes below always have a table to work on.
CREATE TABLE IF NOT EXISTS `eswasa_customer_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `feedback_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue` text COLLATE utf8mb4_unicode_ci,
  `rating` tinyint(4) DEFAULT NULL,
  `suggestion` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'eswasa_quote_requests'
        AND COLUMN_NAME = 'status' AND COLUMN_TYPE LIKE '%''viewed''%') > 0,
    'SELECT ''eswasa_quote_requests.status already has viewed''',
    'ALTER TABLE `eswasa_quote_requests`
        MODIFY COLUMN `status` enum(''new'',''viewed'',''in_progress'',''closed'') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''new'''
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'eswasa_quote_requests' AND COLUMN_NAME = 'read_at') > 0,
    'SELECT ''column eswasa_quote_requests.read_at already present''',
    'ALTER TABLE `eswasa_quote_requests`
        ADD COLUMN `read_at` datetime DEFAULT NULL AFTER `notes`,
        ADD COLUMN `read_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `read_at`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'eswasa_contact_messages' AND COLUMN_NAME = 'read_at') > 0,
    'SELECT ''column eswasa_contact_messages.read_at already present''',
    'ALTER TABLE `eswasa_contact_messages`
        ADD COLUMN `read_at` datetime DEFAULT NULL AFTER `status`,
        ADD COLUMN `read_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `read_at`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'eswasa_customer_feedback' AND COLUMN_NAME = 'read_at') > 0,
    'SELECT ''column eswasa_customer_feedback.read_at already present''',
    'ALTER TABLE `eswasa_customer_feedback`
        ADD COLUMN `read_at` datetime DEFAULT NULL AFTER `is_read`,
        ADD COLUMN `read_by` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `read_at`'
);
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

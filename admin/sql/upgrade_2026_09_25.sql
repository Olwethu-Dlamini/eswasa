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

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

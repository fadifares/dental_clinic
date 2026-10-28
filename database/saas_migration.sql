-- ==============================================================
-- Dental Clinic SaaS Multi-Tenancy Database Update Script
-- Hostinger MySQL / phpMyAdmin Direct Execution
-- ==============================================================

-- 1. Create clinics table
CREATE TABLE IF NOT EXISTS `clinics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `max_users` int unsigned NOT NULL DEFAULT '10',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clinics_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Add SaaS columns to users table
ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL AFTER `id`,
  ADD COLUMN IF NOT EXISTS `is_super_admin` tinyint(1) NOT NULL DEFAULT '0' AFTER `role`,
  ADD COLUMN IF NOT EXISTS `is_primary_owner` tinyint(1) NOT NULL DEFAULT '0' AFTER `is_super_admin`,
  ADD COLUMN IF NOT EXISTS `last_login_at` datetime DEFAULT NULL AFTER `is_active`,
  ADD COLUMN IF NOT EXISTS `last_login_ip` varchar(255) DEFAULT NULL AFTER `last_login_at`;

-- Add foreign key constraint to users if not present
SET @fk_exists := (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = 'fk_users_clinic_id');
SET @sql_users_fk := IF(@fk_exists = 0, 'ALTER TABLE `users` ADD CONSTRAINT `fk_users_clinic_id` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE SET NULL;', 'SELECT "users fk exists";');
PREPARE stmt FROM @sql_users_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Add clinic_id to operational tables
ALTER TABLE `patients` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `doctors` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `appointments` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `dental_charts` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `lab_orders` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `treatment_plans` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `expenses` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;
ALTER TABLE `settings` ADD COLUMN IF NOT EXISTS `clinic_id` bigint unsigned DEFAULT NULL;

-- 4. Create clinic_page_visits table
CREATE TABLE IF NOT EXISTS `clinic_page_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GET',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_clinic_page_visits_clinic_created` (`clinic_id`, `created_at`),
  KEY `idx_clinic_page_visits_clinic_url` (`clinic_id`, `url_path`),
  CONSTRAINT `fk_visits_clinic_id` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Create clinic_user_activities table
CREATE TABLE IF NOT EXISTS `clinic_user_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clinic_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_clinic_activities_clinic_created` (`clinic_id`, `created_at`),
  KEY `idx_clinic_activities_clinic_user` (`clinic_id`, `user_id`),
  KEY `idx_clinic_activities_clinic_action` (`clinic_id`, `action_type`),
  CONSTRAINT `fk_activities_clinic_id` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_activities_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Insert Default Clinic #1 if not exists
INSERT INTO `clinics` (`id`, `name`, `slug`, `phone`, `email`, `address`, `status`, `max_users`, `created_at`, `updated_at`)
SELECT 1, 'العيادة الأولى', 'clinic-1', '+201553408200', 'info@dentalclinic.com', 'الفرع الرئيسي', 'active', 10, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `clinics` WHERE `id` = 1);

-- 7. Migrate all existing unlinked data to Clinic #1
UPDATE `users` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `patients` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `doctors` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `appointments` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `dental_charts` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `lab_orders` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `invoices` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `treatment_plans` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `expenses` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;
UPDATE `settings` SET `clinic_id` = 1 WHERE `clinic_id` IS NULL;

-- 8. Assign Primary Owner & Super Admin to the first user
UPDATE `users` SET `is_primary_owner` = 1, `is_super_admin` = 1 WHERE `id` = 1;

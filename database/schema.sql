-- DDWB Database Schema
-- MySQL 8+ / MariaDB 10.6+
-- Character set: utf8mb4
-- Collation: utf8mb4_unicode_ci

-- ============================================
-- Create Database
-- ============================================

CREATE DATABASE IF NOT EXISTS `ddwb`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `ddwb`;

-- ============================================
-- Users Table
-- ============================================

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Categories Table
-- ============================================

CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `parent_id` INT UNSIGNED NULL DEFAULT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_categories_parent` (`parent_id`),
    KEY `idx_categories_sort_order` (`sort_order`),
    KEY `idx_categories_active` (`active`),
    KEY `idx_categories_deleted` (`deleted_at`),
    
    CONSTRAINT `fk_categories_parent` 
        FOREIGN KEY (`parent_id`) 
        REFERENCES `categories` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Devices Table
-- ============================================

CREATE TABLE IF NOT EXISTS `devices` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `internal_id` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `category_id` INT UNSIGNED NULL DEFAULT NULL,
    `serial_number` VARCHAR(100) NULL DEFAULT NULL,
    `status` ENUM('available', 'in_case', 'lent_out', 'maintenance') NOT NULL DEFAULT 'available',
    `location` VARCHAR(100) NULL DEFAULT NULL,
    `purchase_date` DATE NULL DEFAULT NULL,
    `purchase_price` DECIMAL(10,2) NULL DEFAULT NULL,
    `warranty_expires` DATE NULL DEFAULT NULL,
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_devices_internal_id` (`internal_id`),
    KEY `idx_devices_serial_number` (`serial_number`),
    KEY `idx_devices_category` (`category_id`),
    KEY `idx_devices_status` (`status`),
    KEY `idx_devices_deleted` (`deleted_at`),
    FULLTEXT KEY `idx_devices_search` (`internal_id`, `name`, `serial_number`, `description`),
    
    CONSTRAINT `fk_devices_category` 
        FOREIGN KEY (`category_id`) 
        REFERENCES `categories` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Cases Table
-- ============================================

CREATE TABLE IF NOT EXISTS `cases` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `internal_id` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `status` ENUM('available', 'lent_out', 'maintenance') NOT NULL DEFAULT 'available',
    `location` VARCHAR(100) NULL DEFAULT NULL,
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_cases_internal_id` (`internal_id`),
    KEY `idx_cases_status` (`status`),
    KEY `idx_cases_deleted` (`deleted_at`),
    FULLTEXT KEY `idx_cases_search` (`internal_id`, `name`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Case Device (Many-to-Many Relationship)
-- ============================================

CREATE TABLE IF NOT EXISTS `case_device` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `case_id` INT UNSIGNED NOT NULL,
    `device_id` INT UNSIGNED NOT NULL,
    `assigned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `assigned_by` INT UNSIGNED NULL DEFAULT NULL,
    `notes` TEXT NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_case_device_unique` (`case_id`, `device_id`),
    KEY `idx_case_device_case` (`case_id`),
    KEY `idx_case_device_device` (`device_id`),
    KEY `idx_case_device_assigned_by` (`assigned_by`),
    
    CONSTRAINT `fk_case_device_case` 
        FOREIGN KEY (`case_id`) 
        REFERENCES `cases` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_case_device_device` 
        FOREIGN KEY (`device_id`) 
        REFERENCES `devices` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_case_device_assigned_by` 
        FOREIGN KEY (`assigned_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Rentals Table
-- ============================================

CREATE TABLE IF NOT EXISTS `rentals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `device_id` INT UNSIGNED NULL DEFAULT NULL,
    `case_id` INT UNSIGNED NULL DEFAULT NULL,
    `borrower` VARCHAR(255) NOT NULL,
    `borrower_email` VARCHAR(255) NULL DEFAULT NULL,
    `borrower_phone` VARCHAR(50) NULL DEFAULT NULL,
    `date_out` DATETIME NOT NULL,
    `expected_return` DATETIME NOT NULL,
    `actual_return` DATETIME NULL DEFAULT NULL,
    `status` ENUM('active', 'returned', 'overdue') NOT NULL DEFAULT 'active',
    `notes` TEXT NULL DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `returned_by` INT UNSIGNED NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_rentals_device` (`device_id`),
    KEY `idx_rentals_case` (`case_id`),
    KEY `idx_rentals_borrower` (`borrower`),
    KEY `idx_rentals_status` (`status`),
    KEY `idx_rentals_date_out` (`date_out`),
    KEY `idx_rentals_expected_return` (`expected_return`),
    KEY `idx_rentals_actual_return` (`actual_return`),
    KEY `idx_rentals_created_by` (`created_by`),
    KEY `idx_rentals_returned_by` (`returned_by`),
    KEY `idx_rentals_deleted` (`deleted_at`),
    
    CONSTRAINT `fk_rentals_device` 
        FOREIGN KEY (`device_id`) 
        REFERENCES `devices` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_rentals_case` 
        FOREIGN KEY (`case_id`) 
        REFERENCES `cases` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_rentals_created_by` 
        FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_rentals_returned_by` 
        FOREIGN KEY (`returned_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Maintenance Table
-- ============================================

CREATE TABLE IF NOT EXISTS `maintenance` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `device_id` INT UNSIGNED NOT NULL,
    `type` VARCHAR(100) NOT NULL DEFAULT 'DGUV3',
    `last_inspection_date` DATE NOT NULL,
    `interval_months` INT UNSIGNED NOT NULL DEFAULT 12,
    `next_inspection_date` DATE NOT NULL,
    `status` ENUM('ok', 'upcoming', 'due', 'overdue') NOT NULL DEFAULT 'ok',
    `inspector` VARCHAR(255) NULL DEFAULT NULL,
    `notes` TEXT NULL DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_maintenance_device` (`device_id`),
    KEY `idx_maintenance_type` (`type`),
    KEY `idx_maintenance_last_inspection` (`last_inspection_date`),
    KEY `idx_maintenance_next_inspection` (`next_inspection_date`),
    KEY `idx_maintenance_status` (`status`),
    KEY `idx_maintenance_deleted` (`deleted_at`),
    
    CONSTRAINT `fk_maintenance_device` 
        FOREIGN KEY (`device_id`) 
        REFERENCES `devices` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_maintenance_created_by` 
        FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Packlists Table
-- ============================================

CREATE TABLE IF NOT EXISTS `packlists` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `status` ENUM('draft', 'active', 'completed', 'archived') NOT NULL DEFAULT 'draft',
    `notes` TEXT NULL DEFAULT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_packlists_name` (`name`),
    KEY `idx_packlists_status` (`status`),
    KEY `idx_packlists_created_by` (`created_by`),
    KEY `idx_packlists_deleted` (`deleted_at`),
    FULLTEXT KEY `idx_packlists_search` (`name`, `description`),
    
    CONSTRAINT `fk_packlists_created_by` 
        FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Packlist Items Table
-- ============================================

CREATE TABLE IF NOT EXISTS `packlist_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `packlist_id` INT UNSIGNED NOT NULL,
    `item_type` ENUM('device', 'case') NOT NULL DEFAULT 'device',
    `item_id` INT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `checked` TINYINT(1) NOT NULL DEFAULT 0,
    `notes` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_packlist_items_packlist` (`packlist_id`),
    KEY `idx_packlist_items_item` (`item_type`, `item_id`),
    KEY `idx_packlist_items_sort_order` (`sort_order`),
    
    CONSTRAINT `fk_packlist_items_packlist` 
        FOREIGN KEY (`packlist_id`) 
        REFERENCES `packlists` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Logs Table
-- ============================================

CREATE TABLE IF NOT EXISTS `logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(100) NOT NULL,
    `entity_id` VARCHAR(50) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `metadata_json` JSON NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_logs_user` (`user_id`),
    KEY `idx_logs_action` (`action`),
    KEY `idx_logs_entity` (`entity_type`, `entity_id`),
    KEY `idx_logs_timestamp` (`timestamp`),
    KEY `idx_logs_user_action` (`user_id`, `action`),
    KEY `idx_logs_timestamp_action` (`timestamp`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Settings Table
-- ============================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT NULL DEFAULT NULL,
    `type` ENUM('string', 'number', 'boolean', 'json', 'date') NOT NULL DEFAULT 'string',
    `category` VARCHAR(100) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_settings_key` (`key`),
    KEY `idx_settings_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Label Templates Table
-- ============================================

CREATE TABLE IF NOT EXISTS `label_templates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `type` ENUM('device', 'case', 'both') NOT NULL DEFAULT 'device',
    `width` DECIMAL(6,2) NOT NULL DEFAULT 50.00,
    `height` DECIMAL(6,2) NOT NULL DEFAULT 30.00,
    `unit` ENUM('mm', 'cm', 'inch') NOT NULL DEFAULT 'mm',
    `orientation` ENUM('portrait', 'landscape') NOT NULL DEFAULT 'portrait',
    `include_qr` TINYINT(1) NOT NULL DEFAULT 1,
    `include_barcode` TINYINT(1) NOT NULL DEFAULT 1,
    `include_name` TINYINT(1) NOT NULL DEFAULT 1,
    `include_internal_id` TINYINT(1) NOT NULL DEFAULT 1,
    `include_serial_number` TINYINT(1) NOT NULL DEFAULT 0,
    `font_size` DECIMAL(4,2) NOT NULL DEFAULT 8.00,
    `qr_size` DECIMAL(4,2) NOT NULL DEFAULT 20.00,
    `barcode_type` VARCHAR(20) NOT NULL DEFAULT 'code128',
    `template_json` JSON NULL DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_label_templates_name` (`name`),
    KEY `idx_label_templates_type` (`type`),
    KEY `idx_label_templates_default` (`is_default`),
    KEY `idx_label_templates_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Views
-- ============================================

-- View for device counts by status
CREATE OR REPLACE VIEW `view_device_status_counts` AS
SELECT 
    `status`,
    COUNT(*) as `count`
FROM `devices`
WHERE `deleted_at` IS NULL
GROUP BY `status`;

-- View for active rentals
CREATE OR REPLACE VIEW `view_active_rentals` AS
SELECT 
    r.*,
    d.name as device_name,
    d.internal_id as device_internal_id,
    c.name as case_name,
    c.internal_id as case_internal_id,
    u.name as created_by_name,
    u.email as created_by_email,
    ru.name as returned_by_name
FROM `rentals` r
LEFT JOIN `devices` d ON r.device_id = d.id
LEFT JOIN `cases` c ON r.case_id = c.id
LEFT JOIN `users` u ON r.created_by = u.id
LEFT JOIN `users` ru ON r.returned_by = ru.id
WHERE r.status = 'active' AND r.deleted_at IS NULL;

-- View for overdue rentals
CREATE OR REPLACE VIEW `view_overdue_rentals` AS
SELECT 
    r.*,
    d.name as device_name,
    d.internal_id as device_internal_id,
    c.name as case_name,
    c.internal_id as case_internal_id,
    u.name as created_by_name,
    u.email as created_by_email
FROM `rentals` r
LEFT JOIN `devices` d ON r.device_id = d.id
LEFT JOIN `cases` c ON r.case_id = c.id
LEFT JOIN `users` u ON r.created_by = u.id
WHERE r.status = 'overdue' AND r.deleted_at IS NULL
ORDER BY r.expected_return ASC;

-- View for upcoming maintenance
CREATE OR REPLACE VIEW `view_upcoming_maintenance` AS
SELECT 
    m.*,
    d.name as device_name,
    d.internal_id as device_internal_id,
    d.status as device_status,
    u.name as created_by_name
FROM `maintenance` m
JOIN `devices` d ON m.device_id = d.id
LEFT JOIN `users` u ON m.created_by = u.id
WHERE m.next_inspection_date IS NOT NULL 
  AND m.next_inspection_date > CURDATE()
  AND m.deleted_at IS NULL
ORDER BY m.next_inspection_date ASC;

-- View for overdue maintenance
CREATE OR REPLACE VIEW `view_overdue_maintenance` AS
SELECT 
    m.*,
    d.name as device_name,
    d.internal_id as device_internal_id,
    d.status as device_status,
    u.name as created_by_name
FROM `maintenance` m
JOIN `devices` d ON m.device_id = d.id
LEFT JOIN `users` u ON m.created_by = u.id
WHERE m.next_inspection_date IS NOT NULL 
  AND m.next_inspection_date <= CURDATE()
  AND m.deleted_at IS NULL
ORDER BY m.next_inspection_date ASC;

-- ============================================
-- Triggers
-- ============================================

-- Trigger to update device status when assigned to case
DELIMITER //
CREATE TRIGGER `trg_case_device_after_insert`
    AFTER INSERT ON `case_device`
    FOR EACH ROW
BEGIN
    UPDATE `devices` 
    SET `status` = 'in_case', `updated_at` = CURRENT_TIMESTAMP
    WHERE `id` = NEW.device_id;
END//
DELIMITER ;

-- Trigger to update device status when removed from case
DELIMITER //
CREATE TRIGGER `trg_case_device_after_delete`
    AFTER DELETE ON `case_device`
    FOR EACH ROW
BEGIN
    -- Check if device is lent out or in maintenance
    IF NOT EXISTS (
        SELECT 1 FROM `rentals` 
        WHERE (`device_id` = OLD.device_id OR `case_id` = OLD.case_id) 
          AND `status` = 'active' 
          AND `deleted_at` IS NULL
    ) AND NOT EXISTS (
        SELECT 1 FROM `maintenance` 
        WHERE `device_id` = OLD.device_id 
          AND `status` IN ('due', 'overdue')
          AND `deleted_at` IS NULL
    ) THEN
        UPDATE `devices` 
        SET `status` = 'available', `updated_at` = CURRENT_TIMESTAMP
        WHERE `id` = OLD.device_id;
    END IF;
END//
DELIMITER ;

-- Trigger to update case status when device is lent out
DELIMITER //
CREATE TRIGGER `trg_rentals_after_insert`
    AFTER INSERT ON `rentals`
    FOR EACH ROW
BEGIN
    -- If a case is rented, update its status
    IF NEW.case_id IS NOT NULL THEN
        UPDATE `cases` 
        SET `status` = 'lent_out', `updated_at` = CURRENT_TIMESTAMP
        WHERE `id` = NEW.case_id;
    END IF;
    
    -- Update device status
    IF NEW.device_id IS NOT NULL THEN
        UPDATE `devices` 
        SET `status` = 'lent_out', `updated_at` = CURRENT_TIMESTAMP
        WHERE `id` = NEW.device_id;
    END IF;
END//
DELIMITER ;

-- Trigger to update status when rental is returned
DELIMITER //
CREATE TRIGGER `trg_rentals_after_update`
    AFTER UPDATE ON `rentals`
    FOR EACH ROW
BEGIN
    -- Only trigger when status changes to returned
    IF NEW.status = 'returned' AND OLD.status != 'returned' THEN
        -- Update case status
        IF NEW.case_id IS NOT NULL THEN
            UPDATE `cases` 
            SET `status` = 'available', `updated_at` = CURRENT_TIMESTAMP
            WHERE `id` = NEW.case_id;
        END IF;
        
        -- Update device status
        IF NEW.device_id IS NOT NULL THEN
            UPDATE `devices` 
            SET `status` = 'available', `updated_at` = CURRENT_TIMESTAMP
            WHERE `id` = NEW.device_id;
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger to update maintenance status when next inspection date changes
DELIMITER //
CREATE TRIGGER `trg_maintenance_before_update`
    BEFORE UPDATE ON `maintenance`
    FOR EACH ROW
BEGIN
    -- Calculate new status based on next inspection date
    IF NEW.next_inspection_date IS NOT NULL THEN
        IF NEW.next_inspection_date <= CURDATE() THEN
            SET NEW.status = 'overdue';
        ELSEIF NEW.next_inspection_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN
            SET NEW.status = 'due';
        ELSEIF NEW.next_inspection_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN
            SET NEW.status = 'upcoming';
        ELSE
            SET NEW.status = 'ok';
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger to calculate next inspection date when last inspection or interval changes
DELIMITER //
CREATE TRIGGER `trg_maintenance_before_insert`
    BEFORE INSERT ON `maintenance`
    FOR EACH ROW
BEGIN
    -- Calculate next inspection date if not provided
    IF NEW.next_inspection_date IS NULL AND NEW.last_inspection_date IS NOT NULL AND NEW.interval_months > 0 THEN
        SET NEW.next_inspection_date = DATE_ADD(NEW.last_inspection_date, INTERVAL NEW.interval_months MONTH);
    END IF;
    
    -- Calculate status
    IF NEW.next_inspection_date IS NOT NULL THEN
        IF NEW.next_inspection_date <= CURDATE() THEN
            SET NEW.status = 'overdue';
        ELSEIF NEW.next_inspection_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN
            SET NEW.status = 'due';
        ELSEIF NEW.next_inspection_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN
            SET NEW.status = 'upcoming';
        ELSE
            SET NEW.status = 'ok';
        END IF;
    END IF;
END//
DELIMITER ;

-- Trigger to calculate next inspection date when maintenance is updated
DELIMITER //
CREATE TRIGGER `trg_maintenance_before_update_calc`
    BEFORE UPDATE ON `maintenance`
    FOR EACH ROW
BEGIN
    -- Recalculate next inspection date if last inspection or interval changes
    IF NEW.last_inspection_date != OLD.last_inspection_date OR NEW.interval_months != OLD.interval_months THEN
        IF NEW.last_inspection_date IS NOT NULL AND NEW.interval_months > 0 THEN
            SET NEW.next_inspection_date = DATE_ADD(NEW.last_inspection_date, INTERVAL NEW.interval_months MONTH);
        END IF;
    END IF;
END//
DELIMITER ;

-- ============================================
-- Stored Procedures
-- ============================================

-- Procedure to get dashboard statistics
DELIMITER //
CREATE PROCEDURE `sp_get_dashboard_stats`(
    OUT total_devices INT,
    OUT available_devices INT,
    OUT devices_in_cases INT,
    OUT rented_devices INT,
    OUT devices_in_maintenance INT,
    OUT total_cases INT,
    OUT active_rentals INT,
    OUT overdue_rentals INT,
    OUT upcoming_maintenance INT,
    OUT overdue_maintenance INT
)
BEGIN
    SELECT COUNT(*) INTO total_devices FROM `devices` WHERE `deleted_at` IS NULL;
    SELECT COUNT(*) INTO available_devices FROM `devices` WHERE `status` = 'available' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO devices_in_cases FROM `devices` WHERE `status` = 'in_case' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO rented_devices FROM `devices` WHERE `status` = 'lent_out' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO devices_in_maintenance FROM `devices` WHERE `status` = 'maintenance' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO total_cases FROM `cases` WHERE `deleted_at` IS NULL;
    SELECT COUNT(*) INTO active_rentals FROM `rentals` WHERE `status` = 'active' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO overdue_rentals FROM `rentals` WHERE `status` = 'overdue' AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO upcoming_maintenance FROM `maintenance` WHERE `status` IN ('upcoming', 'due') AND `deleted_at` IS NULL;
    SELECT COUNT(*) INTO overdue_maintenance FROM `maintenance` WHERE `status` = 'overdue' AND `deleted_at` IS NULL;
END//
DELIMITER ;

-- Procedure to get device by internal ID or ID
DELIMITER //
CREATE PROCEDURE `sp_get_device_by_identifier`(
    IN identifier VARCHAR(50)
)
BEGIN
    SELECT * FROM `devices` 
    WHERE (`internal_id` = identifier OR `id` = CAST(identifier AS UNSIGNED)) 
      AND `deleted_at` IS NULL 
    LIMIT 1;
END//
DELIMITER ;

-- Procedure to get case by internal ID or ID
DELIMITER //
CREATE PROCEDURE `sp_get_case_by_identifier`(
    IN identifier VARCHAR(50)
)
BEGIN
    SELECT * FROM `cases` 
    WHERE (`internal_id` = identifier OR `id` = CAST(identifier AS UNSIGNED)) 
      AND `deleted_at` IS NULL 
    LIMIT 1;
END//
DELIMITER ;

-- ============================================
-- Indexes for Performance
-- ============================================

-- Composite index for device search
ALTER TABLE `devices` ADD INDEX `idx_devices_status_category` (`status`, `category_id`);

-- Composite index for rental search
ALTER TABLE `rentals` ADD INDEX `idx_rentals_status_date` (`status`, `expected_return`);

-- Composite index for maintenance search
ALTER TABLE `maintenance` ADD INDEX `idx_maintenance_status_date` (`status`, `next_inspection_date`);

-- ============================================
-- Final Notes
-- ============================================

-- This schema creates all required tables for the DDWB application:
-- - users: User management with roles
-- - categories: Device categories
-- - devices: Inventory devices
-- - cases: Physical containers for devices
-- - case_device: Many-to-many relationship between cases and devices
-- - rentals: Lending/rental tracking
-- - maintenance: DGUV3 maintenance records
-- - packlists: Packing lists
-- - packlist_items: Items in packing lists
-- - logs: Audit trail
-- - settings: Application settings
-- - label_templates: Label printing templates

-- The schema includes:
-- - Proper foreign key constraints
-- - Appropriate indexes for performance
-- - Soft delete support
-- - Full-text search where appropriate
-- - Views for common queries
-- - Triggers for automatic status updates
-- - Stored procedures for complex queries

-- Character set is utf8mb4 for full Unicode support
-- Collation is utf8mb4_unicode_ci for case-insensitive comparisons

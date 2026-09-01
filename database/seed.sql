-- DDWB Database Seed Data
-- This file contains sample data for development and testing

USE `ddwb`;

-- ============================================
-- Clear existing data (for development)
-- ============================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Truncate tables in reverse order of dependencies
TRUNCATE TABLE `case_device`;
TRUNCATE TABLE `packlist_items`;
TRUNCATE TABLE `rentals`;
TRUNCATE TABLE `maintenance`;
TRUNCATE TABLE `logs`;
TRUNCATE TABLE `devices`;
TRUNCATE TABLE `cases`;
TRUNCATE TABLE `packlists`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `settings`;
TRUNCATE TABLE `label_templates`;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- Settings
-- ============================================

INSERT INTO `settings` (`key`, `value`, `type`, `category`, `description`) VALUES
    ('app_name', 'DingeDieWirBesitzen', 'string', 'app', 'Application name'),
    ('app_version', '1.0.0', 'string', 'app', 'Application version'),
    ('maintenance_upcoming_days', '30', 'number', 'maintenance', 'Days before inspection is considered upcoming'),
    ('maintenance_due_days', '7', 'number', 'maintenance', 'Days before inspection is due'),
    ('maintenance_overdue_days', '0', 'number', 'maintenance', 'Days after which inspection is overdue'),
    ('pagination_per_page', '25', 'number', 'ui', 'Number of items per page'),
    ('qr_code_size', '200', 'number', 'labels', 'QR code size in pixels'),
    ('barcode_type', 'code128', 'string', 'labels', 'Default barcode type'),
    ('default_label_template', '1', 'number', 'labels', 'Default label template ID');

-- ============================================
-- Users
-- ============================================

-- Admin user
INSERT INTO `users` (`email`, `password_hash`, `name`, `role`, `active`, `created_at`, `updated_at`) VALUES
    ('admin@ddwb.local', '$2y$12$N9qo8uLOickgx2ZMRZoMy.MH9q9W9Xy9Xv8L3X9z9Xv8L3X9z9Xv8', 'Administrator', 'admin', 1, NOW(), NOW());

-- Regular user
INSERT INTO `users` (`email`, `password_hash`, `name`, `role`, `active`, `created_at`, `updated_at`) VALUES
    ('user@ddwb.local', '$2y$12$N9qo8uLOickgx2ZMRZoMy.MH9q9W9Xy9Xv8L3X9z9Xv8L3X9z9Xv8', 'Benutzer', 'user', 1, NOW(), NOW());

-- Inactive user (for testing)
INSERT INTO `users` (`email`, `password_hash`, `name`, `role`, `active`, `created_at`, `updated_at`) VALUES
    ('inactive@ddwb.local', '$2y$12$N9qo8uLOickgx2ZMRZoMy.MH9q9W9Xy9Xv8L3X9z9Xv8L3X9z9Xv8', 'Inaktiver Benutzer', 'user', 0, NOW(), NOW());

-- Note: The password hash above is for "password" - this is for development only!
-- In production, use proper password hashing with unique salts

-- ============================================
-- Categories
-- ============================================

INSERT INTO `categories` (`name`, `description`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
    ('Elektronik', 'Elektronische Geräte', 1, 1, NOW(), NOW()),
    ('Kamera', 'Kameras und Zubehör', 2, 1, NOW(), NOW()),
    ('Audio', 'Audio-Equipment', 3, 1, NOW(), NOW()),
    ('Beleuchtung', 'Beleuchtungstechnik', 4, 1, NOW(), NOW()),
    ('Computer', 'Computer und Peripherie', 5, 1, NOW(), NOW()),
    ('Netzwerk', 'Netzwerkgeräte', 6, 1, NOW(), NOW()),
    ('Sonstiges', 'Andere Geräte', 7, 1, NOW(), NOW());

-- ============================================
-- Devices
-- ============================================

-- Cameras
INSERT INTO `devices` (`internal_id`, `name`, `description`, `category_id`, `serial_number`, `status`, `location`, `purchase_date`, `purchase_price`, `warranty_expires`, `notes`, `created_at`, `updated_at`) VALUES
    ('DEV-0001', 'Sony Alpha A7 III', 'Vollformat-Kamera mit 24,2 MP', 2, 'SN-SONY-A7III-001', 'available', 'Lager Raum A', '2023-01-15', 1999.99, '2025-01-15', 'Mit 28-70mm Objektiv', NOW(), NOW()),
    ('DEV-0002', 'Canon EOS R5', 'Spiegellose Kamera mit 45 MP', 2, 'SN-CANON-R5-001', 'available', 'Lager Raum A', '2023-02-20', 2499.99, '2025-02-20', 'Mit RF 24-105mm Objektiv', NOW(), NOW()),
    ('DEV-0003', 'DJI Osmo Pocket 3', 'Handheld Gimbal Kamera', 2, 'SN-DJI-OP3-001', 'lent_out', 'Ausgeliehen', '2023-03-10', 549.00, '2025-03-10', 'Mit Zubehör', NOW(), NOW()),
    ('DEV-0004', 'GoPro Hero 11', 'Action-Kamera', 2, 'SN-GOPRO-H11-001', 'available', 'Lager Raum B', '2023-04-05', 399.99, '2025-04-05', '4K Video', NOW(), NOW());

-- Audio Equipment
INSERT INTO `devices` (`internal_id`, `name`, `description`, `category_id`, `serial_number`, `status`, `location`, `purchase_date`, `purchase_price`, `warranty_expires`, `notes`, `created_at`, `updated_at`) VALUES
    ('DEV-0005', 'Rode NTG-4+', 'Shotgun Mikrofon', 3, 'SN-RODE-NTG4-001', 'available', 'Lager Raum A', '2023-01-20', 299.00, '2025-01-20', 'Mit Windschutz', NOW(), NOW()),
    ('DEV-0006', 'Sennheiser EW 100 G4', 'Funkmikrofon-Set', 3, 'SN-SENN-EW100-001', 'in_case', 'Im Case CASE-0001', '2023-02-15', 599.00, '2025-02-15', 'Mit Empfänger und Sender', NOW(), NOW()),
    ('DEV-0007', 'Zoom H6', '6-Kanal Audio-Recorder', 3, 'SN-ZOOM-H6-001', 'available', 'Lager Raum B', '2023-03-25', 349.00, '2025-03-25', 'Mit Zubehör', NOW(), NOW());

-- Lighting
INSERT INTO `devices` (`internal_id`, `name`, `description`, `category_id`, `serial_number`, `status`, `location`, `purchase_date`, `purchase_price`, `warranty_expires`, `notes`, `created_at`, `updated_at`) VALUES
    ('DEV-0008', 'Aputure 300D', 'LED Panel Licht', 4, 'SN-APUTURE-300D-001', 'available', 'Lager Raum C', '2023-01-10', 899.00, '2025-01-10', 'Mit Softbox', NOW(), NOW()),
    ('DEV-0009', 'Godox SL-60W', 'LED Scheinwerfer', 4, 'SN-GODOX-SL60W-001', 'maintenance', 'Wartung', '2023-02-01', 199.00, '2025-02-01', '5600K', NOW(), NOW()),
    ('DEV-0010', 'Neewer 660', 'LED Video Licht', 4, 'SN-NEEWER-660-001', 'available', 'Lager Raum C', '2023-03-05', 129.00, '2025-03-05', 'Mit Diffusor', NOW(), NOW());

-- Computers
INSERT INTO `devices` (`internal_id`, `name`, `description`, `category_id`, `serial_number`, `status`, `location`, `purchase_date`, `purchase_price`, `warranty_expires`, `notes`, `created_at`, `updated_at`) VALUES
    ('DEV-0011', 'MacBook Pro 14"', 'Apple Laptop', 5, 'SN-APPLE-MBP14-001', 'available', 'Büro', '2023-01-05', 2499.00, '2026-01-05', 'M1 Pro, 16GB RAM', NOW(), NOW()),
    ('DEV-0012', 'iMac 24"', 'Apple Desktop', 5, 'SN-APPLE-IMAC24-001', 'available', 'Büro', '2023-02-10', 1699.00, '2026-02-10', 'M1, 8GB RAM', NOW(), NOW());

-- Network
INSERT INTO `devices` (`internal_id`, `name`, `description`, `category_id`, `serial_number`, `status`, `location`, `purchase_date`, `purchase_price`, `warranty_expires`, `notes`, `created_at`, `updated_at`) VALUES
    ('DEV-0013', 'Ubiquiti UniFi U6-Pro', 'WiFi 6 Access Point', 6, 'SN-UI-U6PRO-001', 'available', 'Serverraum', '2023-01-15', 229.00, '2026-01-15', '5GHz WiFi 6', NOW(), NOW()),
    ('DEV-0014', 'TP-Link TL-SG108E', '8-Port Gigabit Switch', 6, 'SN-TPLINK-SG108E-001', 'available', 'Serverraum', '2023-02-20', 129.00, '2026-02-20', 'Managed Switch', NOW(), NOW());

-- ============================================
-- Cases
-- ============================================

INSERT INTO `cases` (`internal_id`, `name`, `description`, `status`, `location`, `notes`, `created_at`, `updated_at`) VALUES
    ('CASE-0001', 'Kamera-Set 1', 'Hauptkamera-Set mit Zubehör', 'available', 'Lager Raum A', 'Enthält Sony A7 III und Zubehör', NOW(), NOW()),
    ('CASE-0002', 'Audio-Set 1', 'Komplettes Audio-Recording-Set', 'available', 'Lager Raum B', 'Enthält Zoom H6 und Mikrofone', NOW(), NOW()),
    ('CASE-0003', 'Beleuchtung-Set 1', 'LED-Licht-Set für Studio', 'available', 'Lager Raum C', 'Enthält Aputure 300D und Godox SL-60W', NOW(), NOW()),
    ('CASE-0004', 'Reise-Set', 'Kompaktes Reise-Set', 'lent_out', 'Ausgeliehen', 'Für mobile Aufnahmen', NOW(), NOW());

-- ============================================
-- Case Device Relationships
-- ============================================

-- Devices in CASE-0001
INSERT INTO `case_device` (`case_id`, `device_id`, `assigned_at`, `assigned_by`, `notes`) VALUES
    (1, 2, NOW(), 1, 'Hauptkamera'),
    (1, 6, NOW(), 1, 'Funkmikrofon'),
    (1, 8, NOW(), 1, 'Hauptlicht');

-- Devices in CASE-0002
INSERT INTO `case_device` (`case_id`, `device_id`, `assigned_at`, `assigned_by`, `notes`) VALUES
    (2, 5, NOW(), 1, 'Shotgun Mikrofon'),
    (2, 7, NOW(), 1, 'Audio-Recorder');

-- Devices in CASE-0003
INSERT INTO `case_device` (`case_id`, `device_id`, `assigned_at`, `assigned_by`, `notes`) VALUES
    (3, 8, NOW(), 1, 'Hauptlicht'),
    (3, 10, NOW(), 1, 'Zusatzlicht');

-- Devices in CASE-0004 (for lending)
INSERT INTO `case_device` (`case_id`, `device_id`, `assigned_at`, `assigned_by`, `notes`) VALUES
    (4, 4, NOW(), 1, 'Action-Kamera'),
    (4, 5, NOW(), 1, 'Mikrofon');

-- Update device statuses to reflect case assignments
UPDATE `devices` SET `status` = 'in_case' WHERE `id` IN (2, 5, 6, 7, 8, 10);

-- ============================================
-- Rentals
-- ============================================

-- Active rental for DEV-0003
INSERT INTO `rentals` (`device_id`, `case_id`, `borrower`, `borrower_email`, `borrower_phone`, `date_out`, `expected_return`, `actual_return`, `status`, `notes`, `created_by`, `returned_by`, `created_at`, `updated_at`) VALUES
    (3, NULL, 'Max Mustermann', 'max.mustermann@example.com', '+49 123 456789', '2024-01-15 10:00:00', '2024-02-15 10:00:00', NULL, 'active', 'Für Projekt X', 1, NULL, NOW(), NOW());

-- Active rental for CASE-0004
INSERT INTO `rentals` (`device_id`, `case_id`, `borrower`, `borrower_email`, `borrower_phone`, `date_out`, `expected_return`, `actual_return`, `status`, `notes`, `created_by`, `returned_by`, `created_at`, `updated_at`) VALUES
    (NULL, 4, 'Anna Schmidt', 'anna.schmidt@example.com', '+49 987 654321', '2024-01-20 14:00:00', '2024-03-20 14:00:00', NULL, 'active', 'Reiseaufnahmen', 1, NULL, NOW(), NOW());

-- Returned rental
INSERT INTO `rentals` (`device_id`, `case_id`, `borrower`, `borrower_email`, `borrower_phone`, `date_out`, `expected_return`, `actual_return`, `status`, `notes`, `created_by`, `returned_by`, `created_at`, `updated_at`) VALUES
    (1, NULL, 'Peter Müller', 'peter.mueller@example.com', '+49 456 789123', '2023-12-01 09:00:00', '2023-12-15 09:00:00', '2023-12-14 17:00:00', 'returned', 'Frühzeitig zurückgegeben', 1, 1, NOW(), NOW());

-- Overdue rental
INSERT INTO `rentals` (`device_id`, `case_id`, `borrower`, `borrower_email`, `borrower_phone`, `date_out`, `expected_return`, `actual_return`, `status`, `notes`, `created_by`, `returned_by`, `created_at`, `updated_at`) VALUES
    (11, NULL, 'Lisa Bauer', 'lisa.bauer@example.com', '+49 789 123456', '2023-12-01 10:00:00', '2023-12-20 10:00:00', NULL, 'overdue', 'Verlängert', 1, NULL, NOW(), NOW());

-- ============================================
-- Maintenance Records
-- ============================================

-- Maintenance for DEV-0001 (Sony A7 III)
INSERT INTO `maintenance` (`device_id`, `type`, `last_inspection_date`, `interval_months`, `next_inspection_date`, `status`, `inspector`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    (1, 'DGUV3', '2023-06-15', 12, '2024-06-15', 'ok', 'Techniker Müller', 'Alle Funktionen geprüft', 1, NOW(), NOW());

-- Maintenance for DEV-0002 (Canon R5)
INSERT INTO `maintenance` (`device_id`, `type`, `last_inspection_date`, `interval_months`, `next_inspection_date`, `status`, `inspector`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    (2, 'DGUV3', '2023-07-01', 12, '2024-07-01', 'upcoming', 'Techniker Schmidt', 'Sensor gereinigt', 1, NOW(), NOW());

-- Maintenance for DEV-0008 (Aputure 300D) - due soon
INSERT INTO `maintenance` (`device_id`, `type`, `last_inspection_date`, `interval_months`, `next_inspection_date`, `status`, `inspector`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    (8, 'DGUV3', '2023-11-01', 6, '2024-05-01', 'due', 'Techniker Bauer', 'LED-Panel überprüft', 1, NOW(), NOW());

-- Maintenance for DEV-0009 (Godox SL-60W) - overdue
INSERT INTO `maintenance` (`device_id`, `type`, `last_inspection_date`, `interval_months`, `next_inspection_date`, `status`, `inspector`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    (9, 'DGUV3', '2023-09-01', 12, '2024-09-01', 'overdue', 'Techniker Weber', 'Überprüfung fällig', 1, NOW(), NOW());

-- Maintenance for DEV-0013 (Ubiquiti U6-Pro)
INSERT INTO `maintenance` (`device_id`, `type`, `last_inspection_date`, `interval_months`, `next_inspection_date`, `status`, `inspector`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    (13, 'Elektrische Sicherheit', '2023-10-15', 24, '2025-10-15', 'ok', 'Techniker Lange', 'Netzwerkgerät geprüft', 1, NOW(), NOW());

-- ============================================
-- Packlists
-- ============================================

INSERT INTO `packlists` (`name`, `description`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
    ('Kamera-Produktion', 'Komplette Ausrüstung für Kamera-Produktion', 'active', 'Für Studioaufnahmen', 1, NOW(), NOW()),
    ('Mobile Reporting', 'Leichtes Set für mobile Berichterstattung', 'draft', 'Für Außenaufnahmen', 1, NOW(), NOW()),
    ('Interview-Set', 'Ausrüstung für Interviews', 'active', 'Mit Mikrofon und Licht', 1, NOW(), NOW());

-- ============================================
-- Packlist Items
-- ============================================

-- Items for Kamera-Produktion
INSERT INTO `packlist_items` (`packlist_id`, `item_type`, `item_id`, `quantity`, `sort_order`, `checked`, `notes`, `created_at`, `updated_at`) VALUES
    (1, 'device', 1, 1, 1, 0, 'Hauptkamera', NOW(), NOW()),
    (1, 'device', 2, 1, 2, 0, 'Backup-Kamera', NOW(), NOW()),
    (1, 'device', 5, 1, 3, 0, 'Shotgun Mikrofon', NOW(), NOW()),
    (1, 'device', 8, 2, 4, 0, 'Hauptlicht', NOW(), NOW()),
    (1, 'device', 10, 1, 5, 0, 'Zusatzlicht', NOW(), NOW());

-- Items for Mobile Reporting
INSERT INTO `packlist_items` (`packlist_id`, `item_type`, `item_id`, `quantity`, `sort_order`, `checked`, `notes`, `created_at`, `updated_at`) VALUES
    (2, 'device', 4, 1, 1, 0, 'Action-Kamera', NOW(), NOW()),
    (2, 'device', 5, 1, 2, 0, 'Shotgun Mikrofon', NOW(), NOW()),
    (2, 'case', 4, 1, 3, 0, 'Reise-Set', NOW(), NOW());

-- Items for Interview-Set
INSERT INTO `packlist_items` (`packlist_id`, `item_type`, `item_id`, `quantity`, `sort_order`, `checked`, `notes`, `created_at`, `updated_at`) VALUES
    (3, 'device', 2, 1, 1, 0, 'Kamera', NOW(), NOW()),
    (3, 'device', 6, 1, 2, 0, 'Funkmikrofon', NOW(), NOW()),
    (3, 'device', 7, 1, 3, 0, 'Audio-Recorder', NOW(), NOW()),
    (3, 'device', 8, 1, 4, 0, 'Licht', NOW(), NOW());

-- ============================================
-- Logs
-- ============================================

-- System logs
INSERT INTO `logs` (`user_id`, `action`, `entity_type`, `entity_id`, `description`, `metadata_json`, `ip_address`, `user_agent`, `timestamp`) VALUES
    (1, 'login', 'users', '1', 'Administrator angemeldet', NULL, '127.0.0.1', 'Mozilla/5.0', NOW()),
    (2, 'login', 'users', '2', 'Benutzer angemeldet', NULL, '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
    (1, 'create', 'devices', '1', 'Gerät DEV-0001 erstellt', JSON_OBJECT('name', 'Sony Alpha A7 III'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (1, 'create', 'devices', '2', 'Gerät DEV-0002 erstellt', JSON_OBJECT('name', 'Canon EOS R5'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (1, 'lend', 'rentals', '1', 'Gerät DEV-0003 ausgeliehen', JSON_OBJECT('borrower', 'Max Mustermann'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (1, 'lend', 'rentals', '2', 'Case CASE-0004 ausgeliehen', JSON_OBJECT('borrower', 'Anna Schmidt'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (1, 'return', 'rentals', '3', 'Gerät DEV-0001 zurückgegeben', NULL, '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (1, 'create', 'cases', '1', 'Case CASE-0001 erstellt', JSON_OBJECT('name', 'Kamera-Set 1'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (1, 'case_assign', 'case_device', '1', 'Gerät DEV-0002 zu Case CASE-0001 hinzugefügt', JSON_OBJECT('case_id', 1, 'device_id', 2), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (1, 'maintenance_create', 'maintenance', '1', 'Wartung für DEV-0001 erstellt', JSON_OBJECT('type', 'DGUV3'), '127.0.0.1', 'Mozilla/5.0', DATE_SUB(NOW(), INTERVAL 5 DAY));

-- ============================================
-- Label Templates
-- ============================================

INSERT INTO `label_templates` (`name`, `description`, `type`, `width`, `height`, `unit`, `orientation`, `include_qr`, `include_barcode`, `include_name`, `include_internal_id`, `include_serial_number`, `font_size`, `qr_size`, `barcode_type`, `is_default`, `created_at`, `updated_at`) VALUES
    ('Standard Device Label', 'Standard-Label für Geräte', 'device', 50.00, 30.00, 'mm', 'portrait', 1, 1, 1, 1, 0, 8.00, 20.00, 'code128', 1, NOW(), NOW()),
    ('Standard Case Label', 'Standard-Label für Cases', 'case', 50.00, 30.00, 'mm', 'portrait', 1, 1, 1, 1, 0, 8.00, 20.00, 'code128', 0, NOW(), NOW()),
    ('Small Device Label', 'Kleines Label für Geräte', 'device', 35.00, 20.00, 'mm', 'portrait', 1, 1, 1, 1, 0, 6.00, 15.00, 'code128', 0, NOW(), NOW()),
    ('Large Device Label', 'Großes Label für Geräte', 'device', 70.00, 40.00, 'mm', 'landscape', 1, 1, 1, 1, 1, 10.00, 25.00, 'code128', 0, NOW(), NOW());

-- ============================================
-- Final Notes
-- ============================================

-- This seed data creates:
-- - 3 users (admin, user, inactive)
-- - 7 categories
-- - 14 devices
-- - 4 cases
-- - Case-device relationships
-- - 4 rentals (active, returned, overdue)
-- - 5 maintenance records (ok, upcoming, due, overdue)
-- - 3 packlists with items
-- - 10 audit log entries
-- - 4 label templates
-- - Application settings

-- Default passwords for development:
-- All users have password: "password"
-- NOTE: Change these passwords immediately in production!

-- The seed data demonstrates:
-- - Device statuses (available, in_case, lent_out, maintenance)
-- - Rental statuses (active, returned, overdue)
-- - Maintenance statuses (ok, upcoming, due, overdue)
-- - Case-device relationships
-- - Packlist items
-- - Audit logging

-- To use this seed data:
-- 1. Import schema.sql first
-- 2. Then import seed.sql
-- 3. The application will be ready for testing

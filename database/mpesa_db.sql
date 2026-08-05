-- =====================================================================
-- M-PESA PAYMENT VERIFICATION AND TRANSACTION MANAGEMENT SYSTEM (MPVS)
-- Database schema & sample data — Technical University of Kenya project
-- ---------------------------------------------------------------------
-- Engine : MySQL 5.7+ / 8.x  (XAMPP default)
-- Charset: utf8mb4
--
-- This is a MANUAL payment verification system. Transactions are
-- recorded by administrators and verified against the local database.
-- No external (Safaricom Daraja) API is used.
--
-- NOTE: Passwords are bcrypt hashes. Plaintext seed passwords:
--       admin   -> Admin@123   (Administrator, pre-approved)
--       staff1  -> Staff@123   (Staff, pre-approved)
--       staff2  -> Staff@123   (Staff, pre-approved)
--       newstaff -> Staff@123  (Staff, PENDING APPROVAL — demo)
--
-- You can import this file directly (phpMyAdmin / mysql CLI) OR run the
-- web installer at /setup.php which executes this same schema for you.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `mpesa_db`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `mpesa_db`;

-- ---------------------------------------------------------------------
-- 1. USERS — administrators & business staff (SRS: User entity)
--    status: pending (awaiting admin approval) | approved | rejected |
--            inactive (suspended)
--    Only 'approved' users may log in.
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `receipts`;
DROP TABLE IF EXISTS `transactions`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `full_name`     VARCHAR(120)     NOT NULL,
  `username`      VARCHAR(60)      NOT NULL,
  `email`         VARCHAR(120)     NOT NULL,
  `phone`         VARCHAR(20)      DEFAULT NULL,
  `password_hash` VARCHAR(255)     NOT NULL,
  `role`          ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  `status`        ENUM('pending','approved','rejected','inactive') NOT NULL DEFAULT 'pending',
  `last_login_at` DATETIME         DEFAULT NULL,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. CUSTOMERS — people paying via M-Pesa (SRS: Customer entity)
-- ---------------------------------------------------------------------
CREATE TABLE `customers` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `full_name`  VARCHAR(120)  NOT NULL,
  `phone`      VARCHAR(20)   NOT NULL,
  `email`      VARCHAR(120)  DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TRANSACTIONS — the core records (SRS: Transaction entity)
--    Created as 'pending' by an administrator, then manually marked
--    'verified' or 'failed' on the Verify Transaction page.
-- ---------------------------------------------------------------------
CREATE TABLE `transactions` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `mpesa_code`  VARCHAR(20)   NOT NULL COMMENT 'M-Pesa-style code (10 chars, unique)',
  `customer_id` INT UNSIGNED  DEFAULT NULL,
  `phone`       VARCHAR(20)   NOT NULL,
  `amount`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status`      ENUM('verified','failed','pending') NOT NULL DEFAULT 'pending',
  `verified_by` INT UNSIGNED  DEFAULT NULL COMMENT 'Admin who verified/marked the payment',
  `verified_at` DATETIME      DEFAULT NULL COMMENT 'When the payment was processed',
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_transactions_mpesa_code` (`mpesa_code`),
  KEY `idx_transactions_customer_id` (`customer_id`),
  KEY `idx_transactions_verified_by` (`verified_by`),
  KEY `idx_transactions_status` (`status`),
  KEY `idx_transactions_verified_at` (`verified_at`),
  KEY `idx_transactions_phone` (`phone`),
  CONSTRAINT `fk_transactions_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_transactions_verified_by`
    FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. RECEIPTS — digital payment receipts (SRS: Receipt entity)
-- ---------------------------------------------------------------------
CREATE TABLE `receipts` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `receipt_no`     VARCHAR(30)  NOT NULL,
  `transaction_id` INT UNSIGNED NOT NULL,
  `generated_by`   INT UNSIGNED DEFAULT NULL,
  `generated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_receipts_receipt_no` (`receipt_no`),
  UNIQUE KEY `uq_receipts_transaction_id` (`transaction_id`),
  KEY `idx_receipts_generated_by` (`generated_by`),
  CONSTRAINT `fk_receipts_transaction`
    FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_receipts_generated_by`
    FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. REPORTS — generated report history (SRS: Report entity)
-- ---------------------------------------------------------------------
CREATE TABLE `reports` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `report_type`   VARCHAR(40)  NOT NULL DEFAULT 'transactions',
  `title`         VARCHAR(160) NOT NULL,
  `date_from`     DATE         DEFAULT NULL,
  `date_to`       DATE         DEFAULT NULL,
  `status_filter` VARCHAR(20)  DEFAULT NULL,
  `summary`       JSON         DEFAULT NULL COMMENT 'Snapshot of computed statistics',
  `generated_by`  INT UNSIGNED DEFAULT NULL,
  `generated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reports_generated_by` (`generated_by`),
  CONSTRAINT `fk_reports_generated_by`
    FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. AUDIT LOGS — activity/monitoring (SRS constraint: unauthorized
--    access attempts may occur and require monitoring)
-- ---------------------------------------------------------------------
CREATE TABLE `audit_logs` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `action`     VARCHAR(60)  NOT NULL,
  `details`    VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45)  DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_logs_user_id` (`user_id`),
  KEY `idx_audit_logs_created_at` (`created_at`),
  CONSTRAINT `fk_audit_logs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SAMPLE DATA
-- =====================================================================

-- Users (bcrypt hashes — see note at top of file)
INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`) VALUES
(1, 'Brian Kioko (Administrator)', 'admin', 'admin@mpvs.local', '0712000001', '$2y$10$VVcfRB3siw6//czIb0g9I.wJavcgPdYrErX5J9PWVyLh7BkMGyHI2', 'admin', 'approved'),
(2, 'Grace Wanjiru (Cashier)',       'staff1', 'staff1@mpvs.local', '0712000002', '$2y$10$GaWph9PmX1g68w4IHdbLFOu8w9D8mFN1Ai99SPbwgP64o9TTabg8O', 'staff', 'approved'),
(3, 'Peter Otieno (Attendant)',      'staff2', 'staff2@mpvs.local', '0712000003', '$2y$10$GaWph9PmX1g68w4IHdbLFOu8w9D8mFN1Ai99SPbwgP64o9TTabg8O', 'staff', 'approved'),
(4, 'Diana Moraa (New Cashier)',     'newstaff', 'newstaff@mpvs.local', '0712000004', '$2y$10$GaWph9PmX1g68w4IHdbLFOu8w9D8mFN1Ai99SPbwgP64o9TTabg8O', 'staff', 'pending'),
(5, 'Karen Njoki (Rejected App)',    'karennjoki', 'karen@mpvs.local', '0712000005', '$2y$10$GaWph9PmX1g68w4IHdbLFOu8w9D8mFN1Ai99SPbwgP64o9TTabg8O', 'staff', 'rejected');

-- Customers
INSERT INTO `customers` (`id`, `full_name`, `phone`, `email`) VALUES
(1, 'James Mwangi',    '0722123456', 'james.mwangi@example.com'),
(2, 'Amina Hassan',    '0733987654', 'amina.hassan@example.com'),
(3, 'Kevin Njoroge',   '0711456789', 'kevin.njoroge@example.com'),
(4, 'Lucy Chebet',     '0744567890', 'lucy.chebet@example.com'),
(5, 'Daniel Kiprotich','0700876543', NULL),
(6, 'Faith Achieng',   '0790123456', 'faith.achieng@example.com');

-- Transactions (verified / failed / pending mix, July – August 2026)
INSERT INTO `transactions` (`id`, `mpesa_code`, `customer_id`, `phone`, `amount`, `status`, `verified_by`, `verified_at`, `created_at`) VALUES
(1,  'SJX3K9Q2PL', 1, '0722123456',  1500.00, 'verified', 2, '2026-07-29 09:14:00', '2026-07-29 09:14:00'),
(2,  'MQT4B8W1XZ', 2, '0733987654',   850.50, 'verified', 2, '2026-07-29 10:02:00', '2026-07-29 10:02:00'),
(3,  'PLR7C2N5YH', 3, '0711456789',  3200.00, 'verified', 1, '2026-07-30 11:45:00', '2026-07-30 11:45:00'),
(4,  'KDF9A3M6QT', 4, '0744567890',  2000.00, 'failed',   2, '2026-07-30 12:30:00', '2026-07-30 12:30:00'),
(5,  'ZXC8V2B4NM', 5, '0700876543',   400.00, 'verified', 2, '2026-07-30 14:20:00', '2026-07-30 14:20:00'),
(6,  'HGJ5T7Y8UI', 6, '0790123456',  1250.00, 'verified', 3, '2026-07-31 09:50:00', '2026-07-31 09:50:00'),
(7,  'QWER4T5Y6U', 1, '0722123456',   750.00, 'verified', 3, '2026-07-31 13:15:00', '2026-07-31 13:15:00'),
(8,  'ASDF6G7H8J', 2, '0733987654',  5400.00, 'pending',  NULL, NULL, '2026-08-01 08:00:00'),
(9,  'NBVC3X4Z5A', 3, '0711456789',   980.00, 'verified', 2, '2026-08-01 10:40:00', '2026-08-01 10:40:00'),
(10, 'POIU1K2J3H', 4, '0744567890',  2750.00, 'verified', 1, '2026-08-02 11:05:00', '2026-08-02 11:05:00'),
(11, 'LKJH5G6F7D', 5, '0700876543',  1600.00, 'failed',   3, '2026-08-02 15:35:00', '2026-08-02 15:35:00'),
(12, 'MNBV8C9X0Z', 6, '0790123456',   300.00, 'verified', 3, '2026-08-03 09:25:00', '2026-08-03 09:25:00'),
(13, 'TYUI2W3E4R', 1, '0722123456',  6200.00, 'verified', 2, '2026-08-03 12:55:00', '2026-08-03 12:55:00'),
(14, 'RFV5T6G7Y8', 2, '0733987654',  1150.00, 'pending',  NULL, NULL, '2026-08-04 08:30:00'),
(15, 'UJM9I0O1P2', 3, '0711456789',   999.00, 'verified', 1, '2026-08-04 14:10:00', '2026-08-04 14:10:00'),
(16, 'QHJ7K8L9MN', 4, '0744567890',  2400.00, 'verified', 1, '2026-08-05 09:30:00', '2026-08-05 09:30:00'),
(17, 'ZXCV9BNM6Q', 5, '0700876543',   650.00, 'failed',   1, '2026-08-05 11:05:00', '2026-08-05 11:05:00');

-- Receipts for verified transactions
INSERT INTO `receipts` (`id`, `receipt_no`, `transaction_id`, `generated_by`, `generated_at`) VALUES
(1, 'RCP-2026-000001', 1,  2, '2026-07-29 09:15:00'),
(2, 'RCP-2026-000002', 2,  2, '2026-07-29 10:03:00'),
(3, 'RCP-2026-000003', 3,  1, '2026-07-30 11:46:00'),
(4, 'RCP-2026-000004', 5,  2, '2026-07-30 14:21:00'),
(5, 'RCP-2026-000005', 6,  3, '2026-07-31 09:51:00'),
(6, 'RCP-2026-000006', 7,  3, '2026-07-31 13:16:00'),
(7, 'RCP-2026-000007', 9,  2, '2026-08-01 10:41:00'),
(8, 'RCP-2026-000008', 10, 1, '2026-08-02 11:06:00'),
(9, 'RCP-2026-000009', 12, 3, '2026-08-03 09:26:00'),
(10, 'RCP-2026-000010', 13, 2, '2026-08-03 12:56:00'),
(11, 'RCP-2026-000011', 15, 1, '2026-08-04 14:11:00'),
(12, 'RCP-2026-000012', 16, 1, '2026-08-05 09:31:00');

-- Report history
INSERT INTO `reports` (`id`, `report_type`, `title`, `date_from`, `date_to`, `status_filter`, `summary`, `generated_by`, `generated_at`) VALUES
(1, 'transactions', 'Weekly transaction summary', '2026-07-27', '2026-08-02', 'all', '{"total":9,"verified":7,"failed":2,"pending":0,"revenue":12930.50,"avg":1436.72}', 1, '2026-08-03 09:00:00'),
(2, 'transactions', 'Verified payments report', '2026-08-03', '2026-08-04', 'verified', '{"total":5,"verified":5,"failed":0,"pending":0,"revenue":12468.00,"avg":2493.60}', 1, '2026-08-04 16:30:00');

-- Audit trail
INSERT INTO `audit_logs` (`user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 'login', 'Administrator logged in', '127.0.0.1', '2026-08-04 08:00:00'),
(2, 'login', 'User logged in', '127.0.0.1', '2026-08-04 08:10:00'),
(2, 'verify_transaction', 'Verified code UJM9I0O1P2 — KES 999.00', '127.0.0.1', '2026-08-04 14:10:00'),
(1, 'generate_report', 'Generated report: Verified payments report', '127.0.0.1', '2026-08-04 16:30:00'),
(4, 'register', 'New staff account created: newstaff (pending approval)', '127.0.0.1', '2026-08-05 08:45:00');

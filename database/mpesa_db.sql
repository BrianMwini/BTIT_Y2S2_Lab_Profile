-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2026 at 02:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mpesa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'Administrator logged in', '127.0.0.1', NULL, '2026-08-04 08:00:00'),
(2, 2, 'login', 'User logged in', '127.0.0.1', NULL, '2026-08-04 08:10:00'),
(3, 2, 'verify_transaction', 'Verified code UJM9I0O1P2 — KES 999.00', '127.0.0.1', NULL, '2026-08-04 14:10:00'),
(4, 1, 'generate_report', 'Generated report: Verified payments report', '127.0.0.1', NULL, '2026-08-04 16:30:00'),
(5, 4, 'register', 'New staff account created: newstaff (pending approval)', '127.0.0.1', NULL, '2026-08-05 08:45:00'),
(6, 1, 'login', 'admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:12'),
(7, 1, 'user_rejected', 'newstaff was rejected (rejected)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:24'),
(8, 1, 'user_suspended', 'staff1 was suspended (inactive)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:32'),
(9, 1, 'user_activated', 'staff1 was activated (approved)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:45'),
(10, 1, 'user_activated', 'karennjoki was activated (approved)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:49'),
(11, 1, 'user_suspended', 'karennjoki was suspended (inactive)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:54:52'),
(12, 1, 'user_updated', 'Updated account karennjoki', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:55:10'),
(13, 1, 'user_suspended', 'karennjoki was suspended (inactive)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:55:15'),
(14, 1, 'logout', 'User signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:55:32'),
(15, 6, 'register', 'New staff account created: Annie (pending approval)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:56:46'),
(16, 6, 'login_failed', 'Blocked login (pending account): Annie', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:56:54'),
(17, 1, 'login', 'admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:57:09'),
(18, 1, 'user_approved', 'Annie was approved (approved)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:57:51'),
(19, 1, 'logout', 'User signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:57:56'),
(20, 6, 'login', 'Annie logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 11:57:58'),
(21, 6, 'logout', 'User signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:38:53'),
(22, 1, 'login', 'admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:38:56'),
(23, 1, 'logout', 'User signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:39:23'),
(24, 1, 'login', 'admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:39:47'),
(25, 1, 'user_activated', 'karennjoki was activated (approved)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:40:20'),
(26, 1, 'user_suspended', 'karennjoki was suspended (inactive)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:40:25'),
(27, 1, 'user_updated', 'Updated account staff2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:40:55'),
(28, 1, 'user_updated', 'Updated account staff1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:41:20'),
(29, 1, 'user_updated', 'Updated account staff1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:41:35'),
(30, 1, 'user_updated', 'Updated account admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:42:01'),
(31, 1, 'user_updated', 'Updated account karennjoki', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:42:20'),
(32, 1, 'user_updated', 'Updated account Diana', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:42:58'),
(33, 1, 'user_updated', 'Updated account karennjoki', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:43:10'),
(34, 1, 'user_updated', 'Updated account Diana', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:43:37'),
(35, 1, 'user_updated', 'Updated account staff2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:43:54'),
(36, 1, 'user_updated', 'Updated account Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:44:05'),
(37, 1, 'user_updated', 'Updated account Grace', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:44:18'),
(38, 1, 'user_updated', 'Updated account admin', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:44:39'),
(39, 1, 'user_updated', 'Updated account Annie', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:44:57'),
(40, 1, 'user_updated', 'Updated account karennjoki', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:45:21'),
(41, 1, 'user_updated', 'Updated account Grace', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:45:37'),
(42, 1, 'user_updated', 'Updated account Peter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 12:45:50'),
(43, 1, 'logout', 'User signed out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-08-30 13:04:54');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `full_name`, `phone`, `email`, `created_at`) VALUES
(1, 'James Mwangi', '0722123456', 'jamesmwangi@gmail.com', '2026-08-30 11:50:13'),
(2, 'Amina Hassan', '0733987654', 'aminahassan@gmail.com', '2026-08-30 11:50:13'),
(3, 'Kevin Njoroge', '0711456789', 'kevinnjoroge@gmail.com', '2026-08-30 11:50:13'),
(4, 'Lucy Chebet', '0744567890', 'lucychebet@gmail.com', '2026-08-30 11:50:13'),
(5, 'Daniel Kiprotich', '0700876543', NULL, '2026-08-30 11:50:13'),
(6, 'Faith Achieng', '0790123456', 'faithachieng@gmail.com', '2026-08-30 11:50:13');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(10) UNSIGNED NOT NULL,
  `receipt_no` varchar(30) NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `generated_by` int(10) UNSIGNED DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `receipt_no`, `transaction_id`, `generated_by`, `generated_at`) VALUES
(1, 'RCP-2026-000001', 1, 2, '2026-07-29 09:15:00'),
(2, 'RCP-2026-000002', 2, 2, '2026-07-29 10:03:00'),
(3, 'RCP-2026-000003', 3, 1, '2026-07-30 11:46:00'),
(4, 'RCP-2026-000004', 5, 2, '2026-07-30 14:21:00'),
(5, 'RCP-2026-000005', 6, 3, '2026-07-31 09:51:00'),
(6, 'RCP-2026-000006', 7, 3, '2026-07-31 13:16:00'),
(7, 'RCP-2026-000007', 9, 2, '2026-08-01 10:41:00'),
(8, 'RCP-2026-000008', 10, 1, '2026-08-02 11:06:00'),
(9, 'RCP-2026-000009', 12, 3, '2026-08-03 09:26:00'),
(10, 'RCP-2026-000010', 13, 2, '2026-08-03 12:56:00'),
(11, 'RCP-2026-000011', 15, 1, '2026-08-04 14:11:00'),
(12, 'RCP-2026-000012', 16, 1, '2026-08-05 09:31:00');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_type` varchar(40) NOT NULL DEFAULT 'transactions',
  `title` varchar(160) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `status_filter` varchar(20) DEFAULT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot of computed statistics' CHECK (json_valid(`summary`)),
  `generated_by` int(10) UNSIGNED DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_type`, `title`, `date_from`, `date_to`, `status_filter`, `summary`, `generated_by`, `generated_at`) VALUES
(1, 'transactions', 'Weekly transaction summary', '2026-07-27', '2026-08-02', 'all', '{\"total\":9,\"verified\":7,\"failed\":2,\"pending\":0,\"revenue\":12930.50,\"avg\":1436.72}', 1, '2026-08-03 09:00:00'),
(2, 'transactions', 'Verified payments report', '2026-08-03', '2026-08-04', 'verified', '{\"total\":5,\"verified\":5,\"failed\":0,\"pending\":0,\"revenue\":12468.00,\"avg\":2493.60}', 1, '2026-08-04 16:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `mpesa_code` varchar(20) NOT NULL COMMENT 'M-Pesa-style code (10 chars, unique)',
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('verified','failed','pending') NOT NULL DEFAULT 'pending',
  `verified_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'Admin who verified/marked the payment',
  `verified_at` datetime DEFAULT NULL COMMENT 'When the payment was processed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `mpesa_code`, `customer_id`, `phone`, `amount`, `status`, `verified_by`, `verified_at`, `created_at`) VALUES
(1, 'SJX3K9Q2PL', 1, '0722123456', 1500.00, 'verified', 2, '2026-07-29 09:14:00', '2026-07-29 09:14:00'),
(2, 'MQT4B8W1XZ', 2, '0733987654', 850.50, 'verified', 2, '2026-07-29 10:02:00', '2026-07-29 10:02:00'),
(3, 'PLR7C2N5YH', 3, '0711456789', 3200.00, 'verified', 1, '2026-07-30 11:45:00', '2026-07-30 11:45:00'),
(4, 'KDF9A3M6QT', 4, '0744567890', 2000.00, 'failed', 2, '2026-07-30 12:30:00', '2026-07-30 12:30:00'),
(5, 'ZXC8V2B4NM', 5, '0700876543', 400.00, 'verified', 2, '2026-07-30 14:20:00', '2026-07-30 14:20:00'),
(6, 'HGJ5T7Y8UI', 6, '0790123456', 1250.00, 'verified', 3, '2026-07-31 09:50:00', '2026-07-31 09:50:00'),
(7, 'QWER4T5Y6U', 1, '0722123456', 750.00, 'verified', 3, '2026-07-31 13:15:00', '2026-07-31 13:15:00'),
(8, 'ASDF6G7H8J', 2, '0733987654', 5400.00, 'pending', NULL, NULL, '2026-08-01 08:00:00'),
(9, 'NBVC3X4Z5A', 3, '0711456789', 980.00, 'verified', 2, '2026-08-01 10:40:00', '2026-08-01 10:40:00'),
(10, 'POIU1K2J3H', 4, '0744567890', 2750.00, 'verified', 1, '2026-08-02 11:05:00', '2026-08-02 11:05:00'),
(11, 'LKJH5G6F7D', 5, '0700876543', 1600.00, 'failed', 3, '2026-08-02 15:35:00', '2026-08-02 15:35:00'),
(12, 'MNBV8C9X0Z', 6, '0790123456', 300.00, 'verified', 3, '2026-08-03 09:25:00', '2026-08-03 09:25:00'),
(13, 'TYUI2W3E4R', 1, '0722123456', 6200.00, 'verified', 2, '2026-08-03 12:55:00', '2026-08-03 12:55:00'),
(14, 'RFV5T6G7Y8', 2, '0733987654', 1150.00, 'pending', NULL, NULL, '2026-08-04 08:30:00'),
(15, 'UJM9I0O1P2', 3, '0711456789', 999.00, 'verified', 1, '2026-08-04 14:10:00', '2026-08-04 14:10:00'),
(16, 'QHJ7K8L9MN', 4, '0744567890', 2400.00, 'verified', 1, '2026-08-05 09:30:00', '2026-08-05 09:30:00'),
(17, 'ZXCV9BNM6Q', 5, '0700876543', 650.00, 'failed', 1, '2026-08-05 11:05:00', '2026-08-05 11:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `username` varchar(60) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `status` enum('pending','approved','rejected','inactive') NOT NULL DEFAULT 'pending',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `phone`, `password_hash`, `role`, `status`, `last_login_at`, `created_at`) VALUES
(1, 'Brian Kioko (Administrator)', 'admin', 'briankayox@gmail.com', '0720864927', '$2y$10$VVcfRB3siw6//czIb0g9I.wJavcgPdYrErX5J9PWVyLh7BkMGyHI2', 'admin', 'approved', '2026-08-30 12:39:46', '2026-08-30 11:50:13'),
(2, 'Grace Wanjiru (Cashier)', 'Grace', 'gracewanjiru@gmail.com', '0756773882', '$2y$10$MisACcD7e3kRcwzKrhfQGe6dtJXiW93kJ4MlhA45Jw6IcV3yOiWfy', 'staff', 'approved', NULL, '2026-08-30 11:50:13'),
(3, 'Peter Otieno', 'Peter', 'peter@gmail.com', '0712272883', '$2y$10$0DdQy6I5N.8E8XCOUDKQWeBx00eG9nJSZrMRKTK0lQy3iKidrlOS2', 'staff', 'approved', NULL, '2026-08-30 11:50:13'),
(4, 'Diana Moraa (New Cashier)', 'Diana', 'Diana@gmail.com', '0757748332', '$2y$10$mTpBABQ.6qdHzhmMc5HmNe5AH/3hzMI4BijitHcWTnsELNjnMZf26', 'staff', 'approved', NULL, '2026-08-30 11:50:13'),
(5, 'Karen Njoki', 'karennjoki', 'karen@gmail.com', '0747272899', '$2y$10$ULavc9.nYz3CUn5M5.aztOVB1r/F7yIdfCC2.Ug4VAQosRzrq2DjK', 'staff', 'inactive', NULL, '2026-08-30 11:50:13'),
(6, 'Annie Njuguna', 'Annie', 'annie@gmail.com', '0712647356', '$2y$10$1U5sJ9RbUWqzmxxIS1w1Eek40Cdge3fruxP17FVC.vgr1dUcQ2YkS', 'staff', 'approved', '2026-08-30 11:57:58', '2026-08-30 11:56:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_logs_user_id` (`user_id`),
  ADD KEY `idx_audit_logs_created_at` (`created_at`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_customers_phone` (`phone`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_receipts_receipt_no` (`receipt_no`),
  ADD UNIQUE KEY `uq_receipts_transaction_id` (`transaction_id`),
  ADD KEY `idx_receipts_generated_by` (`generated_by`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reports_generated_by` (`generated_by`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_transactions_mpesa_code` (`mpesa_code`),
  ADD KEY `idx_transactions_customer_id` (`customer_id`),
  ADD KEY `idx_transactions_verified_by` (`verified_by`),
  ADD KEY `idx_transactions_status` (`status`),
  ADD KEY `idx_transactions_verified_at` (`verified_at`),
  ADD KEY `idx_transactions_phone` (`phone`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `fk_receipts_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_receipts_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_generated_by` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transactions_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

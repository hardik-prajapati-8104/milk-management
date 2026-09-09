-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 09, 2026 at 11:55 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `milk_management_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `log_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activity',
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_logs_log_type_index` (`log_type`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `activity_logs`
--

TRUNCATE TABLE `activity_logs`;
--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `log_type`, `action`, `subject_type`, `subject_id`, `old_values`, `new_values`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'activity', 'login', 'App\\Models\\User', 1, '[]', '[]', 'User logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 06:11:39', '2026-09-09 06:11:39'),
(2, 1, 'activity', 'post', NULL, NULL, '[]', '[]', 'login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 06:11:39', '2026-09-09 06:11:39'),
(3, 1, 'activity', 'created', 'App\\Models\\User', 2, '[]', '{\"role\": \"Admin\", \"email\": \"hardikprajapati8104@gmail.com\"}', 'User Hardik Prajapati Vinubhai created with role Admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 06:22:11', '2026-09-09 06:22:11'),
(4, 1, 'activity', 'post', NULL, NULL, '[]', '[]', 'admin/users', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', '2026-09-09 06:22:11', '2026-09-09 06:22:11');

-- --------------------------------------------------------

--
-- Table structure for table `app_notifications`
--

DROP TABLE IF EXISTS `app_notifications`;
CREATE TABLE IF NOT EXISTS `app_notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_foreign` (`user_id`),
  KEY `app_notifications_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `app_notifications`
--

TRUNCATE TABLE `app_notifications`;
-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
CREATE TABLE IF NOT EXISTS `areas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `areas_city_state_index` (`city`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `areas`
--

TRUNCATE TABLE `areas`;
-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
CREATE TABLE IF NOT EXISTS `bills` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `bill_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `bill_month` tinyint UNSIGNED NOT NULL,
  `bill_year` smallint UNSIGNED NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `morning_qty` decimal(10,3) NOT NULL DEFAULT '0.000',
  `evening_qty` decimal(10,3) NOT NULL DEFAULT '0.000',
  `total_qty` decimal(10,3) NOT NULL DEFAULT '0.000',
  `milk_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `extra_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `delivery_charges` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `penalty` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gst_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `gst_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `previous_due` decimal(12,2) NOT NULL DEFAULT '0.00',
  `advance_adjusted` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `outstanding_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','generated','partially_paid','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `due_date` date DEFAULT NULL,
  `pdf_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_customer_bill_period` (`customer_id`,`bill_month`,`bill_year`),
  UNIQUE KEY `bills_bill_number_unique` (`bill_number`),
  UNIQUE KEY `bills_invoice_number_unique` (`invoice_number`),
  KEY `bills_generated_by_foreign` (`generated_by`),
  KEY `bills_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `bills`
--

TRUNCATE TABLE `bills`;
-- --------------------------------------------------------

--
-- Table structure for table `bill_items`
--

DROP TABLE IF EXISTS `bill_items`;
CREATE TABLE IF NOT EXISTS `bill_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `bill_id` bigint UNSIGNED NOT NULL,
  `daily_entry_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `item_date` date DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_items_bill_id_foreign` (`bill_id`),
  KEY `bill_items_daily_entry_id_foreign` (`daily_entry_id`),
  KEY `bill_items_product_id_foreign` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `bill_items`
--

TRUNCATE TABLE `bill_items`;
-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `cache`
--

TRUNCATE TABLE `cache`;
--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-app_settings', 'a:16:{s:12:\"company_name\";s:13:\"My Milk Dairy\";s:12:\"company_logo\";N;s:10:\"gst_number\";N;s:15:\"company_address\";N;s:13:\"company_phone\";N;s:13:\"company_email\";N;s:15:\"company_website\";N;s:14:\"invoice_prefix\";s:3:\"INV\";s:15:\"consumer_prefix\";s:4:\"MILK\";s:14:\"receipt_prefix\";s:4:\"RCPT\";s:15:\"currency_symbol\";s:3:\"₹\";s:8:\"timezone\";s:12:\"Asia/Kolkata\";s:8:\"language\";s:2:\"en\";s:12:\"sms_provider\";s:4:\"none\";s:16:\"whatsapp_enabled\";s:1:\"0\";s:20:\"backup_reminder_days\";s:1:\"7\";}', 2104313862),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:76:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"customers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:16:\"customers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"customers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:16:\"customers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:16:\"customers.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:15:\"milk-rates.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:17:\"milk-rates.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"milk-rates.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:17:\"milk-rates.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:18:\"daily-entries.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:20:\"daily-entries.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:18:\"daily-entries.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:20:\"daily-entries.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:27:\"daily-entries.lock-override\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:10:\"bills.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"bills.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:10:\"bills.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:12:\"bills.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:14:\"bills.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:12:\"bills.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:11:\"bills.print\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:13:\"payments.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:15:\"payments.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:13:\"payments.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:15:\"payments.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:15:\"payments.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:14:\"payments.print\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:13:\"expenses.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:15:\"expenses.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:13:\"expenses.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:15:\"expenses.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:15:\"expenses.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:14:\"employees.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:16:\"employees.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:14:\"employees.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:16:\"employees.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:11:\"routes.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:13:\"routes.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:11:\"routes.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:13:\"routes.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:13:\"products.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:15:\"products.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:13:\"products.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:15:\"products.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:14:\"inventory.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:16:\"inventory.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:14:\"inventory.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:16:\"inventory.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:14:\"suppliers.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:16:\"suppliers.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:14:\"suppliers.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:16:\"suppliers.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:19:\"milk-purchases.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:21:\"milk-purchases.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:19:\"milk-purchases.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:21:\"milk-purchases.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:15:\"milk-stock.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:17:\"milk-stock.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:15:\"cash-sales.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:17:\"cash-sales.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:15:\"cash-sales.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:17:\"cash-sales.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:12:\"reports.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:14:\"reports.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:13:\"settings.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:13:\"settings.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:10:\"users.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:12:\"users.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:10:\"users.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:12:\"users.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:10:\"roles.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:12:\"roles.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:10:\"roles.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:12:\"roles.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:18:\"activity-logs.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"Manager\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"Accountant\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:8:\"Operator\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"Delivery Boy\";s:1:\"c\";s:3:\"web\";}}}', 1789040500);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `cache_locks`
--

TRUNCATE TABLE `cache_locks`;
-- --------------------------------------------------------

--
-- Table structure for table `cash_sales`
--

DROP TABLE IF EXISTS `cash_sales`;
CREATE TABLE IF NOT EXISTS `cash_sales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cash_sale_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_date` date NOT NULL,
  `shift` enum('morning','evening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `milk_type` enum('cow','buffalo','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cow',
  `quantity` decimal(10,3) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_sales_cash_sale_no_unique` (`cash_sale_no`),
  KEY `cash_sales_payment_method_id_foreign` (`payment_method_id`),
  KEY `cash_sales_created_by_foreign` (`created_by`),
  KEY `cash_sales_sale_date_shift_index` (`sale_date`,`shift`),
  KEY `cash_sales_sale_date_index` (`sale_date`),
  KEY `cash_sales_shift_index` (`shift`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `cash_sales`
--

TRUNCATE TABLE `cash_sales`;
-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `consumer_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternative_mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `village_id` bigint UNSIGNED DEFAULT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_id` bigint UNSIGNED DEFAULT NULL,
  `milk_type` enum('cow','buffalo','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cow',
  `morning_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `evening_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `default_qty_morning` decimal(8,3) NOT NULL DEFAULT '0.000',
  `default_qty_evening` decimal(8,3) NOT NULL DEFAULT '0.000',
  `customer_category_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `joining_date` date DEFAULT NULL,
  `identity_proof_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_proof_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `advance_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `outstanding_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_consumer_id_unique` (`consumer_id`),
  UNIQUE KEY `customers_barcode_unique` (`barcode`),
  KEY `customers_village_id_foreign` (`village_id`),
  KEY `customers_area_id_foreign` (`area_id`),
  KEY `customers_route_id_foreign` (`route_id`),
  KEY `customers_customer_category_id_foreign` (`customer_category_id`),
  KEY `customers_created_by_foreign` (`created_by`),
  KEY `customers_status_index` (`status`),
  KEY `customers_milk_type_index` (`milk_type`),
  KEY `customers_mobile_index` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customers`
--

TRUNCATE TABLE `customers`;
-- --------------------------------------------------------

--
-- Table structure for table `customer_categories`
--

DROP TABLE IF EXISTS `customer_categories`;
CREATE TABLE IF NOT EXISTS `customer_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customer_categories`
--

TRUNCATE TABLE `customer_categories`;
--
-- Dumping data for table `customer_categories`
--

INSERT INTO `customer_categories` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Residential', 'residential', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(2, 'Commercial', 'commercial', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(3, 'Hotel', 'hotel', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(4, 'Restaurant', 'restaurant', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(5, 'Shop', 'shop', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(6, 'Office', 'office', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `customer_documents`
--

DROP TABLE IF EXISTS `customer_documents`;
CREATE TABLE IF NOT EXISTS `customer_documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_documents_customer_id_foreign` (`customer_id`),
  KEY `customer_documents_uploaded_by_foreign` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `customer_documents`
--

TRUNCATE TABLE `customer_documents`;
-- --------------------------------------------------------

--
-- Table structure for table `daily_entries`
--

DROP TABLE IF EXISTS `daily_entries`;
CREATE TABLE IF NOT EXISTS `daily_entries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` bigint UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `shift` enum('morning','evening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `milk_type` enum('cow','buffalo','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cow',
  `quantity` decimal(8,3) NOT NULL DEFAULT '0.000',
  `rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_absent` tinyint(1) NOT NULL DEFAULT '0',
  `is_holiday` tinyint(1) NOT NULL DEFAULT '0',
  `is_manual_override` tinyint(1) NOT NULL DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `entered_by` bigint UNSIGNED DEFAULT NULL,
  `updated_by` bigint UNSIGNED DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `bill_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_customer_date_shift` (`customer_id`,`entry_date`,`shift`),
  KEY `daily_entries_entered_by_foreign` (`entered_by`),
  KEY `daily_entries_updated_by_foreign` (`updated_by`),
  KEY `daily_entries_entry_date_shift_index` (`entry_date`,`shift`),
  KEY `daily_entries_entry_date_index` (`entry_date`),
  KEY `daily_entries_shift_index` (`shift`),
  KEY `daily_entries_bill_id_index` (`bill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `daily_entries`
--

TRUNCATE TABLE `daily_entries`;
-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `joining_date` date DEFAULT NULL,
  `status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  KEY `employees_user_id_foreign` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `employees`
--

TRUNCATE TABLE `employees`;
-- --------------------------------------------------------

--
-- Table structure for table `employee_attendances`
--

DROP TABLE IF EXISTS `employee_attendances`;
CREATE TABLE IF NOT EXISTS `employee_attendances` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint UNSIGNED NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','half_day','leave','holiday') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_attendances_employee_id_attendance_date_unique` (`employee_id`,`attendance_date`),
  KEY `employee_attendances_attendance_date_index` (`attendance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `employee_attendances`
--

TRUNCATE TABLE `employee_attendances`;
-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

DROP TABLE IF EXISTS `employee_documents`;
CREATE TABLE IF NOT EXISTS `employee_documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_employee_id_foreign` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `employee_documents`
--

TRUNCATE TABLE `employee_documents`;
-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_category_id` bigint UNSIGNED NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_to` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED DEFAULT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_payment_method_id_foreign` (`payment_method_id`),
  KEY `expenses_created_by_foreign` (`created_by`),
  KEY `expenses_expense_date_index` (`expense_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `expenses`
--

TRUNCATE TABLE `expenses`;
-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expense_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `expense_categories`
--

TRUNCATE TABLE `expense_categories`;
--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Milk Purchase', 'milk-purchase', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(2, 'Salary', 'salary', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(3, 'Fuel', 'fuel', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(4, 'Electricity', 'electricity', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(5, 'Maintenance', 'maintenance', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(6, 'Vehicle', 'vehicle', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(7, 'Packaging', 'packaging', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(8, 'Miscellaneous', 'miscellaneous', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `failed_jobs`
--

TRUNCATE TABLE `failed_jobs`;
-- --------------------------------------------------------

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `movement_date` date NOT NULL,
  `type` enum('collection','purchase','sale','wastage','transfer_in','transfer_out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_before` decimal(12,3) NOT NULL DEFAULT '0.000',
  `stock_after` decimal(12,3) NOT NULL DEFAULT '0.000',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_movements_product_id_foreign` (`product_id`),
  KEY `inventory_movements_supplier_id_foreign` (`supplier_id`),
  KEY `inventory_movements_created_by_foreign` (`created_by`),
  KEY `inventory_movements_movement_date_index` (`movement_date`),
  KEY `inventory_movements_type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `inventory_movements`
--

TRUNCATE TABLE `inventory_movements`;
-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `jobs`
--

TRUNCATE TABLE `jobs`;
-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `job_batches`
--

TRUNCATE TABLE `job_batches`;
-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `migrations`
--

TRUNCATE TABLE `migrations`;
--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_01_000001_create_permission_tables', 1),
(5, '2025_01_01_000020_create_areas_table', 1),
(6, '2025_01_01_000021_create_villages_table', 1),
(7, '2025_01_01_000022_create_routes_table', 1),
(8, '2025_01_01_000023_create_customer_categories_table', 1),
(9, '2025_01_01_000025_add_profile_fields_to_users_table', 1),
(10, '2025_01_01_000030_create_customers_table', 1),
(11, '2025_01_01_000031_create_customer_documents_table', 1),
(12, '2025_01_01_000040_create_milk_rates_table', 1),
(13, '2025_01_01_000050_create_products_table', 1),
(14, '2025_01_01_000051_create_suppliers_table', 1),
(15, '2025_01_01_000052_create_inventory_table', 1),
(16, '2025_01_01_000060_create_daily_entries_table', 1),
(17, '2025_01_01_000070_create_bills_table', 1),
(18, '2025_01_01_000070_link_daily_entries_to_bills', 1),
(19, '2025_01_01_000071_create_bill_items_table', 1),
(20, '2025_01_01_000080_create_payment_methods_table', 1),
(21, '2025_01_01_000081_create_payments_table', 1),
(22, '2025_01_01_000090_create_expense_categories_table', 1),
(23, '2025_01_01_000091_create_expenses_table', 1),
(24, '2025_01_01_000100_create_employees_table', 1),
(25, '2025_01_01_000110_create_settings_table', 1),
(26, '2025_01_01_000120_create_activity_logs_table', 1),
(27, '2025_01_01_000121_create_app_notifications_table', 1),
(28, '2025_01_01_000130_create_personal_access_tokens_table', 1),
(29, '2025_02_01_000001_create_milk_purchases_table', 1),
(30, '2025_02_01_000002_create_milk_stock_ledger_table', 1),
(31, '2025_02_01_000003_create_cash_sales_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `milk_purchases`
--

DROP TABLE IF EXISTS `milk_purchases`;
CREATE TABLE IF NOT EXISTS `milk_purchases` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `incoming_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_time` time DEFAULT NULL,
  `shift` enum('morning','evening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `milk_type` enum('cow','buffalo','mixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cow',
  `quantity` decimal(10,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `fat_percent` decimal(5,2) DEFAULT NULL,
  `snf_percent` decimal(5,2) DEFAULT NULL,
  `quality_grade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('paid','partial','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method_id` bigint UNSIGNED DEFAULT NULL,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `milk_purchases_incoming_no_unique` (`incoming_no`),
  KEY `milk_purchases_supplier_id_foreign` (`supplier_id`),
  KEY `milk_purchases_payment_method_id_foreign` (`payment_method_id`),
  KEY `milk_purchases_created_by_foreign` (`created_by`),
  KEY `milk_purchases_purchase_date_shift_index` (`purchase_date`,`shift`),
  KEY `milk_purchases_purchase_date_index` (`purchase_date`),
  KEY `milk_purchases_shift_index` (`shift`),
  KEY `milk_purchases_payment_status_index` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `milk_purchases`
--

TRUNCATE TABLE `milk_purchases`;
-- --------------------------------------------------------

--
-- Table structure for table `milk_rates`
--

DROP TABLE IF EXISTS `milk_rates`;
CREATE TABLE IF NOT EXISTS `milk_rates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `effective_date` date NOT NULL,
  `cow_morning_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `cow_evening_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `buffalo_morning_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `buffalo_evening_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `mixed_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `milk_rates_effective_date_unique` (`effective_date`),
  KEY `milk_rates_created_by_foreign` (`created_by`),
  KEY `milk_rates_effective_date_index` (`effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `milk_rates`
--

TRUNCATE TABLE `milk_rates`;
-- --------------------------------------------------------

--
-- Table structure for table `milk_stock_ledger`
--

DROP TABLE IF EXISTS `milk_stock_ledger`;
CREATE TABLE IF NOT EXISTS `milk_stock_ledger` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `movement_date` date NOT NULL,
  `shift` enum('morning','evening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `movement_type` enum('purchase','card_sale','cash_sale','dairy_sale','wastage','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock_before` decimal(10,3) NOT NULL,
  `stock_after` decimal(10,3) NOT NULL,
  `source_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint UNSIGNED DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `milk_stock_ledger_code_unique` (`code`),
  KEY `milk_stock_ledger_source_type_source_id_index` (`source_type`,`source_id`),
  KEY `milk_stock_ledger_created_by_foreign` (`created_by`),
  KEY `milk_stock_ledger_movement_date_shift_direction_index` (`movement_date`,`shift`,`direction`),
  KEY `milk_stock_ledger_movement_date_index` (`movement_date`),
  KEY `milk_stock_ledger_shift_index` (`shift`),
  KEY `milk_stock_ledger_direction_index` (`direction`),
  KEY `milk_stock_ledger_movement_type_index` (`movement_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `milk_stock_ledger`
--

TRUNCATE TABLE `milk_stock_ledger`;
-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `model_has_permissions`
--

TRUNCATE TABLE `model_has_permissions`;
-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `model_has_roles`
--

TRUNCATE TABLE `model_has_roles`;
--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `password_reset_tokens`
--

TRUNCATE TABLE `password_reset_tokens`;
-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `bill_id` bigint UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `adjustment` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `is_advance` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('pending','cleared','bounced','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cleared',
  `receipt_pdf_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sms_sent` tinyint(1) NOT NULL DEFAULT '0',
  `whatsapp_sent` tinyint(1) NOT NULL DEFAULT '0',
  `received_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_receipt_number_unique` (`receipt_number`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_bill_id_foreign` (`bill_id`),
  KEY `payments_payment_method_id_foreign` (`payment_method_id`),
  KEY `payments_received_by_foreign` (`received_by`),
  KEY `payments_payment_date_index` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `payments`
--

TRUNCATE TABLE `payments`;
-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_methods_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `payment_methods`
--

TRUNCATE TABLE `payment_methods`;
--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'cash', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(2, 'UPI', 'upi', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(3, 'Bank Transfer', 'bank-transfer', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(4, 'Cheque', 'cheque', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11'),
(5, 'Wallet', 'wallet', 1, '2026-09-09 05:49:11', '2026-09-09 05:49:11');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'dashboard.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(2, 'customers.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(3, 'customers.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(4, 'customers.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(5, 'customers.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(6, 'customers.export', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(7, 'milk-rates.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(8, 'milk-rates.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(9, 'milk-rates.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(10, 'milk-rates.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(11, 'daily-entries.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(12, 'daily-entries.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(13, 'daily-entries.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(14, 'daily-entries.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(15, 'daily-entries.lock-override', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(16, 'bills.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(17, 'bills.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(18, 'bills.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(19, 'bills.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(20, 'bills.generate', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(21, 'bills.export', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(22, 'bills.print', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(23, 'payments.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(24, 'payments.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(25, 'payments.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(26, 'payments.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(27, 'payments.export', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(28, 'payments.print', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(29, 'expenses.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(30, 'expenses.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(31, 'expenses.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(32, 'expenses.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(33, 'expenses.export', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(34, 'employees.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(35, 'employees.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(36, 'employees.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(37, 'employees.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(38, 'routes.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(39, 'routes.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(40, 'routes.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(41, 'routes.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(42, 'products.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(43, 'products.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(44, 'products.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(45, 'products.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(46, 'inventory.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(47, 'inventory.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(48, 'inventory.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(49, 'inventory.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(50, 'suppliers.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(51, 'suppliers.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(52, 'suppliers.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(53, 'suppliers.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(54, 'milk-purchases.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(55, 'milk-purchases.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(56, 'milk-purchases.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(57, 'milk-purchases.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(58, 'milk-stock.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(59, 'milk-stock.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(60, 'cash-sales.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(61, 'cash-sales.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(62, 'cash-sales.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(63, 'cash-sales.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(64, 'reports.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(65, 'reports.export', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(66, 'settings.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(67, 'settings.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(68, 'users.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(69, 'users.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(70, 'users.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(71, 'users.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(72, 'roles.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(73, 'roles.create', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(74, 'roles.edit', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(75, 'roles.delete', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(76, 'activity-logs.view', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `personal_access_tokens`
--

TRUNCATE TABLE `personal_access_tokens`;
-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'litre',
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `current_stock` decimal(12,3) NOT NULL DEFAULT '0.000',
  `low_stock_threshold` decimal(12,3) NOT NULL DEFAULT '0.000',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `products`
--

TRUNCATE TABLE `products`;
-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `roles`
--

TRUNCATE TABLE `roles`;
--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(2, 'Manager', 'web', '2026-09-09 05:49:09', '2026-09-09 05:49:09'),
(3, 'Accountant', 'web', '2026-09-09 05:49:10', '2026-09-09 05:49:10'),
(4, 'Operator', 'web', '2026-09-09 05:49:10', '2026-09-09 05:49:10'),
(5, 'Delivery Boy', 'web', '2026-09-09 05:49:10', '2026-09-09 05:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `role_has_permissions`
--

TRUNCATE TABLE `role_has_permissions`;
--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(11, 2),
(12, 2),
(13, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(38, 2),
(39, 2),
(40, 2),
(42, 2),
(43, 2),
(44, 2),
(46, 2),
(47, 2),
(48, 2),
(50, 2),
(51, 2),
(52, 2),
(54, 2),
(55, 2),
(56, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(64, 2),
(65, 2),
(76, 2),
(1, 3),
(2, 3),
(6, 3),
(16, 3),
(17, 3),
(18, 3),
(20, 3),
(21, 3),
(22, 3),
(23, 3),
(24, 3),
(25, 3),
(27, 3),
(28, 3),
(29, 3),
(30, 3),
(31, 3),
(33, 3),
(54, 3),
(58, 3),
(60, 3),
(64, 3),
(65, 3),
(1, 4),
(2, 4),
(3, 4),
(4, 4),
(11, 4),
(12, 4),
(13, 4),
(23, 4),
(24, 4),
(60, 4),
(61, 4),
(1, 5),
(2, 5),
(11, 5),
(12, 5),
(13, 5),
(23, 5),
(24, 5);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
CREATE TABLE IF NOT EXISTS `routes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `delivery_boy_id` bigint UNSIGNED DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `routes_code_unique` (`code`),
  KEY `routes_area_id_foreign` (`area_id`),
  KEY `routes_delivery_boy_id_foreign` (`delivery_boy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `routes`
--

TRUNCATE TABLE `routes`;
-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `sessions`
--

TRUNCATE TABLE `sessions`;
--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('785NzqazkpbDq47a5IBLuQevCbe5sIABL2DeBIDp', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmVThKOHlVZHkzM2cwM2lhV2NQWHFZSTExeUwxRlpvUTkyYTd0RmV2IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMVwvbGFyYXZlbFwvY2xhdWRlLXByb2plY3Qtb25lIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1788954841);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `settings`
--

TRUNCATE TABLE `settings`;
--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `group`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'company', 'company_name', 'My Milk Dairy', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(2, 'company', 'company_logo', NULL, 'image', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(3, 'company', 'gst_number', NULL, 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(4, 'company', 'company_address', NULL, 'textarea', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(5, 'company', 'company_phone', NULL, 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(6, 'company', 'company_email', NULL, 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(7, 'company', 'company_website', NULL, 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(8, 'invoice', 'invoice_prefix', 'INV', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(9, 'invoice', 'consumer_prefix', 'MILK', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(10, 'invoice', 'receipt_prefix', 'RCPT', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(11, 'general', 'currency_symbol', '₹', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(12, 'general', 'timezone', 'Asia/Kolkata', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(13, 'general', 'language', 'en', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(14, 'sms', 'sms_provider', 'none', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(15, 'whatsapp', 'whatsapp_enabled', '0', 'boolean', '2026-09-09 05:50:20', '2026-09-09 05:50:20'),
(16, 'backup', 'backup_reminder_days', '7', 'text', '2026-09-09 05:50:20', '2026-09-09 05:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `outstanding_balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_supplier_code_unique` (`supplier_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `suppliers`
--

TRUNCATE TABLE `suppliers`;
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_id` bigint UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_code_unique` (`employee_code`),
  KEY `users_route_id_foreign` (`route_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_code`, `name`, `email`, `phone`, `photo`, `status`, `two_factor_enabled`, `two_factor_secret`, `last_login_at`, `last_login_ip`, `route_id`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'EMP00001', 'Super Admin', 'admin@milkdairy.test', NULL, NULL, 'active', 0, NULL, '2026-09-09 06:11:39', '127.0.0.1', NULL, '2026-09-09 05:49:11', '$2y$12$QVY8dGTGVLOo16gTS0GSVOF/nUjX4FyJTw.rwEFHoMeiAugYKCbjC', NULL, '2026-09-09 05:49:11', '2026-09-09 06:11:39', NULL),
(2, 'EMP00002', 'Hardik Prajapati Vinubhai', 'hardikprajapati8104@gmail.com', '9173307640', NULL, 'active', 0, NULL, NULL, NULL, NULL, NULL, '$2y$12$js6ZnA16u2zbKMrdcshYdOzR/BYP9qhIzRnFTJnJ69HzBZKxVV3ZC', NULL, '2026-09-09 06:22:11', '2026-09-09 06:22:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `villages`
--

DROP TABLE IF EXISTS `villages`;
CREATE TABLE IF NOT EXISTS `villages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `area_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `villages_area_id_foreign` (`area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `villages`
--

TRUNCATE TABLE `villages`;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `app_notifications`
--
ALTER TABLE `app_notifications`
  ADD CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bills_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bill_items`
--
ALTER TABLE `bill_items`
  ADD CONSTRAINT `bill_items_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bill_items_daily_entry_id_foreign` FOREIGN KEY (`daily_entry_id`) REFERENCES `daily_entries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bill_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cash_sales`
--
ALTER TABLE `cash_sales`
  ADD CONSTRAINT `cash_sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cash_sales_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_customer_category_id_foreign` FOREIGN KEY (`customer_category_id`) REFERENCES `customer_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `customers_village_id_foreign` FOREIGN KEY (`village_id`) REFERENCES `villages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_documents`
--
ALTER TABLE `customer_documents`
  ADD CONSTRAINT `customer_documents_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `daily_entries`
--
ALTER TABLE `daily_entries`
  ADD CONSTRAINT `daily_entries_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `daily_entries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_entries_entered_by_foreign` FOREIGN KEY (`entered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `daily_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_attendances`
--
ALTER TABLE `employee_attendances`
  ADD CONSTRAINT `employee_attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `expenses_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inventory_movements`
--
ALTER TABLE `inventory_movements`
  ADD CONSTRAINT `inventory_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventory_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventory_movements_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `milk_purchases`
--
ALTER TABLE `milk_purchases`
  ADD CONSTRAINT `milk_purchases_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `milk_purchases_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `milk_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `milk_rates`
--
ALTER TABLE `milk_rates`
  ADD CONSTRAINT `milk_rates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `milk_stock_ledger`
--
ALTER TABLE `milk_stock_ledger`
  ADD CONSTRAINT `milk_stock_ledger_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_bill_id_foreign` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `payments_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `routes`
--
ALTER TABLE `routes`
  ADD CONSTRAINT `routes_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `routes_delivery_boy_id_foreign` FOREIGN KEY (`delivery_boy_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `villages`
--
ALTER TABLE `villages`
  ADD CONSTRAINT `villages_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

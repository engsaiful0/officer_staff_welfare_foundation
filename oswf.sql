-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2026 at 02:05 AM
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
-- Database: `oswf`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `academic_year_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '2025', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(2, '2024', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(3, '2023', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(4, '2022', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(5, '2021', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(6, '2020', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(7, '2019', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(8, '2018', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(9, '2017', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(10, '2016', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(11, '2015', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(12, '2014', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(13, '2013', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(14, '2012', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(15, '2011', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(16, '2010', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(17, '2009', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(18, '2008', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(19, '2007', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(20, '2006', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(21, '2040', NULL, '2025-09-03 23:15:18', '2025-09-03 23:15:18');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `fevicon` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `date_format` varchar(255) DEFAULT NULL,
  `time_format` varchar(255) DEFAULT NULL,
  `maintainence_mode` tinyint(1) NOT NULL DEFAULT 0,
  `maintainence_mode_message` text DEFAULT NULL,
  `sms_url` varchar(600) DEFAULT NULL,
  `api_key` varchar(600) DEFAULT NULL,
  `sender_id` varchar(600) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sms_status` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `app_name`, `address`, `phone`, `email`, `website`, `currency`, `logo`, `fevicon`, `start_date`, `date_format`, `time_format`, `maintainence_mode`, `maintainence_mode_message`, `sms_url`, `api_key`, `sender_id`, `user_id`, `sms_status`, `created_at`, `updated_at`) VALUES
(1, 'OSWF', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', '01818650864', 'saifuldev2011@gmail.com', 'www.ppi.com', 'BDT', 'logo.png', 'fevicon.jpg', NULL, NULL, NULL, 0, 'ddd', 'http://bulksmsbd.net/api/smsapi', '3H2X9Cc7pm07zt3MpUIf', '8809617619447', NULL, 'off', NULL, '2025-10-11 04:06:09');

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `board_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`id`, `board_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'BTEB', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(2, 'Chittagong', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(3, 'Dhaka', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(4, 'Rajshahi', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(5, 'Comilla', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(6, 'Jessore', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(7, 'Sylhet', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(8, 'Barisal', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(9, 'Mymensingh', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(10, 'Dinajpur', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(11, 'Technical', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_name` varchar(255) NOT NULL,
  `branch_address` text NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_name`, `branch_address`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Chawk Bazar', 'Chawk Bazar', 1, '2025-09-21 19:39:45', '2025-09-21 19:39:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('expense_heads', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:22:\"App\\Models\\ExpenseHead\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"expense_heads\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:4:\"name\";s:5:\"TA-DA\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:08\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:08\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:4:\"name\";s:5:\"TA-DA\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:08\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:08\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:22:\"App\\Models\\ExpenseHead\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"expense_heads\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:2;s:4:\"name\";s:6:\"Salary\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:16\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:16\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:2;s:4:\"name\";s:6:\"Salary\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:16\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:16\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:22:\"App\\Models\\ExpenseHead\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"expense_heads\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:3;s:4:\"name\";s:10:\"House Rent\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:30\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:30\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:3;s:4:\"name\";s:10:\"House Rent\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-08-28 01:05:30\";s:10:\"updated_at\";s:19:\"2025-08-28 01:05:30\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:22:\"App\\Models\\ExpenseHead\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"expense_heads\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:5;s:4:\"name\";s:9:\"Transport\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-09-04 06:25:05\";s:10:\"updated_at\";s:19:\"2025-09-04 06:25:05\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:5;s:4:\"name\";s:9:\"Transport\";s:7:\"user_id\";N;s:10:\"created_at\";s:19:\"2025-09-04 06:25:05\";s:10:\"updated_at\";s:19:\"2025-09-04 06:25:05\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1760180395);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `designation_name` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `designation_type` enum('Member','Employee','Management') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `designation_name`, `user_id`, `created_at`, `updated_at`, `designation_type`) VALUES
(1, 'Instructor', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24', NULL),
(2, 'Sub-Instructor', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24', NULL),
(3, 'Chief Instructor', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(4, 'Principal', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(5, 'Vice Principal', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(6, 'Lecturer', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(7, 'Senior Lecturer', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(8, 'Assistant Professor', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(9, 'Associate Professor', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(10, 'Professor', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25', NULL),
(11, 'Officer', NULL, '2025-08-26 20:30:46', '2025-08-26 20:30:46', NULL),
(12, 'Officer Cash', NULL, '2025-08-26 20:33:43', '2025-08-26 20:33:43', NULL),
(13, 'Test Designation', 1, '2025-10-03 05:47:35', '2025-10-03 05:47:35', 'Member'),
(15, 'ffff', 1, '2025-10-03 05:56:19', '2025-10-03 05:56:19', 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_unique_id` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nid` varchar(255) NOT NULL,
  `religion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `present_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `cv_upload` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_group` varchar(255) DEFAULT NULL,
  `ssc_result` varchar(255) DEFAULT NULL,
  `ssc_documents_upload` varchar(255) DEFAULT NULL,
  `hsc_or_equivalent_group` varchar(255) DEFAULT NULL,
  `hsc_result` varchar(255) DEFAULT NULL,
  `hsc_documents_upload` varchar(255) DEFAULT NULL,
  `bachelor_or_equivalent_group` varchar(255) DEFAULT NULL,
  `result` varchar(255) DEFAULT NULL,
  `honors_documents_upload` varchar(255) DEFAULT NULL,
  `master_or_equivalent_group` varchar(255) DEFAULT NULL,
  `masters_result` varchar(255) DEFAULT NULL,
  `masters_document_upload` varchar(255) DEFAULT NULL,
  `years_of_experience` varchar(255) DEFAULT NULL,
  `date_of_join` date DEFAULT NULL,
  `basic_salary` varchar(255) DEFAULT NULL,
  `house_rent` varchar(255) DEFAULT NULL,
  `medical_allowance` varchar(255) DEFAULT NULL,
  `other_allowance` varchar(255) DEFAULT NULL,
  `gross_salary` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_name`, `employee_unique_id`, `gender`, `father_name`, `mother_name`, `mobile`, `email`, `nid`, `religion_id`, `designation_id`, `present_address`, `permanent_address`, `picture`, `cv_upload`, `ssc_or_equivalent_group`, `ssc_result`, `ssc_documents_upload`, `hsc_or_equivalent_group`, `hsc_result`, `hsc_documents_upload`, `bachelor_or_equivalent_group`, `result`, `honors_documents_upload`, `master_or_equivalent_group`, `masters_result`, `masters_document_upload`, `years_of_experience`, `date_of_join`, `basic_salary`, `house_rent`, `medical_allowance`, `other_allowance`, `gross_salary`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'dddd', 'E-0001', 'male', 'fdfdddd', 'dfdfd', '+8801818650864', 'saifuldev2011@gmail.com', '3333333', 1, 11, 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', 'profile_pictures/283dmmlJRNKH8csfAQ4NxFh4qyAX2W9mBnqiZjUn.png', 'cvs/H7F1CzCbsUq0AgoYTGo4OpPoD2hadFIMUWQxDZ8M.pdf', 'Science', '3', 'documents/0vS0QbPccdlygUgAgREJoZgyPwxSJDo5U11VDkHJ.jpg', 'Science', '3', 'documents/vtuY92Uj4c3m1tsNdISxD5W1ykhrspAoWUfZm0Fy.png', 'TE', '3', 'documents/xxHGDGONThxGoFkDWHDTRTXDTcfvSqBwwAdj2LLf.png', 'Accounting', '3', 'documents/dhmkLF9JFvtVScl6FBiIqP3C5aSuUjG0ygOYp644.png', '33', '2025-08-26', '33', '33', '33', '2000', '2099', 1, '2025-08-27 04:54:54', '2025-08-27 10:30:16'),
(5, 'dddd', 'E-0002', 'male', 'fdfd', 'dd', '+8801818650865', 'saiftuldev2011@gmail.com', '6545434', 1, 11, 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', 'profile_pictures/yp3WrZK9c9U4A0bhgWgeY7GZUuHl79Di64Hwa54Y.jpg', 'cvs/O8s7pKzNZ0Va08JACJS2Jygh5UvZULZrdvDvJJYG.pdf', 'Science', '5', 'documents/Pxvuk2dOoEh7D6jIfluMxwXHCLZjAB8mG4B8nm3G.jpg', 'Science', '4', 'documents/TOamVDPIeOeHI7jUiwBXQYteS1U8kJie8aKMd1TE.jpg', 'IPE', '4', 'documents/CWElKrbN9EvUukTzhgTOYmjilpmy0JIaJ4s3Rrsp.jpg', 'IPE', '4', 'documents/VstzQPg3Mm5Gzjaj5uQRFpYDqhUIQi2xYxegHglL.jpg', '4', '2025-08-27', '4', '4', '4', '4', '16', 1, '2025-08-27 04:58:19', '2025-08-27 04:58:19'),
(7, 'dddd444', 'E-0003', 'male', 'fdfdddd44', 'c44', '+88013818650864', 'saifurrrldev2011@gmail.com', 'rrr', 1, 11, 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', 'profile_pictures/umfwcbzMlfdKly3U9wk7sm1vCpsvqGWnpVzTtYPf.jpg', 'cvs/SnfEXcyLx5CRbnfF7VoIZIwUXolzvPQQJIQBnKHH.pdf', 'Science', NULL, 'documents/bFfzui3ht4FjTFJjCU2VPdq86neRUklVsdeaHuN3.jpg', 'Science', 'r', 'documents/7oK3LeyDIdcDmWaJ53gNhI2EZ4WybCyQYFxJyOeZ.jpg', 'Accounting', 'r', 'documents/A5OPn9BxXQd2ObaNj67k7tA3X7YOUbVEDUltLERx.jpg', 'ARC', 'r', 'documents/VcKcTdRe1OHqid6DXyzI2BHM41nXw8rEHDp9W6Bw.jpg', '44', '2025-08-29', '44', '44', '4111', '44', '4243', 1, '2025-08-27 05:30:02', '2025-08-27 10:29:50'),
(9, 'dddd', 'E-0004', 'male', 'ddd', 'dd', '+8801818650861', 'saifulde1v2011@gmail.com', '333333311', 1, 11, 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', 'profile_pictures/DF3tJsclv2uw0LYrjBWXDgs5d8lt2MrCFghZAOAr.jpg', 'cvs/6var20UeIcwGpcQDXwD844kfUKeDZ5yjRQFaN4i3.docx', 'Science', '33', 'documents/dvtuPEZzZ6koaE4o2kaMBgBK5l0hEFkDDXPiVOrY.jpg', 'Science', '3', 'documents/PzmShpNsCQdtUnLCsvbAxmoK4Pbmgl1yEYTCtCBc.png', 'CE', '3', 'documents/TakKUNerVmZl4AxOBOxkyswGp1yfceop9M62ZIxX.png', 'Accounting', '3', 'documents/3VlmMyJ4jp76lUPpZgibJNEoKSwtm4mbrz7jZSSe.png', '3', '2025-09-26', '33', '333', '33', '33', '432', 1, '2025-09-06 09:51:50', '2025-09-06 09:51:50');

-- --------------------------------------------------------

--
-- Table structure for table `employee_unique_ids`
--

CREATE TABLE `employee_unique_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_unique_id` varchar(10) NOT NULL,
  `serial` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_unique_ids`
--

INSERT INTO `employee_unique_ids` (`id`, `employee_unique_id`, `serial`, `employee_id`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'E-0001', 1, 2, NULL, '2025-08-27 04:54:54', '2025-08-27 04:54:54'),
(3, 'E-0002', 2, 5, NULL, '2025-08-27 04:58:19', '2025-08-27 04:58:19'),
(4, 'E-0003', 3, 7, NULL, '2025-08-27 05:30:02', '2025-08-27 05:30:02'),
(5, 'E-0004', 4, 9, NULL, '2025-09-06 09:51:50', '2025-09-06 09:51:50');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_head_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `expense_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_head_id`, `user_id`, `expense_date`, `remarks`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, '2025-08-28', '120', 1000.00, '2025-08-27 19:23:04', '2025-08-27 19:23:04'),
(2, 2, NULL, '2025-08-14', '1250', 3500.00, '2025-08-27 19:23:18', '2025-08-27 19:23:18'),
(3, 2, NULL, '2025-09-11', 'fdfd', 5000.00, '2025-09-05 18:31:29', '2025-09-05 18:31:29'),
(4, 3, 1, '2025-09-17', '1000', 1000.00, '2025-09-11 03:04:08', '2025-09-11 03:04:08');

-- --------------------------------------------------------

--
-- Table structure for table `expense_heads`
--

CREATE TABLE `expense_heads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_heads`
--

INSERT INTO `expense_heads` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'TA-DA', NULL, '2025-08-27 19:05:08', '2025-08-27 19:05:08'),
(2, 'Salary', NULL, '2025-08-27 19:05:16', '2025-08-27 19:05:16'),
(3, 'House Rent', NULL, '2025-08-27 19:05:30', '2025-08-27 19:05:30'),
(5, 'Transport', NULL, '2025-09-04 00:25:05', '2025-09-04 00:25:05');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_collects`
--

CREATE TABLE `fee_collects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `year_id` int(11) NOT NULL,
  `months` text DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_heads` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fee_heads`)),
  `date` date DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overdue_days` int(11) NOT NULL DEFAULT 0,
  `fine_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fine_details`)),
  `total_amount` decimal(10,2) NOT NULL,
  `net_payable` decimal(10,2) NOT NULL,
  `total_payable` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_collects`
--

INSERT INTO `fee_collects` (`id`, `academic_year_id`, `year_id`, `months`, `semester_id`, `student_id`, `user_id`, `payment_method_id`, `fee_heads`, `date`, `discount`, `fine_amount`, `overdue_days`, `fine_details`, `total_amount`, `net_payable`, `total_payable`, `created_at`, `updated_at`) VALUES
(27, 1, 1, NULL, 1, 19, 1, 1, '\"[{\\\"id\\\":1,\\\"name\\\":\\\"First Semester Mid Term Fee\\\",\\\"amount\\\":\\\"11111.00\\\"},{\\\"id\\\":6,\\\"name\\\":\\\"First Semester Final Term Fee\\\",\\\"amount\\\":\\\"5000.00\\\"},{\\\"id\\\":29,\\\"name\\\":\\\"ID Card Fee\\\",\\\"amount\\\":\\\"500.00\\\"},{\\\"id\\\":30,\\\"name\\\":\\\"First Semester Registration Fee\\\",\\\"amount\\\":\\\"5000.00\\\"}]\"', '2025-09-19', NULL, 0.00, 0, '\"[]\"', 21611.00, 21611.00, 21611.00, '2025-09-19 03:47:27', '2025-09-19 03:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `fee_heads`
--

CREATE TABLE `fee_heads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `month_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fee_type` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_discountable` varchar(255) NOT NULL DEFAULT 'No',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_heads`
--

INSERT INTO `fee_heads` (`id`, `name`, `semester_id`, `month_id`, `fee_type`, `amount`, `is_discountable`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'First Semester Mid Term Fee', 1, NULL, 'Regular', 11111.00, 'No', NULL, '2025-08-27 11:01:05', '2025-08-27 18:50:37'),
(2, 'January Monthly Fee', 1, 1, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:32:09', '2025-08-27 18:48:44'),
(3, 'February Monthly Fee', NULL, 2, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:44:50', '2025-08-27 18:44:50'),
(4, 'March Monthly Fee', NULL, 3, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:48:22', '2025-08-27 18:48:22'),
(5, 'April Monthly Fee', NULL, 4, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:49:17', '2025-08-27 18:49:17'),
(6, 'First Semester Final Term Fee', 1, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 18:50:59', '2025-08-27 18:50:59'),
(7, 'Second Semester Final Term Fee', 2, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:52:05', '2025-08-27 18:52:05'),
(8, 'Second Semester  Mid Term Fee', 2, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:52:21', '2025-08-27 18:52:21'),
(9, 'Third Semester Final Term Fee', 3, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:52:41', '2025-08-27 18:52:41'),
(10, 'Third Semester Mid Term Fee', 3, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:52:56', '2025-08-27 18:52:56'),
(11, 'Fourth Semester Final Term Fee', 4, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:53:17', '2025-08-27 18:53:17'),
(12, 'Fourth Semester Mid Term Fee', 4, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:53:34', '2025-08-27 18:53:34'),
(13, 'Fifth Semester Final Term Fee', 5, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:53:51', '2025-08-27 18:53:51'),
(14, 'Fifth Semester Mid Term Fee', 5, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:54:04', '2025-08-27 18:54:04'),
(15, 'Sixth Semester Final Term Fee', 6, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:54:28', '2025-08-27 18:54:28'),
(16, 'Sixth Semester Mid Term Fee', 6, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:54:45', '2025-08-27 18:54:45'),
(17, 'Seventh Semester Mid Term Fee', 7, NULL, 'Regular', 100.00, 'No', NULL, '2025-08-27 18:55:02', '2025-08-27 18:55:02'),
(18, 'Seventh Semester Final Term Fee', 7, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:55:14', '2025-08-27 18:55:14'),
(19, 'Eighth Semester Mid Term Fee', 8, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:55:31', '2025-08-27 18:55:31'),
(20, 'Eighth Semester Final Term Fee', 8, NULL, 'Regular', 1000.00, 'No', NULL, '2025-08-27 18:55:49', '2025-08-27 18:55:49'),
(21, 'May Monthly Fee', NULL, 5, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:57:16', '2025-08-27 18:57:16'),
(22, 'June Monthly Fee', NULL, 6, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:57:35', '2025-08-27 18:57:35'),
(23, 'July Monthly Fee', NULL, 7, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:57:53', '2025-08-27 18:57:53'),
(24, 'August Monthly Fee', 1, 8, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:58:09', '2025-09-05 23:41:57'),
(25, 'September Monthly Fee', 1, 9, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:58:24', '2025-09-05 23:41:52'),
(26, 'October Monthly Fee', 1, 10, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:58:37', '2025-09-05 23:41:46'),
(27, 'November Monthly Fee', 1, 11, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:58:52', '2025-09-05 23:41:39'),
(28, 'December Monthly Fee', 1, 12, 'Monthly', 1000.00, 'No', NULL, '2025-08-27 18:59:09', '2025-09-05 23:41:26'),
(29, 'ID Card Fee', 1, NULL, 'Regular', 500.00, 'No', NULL, '2025-08-27 18:59:31', '2025-08-27 18:59:31'),
(30, 'First Semester Registration Fee', 1, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:06:22', '2025-08-27 19:06:22'),
(31, 'Second Semester Registration Fee', 2, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:06:34', '2025-08-27 19:06:34'),
(32, 'Third Semester Registration Fee', 3, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:06:49', '2025-08-27 19:06:49'),
(33, 'Fourth Semester Registration Fee', 4, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:07:01', '2025-08-27 19:07:01'),
(34, 'Fifth Semester Registration Fee', 5, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:07:20', '2025-08-27 19:07:20'),
(35, 'Sixth Semester Registration Fee', 6, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:07:32', '2025-08-27 19:07:32'),
(36, 'Seventh Semester Registration Fee', 7, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:07:56', '2025-08-27 19:07:56'),
(37, 'Eighth Semester Registration Fee', 8, NULL, 'Regular', 5000.00, 'Yes', NULL, '2025-08-27 19:08:12', '2025-08-27 19:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `fee_settings`
--

CREATE TABLE `fee_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_deadline_day` int(11) NOT NULL DEFAULT 10,
  `fine_amount_per_day` decimal(8,2) NOT NULL DEFAULT 0.00,
  `maximum_fine_amount` decimal(10,2) DEFAULT NULL,
  `is_percentage_fine` tinyint(1) NOT NULL DEFAULT 0,
  `fine_percentage` decimal(5,2) DEFAULT NULL,
  `fine_type` varchar(255) NOT NULL DEFAULT 'fixed',
  `grace_period_days` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_settings`
--

INSERT INTO `fee_settings` (`id`, `name`, `amount`, `monthly_fee_amount`, `payment_deadline_day`, `fine_amount_per_day`, `maximum_fine_amount`, `is_percentage_fine`, `fine_percentage`, `fine_type`, `grace_period_days`, `is_active`, `notes`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'Monthly Fee Settings', 1000.00, 0.00, 10, 10.00, NULL, 0, NULL, 'fixed', 0, 1, 'fdf', 1, '2025-09-06 20:11:11', '2025-09-11 01:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `income_heads`
--

CREATE TABLE `income_heads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `income_heads`
--

INSERT INTO `income_heads` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Director Collection', NULL, '2025-09-04 01:09:31', '2025-09-04 01:09:31'),
(2, 'dddd', NULL, '2025-09-04 01:10:32', '2025-09-04 01:10:32');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `term_months` int(11) NOT NULL,
  `expiry_date` date NOT NULL,
  `rate` decimal(8,4) NOT NULL,
  `rate_period` enum('annual','monthly') NOT NULL,
  `frequency` enum('monthly','quarterly','daily') NOT NULL,
  `status` enum('active','matured','closed') NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`id`, `member_id`, `principal_amount`, `product_name`, `start_date`, `term_months`, `expiry_date`, `rate`, `rate_period`, `frequency`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(4, 2, 100000.00, NULL, '2025-11-24', 60, '2030-11-24', 12.0000, 'annual', 'monthly', 'active', NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(5, 3, 200000.00, NULL, '2025-12-01', 60, '2030-12-01', 12.0000, 'annual', 'monthly', 'active', NULL, '2025-11-24 06:30:54', '2025-11-24 06:30:54'),
(6, 4, 100000.00, NULL, '2024-12-01', 60, '2029-12-01', 12.0000, 'annual', 'monthly', 'active', NULL, '2025-11-24 20:14:40', '2025-11-24 20:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `investment_accounts`
--

CREATE TABLE `investment_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `investment_id` bigint(20) UNSIGNED NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `account_opening_date` date NOT NULL,
  `account_closing_date` date DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_principal_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_interest_received` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_rent_received` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_payments_made` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_installments_paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `installments_paid_count` int(11) NOT NULL DEFAULT 0,
  `installments_pending_count` int(11) NOT NULL DEFAULT 0,
  `installments_overdue_count` int(11) NOT NULL DEFAULT 0,
  `account_status` enum('active','closed','matured','suspended') NOT NULL DEFAULT 'active',
  `account_notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investment_accounts`
--

INSERT INTO `investment_accounts` (`id`, `investment_id`, `account_number`, `account_opening_date`, `account_closing_date`, `opening_balance`, `current_balance`, `total_principal_paid`, `total_interest_received`, `total_rent_received`, `total_payments_made`, `total_installments_paid`, `installments_paid_count`, `installments_pending_count`, `installments_overdue_count`, `account_status`, `account_notes`, `created_by`, `updated_by`, `closed_by`, `created_at`, `updated_at`) VALUES
(1, 4, 'INV-ACC-2025-000001', '2025-11-24', NULL, 100000.00, 98333.00, 1667.00, 0.00, 509.00, 860.67, 1.00, 1, 59, 0, 'active', NULL, 1, NULL, NULL, '2025-11-24 06:19:26', '2025-12-17 20:07:20'),
(2, 5, 'INV-ACC-2025-000002', '2025-11-24', NULL, 200000.00, 200000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 60, 0, 'active', NULL, 1, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:57'),
(3, 6, 'INV-ACC-2025-000003', '2024-12-01', '2025-11-26', 100000.00, 100000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 60, 0, 'active', NULL, 1, 1, NULL, '2025-11-24 20:14:40', '2025-11-24 20:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `investment_account_numbers`
--

CREATE TABLE `investment_account_numbers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `serial` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `investment_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investment_account_numbers`
--

INSERT INTO `investment_account_numbers` (`id`, `account_number`, `serial`, `user_id`, `investment_account_id`, `year`, `created_at`, `updated_at`) VALUES
(1, 'INV-ACC-2025-000001', 1, 1, 1, '2025', '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(2, 'INV-ACC-2025-000002', 2, 1, 2, '2025', '2025-11-24 06:30:57', '2025-11-24 06:30:57'),
(3, 'INV-ACC-2025-000003', 3, 1, 3, '2025', '2025-11-24 20:14:40', '2025-11-24 20:14:40');

-- --------------------------------------------------------

--
-- Table structure for table `investment_installments`
--

CREATE TABLE `investment_installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `investment_id` bigint(20) UNSIGNED NOT NULL,
  `installment_number` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `beginning_balance` decimal(15,2) NOT NULL,
  `principal_amount` decimal(15,2) NOT NULL,
  `rent` decimal(15,2) NOT NULL,
  `fine_amount` double NOT NULL DEFAULT 0,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `ending_balance` decimal(15,2) NOT NULL,
  `cumulative_rent` decimal(15,2) NOT NULL,
  `status` enum('pending','paid','overdue') NOT NULL DEFAULT 'pending',
  `paid_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `paid_by` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_reference` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `check_number` varchar(255) DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investment_installments`
--

INSERT INTO `investment_installments` (`id`, `investment_id`, `installment_number`, `schedule_date`, `beginning_balance`, `principal_amount`, `rent`, `fine_amount`, `discount_amount`, `total_amount`, `ending_balance`, `cumulative_rent`, `status`, `paid_date`, `notes`, `created_by`, `paid_by`, `payment_method_id`, `transaction_reference`, `receipt_number`, `bank_name`, `check_number`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2025-11-24', 100000.00, 1667.00, 509.00, -1315.33, 0.00, 860.67, 98333.00, 509.00, 'paid', '2025-12-18', 'jj', 1, 1, 5, NULL, 'RCP-20251218-000001', NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-12-17 20:07:20'),
(2, 4, 2, '2025-12-24', 98333.00, 1667.00, 509.00, 0, 0.00, 2176.00, 96666.00, 1018.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(3, 4, 3, '2026-01-24', 96666.00, 1667.00, 509.00, 0, 0.00, 2176.00, 94999.00, 1527.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(4, 4, 4, '2026-02-24', 94999.00, 1667.00, 509.00, 0, 0.00, 2176.00, 93332.00, 2036.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(5, 4, 5, '2026-03-24', 93332.00, 1667.00, 509.00, 0, 0.00, 2176.00, 91665.00, 2545.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(6, 4, 6, '2026-04-24', 91665.00, 1667.00, 509.00, 0, 0.00, 2176.00, 89998.00, 3054.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(7, 4, 7, '2026-05-24', 89998.00, 1667.00, 509.00, 0, 0.00, 2176.00, 88331.00, 3563.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(8, 4, 8, '2026-06-24', 88331.00, 1667.00, 509.00, 0, 0.00, 2176.00, 86664.00, 4072.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(9, 4, 9, '2026-07-24', 86664.00, 1667.00, 509.00, 0, 0.00, 2176.00, 84997.00, 4581.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(10, 4, 10, '2026-08-24', 84997.00, 1667.00, 509.00, 0, 0.00, 2176.00, 83330.00, 5090.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(11, 4, 11, '2026-09-24', 83330.00, 1667.00, 509.00, 0, 0.00, 2176.00, 81663.00, 5599.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(12, 4, 12, '2026-10-24', 81663.00, 1667.00, 509.00, 0, 0.00, 2176.00, 79996.00, 6108.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(13, 4, 13, '2026-11-24', 79996.00, 1667.00, 509.00, 0, 0.00, 2176.00, 78329.00, 6617.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(14, 4, 14, '2026-12-24', 78329.00, 1667.00, 509.00, 0, 0.00, 2176.00, 76662.00, 7126.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(15, 4, 15, '2027-01-24', 76662.00, 1667.00, 509.00, 0, 0.00, 2176.00, 74995.00, 7635.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(16, 4, 16, '2027-02-24', 74995.00, 1667.00, 509.00, 0, 0.00, 2176.00, 73328.00, 8144.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(17, 4, 17, '2027-03-24', 73328.00, 1667.00, 509.00, 0, 0.00, 2176.00, 71661.00, 8653.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(18, 4, 18, '2027-04-24', 71661.00, 1667.00, 509.00, 0, 0.00, 2176.00, 69994.00, 9162.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(19, 4, 19, '2027-05-24', 69994.00, 1667.00, 509.00, 0, 0.00, 2176.00, 68327.00, 9671.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(20, 4, 20, '2027-06-24', 68327.00, 1667.00, 509.00, 0, 0.00, 2176.00, 66660.00, 10180.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(21, 4, 21, '2027-07-24', 66660.00, 1667.00, 509.00, 0, 0.00, 2176.00, 64993.00, 10689.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(22, 4, 22, '2027-08-24', 64993.00, 1667.00, 509.00, 0, 0.00, 2176.00, 63326.00, 11198.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(23, 4, 23, '2027-09-24', 63326.00, 1667.00, 509.00, 0, 0.00, 2176.00, 61659.00, 11707.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(24, 4, 24, '2027-10-24', 61659.00, 1667.00, 509.00, 0, 0.00, 2176.00, 59992.00, 12216.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(25, 4, 25, '2027-11-24', 59992.00, 1667.00, 509.00, 0, 0.00, 2176.00, 58325.00, 12725.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(26, 4, 26, '2027-12-24', 58325.00, 1667.00, 509.00, 0, 0.00, 2176.00, 56658.00, 13234.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(27, 4, 27, '2028-01-24', 56658.00, 1667.00, 509.00, 0, 0.00, 2176.00, 54991.00, 13743.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(28, 4, 28, '2028-02-24', 54991.00, 1667.00, 509.00, 0, 0.00, 2176.00, 53324.00, 14252.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(29, 4, 29, '2028-03-24', 53324.00, 1667.00, 509.00, 0, 0.00, 2176.00, 51657.00, 14761.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(30, 4, 30, '2028-04-24', 51657.00, 1667.00, 509.00, 0, 0.00, 2176.00, 49990.00, 15270.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(31, 4, 31, '2028-05-24', 49990.00, 1667.00, 509.00, 0, 0.00, 2176.00, 48323.00, 15779.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(32, 4, 32, '2028-06-24', 48323.00, 1667.00, 509.00, 0, 0.00, 2176.00, 46656.00, 16288.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(33, 4, 33, '2028-07-24', 46656.00, 1667.00, 509.00, 0, 0.00, 2176.00, 44989.00, 16797.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(34, 4, 34, '2028-08-24', 44989.00, 1667.00, 509.00, 0, 0.00, 2176.00, 43322.00, 17306.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(35, 4, 35, '2028-09-24', 43322.00, 1667.00, 509.00, 0, 0.00, 2176.00, 41655.00, 17815.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(36, 4, 36, '2028-10-24', 41655.00, 1667.00, 509.00, 0, 0.00, 2176.00, 39988.00, 18324.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(37, 4, 37, '2028-11-24', 39988.00, 1667.00, 509.00, 0, 0.00, 2176.00, 38321.00, 18833.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(38, 4, 38, '2028-12-24', 38321.00, 1667.00, 509.00, 0, 0.00, 2176.00, 36654.00, 19342.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(39, 4, 39, '2029-01-24', 36654.00, 1667.00, 509.00, 0, 0.00, 2176.00, 34987.00, 19851.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(40, 4, 40, '2029-02-24', 34987.00, 1667.00, 509.00, 0, 0.00, 2176.00, 33320.00, 20360.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(41, 4, 41, '2029-03-24', 33320.00, 1667.00, 509.00, 0, 0.00, 2176.00, 31653.00, 20869.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(42, 4, 42, '2029-04-24', 31653.00, 1667.00, 509.00, 0, 0.00, 2176.00, 29986.00, 21378.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(43, 4, 43, '2029-05-24', 29986.00, 1667.00, 509.00, 0, 0.00, 2176.00, 28319.00, 21887.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(44, 4, 44, '2029-06-24', 28319.00, 1667.00, 509.00, 0, 0.00, 2176.00, 26652.00, 22396.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(45, 4, 45, '2029-07-24', 26652.00, 1667.00, 509.00, 0, 0.00, 2176.00, 24985.00, 22905.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(46, 4, 46, '2029-08-24', 24985.00, 1667.00, 509.00, 0, 0.00, 2176.00, 23318.00, 23414.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(47, 4, 47, '2029-09-24', 23318.00, 1667.00, 509.00, 0, 0.00, 2176.00, 21651.00, 23923.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(48, 4, 48, '2029-10-24', 21651.00, 1667.00, 509.00, 0, 0.00, 2176.00, 19984.00, 24432.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(49, 4, 49, '2029-11-24', 19984.00, 1667.00, 509.00, 0, 0.00, 2176.00, 18317.00, 24941.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(50, 4, 50, '2029-12-24', 18317.00, 1667.00, 509.00, 0, 0.00, 2176.00, 16650.00, 25450.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(51, 4, 51, '2030-01-24', 16650.00, 1667.00, 509.00, 0, 0.00, 2176.00, 14983.00, 25959.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(52, 4, 52, '2030-02-24', 14983.00, 1667.00, 509.00, 0, 0.00, 2176.00, 13316.00, 26468.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(53, 4, 53, '2030-03-24', 13316.00, 1667.00, 509.00, 0, 0.00, 2176.00, 11649.00, 26977.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(54, 4, 54, '2030-04-24', 11649.00, 1667.00, 509.00, 0, 0.00, 2176.00, 9982.00, 27486.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(55, 4, 55, '2030-05-24', 9982.00, 1667.00, 509.00, 0, 0.00, 2176.00, 8315.00, 27995.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(56, 4, 56, '2030-06-24', 8315.00, 1667.00, 509.00, 0, 0.00, 2176.00, 6648.00, 28504.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(57, 4, 57, '2030-07-24', 6648.00, 1667.00, 509.00, 0, 0.00, 2176.00, 4981.00, 29013.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(58, 4, 58, '2030-08-24', 4981.00, 1667.00, 509.00, 0, 0.00, 2176.00, 3314.00, 29522.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(59, 4, 59, '2030-09-24', 3314.00, 1667.00, 509.00, 0, 0.00, 2176.00, 1647.00, 30031.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(60, 4, 60, '2030-10-24', 1647.00, 1667.00, 509.00, 0, 0.00, 2176.00, -20.00, 30540.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(61, 5, 1, '2025-12-01', 200000.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 196667.00, 1018.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(62, 5, 2, '2026-01-01', 196667.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 193334.00, 2036.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(63, 5, 3, '2026-02-01', 193334.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 190001.00, 3054.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(64, 5, 4, '2026-03-01', 190001.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 186668.00, 4072.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(65, 5, 5, '2026-04-01', 186668.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 183335.00, 5090.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(66, 5, 6, '2026-05-01', 183335.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 180002.00, 6108.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(67, 5, 7, '2026-06-01', 180002.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 176669.00, 7126.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(68, 5, 8, '2026-07-01', 176669.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 173336.00, 8144.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(69, 5, 9, '2026-08-01', 173336.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 170003.00, 9162.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(70, 5, 10, '2026-09-01', 170003.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 166670.00, 10180.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(71, 5, 11, '2026-10-01', 166670.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 163337.00, 11198.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(72, 5, 12, '2026-11-01', 163337.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 160004.00, 12216.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(73, 5, 13, '2026-12-01', 160004.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 156671.00, 13234.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(74, 5, 14, '2027-01-01', 156671.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 153338.00, 14252.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(75, 5, 15, '2027-02-01', 153338.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 150005.00, 15270.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(76, 5, 16, '2027-03-01', 150005.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 146672.00, 16288.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(77, 5, 17, '2027-04-01', 146672.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 143339.00, 17306.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(78, 5, 18, '2027-05-01', 143339.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 140006.00, 18324.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(79, 5, 19, '2027-06-01', 140006.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 136673.00, 19342.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(80, 5, 20, '2027-07-01', 136673.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 133340.00, 20360.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(81, 5, 21, '2027-08-01', 133340.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 130007.00, 21378.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(82, 5, 22, '2027-09-01', 130007.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 126674.00, 22396.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(83, 5, 23, '2027-10-01', 126674.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 123341.00, 23414.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(84, 5, 24, '2027-11-01', 123341.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 120008.00, 24432.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(85, 5, 25, '2027-12-01', 120008.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 116675.00, 25450.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(86, 5, 26, '2028-01-01', 116675.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 113342.00, 26468.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(87, 5, 27, '2028-02-01', 113342.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 110009.00, 27486.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(88, 5, 28, '2028-03-01', 110009.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 106676.00, 28504.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(89, 5, 29, '2028-04-01', 106676.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 103343.00, 29522.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(90, 5, 30, '2028-05-01', 103343.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 100010.00, 30540.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(91, 5, 31, '2028-06-01', 100010.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 96677.00, 31558.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(92, 5, 32, '2028-07-01', 96677.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 93344.00, 32576.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(93, 5, 33, '2028-08-01', 93344.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 90011.00, 33594.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(94, 5, 34, '2028-09-01', 90011.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 86678.00, 34612.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(95, 5, 35, '2028-10-01', 86678.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 83345.00, 35630.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(96, 5, 36, '2028-11-01', 83345.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 80012.00, 36648.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(97, 5, 37, '2028-12-01', 80012.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 76679.00, 37666.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(98, 5, 38, '2029-01-01', 76679.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 73346.00, 38684.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(99, 5, 39, '2029-02-01', 73346.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 70013.00, 39702.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(100, 5, 40, '2029-03-01', 70013.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 66680.00, 40720.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(101, 5, 41, '2029-04-01', 66680.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 63347.00, 41738.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(102, 5, 42, '2029-05-01', 63347.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 60014.00, 42756.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(103, 5, 43, '2029-06-01', 60014.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 56681.00, 43774.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(104, 5, 44, '2029-07-01', 56681.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 53348.00, 44792.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(105, 5, 45, '2029-08-01', 53348.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 50015.00, 45810.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(106, 5, 46, '2029-09-01', 50015.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 46682.00, 46828.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(107, 5, 47, '2029-10-01', 46682.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 43349.00, 47846.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(108, 5, 48, '2029-11-01', 43349.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 40016.00, 48864.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(109, 5, 49, '2029-12-01', 40016.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 36683.00, 49882.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(110, 5, 50, '2030-01-01', 36683.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 33350.00, 50900.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(111, 5, 51, '2030-02-01', 33350.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 30017.00, 51918.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(112, 5, 52, '2030-03-01', 30017.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 26684.00, 52936.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(113, 5, 53, '2030-04-01', 26684.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 23351.00, 53954.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(114, 5, 54, '2030-05-01', 23351.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 20018.00, 54972.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(115, 5, 55, '2030-06-01', 20018.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 16685.00, 55990.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(116, 5, 56, '2030-07-01', 16685.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 13352.00, 57008.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(117, 5, 57, '2030-08-01', 13352.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 10019.00, 58026.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(118, 5, 58, '2030-09-01', 10019.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 6686.00, 59044.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(119, 5, 59, '2030-10-01', 6686.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 3353.00, 60062.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(120, 5, 60, '2030-11-01', 3353.00, 3333.00, 1018.00, 0, 0.00, 4351.00, 20.00, 61080.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(121, 6, 1, '2025-11-25', 100000.00, 1667.00, 509.00, 0, 0.00, 2176.00, 98333.00, 509.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(122, 6, 2, '2025-12-25', 98333.00, 1667.00, 509.00, 0, 0.00, 2176.00, 96666.00, 1018.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(123, 6, 3, '2026-01-25', 96666.00, 1667.00, 509.00, 0, 0.00, 2176.00, 94999.00, 1527.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(124, 6, 4, '2026-02-25', 94999.00, 1667.00, 509.00, 0, 0.00, 2176.00, 93332.00, 2036.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(125, 6, 5, '2026-03-25', 93332.00, 1667.00, 509.00, 0, 0.00, 2176.00, 91665.00, 2545.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(126, 6, 6, '2026-04-25', 91665.00, 1667.00, 509.00, 0, 0.00, 2176.00, 89998.00, 3054.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(127, 6, 7, '2026-05-25', 89998.00, 1667.00, 509.00, 0, 0.00, 2176.00, 88331.00, 3563.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(128, 6, 8, '2026-06-25', 88331.00, 1667.00, 509.00, 0, 0.00, 2176.00, 86664.00, 4072.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(129, 6, 9, '2026-07-25', 86664.00, 1667.00, 509.00, 0, 0.00, 2176.00, 84997.00, 4581.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(130, 6, 10, '2026-08-25', 84997.00, 1667.00, 509.00, 0, 0.00, 2176.00, 83330.00, 5090.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(131, 6, 11, '2026-09-25', 83330.00, 1667.00, 509.00, 0, 0.00, 2176.00, 81663.00, 5599.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(132, 6, 12, '2026-10-25', 81663.00, 1667.00, 509.00, 0, 0.00, 2176.00, 79996.00, 6108.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(133, 6, 13, '2026-11-25', 79996.00, 1667.00, 509.00, 0, 0.00, 2176.00, 78329.00, 6617.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(134, 6, 14, '2026-12-25', 78329.00, 1667.00, 509.00, 0, 0.00, 2176.00, 76662.00, 7126.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(135, 6, 15, '2027-01-25', 76662.00, 1667.00, 509.00, 0, 0.00, 2176.00, 74995.00, 7635.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(136, 6, 16, '2027-02-25', 74995.00, 1667.00, 509.00, 0, 0.00, 2176.00, 73328.00, 8144.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(137, 6, 17, '2027-03-25', 73328.00, 1667.00, 509.00, 0, 0.00, 2176.00, 71661.00, 8653.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(138, 6, 18, '2027-04-25', 71661.00, 1667.00, 509.00, 0, 0.00, 2176.00, 69994.00, 9162.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(139, 6, 19, '2027-05-25', 69994.00, 1667.00, 509.00, 0, 0.00, 2176.00, 68327.00, 9671.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(140, 6, 20, '2027-06-25', 68327.00, 1667.00, 509.00, 0, 0.00, 2176.00, 66660.00, 10180.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(141, 6, 21, '2027-07-25', 66660.00, 1667.00, 509.00, 0, 0.00, 2176.00, 64993.00, 10689.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(142, 6, 22, '2027-08-25', 64993.00, 1667.00, 509.00, 0, 0.00, 2176.00, 63326.00, 11198.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(143, 6, 23, '2027-09-25', 63326.00, 1667.00, 509.00, 0, 0.00, 2176.00, 61659.00, 11707.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(144, 6, 24, '2027-10-25', 61659.00, 1667.00, 509.00, 0, 0.00, 2176.00, 59992.00, 12216.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(145, 6, 25, '2027-11-25', 59992.00, 1667.00, 509.00, 0, 0.00, 2176.00, 58325.00, 12725.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(146, 6, 26, '2027-12-25', 58325.00, 1667.00, 509.00, 0, 0.00, 2176.00, 56658.00, 13234.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(147, 6, 27, '2028-01-25', 56658.00, 1667.00, 509.00, 0, 0.00, 2176.00, 54991.00, 13743.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(148, 6, 28, '2028-02-25', 54991.00, 1667.00, 509.00, 0, 0.00, 2176.00, 53324.00, 14252.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(149, 6, 29, '2028-03-25', 53324.00, 1667.00, 509.00, 0, 0.00, 2176.00, 51657.00, 14761.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(150, 6, 30, '2028-04-25', 51657.00, 1667.00, 509.00, 0, 0.00, 2176.00, 49990.00, 15270.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(151, 6, 31, '2028-05-25', 49990.00, 1667.00, 509.00, 0, 0.00, 2176.00, 48323.00, 15779.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(152, 6, 32, '2028-06-25', 48323.00, 1667.00, 509.00, 0, 0.00, 2176.00, 46656.00, 16288.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(153, 6, 33, '2028-07-25', 46656.00, 1667.00, 509.00, 0, 0.00, 2176.00, 44989.00, 16797.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(154, 6, 34, '2028-08-25', 44989.00, 1667.00, 509.00, 0, 0.00, 2176.00, 43322.00, 17306.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(155, 6, 35, '2028-09-25', 43322.00, 1667.00, 509.00, 0, 0.00, 2176.00, 41655.00, 17815.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(156, 6, 36, '2028-10-25', 41655.00, 1667.00, 509.00, 0, 0.00, 2176.00, 39988.00, 18324.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(157, 6, 37, '2028-11-25', 39988.00, 1667.00, 509.00, 0, 0.00, 2176.00, 38321.00, 18833.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(158, 6, 38, '2028-12-25', 38321.00, 1667.00, 509.00, 0, 0.00, 2176.00, 36654.00, 19342.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(159, 6, 39, '2029-01-25', 36654.00, 1667.00, 509.00, 0, 0.00, 2176.00, 34987.00, 19851.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(160, 6, 40, '2029-02-25', 34987.00, 1667.00, 509.00, 0, 0.00, 2176.00, 33320.00, 20360.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(161, 6, 41, '2029-03-25', 33320.00, 1667.00, 509.00, 0, 0.00, 2176.00, 31653.00, 20869.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(162, 6, 42, '2029-04-25', 31653.00, 1667.00, 509.00, 0, 0.00, 2176.00, 29986.00, 21378.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(163, 6, 43, '2029-05-25', 29986.00, 1667.00, 509.00, 0, 0.00, 2176.00, 28319.00, 21887.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(164, 6, 44, '2029-06-25', 28319.00, 1667.00, 509.00, 0, 0.00, 2176.00, 26652.00, 22396.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(165, 6, 45, '2029-07-25', 26652.00, 1667.00, 509.00, 0, 0.00, 2176.00, 24985.00, 22905.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(166, 6, 46, '2029-08-25', 24985.00, 1667.00, 509.00, 0, 0.00, 2176.00, 23318.00, 23414.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(167, 6, 47, '2029-09-25', 23318.00, 1667.00, 509.00, 0, 0.00, 2176.00, 21651.00, 23923.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(168, 6, 48, '2029-10-25', 21651.00, 1667.00, 509.00, 0, 0.00, 2176.00, 19984.00, 24432.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(169, 6, 49, '2029-11-25', 19984.00, 1667.00, 509.00, 0, 0.00, 2176.00, 18317.00, 24941.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(170, 6, 50, '2029-12-25', 18317.00, 1667.00, 509.00, 0, 0.00, 2176.00, 16650.00, 25450.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(171, 6, 51, '2030-01-25', 16650.00, 1667.00, 509.00, 0, 0.00, 2176.00, 14983.00, 25959.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(172, 6, 52, '2030-02-25', 14983.00, 1667.00, 509.00, 0, 0.00, 2176.00, 13316.00, 26468.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(173, 6, 53, '2030-03-25', 13316.00, 1667.00, 509.00, 0, 0.00, 2176.00, 11649.00, 26977.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(174, 6, 54, '2030-04-25', 11649.00, 1667.00, 509.00, 0, 0.00, 2176.00, 9982.00, 27486.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(175, 6, 55, '2030-05-25', 9982.00, 1667.00, 509.00, 0, 0.00, 2176.00, 8315.00, 27995.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(176, 6, 56, '2030-06-25', 8315.00, 1667.00, 509.00, 0, 0.00, 2176.00, 6648.00, 28504.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(177, 6, 57, '2030-07-25', 6648.00, 1667.00, 509.00, 0, 0.00, 2176.00, 4981.00, 29013.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(178, 6, 58, '2030-08-25', 4981.00, 1667.00, 509.00, 0, 0.00, 2176.00, 3314.00, 29522.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(179, 6, 59, '2030-09-25', 3314.00, 1667.00, 509.00, 0, 0.00, 2176.00, 1647.00, 30031.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(180, 6, 60, '2030-10-25', 1647.00, 1667.00, 509.00, 0, 0.00, 2176.00, -20.00, 30540.00, 'pending', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-24 20:14:40', '2025-11-24 20:14:40');

-- --------------------------------------------------------

--
-- Table structure for table `investment_types`
--

CREATE TABLE `investment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `investment_type_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `investment_types`
--

INSERT INTO `investment_types` (`id`, `investment_type_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'HPSM', 1, '2025-11-23 11:59:33', '2025-11-23 11:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_entries`
--

CREATE TABLE `ledger_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(255) NOT NULL DEFAULT 'investment',
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('deposit','withdrawal','accrual','interest','adjustment','principal','payment','credit') NOT NULL,
  `entry_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `interest_amount` decimal(15,2) DEFAULT NULL,
  `principal_amount` decimal(15,2) DEFAULT NULL,
  `balance_after` decimal(15,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ledger_entries`
--

INSERT INTO `ledger_entries` (`id`, `entity_type`, `entity_id`, `type`, `entry_date`, `amount`, `interest_amount`, `principal_amount`, `balance_after`, `description`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'investment', 4, 'principal', '2025-11-24', 100000.00, NULL, 100000.00, 100000.00, 'Initial investment principal', 1, '2025-11-24 06:19:26', '2025-11-24 06:19:26'),
(3, 'investment', 5, 'principal', '2025-12-01', 200000.00, NULL, 200000.00, 200000.00, 'Initial investment principal', 1, '2025-11-24 06:30:56', '2025-11-24 06:30:56'),
(4, 'investment', 6, 'principal', '2025-11-25', 100000.00, NULL, 100000.00, 100000.00, 'Initial investment principal', 1, '2025-11-24 20:14:40', '2025-11-24 20:14:40'),
(5, 'investment', 4, 'payment', '2025-12-18', 860.67, 509.00, 1667.00, 98333.00, 'Payment for installment #1', 1, '2025-12-17 20:07:20', '2025-12-17 20:07:20');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nid_number` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `designation_id` bigint(20) UNSIGNED NOT NULL,
  `date_of_join` date NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `introducer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `religion_id` bigint(20) UNSIGNED NOT NULL,
  `relation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nominee_name` varchar(255) DEFAULT NULL,
  `nominee_relation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nominee_phone` varchar(255) DEFAULT NULL,
  `temp_username` varchar(255) NOT NULL,
  `temp_password` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `father_name`, `mobile`, `email`, `nid_number`, `picture`, `designation_id`, `date_of_join`, `branch_id`, `present_address`, `permanent_address`, `unique_id`, `introducer_id`, `religion_id`, `relation_id`, `nominee_name`, `nominee_relation_id`, `nominee_phone`, `temp_username`, `temp_password`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'saiful islam', 'xxx', '01818650864', 'saifuldev2011@gmail.com', '111', 'profile_pictures/VSj89wc6KiMgLn3YrRtVpBGqPg9au8sbhz9wNioZ.png', 13, '2025-10-09', 1, 'Chattogram, Bangladesh', 'Gausia Abasik', 'MEM287928', NULL, 1, NULL, 'gggg', 2, '01818650864', 'saifulislam673', 'QOJbTGsf', 2, '2025-10-03 07:19:00', '2025-10-03 07:19:01'),
(3, 'xx', 'eeddd', '01818653864', 'sa3ifuldev2011@gmail.com', 'dddddddddd', 'profile_pictures/ugHKNnudUNbEUUlDp47OX4FStBXlkCUXFKQnjoDW.png', 13, '2025-10-07', 1, 'Chattogram, Bangladesh', 'Gausia Abasik', 'MEM504667', 2, 1, NULL, 'gggg', 1, '01818650864', 'xx520', 'z5W0ewgk', 3, '2025-10-11 04:08:01', '2025-10-11 04:08:01'),
(4, 'yyy', 'yydd', '01818650863', 'saifulde3v2011@gmail.com', '333ddd', 'profile_pictures/xbCac2HSCg2SFRAVBz2J0sTmXsBcavKWvkTSYq47.jpg', 13, '2025-10-01', 1, 'Chattogram, Bangladesh', 'ddd', 'MEM612115', 3, 1, NULL, '333ddd', 1, '01818650864', 'yyy642', 'hnu8VD77', 4, '2025-10-11 05:36:36', '2025-10-11 05:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `member_unique_ids`
--

CREATE TABLE `member_unique_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_unique_id` varchar(10) NOT NULL,
  `serial` bigint(20) UNSIGNED DEFAULT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `member_unique_ids`
--

INSERT INTO `member_unique_ids` (`id`, `member_unique_id`, `serial`, `member_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'M-0001', 1, 2, NULL, '2025-10-03 07:19:01', '2025-10-03 07:19:01'),
(2, 'M-0002', 2, 3, NULL, '2025-10-11 04:08:01', '2025-10-11 04:08:01'),
(3, 'M-0003', 3, 4, NULL, '2025-10-11 05:36:36', '2025-10-11 05:36:36');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_rules_table', 1),
(2, '0001_01_01_000000_create_users_table', 1),
(3, '0001_01_01_000001_create_cache_table', 1),
(4, '0001_01_01_000002_create_jobs_table', 1),
(5, '0001_01_01_0003_create_month_table', 1),
(6, '0001_01_02_000002_create_ssc_passing_years_table', 1),
(7, '0001_01_03_000002_create_ssc_sessions_table', 1),
(8, '2024_07_18_000000_create_semesters_table', 1),
(9, '2025_07_13_163327_create_income_heads_table', 1),
(10, '2025_07_13_163334_create_expense_heads_table', 1),
(11, '2025_07_13_163341_create_fee_heads_table', 1),
(12, '2025_07_16_005326_create_technology_table', 1),
(13, '2025_07_16_005339_create_nationality_table', 1),
(14, '2025_07_16_005347_create_shift_table', 1),
(15, '2025_07_16_005401_create_religion_table', 1),
(16, '2025_07_16_005945_create_designation_table', 1),
(17, '2025_07_18_091126_create_board_table', 1),
(18, '2025_07_18_093000_create_academic_years_table', 1),
(19, '2025_07_18_093107_create_student_table', 1),
(20, '2025_07_24_172700_create_app_settings_table', 1),
(22, '2025_08_01_002224_create_payment_method_table', 1),
(23, '2025_08_02_034442_create_fee_collect_table', 1),
(24, '2025_08_11_080552_create_student_unique_id_table', 1),
(25, '2025_08_19_182100_create_permissions_table', 1),
(26, '2025_08_19_182200_create_permission_rule_table', 1),
(27, '2025_08_23_225120_create_expenses_table', 1),
(31, '2025_08_01_000000_create_employees_table', 2),
(32, '2025_08_24_000000_create_teachers_table', 2),
(33, '2025_08_25_052730_create_teacher_unique_id_table', 2),
(34, '2025_08_27_014333_create_employee_unique_id_table', 2),
(35, '2025_09_06_010931_add_performance_indexes', 3),
(36, '2025_09_06_054507_create_student_fee_summaries_table', 4),
(37, '2025_09_06_072924_create_student_monthly_fees_table', 4),
(39, '2025_09_07_010928_create_fee_settings_table', 5),
(40, '2025_09_07_010730_create_monthly_fee_payments_table', 6),
(41, '2025_09_07_020648_add_crud_fields_to_fee_settings_table', 7),
(42, '2025_09_07_024111_add_year_to_fee_collects_table', 8),
(43, '2025_09_11_071647_add_fine_fields_to_fee_collects_table', 9),
(44, '2025_09_15_024113_add_status_to_students_table', 10),
(45, '2025_09_15_024302_add_status_change_fields_to_students_table', 11),
(46, '2025_09_17_015700_add_promotion_fields_to_students_table', 12),
(47, '2025_09_17_015742_create_student_promotions_table', 13),
(48, '2025_09_17_023215_create_years_table', 14),
(49, '2025_09_17_023741_add_year_id_to_students_table', 15),
(50, '2025_09_19_112135_add_year_columns_to_student_promotions_table', 16),
(51, '2025_01_15_000000_create_course_categories_table', 17),
(52, '2025_09_22_002932_create_system_settings_table', 18),
(53, '2025_09_12_013823_create_branches_table', 19),
(54, '2025_09_12_040143_create_relations_table', 19),
(55, '2025_09_12_040941_create_members_table', 20),
(56, '2025_09_12_062733_create_member_unique_id_table', 20),
(57, '2025_10_03_114620_update_designation_type_column', 21),
(58, '2025_09_12_045403_update_members_table_nominee_relation_to_foreign_key', 22),
(59, '2025_10_03_130807_fix_members_table_relation_id', 23),
(60, '2025_11_23_165352_create_investment_types_table', 24),
(61, '2025_11_24_115709_create_investment_account_numbers_table', 25),
(62, '2025_09_21_034328_create_investments_table', 26),
(63, '2025_09_21_034337_create_ledger_entries_table', 27),
(64, '2025_09_21_063501_update_ledger_entries_for_entity_type', 28),
(66, '2025_11_24_121609_remove_investment_id_from_ledger_entries_table', 29),
(67, '2025_11_25_022054_add_discount_to_investment_installments_table', 30),
(68, '2025_12_17_161919_add_payment_fields_to_investment_installments_table', 31);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_fee_payments`
--

CREATE TABLE `monthly_fee_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `month` int(11) NOT NULL COMMENT 'Month number (1-12)',
  `year` int(11) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `fine_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `due_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `is_overdue` tinyint(1) NOT NULL DEFAULT 0,
  `days_overdue` int(11) NOT NULL DEFAULT 0,
  `fee_collect_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `months`
--

CREATE TABLE `months` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `month_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `months`
--

INSERT INTO `months` (`id`, `user_id`, `month_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'January', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(2, 1, 'February', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(3, 1, 'March', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(4, 1, 'April', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(5, 1, 'May', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(6, 1, 'June', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(7, 1, 'July', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(8, 1, 'August', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(9, 1, 'September', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(10, 1, 'October', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(11, 1, 'November', '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(12, 1, 'December', '2025-08-26 11:01:29', '2025-08-26 11:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `nationalities`
--

CREATE TABLE `nationalities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nationality_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nationalities`
--

INSERT INTO `nationalities` (`id`, `nationality_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Bangladeshi', NULL, '2025-08-27 10:45:45', '2025-08-27 10:45:45'),
(3, 'USAian', NULL, '2025-10-03 03:28:20', '2025-10-03 03:28:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `payment_method_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Rocket-01818650864', NULL, '2025-08-27 19:13:38', '2025-08-27 19:13:38'),
(2, 'bKash-01818650864', NULL, '2025-08-27 19:13:43', '2025-08-27 19:13:43'),
(3, 'IBBL-01818650864', NULL, '2025-08-27 19:13:47', '2025-08-27 19:13:47'),
(5, 'Cash On', NULL, '2025-10-03 05:57:01', '2025-10-03 05:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'employee-add', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(2, 'employee-edit', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(3, 'employee-view', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(4, 'employee-delete', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(5, 'fee-collect-add', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(6, 'rule-add', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(7, 'rule-edit', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(8, 'rule-delete', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(9, 'expense-add', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(10, 'expense-view', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(11, 'expense-edit', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(12, 'expense-delete', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(13, 'member-add', 1, '2025-09-21 20:10:15', '2025-09-21 20:10:15'),
(14, 'member-edit', 1, '2025-09-21 20:10:16', '2025-09-21 20:10:16'),
(15, 'member-view', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(16, 'member-delete', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(17, 'fee-summary-view', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(18, 'my-collection-report-view', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(19, 'settings-view', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(20, 'deposit-add', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(21, 'deposit-view', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(22, 'deposit-edit', 1, '2025-09-21 20:10:17', '2025-09-21 20:10:17'),
(23, 'deposit-delete', 1, '2025-09-21 20:10:18', '2025-09-21 20:10:18'),
(24, 'deposit-import', 1, '2025-09-21 20:10:18', '2025-09-21 20:10:18'),
(25, 'deposit-reports', 1, '2025-09-21 20:10:18', '2025-09-21 20:10:18'),
(26, 'deposit-ledger-add', 1, '2025-09-21 20:10:18', '2025-09-21 20:10:18'),
(27, 'deposit-ledger-view', 1, '2025-09-21 20:10:18', '2025-09-21 20:10:18'),
(28, 'deposit-ledger-edit', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(29, 'deposit-ledger-delete', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(30, 'deposit-ledger-import', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(31, 'deposit-ledger-reports', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(32, 'investment-add', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(33, 'investment-view', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(34, 'investment-edit', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(35, 'investment-delete', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(36, 'investment-import', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(37, 'investment-reports', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(38, 'investment-ledger-add', 1, '2025-09-21 20:10:19', '2025-09-21 20:10:19'),
(39, 'investment-ledger-view', 1, '2025-09-21 20:10:20', '2025-09-21 20:10:20'),
(40, 'investment-ledger-edit', 1, '2025-09-21 20:10:20', '2025-09-21 20:10:20'),
(41, 'investment-ledger-delete', 1, '2025-09-21 20:10:20', '2025-09-21 20:10:20'),
(42, 'investment-ledger-import', 1, '2025-09-21 20:10:20', '2025-09-21 20:10:20'),
(43, 'investment-ledger-reports', 1, '2025-09-21 20:10:21', '2025-09-21 20:10:21'),
(44, 'investment-collection-add', 1, '2025-12-17 17:11:37', NULL),
(45, 'investment-collection-view', 1, '2025-12-18 03:20:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_rules`
--

CREATE TABLE `permission_rules` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `rule_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_rules`
--

INSERT INTO `permission_rules` (`permission_id`, `rule_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-09-21 20:10:56', '2025-09-21 20:10:56'),
(2, 1, 1, '2025-09-21 20:10:56', '2025-09-21 20:10:56'),
(3, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(4, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(5, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(6, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(7, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(8, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(9, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(10, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(11, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(12, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(13, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(14, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(15, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(16, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(17, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(18, 1, 1, '2025-09-21 20:10:57', '2025-09-21 20:10:57'),
(19, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(20, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(21, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(22, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(23, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(24, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(25, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(26, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(27, 1, 1, '2025-09-21 20:10:58', '2025-09-21 20:10:58'),
(28, 1, 1, '2025-09-21 20:10:59', '2025-09-21 20:10:59'),
(29, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(30, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(31, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(32, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(33, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(34, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(35, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(36, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(37, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(38, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(39, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(40, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(41, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(42, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(43, 1, 1, '2025-09-21 20:11:00', '2025-09-21 20:11:00'),
(44, 1, 1, '2025-12-17 17:13:22', '2025-12-17 17:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `relations`
--

CREATE TABLE `relations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `relation_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `relations`
--

INSERT INTO `relations` (`id`, `relation_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Brother', 1, '2025-09-21 20:19:50', '2025-09-21 20:19:50'),
(2, 'Wife', 1, '2025-09-21 20:19:59', '2025-09-21 20:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `religions`
--

CREATE TABLE `religions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `religion_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `religions`
--

INSERT INTO `religions` (`id`, `religion_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Islam', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24'),
(2, 'Christianity', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24'),
(3, 'Hinduism', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24'),
(4, 'Buddhism', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24'),
(5, 'Sikhism', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24'),
(6, 'Judaism', 1, '2025-08-26 11:01:24', '2025-08-26 11:01:24');

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rules`
--

INSERT INTO `rules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', '2025-08-26 11:01:20', '2025-08-26 11:01:20'),
(2, 'Admin', '2025-08-26 11:01:20', '2025-08-26 11:01:20'),
(3, 'Accountant', '2025-08-26 11:01:20', '2025-08-26 11:01:20'),
(4, 'Employee', '2025-08-26 11:01:20', '2025-08-26 11:01:20'),
(5, 'Teacher', '2025-08-26 11:01:20', '2025-08-26 11:01:20'),
(6, 'Student', '2025-08-26 11:01:20', '2025-08-26 11:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `semester_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `semester_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '1st Semester', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(2, '2nd Semester', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(3, '3rd Semester', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(4, '4th Semester', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(5, '5th Semester', 1, '2025-08-26 11:01:28', '2025-08-26 11:01:28'),
(6, '6th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(7, '7th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(8, '8th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(9, '9th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(10, '10th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(11, '11th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29'),
(12, '12th Semester', 1, '2025-08-26 11:01:29', '2025-08-26 11:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('8dHJ6sQFVL3fS1DcFW2GgdZ8xD4iRkf1wrNtBCB3', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZERySE45VVUyajFnQVlNeDA2aHJxRklVUEJvV1h4eWltR0kwUDh6USI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Qvb3N3ZiI7fX0=', 1772758738),
('kFp2ovpfItfbY4fEVeAP1NdTyyXgWZkkyou01dwk', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZWluMWl6VGJlZHZEWlpRSGZFS1k3RThwU2FJMFg4dnYxNFFOalgyeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9sb2NhbGhvc3Qvb3N3Zi9sYXlvdXRzL3ZlcnRpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1766028711);

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shift_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `shift_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'First Shift', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(2, 'Second Shift', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(3, 'Day Shift', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(4, 'Night Shift', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25');

-- --------------------------------------------------------

--
-- Table structure for table `ssc_passing_sessions`
--

CREATE TABLE `ssc_passing_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ssc_passing_sessions`
--

INSERT INTO `ssc_passing_sessions` (`id`, `session_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '2006-2007', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(2, '2007-2008', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(3, '2008-2009', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(4, '2009-2010', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(5, '2010-2011', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(6, '2011-2012', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(7, '2012-2013', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(8, '2013-2014', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(9, '2014-2015', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(10, '2015-2016', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(11, '2016-2017', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(12, '2017-2018', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(13, '2018-2019', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(14, '2019-2020', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(15, '2020-2021', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(16, '2021-2022', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(17, '2022-2023', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(18, '2023-2024', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(19, '2024-2025', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26');

-- --------------------------------------------------------

--
-- Table structure for table `ssc_passing_years`
--

CREATE TABLE `ssc_passing_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `passing_year_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ssc_passing_years`
--

INSERT INTO `ssc_passing_years` (`id`, `passing_year_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '2025', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(2, '2024', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(3, '2023', 1, '2025-08-26 11:01:25', '2025-08-26 11:01:25'),
(4, '2022', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(5, '2021', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(6, '2020', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(7, '2019', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(8, '2018', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(9, '2017', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(10, '2016', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(11, '2015', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(12, '2014', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(13, '2013', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(14, '2012', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(15, '2011', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(16, '2010', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(17, '2009', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(18, '2008', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(19, '2007', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26'),
(20, '2006', 1, '2025-08-26 11:01:26', '2025-08-26 11:01:26');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name_in_banglai` varchar(255) DEFAULT NULL,
  `full_name_in_english_block_letter` varchar(255) DEFAULT NULL,
  `father_name_in_banglai` varchar(255) DEFAULT NULL,
  `father_name_in_english_block_letter` varchar(255) DEFAULT NULL,
  `mother_name_in_banglai` varchar(255) DEFAULT NULL,
  `mother_name_in_english_block_letter` varchar(255) DEFAULT NULL,
  `guardian_name_absence_of_father` varchar(255) DEFAULT NULL,
  `personal_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(255) DEFAULT NULL,
  `present_address` varchar(255) DEFAULT NULL,
  `permanent_address` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended','admission_cancelled') NOT NULL DEFAULT 'active',
  `status_change_reason` text DEFAULT NULL,
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `is_eligible_for_promotion` tinyint(1) NOT NULL DEFAULT 1,
  `is_promoted` tinyint(1) NOT NULL DEFAULT 0,
  `last_promotion_date` timestamp NULL DEFAULT NULL,
  `promotion_notes` text DEFAULT NULL,
  `promoted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `student_unique_id` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `ssc_or_equivalent_institute_name` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_institute_address` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_number_potro` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_roll_number` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_registration_number` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_gpa` varchar(255) DEFAULT NULL,
  `last_institute_testimonial` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `applicant_declaration` varchar(255) DEFAULT NULL,
  `nationality_id` bigint(20) UNSIGNED DEFAULT NULL,
  `religion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `board_id` bigint(20) UNSIGNED DEFAULT NULL,
  `technology_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ssc_or_equivalent_session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ssc_or_equivalent_passing_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name_in_banglai`, `full_name_in_english_block_letter`, `father_name_in_banglai`, `father_name_in_english_block_letter`, `mother_name_in_banglai`, `mother_name_in_english_block_letter`, `guardian_name_absence_of_father`, `personal_number`, `email`, `guardian_phone`, `present_address`, `permanent_address`, `gender`, `status`, `status_change_reason`, `status_changed_at`, `is_eligible_for_promotion`, `is_promoted`, `last_promotion_date`, `promotion_notes`, `promoted_by`, `student_unique_id`, `date_of_birth`, `ssc_or_equivalent_institute_name`, `ssc_or_equivalent_institute_address`, `ssc_or_equivalent_number_potro`, `ssc_or_equivalent_roll_number`, `ssc_or_equivalent_registration_number`, `ssc_or_equivalent_gpa`, `last_institute_testimonial`, `picture`, `applicant_declaration`, `nationality_id`, `religion_id`, `board_id`, `technology_id`, `shift_id`, `academic_year_id`, `semester_id`, `year_id`, `ssc_or_equivalent_session_id`, `ssc_or_equivalent_passing_year_id`, `created_at`, `updated_at`, `user_id`) VALUES
(19, 'Saiful Islam', 'Saiful Islam', 'dfd', 'fdf', 'Kamrun Nesa', 'Kamrun Nesa', 'fdf', '01818650864', 'saifuldev2011@gmail.com', '01818650863', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'male', 'active', NULL, NULL, 1, 1, '2025-09-19 04:23:46', 'Testing bulk promotion functionality', NULL, 'S-0013', '2025-09-19', 'fdfd', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'vvvvvvvvvv', '3', '3', '3', NULL, '20250919032326.png', NULL, 1, 1, 1, 1, 1, 1, 4, 1, 1, 1, '2025-09-18 21:23:26', '2025-09-19 05:41:13', NULL),
(20, 'Saiful Islamxx', 'Saiful Islam xx', 'dfd', 'dddd', 'Kamrun Nesa', 'Kamrun Nesa', 'fdf', '01818650863', 'saifuld3ev2011@gmail.com', '01818650864', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'male', 'active', NULL, NULL, 1, 0, NULL, NULL, NULL, 'S-0014', '2025-09-17', '333', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', '33', '33', '3', '3', NULL, '20250919103750.png', NULL, 1, 1, 1, 1, 1, 1, 4, 2, 1, 1, '2025-09-19 04:37:50', '2025-09-19 05:41:13', NULL),
(21, 'Saiful Islam ddd', 'Saiful Islam ddd', 'dfdSaiful Islam ddd', 'dddd', 'Kamrun Nesa', 'dddddddddddddd', 'ddd', '01818650867', 'saifuld4ev2011@gmail.com', '01818650864', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'male', 'active', NULL, NULL, 1, 0, NULL, NULL, NULL, 'S-0014', '2025-09-19', 'ffffffffff', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'vvvvvvvvvv', '3', '3', '3', NULL, '20250919103953.jpg', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-09-19 04:39:53', '2025-09-19 04:39:53', NULL),
(22, 'Karim Uddin', 'Saiful Islam', 'Karim Uddin 33', 'dddd', 'Kamrun Nesa 33', 'fffffffff 333', NULL, '01818654212', 'saifulde1v2011@gmail.com', '01818650864', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot\r\nGazipur Sadar', 'male', 'active', NULL, NULL, 1, 0, NULL, NULL, NULL, 'S-0015', '2025-09-19', 'fdfd', NULL, NULL, '33333333', '32', '3', NULL, '20250919105707.jpg', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-09-19 04:57:07', '2025-09-19 04:57:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_fee_summaries`
--

CREATE TABLE `student_fee_summaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_fees_paid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`semester_fees_paid`)),
  `total_semester_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_semester_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_fees_paid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`monthly_fees_paid`)),
  `total_monthly_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_monthly_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_fees` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_due` decimal(10,2) NOT NULL DEFAULT 0.00,
  `all_semester_fees_paid` tinyint(1) NOT NULL DEFAULT 0,
  `all_monthly_fees_paid` tinyint(1) NOT NULL DEFAULT 0,
  `all_fees_paid` tinyint(1) NOT NULL DEFAULT 0,
  `semesters_completed` int(11) NOT NULL DEFAULT 0,
  `months_completed` int(11) NOT NULL DEFAULT 0,
  `total_semesters` int(11) NOT NULL DEFAULT 8,
  `total_months` int(11) NOT NULL DEFAULT 48,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_fee_summaries`
--

INSERT INTO `student_fee_summaries` (`id`, `student_id`, `academic_year_id`, `semester_fees_paid`, `total_semester_fees`, `paid_semester_fees`, `monthly_fees_paid`, `total_monthly_fees`, `paid_monthly_fees`, `total_fees`, `total_paid`, `total_due`, `all_semester_fees_paid`, `all_monthly_fees_paid`, `all_fees_paid`, `semesters_completed`, `months_completed`, `total_semesters`, `total_months`, `created_at`, `updated_at`) VALUES
(9, 19, 1, '[1]', 66711.00, 5000.00, '[]', 576000.00, 0.00, 642711.00, 5000.00, 637711.00, 0, 0, 0, 1, 0, 8, 48, '2025-09-18 21:23:54', '2025-09-19 03:47:28'),
(10, 19, 2, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(11, 19, 3, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(12, 19, 4, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(13, 19, 5, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(14, 19, 6, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(15, 19, 7, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(16, 19, 8, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(17, 19, 9, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(18, 19, 10, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(19, 19, 11, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(20, 19, 12, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(21, 19, 13, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(22, 19, 14, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(23, 19, 15, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:40'),
(24, 19, 16, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:40', '2025-09-18 21:28:41'),
(25, 19, 17, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:41', '2025-09-18 21:28:41'),
(26, 19, 18, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:41', '2025-09-18 21:28:41'),
(27, 19, 19, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:41', '2025-09-18 21:28:41'),
(28, 19, 20, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:41', '2025-09-18 21:28:41'),
(29, 19, 21, '[]', 66711.00, 0.00, '[]', 576000.00, 0.00, 642711.00, 0.00, 642711.00, 0, 0, 0, 0, 0, 8, 48, '2025-09-18 21:28:41', '2025-09-18 21:28:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_monthly_fees`
--

CREATE TABLE `student_monthly_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `month_id` bigint(20) UNSIGNED NOT NULL,
  `fee_collect_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_monthly_fees`
--

INSERT INTO `student_monthly_fees` (`id`, `student_id`, `academic_year_id`, `month_id`, `fee_collect_id`, `amount`, `payment_date`, `is_paid`, `notes`, `created_at`, `updated_at`) VALUES
(1, 15, 1, 2, 7, 1000.00, '2025-09-07', 1, 'Paid via fee collection #7', '2025-09-06 20:54:28', '2025-09-06 20:54:28'),
(2, 2, 1, 9, 8, 1000.00, '2025-09-11', 1, 'Paid via fee collection #8', '2025-09-11 00:54:29', '2025-09-11 00:54:29'),
(3, 4, 1, 9, 9, 1000.00, '2025-09-11', 1, 'Paid via fee collection #9', '2025-09-11 00:58:55', '2025-09-11 00:58:55'),
(4, 6, 1, 1, 10, 1000.00, '2025-09-11', 1, 'Paid via fee collection #10', '2025-09-11 03:27:09', '2025-09-11 03:27:09'),
(5, 6, 1, 9, 10, 1000.00, '2025-09-11', 1, 'Paid via fee collection #10', '2025-09-11 03:27:09', '2025-09-11 03:27:09'),
(6, 15, 1, 1, 11, 1000.00, '2025-09-11', 1, 'Paid via fee collection #11', '2025-09-11 03:35:13', '2025-09-11 03:35:13'),
(7, 15, 1, 9, 11, 1000.00, '2025-09-11', 1, 'Paid via fee collection #11', '2025-09-11 03:35:14', '2025-09-11 03:35:14'),
(8, 15, 1, 10, 12, 1000.00, '2025-09-11', 1, 'Paid via fee collection #12', '2025-09-11 03:35:59', '2025-09-11 03:35:59'),
(9, 2, 1, 8, 14, 1000.00, '2025-09-11', 1, 'Paid via fee collection #14', '2025-09-11 03:53:38', '2025-09-11 03:53:38'),
(10, 9, 1, 9, 15, 1000.00, '2025-09-11', 1, 'Paid via fee collection #15', '2025-09-11 03:59:55', '2025-09-11 03:59:55'),
(11, 9, 1, 8, 16, 1000.00, '2025-09-11', 1, 'Paid via fee collection #16', '2025-09-11 04:10:29', '2025-09-11 04:10:29'),
(12, 16, 1, 9, 17, 1000.00, '2025-09-15', 1, 'Paid via fee collection #17', '2025-09-14 20:06:48', '2025-09-14 20:06:48'),
(13, 17, 1, 1, 19, 1000.00, '2025-09-18', 1, 'Paid via fee collection #19', '2025-09-18 03:45:58', '2025-09-18 03:45:58'),
(14, 17, 1, 2, 21, 1000.00, '2025-09-19', 1, 'Paid via fee collection #21', '2025-09-18 19:20:02', '2025-09-18 19:20:02'),
(15, 17, 1, 4, 25, 1000.00, '2025-09-19', 1, 'Paid via fee collection #25', '2025-09-18 20:58:47', '2025-09-18 20:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `student_promotions`
--

CREATE TABLE `student_promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `from_academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_academic_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_semester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promotion_type` enum('semester','year','both') NOT NULL DEFAULT 'semester',
  `promotion_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `promotion_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `promoted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `promotion_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_promotions`
--

INSERT INTO `student_promotions` (`id`, `student_id`, `from_academic_year_id`, `to_academic_year_id`, `from_year_id`, `to_year_id`, `from_semester_id`, `to_semester_id`, `promotion_type`, `promotion_status`, `promotion_reason`, `notes`, `promoted_by`, `promotion_date`, `created_at`, `updated_at`) VALUES
(2, 19, 1, 1, NULL, NULL, 1, 2, 'semester', 'approved', 'Test promotion', 'Testing the promotion system', NULL, '2025-09-19 04:23:29', '2025-09-19 04:23:29', '2025-09-19 04:23:29'),
(3, 19, 1, 1, NULL, NULL, 2, 3, 'semester', 'approved', 'Bulk test promotion', 'Testing bulk promotion functionality', NULL, '2025-09-19 04:23:46', '2025-09-19 04:23:46', '2025-09-19 04:23:46'),
(4, 20, 1, 1, 1, 1, 1, 2, 'semester', 'approved', 'Test promotion', 'Testing the promotion system', NULL, '2025-09-19 05:23:06', '2025-09-19 05:23:06', '2025-09-19 05:23:06'),
(5, 20, 1, 1, 1, 2, 2, 3, 'both', 'approved', 'Year promotion test', 'Testing year promotion from semester 2 to 3', NULL, '2025-09-19 05:23:24', '2025-09-19 05:23:24', '2025-09-19 05:23:24'),
(6, 19, 1, 1, 1, 1, 3, 4, 'semester', 'approved', 'Bulk test promotion', 'Testing bulk promotion functionality', NULL, '2025-09-19 05:41:13', '2025-09-19 05:41:13', '2025-09-19 05:41:13'),
(7, 20, 1, 1, 2, 2, 3, 4, 'semester', 'approved', 'Bulk test promotion', 'Testing bulk promotion functionality', NULL, '2025-09-19 05:41:13', '2025-09-19 05:41:13', '2025-09-19 05:41:13');

-- --------------------------------------------------------

--
-- Table structure for table `student_semester_fees`
--

CREATE TABLE `student_semester_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `semester_id` bigint(20) UNSIGNED NOT NULL,
  `fee_collect_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_semester_fees`
--

INSERT INTO `student_semester_fees` (`id`, `student_id`, `academic_year_id`, `semester_id`, `fee_collect_id`, `amount`, `payment_date`, `is_paid`, `notes`, `created_at`, `updated_at`) VALUES
(1, 15, 1, 1, 13, 5000.00, '2025-09-11', 1, 'Paid via fee collection #13', '2025-09-11 03:40:53', '2025-09-11 03:40:53'),
(2, 17, 1, 1, 24, 5000.00, '2025-09-19', 1, 'Paid via fee collection #24', '2025-09-17 23:21:28', '2025-09-18 20:57:14'),
(3, 18, 1, 2, 20, 500.00, '2025-09-19', 1, 'Paid via fee collection #20', '2025-09-18 18:59:11', '2025-09-18 18:59:11'),
(5, 19, 1, 1, 27, 5000.00, '2025-09-19', 1, 'Paid via fee collection #27', '2025-09-19 03:47:27', '2025-09-19 03:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `student_unique_ids`
--

CREATE TABLE `student_unique_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_unique_id` varchar(10) NOT NULL,
  `serial` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_unique_ids`
--

INSERT INTO `student_unique_ids` (`id`, `student_unique_id`, `serial`, `student_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'S-0001', 1, 2, NULL, '2025-08-27 10:53:26', '2025-08-27 10:53:26'),
(2, 'S-0002', 2, 3, NULL, '2025-08-27 18:24:39', '2025-08-27 18:24:39'),
(3, 'S-0003', 3, 4, NULL, '2025-08-28 12:54:19', '2025-08-28 12:54:19'),
(4, 'S-0004', 4, 5, NULL, '2025-09-05 21:40:31', '2025-09-05 21:40:31'),
(5, 'S-0005', 5, 6, NULL, '2025-09-06 06:27:41', '2025-09-06 06:27:41'),
(6, 'S-0006', 6, 7, NULL, '2025-09-06 06:49:12', '2025-09-06 06:49:12'),
(7, 'S-0007', 7, 8, NULL, '2025-09-06 09:31:11', '2025-09-06 09:31:11'),
(8, 'S-0008', 8, 9, NULL, '2025-09-06 09:38:13', '2025-09-06 09:38:13'),
(9, 'S-0009', 9, 15, NULL, '2025-09-06 19:02:49', '2025-09-06 19:02:49'),
(10, 'S-0010', 10, 16, NULL, '2025-09-14 20:04:40', '2025-09-14 20:04:40'),
(11, 'S-0011', 11, 17, NULL, '2025-09-17 23:16:10', '2025-09-17 23:16:10'),
(12, 'S-0012', 12, 18, NULL, '2025-09-18 18:43:22', '2025-09-18 18:43:22'),
(13, 'S-0013', 13, 19, NULL, '2025-09-18 21:23:26', '2025-09-18 21:23:26'),
(14, 'S-0014', 14, 20, NULL, '2025-09-19 04:37:50', '2025-09-19 04:37:50'),
(16, 'S-0015', 15, 22, NULL, '2025-09-19 04:57:07', '2025-09-19 04:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `setting_type` enum('text','number','boolean','json','email','url') NOT NULL DEFAULT 'text',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_active`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Polytechnic Management System', 'text', 'The name of the application', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(2, 'max_upload_size', '5242880', 'number', 'Maximum file upload size in bytes (5MB)', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(3, 'maintenance_mode', 'false', 'boolean', 'Enable or disable maintenance mode', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(4, 'admin_email', 'admin@polytechnic.edu', 'email', 'Administrator email address', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(5, 'website_url', 'https://polytechnic.edu', 'url', 'Official website URL', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(6, 'notification_settings', '{\"email\": true, \"sms\": false, \"push\": true}', 'json', 'Notification preferences configuration', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(7, 'session_timeout', '3600', 'number', 'Session timeout in seconds (1 hour)', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52'),
(8, 'enable_registration', 'true', 'boolean', 'Allow new user registrations', 1, 1, '2025-09-21 18:30:52', '2025-09-21 18:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `teacher_unique_id` varchar(255) DEFAULT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nid` varchar(255) DEFAULT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `nid_picture` varchar(255) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `basic_salary` varchar(255) DEFAULT NULL,
  `house_rent` varchar(255) DEFAULT NULL,
  `medical_allowance` varchar(255) DEFAULT NULL,
  `other_allowance` varchar(255) DEFAULT NULL,
  `gross_salary` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_group` varchar(255) DEFAULT NULL,
  `ssc_or_equivalent_gpa` varchar(255) DEFAULT NULL,
  `hsc_or_equivalent_group` varchar(255) DEFAULT NULL,
  `hsc_or_equivalent_gpa` varchar(255) DEFAULT NULL,
  `bachelor_or_equivalent_group` varchar(255) DEFAULT NULL,
  `bachelor_or_equivalent_gpa` varchar(255) DEFAULT NULL,
  `master_or_equivalent_group` varchar(255) DEFAULT NULL,
  `master_or_equivalent_gpa` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `religion_id` bigint(20) UNSIGNED NOT NULL,
  `designation_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `teacher_name`, `teacher_unique_id`, `father_name`, `mother_name`, `gender`, `mobile`, `email`, `nid`, `present_address`, `permanent_address`, `picture`, `nid_picture`, `joining_date`, `basic_salary`, `house_rent`, `medical_allowance`, `other_allowance`, `gross_salary`, `ssc_or_equivalent_group`, `ssc_or_equivalent_gpa`, `hsc_or_equivalent_group`, `hsc_or_equivalent_gpa`, `bachelor_or_equivalent_group`, `bachelor_or_equivalent_gpa`, `master_or_equivalent_group`, `master_or_equivalent_gpa`, `user_id`, `religion_id`, `designation_id`, `created_at`, `updated_at`) VALUES
(1, 'fdfdf', 'T-0001', 'fdfd', 'dd', 'male', '+8801818650864', 'saifuldev2011@gmail.com', '3333333', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', '1756312758_GzM0yL3awAEpwDd.jpeg', '1756312758_GzM0yL3awAEpwDd.jpeg', '2025-08-27', '333', '33', '33', '33', '432', 'Science', '3', 'Science', '3', 'TE', '3', 'ARC', '3', 1, 1, 1, '2025-08-27 10:39:18', '2025-08-27 10:39:18'),
(2, 'fdfdf', 'T-0002', 'fdfd', 'dd', 'male', '+8801818650866', 'saifuldev2011@gmail.com', '3333333', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', '1757121065_logo.jpg', '1757121065_logo.png', '2025-09-12', '100', '100', '100', '100', '400', 'Arts', '10', 'CMT', '10', 'BBA', '10', 'Commerce', '100', 1, 1, 5, '2025-09-05 19:11:05', '2025-09-05 19:11:05'),
(3, 'fdfdf', 'T-0003', 'fdfd', 'dfdfd', 'male', '+8801818650868', 'saifuldev20152@gmail.com', '3333333', 'House/Holding: 447/4gh, village/Road: Simultoli Housing, Sangibag, cot', 'Gazipur Sadar', '1757121756_logo.png', '1757121756_logo.png', '2025-09-19', '100', '100', '100', '100', '400', 'Science', '10', 'Science', '10', 'CE', '10', 'Commerce', '10', 1, 1, 1, '2025-09-05 19:22:36', '2025-09-05 19:22:36');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_unique_ids`
--

CREATE TABLE `teacher_unique_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_unique_id` varchar(10) NOT NULL,
  `serial` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_unique_ids`
--

INSERT INTO `teacher_unique_ids` (`id`, `teacher_unique_id`, `serial`, `teacher_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'T-0001', 1, 1, NULL, '2025-08-27 10:39:18', '2025-08-27 10:39:18'),
(2, 'T-0002', 2, 2, NULL, '2025-09-05 19:11:05', '2025-09-05 19:11:05'),
(3, 'T-0003', 3, 3, NULL, '2025-09-05 19:22:37', '2025-09-05 19:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `technologies`
--

CREATE TABLE `technologies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `technology_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technologies`
--

INSERT INTO `technologies` (`id`, `technology_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'CMT', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(2, 'CCE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(3, 'ET', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(4, 'CE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(5, 'ME', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(6, 'PT', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(7, 'Architecture', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(8, 'TE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(9, 'IPE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(10, 'CSE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(11, 'EEE', 1, '2025-08-26 11:01:27', '2025-08-26 11:01:27'),
(13, 'ss', NULL, '2025-09-16 20:31:22', '2025-09-16 20:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `rule_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `profile_picture`, `email`, `email_verified_at`, `password`, `rule_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', NULL, 'test@example.com', '2025-08-26 11:01:22', '$2y$12$cluFTO1xJMoZUKzFyFg89uQTacaXcGSemwV825TCUCA32MPJ/AAc.', 1, 'knX8z2cKTgKkQlWyPVhqTGgKiR16gqjsJnHR6s0LRsDfR9658vWtCvJFufMF', '2025-08-26 11:01:23', '2025-08-26 11:01:23'),
(2, 'saiful islam', NULL, 'saifuldev2011@gmail.com', NULL, '$2y$12$0bwSSI1/VUsHax1TxYxb7.z7wo9yO/i9wQ3I/hlghVTCpCv1sBIG6', 2, NULL, '2025-10-03 07:19:01', '2025-10-03 07:19:01'),
(3, 'xx', NULL, 'sa3ifuldev2011@gmail.com', NULL, '$2y$12$LYb25q.NVjzxvmo0FohBv.eiY6bQ.RqwLYZnbLbfaN2sbJKYyH0SC', 2, NULL, '2025-10-11 04:08:01', '2025-10-11 04:08:01'),
(4, 'yyy', NULL, 'saifulde3v2011@gmail.com', NULL, '$2y$12$aLQmt738rgoYtTfcYmhaXeZnRRyq3ALD0YkMA7K8JPy.6rWC0iZZ6', 2, NULL, '2025-10-11 05:36:38', '2025-10-11 05:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

CREATE TABLE `years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year_name` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `years`
--

INSERT INTO `years` (`id`, `year_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '1st year', 1, '2025-09-16 20:33:35', '2025-09-16 20:33:35'),
(2, '2nd year', 1, '2025-09-16 20:33:36', '2025-09-16 20:33:36'),
(3, '3rd year', 1, '2025-09-16 20:33:36', '2025-09-16 20:33:36'),
(4, '4th year', 1, '2025-09-16 20:33:36', '2025-09-16 20:33:36'),
(5, '5th year', 1, '2025-09-16 20:33:36', '2025-09-16 20:33:36'),
(6, '6th year', 1, '2025-09-16 20:35:08', '2025-09-16 20:35:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academic_years_user_id_foreign` (`user_id`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `app_settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `boards_user_id_foreign` (`user_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branches_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_categories_name_unique` (`name`),
  ADD KEY `course_categories_user_id_foreign` (`user_id`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `designations_user_id_foreign` (`user_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_unique_id_unique` (`employee_unique_id`),
  ADD UNIQUE KEY `employees_mobile_unique` (`mobile`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD UNIQUE KEY `employees_nid_unique` (`nid`),
  ADD KEY `employees_religion_id_foreign` (`religion_id`),
  ADD KEY `employees_designation_id_foreign` (`designation_id`),
  ADD KEY `employees_user_id_foreign` (`user_id`);

--
-- Indexes for table `employee_unique_ids`
--
ALTER TABLE `employee_unique_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_unique_ids_employee_unique_id_unique` (`employee_unique_id`),
  ADD KEY `employee_unique_ids_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_unique_ids_user_id_foreign` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_expense_head_id_index` (`expense_head_id`),
  ADD KEY `expenses_expense_date_index` (`expense_date`),
  ADD KEY `expenses_amount_index` (`amount`),
  ADD KEY `expenses_created_at_index` (`created_at`);

--
-- Indexes for table `expense_heads`
--
ALTER TABLE `expense_heads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_heads_name_unique` (`name`),
  ADD KEY `expense_heads_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_collects`
--
ALTER TABLE `fee_collects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_collects_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `fee_collects_semester_id_foreign` (`semester_id`),
  ADD KEY `fee_collects_student_id_foreign` (`student_id`),
  ADD KEY `fee_collects_user_id_foreign` (`user_id`),
  ADD KEY `fee_collects_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `fee_heads`
--
ALTER TABLE `fee_heads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fee_heads_name_semester_id_unique` (`name`,`semester_id`),
  ADD KEY `fee_heads_semester_id_foreign` (`semester_id`),
  ADD KEY `fee_heads_month_id_foreign` (`month_id`),
  ADD KEY `fee_heads_user_id_foreign` (`user_id`);

--
-- Indexes for table `fee_settings`
--
ALTER TABLE `fee_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_settings_user_id_foreign` (`user_id`);

--
-- Indexes for table `income_heads`
--
ALTER TABLE `income_heads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `income_heads_name_unique` (`name`),
  ADD KEY `income_heads_user_id_foreign` (`user_id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investments_member_id_status_index` (`member_id`,`status`),
  ADD KEY `investments_expiry_date_index` (`expiry_date`);

--
-- Indexes for table `investment_accounts`
--
ALTER TABLE `investment_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investment_account_numbers`
--
ALTER TABLE `investment_account_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `investment_account_numbers_account_number_unique` (`account_number`),
  ADD KEY `investment_account_numbers_account_number_index` (`account_number`),
  ADD KEY `investment_account_numbers_serial_index` (`serial`),
  ADD KEY `investment_account_numbers_year_serial_index` (`year`,`serial`),
  ADD KEY `investment_account_numbers_user_id_index` (`user_id`),
  ADD KEY `investment_account_numbers_investment_account_id_index` (`investment_account_id`);

--
-- Indexes for table `investment_installments`
--
ALTER TABLE `investment_installments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `investment_installments_receipt_number_unique` (`receipt_number`),
  ADD KEY `investment_installments_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `investment_types`
--
ALTER TABLE `investment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investment_types_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ledger_entries_created_by_foreign` (`created_by`),
  ADD KEY `ledger_entries_investment_id_entry_date_index` (`entry_date`),
  ADD KEY `ledger_entries_entry_date_index` (`entry_date`),
  ADD KEY `ledger_entries_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  ADD KEY `ledger_entries_entity_type_index` (`entity_type`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_email_unique` (`email`),
  ADD UNIQUE KEY `members_nid_number_unique` (`nid_number`),
  ADD UNIQUE KEY `members_unique_id_unique` (`unique_id`),
  ADD UNIQUE KEY `members_temp_username_unique` (`temp_username`),
  ADD KEY `members_designation_id_foreign` (`designation_id`),
  ADD KEY `members_relation_id_foreign` (`relation_id`),
  ADD KEY `members_branch_id_foreign` (`branch_id`),
  ADD KEY `members_introducer_id_foreign` (`introducer_id`),
  ADD KEY `members_religion_id_foreign` (`religion_id`),
  ADD KEY `members_user_id_foreign` (`user_id`),
  ADD KEY `members_nominee_relation_id_foreign` (`nominee_relation_id`);

--
-- Indexes for table `member_unique_ids`
--
ALTER TABLE `member_unique_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_unique_ids_member_unique_id_unique` (`member_unique_id`),
  ADD KEY `member_unique_ids_member_id_foreign` (`member_id`),
  ADD KEY `member_unique_ids_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_fee_payments`
--
ALTER TABLE `monthly_fee_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_fee_payments_unique` (`student_id`,`academic_year_id`,`month`,`year`),
  ADD KEY `monthly_fee_payments_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `monthly_fee_payments_fee_collect_id_foreign` (`fee_collect_id`),
  ADD KEY `monthly_fee_payments_is_paid_due_date_index` (`is_paid`,`due_date`),
  ADD KEY `monthly_fee_payments_is_overdue_due_date_index` (`is_overdue`,`due_date`),
  ADD KEY `monthly_fee_payments_month_year_index` (`month`,`year`);

--
-- Indexes for table `months`
--
ALTER TABLE `months`
  ADD PRIMARY KEY (`id`),
  ADD KEY `months_user_id_foreign` (`user_id`);

--
-- Indexes for table `nationalities`
--
ALTER TABLE `nationalities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nationalities_nationality_name_unique` (`nationality_name`),
  ADD KEY `nationalities_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_methods_user_id_foreign` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD KEY `permissions_user_id_foreign` (`user_id`);

--
-- Indexes for table `permission_rules`
--
ALTER TABLE `permission_rules`
  ADD PRIMARY KEY (`permission_id`,`rule_id`),
  ADD KEY `permission_rules_rule_id_foreign` (`rule_id`),
  ADD KEY `permission_rules_user_id_foreign` (`user_id`);

--
-- Indexes for table `relations`
--
ALTER TABLE `relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `relations_user_id_foreign` (`user_id`);

--
-- Indexes for table `religions`
--
ALTER TABLE `religions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `religions_user_id_foreign` (`user_id`);

--
-- Indexes for table `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rules_name_unique` (`name`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `semesters_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_user_id_foreign` (`user_id`);

--
-- Indexes for table `ssc_passing_sessions`
--
ALTER TABLE `ssc_passing_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ssc_passing_sessions_session_name_unique` (`session_name`),
  ADD KEY `ssc_passing_sessions_user_id_foreign` (`user_id`);

--
-- Indexes for table `ssc_passing_years`
--
ALTER TABLE `ssc_passing_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ssc_passing_years_passing_year_name_unique` (`passing_year_name`),
  ADD KEY `ssc_passing_years_user_id_foreign` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `students_ssc_or_equivalent_session_id_foreign` (`ssc_or_equivalent_session_id`),
  ADD KEY `students_ssc_or_equivalent_passing_year_id_foreign` (`ssc_or_equivalent_passing_year_id`),
  ADD KEY `students_nationality_id_foreign` (`nationality_id`),
  ADD KEY `students_religion_id_foreign` (`religion_id`),
  ADD KEY `students_board_id_foreign` (`board_id`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_academic_year_id_index` (`academic_year_id`),
  ADD KEY `students_semester_id_index` (`semester_id`),
  ADD KEY `students_technology_id_index` (`technology_id`),
  ADD KEY `students_shift_id_index` (`shift_id`),
  ADD KEY `students_student_unique_id_index` (`student_unique_id`),
  ADD KEY `students_personal_number_index` (`personal_number`),
  ADD KEY `students_full_name_in_english_block_letter_index` (`full_name_in_english_block_letter`),
  ADD KEY `students_created_at_index` (`created_at`),
  ADD KEY `students_promoted_by_foreign` (`promoted_by`),
  ADD KEY `students_year_id_foreign` (`year_id`);

--
-- Indexes for table `student_fee_summaries`
--
ALTER TABLE `student_fee_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_fee_summaries_student_id_academic_year_id_unique` (`student_id`,`academic_year_id`),
  ADD KEY `student_fee_summaries_academic_year_id_foreign` (`academic_year_id`);

--
-- Indexes for table `student_monthly_fees`
--
ALTER TABLE `student_monthly_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_monthly_fees_student_id_academic_year_id_month_id_unique` (`student_id`,`academic_year_id`,`month_id`),
  ADD KEY `student_monthly_fees_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `student_monthly_fees_month_id_foreign` (`month_id`),
  ADD KEY `student_monthly_fees_fee_collect_id_foreign` (`fee_collect_id`);

--
-- Indexes for table `student_promotions`
--
ALTER TABLE `student_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_promotions_student_id_foreign` (`student_id`),
  ADD KEY `student_promotions_from_academic_year_id_foreign` (`from_academic_year_id`),
  ADD KEY `student_promotions_to_academic_year_id_foreign` (`to_academic_year_id`),
  ADD KEY `student_promotions_from_semester_id_foreign` (`from_semester_id`),
  ADD KEY `student_promotions_to_semester_id_foreign` (`to_semester_id`),
  ADD KEY `student_promotions_promoted_by_foreign` (`promoted_by`),
  ADD KEY `student_promotions_from_year_id_foreign` (`from_year_id`),
  ADD KEY `student_promotions_to_year_id_foreign` (`to_year_id`);

--
-- Indexes for table `student_semester_fees`
--
ALTER TABLE `student_semester_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_semester_fees_student_id_foreign` (`student_id`),
  ADD KEY `student_semester_fees_academic_year_id_foreign` (`academic_year_id`),
  ADD KEY `student_semester_fees_semester_id_foreign` (`semester_id`),
  ADD KEY `student_semester_fees_fee_collect_id_foreign` (`fee_collect_id`);

--
-- Indexes for table `student_unique_ids`
--
ALTER TABLE `student_unique_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_unique_ids_unique_id_unique` (`student_unique_id`),
  ADD KEY `student_unique_ids_user_id_foreign` (`user_id`),
  ADD KEY `student_unique_ids_student_id_index` (`student_id`),
  ADD KEY `student_unique_ids_serial_index` (`serial`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_setting_key_unique` (`setting_key`),
  ADD KEY `system_settings_user_id_foreign` (`user_id`),
  ADD KEY `system_settings_setting_key_is_active_index` (`setting_key`,`is_active`),
  ADD KEY `system_settings_setting_type_index` (`setting_type`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_teacher_unique_id_unique` (`teacher_unique_id`),
  ADD KEY `teachers_user_id_foreign` (`user_id`),
  ADD KEY `teachers_religion_id_foreign` (`religion_id`),
  ADD KEY `teachers_designation_id_foreign` (`designation_id`);

--
-- Indexes for table `teacher_unique_ids`
--
ALTER TABLE `teacher_unique_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_unique_ids_teacher_unique_id_unique` (`teacher_unique_id`),
  ADD KEY `teacher_unique_ids_teacher_id_foreign` (`teacher_id`),
  ADD KEY `teacher_unique_ids_user_id_foreign` (`user_id`);

--
-- Indexes for table `technologies`
--
ALTER TABLE `technologies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technologies_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_rule_id_foreign` (`rule_id`);

--
-- Indexes for table `years`
--
ALTER TABLE `years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `years_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employee_unique_ids`
--
ALTER TABLE `employee_unique_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expense_heads`
--
ALTER TABLE `expense_heads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_collects`
--
ALTER TABLE `fee_collects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `fee_heads`
--
ALTER TABLE `fee_heads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `fee_settings`
--
ALTER TABLE `fee_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `income_heads`
--
ALTER TABLE `income_heads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `investment_accounts`
--
ALTER TABLE `investment_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investment_account_numbers`
--
ALTER TABLE `investment_account_numbers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `investment_installments`
--
ALTER TABLE `investment_installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `investment_types`
--
ALTER TABLE `investment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `member_unique_ids`
--
ALTER TABLE `member_unique_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `monthly_fee_payments`
--
ALTER TABLE `monthly_fee_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `months`
--
ALTER TABLE `months`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nationalities`
--
ALTER TABLE `nationalities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `relations`
--
ALTER TABLE `relations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `religions`
--
ALTER TABLE `religions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rules`
--
ALTER TABLE `rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ssc_passing_sessions`
--
ALTER TABLE `ssc_passing_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ssc_passing_years`
--
ALTER TABLE `ssc_passing_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `student_fee_summaries`
--
ALTER TABLE `student_fee_summaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `student_monthly_fees`
--
ALTER TABLE `student_monthly_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_promotions`
--
ALTER TABLE `student_promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_semester_fees`
--
ALTER TABLE `student_semester_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_unique_ids`
--
ALTER TABLE `student_unique_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_unique_ids`
--
ALTER TABLE `teacher_unique_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `technologies`
--
ALTER TABLE `technologies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `years`
--
ALTER TABLE `years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD CONSTRAINT `academic_years_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD CONSTRAINT `app_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `boards`
--
ALTER TABLE `boards`
  ADD CONSTRAINT `boards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD CONSTRAINT `course_categories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `designations`
--
ALTER TABLE `designations`
  ADD CONSTRAINT `designations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_unique_ids`
--
ALTER TABLE `employee_unique_ids`
  ADD CONSTRAINT `employee_unique_ids_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_unique_ids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_head_id_foreign` FOREIGN KEY (`expense_head_id`) REFERENCES `expense_heads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_heads`
--
ALTER TABLE `expense_heads`
  ADD CONSTRAINT `expense_heads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_collects`
--
ALTER TABLE `fee_collects`
  ADD CONSTRAINT `fee_collects_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_collects_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_collects_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_collects_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_collects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_heads`
--
ALTER TABLE `fee_heads`
  ADD CONSTRAINT `fee_heads_month_id_foreign` FOREIGN KEY (`month_id`) REFERENCES `months` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_heads_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_heads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_settings`
--
ALTER TABLE `fee_settings`
  ADD CONSTRAINT `fee_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `income_heads`
--
ALTER TABLE `income_heads`
  ADD CONSTRAINT `income_heads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `investments`
--
ALTER TABLE `investments`
  ADD CONSTRAINT `investments_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `investment_account_numbers`
--
ALTER TABLE `investment_account_numbers`
  ADD CONSTRAINT `investment_account_numbers_investment_account_id_foreign` FOREIGN KEY (`investment_account_id`) REFERENCES `investment_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `investment_account_numbers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `investment_installments`
--
ALTER TABLE `investment_installments`
  ADD CONSTRAINT `investment_installments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `investment_types`
--
ALTER TABLE `investment_types`
  ADD CONSTRAINT `investment_types_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ledger_entries`
--
ALTER TABLE `ledger_entries`
  ADD CONSTRAINT `ledger_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_introducer_id_foreign` FOREIGN KEY (`introducer_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `members_nominee_relation_id_foreign` FOREIGN KEY (`nominee_relation_id`) REFERENCES `relations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `members_relation_id_foreign` FOREIGN KEY (`relation_id`) REFERENCES `relations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_religion_id_foreign` FOREIGN KEY (`religion_id`) REFERENCES `religions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_unique_ids`
--
ALTER TABLE `member_unique_ids`
  ADD CONSTRAINT `member_unique_ids_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_unique_ids_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_fee_payments`
--
ALTER TABLE `monthly_fee_payments`
  ADD CONSTRAINT `monthly_fee_payments_academic_year_id_foreign` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monthly_fee_payments_fee_collect_id_foreign` FOREIGN KEY (`fee_collect_id`) REFERENCES `fee_collects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `monthly_fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `months`
--
ALTER TABLE `months`
  ADD CONSTRAINT `months_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nationalities`
--
ALTER TABLE `nationalities`
  ADD CONSTRAINT `nationalities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_rules`
--
ALTER TABLE `permission_rules`
  ADD CONSTRAINT `permission_rules_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_rules_rule_id_foreign` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_rules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `relations`
--
ALTER TABLE `relations`
  ADD CONSTRAINT `relations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `religions`
--
ALTER TABLE `religions`
  ADD CONSTRAINT `religions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `semesters`
--
ALTER TABLE `semesters`
  ADD CONSTRAINT `semesters_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 16, 2026 at 06:48 PM
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
-- Database: `mscpua`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE IF NOT EXISTS `audit_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_timestamp` (`timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `timestamp`) VALUES
(1, NULL, 'login_failed', 'Failed login attempt for: admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:14:07'),
(2, NULL, 'login_failed', 'Failed login attempt for: admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:16:10'),
(3, NULL, 'login_failed', 'Failed login attempt for: admin - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:17:30'),
(4, 2, 'register', 'New user registered: cyrus', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:18:37'),
(5, 2, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:18:54'),
(6, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:19:00'),
(7, 2, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 17:19:45'),
(8, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:01:52'),
(9, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:02:16'),
(10, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:02:49'),
(11, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:02:53'),
(12, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:03:37'),
(13, NULL, 'login_failed', 'Failed login attempt for: cyrus.orji@imopoly.edu.ng - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:04:00'),
(14, NULL, 'login_failed', 'Failed login attempt for: admin - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:04:23'),
(15, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:06:52'),
(16, NULL, 'login_failed', 'Failed login attempt for: cyrus - Password mismatch', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:07:01'),
(17, NULL, 'login_failed', 'Failed login attempt for: cyrus', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:08:39'),
(18, 1, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:10:06'),
(19, 1, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:11:18'),
(20, 2, 'password_reset_request', 'Password reset requested for: cyrus.orji@imopoly.edu.ng', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:13:33'),
(21, 2, 'password_reset_request', 'Password reset requested for: cyrus.orji@imopoly.edu.ng', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:13:56'),
(22, 2, 'password_reset_request', 'Password reset requested for: cyrus.orji@imopoly.edu.ng', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:14:31'),
(23, 2, 'password_reset_request', 'Password reset requested for: cyrus.orji@imopoly.edu.ng', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:17:45'),
(24, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:19:37'),
(25, 2, 'encrypt_text', 'Text encrypted: Emeka', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:20:23'),
(26, 2, 'key_regenerated', 'Encryption key regenerated for user: cyrus', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:24:20'),
(27, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:24:33'),
(28, 2, 'encrypt_text', 'Text encrypted: Testing 2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:25:08'),
(29, 2, 'decrypt', 'Data decrypted: Testing 2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:25:18'),
(30, 2, 'encrypt_file', 'File encrypted: tes67.txt', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:26:28'),
(31, 2, 'decrypt', 'Data decrypted: tes67.txt', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:26:38'),
(32, 2, 'benchmark', 'Performance benchmark run for 1 data sizes', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:35:35'),
(33, 2, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:35:58'),
(34, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:37:11'),
(35, 2, 'encrypt_text', 'Text encrypted: Test 3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:37:33'),
(36, 2, 'encrypt_text', 'Text encrypted: Jude Onitsha', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:37:59'),
(37, 2, 'encrypt_text', 'Text encrypted: Cynthia Imo', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:38:20'),
(38, 2, 'encrypt_text', 'Text encrypted: House rent', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:38:44'),
(39, 2, 'benchmark', 'Performance benchmark run for 1 data sizes', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:40:30'),
(40, 2, 'logout', 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:40:41'),
(41, 2, 'login', 'User logged in successfully', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:128.0) Gecko/20100101 Firefox/128.0', '2026-07-16 18:43:27');

-- --------------------------------------------------------

--
-- Table structure for table `encrypted_data`
--

DROP TABLE IF EXISTS `encrypted_data`;
CREATE TABLE IF NOT EXISTS `encrypted_data` (
  `data_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ciphertext` longtext NOT NULL,
  `iv` varchar(255) NOT NULL,
  `key_version` int NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `data_type` enum('text','file') DEFAULT 'text',
  `encryption_time` float DEFAULT NULL,
  `decryption_time` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`data_id`),
  KEY `idx_user_data` (`user_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `encrypted_data`
--

INSERT INTO `encrypted_data` (`data_id`, `user_id`, `ciphertext`, `iv`, `key_version`, `file_name`, `file_size`, `file_type`, `data_type`, `encryption_time`, `decryption_time`, `created_at`, `updated_at`) VALUES
(1, 2, 'hVLtpkl5X2Q1ycJPzf+aqg==', 'uC2dIbidWpUgrZSXIoprng==', 1, 'Emeka', NULL, NULL, 'text', 0.000589132, NULL, '2026-07-16 18:20:23', NULL),
(2, 2, 'SJ4WwJ4JjUhI3HlCDkmqEA==', 'Km7zWwQLv3Con/zdCieTBA==', 1, 'Testing 2', NULL, NULL, 'text', 0.000272989, 0.14208, '2026-07-16 18:25:08', NULL),
(3, 2, 'HQS+YdOhnrGKPoPxm9kLVOukNtWg/XL0ZYjjKyDjBV4C4ikstfJ0zQtCo154pOyKRjlMMqOEAA/PPP85cSyDVgVYGX9jc+Rd3tax5It0ICblKK04Wf+jbqszc240IijOsu86mOQ6biyHVnfnRciwJhGMqey6suruWIhvlPY8tE/1A4OHeH5WjRqA3dHQZOFIhHXse8usfRTV2wYU9UrG71UrcEDsn6ovuaomO54Pt9o53lOlOlBJIF3wWSlSGeNsG48CWkh5qVTWlkbKQhGwfgE02BN5xRzUSrP1ya3BWkzlfZY6f7pCmWN6Oaz6kf4cU3wiCvDpvfzzXrC778oO/WW+s8h6lEc/ovIgLr0/YNkZjYnC0GIw2kLnW4Un9xzScorMMLnYm8tj2M8BtKETaUm0Osm4u/pp/klY8p21imL1Xp3ZE4iaXUDHioE4e5iS70ejGAqwoQZI2Ajy32iYXJ20yxeJ92MnGTXEZ1eCwNF15RN4PSHEz6R3+W6q58pr2toyMa1fhpLMgZ+E2sLlqwTOFwyMSKZm3PBD+5NXQyrjlH/dVPFZyMJfUWWHlZYeyrjB8Rtl6x4ToVX53jqCNLCcfAy8CX4+lLRm3Whjf5e+wctKM82O1E78xW07GXHKnm8kPfubVuAOXjRgvRPeUA==', 'UGRTh98pi2FQLKLjtyHlhg==', 1, 'tes67.txt', 481, 'text/plain', 'file', 0.0539219, 0.141546, '2026-07-16 18:26:28', NULL),
(4, 2, 'kbqhcSVPvUKTmvT7AF1h3OKfsy1IB7tMd8YziXuGbeg=', '7B/H+cBsGjDi50ZpO27ewA==', 1, 'Test 3', NULL, NULL, 'text', 0.000279188, NULL, '2026-07-16 18:37:33', NULL),
(5, 2, 'opRnP+SrQxbeKTe6DrtGHTtvic4rplLKR22KK/9SvkU=', '9rFTMgK3tacMs+c4FTFWlg==', 1, 'Jude Onitsha', NULL, NULL, 'text', 0.00028491, NULL, '2026-07-16 18:37:59', NULL),
(6, 2, 'VJiP0/Cpv7o1vc3Gn43q4D1OuUxm12cvs4p00c4OOT8=', 'rh6lXLIBymuHfhIyn0w5yQ==', 1, 'Cynthia Imo', NULL, NULL, 'text', 0.000232935, NULL, '2026-07-16 18:38:20', NULL),
(7, 2, 'UbrUJDIC2YsZNw3YhM88A1lzxSwzHSok48kpYCQ6UxQCbT021GxKoU4ak6HeZx9E', 'g9jzWr3rb4EUTmrM4Qqd6A==', 1, 'House rent', NULL, NULL, 'text', 0.000275135, NULL, '2026-07-16 18:38:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `encryption_keys`
--

DROP TABLE IF EXISTS `encryption_keys`;
CREATE TABLE IF NOT EXISTS `encryption_keys` (
  `key_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `key_version` int NOT NULL DEFAULT '1',
  `encrypted_key` text NOT NULL,
  `key_salt` varchar(255) NOT NULL,
  `key_iv` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_used` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `idx_user_keys` (`user_id`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `encryption_keys`
--

INSERT INTO `encryption_keys` (`key_id`, `user_id`, `key_version`, `encrypted_key`, `key_salt`, `key_iv`, `created_at`, `expires_at`, `is_active`, `last_used`) VALUES
(2, 2, 1, 'toAd6GLUALrdt0I5dNZDpL2Uw3EU9XXrWQqzVaGDd0NnxQsOAiR7eV1JVWpUKIKv', 'nvHP8sYa8SHj+lFJa7YfbA==', 'f0cJjX5zGzIXAacfe0taig==', '2026-07-16 18:24:20', '2027-07-16 18:24:20', 1, '2026-07-16 18:26:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`),
  KEY `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `created_at`, `last_login`, `is_active`, `reset_token`, `reset_expires`) VALUES
(1, 'admin', 'admin@pauluniversity.edu.ng', '$2y$12$J.Rs682ixkuMzk/0CrE0WeB.vYuN6H0dNDhOv0h/PkwpFdv9b9Mwu', '2026-07-16 17:13:07', NULL, 1, NULL, NULL),
(2, 'cyrus', 'cyrus.orji@imopoly.edu.ng', '$2y$12$kuJPxBbkMpJ5B5t9gmDRmudsfIZI.i0kqNZZg.My2HbR.hwYOjk1W', '2026-07-16 17:18:36', '2026-07-16 17:19:00', 1, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `encrypted_data`
--
ALTER TABLE `encrypted_data`
  ADD CONSTRAINT `encrypted_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `encryption_keys`
--
ALTER TABLE `encryption_keys`
  ADD CONSTRAINT `encryption_keys_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

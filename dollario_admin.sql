-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2025 at 12:53 PM
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
-- Database: `dollario_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `sub_admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','subadmin') DEFAULT 'admin',
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `email` varchar(250) NOT NULL,
  `avatar` int(11) NOT NULL,
  `referral_code` varchar(50) NOT NULL DEFAULT 'UNIQUE',
  `program_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `role`, `status`, `created_at`, `first_name`, `last_name`, `phone`, `country`, `email`, `avatar`, `referral_code`, `program_status`) VALUES
(1, 'kamlesh', '$2y$10$3B/Bsz.Zs1nPnsASo3F8lOlHRYUIEhDkgL/14l.XNro14bDq3mioS', 'admin', 'active', '2025-04-29 16:15:45', '', '', '', '', '', 0, 'UNIQUE', 1),
(2, '', '$2y$10$x4.LEyh6LdkV5Tin2AtfzugQniow06Ql0U2RXNXvhJmFZWem2.REy', 'admin', 'active', '2025-04-29 16:26:19', 'Kamlesh', 'Kumar', '6376781372', 'india', 'kkjangirnhr@gmail.com', 0, 'UNIQUE', 1),
(3, 'kamleshs', '$2y$10$OJSkVqXiVFXxgJHJDKLNV.x9WG74F4A0onqld6c46XMqjsjFOCQ02', 'admin', 'active', '2025-04-30 14:53:23', 'aarav', 'jangir', '638563284', 'United Kingdom', 'kkjangirnhr@gmail.com', 0, 'UNIQUE', 1),
(4, 'kkkkkk', '$2y$10$5UpWA.3g.tczZx6z28D/8OndTOs3ZiQcfI15f38QmRwFdG26U8vsG', 'admin', 'active', '2025-05-02 11:31:38', 'rahul', 'parja', '433456546656', 'India', 'rrrr@gmail.com', 0, 'UNIQUE', 1),
(5, 'mukeshnimel', '$2y$10$ZidWSxlot6tmAVhaanJZx.PDgUFfs0Lg6V750kBvwLthiMJLIa7Ee', 'admin', 'active', '2025-05-02 11:42:53', 'mukesh', 'nimel', '06376781372', 'India', 'mukesh@gmail.com', 0, 'UNIQUE', 1),
(6, 'mukesh9_x', '$2y$10$5Bb27R93Me586zN0J4MDbuOyOi9zX0Paaug..hZR3nuy1wHq4zZYq', 'admin', 'active', '2025-05-07 07:05:10', 'ankit', 'bihari', '6261986281', 'India', 'ankitpayhe96@gmail.com', 0, 'UNIQUE', 1),
(7, 'adminuser', '$2y$10$W5qYX4GqW3KKS.8lly7cqeNnMNpuqSXn2pvSSxFqLEkdHtu17sznS', 'admin', 'active', '2025-05-14 06:36:22', 'admin', 'user', '435635644675765', 'United States', 'adminuser@gmail.com', 0, 'UNIQUE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `user_id`, `bank_name`, `account_number`, `is_primary`, `added_on`) VALUES
(1, 1, 'kamlesh kumar', '38580245958', 1, '2025-05-15 14:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_ips`
--

CREATE TABLE `blocked_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `blocked_by` int(11) DEFAULT NULL,
  `blocked_at` datetime NOT NULL DEFAULT current_timestamp(),
  `block_until` datetime DEFAULT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('referral','promotional','seasonal','bonus') NOT NULL,
  `status` enum('draft','active','upcoming','expired') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `terms` text NOT NULL,
  `participants` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `name`, `description`, `type`, `status`, `start_date`, `end_date`, `image`, `terms`, `participants`, `created_at`) VALUES
(15, 'Kamlesh Kumar ', 'vvhhfh', 'promotional', 'upcoming', '2025-04-29', '2025-05-20', 'uploads/Screenshot 2025-04-23 234022.png', 'rergetg', 0, '2025-05-01 11:46:16'),
(16, 'kamlesh123hwys', '55555', 'referral', 'active', '2025-05-01', '2025-05-12', '', 'vjvjhfhj', 0, '2025-05-01 11:47:58'),
(17, 'asdxasdad', 'qadadD', 'referral', 'upcoming', '2025-05-01', '2025-05-02', '', 'QdaD', 0, '2025-05-01 12:09:46');

-- --------------------------------------------------------

--
-- Table structure for table `crypto_rates`
--

CREATE TABLE `crypto_rates` (
  `id` int(11) NOT NULL,
  `pair` varchar(10) DEFAULT 'USDT/INR',
  `rate` decimal(18,8) DEFAULT NULL,
  `spread` decimal(5,2) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `template_content` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `base_currency` varchar(3) NOT NULL,
  `target_currency` varchar(3) NOT NULL,
  `rate` decimal(12,6) NOT NULL,
  `margin` decimal(5,2) DEFAULT 0.00,
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_structures`
--

CREATE TABLE `fee_structures` (
  `id` int(11) NOT NULL,
  `fee_type` varchar(50) NOT NULL,
  `fee_name` varchar(100) NOT NULL,
  `calculation_method` enum('percentage','fixed','tiered') NOT NULL,
  `fee_value` decimal(10,2) NOT NULL,
  `min_amount` decimal(15,2) DEFAULT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `applicable_currency` varchar(3) DEFAULT NULL,
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inr_withdrawals`
--

CREATE TABLE `inr_withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `account_details` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inr_withdrawals`
--

INSERT INTO `inr_withdrawals` (`id`, `user_id`, `amount`, `method`, `account_details`, `status`, `requested_at`, `approved_at`) VALUES
(1, 101, 1500.00, 'UPI', 'kamlesh@upi', 'Pending', '2025-05-16 15:00:37', '2025-05-16 15:00:43'),
(2, 102, 3200.00, 'Paytm', '9999999999', 'Approved', '2025-05-16 15:00:37', NULL),
(3, 103, 2200.00, 'Razorpay', 'kamlesh@razorpay', 'Pending', '2025-05-16 15:00:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `interest_rates`
--

CREATE TABLE `interest_rates` (
  `id` int(11) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `rate_type` enum('fixed','variable') NOT NULL,
  `rate_value` decimal(5,2) NOT NULL,
  `min_amount` decimal(15,2) DEFAULT NULL,
  `max_amount` decimal(15,2) DEFAULT NULL,
  `min_term` int(11) DEFAULT NULL COMMENT 'In days',
  `max_term` int(11) DEFAULT NULL COMMENT 'In days',
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_documents`
--

CREATE TABLE `kyc_documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `document_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verifications`
--

CREATE TABLE `kyc_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(100) DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `ip_address`, `user_agent`, `login_time`) VALUES
(1, 6, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-05-07 07:13:30'),
(2, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:36:38'),
(3, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:39:40'),
(4, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:41:36'),
(5, 7, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-14 06:42:03'),
(6, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-16 09:56:35'),
(7, 2, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', '2025-05-20 05:57:35');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('system','promotional','user_alert','update') NOT NULL,
  `priority` enum('normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `message`, `type`, `priority`, `is_read`, `created_at`, `user_id`) VALUES
(2, 'ewgsdgds', 'sgdth', 'promotional', 'high', 1, '2025-05-02 10:55:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rate_history`
--

CREATE TABLE `rate_history` (
  `id` int(11) NOT NULL,
  `rate_type` enum('exchange','interest','fee') NOT NULL,
  `rate_id` int(11) NOT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `changed_by` int(11) NOT NULL,
  `change_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `referee_id` int(11) NOT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `reward_amount` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_bonus`
--

CREATE TABLE `referral_bonus` (
  `id` int(11) NOT NULL,
  `referred_by` int(11) NOT NULL,
  `referred_user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `security_settings`
--

CREATE TABLE `security_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `encryption_status` varchar(100) NOT NULL DEFAULT 'AES-256',
  `ssl_status` varchar(100) NOT NULL DEFAULT 'Active',
  `ip_whitelist_status` varchar(100) NOT NULL DEFAULT 'Enabled',
  `rate_limiting_status` varchar(100) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_group` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_group`, `setting_key`, `setting_value`) VALUES
(1, 'general', 'company_name', 'Dollario'),
(2, 'general', 'company_email', 'kkjangirnhr@gmail.com'),
(3, 'general', 'company_phone', '06376781372'),
(4, 'general', 'company_website', 'https://dollario.com'),
(5, 'general', 'company_address', '123 Business Street, Mumbai, Maharashtra - 400001, India'),
(6, 'general', 'timezone', 'Asia/Kolkata'),
(7, 'general', 'default_language', 'en'),
(8, 'general', 'date_format', 'd-m-Y'),
(9, 'general', 'time_format', '12'),
(10, 'general', 'currency', 'INR'),
(11, 'general', 'maintenance_mode', '1'),
(12, 'general', 'user_registration', '1'),
(13, 'general', 'email_verification', '1'),
(14, 'general', 'kyc_verification', '1'),
(15, 'general', 'session_timeout', '80'),
(16, 'email', 'smtp_host', 'smtp.dollario.com'),
(17, 'email', 'smtp_port', '587'),
(18, 'email', 'smtp_username', 'noreply@dollario.com'),
(19, 'email', 'smtp_password', ''),
(20, 'email', 'smtp_encryption', 'tls'),
(21, 'email', 'from_email', 'noreply@dollario.com'),
(22, 'email', 'from_name', 'Dollario'),
(23, 'email', 'welcome_email_template', 'default'),
(24, 'email', 'transaction_email_template', 'default'),
(25, 'email', 'notification_email_template', 'default'),
(26, 'security', 'login_attempts', '5'),
(27, 'security', 'login_block_time', '30'),
(28, 'security', 'password_strength', 'medium'),
(29, 'security', '2fa_enabled', '1'),
(30, 'security', 'ip_whitelist', ''),
(31, 'security', 'session_fixation', '1'),
(32, 'security', 'cookie_secure', '1'),
(33, 'security', 'cookie_httponly', '1'),
(34, 'notifications', 'email_enabled', '1'),
(35, 'notifications', 'sms_enabled', '0'),
(36, 'notifications', 'push_enabled', '1'),
(37, 'notifications', 'deposit_notify', '1'),
(38, 'notifications', 'withdrawal_notify', '1'),
(39, 'notifications', 'login_notify', '1'),
(40, 'notifications', 'admin_notify_email', 'admin@dollario.com'),
(41, 'payment', 'payment_gateway', 'stripe'),
(42, 'payment', 'stripe_publishable_key', 'pk_test_123'),
(43, 'payment', 'stripe_secret_key', 'sk_test_123'),
(44, 'payment', 'paypal_client_id', ''),
(45, 'payment', 'paypal_secret', ''),
(46, 'payment', 'min_deposit', '100'),
(47, 'payment', 'max_deposit', '100000'),
(48, 'payment', 'min_withdrawal', '500'),
(49, 'payment', 'withdrawal_fee', '10'),
(50, 'payment', 'withdrawal_fee_type', 'percentage'),
(51, 'payment', 'deposit_approval', '0'),
(52, 'maintenance', 'maintenance_mode', '0'),
(53, 'maintenance', 'maintenance_message', 'We are currently undergoing maintenance. Please check back later.'),
(54, 'maintenance', 'maintenance_start', ''),
(55, 'maintenance', 'maintenance_end', ''),
(56, 'maintenance', 'allowed_ips', '127.0.0.1,192.168.1.1');

-- --------------------------------------------------------

--
-- Table structure for table `sub_admins`
--

CREATE TABLE `sub_admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `subadmin_id` varchar(20) DEFAULT 'NOT NULL',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `permissions` text NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_admins`
--

INSERT INTO `sub_admins` (`id`, `name`, `email`, `password`, `created_at`, `subadmin_id`, `first_name`, `last_name`, `phone`, `role`, `status`, `permissions`, `role_id`) VALUES
(9, '', 'kkjangirnhr@gmail.com', '', '2025-05-01 17:14:59', 'NOT NULL', 'Kamlesh', 'Kumar', '6376781372', 'support', 'Active', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('deposit','withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `status`, `created_at`) VALUES
(1, 101, 'deposit', 500.00, 'approved', '2025-05-20 09:03:22'),
(2, 102, '', 200.00, 'pending', '2025-05-20 09:03:22'),
(3, 103, 'deposit', 1000.00, '', '2025-05-20 09:03:22');

-- --------------------------------------------------------

--
-- Table structure for table `usdt_deposits`
--

CREATE TABLE `usdt_deposits` (
  `id` int(11) NOT NULL,
  `tx_hash` varchar(100) DEFAULT NULL,
  `wallet_address` varchar(100) DEFAULT NULL,
  `amount` decimal(18,8) DEFAULT NULL,
  `confirmations` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usdt_deposits`
--

INSERT INTO `usdt_deposits` (`id`, `tx_hash`, `wallet_address`, `amount`, `confirmations`, `timestamp`) VALUES
(1, '0xa3c1e5f8d93a1d3e4b9d8f12b3a1cdef', 'TW3D8rMqBvLo4UXn4k8kSJPQsV9MzZztVm', 100.00000000, 3, '2025-05-16 14:30:00'),
(2, '0x9f1e0a14bfc1a2d5c4e9b8723dc28f94', 'TGT2nWeU5ZfPwVZ5DkP2HGB3vYjKxdQzUJ', 250.50000000, 4, '2025-05-16 13:15:20'),
(3, '0xb7f3c2d1e4a9f3b1d2a7e8123f2d8a56', 'TXKg7MfY2Fb6ZdLuYgCnFxA2hL9zPd3aAz', 75.25000000, 6, '2025-05-15 18:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `referral_code` varchar(20) NOT NULL,
  `referral_program_enabled` tinyint(1) DEFAULT 1,
  `referral_balance` decimal(10,2) DEFAULT 0.00,
  `usdt_wallet` varchar(42) DEFAULT 'NOT NULL',
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'NOT NULL',
  `status` enum('pending','active') DEFAULT 'pending',
  `referred_by` int(11) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `two_fa_enabled TINYINT` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `referral_code`, `referral_program_enabled`, `referral_balance`, `usdt_wallet`, `password`, `role`, `status`, `referred_by`, `mobile`, `otp`, `ip_address`, `two_fa_enabled TINYINT`) VALUES
(1, 'testuser', 'test@example.com', 'ABC123', 1, 240.00, 'NOT NULL', NULL, 'NOT NULL', 'active', NULL, '', '', NULL, 0),
(2, 'ankitpathe', 'dimpalkumar64552@gmail.com', '', 1, 0.00, 'NOT NULL', '$2y$10$KJe7Fpg7xYcSrUiEh7VpjurSW3Q1x0HOfx0.4qcBG8zIdtt4YJm36', 'user', 'active', NULL, '', '', NULL, 0),
(3, 'Kamlesh Kumar', 'kkjangirnhr@gmail.com', '', 1, 0.00, 'NOT NULL', '$2y$10$PC0TxyFzbbn0Unc7MUBV.u4gJfDDNE9C/TRDNCiWTakAVc1SRoCgG', 'user', 'active', NULL, '6376781372', '', NULL, 0),
(5, 'afraj', 'afraj@gmail.com', '', 1, 0.00, 'NOT NULL', '$2y$10$ZiqMWxOiXO6P0PExcCbxaO4Vdngu8t96tHbMMTFkB4qHyaJ3QuAvy', 'user', 'active', NULL, '', '', NULL, 0),
(6, 'afrajkhan', 'fajukhan07895@gmail.com', '', 1, 0.00, 'NOT NULL', '$2y$10$eDIFW5sGWxUhu06T0lBk..h7f6pCSJd2kzD5qz8iEo4MKdap6Kdpq', 'user', 'active', NULL, '', '', NULL, 0),
(7, 'afrajkhan', 'kadbajsdfaj@gmail.com', '', 1, 0.00, 'NOT NULL', '$2y$10$hoKpXtiRb4MX3srEZEAGueB3IyR7fRzYjKc1asQgYZkTQ0A.pq5La', 'user', 'active', NULL, '', '', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_kyc`
--

CREATE TABLE `user_kyc` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pan_card` varchar(255) DEFAULT NULL,
  `aadhaar_card` varchar(255) DEFAULT NULL,
  `bank_statement` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('transaction','security','promotion','system') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_settings`
--

CREATE TABLE `user_notification_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_transactions` tinyint(1) DEFAULT 1,
  `email_promotions` tinyint(1) DEFAULT 1,
  `push_transactions` tinyint(1) DEFAULT 1,
  `push_security` tinyint(1) DEFAULT 1,
  `sms_transactions` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `inr_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `usdt_balance` decimal(15,6) NOT NULL DEFAULT 0.000000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `inr_balance`, `usdt_balance`, `created_at`, `updated_at`) VALUES
(1, 1, 0.00, 0.000000, '2025-05-07 10:54:02', '2025-05-07 10:54:02');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(20) NOT NULL,
  `details` text NOT NULL,
  `status` enum('pending','completed','rejected') DEFAULT 'pending',
  `request_date` datetime DEFAULT current_timestamp(),
  `processed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_admin_id` (`sub_admin_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `blocked_by` (`blocked_by`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crypto_rates`
--
ALTER TABLE `crypto_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currency_pair` (`base_currency`,`target_currency`,`effective_date`);

--
-- Indexes for table `fee_structures`
--
ALTER TABLE `fee_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inr_withdrawals`
--
ALTER TABLE `inr_withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interest_rates`
--
ALTER TABLE `interest_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rate_history`
--
ALTER TABLE `rate_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rate_type_id` (`rate_type`,`rate_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referee_id` (`referee_id`);

--
-- Indexes for table `referral_bonus`
--
ALTER TABLE `referral_bonus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `activity_type` (`activity_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_group` (`setting_group`,`setting_key`);

--
-- Indexes for table `sub_admins`
--
ALTER TABLE `sub_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usdt_deposits`
--
ALTER TABLE `usdt_deposits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tx_hash` (`tx_hash`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_kyc`
--
ALTER TABLE `user_kyc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_notification_settings`
--
ALTER TABLE `user_notification_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `crypto_rates`
--
ALTER TABLE `crypto_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_structures`
--
ALTER TABLE `fee_structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inr_withdrawals`
--
ALTER TABLE `inr_withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `interest_rates`
--
ALTER TABLE `interest_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rate_history`
--
ALTER TABLE `rate_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_bonus`
--
ALTER TABLE `referral_bonus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `security_settings`
--
ALTER TABLE `security_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `sub_admins`
--
ALTER TABLE `sub_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `usdt_deposits`
--
ALTER TABLE `usdt_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_kyc`
--
ALTER TABLE `user_kyc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notification_settings`
--
ALTER TABLE `user_notification_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`sub_admin_id`) REFERENCES `sub_admins` (`id`);

--
-- Constraints for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  ADD CONSTRAINT `blocked_ips_ibfk_1` FOREIGN KEY (`blocked_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `referrals_ibfk_2` FOREIGN KEY (`referee_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `security_logs`
--
ALTER TABLE `security_logs`
  ADD CONSTRAINT `security_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `security_settings`
--
ALTER TABLE `security_settings`
  ADD CONSTRAINT `security_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

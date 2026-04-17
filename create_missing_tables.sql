-- ============================================================
-- Run this SQL in phpMyAdmin to create missing tables
-- Database: u973762102_adming
-- ============================================================

-- 1. user_transactions table (used by sell, deposit, withdraw)
CREATE TABLE IF NOT EXISTS `user_transactions` (
  `id`          int(11)       NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)       NOT NULL,
  `type`        varchar(30)   NOT NULL COMMENT 'sell, deposit, withdraw_inr, withdraw',
  `amount`      decimal(15,2) NOT NULL,
  `currency`    varchar(10)   NOT NULL DEFAULT 'INR',
  `description` text          DEFAULT NULL,
  `status`      varchar(20)   NOT NULL DEFAULT 'pending' COMMENT 'pending, completed, rejected',
  `chain`        varchar(50)  DEFAULT NULL,
  `wallet_address` varchar(100) DEFAULT NULL,
  `created_at`  datetime      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. inr_deposits table (used by deposit.php — admin approves)
CREATE TABLE IF NOT EXISTS `inr_deposits` (
  `id`          int(11)       NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)       NOT NULL,
  `amount`      decimal(15,2) NOT NULL,
  `method`      varchar(50)   DEFAULT NULL COMMENT 'UPI, NEFT, Bank Transfer, Paytm',
  `utr_number`  varchar(100)  DEFAULT NULL,
  `bank_id`     int(11)       DEFAULT NULL,
  `status`      enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` datetime      DEFAULT NULL,
  `created_at`  datetime      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

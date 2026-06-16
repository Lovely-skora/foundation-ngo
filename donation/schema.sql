-- ============================================================
--  DONATION SYSTEM — DATABASE SCHEMA
--  Import: mysql -u root -p donation_db < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS donation_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE donation_db;

CREATE TABLE IF NOT EXISTS donors (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(120)  NOT NULL,
    email         VARCHAR(180)  NOT NULL,
    phone         VARCHAR(20)   NOT NULL,
    city          VARCHAR(80),
    state         VARCHAR(80),
    pan_number    VARCHAR(10),
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS donations (
    id                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_id             INT UNSIGNED NOT NULL,
    razorpay_order_id    VARCHAR(60)  NOT NULL,
    razorpay_payment_id  VARCHAR(60)  DEFAULT NULL,
    razorpay_signature   VARCHAR(128) DEFAULT NULL,
    amount               DECIMAL(10,2) NOT NULL,
    currency             VARCHAR(5)   DEFAULT 'INR',
    donation_type        ENUM('one-time','monthly') DEFAULT 'one-time',
    campaign             VARCHAR(100),
    message              TEXT,
    wants_80g            TINYINT(1)   DEFAULT 1,
    payment_method       VARCHAR(30),
    payment_status       ENUM('created','paid','failed','refunded') DEFAULT 'created',
    receipt_path         VARCHAR(255),
    ip_address           VARCHAR(45),
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE INDEX idx_order  (razorpay_order_id),
    INDEX idx_status         (payment_status),
    FOREIGN KEY (donor_id) REFERENCES donors(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS rate_limits (
    ip_address   VARCHAR(45) NOT NULL,
    attempts     INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin login attempts tracking
CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address   VARCHAR(45)  NOT NULL,
    username     VARCHAR(80)  NOT NULL,
    attempted_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blocked IPs
CREATE TABLE IF NOT EXISTS admin_blocked_ips (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address   VARCHAR(45)  NOT NULL UNIQUE,
    username     VARCHAR(80),
    reason       VARCHAR(255) DEFAULT '3 failed login attempts',
    blocked_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    unblocked_at TIMESTAMP    NULL,
    is_active    TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
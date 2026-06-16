<?php
require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;

    public static function get() {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );
            $options = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            );
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('DB Connection failed: ' . $e->getMessage());
                http_response_code(500);
                die(json_encode(array('error' => 'Database connection failed.')));
            }
        }
        return self::$instance;
    }
}

function createTables() {
    $db = Database::get();
    $db->exec("
        CREATE TABLE IF NOT EXISTS donors (
            id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name     VARCHAR(120)  NOT NULL,
            email         VARCHAR(180)  NOT NULL,
            phone         VARCHAR(20)   NOT NULL,
            city          VARCHAR(80),
            state         VARCHAR(80),
            pan_number    VARCHAR(10),
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS donations (
            id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            donor_id         INT UNSIGNED NOT NULL,
            razorpay_order_id  VARCHAR(60)  NOT NULL UNIQUE,
            razorpay_payment_id VARCHAR(60) DEFAULT NULL,
            razorpay_signature  VARCHAR(128) DEFAULT NULL,
            amount           DECIMAL(10,2) NOT NULL,
            currency         VARCHAR(5)   DEFAULT 'INR',
            donation_type    ENUM('one-time','monthly') DEFAULT 'one-time',
            campaign         VARCHAR(100),
            message          TEXT,
            wants_80g        TINYINT(1)   DEFAULT 1,
            payment_method   VARCHAR(30),
            payment_status   ENUM('created','paid','failed','refunded') DEFAULT 'created',
            receipt_path     VARCHAR(255),
            ip_address       VARCHAR(45),
            created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (donor_id) REFERENCES donors(id),
            INDEX idx_order  (razorpay_order_id),
            INDEX idx_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS rate_limits (
            ip_address  VARCHAR(45) NOT NULL,
            attempts    INT DEFAULT 1,
            window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

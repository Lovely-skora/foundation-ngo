<?php
// =============================================
// admin/db.php — Database Connection
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'foundation_ngo');

$pdo = null;

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid red;margin:20px;border-radius:6px;">
        <strong>Database Connection Failed.</strong><br>
        Please check <code>admin/db.php</code> and update your DB credentials.<br>
        Error: ' . htmlspecialchars($e->getMessage()) . '
    </div>');
}

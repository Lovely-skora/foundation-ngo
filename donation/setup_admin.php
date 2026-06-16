<?php
/**
 * SETUP SCRIPT — Run once to create admin user + update DB schema
 * URL: https://yourdomain.com/donation/setup_admin.php
 * DELETE THIS FILE after running!
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$db = Database::get();

// Create admin_users table
$db->exec("
    CREATE TABLE IF NOT EXISTS admin_users (
        id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username      VARCHAR(80)  NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        full_name     VARCHAR(120) NOT NULL,
        is_active     TINYINT(1)   DEFAULT 1,
        last_login    TIMESTAMP    NULL,
        created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Create default admin — CHANGE THESE BEFORE RUNNING
$username  = 'vwf@^%$^#NGHJ';
$password  = 'A_^&^&*%klsdafj%$@#%#';
$fullName  = 'VWF';

// Check if already exists
$stmt = $db->prepare("SELECT id FROM admin_users WHERE username = ?");
$stmt->execute(array($username));
if ($stmt->fetch()) {
    die('<p style="font-family:sans-serif;color:orange">⚠️ Admin user already exists. <a href="public/admin/login.php">Go to Login</a></p>');
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$db->prepare("INSERT INTO admin_users (username, password_hash, full_name) VALUES (?,?,?)")
   ->execute(array($username, $hash, $fullName));

echo '<div style="font-family:sans-serif;max-width:500px;margin:40px auto;padding:24px;background:#E1F5EE;border-radius:10px">
  <h2 style="color:#0F6E56">✅ Admin setup complete!</h2>
  <p style="margin-top:12px"><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>
  <p style="margin-top:6px"><strong>Password:</strong> ' . htmlspecialchars($password) . '</p>
  <p style="margin-top:16px;color:#B91C1C;font-weight:500">⚠️ DELETE this file (setup_admin.php) immediately after login!</p>
  <a href="public/admin/login.php" style="display:inline-block;margin-top:16px;padding:10px 20px;background:#1D9E75;color:#fff;border-radius:8px;text-decoration:none">→ Go to Admin Login</a>
</div>';

<?php
require_once __DIR__ . '/../../includes/security.php';
require_once __DIR__ . '/../../includes/db.php';

secureSession();
setSecurityHeaders();

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$expired = isset($_GET['expired']);
$ip      = getClientIp();

// ── Create tables if not exist ────────────────────────────────
function ensureBlockTables($db) {
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_login_attempts (
            id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address   VARCHAR(45)  NOT NULL,
            username     VARCHAR(80)  NOT NULL,
            attempted_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS admin_blocked_ips (
            id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address   VARCHAR(45)  NOT NULL UNIQUE,
            username     VARCHAR(80),
            reason       VARCHAR(255) DEFAULT '3 failed login attempts',
            blocked_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
            is_active    TINYINT(1)   DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

// ── Check if IP is blocked ─────────────────────────────────────
function isIpBlocked($db, $ip) {
    $stmt = $db->prepare("SELECT id FROM admin_blocked_ips WHERE ip_address = ? AND is_active = 1 LIMIT 1");
    $stmt->execute(array($ip));
    return (bool)$stmt->fetch();
}

// ── Count recent failed attempts ──────────────────────────────
function getFailedAttempts($db, $ip) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE ip_address = ?");
    $stmt->execute(array($ip));
    return (int)$stmt->fetchColumn();
}

// ── Log failed attempt ────────────────────────────────────────
function logFailedAttempt($db, $ip, $username) {
    $db->prepare("INSERT INTO admin_login_attempts (ip_address, username) VALUES (?, ?)")
       ->execute(array($ip, $username));
}

// ── Block IP permanently ──────────────────────────────────────
function blockIp($db, $ip, $username) {
    $db->prepare("INSERT INTO admin_blocked_ips (ip_address, username) VALUES (?, ?) ON DUPLICATE KEY UPDATE is_active = 1, blocked_at = NOW()")
       ->execute(array($ip, $username));
    logEvent('warning', 'IP permanently blocked after 3 failed attempts', array('ip' => $ip, 'username' => $username));
}

$db = Database::get();
ensureBlockTables($db);

// ── Check block BEFORE showing form ──────────────────────────
if (isIpBlocked($db, $ip)) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Access Blocked</title>
    <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:sans-serif;background:#1a1a1a;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
    .box{background:#fff;border-radius:12px;padding:40px;max-width:420px;text-align:center}
    .icon{font-size:52px;margin-bottom:16px}
    h1{font-size:22px;color:#B91C1C;margin-bottom:10px}
    p{font-size:14px;color:#666;line-height:1.6}
    .ip{font-family:monospace;background:#f5f5f5;padding:4px 10px;border-radius:4px;font-size:13px;margin-top:10px;display:inline-block}
    </style>
    </head>
    <body>
    <div class="box">
      <div class="icon">🚫</div>
      <h1>Access Permanently Blocked</h1>
      <p>Your IP address has been blocked due to <strong>3 failed login attempts</strong>.</p>
      <p>Please contact the administrator to get unblocked.</p>
      <div class="ip"><?= htmlspecialchars($ip) ?></div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// ── Handle login form ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = cleanStr(isset($_POST['username']) ? $_POST['username'] : '', 80);
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $stmt = $db->prepare("SELECT id, username, password_hash, full_name FROM admin_users WHERE username = ? AND is_active = 1 LIMIT 1");
        $stmt->execute(array($username));
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // ✅ Login success — clear attempts
            $db->prepare("DELETE FROM admin_login_attempts WHERE ip_address = ?")
               ->execute(array($ip));

            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_name']      = $admin['full_name'];
            $_SESSION['admin_username']  = $admin['username'];
            $_SESSION['login_time']      = time();

            $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?")
               ->execute(array($admin['id']));
            logEvent('info', 'Admin login success', array('username' => $username, 'ip' => $ip));

            header('Location: dashboard.php');
            exit;

        } else {
            // ❌ Wrong password
            logFailedAttempt($db, $ip, $username);
            $attempts = getFailedAttempts($db, $ip);
            $remaining = 3 - $attempts;

            logEvent('warning', 'Failed admin login', array('username' => $username, 'ip' => $ip, 'attempts' => $attempts));

            if ($attempts >= 3) {
                // Block permanently
                blockIp($db, $ip, $username);
                header('Location: login.php');
                exit;
            }

            $error = 'Invalid username or password. ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining before permanent block.';
            sleep(1);
        }
    }
}

$csrf = generateCsrfToken();
$attempts = getFailedAttempts($db, $ip);
$remaining = 3 - $attempts;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — <?= htmlspecialchars(APP_NAME) ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:linear-gradient(135deg,#0F6E56,#1D9E75);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:16px;padding:40px;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,.25)}
.logo{text-align:center;margin-bottom:28px}
.logo .icon{font-size:44px}
.logo h1{font-size:20px;font-weight:600;color:#0F6E56;margin-top:10px}
.logo p{font-size:13px;color:#888;margin-top:4px}
.field{margin-bottom:16px}
.field label{display:block;font-size:13px;font-weight:500;color:#444;margin-bottom:6px}
.field input{width:100%;padding:11px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px;transition:border-color .15s;font-family:inherit}
.field input:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.12)}
.btn{width:100%;padding:13px;background:#1D9E75;border:none;border-radius:8px;color:#fff;font-size:15px;font-weight:500;cursor:pointer;margin-top:8px;font-family:inherit;transition:background .15s}
.btn:hover{background:#0F6E56}
.btn:disabled{background:#aaa;cursor:not-allowed}
.alert{padding:11px 14px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert.error{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C}
.alert.warning{background:#FFFBEB;border:1px solid #FDE68A;color:#92400E}
.attempts-bar{display:flex;gap:6px;margin-bottom:16px;justify-content:center}
.attempt-dot{width:12px;height:12px;border-radius:50%;background:#eee}
.attempt-dot.used{background:#B91C1C}
.attempt-dot.ok{background:#1D9E75}
.footer-note{text-align:center;font-size:12px;color:#bbb;margin-top:20px}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="icon">🔐</div>
    <h1><?= htmlspecialchars(APP_NAME) ?></h1>
    <p>Admin Panel — Secure Login</p>
  </div>

  <?php if ($expired): ?>
    <div class="alert warning">⏱ Session expired. Please login again.</div>
  <?php endif ?>

  <?php if ($error): ?>
    <div class="alert error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif ?>

  <?php if ($attempts > 0): ?>
  <div class="attempts-bar">
    <?php for ($i = 1; $i <= 3; $i++): ?>
      <div class="attempt-dot <?= $i <= $attempts ? 'used' : 'ok' ?>"></div>
    <?php endfor ?>
  </div>
  <p style="text-align:center;font-size:12px;color:#B91C1C;margin-bottom:14px">
    ⚠️ <?= $attempts ?>/3 failed attempts — <?= $remaining ?> remaining
  </p>
  <?php endif ?>

  <form method="POST" action="login.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
    <div class="field">
      <label>Username</label>
      <input type="text" name="username" placeholder="admin" autocomplete="username" required autofocus>
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="password" placeholder="••••••••" autocomplete="current-password" required>
    </div>
    <button type="submit" class="btn" <?= $attempts >= 3 ? 'disabled' : '' ?>>🔓 Login</button>
  </form>

  <div class="footer-note">🔒 Authorised personnel only</div>
</div>
</body>
</html>

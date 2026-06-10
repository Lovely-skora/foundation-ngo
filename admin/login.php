<?php
// =============================================
// admin/login.php — Secure Login
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ]);
}

// Already logged in
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once 'db.php';

$error = '';
$timeout = isset($_GET['timeout']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Brute force: max 5 attempts per 10 mins
        $attempts_key = 'login_attempts_' . $_SERVER['REMOTE_ADDR'];
        $last_attempt_key = 'login_last_attempt_' . $_SERVER['REMOTE_ADDR'];

        if (!isset($_SESSION[$attempts_key])) $_SESSION[$attempts_key] = 0;
        if (!isset($_SESSION[$last_attempt_key])) $_SESSION[$last_attempt_key] = 0;

        // Reset counter after 10 minutes
        if ((time() - $_SESSION[$last_attempt_key]) > 600) {
            $_SESSION[$attempts_key] = 0;
        }

        if ($_SESSION[$attempts_key] >= 5) {
            $wait = 600 - (time() - $_SESSION[$last_attempt_key]);
            $error = 'Too many failed attempts. Please wait ' . ceil($wait / 60) . ' minute(s).';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $password === '') {
                $error = 'Please enter both username and password.';
            } else {
                $stmt = $pdo->prepare('SELECT id, password FROM admin_users WHERE username = ? LIMIT 1');
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Success — regenerate session
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_last_active'] = time();
                    unset($_SESSION[$attempts_key], $_SESSION[$last_attempt_key]);
                    header('Location: index.php');
                    exit;
                } else {
                    $_SESSION[$attempts_key]++;
                    $_SESSION[$last_attempt_key] = time();
                    $remaining = 5 - $_SESSION[$attempts_key];
                    $error = 'Invalid username or password.' . ($remaining > 0 ? " $remaining attempt(s) remaining." : '');
                }
            }
        }
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Foundation NGO</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: #0f172a;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .login-card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 12px;
    padding: 40px 36px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
  }
  .login-card h1 {
    color: #f1f5f9;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
    text-align: center;
  }
  .login-card p.sub {
    color: #94a3b8;
    font-size: 13px;
    text-align: center;
    margin-bottom: 28px;
  }
  .alert {
    padding: 10px 14px;
    border-radius: 6px;
    font-size: 13px;
    margin-bottom: 18px;
  }
  .alert-danger { background: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d; }
  .alert-warning { background: #431407; color: #fdba74; border: 1px solid #7c2d12; }
  label {
    display: block;
    color: #94a3b8;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
  }
  input[type=text], input[type=password] {
    width: 100%;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 6px;
    color: #f1f5f9;
    font-size: 14px;
    padding: 10px 14px;
    outline: none;
    transition: border-color .2s;
    margin-bottom: 18px;
  }
  input:focus { border-color: #6366f1; }
  button[type=submit] {
    width: 100%;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    padding: 11px;
    cursor: pointer;
    transition: background .2s;
  }
  button:hover { background: #4f46e5; }
  .lock-icon { text-align: center; font-size: 36px; margin-bottom: 16px; }
</style>
</head>
<body>
<div class="login-card">
  <div class="lock-icon">🔒</div>
  <h1>Admin Panel</h1>
  <p class="sub">Foundation NGO — Secure Access</p>

  <?php if ($timeout): ?>
    <div class="alert alert-warning">Session expired. Please log in again.</div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <label>Username</label>
    <input type="text" name="username" autocomplete="username" required
           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    <label>Password</label>
    <input type="password" name="password" autocomplete="current-password" required>
    <button type="submit">Login →</button>
  </form>
</div>
</body>
</html>

<?php
// =============================================
// admin/auth.php — Session Guard
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ]);
}

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Session timeout: 2 hours
if (isset($_SESSION['admin_last_active']) && (time() - $_SESSION['admin_last_active']) > 7200) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['admin_last_active'] = time();

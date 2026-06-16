<?php
require_once __DIR__ . '/../../includes/security.php';

function requireAdminLogin() {
    secureSession();
    // Session timeout — 2 hours
    if (!empty($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 7200) {
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
    // Refresh login time on activity
    $_SESSION['login_time'] = time();
}

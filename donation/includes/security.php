<?php
require_once __DIR__ . '/config.php';

function secureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.name',            'VWFSESS');
        ini_set('session.cookie_path',     '/');
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure',   APP_ENV === 'production' ? 1 : 0);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime',  SESSION_LIFETIME);
        ini_set('session.use_strict_mode', 1);
        session_start();

        if (empty($_SESSION['_initiated'])) {
            $_SESSION['_initiated'] = true;
        }
    }
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCsrfToken()) . '">';
}

function cleanStr($val, $maxLen = 255) {
    return substr(trim(strip_tags((string)$val)), 0, $maxLen);
}

function cleanEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

function cleanPhone($phone) {
    $p = preg_replace('/[^\d+]/', '', $phone);
    return (strlen($p) >= 10 && strlen($p) <= 15) ? $p : false;
}

function cleanPan($pan) {
    $pan = strtoupper(trim($pan));
    return preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $pan) ? $pan : false;
}

function cleanAmount($amount) {
    $amt = filter_var($amount, FILTER_VALIDATE_FLOAT);
    if ($amt === false || $amt < 1 || $amt > 1000000) return false;
    return round($amt, 2);
}

function checkRateLimit() {
    $ip = getClientIp();
    $db = Database::get();

    $db->prepare("DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL ? SECOND)")
       ->execute(array(RATE_LIMIT_WINDOW));

    $stmt = $db->prepare("SELECT attempts FROM rate_limits WHERE ip_address = ?");
    $stmt->execute(array($ip));
    $row = $stmt->fetch();

    if (!$row) {
        $db->prepare("INSERT INTO rate_limits (ip_address) VALUES (?)")->execute(array($ip));
        return true;
    }
    if ($row['attempts'] >= RATE_LIMIT_MAX) return false;

    $db->prepare("UPDATE rate_limits SET attempts = attempts + 1 WHERE ip_address = ?")
       ->execute(array($ip));
    return true;
}

function getClientIp() {
    $headers = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR');
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) {
            $ip = trim(explode(',', $_SERVER[$h])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
}

function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header("Content-Security-Policy: default-src * 'unsafe-inline' 'unsafe-eval'; script-src * 'unsafe-inline' 'unsafe-eval'; style-src * 'unsafe-inline'; font-src * data:; img-src * data: blob:; frame-src *; connect-src *;");
}

function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function logEvent($level, $msg, $context = array()) {
    $logDir = __DIR__ . '/../logs/';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0750, true);
    }
    $line = sprintf(
        "[%s] [%s] %s %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $msg,
        $context ? json_encode($context) : ''
    );
    error_log($line, 3, $logDir . 'app.log');
}
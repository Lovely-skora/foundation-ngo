<?php
define('APP_NAME',    'Vivekananda Welfare Foundation');
define('APP_URL',     'https://vwf.org.in/donation');
define('APP_ENV',     'production');

// ── Database ────────────────────────────────────────────────
define('DB_HOST',     'localhost');
define('DB_NAME',     'skorasoft_donation');
define('DB_USER',     'skorasoft_donation');
define('DB_PASS',     'ZsdJ?p~6}6,D=CC}');
define('DB_CHARSET',  'utf8mb4');

// ── Razorpay ─────────────────────────────────────────────────
define('RAZORPAY_KEY_ID',     'rzp_live_SwkCJcx24fWFhF');
define('RAZORPAY_KEY_SECRET', 'tpw4qiAiXPU0BxyArBF7ppiv');

// ── Email (PHPMailer / SMTP) ──────────────────────────────────
define('SMTP_HOST',     'smtp.gmail.com');
define('SMTP_PORT',     587);
define('SMTP_USER',     'swatantraverma2801@gmail.com');
define('SMTP_PASS',     'negmxjgultlhlmkm');
define('MAIL_FROM',     'swatantraverma2801@gmail.com');
define('MAIL_FROM_NAME', APP_NAME);

// ── NGO Legal Details (printed on receipts) ──────────────────
define('NGO_REG_NO',    'HOME/SRC-7298');
define('NGO_80G_NO',    'AAEAV4567C25KL02');
define('NGO_PAN',       'AAEAV4567C');
define('NGO_ADDRESS',   'House No.15, 2nd Floor, DMC Building, Opp. Gurudwara, Kalibari Road, Dimapur- 797112- Nagaland');
define('NGO_PHONE',     '+91-9774807718');
define('NGO_EMAIL',     'vwfdimapur@gmail.com');

// ── Security ─────────────────────────────────────────────────
define('CSRF_TOKEN_LENGTH', 32);
define('SESSION_LIFETIME',  1800);
define('RATE_LIMIT_WINDOW', 300);
define('RATE_LIMIT_MAX',    10);

// ── Receipts folder ──────────────────────────────────────────
define('RECEIPTS_DIR', __DIR__ . '/../receipts/');

// ── Timezone ─────────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');
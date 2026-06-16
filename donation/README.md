# 🇮🇳 PHP Donation System with Razorpay

Complete, production-ready NGO donation system with:
- 3-step donation form (Details → Amount → Payment)
- Razorpay payment gateway (UPI / Card / Net Banking / Wallets)
- 80G tax receipt generation (HTML → printable PDF)
- Auto email receipt
- Admin panel
- Full security hardening

---

## 📁 Folder Structure

```
donation-php/
├── public/                  ← Webroot (point Apache/Nginx here)
│   ├── index.php            ← Main donation form
│   ├── create_order.php     ← API: Create Razorpay order
│   ├── verify_payment.php   ← API: Verify payment signature
│   ├── webhook.php          ← Razorpay webhook (server-to-server)
│   └── admin.php            ← Admin panel
├── includes/
│   ├── config.php           ← ⚠️ Fill your keys here
│   ├── db.php               ← Database connection + schema
│   ├── security.php         ← CSRF, rate limit, sanitization
│   ├── razorpay.php         ← Razorpay API integration
│   ├── receipt.php          ← PDF/HTML receipt generator
│   └── mailer.php           ← Email sender
├── assets/css/style.css     ← Frontend styles
├── receipts/                ← Generated receipts (writable, not public)
├── logs/                    ← App logs (writable)
├── schema.sql               ← MySQL schema
└── .htaccess                ← Security config
```

---

## ⚙️ Setup Steps

### 1. Database
```bash
mysql -u root -p -e "CREATE DATABASE donation_db;"
mysql -u root -p donation_db < schema.sql
```

### 2. Configuration
Edit `includes/config.php`:
```php
define('DB_HOST',     'localhost');
define('DB_NAME',     'donation_db');
define('DB_USER',     'your_db_user');
define('DB_PASS',     'your_db_password');

define('RAZORPAY_KEY_ID',     'rzp_test_XXXXXXXXXX');
define('RAZORPAY_KEY_SECRET', 'your_secret');

define('APP_URL',  'https://yourdomain.com');
define('APP_ENV',  'production');   // change from 'development'
```

### 3. File permissions
```bash
chmod 750 receipts/ logs/
chown www-data:www-data receipts/ logs/
```

### 4. Razorpay Dashboard setup
- Go to Dashboard → Settings → Webhooks
- Add webhook URL: `https://yourdomain.com/webhook.php`
- Select events: `payment.captured`, `payment.failed`, `refund.created`
- Copy webhook secret → paste in `RAZORPAY_KEY_SECRET`

### 5. Admin panel
Access: `https://yourdomain.com/admin.php?key=YOUR_ADMIN_KEY`
Change the `ADMIN_KEY` constant in `admin.php` to something strong.

---

## 🔒 Security Features

| Feature | Implementation |
|---------|----------------|
| CSRF Protection | Token per session, verified on every POST |
| SQL Injection | PDO prepared statements everywhere |
| XSS | `htmlspecialchars()` on all output |
| Input validation | Type-checked, length-limited, regex-validated |
| Rate limiting | DB-backed, 10 attempts per 5 minutes per IP |
| Signature verification | HMAC-SHA256 on every payment |
| Session hardening | httponly, samesite=Strict, strict mode |
| Security headers | CSP, X-Frame-Options, HSTS (production) |
| Directory protection | .htaccess blocks includes/, logs/, receipts/ |
| Error handling | Errors logged, never shown to user |

---

## 📧 Email (Production)
Replace `mail()` in `mailer.php` with PHPMailer:
```bash
composer require phpmailer/phpmailer
```
Then follow the commented example in `mailer.php`.

---

## 💳 Going Live
1. Switch `rzp_test_` keys → `rzp_live_` keys in config.php
2. Set `APP_ENV = 'production'`
3. Enable HTTPS + HSTS (uncomment in .htaccess)
4. Set strong `ADMIN_KEY`
5. Disable `display_errors` on server level

---

## 🧾 80G Receipt
- Generated as HTML (print-ready)
- For true PDF: install `wkhtmltopdf` and replace `file_put_contents` in `receipt.php` with:
```php
shell_exec("wkhtmltopdf {$htmlPath} {$pdfPath}");
```
- Or use `composer require mpdf/mpdf`

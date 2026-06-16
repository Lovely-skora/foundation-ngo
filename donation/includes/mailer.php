<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';

class Mailer {

    public static function sendReceiptEmail($donor, $donation, $receiptPath) {
        $to      = $donor['email'];
        $name    = $donor['full_name'];
        $amount  = number_format($donation['amount'], 2);
        $orderId = $donation['razorpay_order_id'];

        $subject = "Donation Receipt - Rs." . $amount . " | " . APP_NAME;
        $body    = self::receiptEmailHtml($name, $amount, $orderId, $donation);

        return self::send($to, $name, $subject, $body, $receiptPath);
    }

    private static function send($toEmail, $toName, $subject, $htmlBody, $attachPath = null) {
        // FIX 5: Use SMTP via PHPMailer-style manual SMTP if available,
        // fallback to php mail(). On shared hosting php mail() usually works fine.
        // If you want proper SMTP (Gmail), install PHPMailer via composer and replace this method.

        $boundary = md5(uniqid((string)rand(), true));

        // Encode subject for non-ASCII
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $encodedName    = '=?UTF-8?B?' . base64_encode($toName) . '?=';
        $fromName       = '=?UTF-8?B?' . base64_encode(MAIL_FROM_NAME) . '?=';

        $headers = implode("\r\n", array(
            'MIME-Version: 1.0',
            'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
            'From: ' . $fromName . ' <' . MAIL_FROM . '>',
            'Reply-To: ' . MAIL_FROM,
            'X-Mailer: PHP/' . PHP_VERSION,
        ));

        $message  = "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        $message .= $htmlBody . "\r\n";

        if ($attachPath && file_exists($attachPath)) {
            $fileData = chunk_split(base64_encode(file_get_contents($attachPath)));
            $fileName = basename($attachPath);
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: text/html; name=\"{$fileName}\"\r\n";
            $message .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $message .= $fileData . "\r\n";
        }
        $message .= "--{$boundary}--";

        $sent = @mail($toEmail, $encodedSubject, $message, $headers);
        if (!$sent) {
            logEvent('error', 'Mail send failed', array('to' => $toEmail));
        }
        return $sent;
    }

    private static function receiptEmailHtml($name, $amount, $orderId, $d) {
        $ngo      = APP_NAME;
        $date     = date('d M Y');
        $campaign = htmlspecialchars(isset($d['campaign']) ? $d['campaign'] : 'General Fund');
        $payId    = htmlspecialchars(isset($d['razorpay_payment_id']) ? $d['razorpay_payment_id'] : '');
        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
<body style="font-family:sans-serif;background:#f5f5f5;padding:30px">
<div style="max-width:560px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden">
  <div style="background:#0F6E56;padding:28px 32px;color:#fff">
    <h1 style="margin:0;font-size:20px">' . $ngo . '</h1>
    <p style="margin:6px 0 0;opacity:.8;font-size:13px">Donation receipt - ' . $date . '</p>
  </div>
  <div style="padding:28px 32px">
    <p style="font-size:16px">Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
    <p style="color:#555;font-size:14px;line-height:1.7;margin-top:12px">Thank you for your generous donation of <strong style="color:#0F6E56">&#8377;' . $amount . '</strong> towards <em>' . $campaign . '</em>. Your support means the world to us.</p>
    <div style="background:#E1F5EE;border-radius:8px;padding:16px 20px;margin:20px 0">
      <p style="margin:0;font-size:13px;color:#0F6E56"><strong>Order ID:</strong> ' . htmlspecialchars($orderId) . '</p>
      ' . ($payId ? '<p style="margin:6px 0 0;font-size:13px;color:#0F6E56"><strong>Payment ID:</strong> ' . $payId . '</p>' : '') . '
      <p style="margin:6px 0 0;font-size:13px;color:#0F6E56"><strong>Amount:</strong> &#8377;' . $amount . '</p>
      <p style="margin:6px 0 0;font-size:13px;color:#0F6E56"><strong>Status:</strong> &#10003; Confirmed</p>
    </div>
    <p style="font-size:13px;color:#777">Your 80G tax receipt is attached to this email. Please keep it safe for tax filing.</p>
    <p style="font-size:14px;margin-top:24px">With gratitude,<br><strong style="color:#0F6E56">' . $ngo . '</strong></p>
  </div>
  <div style="background:#f9f9f7;padding:14px 32px;font-size:11px;color:#999">
    Automated email | Section 80G Eligible | ' . NGO_REG_NO . '
  </div>
</div>
</body></html>';
    }
}

<?php
require_once __DIR__ . '/config.php';

class ReceiptGenerator {

    public static function generate($donation, $donor) {
        return self::buildPdf($donation, $donor);
    }

    private static function buildPdf($d, $donor) {
        $receiptNo   = 'REC-' . strtoupper(substr($d['razorpay_order_id'], -8)) . '-' . date('Y');
        $date        = date('d M Y', strtotime($d['created_at']));
        $amount      = number_format($d['amount'], 2);
        $amountWords = self::amountToWords((float)$d['amount']);

        if (!is_dir(RECEIPTS_DIR)) {
            mkdir(RECEIPTS_DIR, 0750, true);
        }

        $html     = self::buildHtml($d, $donor, $receiptNo, $date, $amount, $amountWords);
        $filename = 'receipt_' . $receiptNo . '_' . time() . '.html';
        $path     = RECEIPTS_DIR . $filename;
        file_put_contents($path, $html);

        return $path;
    }

    private static function buildHtml($d, $donor, $receiptNo, $date, $amount, $amountWords) {
        $ngoName    = APP_NAME;
        $ngoReg     = NGO_REG_NO;
        $ngo80g     = NGO_80G_NO;
        $ngoPan     = NGO_PAN;
        $ngoAddr    = NGO_ADDRESS;
        $donorName  = htmlspecialchars($donor['full_name']);
        $donorEmail = htmlspecialchars($donor['email']);
        $donorPhone = htmlspecialchars($donor['phone']);
        $donorPan   = htmlspecialchars($donor['pan_number'] ? $donor['pan_number'] : 'Not provided');
        $campaign   = htmlspecialchars($d['campaign'] ? $d['campaign'] : 'General Fund');
        $payId      = htmlspecialchars($d['razorpay_payment_id']);
        $ordId      = htmlspecialchars($d['razorpay_order_id']);
        $donType    = htmlspecialchars($d['donation_type']);

        return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Donation Receipt - ' . $receiptNo . '</title>
<style>
@import url(\'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@400;500;600&display=swap\');
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:\'DM Sans\',sans-serif;color:#1a1a1a;background:#f5f4f0;padding:40px 20px;print-color-adjust:exact;-webkit-print-color-adjust:exact}
.page{max-width:680px;margin:0 auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08)}
.header{background:#0F6E56;padding:32px 40px;color:#fff;display:flex;justify-content:space-between;align-items:flex-start}
.org-name{font-family:\'DM Serif Display\',serif;font-size:22px;margin-bottom:4px}
.org-meta{font-size:12px;opacity:.8;line-height:1.8}
.receipt-badge{text-align:right}
.receipt-badge .label{font-size:11px;opacity:.7;text-transform:uppercase;letter-spacing:.08em}
.receipt-badge .no{font-size:18px;font-weight:600;font-family:monospace;margin-top:4px}
.green-strip{height:5px;background:linear-gradient(90deg,#1D9E75,#5DCAA5)}
.body{padding:36px 40px}
.thank-you{font-family:\'DM Serif Display\',serif;font-size:28px;color:#0F6E56;margin-bottom:6px}
.sub{font-size:14px;color:#666;margin-bottom:28px}
.amount-box{background:#E1F5EE;border-radius:10px;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;border-left:4px solid #1D9E75}
.amt-label{font-size:13px;color:#0F6E56;font-weight:500}
.amt-value{font-size:32px;font-weight:600;color:#0F6E56;font-family:\'DM Serif Display\',serif}
.amt-words{font-size:12px;color:#555;margin-top:4px}
.status-badge{display:inline-flex;align-items:center;gap:5px;background:#E1F5EE;color:#0F6E56;font-size:12px;font-weight:500;padding:3px 12px;border-radius:20px}
.section{margin-bottom:24px}
.section-title{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#999;font-weight:500;margin-bottom:12px;padding-bottom:6px;border-bottom:0.5px solid #eee}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.info-item .key{font-size:12px;color:#888;margin-bottom:2px}
.info-item .val{font-size:14px;font-weight:500;color:#1a1a1a}
.txn-box{background:#f9f9f7;border-radius:8px;padding:16px 20px}
.txn-row{display:flex;justify-content:space-between;padding:5px 0;font-size:13px;border-bottom:0.5px solid #eee}
.txn-row:last-child{border:none}
.txn-row .k{color:#777}
.txn-row .v{font-weight:500;font-family:monospace;font-size:12px}
.seal{text-align:right;margin-top:20px;padding-top:20px;border-top:0.5px dashed #ddd}
.seal .sig{font-family:\'DM Serif Display\',serif;font-size:18px;color:#0F6E56}
.seal .sig-label{font-size:11px;color:#999;margin-top:2px}
.footer{background:#f9f9f7;padding:20px 40px;font-size:11px;color:#888;line-height:1.8;border-top:0.5px solid #eee}
@media print{body{background:#fff;padding:0}.page{box-shadow:none;border-radius:0}}
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div>
      <div class="org-name">' . $ngoName . '</div>
      <div class="org-meta">
        Reg No: ' . $ngoReg . ' &nbsp;|&nbsp; PAN: ' . $ngoPan . '<br>
        80G No: ' . $ngo80g . '<br>
        ' . $ngoAddr . '
      </div>
    </div>
    <div class="receipt-badge">
      <div class="label">Receipt No.</div>
      <div class="no">' . $receiptNo . '</div>
      <div style="font-size:12px;opacity:.7;margin-top:4px">' . $date . '</div>
    </div>
  </div>
  <div class="green-strip"></div>
  <div class="body">
    <div class="thank-you">Thank you, ' . $donorName . '!</div>
    <div class="sub">Your generous contribution helps us make a difference every day.</div>
    <div class="amount-box">
      <div>
        <div class="amt-label">Donation amount</div>
        <div class="amt-value">&#8377;' . $amount . '</div>
        <div class="amt-words">' . $amountWords . ' Only</div>
      </div>
      <span class="status-badge">&#10003; Confirmed</span>
    </div>
    <div class="section">
      <div class="section-title">Donor information</div>
      <div class="info-grid">
        <div class="info-item"><div class="key">Full Name</div><div class="val">' . $donorName . '</div></div>
        <div class="info-item"><div class="key">Email</div><div class="val">' . $donorEmail . '</div></div>
        <div class="info-item"><div class="key">Mobile</div><div class="val">' . $donorPhone . '</div></div>
        <div class="info-item"><div class="key">PAN Number</div><div class="val">' . $donorPan . '</div></div>
      </div>
    </div>
    <div class="section">
      <div class="section-title">Donation details</div>
      <div class="info-grid">
        <div class="info-item"><div class="key">Campaign</div><div class="val">' . $campaign . '</div></div>
        <div class="info-item"><div class="key">Type</div><div class="val">' . $donType . '</div></div>
      </div>
    </div>
    <div class="section">
      <div class="section-title">Transaction details</div>
      <div class="txn-box">
        <div class="txn-row"><span class="k">Razorpay Order ID</span><span class="v">' . $ordId . '</span></div>
        <div class="txn-row"><span class="k">Razorpay Payment ID</span><span class="v">' . $payId . '</span></div>
        <div class="txn-row"><span class="k">Payment Method</span><span class="v">' . $d['payment_method'] . '</span></div>
        <div class="txn-row"><span class="k">Date &amp; Time</span><span class="v">' . $d['created_at'] . '</span></div>
        <div class="txn-row"><span class="k">Status</span><span class="v" style="color:#1D9E75">SUCCESS</span></div>
      </div>
    </div>
    <div class="seal">
      <div class="sig">' . $ngoName . '</div>
      <div class="sig-label">Authorised Signatory</div>
    </div>
  </div>
  <div class="footer">
    <strong>80G Tax Exemption:</strong> This donation qualifies for tax deduction under Section 80G of the Income Tax Act, 1961. Certificate No: ' . $ngo80g . '<br>
    <strong>Note:</strong> Please retain this receipt for your tax records. For queries contact <strong>' . $ngoAddr . '</strong><br>
    This is a computer-generated receipt and does not require a physical signature.
  </div>
</div>
</body>
</html>';
    }

    private static function amountToWords($amount) {
        $ones = array('','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                 'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                 'Seventeen','Eighteen','Nineteen');
        $tens = array('','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety');

        $n = (int)$amount;
        if ($n === 0) return 'Zero Rupees';

        $words = '';
        if ($n >= 100000) { $words .= self::twoDigit((int)($n/100000), $ones, $tens) . ' Lakh '; $n %= 100000; }
        if ($n >= 1000)   { $words .= self::twoDigit((int)($n/1000),   $ones, $tens) . ' Thousand '; $n %= 1000; }
        if ($n >= 100)    { $words .= $ones[(int)($n/100)] . ' Hundred '; $n %= 100; }
        if ($n > 0)       { $words .= self::twoDigit($n, $ones, $tens); }

        return trim($words) . ' Rupees';
    }

    private static function twoDigit($n, $ones, $tens) {
        if ($n < 20) return $ones[$n];
        return $tens[(int)($n/10)] . ($n % 10 ? ' ' . $ones[$n % 10] : '');
    }
}

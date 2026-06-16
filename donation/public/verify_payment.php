<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/razorpay.php';
require_once __DIR__ . '/../includes/receipt.php';
require_once __DIR__ . '/../includes/mailer.php';

secureSession();
setSecurityHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(array('error' => 'Method not allowed.'), 405);
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!$input) jsonResponse(array('error' => 'Invalid request.'), 400);

if (!verifyCsrfToken(isset($input['csrf_token']) ? $input['csrf_token'] : '')) {
    jsonResponse(array('error' => 'Invalid session.'), 403);
}

$donationId = (int)(isset($input['donation_id']) ? $input['donation_id'] : 0);
$orderId    = cleanStr(isset($input['razorpay_order_id'])   ? $input['razorpay_order_id']   : '', 60);
$paymentId  = cleanStr(isset($input['razorpay_payment_id']) ? $input['razorpay_payment_id'] : '', 60);
$signature  = cleanStr(isset($input['razorpay_signature'])  ? $input['razorpay_signature']  : '', 128);

if (!$donationId || !$orderId || !$paymentId || !$signature) {
    jsonResponse(array('error' => 'Missing payment parameters.'), 422);
}

function fetchDonation($db, $id, $orderId) {
    $s = $db->prepare("
        SELECT d.*, dn.full_name, dn.email, dn.phone, dn.pan_number
        FROM   donations d
        JOIN   donors    dn ON dn.id = d.donor_id
        WHERE  d.id = ? AND d.razorpay_order_id = ?
        LIMIT  1
    ");
    $s->execute(array($id, $orderId));
    return $s->fetch(PDO::FETCH_ASSOC);
}

try {
    $db = Database::get();

    $donation = fetchDonation($db, $donationId, $orderId);

    if (!$donation) {
        jsonResponse(array('error' => 'Donation record not found.'), 404);
    }

    if ($donation['payment_status'] === 'paid') {
        jsonResponse(array(
            'success'     => true,
            'receipt_url' => !empty($donation['receipt_path'])
                             ? APP_URL . '/public/download.php?token=' . urlencode($donation['razorpay_payment_id'])
                             : null,
        ));
    }

    if (!Razorpay::verifySignature($orderId, $paymentId, $signature)) {
        logEvent('warning', 'Signature mismatch', array(
            'donation_id' => $donationId,
            'payment_id'  => $paymentId,
        ));
        $db->prepare("UPDATE donations SET payment_status='failed' WHERE id=?")
           ->execute(array($donationId));
        jsonResponse(array('error' => 'Payment verification failed. Contact support.'), 400);
    }

    $db->prepare("
        UPDATE donations SET
          razorpay_payment_id = ?,
          razorpay_signature  = ?,
          payment_status      = 'paid',
          payment_method      = 'razorpay'
        WHERE id = ?
    ")->execute(array($paymentId, $signature, $donationId));

    $donation = fetchDonation($db, $donationId, $orderId);

    if (!$donation) {
        jsonResponse(array('error' => 'Could not reload donation after update.'), 500);
    }

    $receiptPath = null;
    try {
        $donor = array(
            'full_name'  => $donation['full_name'],
            'email'      => $donation['email'],
            'phone'      => $donation['phone'],
            'pan_number' => isset($donation['pan_number']) ? $donation['pan_number'] : '',
        );
        $receiptPath = ReceiptGenerator::generate($donation, $donor);
        $db->prepare("UPDATE donations SET receipt_path=? WHERE id=?")
           ->execute(array($receiptPath, $donationId));
        logEvent('info', 'Receipt generated', array('path' => $receiptPath));
    } catch (Exception $e) {
        logEvent('error', 'Receipt generation failed', array('msg' => $e->getMessage()));
    }

    try {
        Mailer::sendReceiptEmail(
            array('full_name' => $donation['full_name'], 'email' => $donation['email']),
            $donation,
            $receiptPath ? $receiptPath : ''
        );
    } catch (Exception $e) {
        logEvent('error', 'Email send failed', array('msg' => $e->getMessage()));
    }

    logEvent('info', 'Payment verified', array(
        'donation_id' => $donationId,
        'payment_id'  => $paymentId,
        'amount'      => $donation['amount'],
    ));

    jsonResponse(array(
        'success'     => true,
        'receipt_url' => $receiptPath
                         ? APP_URL . '/public/download.php?token=' . urlencode($donation['razorpay_payment_id'])
                         : null,
    ));

} catch (Exception $e) {
    logEvent('error', 'verify_payment exception', array('msg' => $e->getMessage()));
    jsonResponse(array('error' => 'Server error during verification.'), 500);
}

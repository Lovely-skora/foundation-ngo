<?php
/**
 * POST /webhook.php
 * Razorpay sends events here directly (server-to-server).
 * Set this URL in Razorpay Dashboard → Webhooks.
 * Add webhook secret in config.php as RAZORPAY_KEY_SECRET.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/razorpay.php';
require_once __DIR__ . '/../includes/receipt.php';
require_once __DIR__ . '/../includes/mailer.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

$payload   = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

// Verify webhook signature
if (!Razorpay::verifyWebhookSignature($payload, $signature)) {
    logEvent('warning', 'Webhook signature mismatch');
    http_response_code(400);
    echo json_encode(['error' => 'Invalid signature']);
    exit;
}

$event = json_decode($payload, true);
if (!$event) { http_response_code(400); exit; }

$eventName = $event['event'] ?? '';
logEvent('info', 'Webhook received', ['event' => $eventName]);

try {
    $db = Database::get();

    switch ($eventName) {

        case 'payment.captured':
            $payment = $event['payload']['payment']['entity'] ?? [];
            $orderId = $payment['order_id'] ?? '';
            $payId   = $payment['id'] ?? '';

            if (!$orderId) break;

            $stmt = $db->prepare("SELECT * FROM donations WHERE razorpay_order_id=? LIMIT 1");
            $stmt->execute([$orderId]);
            $donation = $stmt->fetch();

            if ($donation && $donation['payment_status'] !== 'paid') {
                $db->prepare("
                    UPDATE donations SET
                      razorpay_payment_id = ?,
                      payment_status      = 'paid',
                      payment_method      = ?
                    WHERE razorpay_order_id = ?
                ")->execute([
                    $payId,
                    $payment['method'] ?? 'razorpay',
                    $orderId,
                ]);
                logEvent('info', 'Payment captured via webhook', ['order' => $orderId]);
            }
            break;

        case 'payment.failed':
            $payment = $event['payload']['payment']['entity'] ?? [];
            $orderId = $payment['order_id'] ?? '';
            if ($orderId) {
                $db->prepare("UPDATE donations SET payment_status='failed' WHERE razorpay_order_id=? AND payment_status='created'")
                   ->execute([$orderId]);
                logEvent('info', 'Payment failed via webhook', ['order' => $orderId]);
            }
            break;

        case 'refund.created':
            $refund  = $event['payload']['refund']['entity'] ?? [];
            $payId   = $refund['payment_id'] ?? '';
            if ($payId) {
                $db->prepare("UPDATE donations SET payment_status='refunded' WHERE razorpay_payment_id=?")
                   ->execute([$payId]);
                logEvent('info', 'Refund created via webhook', ['payment_id' => $payId]);
            }
            break;
    }

    http_response_code(200);
    echo json_encode(['status' => 'ok']);

} catch (Throwable $e) {
    logEvent('error', 'Webhook handler exception', ['msg' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}

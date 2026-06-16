<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';

class Razorpay {

    public static function createOrder($amount, $receipt) {
        $payload = array(
            'amount'          => (int)($amount * 100),
            'currency'        => 'INR',
            'receipt'         => $receipt,
            'payment_capture' => 1,
            'notes'           => array('source' => APP_NAME),
        );

        $response = self::apiCall('POST', '/orders', $payload);

        if (empty($response['id'])) {
            logEvent('error', 'Razorpay order creation failed', $response);
            throw new RuntimeException('Payment gateway error. Please try again.');
        }
        return $response;
    }

    public static function verifySignature($orderId, $paymentId, $signature) {
        $expected = hash_hmac(
            'sha256',
            $orderId . '|' . $paymentId,
            RAZORPAY_KEY_SECRET
        );
        return hash_equals($expected, $signature);
    }

    public static function verifyWebhookSignature($payload, $signature) {
        $expected = hash_hmac('sha256', $payload, RAZORPAY_KEY_SECRET);
        return hash_equals($expected, $signature);
    }

    private static function apiCall($method, $endpoint, $data = array()) {
        $url = 'https://api.razorpay.com/v1' . $endpoint;
        $ch  = curl_init($url);

        // FIX 6: Removed XAMPP-specific cert path — on production server, system CA bundle is used
        // SSL verification always ON in production
        $sslVerify = (APP_ENV === 'production');
        $caBundle  = null;

        // Use bundled cacert.pem if present (for shared hosting without updated CA bundle)
        $localCert = __DIR__ . '/cacert.pem';
        if (file_exists($localCert) && filesize($localCert) > 1000) {
            $sslVerify = true;
            $caBundle  = $localCert;
        }

        $curlOpts = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
            CURLOPT_HTTPHEADER     => array('Content-Type: application/json'),
            CURLOPT_SSL_VERIFYPEER => $sslVerify,
            CURLOPT_SSL_VERIFYHOST => $sslVerify ? 2 : 0,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        );

        if ($caBundle) {
            $curlOpts[CURLOPT_CAINFO] = $caBundle;
        }

        curl_setopt_array($ch, $curlOpts);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            logEvent('error', 'Razorpay cURL error', array('err' => $curlErr));
            throw new RuntimeException('Network error contacting payment gateway.');
        }

        $decoded = json_decode($result, true);
        if (!$decoded) $decoded = array();
        if ($httpCode >= 400) {
            logEvent('error', 'Razorpay API error', array('code' => $httpCode, 'body' => $decoded));
        }
        return $decoded;
    }
}

<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/razorpay.php';

secureSession();
setSecurityHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(array('error' => 'Method not allowed.'), 405);
}

$raw   = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!$input) jsonResponse(array('error' => 'Invalid request body.'), 400);

if (!verifyCsrfToken(isset($input['csrf_token']) ? $input['csrf_token'] : '')) {
    jsonResponse(array('error' => 'Invalid session. Please refresh the page.'), 403);
}

if (!checkRateLimit()) {
    jsonResponse(array('error' => 'Too many requests. Please wait a few minutes.'), 429);
}

$errors = array();

$name  = cleanStr(isset($input['full_name']) ? $input['full_name'] : '', 120);
$email = cleanEmail(isset($input['email']) ? $input['email'] : '');
$phone = cleanPhone(isset($input['phone']) ? $input['phone'] : '');
$city  = cleanStr(isset($input['city']) ? $input['city'] : '', 80);
$state = cleanStr(isset($input['state']) ? $input['state'] : '', 80);
$pan   = !empty($input['pan_number']) ? cleanPan($input['pan_number']) : null;
$amt   = cleanAmount(isset($input['amount']) ? $input['amount'] : 0);

$donationType = in_array(isset($input['donation_type']) ? $input['donation_type'] : '', array('one-time','monthly'), true)
                ? $input['donation_type'] : 'one-time';

$allowedCampaigns = array('General Fund','Feed Children','Plant Trees','Education for All','Clean Water');
$campaign = in_array(isset($input['campaign']) ? $input['campaign'] : '', $allowedCampaigns, true)
            ? $input['campaign'] : 'General Fund';

$message  = cleanStr(isset($input['message']) ? $input['message'] : '', 300);
$wants80g = isset($input['wants_80g']) ? (int)(bool)$input['wants_80g'] : 1;

if (!$name)   $errors[] = 'Full name is required.';
if (!$email)  $errors[] = 'Valid email is required.';
if (!$phone)  $errors[] = 'Valid mobile number is required.';
if (!$amt)    $errors[] = 'Valid donation amount required.';
if ($pan === false) $errors[] = 'Invalid PAN format (e.g. ABCDE1234F).';

if ($errors) {
    jsonResponse(array('error' => implode(' ', $errors)), 422);
}

try {
    $db = Database::get();

    $stmt = $db->prepare("SELECT id FROM donors WHERE email = ? LIMIT 1");
    $stmt->execute(array($email));
    $donor = $stmt->fetch();

    if ($donor) {
        $donorId = $donor['id'];
        $db->prepare("UPDATE donors SET full_name=?, phone=?, city=?, state=?, pan_number=? WHERE id=?")
           ->execute(array($name, $phone, $city, $state, $pan, $donorId));
    } else {
        $db->prepare("INSERT INTO donors (full_name, email, phone, city, state, pan_number) VALUES (?,?,?,?,?,?)")
           ->execute(array($name, $email, $phone, $city, $state, $pan));
        $donorId = (int)$db->lastInsertId();
    }

    $receipt  = 'DON-' . $donorId . '-' . time();
    $rzpOrder = Razorpay::createOrder((float)$amt, $receipt);

    $db->prepare("
        INSERT INTO donations
          (donor_id, razorpay_order_id, amount, donation_type, campaign, message, wants_80g, payment_status, ip_address)
        VALUES (?,?,?,?,?,?,?,'created',?)
    ")->execute(array(
        $donorId,
        $rzpOrder['id'],
        $amt,
        $donationType,
        $campaign,
        $message,
        $wants80g,
        getClientIp(),
    ));
    $donationId = (int)$db->lastInsertId();

    logEvent('info', 'Order created', array(
        'donation_id' => $donationId,
        'order_id'    => $rzpOrder['id'],
        'amount'      => $amt,
    ));

    jsonResponse(array(
        'order_id'    => $rzpOrder['id'],
        'amount'      => $rzpOrder['amount'],
        'donation_id' => $donationId,
    ));

} catch (Exception $e) {
    logEvent('error', 'create_order exception', array('msg' => $e->getMessage()));
    jsonResponse(array('error' => 'Server error. Please try again.'), 500);
}

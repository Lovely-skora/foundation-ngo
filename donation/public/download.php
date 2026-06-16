<?php
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';

setSecurityHeaders();

$token = cleanStr(isset($_GET['token']) ? $_GET['token'] : '', 60);

if (!$token) {
    http_response_code(403);
    die('Access denied.');
}
$db   = Database::get();
$stmt = $db->prepare("
    SELECT d.receipt_path, dn.full_name
    FROM   donations d
    JOIN   donors dn ON dn.id = d.donor_id
    WHERE  d.razorpay_payment_id = ?
    AND    d.payment_status = 'paid'
    LIMIT  1
");
$stmt->execute(array($token));
$row = $stmt->fetch();

if (!$row || empty($row['receipt_path'])) {
    http_response_code(404);
    die('Receipt not found.');
}

$path = $row['receipt_path'];

if (!file_exists($path)) {
    http_response_code(404);
    die('Receipt file missing.');
}

header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="donation_receipt.html"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;

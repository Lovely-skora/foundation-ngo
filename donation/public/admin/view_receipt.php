<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();

$id   = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
if (!$id) { http_response_code(400); die('Invalid request.'); }

$db   = Database::get();
$stmt = $db->prepare("SELECT receipt_path FROM donations WHERE id = ? AND payment_status = 'paid' LIMIT 1");
$stmt->execute(array($id));
$row  = $stmt->fetch();

if (!$row || empty($row['receipt_path']) || !file_exists($row['receipt_path'])) {
    http_response_code(404);
    die('Receipt not found.');
}

header('Content-Type: text/html; charset=UTF-8');
readfile($row['receipt_path']);
exit;

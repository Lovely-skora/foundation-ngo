<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();

$db     = Database::get();
$status   = in_array(isset($_GET['status']) ? $_GET['status'] : '', array('','created','paid','failed','refunded')) ? (isset($_GET['status']) ? $_GET['status'] : '') : '';
$campaign = cleanStr(isset($_GET['campaign']) ? $_GET['campaign'] : '', 100);
$search   = cleanStr(isset($_GET['search']) ? $_GET['search'] : '', 100);
$from     = cleanStr(isset($_GET['from']) ? $_GET['from'] : '', 20);
$to       = cleanStr(isset($_GET['to']) ? $_GET['to'] : '', 20);

$where  = 'WHERE 1';
$params = array();
if ($status)   { $where .= ' AND d.payment_status = ?';  $params[] = $status; }
if ($campaign) { $where .= ' AND d.campaign = ?';         $params[] = $campaign; }
if ($search)   { $where .= ' AND (dn.full_name LIKE ? OR dn.email LIKE ? OR dn.phone LIKE ?)';
                 $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($from)     { $where .= ' AND DATE(d.created_at) >= ?'; $params[] = $from; }
if ($to)       { $where .= ' AND DATE(d.created_at) <= ?'; $params[] = $to; }

$stmt = $db->prepare("
    SELECT d.id, d.created_at, dn.full_name, dn.email, dn.phone, dn.pan_number,
           dn.city, dn.state, d.amount, d.campaign, d.donation_type,
           d.payment_status, d.razorpay_payment_id, d.razorpay_order_id,
           d.payment_method, d.wants_80g
    FROM donations d JOIN donors dn ON dn.id = d.donor_id
    $where ORDER BY d.created_at DESC
");
$stmt->execute($params);
$rows = $stmt->fetchAll();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="donations_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

fputcsv($out, array('ID','Date','Name','Email','Phone','PAN','City','State','Amount','Campaign','Type','Status','Payment ID','Order ID','Method','80G'));
foreach ($rows as $r) {
    fputcsv($out, array(
        $r['id'], $r['created_at'], $r['full_name'], $r['email'], $r['phone'],
        $r['pan_number'], $r['city'], $r['state'], $r['amount'], $r['campaign'],
        $r['donation_type'], $r['payment_status'], $r['razorpay_payment_id'],
        $r['razorpay_order_id'], $r['payment_method'], $r['wants_80g'] ? 'Yes' : 'No'
    ));
}
fclose($out);
exit;

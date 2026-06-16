<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();
setSecurityHeaders();

$db   = Database::get();
$page = max(1,(int)(isset($_GET['page'])?$_GET['page']:1));
$perPage = 25; $offset = ($page-1)*$perPage;

$countStmt = $db->query("SELECT COUNT(*) FROM donations WHERE payment_status='paid'");
$total = (int)$countStmt->fetchColumn();
$pages = max(1,ceil($total/$perPage));

$stmt = $db->prepare("
    SELECT d.id, d.amount, d.campaign, d.created_at, d.receipt_path,
           d.razorpay_payment_id, dn.full_name, dn.email, dn.pan_number
    FROM donations d JOIN donors dn ON dn.id=d.donor_id
    WHERE d.payment_status='paid'
    ORDER BY d.created_at DESC LIMIT ? OFFSET ?
");
$stmt->execute(array($perPage,$offset));
$rows = $stmt->fetchAll();

$pageTitle='Receipts'; $pageSubtitle='All generated receipts'; $activePage='receipts';
include __DIR__ . '/layout.php';
?>
<div class="card">
  <div class="card-header"><h3>Receipts <span style="color:#aaa;font-weight:400">(<?= $total ?>)</span></h3></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Date</th><th>Donor</th><th>Email</th><th>PAN</th><th>Amount</th><th>Campaign</th><th>Payment ID</th><th>Receipt</th></tr>
      </thead>
      <tbody>
      <?php if ($rows): foreach ($rows as $r): ?>
        <tr>
          <td style="color:#ccc;font-size:11px"><?= $r['id'] ?></td>
          <td style="font-size:12px"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
          <td><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
          <td style="font-size:13px"><?= htmlspecialchars($r['email']) ?></td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($r['pan_number']?$r['pan_number']:'—') ?></td>
          <td><strong style="color:#0F6E56">₹<?= number_format($r['amount'],2) ?></strong></td>
          <td style="font-size:12px"><?= htmlspecialchars($r['campaign']) ?></td>
          <td style="font-family:monospace;font-size:11px;color:#aaa"><?= htmlspecialchars($r['razorpay_payment_id']?$r['razorpay_payment_id']:'—') ?></td>
          <td>
            <?php if ($r['receipt_path'] && file_exists($r['receipt_path'])): ?>
              <a href="view_receipt.php?id=<?= $r['id'] ?>" target="_blank" class="btn btn-receipt btn-sm">🧾 View</a>
            <?php else: ?>
              <span style="color:#ddd;font-size:12px">Not generated</span>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr class="empty-row"><td colspan="9">No receipts found.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div class="pagination">
    <?php for ($i=1;$i<=$pages;$i++): ?>
      <a href="?page=<?= $i ?>" class="<?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor ?>
  </div>
  <?php endif ?>
</div>
<?php include __DIR__ . '/layout_footer.php'; ?>

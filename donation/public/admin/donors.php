<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();
setSecurityHeaders();

$db     = Database::get();
$search = cleanStr(isset($_GET['search']) ? $_GET['search'] : '', 100);
$page   = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage = 25; $offset = ($page-1)*$perPage;

$where = 'WHERE 1'; $params = array();
if ($search) {
    $where .= ' AND (dn.full_name LIKE ? OR dn.email LIKE ? OR dn.phone LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}

$countStmt = $db->prepare("SELECT COUNT(DISTINCT dn.id) FROM donors dn $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, ceil($total/$perPage));

$rowParams = array_merge($params, array($perPage, $offset));
$stmt = $db->prepare("
    SELECT dn.*,
           COUNT(d.id) AS total_donations,
           COALESCE(SUM(CASE WHEN d.payment_status='paid' THEN d.amount END),0) AS total_amount,
           MAX(d.created_at) AS last_donation
    FROM donors dn LEFT JOIN donations d ON d.donor_id = dn.id
    $where GROUP BY dn.id ORDER BY dn.created_at DESC LIMIT ? OFFSET ?
");
$stmt->execute($rowParams);
$rows = $stmt->fetchAll();

$pageTitle = 'Donors'; $pageSubtitle = 'All registered donors';
$activePage = 'donors';
include __DIR__ . '/layout.php';
?>

<form method="GET" class="filter-bar">
  <input type="text" name="search" placeholder="🔍 Search name, email, phone" value="<?= htmlspecialchars($search) ?>">
  <button type="submit" class="btn btn-primary">Search</button>
  <a href="donors.php" class="btn btn-outline">Reset</a>
</form>

<div class="card">
  <div class="card-header"><h3>Donors <span style="color:#aaa;font-weight:400">(<?= $total ?>)</span></h3></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>PAN</th><th>City/State</th><th>Total Donated</th><th>Donations</th><th>Last Donation</th></tr>
      </thead>
      <tbody>
      <?php if ($rows): foreach ($rows as $r): ?>
        <tr>
          <td style="color:#ccc;font-size:11px"><?= $r['id'] ?></td>
          <td><strong><?= htmlspecialchars($r['full_name']) ?></strong></td>
          <td style="font-size:13px"><?= htmlspecialchars($r['email']) ?></td>
          <td style="font-size:13px"><?= htmlspecialchars($r['phone']) ?></td>
          <td style="font-family:monospace;font-size:12px"><?= htmlspecialchars($r['pan_number'] ? $r['pan_number'] : '—') ?></td>
          <td style="font-size:12px;color:#888"><?= htmlspecialchars(($r['city']?$r['city']:'') . ($r['state']?', '.$r['state']:'')) ?></td>
          <td><strong style="color:#0F6E56">₹<?= number_format($r['total_amount'],2) ?></strong></td>
          <td style="text-align:center"><?= $r['total_donations'] ?></td>
          <td style="font-size:12px;color:#aaa"><?= $r['last_donation'] ? date('d M Y', strtotime($r['last_donation'])) : '—' ?></td>
        </tr>
      <?php endforeach; else: ?>
        <tr class="empty-row"><td colspan="9">No donors found.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div class="pagination">
    <?php for ($i=1;$i<=$pages;$i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor ?>
  </div>
  <?php endif ?>
</div>

<?php include __DIR__ . '/layout_footer.php'; ?>

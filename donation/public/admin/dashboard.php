<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();
setSecurityHeaders();

$db = Database::get();

// Filters
$status   = in_array(isset($_GET['status']) ? $_GET['status'] : '', array('','created','paid','failed','refunded')) ? (isset($_GET['status']) ? $_GET['status'] : '') : '';
$campaign = cleanStr(isset($_GET['campaign']) ? $_GET['campaign'] : '', 100);
$search   = cleanStr(isset($_GET['search']) ? $_GET['search'] : '', 100);
$from     = cleanStr(isset($_GET['from']) ? $_GET['from'] : '', 20);
$to       = cleanStr(isset($_GET['to']) ? $_GET['to'] : '', 20);
$page     = max(1, (int)(isset($_GET['page']) ? $_GET['page'] : 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

$where  = 'WHERE 1';
$params = array();
if ($status)   { $where .= ' AND d.payment_status = ?';  $params[] = $status; }
if ($campaign) { $where .= ' AND d.campaign = ?';         $params[] = $campaign; }
if ($search)   { $where .= ' AND (dn.full_name LIKE ? OR dn.email LIKE ? OR dn.phone LIKE ? OR d.razorpay_payment_id LIKE ?)';
                 $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($from)     { $where .= ' AND DATE(d.created_at) >= ?'; $params[] = $from; }
if ($to)       { $where .= ' AND DATE(d.created_at) <= ?'; $params[] = $to; }

$countStmt = $db->prepare("SELECT COUNT(*) FROM donations d JOIN donors dn ON dn.id = d.donor_id $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, ceil($total / $perPage));

$rowParams = array_merge($params, array($perPage, $offset));
$stmt = $db->prepare("
    SELECT d.id, d.amount, d.donation_type, d.campaign, d.payment_status,
           d.razorpay_payment_id, d.razorpay_order_id, d.receipt_path,
           d.created_at, d.payment_method, d.wants_80g, d.message,
           dn.full_name, dn.email, dn.phone, dn.pan_number, dn.city, dn.state
    FROM donations d JOIN donors dn ON dn.id = d.donor_id
    $where ORDER BY d.created_at DESC LIMIT ? OFFSET ?
");
$stmt->execute($rowParams);
$rows = $stmt->fetchAll();

$stats = $db->query("
    SELECT
      COALESCE(SUM(CASE WHEN payment_status='paid' THEN amount END),0) AS total_amount,
      COUNT(CASE WHEN payment_status='paid' THEN 1 END)    AS paid_count,
      COUNT(CASE WHEN payment_status='failed' THEN 1 END)  AS failed_count,
      COUNT(CASE WHEN payment_status='created' THEN 1 END) AS pending_count,
      COUNT(*) AS total_count
    FROM donations
")->fetch();

$monthly = $db->query("
    SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
           COALESCE(SUM(CASE WHEN payment_status='paid' THEN amount ELSE 0 END),0) AS amount,
           COUNT(CASE WHEN payment_status='paid' THEN 1 END) AS cnt
    FROM donations
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at,'%Y-%m')
    ORDER BY MIN(created_at)
")->fetchAll();

$pageTitle    = 'Dashboard';
$pageSubtitle = 'All donations overview — ' . date('d M Y');
$activePage   = 'dashboard';
include __DIR__ . '/layout.php';
?>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card" data-icon="💰">
    <div class="stat-val">₹<?= number_format($stats['total_amount'],2) ?></div>
    <div class="stat-lbl">Total Collected</div>
    <div class="stat-sub"><?= $stats['paid_count'] ?> successful donations</div>
  </div>
  <div class="stat-card" data-icon="✅">
    <div class="stat-val"><?= $stats['paid_count'] ?></div>
    <div class="stat-lbl">Successful</div>
  </div>
  <div class="stat-card" data-icon="⏳">
    <div class="stat-val"><?= $stats['pending_count'] ?></div>
    <div class="stat-lbl">Pending</div>
  </div>
  <div class="stat-card" data-icon="❌">
    <div class="stat-val"><?= $stats['failed_count'] ?></div>
    <div class="stat-lbl">Failed</div>
  </div>
</div>

<!-- Monthly Chart -->
<?php if ($monthly): ?>
<div class="card">
  <div class="card-header"><h3>📈 Monthly Donations (Last 6 months)</h3></div>
  <div class="card-body">
    <?php $maxAmt = 1; foreach ($monthly as $m) { if ($m['amount'] > $maxAmt) $maxAmt = $m['amount']; } ?>
    <div class="bar-chart">
      <?php foreach ($monthly as $m): ?>
        <?php $h = max(4, round(($m['amount'] / $maxAmt) * 110)); ?>
        <div class="bar-wrap">
          <div class="bar-val">₹<?= $m['amount'] >= 1000 ? number_format($m['amount']/1000,1).'k' : $m['amount'] ?></div>
          <div class="bar" style="height:<?= $h ?>px" title="<?= $m['month'] ?>: ₹<?= number_format($m['amount'],2) ?> (<?= $m['cnt'] ?> donations)"></div>
          <div class="bar-label"><?= $m['month'] ?></div>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>
<?php endif ?>

<!-- Filters -->
<form method="GET" class="filter-bar">
  <input type="text" name="search" placeholder="🔍 Name, email, phone, payment ID" value="<?= htmlspecialchars($search) ?>">
  <select name="status">
    <option value="">All Status</option>
    <?php foreach (array('paid','created','failed','refunded') as $s): ?>
      <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
    <?php endforeach ?>
  </select>
  <select name="campaign">
    <option value="">All Campaigns</option>
    <?php foreach (array('General Fund','Feed Children','Plant Trees','Education for All','Clean Water') as $c): ?>
      <option value="<?= $c ?>" <?= $campaign===$c?'selected':'' ?>><?= $c ?></option>
    <?php endforeach ?>
  </select>
  <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" title="From date">
  <input type="date" name="to"   value="<?= htmlspecialchars($to) ?>"   title="To date">
  <button type="submit" class="btn btn-primary">Filter</button>
  <a href="dashboard.php" class="btn btn-outline">Reset</a>
  <a href="export.php?<?= htmlspecialchars(http_build_query(array('status'=>$status,'campaign'=>$campaign,'search'=>$search,'from'=>$from,'to'=>$to))) ?>" class="btn btn-outline" target="_blank">⬇ CSV</a>
</form>

<!-- Table -->
<div class="card">
  <div class="card-header">
    <h3>All Donations <span style="color:#aaa;font-weight:400">(<?= $total ?>)</span></h3>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date</th>
          <th>Donor</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Amount</th>
          <th>Campaign</th>
          <th>Type</th>
          <th>Status</th>
          <th>Payment ID</th>
          <th>80G</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($rows): foreach ($rows as $r): ?>
        <tr>
          <td style="color:#ccc;font-size:11px"><?= $r['id'] ?></td>
          <td style="white-space:nowrap">
            <div style="font-size:13px"><?= date('d M Y', strtotime($r['created_at'])) ?></div>
            <div style="font-size:11px;color:#bbb"><?= date('H:i', strtotime($r['created_at'])) ?></div>
          </td>
          <td>
            <div style="font-weight:500"><?= htmlspecialchars($r['full_name']) ?></div>
            <div style="font-size:11px;color:#bbb"><?= htmlspecialchars(($r['city'] ? $r['city'] : '') . ($r['state'] ? ', '.$r['state'] : '')) ?></div>
          </td>
          <td style="font-size:13px"><?= htmlspecialchars($r['email']) ?></td>
          <td style="font-size:13px"><?= htmlspecialchars($r['phone']) ?></td>
          <td><strong style="color:#0F6E56">₹<?= number_format($r['amount'],2) ?></strong></td>
          <td style="font-size:12px"><?= htmlspecialchars($r['campaign']) ?></td>
          <td style="font-size:12px"><?= $r['donation_type'] ?></td>
          <td><span class="badge badge-<?= $r['payment_status'] ?>"><?= ucfirst($r['payment_status']) ?></span></td>
          <td style="font-family:monospace;font-size:11px;color:#aaa;max-width:140px;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($r['razorpay_payment_id'] ? $r['razorpay_payment_id'] : '—') ?></td>
          <td style="text-align:center"><?= $r['wants_80g'] ? '✅' : '—' ?></td>
          <td style="white-space:nowrap">
            <button class="btn btn-outline btn-sm" onclick='openModal(<?= htmlspecialchars(json_encode($r), ENT_QUOTES) ?>)'>👁 View</button>
            <?php if ($r['receipt_path'] && file_exists($r['receipt_path'])): ?>
              <a href="view_receipt.php?id=<?= $r['id'] ?>" target="_blank" class="btn btn-receipt btn-sm">🧾</a>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr class="empty-row"><td colspan="12">No records found.</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
  <?php if ($pages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&status=<?= urlencode($status) ?>&campaign=<?= urlencode($campaign) ?>&search=<?= urlencode($search) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>">← Prev</a><?php endif ?>
    <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
      <a href="?page=<?= $i ?>&status=<?= urlencode($status) ?>&campaign=<?= urlencode($campaign) ?>&search=<?= urlencode($search) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>" class="<?= $i===$page?'active':'' ?>"><?= $i ?></a>
    <?php endfor ?>
    <?php if ($page < $pages): ?><a href="?page=<?= $page+1 ?>&status=<?= urlencode($status) ?>&campaign=<?= urlencode($campaign) ?>&search=<?= urlencode($search) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>">Next →</a><?php endif ?>
  </div>
  <?php endif ?>
</div>

<!-- Detail Modal -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <div class="modal-header">
      <h3>Donation Details</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="modal-body"></div>
  </div>
</div>

<script>
function openModal(r) {
  var colors = {paid:'#0F6E56',failed:'#B91C1C',created:'#92400E',refunded:'#1D4ED8'};
  document.getElementById('modal-body').innerHTML =
    '<div class="detail-grid">' +
      row('Donor Name', r.full_name) + row('Email', r.email) +
      row('Phone', r.phone) + row('PAN', r.pan_number || '—') +
      row('City / State', ((r.city||'') + (r.state ? ', '+r.state : '')) || '—') +
      '<div class="detail-item"><div class="dk">Amount</div><div class="dv" style="color:#0F6E56;font-size:20px">₹' + parseFloat(r.amount).toLocaleString('en-IN',{minimumFractionDigits:2}) + '</div></div>' +
    '</div>' +
    '<div class="divider"></div>' +
    '<div class="detail-grid">' +
      row('Campaign', r.campaign) + row('Type', r.donation_type) +
      '<div class="detail-item"><div class="dk">Status</div><div class="dv" style="color:' + (colors[r.payment_status]||'#333') + '">' + r.payment_status.toUpperCase() + '</div></div>' +
      row('80G Receipt', r.wants_80g == 1 ? '✅ Requested' : '—') +
    '</div>' +
    '<div class="divider"></div>' +
    '<div class="detail-grid">' +
      '<div class="detail-item"><div class="dk">Order ID</div><div class="dv" style="font-family:monospace;font-size:11px">' + e(r.razorpay_order_id) + '</div></div>' +
      '<div class="detail-item"><div class="dk">Payment ID</div><div class="dv" style="font-family:monospace;font-size:11px">' + e(r.razorpay_payment_id||'—') + '</div></div>' +
      row('Method', r.payment_method||'—') + row('Date & Time', r.created_at) +
    '</div>' +
    (r.message ? '<div class="divider"></div>' + row('Message', r.message) : '');
  document.getElementById('modal').classList.add('open');
}
function row(k,v){ return '<div class="detail-item"><div class="dk">'+k+'</div><div class="dv">'+e(v)+'</div></div>'; }
function closeModal(){ document.getElementById('modal').classList.remove('open'); }
function e(s){ return s ? String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : '—'; }
document.getElementById('modal').addEventListener('click', function(ev){ if(ev.target===this) closeModal(); });
</script>

<?php include __DIR__ . '/layout_footer.php'; ?>

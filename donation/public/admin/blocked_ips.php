<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();
setSecurityHeaders();

$db = Database::get();

// Ensure tables exist
$db->exec("
    CREATE TABLE IF NOT EXISTS admin_login_attempts (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        username VARCHAR(80) NOT NULL,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_ip (ip_address)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS admin_blocked_ips (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL UNIQUE,
        username VARCHAR(80),
        reason VARCHAR(255) DEFAULT '3 failed login attempts',
        blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        unblocked_at TIMESTAMP NULL,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unblock_id'])) {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        die('Invalid request.');
    }
    $id = (int)$_POST['unblock_id'];
    $stmt = $db->prepare("SELECT ip_address FROM admin_blocked_ips WHERE id = ?");
    $stmt->execute(array($id));
    $row = $stmt->fetch();

    if ($row) {
        $db->prepare("UPDATE admin_blocked_ips SET is_active=0, unblocked_at=NOW() WHERE id=?")
           ->execute(array($id));
        $db->prepare("DELETE FROM admin_login_attempts WHERE ip_address=?")
           ->execute(array($row['ip_address']));
        logEvent('info', 'IP unblocked', array('ip'=>$row['ip_address'],'admin'=>$_SESSION['admin_id']));
        $success = 'IP ' . htmlspecialchars($row['ip_address']) . ' unblocked successfully!';
    }
}

$blocked = $db->query("SELECT * FROM admin_blocked_ips ORDER BY is_active DESC, blocked_at DESC")->fetchAll();
$activeCount = 0;
foreach ($blocked as $b) { if ($b['is_active']) $activeCount++; }

$csrf = generateCsrfToken();
$pageTitle = 'Blocked IPs'; $pageSubtitle = 'Manage blocked IP addresses'; $activePage = 'blocked';
include __DIR__ . '/layout.php';
?>

<?php if ($success): ?>
  <div class="alert alert-success">✅ <?= $success ?></div>
<?php endif ?>

<div class="alert alert-info">
  ℹ️ An IP is permanently blocked after <strong>3 failed login attempts</strong>. Click Unblock to restore access.
</div>

<div class="card">
  <div class="card-header">
    <h3>Blocked IPs <span style="color:#aaa;font-weight:400">(<?= $activeCount ?> active)</span></h3>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>IP Address</th><th>Username tried</th><th>Blocked at</th><th>Unblocked at</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
      <?php if ($blocked): foreach ($blocked as $r): ?>
        <tr>
          <td style="font-family:monospace;font-weight:500"><?= htmlspecialchars($r['ip_address']) ?></td>
          <td><?= htmlspecialchars($r['username'] ? $r['username'] : '—') ?></td>
          <td style="font-size:12px;color:#888"><?= date('d M Y H:i', strtotime($r['blocked_at'])) ?></td>
          <td style="font-size:12px;color:#888"><?= $r['unblocked_at'] ? date('d M Y H:i', strtotime($r['unblocked_at'])) : '—' ?></td>
          <td>
            <?php if ($r['is_active']): ?>
              <span class="badge badge-failed">🔴 Blocked</span>
            <?php else: ?>
              <span class="badge badge-paid">✅ Unblocked</span>
            <?php endif ?>
          </td>
          <td>
            <?php if ($r['is_active']): ?>
              <form method="POST" onsubmit="return confirm('Unblock <?= htmlspecialchars($r['ip_address']) ?>?')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="unblock_id" value="<?= $r['id'] ?>">
                <button type="submit" class="btn btn-primary btn-sm">🔓 Unblock</button>
              </form>
            <?php else: ?>
              <span style="color:#ddd;font-size:12px">—</span>
            <?php endif ?>
          </td>
        </tr>
      <?php endforeach; else: ?>
        <tr class="empty-row"><td colspan="6">✅ No blocked IPs — all clear!</td></tr>
      <?php endif ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/layout_footer.php'; ?>

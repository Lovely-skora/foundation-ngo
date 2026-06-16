<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../../includes/db.php';

requireAdminLogin();
setSecurityHeaders();

$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $error = 'Invalid request.';
    } else {
        $current = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $new     = isset($_POST['new_password'])     ? $_POST['new_password']     : '';
        $confirm = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if (strlen($new) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $db   = Database::get();
            $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
            $stmt->execute(array($_SESSION['admin_id']));
            $admin = $stmt->fetch();

            if ($admin && password_verify($current, $admin['password_hash'])) {
                $hash = password_hash($new, PASSWORD_BCRYPT);
                $db->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ?")
                   ->execute(array($hash, $_SESSION['admin_id']));
                $success = 'Password changed successfully!';
                logEvent('info', 'Admin password changed', array('admin_id' => $_SESSION['admin_id']));
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }
}

$csrf = generateCsrfToken();
$pageTitle = 'Change Password'; $activePage = 'password';
include __DIR__ . '/layout.php';
?>

<?php if ($success): ?>
  <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
<?php endif ?>
<?php if ($error): ?>
  <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif ?>

<div class="card" style="max-width:460px">
  <div class="card-header"><h3>🔑 Change Password</h3></div>
  <div class="card-body">
    <form method="POST" action="change_password.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" name="current_password" required>
      </div>
      <div class="form-group">
        <label>New Password <span style="color:#aaa;font-size:12px">(min 8 characters)</span></label>
        <input type="password" name="new_password" required minlength="8">
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">Update Password</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/layout_footer.php'; ?>

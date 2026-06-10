<?php
require 'auth.php';
require 'db.php';
require '_layout.php';

$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $msg = 'Invalid request token. Please try again.';
        $msgType = 'danger';
    } else {
        $current  = $_POST['current_password'] ?? '';
        $new      = $_POST['new_password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $msg = 'Saare fields required hain.';
            $msgType = 'danger';
        } elseif ($new !== $confirm) {
            $msg = 'New password aur confirm password match nahi kar rahe.';
            $msgType = 'danger';
        } elseif (strlen($new) < 8) {
            $msg = 'New password kam se kam 8 characters ka hona chahiye.';
            $msgType = 'danger';
        } elseif (!preg_match('/[A-Z]/', $new) || !preg_match('/[0-9]/', $new) || !preg_match('/[\W_]/', $new)) {
            $msg = 'Password mein ek uppercase letter, ek number, aur ek special character (@#$! etc.) hona chahiye.';
            $msgType = 'danger';
        } else {
            $stmt = $pdo->prepare('SELECT password FROM admin_users WHERE id = ?');
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($current, $admin['password'])) {
                $msg = 'Current password galat hai.';
                $msgType = 'danger';
            } else {
                $hash = password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]);
                $pdo->prepare('UPDATE admin_users SET password = ? WHERE id = ?')
                    ->execute([$hash, $_SESSION['admin_id']]);
                $msg = '✅ Password successfully change ho gaya!';
            }
        }
    }
}

layout_head('Change Password');
layout_nav('');
?>

<div class="page-title">🔑 Change Password</div>

<?php if ($msg): ?>
  <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="card" style="max-width:460px;">
  <div class="card-title">Update Your Password</div>
  <form method="POST" action="">
    <?= csrf_field() ?>

    <div class="form-group">
      <label>Current Password</label>
      <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
    </div>

    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="new_password" id="newPass" class="form-control" required autocomplete="new-password">
      <div id="strengthBar" style="height:4px;border-radius:4px;margin-top:6px;background:#e2e8f0;transition:.3s;">
        <div id="strengthFill" style="height:100%;border-radius:4px;width:0;transition:.3s;"></div>
      </div>
      <div id="strengthLabel" style="font-size:11px;color:#94a3b8;margin-top:4px;"></div>
    </div>

    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" name="confirm_password" id="confirmPass" class="form-control" required autocomplete="new-password">
      <div id="matchMsg" style="font-size:11px;margin-top:4px;"></div>
    </div>

    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:10px 14px;font-size:12px;color:#64748b;margin-bottom:16px;">
      Password requirements: <strong>8+ characters</strong>, ek <strong>uppercase</strong>, ek <strong>number</strong>, ek <strong>special character</strong> (@#$!% etc.)
    </div>

    <button type="submit" class="btn btn-primary">💾 Update Password</button>
    <a href="index.php" class="btn btn-outline" style="margin-left:8px;">Cancel</a>
  </form>
</div>

<script>
const newPass     = document.getElementById('newPass');
const confirmPass = document.getElementById('confirmPass');
const fill        = document.getElementById('strengthFill');
const label       = document.getElementById('strengthLabel');
const matchMsg    = document.getElementById('matchMsg');

newPass.addEventListener('input', function() {
  const v = this.value;
  let score = 0;
  if (v.length >= 8)          score++;
  if (/[A-Z]/.test(v))        score++;
  if (/[0-9]/.test(v))        score++;
  if (/[\W_]/.test(v))        score++;
  if (v.length >= 12)         score++;

  const map = {
    0: ['0%',   '#ef4444', ''],
    1: ['25%',  '#ef4444', 'Weak'],
    2: ['50%',  '#f97316', 'Fair'],
    3: ['75%',  '#eab308', 'Good'],
    4: ['88%',  '#22c55e', 'Strong'],
    5: ['100%', '#16a34a', 'Very Strong'],
  };
  const [w, c, t] = map[score];
  fill.style.width = w;
  fill.style.background = c;
  label.textContent = t;
  label.style.color = c;
  checkMatch();
});

confirmPass.addEventListener('input', checkMatch);

function checkMatch() {
  if (!confirmPass.value) { matchMsg.textContent = ''; return; }
  if (newPass.value === confirmPass.value) {
    matchMsg.textContent = '✅ Passwords match';
    matchMsg.style.color = '#16a34a';
  } else {
    matchMsg.textContent = '❌ Passwords do not match';
    matchMsg.style.color = '#ef4444';
  }
}
</script>

<?php layout_end(); ?>
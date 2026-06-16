<?php
// Common admin layout — include at top of every admin page
// Usage: include layout with $pageTitle and $activePage set
if (!isset($pageTitle)) $pageTitle = 'Admin';
if (!isset($activePage)) $activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($pageTitle) ?> — <?= htmlspecialchars(APP_NAME) ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f4f4f0;color:#1a1a1a;font-size:14px}
a{text-decoration:none;color:inherit}

/* ── Sidebar ── */
.sidebar{position:fixed;top:0;left:0;width:240px;height:100vh;background:#0F6E56;color:#fff;display:flex;flex-direction:column;z-index:100;overflow-y:auto}
.sidebar-logo{padding:22px 20px 18px;border-bottom:1px solid rgba(255,255,255,.12)}
.sidebar-logo .s-icon{font-size:30px}
.sidebar-logo h2{font-size:15px;font-weight:600;margin-top:7px;line-height:1.3;color:#fff}
.sidebar-logo p{font-size:11px;opacity:.55;margin-top:3px}
.sidebar-nav{flex:1;padding:10px 0}
.nav-section{padding:14px 20px 6px;font-size:10px;text-transform:uppercase;letter-spacing:.1em;opacity:.45;font-weight:500}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 20px;font-size:13.5px;color:rgba(255,255,255,.85);cursor:pointer;transition:all .15s;border-left:3px solid transparent;white-space:nowrap}
.nav-item:hover{background:rgba(255,255,255,.09);color:#fff}
.nav-item.active{background:rgba(255,255,255,.13);border-left-color:#5DCAA5;color:#fff}
.nav-item .ni{font-size:15px;width:20px;text-align:center;flex-shrink:0}
.sidebar-footer{padding:14px 20px;border-top:1px solid rgba(255,255,255,.12)}
.sf-name{font-size:13px;font-weight:600;color:#fff}
.sf-user{font-size:11px;opacity:.55;margin-top:2px}
.sf-logout{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px;padding:8px;background:rgba(255,255,255,.1);border-radius:7px;font-size:12px;color:#fff;transition:background .15s}
.sf-logout:hover{background:rgba(255,255,255,.2)}

/* ── Main ── */
.main{margin-left:240px;min-height:100vh;display:flex;flex-direction:column}
.topbar{background:#fff;padding:14px 28px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:50}
.topbar-left h1{font-size:17px;font-weight:600}
.topbar-left .sub{font-size:12px;color:#999;margin-top:2px}
.topbar-right{display:flex;align-items:center;gap:12px}
.topbar-right a{font-size:13px;color:#1D9E75;padding:6px 12px;border:1px solid #1D9E75;border-radius:6px;transition:all .15s}
.topbar-right a:hover{background:#1D9E75;color:#fff}
.content{padding:24px 28px;flex:1}

/* ── Cards ── */
.card{background:#fff;border-radius:10px;border:1px solid #eee;overflow:hidden;margin-bottom:20px}
.card-header{padding:14px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
.card-header h3{font-size:14px;font-weight:500;color:#333}
.card-body{padding:20px}

/* ── Stats ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:10px;padding:18px 20px;border:1px solid #eee;position:relative;overflow:hidden}
.stat-card::after{content:attr(data-icon);position:absolute;right:14px;top:14px;font-size:22px;opacity:.15}
.stat-val{font-size:24px;font-weight:600;color:#0F6E56}
.stat-lbl{font-size:12px;color:#999;margin-top:4px}
.stat-sub{font-size:11px;color:#bbb;margin-top:2px}

/* ── Table ── */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th{background:#f9f9f7;padding:10px 14px;text-align:left;font-size:11px;color:#888;font-weight:500;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;border-bottom:1px solid #eee}
td{padding:11px 14px;border-bottom:1px solid #f5f5f0;vertical-align:middle}
tr:last-child td{border:none}
tr:hover td{background:#fafaf8}
.empty-row td{text-align:center;padding:40px;color:#bbb;font-size:13px}

/* ── Badges ── */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;white-space:nowrap}
.badge-paid{background:#E1F5EE;color:#0F6E56}
.badge-failed{background:#FEF2F2;color:#B91C1C}
.badge-created{background:#FFFBEB;color:#92400E}
.badge-refunded{background:#EFF6FF;color:#1D4ED8}
.badge-blocked{background:#FEF2F2;color:#B91C1C}
.badge-active{background:#E1F5EE;color:#0F6E56}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:7px;font-size:13px;font-weight:500;cursor:pointer;transition:all .15s;font-family:inherit;border:none}
.btn-primary{background:#1D9E75;color:#fff}
.btn-primary:hover{background:#0F6E56}
.btn-outline{background:#fff;color:#555;border:1px solid #ddd}
.btn-outline:hover{border-color:#1D9E75;color:#1D9E75}
.btn-danger{background:#FEF2F2;color:#B91C1C;border:1px solid #FECACA}
.btn-danger:hover{background:#B91C1C;color:#fff}
.btn-sm{padding:4px 10px;font-size:12px}
.btn-receipt{background:#E1F5EE;color:#0F6E56;border:1px solid #A7F3D0}
.btn-receipt:hover{background:#1D9E75;color:#fff}

/* ── Forms ── */
.filter-bar{background:#fff;border-radius:10px;padding:14px 16px;margin-bottom:16px;border:1px solid #eee;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.filter-bar input,.filter-bar select{padding:8px 12px;border:1px solid #ddd;border-radius:7px;font-size:13px;font-family:inherit;color:#333;background:#fafaf8}
.filter-bar input:focus,.filter-bar select:focus{outline:none;border-color:#1D9E75}
.filter-bar input[type=text]{min-width:220px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:13px;font-weight:500;color:#444;margin-bottom:6px}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px 13px;border:1px solid #ddd;border-radius:8px;font-size:14px;font-family:inherit;transition:border-color .15s;background:#fafaf8}
.form-group input:focus,.form-group select:focus{outline:none;border-color:#1D9E75;box-shadow:0 0 0 3px rgba(29,158,117,.1);background:#fff}

/* ── Alerts ── */
.alert{padding:11px 16px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px}
.alert-success{background:#E1F5EE;border:1px solid #A7F3D0;color:#0F6E56}
.alert-error{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C}
.alert-warning{background:#FFFBEB;border:1px solid #FDE68A;color:#92400E}
.alert-info{background:#EFF6FF;border:1px solid #BFDBFE;color:#1D4ED8}

/* ── Pagination ── */
.pagination{display:flex;gap:5px;padding:14px 20px;justify-content:center;border-top:1px solid #eee;flex-wrap:wrap}
.pagination a{padding:6px 12px;background:#fff;border:1px solid #ddd;border-radius:6px;font-size:13px;color:#555;transition:all .15s}
.pagination a.active{background:#1D9E75;color:#fff;border-color:#1D9E75}
.pagination a:hover:not(.active){border-color:#1D9E75;color:#1D9E75}

/* ── Modal ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:200;align-items:center;justify-content:center;padding:20px}
.modal-overlay.open{display:flex}
.modal{background:#fff;border-radius:12px;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.2)}
.modal-header{padding:18px 24px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff}
.modal-header h3{font-size:16px;font-weight:600}
.modal-close{font-size:18px;cursor:pointer;color:#aaa;background:none;border:none;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:6px;transition:all .15s}
.modal-close:hover{background:#f5f5f5;color:#333}
.modal-body{padding:22px 24px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.detail-item .dk{font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px}
.detail-item .dv{font-size:14px;font-weight:500;color:#1a1a1a}
.divider{height:1px;background:#f0f0ec;margin:16px 0}

/* ── Bar chart ── */
.bar-chart{display:flex;align-items:flex-end;gap:10px;height:130px;padding:0 4px}
.bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px}
.bar{width:100%;background:#E1F5EE;border-radius:5px 5px 0 0;transition:height .3s,background .15s;min-height:4px;cursor:pointer}
.bar:hover{background:#1D9E75}
.bar-label{font-size:10px;color:#bbb;white-space:nowrap;text-align:center}
.bar-val{font-size:10px;color:#0F6E56;font-weight:500;text-align:center}

/* ── Mobile ── */
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);transition:transform .3s}
  .sidebar.open{transform:translateX(0)}
  .main{margin-left:0}
  .stats-grid{grid-template-columns:repeat(2,1fr)}
  .content{padding:16px}
  .topbar{padding:12px 16px}
  .mobile-menu{display:flex!important}
}
.mobile-menu{display:none;background:none;border:none;font-size:20px;cursor:pointer;padding:4px}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-logo">
    <div class="s-icon">❤️</div>
    <h2><?= htmlspecialchars(APP_NAME) ?></h2>
    <p>Admin Panel</p>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section">Main</div>
    <a class="nav-item <?= $activePage==='dashboard'?'active':'' ?>" href="dashboard.php">
      <span class="ni">📊</span> Dashboard
    </a>
    <a class="nav-item <?= $activePage==='donors'?'active':'' ?>" href="donors.php">
      <span class="ni">👥</span> Donors
    </a>
    <div class="nav-section">Reports</div>
    <a class="nav-item <?= $activePage==='receipts'?'active':'' ?>" href="receipts.php">
      <span class="ni">🧾</span> Receipts
    </a>
    <a class="nav-item" href="export.php" target="_blank">
      <span class="ni">⬇️</span> Export CSV
    </a>
    <div class="nav-section">Security</div>
    <a class="nav-item <?= $activePage==='blocked'?'active':'' ?>" href="blocked_ips.php">
      <span class="ni">🚫</span> Blocked IPs
    </a>
    <div class="nav-section">Account</div>
    <a class="nav-item <?= $activePage==='password'?'active':'' ?>" href="change_password.php">
      <span class="ni">🔑</span> Change Password
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="sf-name"><?= htmlspecialchars($_SESSION['admin_name']) ?></div>
    <div class="sf-user"><?= htmlspecialchars($_SESSION['admin_username']) ?></div>
    <a class="sf-logout" href="logout.php">🚪 Logout</a>
  </div>
</div>

<!-- Main wrapper starts — each page fills .main -->
<div class="main" id="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="mobile-menu" onclick="toggleSidebar()">☰</button>
      <div class="topbar-left">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <?php if (!empty($pageSubtitle)): ?>
          <div class="sub"><?= htmlspecialchars($pageSubtitle) ?></div>
        <?php endif ?>
      </div>
    </div>
    <div class="topbar-right">
      <a href="<?= APP_URL ?>/public/" target="_blank">🔗 Donation Form</a>
    </div>
  </div>
  <div class="content">
<!-- PAGE CONTENT STARTS HERE -->

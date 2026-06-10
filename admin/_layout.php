<?php
// =============================================
// admin/_layout.php — Shared admin UI helper
// Usage: require '_layout.php'; then call layout_head(), layout_nav(), layout_end()
// =============================================

function layout_head(string $title): void {
    $t = htmlspecialchars($title);
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$t} — Admin</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f1f5f9; color: #1e293b; }
  a { text-decoration: none; }

  /* Sidebar */
  .sidebar {
    position: fixed; left: 0; top: 0; bottom: 0; width: 220px;
    background: #0f172a; padding: 0; overflow-y: auto; z-index: 100;
  }
  .sidebar-brand {
    padding: 20px 20px 16px;
    border-bottom: 1px solid #1e293b;
    color: #f1f5f9;
    font-size: 15px;
    font-weight: 700;
  }
  .sidebar-brand small { display: block; color: #64748b; font-size: 11px; font-weight: 400; margin-top: 2px; }
  .sidebar nav { padding: 12px 0; }
  .sidebar nav a {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 20px;
    color: #94a3b8;
    font-size: 13.5px;
    transition: background .15s, color .15s;
  }
  .sidebar nav a:hover, .sidebar nav a.active {
    background: #1e293b; color: #f1f5f9;
  }
  .sidebar nav a .icon { font-size: 16px; width: 20px; text-align: center; }
  .sidebar nav .nav-section {
    padding: 14px 20px 6px;
    color: #475569;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .sidebar-footer {
    position: sticky; bottom: 0;
    background: #0f172a;
    border-top: 1px solid #1e293b;
    padding: 12px 20px;
  }
  .sidebar-footer a {
    color: #ef4444;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .sidebar-footer a:hover { color: #fca5a5; }

  /* Main area */
  .main { margin-left: 220px; padding: 28px 32px; min-height: 100vh; }
  .page-title {
    font-size: 22px; font-weight: 700; color: #0f172a;
    margin-bottom: 20px; display: flex; align-items: center; gap: 10px;
  }
  .page-title small { font-size: 13px; font-weight: 400; color: #64748b; }

  /* Cards */
  .card {
    background: #fff; border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 22px 24px;
    margin-bottom: 22px;
  }
  .card-title { font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #0f172a; }

  /* Alerts */
  .alert { padding: 10px 16px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
  .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
  .alert-danger  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
  .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

  /* Forms */
  .form-group { margin-bottom: 16px; }
  .form-group label { display: block; font-size: 12px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
  .form-control {
    width: 100%; border: 1px solid #cbd5e1; border-radius: 6px;
    padding: 9px 12px; font-size: 13.5px; color: #1e293b;
    background: #f8fafc; outline: none; transition: border-color .2s;
  }
  .form-control:focus { border-color: #6366f1; background: #fff; }
  select.form-control { cursor: pointer; }

  /* Buttons */
  .btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 6px; font-size: 13px;
    font-weight: 600; border: none; cursor: pointer; transition: .15s;
  }
  .btn-primary { background: #6366f1; color: #fff; }
  .btn-primary:hover { background: #4f46e5; }
  .btn-danger  { background: #ef4444; color: #fff; }
  .btn-danger:hover { background: #dc2626; }
  .btn-sm { padding: 5px 10px; font-size: 12px; }
  .btn-outline {
    background: transparent; border: 1px solid #cbd5e1; color: #475569;
  }
  .btn-outline:hover { background: #f1f5f9; }

  /* Table */
  .table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
  .table th { background: #f8fafc; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; border-bottom: 2px solid #e2e8f0; }
  .table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
  .table tr:last-child td { border-bottom: none; }
  .table tr:hover td { background: #f8fafc; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
  .badge-img { background: #ede9fe; color: #5b21b6; }
  .badge-vid { background: #dbeafe; color: #1e40af; }

  /* Tabs */
  .tabs { display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 22px; }
  .tab-btn {
    padding: 9px 18px; font-size: 13.5px; font-weight: 600;
    color: #64748b; background: none; border: none;
    border-bottom: 2px solid transparent; margin-bottom: -2px;
    cursor: pointer; transition: .15s;
  }
  .tab-btn.active { color: #6366f1; border-bottom-color: #6366f1; }
  .tab-pane { display: none; }
  .tab-pane.active { display: block; }

  /* Thumbnail preview grid */
  .thumb-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
  .thumb-grid img { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; border: 2px solid #e2e8f0; }

  /* Responsive */
  @media (max-width: 768px) {
    .sidebar { width: 100%; height: auto; position: relative; }
    .main { margin-left: 0; padding: 16px; }
  }
</style>
</head>
<body>
HTML;
}

function layout_nav(string $active = ''): void {
    $links = [
        'index'      => ['🏠', 'Dashboard',       'index.php'],
        'categories' => ['🏷️', 'Categories',      'categories.php'],
        'media'      => ['🖼️', 'Media Upload',    'media.php'],
    ];

    echo '<div class="sidebar">';
    echo '<div class="sidebar-brand">Foundation NGO <small>Admin Panel</small></div>';
    echo '<nav>';
    echo '<div class="nav-section">Main</div>';
    foreach ($links as $key => [$icon, $label, $href]) {
        $cls = ($active === $key) ? ' active' : '';
        echo "<a href=\"{$href}\" class=\"{$cls}\"><span class=\"icon\">{$icon}</span>{$label}</a>";
    }
    echo '</nav>';
    echo '<div class="sidebar-footer"><a href="logout.php">⏻ Logout</a></div>';
    echo '</div>';
    echo '<div class="main">';
}

function layout_end(): void {
    echo '</div></body></html>';
}

function csrf_field(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function slug(string $text): string {
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

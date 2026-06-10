<?php
require 'auth.php';
require 'db.php';
require '_layout.php';

// Quick stats
$imgCats  = $pdo->query('SELECT COUNT(*) FROM image_categories')->fetchColumn();
$vidCats  = $pdo->query('SELECT COUNT(*) FROM video_categories')->fetchColumn();
$imgCount = $pdo->query('SELECT COUNT(*) FROM gallery_images')->fetchColumn();
$vidCount = $pdo->query('SELECT COUNT(*) FROM gallery_videos')->fetchColumn();

layout_head('Dashboard');
layout_nav('index');
?>

<div class="page-title">🏠 Dashboard <small>Welcome back!</small></div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:16px;margin-bottom:28px;">
  <?php
  $stats = [
    ['🏷️', 'Image Categories', $imgCats,  '#ede9fe', '#5b21b6'],
    // ['🏷️', 'Video Categories', $vidCats,  '#dbeafe', '#1e40af'],
    ['🖼️', 'Total Images',     $imgCount, '#f0fdf4', '#166534'],
    // ['▶️', 'Total Videos',     $vidCount, '#fff7ed', '#9a3412'],
  ];
  foreach ($stats as [$icon, $label, $val, $bg, $col]):
  ?>
  <div class="card" style="text-align:center;background:<?= $bg ?>;border-color:<?= $bg ?>;">
    <div style="font-size:28px;margin-bottom:6px;"><?= $icon ?></div>
    <div style="font-size:28px;font-weight:800;color:<?= $col ?>"><?= $val ?></div>
    <div style="font-size:12px;color:<?= $col ?>;opacity:.8;margin-top:2px;"><?= $label ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card">
  <div class="card-title">Quick Actions</div>
  <a href="categories.php" class="btn btn-outline" style="margin-right:8px;">🏷️ Manage Categories</a>
  <a href="media.php" class="btn btn-primary">➕ Add Media</a>
</div>

<?php layout_end(); ?>

<?php
// =============================================
// admin/categories.php
// Manage Image & Video categories on one page
// =============================================
require 'auth.php';
require 'db.php';
require '_layout.php';

$msg = '';
$msgType = 'success';
$activeTab = $_GET['tab'] ?? 'image'; // 'image' or 'video'

// ---- POST actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $msg = 'Invalid request token. Please try again.';
        $msgType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';

        // -- Add image category --
        if ($action === 'add_img_cat') {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $msg = 'Category name cannot be empty.';
                $msgType = 'danger';
            } else {
                $s = slug($name);
                // Make slug unique
                $exists = $pdo->prepare('SELECT id FROM image_categories WHERE slug = ?');
                $exists->execute([$s]);
                if ($exists->fetch()) $s .= '-' . time();
                $pdo->prepare('INSERT INTO image_categories (name, slug) VALUES (?, ?)')->execute([$name, $s]);
                $msg = "Image category <strong>" . htmlspecialchars($name) . "</strong> added.";
            }
            $activeTab = 'image';
        }

        // -- Delete image category --
        elseif ($action === 'del_img_cat') {
            $id = (int)($_POST['id'] ?? 0);
            $count = $pdo->prepare('SELECT COUNT(*) FROM gallery_images WHERE category_id = ?');
            $count->execute([$id]);
            if ($count->fetchColumn() > 0) {
                $msg = '⚠️ Cannot delete: this category still has images. Remove all images first.';
                $msgType = 'warning';
            } else {
                $pdo->prepare('DELETE FROM image_categories WHERE id = ?')->execute([$id]);
                $msg = 'Image category deleted.';
            }
            $activeTab = 'image';
        }

        // -- Edit image category name --
        elseif ($action === 'edit_img_cat') {
            $id   = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $msg = 'Category name cannot be empty.';
                $msgType = 'danger';
            } else {
                $pdo->prepare('UPDATE image_categories SET name = ? WHERE id = ?')->execute([$name, $id]);
                $msg = 'Image category updated.';
            }
            $activeTab = 'image';
        }

        // -- Add video category --
        elseif ($action === 'add_vid_cat') {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $msg = 'Category name cannot be empty.';
                $msgType = 'danger';
            } else {
                $s = slug($name);
                $exists = $pdo->prepare('SELECT id FROM video_categories WHERE slug = ?');
                $exists->execute([$s]);
                if ($exists->fetch()) $s .= '-' . time();
                $pdo->prepare('INSERT INTO video_categories (name, slug) VALUES (?, ?)')->execute([$name, $s]);
                $msg = "Video category <strong>" . htmlspecialchars($name) . "</strong> added.";
            }
            $activeTab = 'video';
        }

        // -- Delete video category --
        elseif ($action === 'del_vid_cat') {
            $id = (int)($_POST['id'] ?? 0);
            $count = $pdo->prepare('SELECT COUNT(*) FROM gallery_videos WHERE category_id = ?');
            $count->execute([$id]);
            if ($count->fetchColumn() > 0) {
                $msg = '⚠️ Cannot delete: this category still has videos. Remove all videos first.';
                $msgType = 'warning';
            } else {
                $pdo->prepare('DELETE FROM video_categories WHERE id = ?')->execute([$id]);
                $msg = 'Video category deleted.';
            }
            $activeTab = 'video';
        }

        // -- Edit video category name --
        elseif ($action === 'edit_vid_cat') {
            $id   = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if ($name === '') {
                $msg = 'Category name cannot be empty.';
                $msgType = 'danger';
            } else {
                $pdo->prepare('UPDATE video_categories SET name = ? WHERE id = ?')->execute([$name, $id]);
                $msg = 'Video category updated.';
            }
            $activeTab = 'video';
        }
    }
    // Redirect to prevent re-POST
    $redirectMsg = urlencode($msg);
    $redirectType = urlencode($msgType);
    header("Location: categories.php?tab={$activeTab}&msg={$redirectMsg}&mtype={$redirectType}");
    exit;
}

// Flash message from redirect
if (!empty($_GET['msg'])) {
    $msg     = htmlspecialchars_decode(urldecode($_GET['msg']));
    $msgType = htmlspecialchars(urldecode($_GET['mtype'] ?? 'success'));
}
$activeTab = $_GET['tab'] ?? 'image';

// Fetch categories with item counts
$imgCats = $pdo->query(
    'SELECT ic.*, COUNT(gi.id) AS item_count
     FROM image_categories ic
     LEFT JOIN gallery_images gi ON gi.category_id = ic.id
     GROUP BY ic.id ORDER BY ic.created_at DESC'
)->fetchAll();

$vidCats = $pdo->query(
    'SELECT vc.*, COUNT(gv.id) AS item_count
     FROM video_categories vc
     LEFT JOIN gallery_videos gv ON gv.category_id = vc.id
     GROUP BY vc.id ORDER BY vc.created_at DESC'
)->fetchAll();

// Editing state
$editImgId = (int)($_GET['edit_img'] ?? 0);
$editVidId = (int)($_GET['edit_vid'] ?? 0);
$editImgRow = $editImgId ? array_filter($imgCats, fn($r) => $r['id'] === $editImgId) : [];
$editImgRow = $editImgRow ? array_values($editImgRow)[0] : null;
$editVidRow = $editVidId ? array_filter($vidCats, fn($r) => $r['id'] === $editVidId) : [];
$editVidRow = $editVidRow ? array_values($editVidRow)[0] : null;

layout_head('Categories');
layout_nav('categories');
?>

<div class="page-title">🏷️ Categories</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- Tabs -->
<div class="tabs">
  <button class="tab-btn <?= $activeTab === 'image' ? 'active' : '' ?>" onclick="switchTab('image')">🖼️ Image Categories</button>
  <!-- <button class="tab-btn <?= $activeTab === 'video' ? 'active' : '' ?>" onclick="switchTab('video')">▶️ Video Categories</button> -->
</div>

<!-- ===== IMAGE CATEGORIES TAB ===== -->
<div id="tab-image" class="tab-pane <?= $activeTab === 'image' ? 'active' : '' ?>">

  <div class="card">
    <div class="card-title"><?= $editImgRow ? '✏️ Edit Image Category' : '➕ Add Image Category' ?></div>
    <form method="POST" action="categories.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editImgRow ? 'edit_img_cat' : 'add_img_cat' ?>">
      <?php if ($editImgRow): ?>
        <input type="hidden" name="id" value="<?= $editImgRow['id'] ?>">
      <?php endif; ?>
      <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="flex:1;min-width:200px;margin:0;">
          <label>Category Name</label>
          <input type="text" name="name" class="form-control"
                 placeholder="e.g. Annual Events"
                 value="<?= $editImgRow ? htmlspecialchars($editImgRow['name']) : '' ?>"
                 required>
        </div>
        <div>
          <button type="submit" class="btn btn-primary">
            <?= $editImgRow ? '💾 Update' : '➕ Add Category' ?>
          </button>
          <?php if ($editImgRow): ?>
            <a href="categories.php?tab=image" class="btn btn-outline" style="margin-left:6px;">Cancel</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-title">All Image Categories (<?= count($imgCats) ?>)</div>
    <?php if (empty($imgCats)): ?>
      <p style="color:#94a3b8;font-size:13px;">No categories yet. Add one above.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr><th>#</th><th>Name</th><th>Images</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($imgCats as $i => $cat): ?>
          <tr>
            <td style="color:#94a3b8;"><?= $i + 1 ?></td>
            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
            <td><span class="badge badge-img"><?= $cat['item_count'] ?> images</span></td>
            <td style="color:#94a3b8;font-size:12px;"><?= date('d M Y', strtotime($cat['created_at'])) ?></td>
            <td>
              <a href="categories.php?tab=image&edit_img=<?= $cat['id'] ?>" class="btn btn-sm btn-outline">✏️ Edit</a>
              <?php if ($cat['item_count'] == 0): ?>
              <form method="POST" action="categories.php" style="display:inline;"
                    onsubmit="return confirm('Delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="del_img_cat">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
              </form>
              <?php else: ?>
              <button class="btn btn-sm btn-danger" disabled
                      title="Remove all <?= $cat['item_count'] ?> image(s) first"
                      style="opacity:.5;cursor:not-allowed;">🔒 Delete</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<!-- ===== VIDEO CATEGORIES TAB ===== -->
<div id="tab-video" class="tab-pane <?= $activeTab === 'video' ? 'active' : '' ?>">

  <div class="card">
    <div class="card-title"><?= $editVidRow ? '✏️ Edit Video Category' : '➕ Add Video Category' ?></div>
    <form method="POST" action="categories.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editVidRow ? 'edit_vid_cat' : 'add_vid_cat' ?>">
      <?php if ($editVidRow): ?>
        <input type="hidden" name="id" value="<?= $editVidRow['id'] ?>">
      <?php endif; ?>
      <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="flex:1;min-width:200px;margin:0;">
          <label>Category Name</label>
          <input type="text" name="name" class="form-control"
                 placeholder="e.g. Campaign Videos"
                 value="<?= $editVidRow ? htmlspecialchars($editVidRow['name']) : '' ?>"
                 required>
        </div>
        <div>
          <button type="submit" class="btn btn-primary">
            <?= $editVidRow ? '💾 Update' : '➕ Add Category' ?>
          </button>
          <?php if ($editVidRow): ?>
            <a href="categories.php?tab=video" class="btn btn-outline" style="margin-left:6px;">Cancel</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-title">All Video Categories (<?= count($vidCats) ?>)</div>
    <?php if (empty($vidCats)): ?>
      <p style="color:#94a3b8;font-size:13px;">No categories yet. Add one above.</p>
    <?php else: ?>
      <table class="table">
        <thead>
          <tr><th>#</th><th>Name</th><th>Videos</th><th>Created</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php foreach ($vidCats as $i => $cat): ?>
          <tr>
            <td style="color:#94a3b8;"><?= $i + 1 ?></td>
            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
            <td><span class="badge badge-vid"><?= $cat['item_count'] ?> videos</span></td>
            <td style="color:#94a3b8;font-size:12px;"><?= date('d M Y', strtotime($cat['created_at'])) ?></td>
            <td>
              <a href="categories.php?tab=video&edit_vid=<?= $cat['id'] ?>" class="btn btn-sm btn-outline">✏️ Edit</a>
              <?php if ($cat['item_count'] == 0): ?>
              <form method="POST" action="categories.php" style="display:inline;"
                    onsubmit="return confirm('Delete category \'<?= htmlspecialchars(addslashes($cat['name'])) ?>\'?')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="del_vid_cat">
                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger">🗑️ Delete</button>
              </form>
              <?php else: ?>
              <button class="btn btn-sm btn-danger" disabled
                      title="Remove all <?= $cat['item_count'] ?> video(s) first"
                      style="opacity:.5;cursor:not-allowed;">🔒 Delete</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  event.target.classList.add('active');
  // Update URL without reload
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  window.history.replaceState({}, '', url);
}
</script>

<?php layout_end(); ?>

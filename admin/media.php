<?php
// =============================================
// admin/media.php
// Upload images + add videos — one page, two tabs
// Images stored in: ../uploads/gallery/
// =============================================
require 'auth.php';
require 'db.php';
require '_layout.php';

define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/gallery/');
define('UPLOAD_URL', '../uploads/gallery/');
define('ALLOWED_IMG', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB per image

// Ensure upload folder exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    // Prevent direct PHP execution in uploads
    file_put_contents(UPLOAD_DIR . '.htaccess', "Options -Indexes\n<FilesMatch '\\.php$'>\nDeny from all\n</FilesMatch>\n");
}

$msg = '';
$msgType = 'success';
$activeTab = $_GET['tab'] ?? 'images';

// ---- POST actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $msg = 'Invalid request token. Please try again.';
        $msgType = 'danger';
    } else {
        $action = $_POST['action'] ?? '';

        // ---- Upload Images ----
        if ($action === 'upload_images') {
            $catId = (int)($_POST['image_category_id'] ?? 0);
            $caption = trim($_POST['caption'] ?? '');

            if (!$catId) {
                $msg = 'Please select an image category.';
                $msgType = 'danger';
            } elseif (empty($_FILES['images']['name'][0])) {
                $msg = 'Please select at least one image to upload.';
                $msgType = 'danger';
            } else {
                // Verify category exists
                $catCheck = $pdo->prepare('SELECT id FROM image_categories WHERE id = ?');
                $catCheck->execute([$catId]);
                if (!$catCheck->fetch()) {
                    $msg = 'Invalid category selected.';
                    $msgType = 'danger';
                } else {
                    $uploaded = 0;
                    $errors = [];
                    $files = $_FILES['images'];
                    $total = count($files['name']);

                    for ($i = 0; $i < $total; $i++) {
                        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                            $errors[] = htmlspecialchars($files['name'][$i]) . ': Upload error code ' . $files['error'][$i];
                            continue;
                        }

                        // Size check
                        if ($files['size'][$i] > MAX_FILE_SIZE) {
                            $errors[] = htmlspecialchars($files['name'][$i]) . ': File too large (max 5MB).';
                            continue;
                        }

                        // Type check — use finfo for real type, not extension
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime  = finfo_file($finfo, $files['tmp_name'][$i]);
                        finfo_close($finfo);

                        if (!in_array($mime, ALLOWED_IMG, true)) {
                            $errors[] = htmlspecialchars($files['name'][$i]) . ': Invalid file type (only JPG, PNG, WEBP, GIF allowed).';
                            continue;
                        }

                        // Generate safe unique filename
                        $ext      = match($mime) {
                            'image/jpeg' => 'jpg',
                            'image/png'  => 'png',
                            'image/webp' => 'webp',
                            'image/gif'  => 'gif',
                        };
                        $filename = 'img_' . uniqid('', true) . '.' . $ext;
                        $dest     = UPLOAD_DIR . $filename;

                        if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                            $pdo->prepare('INSERT INTO gallery_images (category_id, filename, caption) VALUES (?, ?, ?)')
                                ->execute([$catId, $filename, $caption]);
                            $uploaded++;
                        } else {
                            $errors[] = htmlspecialchars($files['name'][$i]) . ': Could not save file.';
                        }
                    }

                    if ($uploaded > 0) {
                        $msg = "✅ {$uploaded} image(s) uploaded successfully.";
                        if ($errors) $msg .= ' Some files had issues: ' . implode('; ', $errors);
                    } else {
                        $msg = 'No images uploaded. ' . implode('; ', $errors);
                        $msgType = 'danger';
                    }
                }
            }
            $activeTab = 'images';
        }

        // ---- Delete Image ----
        elseif ($action === 'del_image') {
            $id = (int)($_POST['id'] ?? 0);
            $row = $pdo->prepare('SELECT filename FROM gallery_images WHERE id = ?');
            $row->execute([$id]);
            $img = $row->fetch();
            if ($img) {
                $file = UPLOAD_DIR . $img['filename'];
                if (file_exists($file)) @unlink($file);
                $pdo->prepare('DELETE FROM gallery_images WHERE id = ?')->execute([$id]);
                $msg = 'Image deleted.';
            }
            $activeTab = 'images';
        }

        // ---- Add Video ----
        elseif ($action === 'add_video') {
            $catId = (int)($_POST['video_category_id'] ?? 0);
            $ytUrl = trim($_POST['youtube_url'] ?? '');
            $title = trim($_POST['video_title'] ?? '');

            if (!$catId) {
                $msg = 'Please select a video category.';
                $msgType = 'danger';
            } elseif ($ytUrl === '') {
                $msg = 'Please enter a YouTube URL.';
                $msgType = 'danger';
            } else {
                // Extract YouTube video ID for thumbnail
                $ytId = '';
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
                    $ytId = $m[1];
                }
                if (!$ytId) {
                    $msg = 'Invalid YouTube URL. Please use a valid youtube.com or youtu.be link.';
                    $msgType = 'danger';
                } else {
                    $thumbnail = "https://img.youtube.com/vi/{$ytId}/hqdefault.jpg";
                    $catCheck = $pdo->prepare('SELECT id FROM video_categories WHERE id = ?');
                    $catCheck->execute([$catId]);
                    if (!$catCheck->fetch()) {
                        $msg = 'Invalid category.';
                        $msgType = 'danger';
                    } else {
                        $pdo->prepare('INSERT INTO gallery_videos (category_id, youtube_url, thumbnail, title) VALUES (?, ?, ?, ?)')
                            ->execute([$catId, $ytUrl, $thumbnail, $title]);
                        $msg = '✅ Video added successfully.';
                    }
                }
            }
            $activeTab = 'videos';
        }

        // ---- Delete Video ----
        elseif ($action === 'del_video') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM gallery_videos WHERE id = ?')->execute([$id]);
            $msg = 'Video deleted.';
            $activeTab = 'videos';
        }
    }

    // Redirect to prevent re-POST
    header('Location: media.php?tab=' . $activeTab . '&msg=' . urlencode($msg) . '&mtype=' . urlencode($msgType));
    exit;
}

// Flash message
if (!empty($_GET['msg'])) {
    $msg     = htmlspecialchars_decode(urldecode($_GET['msg']));
    $msgType = htmlspecialchars(urldecode($_GET['mtype'] ?? 'success'));
}
$activeTab = $_GET['tab'] ?? 'images';

// Fetch categories for dropdowns
$imgCats = $pdo->query('SELECT id, name FROM image_categories ORDER BY name')->fetchAll();
$vidCats = $pdo->query('SELECT id, name FROM video_categories ORDER BY name')->fetchAll();

// Fetch existing media (paginated — latest 30)
$filterImgCat = (int)($_GET['img_cat'] ?? 0);
$filterVidCat = (int)($_GET['vid_cat'] ?? 0);

$imgQuery = 'SELECT gi.*, ic.name AS cat_name
             FROM gallery_images gi
             JOIN image_categories ic ON ic.id = gi.category_id';
if ($filterImgCat) $imgQuery .= ' WHERE gi.category_id = ' . $filterImgCat;
$imgQuery .= ' ORDER BY gi.created_at DESC LIMIT 60';
$images = $pdo->query($imgQuery)->fetchAll();

$vidQuery = 'SELECT gv.*, vc.name AS cat_name
             FROM gallery_videos gv
             JOIN video_categories vc ON vc.id = gv.category_id';
if ($filterVidCat) $vidQuery .= ' WHERE gv.category_id = ' . $filterVidCat;
$vidQuery .= ' ORDER BY gv.created_at DESC LIMIT 60';
$videos = $pdo->query($vidQuery)->fetchAll();

layout_head('Media Upload');
layout_nav('media');
?>

<div class="page-title">🖼️ Media Upload</div>

<?php if ($msg): ?>
<div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
<?php endif; ?>

<div class="tabs">
  <button class="tab-btn <?= $activeTab === 'images' ? 'active' : '' ?>" onclick="switchTab('images')">🖼️ Images</button>
  <!-- <button class="tab-btn <?= $activeTab === 'videos' ? 'active' : '' ?>" onclick="switchTab('videos')">▶️ Videos</button> -->
</div>

<!-- ===== IMAGES TAB ===== -->
<div id="tab-images" class="tab-pane <?= $activeTab === 'images' ? 'active' : '' ?>">

  <!-- Upload form -->
  <div class="card">
    <div class="card-title">➕ Upload Images</div>
    <?php if (empty($imgCats)): ?>
      <div class="alert alert-warning">No image categories found. <a href="categories.php?tab=image">Create one first →</a></div>
    <?php else: ?>
    <form method="POST" action="media.php" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="upload_images">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;flex-wrap:wrap;">
        <div class="form-group">
          <label>Select Category</label>
          <select name="image_category_id" class="form-control" required>
            <option value="">-- Choose Category --</option>
            <?php foreach ($imgCats as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Caption (optional, applies to all)</label>
          <input type="text" name="caption" class="form-control" placeholder="e.g. Annual Event 2024">
        </div>
      </div>
      <div class="form-group">
        <label>Select Images (multiple allowed — JPG, PNG, WEBP, GIF — max 5MB each)</label>
        <input type="file" name="images[]" class="form-control" multiple
               accept="image/jpeg,image/png,image/webp,image/gif"
               id="imageFileInput" required>
      </div>
      <!-- Preview thumbnails -->
      <div class="thumb-grid" id="previewGrid"></div>
      <br>
      <button type="submit" class="btn btn-primary">⬆️ Upload Images</button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Existing images -->
  <div class="card">
    <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <span>Uploaded Images (showing latest 60)</span>
      <form method="GET" action="media.php" style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="tab" value="images">
        <select name="img_cat" class="form-control" style="width:auto;padding:6px 10px;">
          <option value="">All Categories</option>
          <?php foreach ($imgCats as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $filterImgCat == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-outline">Filter</button>
      </form>
    </div>

    <?php if (empty($images)): ?>
      <p style="color:#94a3b8;font-size:13px;">No images uploaded yet.</p>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;">
      <?php foreach ($images as $img): ?>
      <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;position:relative;">
        <img src="<?= UPLOAD_URL . htmlspecialchars($img['filename']) ?>"
             alt="" style="width:100%;height:110px;object-fit:cover;display:block;">
        <div style="padding:8px 10px;background:#f8fafc;">
          <div style="font-size:11px;color:#64748b;margin-bottom:4px;">
            <span class="badge badge-img"><?= htmlspecialchars($img['cat_name']) ?></span>
          </div>
          <?php if ($img['caption']): ?>
            <div style="font-size:11px;color:#475569;margin-bottom:6px;"><?= htmlspecialchars($img['caption']) ?></div>
          <?php endif; ?>
          <form method="POST" action="media.php"
                onsubmit="return confirm('Delete this image?')"
                style="margin:0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="del_image">
            <input type="hidden" name="id" value="<?= $img['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger" style="width:100%;">🗑️ Delete</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ===== VIDEOS TAB ===== -->
<div id="tab-videos" class="tab-pane <?= $activeTab === 'videos' ? 'active' : '' ?>">

  <!-- Add video form -->
  <div class="card">
    <div class="card-title">➕ Add YouTube Video</div>
    <?php if (empty($vidCats)): ?>
      <div class="alert alert-warning">No video categories found. <a href="categories.php?tab=video">Create one first →</a></div>
    <?php else: ?>
    <form method="POST" action="media.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add_video">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div class="form-group">
          <label>Select Category</label>
          <select name="video_category_id" class="form-control" required>
            <option value="">-- Choose Category --</option>
            <?php foreach ($vidCats as $cat): ?>
              <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Video Title (optional)</label>
          <input type="text" name="video_title" class="form-control" placeholder="e.g. Foundation Day 2024">
        </div>
      </div>
      <div class="form-group">
        <label>YouTube URL</label>
        <input type="url" name="youtube_url" class="form-control"
               placeholder="https://www.youtube.com/watch?v=..." required
               id="ytUrlInput">
      </div>
      <!-- YT preview -->
      <div id="ytPreview" style="display:none;margin-bottom:12px;">
        <img id="ytThumb" src="" alt="Thumbnail" style="height:100px;border-radius:6px;border:2px solid #e2e8f0;">
        <div style="font-size:12px;color:#64748b;margin-top:4px;">Thumbnail preview</div>
      </div>
      <button type="submit" class="btn btn-primary">➕ Add Video</button>
    </form>
    <?php endif; ?>
  </div>

  <!-- Existing videos -->
  <div class="card">
    <div class="card-title" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
      <span>Added Videos (showing latest 60)</span>
      <form method="GET" action="media.php" style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="tab" value="videos">
        <select name="vid_cat" class="form-control" style="width:auto;padding:6px 10px;">
          <option value="">All Categories</option>
          <?php foreach ($vidCats as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $filterVidCat == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm btn-outline">Filter</button>
      </form>
    </div>

    <?php if (empty($videos)): ?>
      <p style="color:#94a3b8;font-size:13px;">No videos added yet.</p>
    <?php else: ?>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;">
      <?php foreach ($videos as $vid): ?>
      <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
        <a href="<?= htmlspecialchars($vid['youtube_url']) ?>" target="_blank" rel="noopener"
           style="display:block;position:relative;">
          <img src="<?= htmlspecialchars($vid['thumbnail']) ?>"
               alt="" style="width:100%;height:115px;object-fit:cover;display:block;">
          <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                      background:rgba(0,0,0,.6);border-radius:50%;width:38px;height:38px;
                      display:flex;align-items:center;justify-content:center;font-size:16px;">▶️</div>
        </a>
        <div style="padding:8px 10px;background:#f8fafc;">
          <?php if ($vid['title']): ?>
            <div style="font-size:12px;font-weight:600;color:#1e293b;margin-bottom:4px;
                        white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
              <?= htmlspecialchars($vid['title']) ?>
            </div>
          <?php endif; ?>
          <div style="margin-bottom:6px;">
            <span class="badge badge-vid"><?= htmlspecialchars($vid['cat_name']) ?></span>
          </div>
          <form method="POST" action="media.php"
                onsubmit="return confirm('Delete this video?')" style="margin:0;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="del_video">
            <input type="hidden" name="id" value="<?= $vid['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger" style="width:100%;">🗑️ Delete</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Tab switching
function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  event.target.classList.add('active');
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  window.history.replaceState({}, '', url);
}

// Image file preview
document.getElementById('imageFileInput')?.addEventListener('change', function() {
  const grid = document.getElementById('previewGrid');
  grid.innerHTML = '';
  Array.from(this.files).forEach(file => {
    if (!file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.createElement('img');
      img.src = e.target.result;
      grid.appendChild(img);
    };
    reader.readAsDataURL(file);
  });
});

// YouTube thumbnail preview
document.getElementById('ytUrlInput')?.addEventListener('input', function() {
  const url = this.value;
  const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/);
  const preview = document.getElementById('ytPreview');
  const thumb = document.getElementById('ytThumb');
  if (match) {
    thumb.src = 'https://img.youtube.com/vi/' + match[1] + '/hqdefault.jpg';
    preview.style.display = 'block';
  } else {
    preview.style.display = 'none';
  }
});
</script>

<?php layout_end(); ?>

<?php
require_once 'admin/db.php';

$cats = $pdo->query(
    'SELECT vc.id, vc.name, COUNT(gv.id) AS cnt
     FROM video_categories vc
     INNER JOIN gallery_videos gv ON gv.category_id = vc.id
     GROUP BY vc.id ORDER BY vc.name'
)->fetchAll();

$activeCat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

if ($activeCat) {
    $stmt = $pdo->prepare(
        'SELECT gv.*, vc.name AS cat_name
         FROM gallery_videos gv
         JOIN video_categories vc ON vc.id = gv.category_id
         WHERE gv.category_id = ?
         ORDER BY gv.created_at DESC'
    );
    $stmt->execute([$activeCat]);
} else {
    $stmt = $pdo->query(
        'SELECT gv.*, vc.name AS cat_name
         FROM gallery_videos gv
         JOIN video_categories vc ON vc.id = gv.category_id
         ORDER BY gv.created_at DESC'
    );
}
$videos = $stmt->fetchAll();
?>
<!doctype html>
<html lang="zxx">

<head>
  <!-- Meta -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1" />
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="author" content="Awaiken" />
  <!-- Page Title -->
  <title>Life Foundation || Video Gallery</title>

  <!-- Header-links Start -->
  <?php include 'inc/header-links.php'; ?>
  <!-- Header-links End -->

  <!-- Mouse Cursor Css File -->
  <link rel="stylesheet" href="css/mousecursor.css">

  <style>
    .gallery-filter-tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      padding: 24px 0 16px;
    }
    .gallery-filter-tabs a {
      padding: 7px 20px;
      border-radius: 30px;
      font-size: 13px;
      font-weight: 600;
      border: 2px solid #ccc;
      color: #444;
      text-decoration: none;
      transition: .2s;
    }
    .gallery-filter-tabs a.active,
    .gallery-filter-tabs a:hover {
      background: var(--primary-color, #e63528);
      border-color: var(--primary-color, #e63528);
      color: #fff;
    }
    .no-media-msg {
      text-align: center;
      padding: 60px 20px;
      color: #888;
      font-size: 16px;
      width: 100%;
    }
    .video-title {
      padding: 8px 4px 0;
      font-size: 13px;
      font-weight: 600;
      color: #333;
    }
  </style>
</head>
<body>

  <!-- Header Start -->
  <?php include 'inc/header.php'; ?>
  <!-- Header End -->

  <!-- Page Header Section Start -->
  <div class="page-header dark-section parallaxie">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="page-header-box">
            <h1 class="text-anime-style-3" data-cursor="-opaque">Our video</h1>
            <nav class="wow fadeInUp">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Our Video</li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Page Header Section End -->

  <!-- Scrolling Ticker Section Start -->
  <div class="our-scrolling-ticker">
    <div class="scrolling-ticker-box">
      <div class="scrolling-content">
        <span><img src="images/icon-asterisk.svg" alt="">Community Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="">Future Ready</span>
        <span><img src="images/icon-asterisk.svg" alt="">Community Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="">Future Ready</span>
      </div>
      <div class="scrolling-content">
        <span><img src="images/icon-asterisk.svg" alt="">Community Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="">Future Ready</span>
        <span><img src="images/icon-asterisk.svg" alt="">Community Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Health Support</span>
        <span><img src="images/icon-asterisk.svg" alt="">Volunteer Impact</span>
        <span><img src="images/icon-asterisk.svg" alt="">Future Ready</span>
      </div>
    </div>
  </div>
  <!-- Scrolling Ticker Section End -->

  <!-- Page Video Gallery Start -->
  <div class="page-video-gallery">
    <div class="container">

      <?php if (!empty($cats)): ?>
      <div class="gallery-filter-tabs">
        <a href="video-gallery.php" class="<?= $activeCat === 0 ? 'active' : '' ?>">All</a>
        <?php foreach ($cats as $cat): ?>
          <a href="video-gallery.php?cat=<?= $cat['id'] ?>"
             class="<?= $activeCat === (int)$cat['id'] ? 'active' : '' ?>">
            <?= htmlspecialchars($cat['name']) ?>
            <small style="opacity:.7;">(<?= $cat['cnt'] ?>)</small>
          </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="row">
        <?php if (empty($videos)): ?>
          <div class="no-media-msg">
            <p>No videos available yet. Check back soon!</p>
          </div>
        <?php else: ?>
          <?php
          $delays = ['', '0.2s', '0.4s', '0.6s', '0.8s', '1s', '1.2s', '1.4s', '1.6s'];
          foreach ($videos as $i => $vid):
            $delay = $delays[$i % count($delays)];
          ?>
          <div class="col-lg-4 col-md-6">
            <!-- Video Gallery start -->
            <div class="video-gallery-image wow fadeInUp" <?= $delay ? "data-wow-delay=\"{$delay}\"" : '' ?>>
              <a href="<?= htmlspecialchars($vid['youtube_url']) ?>"
                 class="popup-video" data-cursor-text="Play">
                <figure>
                  <img src="<?= htmlspecialchars($vid['thumbnail']) ?>"
                       alt="<?= htmlspecialchars($vid['title'] ?: $vid['cat_name']) ?>"
                       loading="lazy">
                </figure>
              </a>
              <?php if ($vid['title']): ?>
                <div class="video-title"><?= htmlspecialchars($vid['title']) ?></div>
              <?php endif; ?>
            </div>
            <!-- Video Gallery end -->
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
  <!-- Page Video Gallery End -->

  <!-- Footer Start -->
  <?php include 'inc/footer.php'; ?>
  <!-- Footer End -->

  <!-- Jquery Library File -->
  <script src="js/jquery-3.7.1.min.js"></script>
  <!-- SlickNav js file -->
  <script src="js/jquery.slicknav.js"></script>
  <!-- Swiper js file -->
  <script src="js/swiper-bundle.min.js"></script>
  <!-- Counter js file -->
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.counterup.min.js"></script>
  <!-- Magnific js file -->
  <script src="js/jquery.magnific-popup.min.js"></script>
  <!-- SmoothScroll -->
  <script src="js/SmoothScroll.js"></script>
  <!-- Parallax js -->
  <script src="js/parallaxie.js"></script>
  <!-- MagicCursor js file -->
  <script src="js/gsap.min.js"></script>
  <script src="js/magiccursor.js"></script>
  <!-- Text Effect js file -->
  <script src="js/SplitText.min.js"></script>
  <script src="js/ScrollTrigger.min.js"></script>
  <!-- Main Custom js file -->
  <script src="js/function.js"></script>

</body>
</html>
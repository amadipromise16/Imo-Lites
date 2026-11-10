<?php
$page_title = "Videos | Imolites in Diaspora Social Club USA";
$meta_description = "Watch cultural celebrations, induction features, and club media from Imolites in Diaspora Social Club USA.";
$active_page = 'videos';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">Experience Imolites · Club Media</div>
      <h1 class="title">Our stories in motion</h1>
      <p class="lead">Explore our cultural celebrations and club videos. Tap on any video to play with sound.</p>
    </div>
  </section>

  <section class="section white">
    <div class="wrap">
      <div class="video-grid">
        <article class="video-card">
          <video controls playsinline poster="assets/induction-video-poster.jpg" preload="none">
            <source src="assets/induction-video.mp4" type="video/mp4"/>
            Your browser does not support video playback.
          </video>
          <h3>Imolites video feature</h3>
          <p>Club media • 3 min 43 sec</p>
        </article>

        <article class="video-card">
          <video controls playsinline poster="assets/club-video-poster.jpg" preload="none">
            <source src="assets/club-video.mp4" type="video/mp4"/>
            Your browser does not support video playback.
          </video>
          <h3>Imolites short feature</h3>
          <p>Club media • 35 sec</p>
        </article>
      </div>

      <p class="small-note" style="margin-top:28px">Video descriptions are intentionally neutral until approved captions are provided.</p>
      
      <div class="center" style="margin-top:40px;text-align:center">
        <a class="btn" href="gallery<?= $ext ?>">Explore Cultural Gallery →</a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>


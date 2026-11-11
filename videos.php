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

      <div style="margin-top:36px; padding:26px 28px; background:linear-gradient(135deg, #072e1e 0%, #0d4630 100%); border:1.5px solid var(--gold); border-radius:12px; text-align:center; color:#fff;">
        <h3 style="font-family:'Playfair Display',serif; color:#ffffff; font-size:22px; margin:0 0 8px;">View More of Our Videos on TikTok and Facebook</h3>
        <p style="color:#d0ded5; font-size:14.5px; max-width:620px; margin:0 auto 18px;">Stay entertained and connected with event highlights, cultural dances, celebration clips, and member spotlights directly on our official social channels.</p>
        <div style="display:inline-flex; gap:14px; flex-wrap:wrap; justify-content:center;">
          <a class="btn" href="https://www.tiktok.com/@imolitesusa1?_r=1&amp;_t=ZP-9BIUJp5vLWY" rel="noopener" target="_blank" style="display:inline-flex; align-items:center; gap:8px;">
            <span>🎵 Watch on TikTok</span> ↗
          </a>
          <a class="btn btn-outline" href="https://www.facebook.com/share/1KKe8uVxz8/?mibextid=wwXIfr" rel="noopener" target="_blank" style="display:inline-flex; align-items:center; gap:8px; border-color:var(--gold); color:#ffffff;">
            <span>📘 Watch on Facebook</span> ↗
          </a>
        </div>
      </div>

      <div class="center" style="margin-top:40px;text-align:center">
        <a class="btn" href="gallery<?= $ext ?>">Explore Cultural Gallery →</a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

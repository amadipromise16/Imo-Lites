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
          <h3>2026 Induction E-Brochure (Landscape Edition)</h3>
          <p>Official Gala E-Brochure • High Definition Video (624 MB)</p>
          <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
            <a class="btn" href="https://drive.usercontent.google.com/download?id=17YikZRwCrO4P5-VeyM81MltQjFPUPqc2&export=download&confirm=t" download style="padding:7px 12px; font-size:12.5px;">📥 Download Full HD (624 MB)</a>
            <a class="btn btn-outline" href="https://drive.google.com/file/d/17YikZRwCrO4P5-VeyM81MltQjFPUPqc2/view?usp=sharing" target="_blank" rel="noopener" style="padding:7px 12px; font-size:12.5px; border-color:var(--gold); color:#f3e0aa;">Google Drive Player ↗</a>
          </div>
        </article>

        <article class="video-card">
          <video controls playsinline poster="assets/charity-outreach-poster.jpg" preload="none">
            <source src="assets/charity-outreach-video.mp4" type="video/mp4"/>
            Your browser does not support video playback.
          </video>
          <h3>Imolites Charity Outreach — School for the Deaf</h3>
          <p>School for the Deaf, Ofekata, Orodo • Full Video • 3 min 48 sec</p>
          <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
            <a class="btn" href="assets/charity-outreach-video.mp4" download="imolites-charity-outreach-video.mp4" style="padding:7px 12px; font-size:12.5px;">📥 Download Video (MP4)</a>
            <a class="btn btn-outline" href="gallery<?= $ext ?>?category=charity" style="padding:7px 12px; font-size:12.5px; border-color:var(--gold); color:#f3e0aa;">View Outreach Section →</a>
          </div>
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

      <div style="margin-top:32px; padding:22px 24px; background:#ffffff; border:2px solid var(--gold); border-radius:12px; display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:16px;">
        <div style="display:flex; align-items:center; gap:16px;">
          <img src="assets/qr-induction-brochure.png" alt="QR Code" style="width:70px; height:70px; border-radius:8px; border:1px solid #d4af37;"/>
          <div>
            <h4 style="font-family:'Playfair Display',serif; color:#072e1e; font-size:18px; margin:0 0 4px;">Looking for the Portrait E-Brochure &amp; Official QR Code?</h4>
            <p style="color:#4a5c52; font-size:13.5px; margin:0;">Access the digital portrait layout, scan the branded QR code, or download for printing.</p>
          </div>
        </div>
        <a class="btn" href="gallery<?= $ext ?>?category=brochure" style="white-space:nowrap;">Open Brochure Section →</a>
      </div>

      <div style="margin-top:36px; padding:26px 28px; background:linear-gradient(135deg, #072e1e 0%, #0d4630 100%); border:1.5px solid var(--gold); border-radius:12px; text-align:center; color:#fff;">
        <h3 style="font-family:'Playfair Display',serif; color:#ffffff; font-size:22px; margin:0 0 8px;">View More of Our Videos on YouTube, TikTok and Facebook</h3>
        <p style="color:#d0ded5; font-size:14.5px; max-width:620px; margin:0 auto 18px;">Stay entertained and connected with event highlights, cultural dances, celebration clips, and member spotlights directly on our official social channels.</p>
        <div style="display:inline-flex; gap:14px; flex-wrap:wrap; justify-content:center;">
          <a class="btn btn-outline" href="https://www.youtube.com/results?search_query=Imolites+in+Diaspora+Social+Club+USA" rel="noopener" target="_blank" style="display:inline-flex; align-items:center; gap:8px; border-color:#ff4444; color:#ffffff; background:rgba(255,0,0,0.15);">
            <span>📺 Watch on YouTube</span> ↗
          </a>
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

<?php
$page_title = "Imolites in Diaspora Social Club USA | Progress &amp; Hospitality";
$meta_description = "Imolites in Diaspora Social Club USA — progress and hospitality, Imo heritage, community and our November 14, 2026 induction and end-of-year celebration.";
$active_page = 'home';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <!-- Full-Width Hero Banner Section -->
  <section class="hero-banner-section">
    <div class="wrap">
      <!-- The entire picture as the hero banner -->
      <div class="hero-full-banner">
        <img alt="Imolites in Diaspora Social Club USA - Progress and Hospitality Banner" fetchpriority="high" src="assets/club-banner.png"/>
      </div>

      <div class="hero-content">
        <div class="eyebrow">Culture · Community · Connection</div>
        <h1>Rooted in Imo.<br/><em>United in Progress.</em></h1>
        <p class="lead">Imolites in Diaspora Social Club USA brings sons, daughters, families, and friends of Imo State together through unity, cultural pride, and the spirit of hospitality.</p>
        <div class="actions">
          <a class="btn btn-event" href="event<?= $ext ?>">2026 Induction Celebration →</a>
          <a class="btn btn-outline" href="gallery<?= $ext ?>">Explore Our Gallery</a>
        </div>
      </div>
    </div>
  </section>

  <!-- 3-Pillar Gold Strip -->
  <div class="strip">
    <div>Our Heritage</div>
    <div>Our Community</div>
    <div>Our Progress</div>
  </div>

  <!-- Dedicated Sections / Explore Pages Grid -->
  <section class="section">
    <div class="wrap">
      <div class="head" style="text-align:center;margin:0 auto 36px">
        <div class="eyebrow">Discover Imolites</div>
        <h2 class="title">Explore Our Club &amp; Community</h2>
        <p class="intro">Discover each part of our community—from our rich Imo heritage and club purpose to our upcoming celebrations and cultural media.</p>
      </div>

      <div class="page-preview-grid">
        <div class="page-preview-card">
          <div>
            <div class="eyebrow" style="font-size:11px">Who We Are</div>
            <h3>Our Club</h3>
            <p>From the heart of Imo State to communities across America, we celebrate the bonds that connect us through friendship, cultural expression, and family.</p>
          </div>
          <a class="card-link" href="about<?= $ext ?>">Explore Our Club →</a>
        </div>

        <div class="page-preview-card">
          <div>
            <div class="eyebrow" style="font-size:11px">What Brings Us Together</div>
            <h3>Our Purpose</h3>
            <p>Built upon the three pillars of Unity &amp; Fellowship, Heritage &amp; Culture, and Progress &amp; Hospitality across generations.</p>
          </div>
          <a class="card-link" href="values<?= $ext ?>">Discover Our Purpose →</a>
        </div>

        <div class="page-preview-card" style="border-color:var(--gold);background:#fffdf7">
          <div>
            <div class="eyebrow" style="font-size:11px;color:#b58d34">November 14, 2026</div>
            <h3>2026 Induction</h3>
            <p>Join us at C &amp; C Ballroom in Garland, Texas for an unforgettable evening celebrating our members, heritage, and progress.</p>
          </div>
          <a class="card-link btn-event" href="event<?= $ext ?>">Induction Details &amp; Music →</a>
        </div>

        <div class="page-preview-card">
          <div>
            <div class="eyebrow" style="font-size:11px">Visual Story &amp; Snaps</div>
            <h3>Cultural Gallery</h3>
            <p>Explore event invitations, Imo State artwork, club crest mockups, or snap and upload your own photos directly to our live gallery!</p>
          </div>
          <a class="card-link" href="gallery<?= $ext ?>">View &amp; Upload Photos →</a>
        </div>

        <div class="page-preview-card">
          <div>
            <div class="eyebrow" style="font-size:11px">Stories In Motion</div>
            <h3>Video Library</h3>
            <p>Experience our cultural celebrations and club features in motion. Watch full club media and short features with sound.</p>
          </div>
          <a class="card-link" href="videos<?= $ext ?>">Watch Club Videos →</a>
        </div>

        <div class="page-preview-card">
          <div>
            <div class="eyebrow" style="font-size:11px">Stay Connected</div>
            <h3>Connect</h3>
            <p>Follow the Imolites community on Facebook, Instagram, TikTok, and YouTube for updates, moments, and event invitations.</p>
          </div>
          <a class="card-link" href="connect<?= $ext ?>">Get Connected →</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Call & Response Feature -->
  <section class="section white">
    <div class="wrap two">
      <div>
        <div class="eyebrow">Our Motto &amp; Spirit</div>
        <h2 class="title">A community that feels like home.</h2>
        <p class="intro">We honor our traditions while creating opportunities for in next generation to know where they come from, and to build where they are going.</p>
        <div style="margin-top:24px">
          <a class="btn" href="about<?= $ext ?>">Learn More About Us</a>
        </div>
      </div>

      <div class="dark-card">
        <div class="eyebrow">Our call &amp; response</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:38px;line-height:1.35;margin:18px 0;color:#ffffff">Imolites! <span style="color:#e5c278">Progress!</span><br/>Imolites! <span style="color:#e5c278">Progress!</span><br/>Progress! <span style="color:#e5c278">Imolites!</span></h2>
        <p>One shared heritage. One community. A commitment to progress and hospitality.</p>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

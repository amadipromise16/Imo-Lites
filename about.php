<?php
$page_title = "Our Club | Imolites in Diaspora Social Club USA";
$meta_description = "Learn about Imolites in Diaspora Social Club USA — who we are, our heritage, our call & response, and our commitment to progress and hospitality.";
$active_page = 'about';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">Who we are · Our Club</div>
      <h1 class="title">A community that feels like home.</h1>
      <p class="lead">Celebrating our shared heritage, creating meaningful progress, and connecting families across the diaspora.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap two">
      <div>
        <div class="eyebrow">Our Roots &amp; Mission</div>
        <h2 class="title">Honoring traditions, building the future.</h2>
        <p class="intro">From the heart of Imo State to communities across America, we celebrate the bonds that connect us. Imolites in Diaspora Social Club USA is a gathering place for friendship, cultural expression, family, and meaningful progress.</p>
        <p class="intro">We honor our traditions while creating opportunities for the next generation to know where they come from—and to build where they are going.</p>
        <div class="actions" style="justify-content:flex-start">
          <a class="btn" href="values<?= $ext ?>">Discover Our Purpose →</a>
          <a class="btn btn-outline" style="color:#072e1e;border-color:#072e1e" href="gallery<?= $ext ?>">Explore Our Gallery</a>
        </div>
      </div>

      <div class="dark-card">
        <div class="eyebrow">Our call &amp; response</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:38px;line-height:1.35;margin:18px 0;color:#ffffff">Imolites! <span style="color:#e5c278">Progress!</span><br/>Imolites! <span style="color:#e5c278">Progress!</span><br/>Progress! <span style="color:#e5c278">Imolites!</span></h2>
        <p>One shared heritage. One community. A commitment to progress and hospitality.</p>
      </div>
    </div>
  </section>

  <!-- 3-Pillar Gold Strip -->
  <div class="strip">
    <div>Our Heritage</div>
    <div>Our Community</div>
    <div>Our Progress</div>
  </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

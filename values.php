<?php
$page_title = "Our Purpose &amp; Values | Imolites in Diaspora Social Club USA";
$meta_description = "Discover the three foundational pillars of Imolites in Diaspora Social Club USA: Unity & fellowship, Heritage & culture, and Progress & hospitality.";
$active_page = 'values';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">What brings us together · Our Purpose</div>
      <h1 class="title">United by purpose.</h1>
      <p class="lead">Our club's spirit is reflected in the connections we make, the traditions we preserve, and the support we offer one another.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Our Three Pillars</div>
        <h2 class="title">The Foundations of Our Community</h2>
        <p class="intro">Each pillar represents a sacred duty to our forebears, our families today, and the generations yet to come.</p>
      </div>

      <div class="card-grid">
        <div class="value-card">
          <div class="num">01</div>
          <h3>Unity &amp; fellowship</h3>
          <p>Building bonds of friendship and mutual respect among Imolites at home and abroad.</p>
        </div>

        <div class="value-card">
          <div class="num">02</div>
          <h3>Heritage &amp; culture</h3>
          <p>Keeping the history, language, values, and celebrations of Imo State alive across generations.</p>
        </div>

        <div class="value-card">
          <div class="num">03</div>
          <h3>Progress &amp; hospitality</h3>
          <p>Creating an inclusive, welcoming community that celebrates achievements and moves forward together.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="section white">
    <div class="wrap two">
      <div>
        <div class="eyebrow">Imolites style</div>
        <h2 class="title">Our crest. Our identity.</h2>
        <p class="intro">From formal club occasions to everyday apparel, the original Imolites crest connects every generation to a shared identity. See how the approved emblem looks on polos, jackets, hats, patches, and more.</p>
        <div class="actions" style="justify-content:flex-start">
          <a class="btn" href="gallery<?= $ext ?>?category=branding">Explore Brand Imagery</a>
          <a class="btn btn-outline" style="color:#072e1e;border-color:#072e1e" href="event<?= $ext ?>">2026 Induction Gala</a>
        </div>
      </div>
      <img alt="Original crest displayed on polo shirts, baseball hats, jackets, patches and website" class="merch" loading="lazy" src="assets/merchandise.webp"/>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>


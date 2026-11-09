<?php
require_once __DIR__ . '/data/gallery_data.php';

$page_title = "Gallery | Imolites in Diaspora Social Club USA";
$meta_description = "Imolites club gallery featuring previously created induction art, Imo State-inspired imagery, original official logo and merchandise mockups.";
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">Imolites · Visual Gallery</div>
      <h1 class="title">Our heritage in pictures.</h1>
      <p class="lead">Celebrate our club, our shared roots, and the artistic spirit of Imo State through event promotions, cultural artwork and approved-crest mockups.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <div aria-label="Filter gallery" class="filterbar" role="group">
        <button class="filter active" data-filter="all">All images</button>
        <button class="filter" data-filter="events">Events &amp; invitations</button>
        <button class="filter" data-filter="heritage">Imo State artwork</button>
        <button class="filter" data-filter="branding">Official crest &amp; swag</button>
        <a class="filter-btn" href="#videos">Videos</a>
      </div>

      <div class="gallery-grid">
        <?php foreach ($gallery_items as $item): ?>
          <figure class="gallery-card" data-category="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>">
            <img alt="<?= htmlspecialchars($item['alt'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" tabindex="0"/>
            <?php if (!empty($item['title'])): ?>
              <figcaption><b><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></b><small><?= htmlspecialchars($item['subtitle'], ENT_QUOTES, 'UTF-8') ?></small></figcaption>
            <?php else: ?>
              <figcaption><?= htmlspecialchars($item['caption'], ENT_QUOTES, 'UTF-8') ?></figcaption>
            <?php endif; ?>
          </figure>
        <?php endforeach; ?>
      </div>

      <p class="small-note" style="margin-top:26px">Gallery notice: Some Imo State images are AI-generated artistic interpretations and should not be presented as verified photographs of specific locations. Event artwork is from earlier club creative projects.</p>
    </div>
  </section>
</main>

<div aria-label="Image preview" aria-modal="true" class="lightbox" role="dialog">
  <button aria-label="Close preview" class="close">×</button>
  <img alt="Enlarged gallery artwork" src=""/>
</div>

<section class="section white videos" id="videos">
  <div class="wrap">
    <div class="eyebrow">Club video library</div>
    <h2 class="title">Videos</h2>
    <div class="video-grid">
      <?php foreach ($video_items as $video): ?>
        <article class="video-card">
          <video controls="" playsinline="" poster="<?= htmlspecialchars($video['poster'], ENT_QUOTES, 'UTF-8') ?>" preload="none">
            <source src="<?= htmlspecialchars($video['src'], ENT_QUOTES, 'UTF-8') ?>" type="video/mp4"/>
          </video>
          <h3><?= htmlspecialchars($video['title'], ENT_QUOTES, 'UTF-8') ?></h3>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<script>
const buttons = [...document.querySelectorAll('.filter')],
      cards = [...document.querySelectorAll('.gallery-card')];

function apply(v) {
  buttons.forEach(b => b.classList.toggle('active', b.dataset.filter === v));
  cards.forEach(c => c.classList.toggle('hidden', v !== 'all' && c.dataset.category !== v));
}

buttons.forEach(b => b.addEventListener('click', () => {
  apply(b.dataset.filter);
  const targetPage = window.location.pathname.endsWith('.html') ? 'gallery.html' : 'gallery.php';
  history.replaceState(null, '', b.dataset.filter === 'all' ? targetPage : '?category=' + b.dataset.filter);
}));

let v = new URLSearchParams(location.search).get('category');
if (['events', 'heritage', 'branding'].includes(v)) apply(v);

let lightbox = document.querySelector('.lightbox');
document.querySelectorAll('.gallery-card img').forEach(im => {
  function show() {
    lightbox.querySelector('img').src = im.src;
    lightbox.querySelector('img').alt = im.alt;
    lightbox.classList.add('open');
  }
  im.addEventListener('click', show);
  im.addEventListener('keydown', e => {
    if (e.key === 'Enter') show();
  });
});

document.querySelector('.close').addEventListener('click', () => lightbox.classList.remove('open'));
lightbox.addEventListener('click', e => {
  if (e.target === lightbox) lightbox.classList.remove('open');
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') lightbox.classList.remove('open');
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
$page_title = "Imolites in Diaspora Social Club USA | Progress &amp; Hospitality";
$meta_description = "Imolites in Diaspora Social Club USA — progress and hospitality, Imo heritage, community and our November 14, 2026 induction and end-of-year celebration.";
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="hero">
    <div class="wrap hero-grid">
      <div>
        <div class="eyebrow">Culture · Community · Connection</div>
        <h1>Rooted in Imo.<br/><em>United in Progress.</em></h1>
        <p class="lead">Imolites in Diaspora Social Club USA brings sons, daughters, families, and friends of Imo State together through unity, cultural pride, and the spirit of hospitality.</p>
        <div class="actions">
          <a class="btn" href="#event">2026 Induction Celebration →</a>
          <a class="btn btn-outline" href="gallery<?= $ext ?>">Explore Our Gallery</a>
        </div>
      </div>
      <div class="hero-mark">
        <img alt="Approved official club emblem displaying Imo State local government areas" fetchpriority="high" src="assets/official-logo.webp"/>
      </div>
    </div>
  </section>

  <div class="strip">
    <div>Our Heritage</div>
    <div>Our Community</div>
    <div>Our Progress</div>
  </div>

  <section class="section" id="about">
    <div class="wrap two">
      <div>
        <div class="eyebrow">Who we are</div>
        <h2 class="title">A community that feels like home.</h2>
        <p class="intro">From the heart of Imo State to communities across America, we celebrate the bonds that connect us. Imolites in Diaspora Social Club USA is a gathering place for friendship, cultural expression, family, and meaningful progress.</p>
        <p class="intro">We honor our traditions while creating opportunities for the next generation to know where they come from—and to build where they are going.</p>
        <a class="btn" href="#values">Discover Our Purpose</a>
      </div>
      <div class="dark-card">
        <div class="eyebrow">Our call &amp; response</div>
        <h2 style="font-family:Playfair Display,serif;font-size:42px;line-height:1.35;margin:18px 0">Imolites! <span style="color:#e5c278">Progress!</span><br/>Imolites! <span style="color:#e5c278">Progress!</span><br/>Progress! <span style="color:#e5c278">Imolites!</span></h2>
        <p>One shared heritage. One community. A commitment to progress and hospitality.</p>
      </div>
    </div>
  </section>

  <section class="section white" id="values">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">What brings us together</div>
        <h2 class="title">United by purpose.</h2>
        <p class="intro">Our club's spirit is reflected in the connections we make, the traditions we preserve, and the support we offer one another.</p>
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

  <section class="section event" id="event">
    <div class="wrap event-flex">
      <div>
        <img alt="Existing Imolites induction and year-end gala 2026 invitation" class="poster" loading="lazy" src="assets/gala-poster.webp"/>
      </div>
      <div>
        <div class="eyebrow">Upcoming celebration · November 2026</div>
        <h2 class="title">Induction &amp; End-of-Year Celebration</h2>
        <p class="intro">Join us for an unforgettable evening celebrating our members, our heritage, and the progress of Imolites in Diaspora Social Club USA.</p>
        <div class="detail"><b>DATE</b><span>Saturday, November 14, 2026</span></div>
        <div class="detail"><b>TIME</b><span>9:00 PM Central Time</span></div>
        <div class="detail"><b>VENUE</b><span>C &amp; C Ballroom<br/>3338 Broadway Blvd, #304<br/>Garland, TX 75043</span></div>
        <div class="detail"><b>THEME</b><span>Unity · Culture · Heritage · Progress</span></div>
        <div class="actions">
          <a class="btn" href="https://www.google.com/maps/search/?api=1&amp;query=C%26C+Ballroom+3338+Broadway+Blvd+Garland+TX+75043" rel="noopener" target="_blank">Get Directions ↗</a>
          <a class="btn btn-outline" href="gallery<?= $ext ?>?category=events">View Event Artwork</a>
        </div>
        <p class="event-note">Event information shown from the club's previously created official invitation. RSVP/ticketing details are not yet provided.</p>
      </div>
    </div>
  </section>

  <section class="section" id="gallery">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Our visual story</div>
        <h2 class="title">The people, the culture, the celebrations.</h2>
        <p class="intro">Explore previously created Imolites event artwork, images celebrating Imo State, and the club's official crest in use.</p>
      </div>
      <div class="gallery-preview">
        <a href="gallery<?= $ext ?>?category=events"><img alt="Gold and burgundy club gala invitation" loading="lazy" src="assets/gala-invitation.webp"/></a>
        <a href="gallery<?= $ext ?>?category=heritage"><img alt="Creative visual tribute to Imo State heritage" loading="lazy" src="assets/heritage-mosaic.webp"/></a>
        <a href="gallery<?= $ext ?>?category=branding"><img alt="Merchandise mockups using original Imolites crest" loading="lazy" src="assets/merchandise.webp"/></a>
      </div>
      <div class="center">
        <a class="btn" href="gallery<?= $ext ?>">View Full Gallery →</a>
      </div>
    </div>
  </section>

  <section class="section white videos" id="videos">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Experience Imolites</div>
        <h2 class="title">Our stories in motion</h2>
        <p>Explore our cultural celebrations and club videos. Tap to play with sound.</p>
      </div>
      <div class="video-grid">
        <article class="video-card">
          <video controls="" playsinline="" poster="assets/induction-video-poster.jpg" preload="none">
            <source src="assets/induction-video.mp4" type="video/mp4"/>
            Your browser does not support video playback.
          </video>
          <h3>Imolites video feature</h3>
          <p>Club media • 3 min 43 sec</p>
        </article>
        <article class="video-card">
          <video controls="" playsinline="" poster="assets/club-video-poster.jpg" preload="none">
            <source src="assets/club-video.mp4" type="video/mp4"/>
            Your browser does not support video playback.
          </video>
          <h3>Imolites short feature</h3>
          <p>Club media • 35 sec</p>
        </article>
      </div>
      <p class="small-note">Video descriptions are intentionally neutral until approved captions are provided.</p>
      <div class="center">
        <a class="btn" href="gallery<?= $ext ?>?category=videos">Explore media gallery</a>
      </div>
    </div>
  </section>

  <section class="section white">
    <div class="wrap two">
      <div>
        <div class="eyebrow">Imolites style</div>
        <h2 class="title">Our crest. Our identity.</h2>
        <p class="intro">From formal club occasions to everyday apparel, the original Imolites crest connects every generation to a shared identity. See how the approved emblem looks on polos, jackets, hats, patches, and more.</p>
        <a class="btn" href="gallery<?= $ext ?>?category=branding">Explore Brand Imagery</a>
      </div>
      <img alt="Original crest displayed on polo shirts, baseball hats, jackets, patches and website" class="merch" loading="lazy" src="assets/merchandise.webp"/>
    </div>
  </section>

  <section class="section" id="connect">
    <div class="wrap">
      <div class="head">
        <div class="eyebrow">Stay connected</div>
        <h2 class="title">Follow the Imolites community.</h2>
        <p class="intro">Find community updates, celebrations, event invitations, and moments from across the diaspora.</p>
      </div>
      <div class="socials">
        <a class="social" href="https://www.facebook.com/search/top?q=Imolites%20in%20Diaspora%20Social%20Club%20USA" rel="noopener" target="_blank">Facebook search ↗</a>
        <a class="social" href="https://www.instagram.com/explore/tags/imolites/" rel="noopener" target="_blank">Instagram #Imolites ↗</a>
        <a class="social" href="https://www.tiktok.com/search?q=Imolites%20in%20Diaspora" rel="noopener" target="_blank">TikTok search ↗</a>
        <a class="social" href="https://www.youtube.com/results?search_query=Imolites+in+Diaspora+Social+Club+USA" rel="noopener" target="_blank">YouTube search ↗</a>
      </div>
      <p class="small-note">These are platform discovery links, not verified official club profiles. Replace with the club's direct account URLs before publishing.</p>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<?php
$page_title = "2026 Induction Celebration | Imolites in Diaspora Social Club USA";
$meta_description = "Join Imolites in Diaspora Social Club USA for our historic Induction & End-of-Year Celebration on Saturday, November 14, 2026 at C & C Ballroom in Garland, TX.";
$active_page = 'event';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">Upcoming celebration · November 2026</div>
      <h1 class="title">Induction &amp; End-of-Year Celebration</h1>
      <p class="lead">Join us for an unforgettable evening celebrating our members, our heritage, and the progress of Imolites in Diaspora Social Club USA.</p>
    </div>
  </section>

  <section class="section event" style="border:none">
    <div class="wrap event-flex">
      <div>
        <img alt="Existing Imolites induction and year-end gala 2026 invitation" class="poster" loading="lazy" src="assets/gala-poster.webp"/>
      </div>
      <div>
        <div class="eyebrow" style="color:var(--gold-light)">Formal Gala Invitation</div>
        <h2 class="title" style="color:#ffffff">Celebrate Our Progress &amp; Hospitality</h2>
        <p class="intro">An evening of cultural pageantry, induction ceremonies, fellowship, and celebration with Imolites and friends from across the diaspora.</p>

        <div class="detail">
          <b>DATE</b>
          <span>Saturday, November 14, 2026</span>
        </div>
        <div class="detail">
          <b>TIME</b>
          <span>9:00 PM Central Time</span>
        </div>
        <div class="detail">
          <b>VENUE</b>
          <span>C &amp; C Ballroom<br/>3338 Broadway Blvd, #304<br/>Garland, TX 75043</span>
        </div>
        <div class="detail">
          <b>THEME</b>
          <span>Unity · Culture · Heritage · Progress</span>
        </div>

        <div class="actions" style="justify-content:flex-start">
          <a class="btn" href="https://www.google.com/maps/search/?api=1&amp;query=C%26C+Ballroom+3338+Broadway+Blvd+Garland+TX+75043" rel="noopener" target="_blank">Get Directions ↗</a>
          <a class="btn btn-outline" href="gallery<?= $ext ?>?category=events">View Event Artwork</a>
        </div>
        <p class="event-note">Event information shown from the club's previously created official invitation. RSVP/ticketing details are not yet provided.</p>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

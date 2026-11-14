<?php
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';
?>
<footer class="footer">
  <div class="wrap">
    <div class="footgrid">
      <div>
        <div class="eyebrow">IMOLITES IN DIASPORA SOCIAL CLUB USA</div>
        <h2 style="font-family:'Playfair Display',serif;font-size:28px;color:#ffffff;margin:8px 0 14px">Progress &amp; Hospitality.</h2>
        <p>Preserving our heritage, celebrating our community, and building connections across generations.</p>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="about<?= $ext ?>">Our Club &amp; Story</a>
        <a href="values<?= $ext ?>">Our Purpose &amp; Values</a>
        <a href="event<?= $ext ?>">2026 Induction &amp; Gala</a>
        <a href="gallery<?= $ext ?>">Cultural Gallery</a>
        <a href="videos<?= $ext ?>">Video Library</a>
      </div>
      <div>
        <h4>Get connected</h4>
        <a href="connect<?= $ext ?>">Social Media &amp; Community</a>
        <a href="https://www.facebook.com/share/1KKe8uVxz8/?mibextid=wwXIfr" rel="noopener" target="_blank">Official Facebook ↗</a>
        <a href="https://www.tiktok.com/@imolitesusa1?_r=1&amp;_t=ZP-9BIUJp5vLWY" rel="noopener" target="_blank">TikTok @imolitesusa1 ↗</a>
        <a href="https://www.youtube.com/results?search_query=Imolites+in+Diaspora+Social+Club+USA" rel="noopener" target="_blank">Official YouTube ↗</a>
        <a href="https://www.google.com/maps/search/?api=1&amp;query=3338+Broadway+Blvd+Garland+TX+75043" rel="noopener" target="_blank">Event location ↗</a>
        <a href="admin<?= $ext ?>" style="color:var(--gold,#d4af37); font-weight:600;">🔒 Admin Portal</a>
      </div>
    </div>
    <div class="copyright">© <span id="yr"><?= date('Y') ?></span> Imolites in Diaspora Social Club USA · Powered by <strong>Digimadi media</strong> · Official crest retained without redesign.</div>
  </div>
</footer>

<!-- 2026 Induction Celebration Audio -->
<audio id="event-audio" preload="auto" playsinline>
  <source src="assets/event-music.mp3" type="audio/mpeg"/>
</audio>

<div id="event-audio-badge" class="event-audio-badge" style="display:none;" aria-live="polite">
  <span class="audio-equalizer" id="audio-equalizer" aria-hidden="true">
    <span></span><span></span><span></span><span></span>
  </span>
  <span id="audio-status-text">🎵 2026 Induction Music · Tap to Play</span>
  <button id="event-audio-toggle" type="button" aria-label="Play or pause celebration music" title="Play / Pause">▶</button>
</div>

<script>
document.querySelector('.hamb')?.addEventListener('click', function() {
  let n = document.querySelector('.navlinks');
  n.classList.toggle('open');
  this.setAttribute('aria-expanded', n.classList.contains('open'));
});
document.querySelectorAll('.navlinks a').forEach(a => a.addEventListener('click', () => document.querySelector('.navlinks')?.classList.remove('open')));
document.querySelector('#yr').textContent = new Date().getFullYear();

// Induction Celebration Audio Controller
(function() {
  const audio = document.getElementById('event-audio');
  const badge = document.getElementById('event-audio-badge');
  const toggleBtn = document.getElementById('event-audio-toggle');
  const statusText = document.getElementById('audio-status-text');
  const equalizer = document.getElementById('audio-equalizer');
  if (!audio) return;

  audio.loop = false; // Must play once through to the end

  const path = window.location.pathname.toLowerCase();
  const search = window.location.search.toLowerCase();
  const isEventPage = path.includes('event') || search.includes('event');

  function updateUI(playing) {
    if (!badge) return;
    badge.style.display = 'flex';
    if (playing) {
      if (statusText) statusText.textContent = '🎵 Playing 2026 Induction Music';
      if (toggleBtn) {
        toggleBtn.textContent = '⏸';
        toggleBtn.setAttribute('aria-label', 'Pause celebration music');
        toggleBtn.title = 'Pause';
      }
      if (equalizer) equalizer.classList.add('playing');
    } else {
      if (statusText) statusText.textContent = '🎵 2026 Induction Music · Tap to Play';
      if (toggleBtn) {
        toggleBtn.textContent = '▶';
        toggleBtn.setAttribute('aria-label', 'Play celebration music');
        toggleBtn.title = 'Play';
      }
      if (equalizer) equalizer.classList.remove('playing');
    }
  }

  function startAudio() {
    if (!audio.paused) {
      updateUI(true);
      return Promise.resolve();
    }
    const playPromise = audio.play();
    if (playPromise !== undefined) {
      return playPromise.then(() => {
        updateUI(true);
      }).catch(err => {
        updateUI(false);
        throw err;
      });
    }
    updateUI(!audio.paused);
    return Promise.resolve();
  }

  function pauseAudio() {
    audio.pause();
    updateUI(false);
  }

  // Toggle button click
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
      if (audio.paused) {
        startAudio().catch(() => {});
      } else {
        pauseAudio();
      }
    });
  }

  // Entire badge clickable
  if (badge) {
    badge.addEventListener('click', function(e) {
      if (e.target === toggleBtn) return;
      if (audio.paused) {
        startAudio().catch(() => {});
      } else {
        pauseAudio();
      }
    });
  }

  // When audio finishes, stop and update UI
  audio.addEventListener('ended', function() {
    updateUI(false);
    if (statusText) statusText.textContent = '🎵 2026 Induction Music Finished';
  });

  // Track user tapping/clicking navigation links leading to Event page
  const eventLinkSelectors = [
    'a[href*="event"]',
    'a[href*="category=events"]',
    '.filter[data-filter="events"]',
    '.navcta',
    '.btn-event'
  ];
  document.querySelectorAll(eventLinkSelectors.join(',')).forEach(el => {
    ['click', 'touchend'].forEach(evt => {
      el.addEventListener(evt, function() {
        try { sessionStorage.setItem('imolites_play_event_music', '1'); } catch(e){}
      }, { passive: true });
    });
  });

  // If user is on Event page
  if (isEventPage) {
    updateUI(false);

    // Function to unlock audio on first user touch anywhere on the page
    const unlockGesture = function() {
      startAudio().then(() => {
        ['touchstart', 'touchend', 'click', 'scroll', 'pointerdown'].forEach(ev => {
          window.removeEventListener(ev, unlockGesture);
          document.removeEventListener(ev, unlockGesture);
        });
      }).catch(() => {});
    };

    ['touchstart', 'touchend', 'click', 'scroll', 'pointerdown'].forEach(ev => {
      window.addEventListener(ev, unlockGesture, { passive: true });
      document.addEventListener(ev, unlockGesture, { passive: true });
    });

    // Also attempt immediate play if user already clicked an event link or browser allows
    startAudio().catch(() => {
      // Autoplay blocked by mobile browser - waiting for user touch
    });
  }
})();
</script>
</body>
</html>

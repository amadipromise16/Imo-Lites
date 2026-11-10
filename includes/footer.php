<?php
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';
?>
<footer class="footer">
  <div class="wrap">
    <div class="footgrid">
      <div>
        <div class="eyebrow">IMOLITES IN DIASPORA SOCIAL CLUB USA</div>
        <h2 style="font-family:Playfair Display,serif;font-size:30px;color:white">Progress &amp; Hospitality.</h2>
        <p>Preserving our heritage, celebrating our community, and building connections across generations.</p>
      </div>
      <div>
        <h4>Explore</h4>
        <a href="index<?= $ext ?>#about">About us</a>
        <a href="index<?= $ext ?>#event">Induction &amp; Year-End Party</a>
        <a href="gallery<?= $ext ?>">Cultural gallery</a>
      </div>
      <div>
        <h4>Get connected</h4>
        <a href="index<?= $ext ?>#connect">Social media</a>
        <a href="https://www.google.com/maps/search/?api=1&amp;query=3338+Broadway+Blvd+Garland+TX+75043" rel="noopener" target="_blank">Event location ↗</a>
      </div>
    </div>
    <div class="copyright">© <span id="yr"><?= date('Y') ?></span> Imolites in Diaspora Social Club USA · Official crest retained without redesign. Gallery contains promotional artwork and creative Imo State illustrations.</div>
  </div>
</footer>

<!-- 2026 Induction Celebration Audio -->
<audio id="event-audio" preload="auto" playsinline>
  <source src="assets/event-music.mp3" type="audio/mpeg"/>
</audio>

<div id="event-audio-badge" class="event-audio-badge" style="display:none;" aria-live="polite">
  <span>🎵 2026 Induction Music</span>
  <button id="event-audio-toggle" aria-label="Pause audio" title="Pause / Play">⏸</button>
</div>

<script>
document.querySelector('.hamb')?.addEventListener('click',function(){
  let n=document.querySelector('.navlinks');
  n.classList.toggle('open');
  this.setAttribute('aria-expanded',n.classList.contains('open'))
});
document.querySelectorAll('.navlinks a').forEach(a=>a.addEventListener('click',()=>document.querySelector('.navlinks')?.classList.remove('open')));
document.querySelector('#yr').textContent=new Date().getFullYear();

// Induction Celebration Audio Controller
(function() {
  const audio = document.getElementById('event-audio');
  const badge = document.getElementById('event-audio-badge');
  const toggleBtn = document.getElementById('event-audio-toggle');
  if (!audio) return;

  audio.loop = false;
  let hasStarted = false;

  function showBadge() {
    if (badge) badge.style.display = 'flex';
  }
  function hideBadge() {
    if (badge) badge.style.display = 'none';
  }

  function playInductionAudio() {
    if (hasStarted) return;
    hasStarted = true;
    try {
      const p = audio.play();
      if (p !== undefined) {
        p.then(() => {
          showBadge();
        }).catch(err => {
          hasStarted = false;
        });
      }
    } catch(e) {
      hasStarted = false;
    }
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      if (audio.paused) {
        audio.play();
        toggleBtn.textContent = '⏸';
      } else {
        audio.pause();
        toggleBtn.textContent = '▶';
      }
    });
  }

  audio.addEventListener('ended', function() {
    hideBadge();
  });

  const eventSelectors = [
    'a[href*="#event"]',
    'a[href*="category=events"]',
    '.filter[data-filter="events"]',
    '.navcta'
  ];

  document.querySelectorAll(eventSelectors.join(',')).forEach(el => {
    ['click', 'touchend'].forEach(evt => {
      el.addEventListener(evt, function() {
        playInductionAudio();
        try {
          sessionStorage.setItem('imolites_play_event', '1');
        } catch(e) {}
      }, { passive: true });
    });
  });

  try {
    const shouldPlay = window.location.hash === '#event' || sessionStorage.getItem('imolites_play_event') === '1';
    if (shouldPlay) {
      sessionStorage.removeItem('imolites_play_event');
      playInductionAudio();
      if (!hasStarted) {
        const unlock = function() {
          playInductionAudio();
          window.removeEventListener('click', unlock);
          window.removeEventListener('touchend', unlock);
        };
        window.addEventListener('click', unlock, { once: true, passive: true });
        window.addEventListener('touchend', unlock, { once: true, passive: true });
      }
    }
  } catch(e) {}
})();
</script>
</body>
</html>

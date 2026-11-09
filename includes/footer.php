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
<script>
document.querySelector('.hamb')?.addEventListener('click',function(){
  let n=document.querySelector('.navlinks');
  n.classList.toggle('open');
  this.setAttribute('aria-expanded',n.classList.contains('open'))
});
document.querySelectorAll('.navlinks a').forEach(a=>a.addEventListener('click',()=>document.querySelector('.navlinks')?.classList.remove('open')));
document.querySelector('#yr').textContent=new Date().getFullYear();
</script>
</body>
</html>

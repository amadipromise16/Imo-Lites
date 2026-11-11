<?php
if (!isset($page_title)) {
    $page_title = "Imolites in Diaspora Social Club USA | Progress &amp; Hospitality";
}
if (!isset($meta_description)) {
    $meta_description = "Imolites in Diaspora Social Club USA — progress and hospitality, Imo heritage, community and our November 14, 2026 induction and end-of-year celebration.";
}
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';
if (!isset($active_page)) {
    $active_page = 'home';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width,initial-scale=1" name="viewport"/>
  <meta content="#032417" name="theme-color"/>
  <meta content="<?= htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8') ?>" name="description"/>
  <title><?= $page_title ?></title>
  <link rel="icon" href="assets/crest-logo.png?v=2" type="image/png"/>
  <link href="style.css?v=20261111c" rel="stylesheet"/>
</head>
<body>
<div class="topline">Our roots unite us. Progress moves us forward.</div>
<header class="nav">
  <div class="wrap nav-row">
    <a class="brand" href="index<?= $ext ?>">
      <img alt="Official Imolites club crest" src="assets/crest-logo.png?v=2"/>
      <span><strong>IMOLITES</strong><small>IN DIASPORA · SOCIAL CLUB USA</small></span>
    </a>
    <button aria-expanded="false" aria-label="Open navigation" class="hamb">☰</button>
    <nav class="navlinks" id="navlinks">
      <a class="<?= $active_page === 'home' ? 'active' : '' ?>" href="index<?= $ext ?>">Home</a>
      <a class="<?= $active_page === 'about' ? 'active' : '' ?>" href="about<?= $ext ?>">Our Club</a>
      <a class="<?= $active_page === 'values' ? 'active' : '' ?>" href="values<?= $ext ?>">Our Purpose</a>
      <a class="<?= $active_page === 'event' ? 'active' : '' ?>" href="event<?= $ext ?>">2026 Induction</a>
      <a class="<?= $active_page === 'videos' ? 'active' : '' ?>" href="videos<?= $ext ?>">Videos</a>
      <a class="<?= $active_page === 'gallery' ? 'active' : '' ?>" href="gallery<?= $ext ?>">Gallery</a>
      <a class="<?= $active_page === 'connect' ? 'active' : '' ?>" href="connect<?= $ext ?>">Connect</a>
      <a class="navcta" href="event<?= $ext ?>">Event Details</a>
    </nav>
  </div>
</header>

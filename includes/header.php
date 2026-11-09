<?php
if (!isset($page_title)) {
    $page_title = "Imolites in Diaspora Social Club USA | Progress &amp; Hospitality";
}
if (!isset($meta_description)) {
    $meta_description = "Imolites in Diaspora Social Club USA — progress and hospitality, Imo heritage, community and our November 14, 2026 induction and end-of-year celebration.";
}
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width,initial-scale=1" name="viewport"/>
  <meta content="#03271c" name="theme-color"/>
  <meta content="<?= htmlspecialchars($meta_description, ENT_QUOTES, 'UTF-8') ?>" name="description"/>
  <title><?= $page_title ?></title>
  <link rel="icon" href="assets/official-logo.webp" type="image/webp"/>
  <link href="style.css" rel="stylesheet"/>
</head>
<body>
<div class="topline">Our roots unite us. Progress moves us forward.</div>
<header class="nav">
  <div class="wrap nav-row">
    <a class="brand" href="index<?= $ext ?>">
      <img alt="Original official Imolites club crest" src="assets/official-logo.webp"/>
      <span><strong>IMOLITES</strong><small>IN DIASPORA · SOCIAL CLUB USA</small></span>
    </a>
    <button aria-expanded="false" aria-label="Open navigation" class="hamb">☰</button>
    <nav class="navlinks" id="navlinks">
      <a href="index<?= $ext ?>#about">Our Club</a>
      <a href="index<?= $ext ?>#values">Our Purpose</a>
      <a href="index<?= $ext ?>#event">2026 Induction</a>
      <a href="index<?= $ext ?>#videos">Videos</a>
      <a href="gallery<?= $ext ?>">Gallery</a>
      <a href="index<?= $ext ?>#connect">Connect</a>
      <a class="navcta" href="index<?= $ext ?>#event">Event Details</a>
    </nav>
  </div>
</header>

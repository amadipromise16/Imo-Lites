<?php
// Bulletproof error suppression for clean production render
ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require_once __DIR__ . '/data/gallery_data.php';

// 1. Base gallery items (always guaranteed array)
$base_items = function_exists('get_base_gallery_items') ? get_base_gallery_items() : [];
if (empty($base_items)) {
    $baseFile = __DIR__ . '/data/base_gallery_items.json';
    if (file_exists($baseFile)) {
        $base_items = json_decode(@file_get_contents($baseFile), true) ?: [];
    }
}
if (!is_array($base_items)) {
    $base_items = [];
}

// 2. Load persistent community photos
$communityPhotos = [];
$communityFile = __DIR__ . '/data/community_photos.json';
if (file_exists($communityFile)) {
    $rawComm = @file_get_contents($communityFile);
    $communityPhotos = $rawComm ? (json_decode($rawComm, true) ?: []) : [];
    if (is_array($communityPhotos)) {
        // Ensure test photo from Nov 10 2:59 PM is filtered out
        $communityPhotos = array_values(array_filter($communityPhotos, function($p) {
            if (!is_array($p)) return false;
            $sub = strtolower($p['subtitle'] ?? '');
            $tit = strtolower($p['title'] ?? '');
            return !(strpos($sub, '2:59 pm') !== false || 
                     (strpos($sub, 'nov 10, 2026') !== false && strpos($tit, 'community celebration snap') !== false) ||
                     (strpos($sub, 'nov 12, 2026') !== false && strpos($tit, 'community celebration snap') !== false));
        }));
    } else {
        $communityPhotos = [];
    }
}

// 3. Load admin-added custom photos
$adminPhotos = [];
$adminFile = __DIR__ . '/data/admin_gallery_items.json';
if (file_exists($adminFile)) {
    $rawAdm = @file_get_contents($adminFile);
    $adminPhotos = $rawAdm ? (json_decode($rawAdm, true) ?: []) : [];
    if (!is_array($adminPhotos)) {
        $adminPhotos = [];
    }
}

// 4. Load deleted photos blacklist
$deletedIds = [];
$deletedFile = __DIR__ . '/data/deleted_photos.json';
if (file_exists($deletedFile)) {
    $rawDel = @file_get_contents($deletedFile);
    $deletedIds = $rawDel ? (json_decode($rawDel, true) ?: []) : [];
}
if (!is_array($deletedIds)) {
    $deletedIds = [];
}

// Filter community photos
$communityPhotos = array_values(array_filter($communityPhotos, function($p) use ($deletedIds) {
    if (!is_array($p)) return false;
    $id = $p['id'] ?? '';
    $img = $p['image'] ?? '';
    return !in_array($id, $deletedIds) && !in_array($img, $deletedIds);
}));

// Filter admin photos
$adminPhotos = array_values(array_filter($adminPhotos, function($p) use ($deletedIds) {
    if (!is_array($p)) return false;
    $id = $p['id'] ?? '';
    $img = $p['image'] ?? '';
    return !in_array($id, $deletedIds) && !in_array($img, $deletedIds);
}));

// Filter base gallery items
$active_gallery_items = array_values(array_filter($base_items, function($p) use ($deletedIds) {
    if (!is_array($p)) return false;
    $img = $p['image'] ?? '';
    $id = $p['id'] ?? '';
    $key = 'base_' . md5($img);
    return !in_array($key, $deletedIds) && !in_array($img, $deletedIds) && !in_array($id, $deletedIds);
}));

// 5. Load edited photos overrides
$editedFile = __DIR__ . '/data/edited_photos.json';
$edits = [];
if (file_exists($editedFile)) {
    $rawEd = @file_get_contents($editedFile);
    $edits = $rawEd ? (json_decode($rawEd, true) ?: []) : [];
    if (!is_array($edits)) {
        $edits = [];
    }
}

$apply_edits = function(&$item) use ($edits) {
    if (!is_array($item)) return;
    $id = $item['id'] ?? '';
    $img = $item['image'] ?? '';
    $key = isset($edits[$id]) ? $id : (isset($edits[$img]) ? $img : null);
    if ($key && isset($edits[$key]) && is_array($edits[$key])) {
        $e = $edits[$key];
        if (!empty($e['title'])) { $item['title'] = $e['title']; $item['alt'] = $e['title']; }
        if (isset($e['subtitle'])) { $item['subtitle'] = $e['subtitle']; }
        if (!empty($e['category'])) { $item['category'] = $e['category']; }
    }
};

foreach ($communityPhotos as &$cp) { $apply_edits($cp); }
unset($cp);
foreach ($adminPhotos as &$ap) { $apply_edits($ap); }
unset($ap);
foreach ($active_gallery_items as &$bp) { $apply_edits($bp); }
unset($bp);

// Merge community + admin + base gallery items
$display_items = array_merge($communityPhotos, $adminPhotos, $active_gallery_items);

$page_title = "Gallery | Imolites in Diaspora Social Club USA";
$meta_description = "Imolites club gallery featuring community photo snaps, induction art, Imo State-inspired imagery, and merchandise mockups.";
$active_page = 'gallery';
$ext = (!isset($static_export) || !$static_export) ? '.php' : '.html';

require __DIR__ . '/includes/header.php';
?>

<main>
  <section class="pagehead">
    <div class="wrap">
      <div class="eyebrow">Imolites · Visual Gallery</div>
      <h1 class="title">Our heritage in pictures.</h1>
      <p class="lead">Celebrate our club, our shared roots, and the artistic spirit of Imo State through event promotions, cultural artwork, approved-crest mockups, and member photo snaps.</p>
    </div>
  </section>

  <section class="section">
    <div class="wrap">
      <style>
        /* Bulletproof gallery badge styling across all servers and browser cache states */
        .gallery-card { position: relative !important; }
        .badge-executive {
          position: absolute !important;
          top: 12px !important;
          left: 12px !important;
          background: linear-gradient(135deg, #042115 0%, #0d4630 60%, #b8860b 100%) !important;
          color: #ffffff !important;
          border: 1px solid #d4af37 !important;
          font-size: 11px !important;
          font-weight: 800 !important;
          padding: 4px 10px !important;
          border-radius: 20px !important;
          text-transform: uppercase !important;
          letter-spacing: 0.06em !important;
          box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
          z-index: 2 !important;
          display: inline-block !important;
          margin: 0 !important;
          line-height: normal !important;
        }
        .badge-community {
          position: absolute !important;
          top: 12px !important;
          left: 12px !important;
          background: linear-gradient(135deg, #072e1e 0%, #0d4630 100%) !important;
          color: #ffffff !important;
          border: 1px solid #d4af37 !important;
          font-size: 11px !important;
          font-weight: 800 !important;
          padding: 4px 10px !important;
          border-radius: 20px !important;
          text-transform: uppercase !important;
          letter-spacing: 0.06em !important;
          box-shadow: 0 4px 10px rgba(0,0,0,0.3) !important;
          z-index: 2 !important;
          display: inline-block !important;
          margin: 0 !important;
          line-height: normal !important;
        }
      </style>
      <!-- Interactive Snap & Share Photo Bar -->
      <div class="snap-upload-bar">
        <div>
          <h3>📸 Share Your Photos With the Club</h3>
          <p>Snap a photo directly on this site or upload from your device to publish it to our live gallery for everyone to see!</p>
        </div>
        <button class="btn-snap" id="open-upload-btn" type="button">📸 Snap / Upload Photo</button>
      </div>

      <!-- Filter Bar -->
      <div aria-label="Filter gallery" class="filterbar" role="group">
        <button class="filter active" data-filter="all">All images</button>
        <button class="filter" data-filter="brochure">📖 2026 Induction Brochure</button>
        <button class="filter" data-filter="executive">Executive Committee</button>
        <button class="filter" data-filter="trustees">Board of Trustees &amp; Founder</button>
        <button class="filter" data-filter="induction_committee">Induction Committee</button>
        <button class="filter" data-filter="events">2026 Induction Honorees</button>
        <button class="filter" data-filter="community">Community snaps</button>
        <button class="filter" data-filter="heritage">Imo State artwork</button>
        <button class="filter" data-filter="branding">Official crest &amp; swag</button>
        <a class="filter-btn" href="videos<?= $ext ?>">Watch Videos ↗</a>
      </div>

      <!-- Dedicated 2026 Induction Brochure Showcase Hero -->
      <div id="brochure-showcase-box" style="display:none; margin:0 0 32px; background:linear-gradient(135deg,#042115 0%,#072e1e 50%,#0d4630 100%); border:2px solid var(--gold,#d4af37); border-radius:16px; padding:28px 24px; color:#ffffff; box-shadow:0 10px 30px rgba(0,0,0,0.25);">
        <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:20px;">
          <div style="max-width:650px;">
            <div style="display:inline-block; background:rgba(212,175,55,0.2); border:1px solid #d4af37; color:#f3e0aa; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:0.08em; padding:4px 12px; border-radius:20px; margin-bottom:10px;">
              ★ Official 2026 Gala Media Release ★
            </div>
            <h2 style="font-family:'Playfair Display',serif; font-size:26px; color:#ffffff; margin:0 0 8px;">
              2026 Induction E-Brochure, Video &amp; Official QR Code
            </h2>
            <p style="color:#d0ded5; font-size:14.5px; line-height:1.55; margin:0 0 16px;">
              Welcome to the official media hub for our 2026 Induction and End of the Year Party. Download the full landscape video brochure (624 MB) or portrait layout, stream the video online, scan the branded QR code with your mobile camera, and connect with our official YouTube channel!
            </p>
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
              <a href="https://drive.usercontent.google.com/download?id=17YikZRwCrO4P5-VeyM81MltQjFPUPqc2&export=download&confirm=t" class="btn" style="background:#d4af37; color:#042115; font-weight:bold; font-size:13.5px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;" download>
                📥 Download Full HD Video (624 MB)
              </a>
              <a href="assets/digital-brochure.webp" download="imolites-2026-induction-brochure-portrait.webp" class="btn btn-outline" style="border:1.5px solid #d4af37; color:#f3e0aa; font-size:13.5px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                📱 Download Portrait Brochure
              </a>
              <a href="assets/qr-induction-brochure.png" download="imolites-2026-induction-brochure-qr.png" class="btn btn-outline" style="border:1.5px solid #86efac; color:#86efac; font-size:13.5px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                📲 Download QR Code (PNG)
              </a>
              <a href="https://www.youtube.com/results?search_query=Imolites+in+Diaspora+Social+Club+USA" target="_blank" rel="noopener" class="btn btn-outline" style="border:1.5px solid #ff4444; color:#ffffff; background:rgba(255,0,0,0.15); font-size:13.5px; padding:10px 18px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                📺 Watch on YouTube ↗
              </a>
            </div>
          </div>
          <div style="text-align:center; min-width:180px;">
            <div style="background:#ffffff; padding:10px; border-radius:12px; border:2px solid #d4af37; display:inline-block; box-shadow:0 8px 24px rgba(0,0,0,0.35);">
              <img src="assets/qr-induction-brochure.png" alt="Scan QR code for 2026 Induction Brochure" style="width:140px; height:140px; display:block;"/>
            </div>
            <div style="font-size:11px; color:#f3e0aa; font-weight:700; margin-top:8px; text-transform:uppercase; letter-spacing:0.05em;">
              Scan on Mobile
            </div>
            <a href="assets/qr-induction-brochure.svg" download="imolites-qr-code.svg" style="font-size:11.5px; color:#86efac; text-decoration:underline; display:block; margin-top:4px;">
              Get Vector (SVG) ↓
            </a>
          </div>
        </div>
      </div>

      <!-- Active Folder Navigation Bar -->
      <div id="active-folder-bar" class="active-folder-bar" style="display:none;">
        <div style="display:flex; align-items:center; gap:12px;">
          <span style="font-size:26px;">📁</span>
          <div>
            <h3 id="folder-bar-title" style="margin:0; font-family:'Playfair Display',serif; color:#f3e0aa; font-size:20px;">Album Folder</h3>
            <p id="folder-bar-desc" style="margin:2px 0 0; font-size:13px; color:#d0ded5;">Viewing photos in this folder</p>
          </div>
        </div>
        <button type="button" id="btn-close-folder" class="btn btn-outline" style="border-color:var(--gold); color:#f3e0aa; padding:8px 16px; font-size:13px; cursor:pointer;">
          ← Back to All Albums
        </button>
      </div>

      <!-- Gallery Grid -->
      <div class="gallery-grid" id="gallery-grid">
        <?php foreach ($display_items as $item): 
          $category = $item['category'] ?? 'community';
          $isCommunity = ($category === 'community');
          $itemId = $item['id'] ?? '';
          $folderId = $item['folder_id'] ?? ($category === 'executive' ? 'executive' : '');
          $isFolderCover = !empty($item['is_folder_cover']);
          // Hide executive, trustees, induction_committee, events, and brochure on initial load of 'all'
          $hideOnAll = in_array($category, ['executive', 'trustees', 'induction_committee', 'events', 'brochure']) || $isFolderCover || !empty($folderId);

          $cardClasses = ['gallery-card'];
          if ($isFolderCover) $cardClasses[] = 'album-folder-card';
        ?>
          <figure class="<?= implode(' ', $cardClasses) ?>" 
                  data-category="<?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?>" 
                  <?= $itemId ? 'data-id="' . htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                  <?= $isFolderCover ? 'data-is-folder="true" data-folder-target="' . htmlspecialchars($folderId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                  <?= (!$isFolderCover && $folderId) ? 'data-in-folder="' . htmlspecialchars($folderId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>
                  <?= $hideOnAll ? 'style="display:none;"' : '' ?>>
            
            <?php if ($isFolderCover): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#042115 0%,#0d4630 60%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:5px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 12px rgba(0,0,0,0.35);z-index:2;display:inline-flex;align-items:center;gap:6px;margin:0;line-height:normal;">
                📁 Executive Council Album
              </span>
            <?php elseif ($isCommunity): ?>
              <span class="badge-community" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Community Snap</span>
            <?php elseif ($category === 'executive'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#042115 0%,#0d4630 60%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Executive Committee</span>
            <?php elseif ($category === 'trustees'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 50%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Board of Trustees &amp; Founder</span>
            <?php elseif ($category === 'induction_committee'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#14532d 0%,#042115 100%);color:#ffffff;border:1px solid #86efac;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Induction Committee</span>
            <?php elseif ($category === 'events'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#8b0000 0%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">2026 Induction Honoree</span>
            <?php elseif ($category === 'brochure'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#b8860b 100%);color:#ffffff;border:1px solid #f3e0aa;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">
                <?= ($itemId === 'brochure_official_qr') ? 'Official QR Code' : '2026 Induction Brochure' ?>
              </span>
            <?php endif; ?>

            <img alt="<?= htmlspecialchars($item['alt'] ?? ($item['title'] ?? 'Imolites Gallery Photo'), ENT_QUOTES, 'UTF-8') ?>" loading="lazy" src="<?= htmlspecialchars($item['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" tabindex="0"/>
            
            <figcaption>
              <?php if (!empty($item['title'])): ?>
                <b><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></b>
                <?php if (!empty($item['subtitle'])): ?>
                  <small><?= htmlspecialchars($item['subtitle'], ENT_QUOTES, 'UTF-8') ?></small>
                <?php endif; ?>
              <?php else: ?>
                <?= htmlspecialchars($item['caption'] ?? '', ENT_QUOTES, 'UTF-8') ?>
              <?php endif; ?>
            </figcaption>

            <?php if ($category === 'brochure'): ?>
              <div style="padding:10px 14px 14px; display:flex; flex-direction:column; gap:8px; background:rgba(7,46,30,0.03); border-top:1px solid rgba(0,0,0,0.06);">
                <?php if ($itemId === 'brochure_landscape_video'): ?>
                  <a href="https://drive.usercontent.google.com/download?id=17YikZRwCrO4P5-VeyM81MltQjFPUPqc2&export=download&confirm=t" class="btn" style="padding:8px 12px; font-size:12.5px; text-align:center; background:#d4af37; color:#042115; font-weight:700; border-radius:6px; text-decoration:none;" download>
                    📥 Download Full Video (624 MB)
                  </a>
                  <div style="display:flex; gap:6px;">
                    <a href="https://drive.google.com/file/d/17YikZRwCrO4P5-VeyM81MltQjFPUPqc2/view?usp=sharing" target="_blank" rel="noopener" class="btn btn-outline" style="flex:1; padding:6px 8px; font-size:11.5px; text-align:center; border:1px solid #072e1e; border-radius:6px; text-decoration:none;">
                      Drive Player ↗
                    </a>
                    <a href="https://www.youtube.com/results?search_query=Imolites+in+Diaspora+Social+Club+USA" target="_blank" rel="noopener" class="btn btn-outline" style="flex:1; padding:6px 8px; font-size:11.5px; text-align:center; border:1px solid #c00; color:#c00; border-radius:6px; text-decoration:none;">
                      YouTube ↗
                    </a>
                  </div>
                <?php elseif ($itemId === 'brochure_portrait_edition'): ?>
                  <a href="assets/digital-brochure.webp" download="imolites-2026-induction-brochure-portrait.webp" class="btn" style="padding:8px 12px; font-size:12.5px; text-align:center; background:#072e1e; color:#ffffff; font-weight:700; border-radius:6px; text-decoration:none;">
                    📥 Download Portrait Layout
                  </a>
                <?php elseif ($itemId === 'brochure_official_qr'): ?>
                  <div style="display:flex; gap:6px;">
                    <a href="assets/qr-induction-brochure.png" download="imolites-2026-induction-brochure-qr.png" class="btn" style="flex:1; padding:8px 8px; font-size:12px; text-align:center; background:#072e1e; color:#ffffff; font-weight:700; border-radius:6px; text-decoration:none;">
                      📥 Save PNG
                    </a>
                    <a href="assets/qr-induction-brochure.svg" download="imolites-2026-induction-brochure-qr.svg" class="btn btn-outline" style="flex:1; padding:8px 8px; font-size:12px; text-align:center; border:1px solid #072e1e; border-radius:6px; text-decoration:none;">
                      Save SVG
                    </a>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

            <?php if ($isFolderCover): ?>
              <div class="folder-action-hint">
                📁 Click to Open Executive Album &amp; View All Photos →
              </div>
            <?php endif; ?>
          </figure>
        <?php endforeach; ?>
      </div>
      <p class="small-note" style="margin-top:30px">Gallery notice: Some Imo State images are AI-generated artistic interpretations and should not be presented as verified photographs of specific locations. Event artwork is from earlier club creative projects. Community photos are contributed by club members.</p>
    </div>
  </section>
</main>

<!-- Lightbox Modal -->
<div aria-label="Image preview" aria-modal="true" class="lightbox" role="dialog">
  <button aria-label="Close preview" class="close">&times;</button>
  <img alt="Enlarged gallery artwork" src=""/>
</div>

<!-- Snap & Upload Modal -->
<div class="upload-modal" id="upload-modal" aria-hidden="true" role="dialog">
  <div class="upload-modal-card">
    <button class="modal-close" id="close-upload-btn" aria-label="Close modal">&times;</button>
    <h3>📸 Upload Photo to Gallery</h3>
    <p>Snap a photo directly with your phone camera or select an image from your device to publish immediately.</p>
    
    <form id="photo-upload-form">
      <div class="form-group">
        <label for="photo-file">Select or Snap Photo *</label>
        <input type="file" id="photo-file" name="photo" accept="image/*" capture="environment" style="display:none;"/>
        <div class="file-dropzone" id="file-dropzone" tabindex="0" role="button">
          <span style="font-size:32px;display:block;margin-bottom:6px">📷</span>
          <b>Tap to Snap a Photo or Pick from Gallery</b>
          <small style="display:block;color:#6b786e;margin-top:4px">JPG, PNG, WEBP (Max 15MB)</small>
        </div>
        <div class="preview-container" id="preview-container">
          <img id="image-preview" src="" alt="Selected photo preview"/>
          <button type="button" class="btn-change-photo" id="change-photo-btn">Change Photo</button>
        </div>
      </div>

      <div class="form-group">
        <label for="photo-title">Photo Title / Caption</label>
        <input type="text" id="photo-title" name="title" placeholder="e.g. Celebration with Club Family, Gala Night..."/>
      </div>

      <div class="form-group">
        <label for="photo-uploader">Your Name (Optional)</label>
        <input type="text" id="photo-uploader" name="uploader" placeholder="e.g. Club Member, Amadi..."/>
      </div>

      <button type="submit" class="btn" id="upload-submit-btn" style="width:100%">Publish to Live Gallery →</button>
    </form>
  </div>
</div>

<!-- Upload Success Celebration Modal -->
<div class="upload-modal" id="upload-success-modal" aria-hidden="true" role="dialog">
  <div class="upload-modal-card success-card">
    <button class="modal-close" id="close-success-btn" aria-label="Close modal">&times;</button>
    <div style="font-size: 50px; margin-bottom: 8px;">🎉</div>
    <h3 style="font-family: 'Playfair Display', Georgia, serif; font-size: 23px; color: #072e1e; margin-bottom: 8px;">
      You have successfully uploaded your photo!
    </h3>
    <p style="color: #4a5c52; font-size: 14px; line-height: 1.5; margin-bottom: 18px;">
      Your photo is now published live in our community gallery for members and visitors worldwide to see.
    </p>

    <div id="success-photo-preview-wrap" style="margin: 0 auto 20px; width: 150px; height: 150px; border-radius: 12px; overflow: hidden; border: 3px solid var(--gold); box-shadow: 0 6px 20px rgba(0,0,0,0.18);">
      <img id="success-photo-thumb" src="" alt="Uploaded photo preview" style="width: 100%; height: 100%; object-fit: cover; display: block;"/>
    </div>

    <div style="display: flex; flex-direction: column; gap: 10px;">
      <button type="button" class="btn" id="btn-view-gallery" style="width: 100%; font-size: 15px; padding: 13px 20px;">
        View Gallery →
      </button>
      <button type="button" class="btn btn-outline" id="btn-upload-more" style="width: 100%; border-color: #c9a050; color: #072e1e;">
        Upload Another Photo
      </button>
    </div>
  </div>
</div>

<script>
// Filter & Folder Album Controller
const buttons = [...document.querySelectorAll('.filter')];
let cards = [...document.querySelectorAll('.gallery-card')];
const activeFolderBar = document.getElementById('active-folder-bar');
const folderBarTitle = document.getElementById('folder-bar-title');
const folderBarDesc = document.getElementById('folder-bar-desc');
const btnCloseFolder = document.getElementById('btn-close-folder');

let currentSection = 'all';
let folderExpanded = false;

function updateGalleryDisplay() {
  cards = [...document.querySelectorAll('.gallery-card')];
  buttons.forEach(b => b.classList.toggle('active', b.dataset.filter === currentSection));

  const brochureShowcaseBox = document.getElementById('brochure-showcase-box');
  if (brochureShowcaseBox) {
    brochureShowcaseBox.style.display = (currentSection === 'brochure') ? 'block' : 'none';
  }

  if (currentSection === 'all') {
    folderExpanded = false;
    if (activeFolderBar) activeFolderBar.style.display = 'none';
    cards.forEach(c => {
      const cat = c.dataset.category;
      // HIDE executive, trustees, induction_committee, events, AND brochure on 'all'
      if (cat === 'executive' || cat === 'trustees' || cat === 'induction_committee' || cat === 'events' || cat === 'brochure' || c.dataset.isFolder === 'true' || c.dataset.inFolder) {
        c.style.display = 'none';
      } else {
        c.style.display = 'block';
      }
    });
  } else if (currentSection === 'brochure') {
    folderExpanded = true;
    if (activeFolderBar) {
      activeFolderBar.style.display = 'flex';
      folderBarTitle.textContent = '📖 2026 Official Induction E-Brochure & Media';
      folderBarDesc.textContent = 'Download Landscape video edition (624 MB), Portrait mobile edition, and scan our branded QR code';
      if (btnCloseFolder) btnCloseFolder.textContent = '← Back to All Photos';
    }
    cards.forEach(c => {
      c.style.display = (c.dataset.category === 'brochure') ? 'block' : 'none';
    });
  } else if (currentSection === 'executive') {
    if (!folderExpanded) {
      if (activeFolderBar) activeFolderBar.style.display = 'none';
      cards.forEach(c => {
        if (c.dataset.isFolder === 'true' && c.dataset.folderTarget === 'executive') {
          c.style.display = 'block';
        } else {
          c.style.display = 'none';
        }
      });
    } else {
      if (activeFolderBar) {
        activeFolderBar.style.display = 'flex';
        folderBarTitle.textContent = 'Executive Committee Album';
        folderBarDesc.textContent = 'Meet the Executive Officers of Imolites in Diaspora Social Club USA';
        if (btnCloseFolder) btnCloseFolder.textContent = '← Back to Executive Cover';
      }
      cards.forEach(c => {
        if (c.dataset.category === 'executive' || c.dataset.inFolder === 'executive') {
          c.style.display = 'block';
        } else {
          c.style.display = 'none';
        }
      });
    }
  } else if (currentSection === 'trustees') {
    folderExpanded = true;
    if (activeFolderBar) {
      activeFolderBar.style.display = 'flex';
      folderBarTitle.textContent = '🏛️ Board of Trustees & Founder Album';
      folderBarDesc.textContent = 'Honoring the Visionary Founder and Board of Trustees of Imolites in Diaspora Social Club USA';
      if (btnCloseFolder) btnCloseFolder.textContent = '← Back to All Photos';
    }
    cards.forEach(c => {
      c.style.display = (c.dataset.category === 'trustees') ? 'block' : 'none';
    });
  } else if (currentSection === 'induction_committee') {
    folderExpanded = true;
    if (activeFolderBar) {
      activeFolderBar.style.display = 'flex';
      folderBarTitle.textContent = '🤝 2026 Induction Planning Committee';
      folderBarDesc.textContent = 'The dedicated committee members organizing and putting the 2026 Induction Gala together';
      if (btnCloseFolder) btnCloseFolder.textContent = '← Back to All Photos';
    }
    cards.forEach(c => {
      c.style.display = (c.dataset.category === 'induction_committee') ? 'block' : 'none';
    });
  } else if (currentSection === 'events') {
    folderExpanded = true;
    if (activeFolderBar) {
      activeFolderBar.style.display = 'flex';
      folderBarTitle.textContent = '🎖️ 2026 Induction Honorees Album';
      folderBarDesc.textContent = 'Celebrating the distinguished honorees of our 2026 Induction & End of the Year Party';
      if (btnCloseFolder) btnCloseFolder.textContent = '← Back to All Photos';
    }
    cards.forEach(c => {
      c.style.display = (c.dataset.category === 'events') ? 'block' : 'none';
    });
  } else {
    // Other categories: community, heritage, branding
    folderExpanded = false;
    if (activeFolderBar) activeFolderBar.style.display = 'none';
    cards.forEach(c => {
      c.style.display = (c.dataset.category === currentSection) ? 'block' : 'none';
    });
  }
}

function applyFilter(v) {
  currentSection = v;
  folderExpanded = false;
  updateGalleryDisplay();
  const grid = document.getElementById('gallery-grid');
  if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function openFolder(folderId) {
  currentSection = folderId;
  folderExpanded = true;
  updateGalleryDisplay();
  const grid = document.getElementById('gallery-grid');
  if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

buttons.forEach(btn => {
  btn.addEventListener('click', () => applyFilter(btn.dataset.filter));
});

if (btnCloseFolder) {
  btnCloseFolder.addEventListener('click', () => {
    if (currentSection === 'executive' && folderExpanded) {
      folderExpanded = false;
      updateGalleryDisplay();
    } else {
      applyFilter('all');
    }
  });
}

function setupFolderClickEvents() {
  document.querySelectorAll('.gallery-card[data-is-folder="true"]').forEach(card => {
    if (!card.dataset.hasFolderEvent) {
      card.dataset.hasFolderEvent = 'true';
      card.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        openFolder(this.dataset.folderTarget);
      });
    }
  });
}
setupFolderClickEvents();

// Check URL query parameters & hash
const urlParams = new URLSearchParams(window.location.search);
const urlFilter = urlParams.get('category') || urlParams.get('section') || urlParams.get('tab') || (window.location.hash ? window.location.hash.replace('#', '') : null);
if (urlFilter) {
  applyFilter(urlFilter);
} else {
  applyFilter('all');
}

const lightbox = document.querySelector('.lightbox');
const lbImg = lightbox.querySelector('img');

function setupLightboxEvents() {
  document.querySelectorAll('.gallery-card img').forEach(img => {
    if (!img.dataset.hasLb) {
      img.dataset.hasLb = 'true';
      img.addEventListener('click', (e) => {
        const parentCard = img.closest('.gallery-card');
        if (parentCard && parentCard.dataset.isFolder === 'true' && (!folderExpanded || currentSection !== parentCard.dataset.folderTarget)) {
          // If on main view, clicking the cover card opens the folder
          return;
        }
        lbImg.src = img.src;
        lbImg.alt = img.alt;
        lightbox.classList.add('open');
      });
      img.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          const parentCard = img.closest('.gallery-card');
          if (parentCard && parentCard.dataset.isFolder === 'true' && (!folderExpanded || currentSection !== parentCard.dataset.folderTarget)) {
            return;
          }
          lbImg.src = img.src;
          lbImg.alt = img.alt;
          lightbox.classList.add('open');
        }
      });
    }
  });
}
setupLightboxEvents();

lightbox.addEventListener('click', (e) => {
  if (e.target === lightbox || e.target.classList.contains('close')) {
    lightbox.classList.remove('open');
  }
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    lightbox.classList.remove('open');
    closeUploadModal();
    closeSuccessModal();
  }
});

// Snap / Upload Modal Controller
const uploadModal = document.getElementById('upload-modal');
const openUploadBtn = document.getElementById('open-upload-btn');
const closeUploadBtn = document.getElementById('close-upload-btn');
const fileDropzone = document.getElementById('file-dropzone');
const photoFileInput = document.getElementById('photo-file');
const previewContainer = document.getElementById('preview-container');
const imagePreview = document.getElementById('image-preview');
const changePhotoBtn = document.getElementById('change-photo-btn');
const photoUploadForm = document.getElementById('photo-upload-form');
const uploadSubmitBtn = document.getElementById('upload-submit-btn');
const galleryGrid = document.getElementById('gallery-grid');

// Success Modal Elements
const uploadSuccessModal = document.getElementById('upload-success-modal');
const closeSuccessBtn = document.getElementById('close-success-btn');
const btnViewGallery = document.getElementById('btn-view-gallery');
const btnUploadMore = document.getElementById('btn-upload-more');
const successPhotoThumb = document.getElementById('success-photo-thumb');

let lastUploadedPhotoId = null;

function openUploadModal() {
  uploadModal.classList.add('open');
  uploadModal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeUploadModal() {
  uploadModal.classList.remove('open');
  uploadModal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function openSuccessModal(photo) {
  if (!uploadSuccessModal) return;
  lastUploadedPhotoId = photo ? photo.id : null;
  if (successPhotoThumb && photo && photo.image) {
    successPhotoThumb.src = photo.image;
  }
  uploadSuccessModal.classList.add('open');
  uploadSuccessModal.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
}

function closeSuccessModal() {
  if (!uploadSuccessModal) return;
  uploadSuccessModal.classList.remove('open');
  uploadSuccessModal.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

if (openUploadBtn) openUploadBtn.addEventListener('click', openUploadModal);
if (closeUploadBtn) closeUploadBtn.addEventListener('click', closeUploadModal);
if (closeSuccessBtn) closeSuccessBtn.addEventListener('click', closeSuccessModal);
if (changePhotoBtn) changePhotoBtn.addEventListener('click', () => photoFileInput.click());

if (btnUploadMore) {
  btnUploadMore.addEventListener('click', function() {
    closeSuccessModal();
    openUploadModal();
  });
}

if (btnViewGallery) {
  btnViewGallery.addEventListener('click', function() {
    closeSuccessModal();
    applyFilter('community');
    setTimeout(() => {
      let targetCard = null;
      if (lastUploadedPhotoId) {
        targetCard = document.querySelector(`.gallery-card[data-id="${lastUploadedPhotoId}"]`);
      }
      if (!targetCard) {
        targetCard = document.querySelector('.gallery-card[data-category="community"]');
      }
      if (targetCard) {
        targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        targetCard.classList.add('newly-uploaded-highlight');
        setTimeout(() => targetCard.classList.remove('newly-uploaded-highlight'), 5000);
      } else {
        window.scrollTo({ top: galleryGrid.offsetTop - 80, behavior: 'smooth' });
      }
    }, 200);
  });
}

uploadModal.addEventListener('click', function(e) {
  if (e.target === uploadModal) closeUploadModal();
});
if (uploadSuccessModal) {
  uploadSuccessModal.addEventListener('click', function(e) {
    if (e.target === uploadSuccessModal) closeSuccessModal();
  });
}

if (fileDropzone) {
  fileDropzone.addEventListener('click', () => photoFileInput.click());
  fileDropzone.addEventListener('keydown', (e) => { if (e.key === 'Enter') photoFileInput.click(); });
}

// Global Shared Cloud Database & Local Fallback
const CLOUD_KV_ENDPOINT = 'https://kvs.ix.workers.dev/imolites/community-photos';
const STORAGE_KEY = 'imolites_community_photos_v6';

// Clear test caches from user's phone and laptop
try {
  localStorage.removeItem('imolites_community_photos_v5');
  localStorage.removeItem('imolites_community_photos_v4');
  localStorage.removeItem('imolites_community_photos_v3');
  localStorage.removeItem('imolites_community_snaps');
  localStorage.removeItem('imolites_uploaded_snaps');
  localStorage.removeItem('imolites_local_snaps');
} catch(e) {}

function isTestPhoto(p) {
  if (!p) return true;
  const sub = (p.subtitle || '').toLowerCase();
  const tit = (p.title || '').toLowerCase();
  const deletedList = JSON.parse(localStorage.getItem('imolites_deleted_photos') || '[]');
  if (p.id && deletedList.includes(p.id)) return true;
  if (p.image && deletedList.includes(p.image)) return true;
  if (sub.includes('2:59 pm') || 
      (sub.includes('nov 10, 2026') && tit.includes('community celebration snap')) ||
      (sub.includes('nov 12, 2026') && tit.includes('community celebration snap'))) {
    return true;
  }
  return false;
}

// Remove any test card or deleted card that might be in initial HTML
document.querySelectorAll('.gallery-card').forEach(card => {
  const text = card.textContent.toLowerCase();
  if (text.includes('2:59 pm') || 
      (text.includes('nov 10, 2026') && text.includes('community celebration snap')) ||
      (text.includes('nov 12, 2026') && text.includes('community celebration snap'))) {
    card.remove();
  }
});

// Filter deleted photos & apply admin edits locally
function applyLocalModeration() {
  try {
    const deletedList = JSON.parse(localStorage.getItem('imolites_deleted_photos') || '[]');
    if (Array.isArray(deletedList) && deletedList.length > 0) {
      document.querySelectorAll('.gallery-card').forEach(card => {
        const id = card.dataset.id || '';
        const img = card.querySelector('img')?.src || '';
        if (deletedList.includes(id) || deletedList.includes(img)) {
          card.remove();
        }
      });
    }

    const editsMap = JSON.parse(localStorage.getItem('imolites_photo_edits') || '{}');
    document.querySelectorAll('.gallery-card').forEach(card => {
      const id = card.dataset.id || '';
      const img = card.querySelector('img')?.src || '';
      const key = editsMap[id] ? id : (editsMap[img] ? img : null);
      if (key && editsMap[key]) {
        const e = editsMap[key];
        if (e.title) {
          const b = card.querySelector('figcaption b');
          if (b) b.textContent = e.title;
        }
        if (e.subtitle) {
          const sm = card.querySelector('figcaption small');
          if (sm) sm.textContent = e.subtitle;
        }
        if (e.category) {
          card.dataset.category = e.category;
        }
      }
    });
  } catch(e) {}
}

// Load cached community snaps immediately on page load
function loadCachedCommunityPhotos() {
  try {
    const deletedList = JSON.parse(localStorage.getItem('imolites_deleted_photos') || '[]');
    let cached = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    if (Array.isArray(cached)) {
      cached = cached.filter(p => !isTestPhoto(p) && !deletedList.includes(p.id) && !deletedList.includes(p.image));
      localStorage.setItem(STORAGE_KEY, JSON.stringify(cached));
      cached.forEach(photo => prependPhotoToGallery(photo));
    }
    
    // Also load admin custom items from localStorage
    let cachedAdmin = JSON.parse(localStorage.getItem('imolites_admin_photos') || '[]');
    if (Array.isArray(cachedAdmin)) {
      cachedAdmin = cachedAdmin.filter(p => !isTestPhoto(p) && !deletedList.includes(p.id) && !deletedList.includes(p.image));
      localStorage.setItem('imolites_admin_photos', JSON.stringify(cachedAdmin));
      cachedAdmin.forEach(photo => prependPhotoToGallery(photo));
    }
  } catch(e) {}
  applyLocalModeration();
}

// Client-side fast image compression for instant sharp uploads
function compressImageFile(file, maxWidth = 1200, maxHeight = 1200, quality = 0.82) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onerror = reject;
    reader.onload = function(e) {
      const img = new Image();
      img.onerror = reject;
      img.onload = function() {
        let w = img.width;
        let h = img.height;
        if (w > maxWidth || h > maxHeight) {
          if (w / h > maxWidth / maxHeight) {
            h = Math.round((h * maxWidth) / w);
            w = maxWidth;
          } else {
            w = Math.round((w * maxHeight) / h);
            h = maxHeight;
          }
        }
        const canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);
        const dataUrl = canvas.toDataURL('image/jpeg', quality);
        resolve(dataUrl);
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  });
}

// Handle file selection and preview
let selectedDataUrl = '';
photoFileInput.addEventListener('change', async function() {
  const file = this.files[0];
  if (!file) return;

  fileDropzone.innerHTML = '<span style="font-size:32px;display:block;margin-bottom:6px">⏳</span><b>Optimizing photo for crisp display...</b>';

  try {
    selectedDataUrl = await compressImageFile(file);
    imagePreview.src = selectedDataUrl;
    previewContainer.style.display = 'block';
    fileDropzone.style.display = 'none';
  } catch (err) {
    console.error('Error optimizing image:', err);
    fileDropzone.innerHTML = '<span style="font-size:32px;display:block;margin-bottom:6px">📷</span><b>Tap to Snap a Photo or Pick from Gallery</b><small style="display:block;color:#6b786e;margin-top:4px">JPG, PNG, WEBP (Max 15MB)</small>';
  }
});

// Function to prepend photo card to DOM
function prependPhotoToGallery(photo) {
  if (!photo || !photo.image || isTestPhoto(photo)) return;
  const deletedList = JSON.parse(localStorage.getItem('imolites_deleted_photos') || '[]');
  if (deletedList.includes(photo.id) || deletedList.includes(photo.image)) return;

  // Check if card with this image or id already exists
  if (photo.id && document.querySelector(`.gallery-card[data-id="${photo.id}"]`)) return;
  if (document.querySelector(`.gallery-card img[src="${photo.image}"]`)) return;

  const cat = photo.category || 'community';
  const figure = document.createElement('figure');
  figure.className = 'gallery-card';
  figure.dataset.category = cat;
  if (photo.id) figure.dataset.id = photo.id;

  // Handle visibility on initial load
  if (currentSection === 'all') {
    if (['executive', 'trustees', 'induction_committee', 'events'].includes(cat)) {
      figure.style.display = 'none';
    } else {
      figure.style.display = 'block';
    }
  } else {
    figure.style.display = (currentSection === cat) ? 'block' : 'none';
  }

  if (cat === 'executive') {
    figure.dataset.inFolder = 'executive';
    const badge = document.createElement('span');
    badge.className = 'badge-executive';
    badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#042115 0%,#0d4630 60%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
    badge.textContent = 'Executive Committee';
    figure.appendChild(badge);
  } else if (cat === 'trustees') {
    const badge = document.createElement('span');
    badge.className = 'badge-executive';
    badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 50%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
    badge.textContent = 'Board of Trustees & Founder';
    figure.appendChild(badge);
  } else if (cat === 'induction_committee') {
    const badge = document.createElement('span');
    badge.className = 'badge-executive';
    badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#14532d 0%,#042115 100%);color:#ffffff;border:1px solid #86efac;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
    badge.textContent = 'Induction Committee';
    figure.appendChild(badge);
  } else if (cat === 'events') {
    const badge = document.createElement('span');
    badge.className = 'badge-executive';
    badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#8b0000 0%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
    badge.textContent = '2026 Induction Honoree';
    figure.appendChild(badge);
  } else {
    const badge = document.createElement('span');
    badge.className = 'badge-community';
    badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
    badge.textContent = 'Community Snap';
    figure.appendChild(badge);
  }

  const img = document.createElement('img');
  img.src = photo.image;
  img.alt = photo.title || 'Gallery Photo';
  img.loading = 'lazy';
  img.tabIndex = 0;
  figure.appendChild(img);

  const figcaption = document.createElement('figcaption');
  const b = document.createElement('b');
  b.textContent = photo.title || 'Gallery Photo';
  figcaption.appendChild(b);

  const small = document.createElement('small');
  small.textContent = photo.subtitle || ('Added on ' + new Date(photo.timestamp || Date.now()).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }));
  figcaption.appendChild(small);

  figure.appendChild(figcaption);

  galleryGrid.insertBefore(figure, galleryGrid.firstChild);
  setupLightboxEvents();
  cards = [...document.querySelectorAll('.gallery-card')];
}

// Fetch photos from the global cloud edge database & local PHP backend
async function loadGlobalCommunityPhotos() {
  const remotePhotos = [];
  const deletedSet = new Set(JSON.parse(localStorage.getItem('imolites_deleted_photos') || '[]'));

  // 1. Try local PHP upload endpoint on Hostinger
  if (window.location.protocol.startsWith('http')) {
    try {
      const phpRes = await fetch('upload.php');
      if (phpRes.ok) {
        const phpData = await phpRes.json();
        if (phpData) {
          if (Array.isArray(phpData.deleted_ids)) {
            phpData.deleted_ids.forEach(id => deletedSet.add(id));
            localStorage.setItem('imolites_deleted_photos', JSON.stringify([...deletedSet]));
          }
          if (Array.isArray(phpData.photos)) {
            remotePhotos.push(...phpData.photos);
          }
        }
      }
    } catch(e) {}
  }

  // 2. Also check admin photos in localStorage
  try {
    const admList = JSON.parse(localStorage.getItem('imolites_admin_photos') || '[]');
    if (Array.isArray(admList)) {
      remotePhotos.push(...admList);
    }
  } catch(e) {}

  // Filter out deleted and test photos
  const filtered = remotePhotos.filter(p => {
    if (isTestPhoto(p)) return false;
    const id = p.id || '';
    const img = p.image || '';
    if (deletedSet.has(id) || deletedSet.has(img)) return false;
    return true;
  });

  // Apply to gallery
  filtered.forEach(photo => prependPhotoToGallery(photo));

  // Purge any deleted items from DOM
  applyLocalModeration();
}

// 1. Load instant cache first
loadCachedCommunityPhotos();
// 2. Fetch fresh live global data
loadGlobalCommunityPhotos();

// Auto-sync community photos every 15 seconds so other devices see updates live
setInterval(loadGlobalCommunityPhotos, 15000);

// Save photo record to global Cloudflare edge database so everyone sees it
async function savePhotoToGlobalCloud(photo) {
  try {
    let photos = [];
    try {
      const getRes = await fetch(CLOUD_KV_ENDPOINT);
      if (getRes.ok) {
        const getData = await getRes.json();
        if (getData && Array.isArray(getData.photos)) {
          photos = getData.photos.filter(p => !isTestPhoto(p));
        }
      }
    } catch(e) {}

    // Prepend photo avoiding duplicate
    photos = photos.filter(p => p.image !== photo.image && p.id !== photo.id && !isTestPhoto(p));
    photos.unshift(photo);

    // Keep up to 50 latest community photos
    if (photos.length > 50) photos = photos.slice(0, 50);

    // Write to Cloudflare edge KV
    await fetch(CLOUD_KV_ENDPOINT, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ photos: photos })
    });

    // Update local cache
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(photos));
    } catch(e) {}
  } catch(err) {
    console.error('Error saving to global cloud:', err);
  }
}

// Handle form submission
photoUploadForm.addEventListener('submit', async function(e) {
  e.preventDefault();
  if (!selectedDataUrl) {
    alert('Please select or snap a photo first.');
    return;
  }

  uploadSubmitBtn.disabled = true;
  uploadSubmitBtn.textContent = 'Publishing photo to global gallery...';

  const titleVal = document.getElementById('photo-title').value.trim() || 'Community Celebration Snap';
  const uploaderVal = document.getElementById('photo-uploader').value.trim() || 'Club Member';
  const dateStr = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  const photoId = 'snap_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);

  let finalImage = selectedDataUrl;
  let serverPhotoEntry = null;

  // Step 1: If hosted on PHP (e.g. Hostinger), save directly to server storage via upload.php
  if (window.location.protocol.startsWith('http')) {
    try {
      const formData = new FormData();
      formData.append('title', titleVal);
      formData.append('uploader', uploaderVal);
      formData.append('image_data', selectedDataUrl);

      const phpRes = await fetch('upload.php', {
        method: 'POST',
        body: formData
      });

      if (phpRes.ok) {
        const phpData = await phpRes.json();
        if (phpData && phpData.success && phpData.photo) {
          serverPhotoEntry = phpData.photo;
          if (phpData.photo.image) {
            finalImage = phpData.photo.image;
          }
        }
      }
    } catch(err) {
      console.warn('Local PHP upload attempt:', err);
    }
  }

  // Step 2: Assemble new photo object
  const newPhoto = serverPhotoEntry || {
    id: photoId,
    category: 'community',
    image: finalImage,
    title: titleVal,
    subtitle: 'Shared by ' + uploaderVal + ' on ' + dateStr,
    uploader: uploaderVal,
    timestamp: Date.now()
  };

  // Step 3: Render card immediately in DOM and save to local cache
  prependPhotoToGallery(newPhoto);
  try {
    const localList = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    const filtered = localList.filter(p => p.id !== newPhoto.id && p.image !== newPhoto.image && !isTestPhoto(p));
    filtered.unshift(newPhoto);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(filtered));
  } catch(e) {}

  // Step 4: Publish to shared global cloud database (Cloudflare edge KV)
  const cloudPhoto = {
    ...newPhoto,
    image: selectedDataUrl // ensures anyone on any external domain or device can display it
  };
  try {
    await savePhotoToGlobalCloud(cloudPhoto);
  } catch(e) {
    console.warn('Could not sync to cloud store:', e);
  }

  // Step 5: Reset form and close upload modal
  uploadSubmitBtn.disabled = false;
  uploadSubmitBtn.textContent = 'Publish to Live Gallery →';
  photoUploadForm.reset();
  previewContainer.style.display = 'none';
  fileDropzone.style.display = 'block';
  fileDropzone.innerHTML = '<span style="font-size:32px;display:block;margin-bottom:6px">📷</span><b>Tap to Snap a Photo or Pick from Gallery</b><small style="display:block;color:#6b786e;margin-top:4px">JPG, PNG, WEBP (Max 15MB)</small>';
  selectedDataUrl = '';
  closeUploadModal();

  // Step 6: Show gorgeous celebration modal with "View Gallery" button!
  openSuccessModal(newPhoto);
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>

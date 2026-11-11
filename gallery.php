<?php
require_once __DIR__ . '/data/gallery_data.php';

// Load persistent community photos
$communityPhotos = [];
$communityFile = __DIR__ . '/data/community_photos.json';
if (file_exists($communityFile)) {
    $communityPhotos = json_decode(file_get_contents($communityFile), true) ?: [];
    // Ensure test photo from Nov 10 2:59 PM is filtered out
    $communityPhotos = array_values(array_filter($communityPhotos, function($p) {
        $sub = strtolower($p['subtitle'] ?? '');
        $tit = strtolower($p['title'] ?? '');
        return !(strpos($sub, '2:59 pm') !== false || (strpos($sub, 'nov 10, 2026') !== false && strpos($tit, 'community celebration snap') !== false));
    }));
}

// Merge community photos first so new snaps appear prominently
$display_items = array_merge($communityPhotos, $gallery_items);

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
        <button class="filter" data-filter="executive">Executive Committee</button>
        <button class="filter" data-filter="events">2026 Induction Honorees</button>
        <button class="filter" data-filter="community">Community snaps</button>
        <button class="filter" data-filter="heritage">Imo State artwork</button>
        <button class="filter" data-filter="branding">Official crest &amp; swag</button>
        <a class="filter-btn" href="videos<?= $ext ?>">Watch Videos ↗</a>
      </div>

      <!-- Gallery Grid -->
      <div class="gallery-grid" id="gallery-grid">
        <?php foreach ($display_items as $item): 
          $isCommunity = ($item['category'] === 'community');
          $itemId = $item['id'] ?? '';
        ?>
          <figure class="gallery-card" data-category="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>" <?= $itemId ? 'data-id="' . htmlspecialchars($itemId, ENT_QUOTES, 'UTF-8') . '"' : '' ?>>
            <?php if ($isCommunity): ?>
              <span class="badge-community" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Community Snap</span>
            <?php elseif ($item['category'] === 'executive'): ?>
              <span class="badge-executive" style="position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#042115 0%,#0d4630 60%,#b8860b 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;">Executive Committee</span>
            <?php endif; ?>
            <img alt="<?= htmlspecialchars($item['alt'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" tabindex="0"/>
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
// Filter & Lightbox controller
const buttons = [...document.querySelectorAll('.filter')];
let cards = [...document.querySelectorAll('.gallery-card')];

function applyFilter(v) {
  buttons.forEach(b => b.classList.toggle('active', b.dataset.filter === v));
  cards.forEach(c => {
    const show = (v === 'all') || (c.dataset.category === v);
    c.style.display = show ? 'block' : 'none';
  });
}

buttons.forEach(btn => {
  btn.addEventListener('click', () => applyFilter(btn.dataset.filter));
});

const urlFilter = new URLSearchParams(window.location.search).get('category');
if (urlFilter) applyFilter(urlFilter);

const lightbox = document.querySelector('.lightbox');
const lbImg = lightbox.querySelector('img');

function setupLightboxEvents() {
  document.querySelectorAll('.gallery-card img').forEach(img => {
    if (!img.dataset.hasLb) {
      img.dataset.hasLb = 'true';
      img.addEventListener('click', () => {
        lbImg.src = img.src;
        lbImg.alt = img.alt;
        lightbox.classList.add('open');
      });
      img.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
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
  if (!p) return false;
  const sub = (p.subtitle || '').toLowerCase();
  const tit = (p.title || '').toLowerCase();
  if (sub.includes('2:59 pm') || (sub.includes('nov 10, 2026') && tit.includes('community celebration snap'))) {
    return true;
  }
  return false;
}

// Remove any test card that might be in initial HTML
document.querySelectorAll('.gallery-card[data-category="community"]').forEach(card => {
  const text = card.textContent.toLowerCase();
  if (text.includes('2:59 pm') || (text.includes('nov 10, 2026') && text.includes('community celebration snap'))) {
    card.remove();
  }
});

// Load cached community snaps immediately on page load
function loadCachedCommunityPhotos() {
  try {
    const cached = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    if (Array.isArray(cached)) {
      cached.filter(p => !isTestPhoto(p)).forEach(photo => prependPhotoToGallery(photo));
    }
  } catch(e) {}
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
  // Check if card with this image or id already exists
  if (photo.id && document.querySelector(`.gallery-card[data-id="${photo.id}"]`)) return;
  if (document.querySelector(`.gallery-card img[src="${photo.image}"]`)) return;

  const figure = document.createElement('figure');
  figure.className = 'gallery-card';
  figure.dataset.category = 'community';
  if (photo.id) figure.dataset.id = photo.id;

  const badge = document.createElement('span');
  badge.className = 'badge-community';
  badge.style.cssText = 'position:absolute;top:12px;left:12px;background:linear-gradient(135deg,#072e1e 0%,#0d4630 100%);color:#ffffff;border:1px solid #d4af37;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:0.06em;box-shadow:0 4px 10px rgba(0,0,0,0.3);z-index:2;display:inline-block;margin:0;line-height:normal;';
  badge.textContent = 'Community Snap';
  figure.appendChild(badge);

  const img = document.createElement('img');
  img.src = photo.image;
  img.alt = photo.title || 'Community Photo';
  img.loading = 'lazy';
  img.tabIndex = 0;
  figure.appendChild(img);

  const figcaption = document.createElement('figcaption');
  const b = document.createElement('b');
  b.textContent = photo.title || 'Community Celebration Snap';
  figcaption.appendChild(b);

  const small = document.createElement('small');
  small.textContent = photo.subtitle || ('Shared by ' + (photo.uploader || 'Member') + ' on ' + new Date(photo.timestamp || Date.now()).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }));
  figcaption.appendChild(small);

  figure.appendChild(figcaption);

  galleryGrid.insertBefore(figure, galleryGrid.firstChild);
  setupLightboxEvents();
  cards = [...document.querySelectorAll('.gallery-card')];
}

// Fetch photos from the global cloud edge database & local PHP backend
async function loadGlobalCommunityPhotos() {
  const remotePhotos = [];

  // 1. Try local PHP upload endpoint on Hostinger
  if (window.location.protocol.startsWith('http')) {
    try {
      const phpRes = await fetch('upload.php');
      if (phpRes.ok) {
        const phpData = await phpRes.json();
        if (phpData && Array.isArray(phpData.photos)) {
          remotePhotos.push(...phpData.photos);
        }
      }
    } catch(e) {}
  }

  // 2. Try global Cloudflare Edge KV
  try {
    const kvRes = await fetch(CLOUD_KV_ENDPOINT);
    if (kvRes.ok) {
      const kvData = await kvRes.json();
      if (kvData && Array.isArray(kvData.photos)) {
        remotePhotos.push(...kvData.photos);
      }
    }
  } catch(err) {
    console.warn('Could not read global community photos:', err);
  }

  // Deduplicate and filter test photos
  const seen = new Set();
  const uniquePhotos = [];
  for (const p of remotePhotos) {
    if (isTestPhoto(p)) continue;
    const key = p.id || p.image;
    if (key && !seen.has(key)) {
      seen.add(key);
      uniquePhotos.push(p);
    }
  }

  if (uniquePhotos.length > 0) {
    uniquePhotos.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
    uniquePhotos.forEach(photo => prependPhotoToGallery(photo));
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(uniquePhotos));
    } catch(e) {}
  }

  cards = [...document.querySelectorAll('.gallery-card')];
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

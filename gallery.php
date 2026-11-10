<?php
require_once __DIR__ . '/data/gallery_data.php';

// Load persistent community photos
$communityPhotos = [];
$communityFile = __DIR__ . '/data/community_photos.json';
if (file_exists($communityFile)) {
    $communityPhotos = json_decode(file_get_contents($communityFile), true) ?: [];
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
        <button class="filter" data-filter="community">Community snaps</button>
        <button class="filter" data-filter="events">Events &amp; invitations</button>
        <button class="filter" data-filter="heritage">Imo State artwork</button>
        <button class="filter" data-filter="branding">Official crest &amp; swag</button>
        <a class="filter-btn" href="videos<?= $ext ?>">Watch Videos ↗</a>
      </div>

      <!-- Gallery Grid -->
      <div class="gallery-grid" id="gallery-grid">
        <?php foreach ($display_items as $item): 
          $isCommunity = ($item['category'] === 'community');
        ?>
          <figure class="gallery-card" data-category="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>">
            <?php if ($isCommunity): ?>
              <span class="badge-community">Community Snap</span>
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
        </div>
      </div>

      <div class="form-group">
        <label for="photo-title">Photo Title / Caption</label>
        <input type="text" id="photo-title" name="title" placeholder="e.g. Induction Celebration, Imo Heritage, Family..."/>
      </div>

      <div class="form-group">
        <label for="photo-uploader">Your Name (Optional)</label>
        <input type="text" id="photo-uploader" name="uploader" placeholder="e.g. Club Member, Amadi..."/>
      </div>

      <button type="submit" class="btn" id="upload-submit-btn" style="width:100%">Publish to Live Gallery →</button>
    </form>
  </div>
</div>

<script>
// Filter & Lightbox controller
const buttons = [...document.querySelectorAll('.filter')];
let cards = [...document.querySelectorAll('.gallery-card')];

function applyFilter(v) {
  buttons.forEach(b => b.classList.toggle('active', b.dataset.filter === v));
  cards = [...document.querySelectorAll('.gallery-card')];
  cards.forEach(c => c.classList.toggle('hidden', v !== 'all' && c.dataset.category !== v));
}

buttons.forEach(b => b.addEventListener('click', () => {
  applyFilter(b.dataset.filter);
  const targetPage = window.location.pathname.endsWith('.html') ? 'gallery.html' : 'gallery.php';
  history.replaceState(null, '', b.dataset.filter === 'all' ? targetPage : '?category=' + b.dataset.filter);
}));

let v = new URLSearchParams(location.search).get('category');
if (['community', 'events', 'heritage', 'branding'].includes(v)) applyFilter(v);

// Lightbox
const lightbox = document.querySelector('.lightbox');
function setupLightboxEvents() {
  document.querySelectorAll('.gallery-card img').forEach(im => {
    im.onclick = function() {
      lightbox.querySelector('img').src = im.src;
      lightbox.querySelector('img').alt = im.alt;
      lightbox.classList.add('open');
    };
    im.onkeydown = function(e) {
      if (e.key === 'Enter') im.click();
    };
  });
}
setupLightboxEvents();

document.querySelector('.close').addEventListener('click', () => lightbox.classList.remove('open'));
lightbox.addEventListener('click', e => {
  if (e.target === lightbox) lightbox.classList.remove('open');
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    lightbox.classList.remove('open');
    closeUploadModal();
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
const photoUploadForm = document.getElementById('photo-upload-form');
const uploadSubmitBtn = document.getElementById('upload-submit-btn');
const galleryGrid = document.getElementById('gallery-grid');

function openUploadModal() {
  uploadModal.classList.add('open');
  uploadModal.setAttribute('aria-hidden', 'false');
}

function closeUploadModal() {
  uploadModal.classList.remove('open');
  uploadModal.setAttribute('aria-hidden', 'true');
}

if (openUploadBtn) openUploadBtn.addEventListener('click', openUploadModal);
if (closeUploadBtn) closeUploadBtn.addEventListener('click', closeUploadModal);
if (fileDropzone) {
  fileDropzone.addEventListener('click', () => photoFileInput.click());
  fileDropzone.addEventListener('keydown', (e) => { if (e.key === 'Enter') photoFileInput.click(); });
}

// Handle file selection and preview
let selectedDataUrl = '';
photoFileInput.addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = function(e) {
    selectedDataUrl = e.target.result;
    imagePreview.src = selectedDataUrl;
    previewContainer.style.display = 'block';
    fileDropzone.style.display = 'none';
  };
  reader.readAsDataURL(file);
});

// Function to prepend newly added photo card to DOM
function prependPhotoToGallery(photo) {
  const figure = document.createElement('figure');
  figure.className = 'gallery-card';
  figure.dataset.category = 'community';

  const badge = document.createElement('span');
  badge.className = 'badge-community';
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
  small.textContent = photo.subtitle || ('Shared by ' + (photo.uploader || 'Member') + ' on ' + new Date().toLocaleDateString());
  figcaption.appendChild(small);

  figure.appendChild(figcaption);

  galleryGrid.insertBefore(figure, galleryGrid.firstChild);
  setupLightboxEvents();
  cards = [...document.querySelectorAll('.gallery-card')];
}

// Load any local-storage community photos (for GitHub Pages static demo)
function loadStoredCommunityPhotos() {
  try {
    const stored = JSON.parse(localStorage.getItem('imolites_community_snaps') || '[]');
    stored.forEach(photo => {
      // Avoid duplicate if already present in server items
      if (!document.querySelector(`img[src="${photo.image}"]`)) {
        prependPhotoToGallery(photo);
      }
    });
  } catch(e) {}
}
loadStoredCommunityPhotos();

// Handle form submission
photoUploadForm.addEventListener('submit', function(e) {
  e.preventDefault();
  const file = photoFileInput.files[0];
  if (!file && !selectedDataUrl) {
    alert('Please select or snap a photo first.');
    return;
  }

  uploadSubmitBtn.disabled = true;
  uploadSubmitBtn.textContent = 'Uploading...';

  const titleVal = document.getElementById('photo-title').value.trim() || 'Community Celebration Snap';
  const uploaderVal = document.getElementById('photo-uploader').value.trim() || 'Club Member';
  const dateStr = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

  // First try server upload via upload.php (for PHP backend)
  const formData = new FormData(photoUploadForm);

  fetch('upload.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && data.photo) {
      prependPhotoToGallery(data.photo);
      finishUploadSuccess();
    } else {
      // Fallback for static hosting (GitHub Pages)
      saveToClientStorage();
    }
  })
  .catch(() => {
    // If upload.php is not accessible (e.g. on GitHub Pages static host), save to client storage
    saveToClientStorage();
  });

  function saveToClientStorage() {
    const localPhoto = {
      id: 'snap_' + Date.now(),
      category: 'community',
      image: selectedDataUrl,
      title: titleVal,
      subtitle: 'Shared by ' + uploaderVal + ' on ' + dateStr,
      uploader: uploaderVal,
      timestamp: Date.now()
    };

    try {
      const stored = JSON.parse(localStorage.getItem('imolites_community_snaps') || '[]');
      stored.unshift(localPhoto);
      // Keep up to 20 local snaps
      if (stored.length > 20) stored.pop();
      localStorage.setItem('imolites_community_snaps', JSON.stringify(stored));
    } catch(err) {
      console.warn('Local storage error:', err);
    }

    prependPhotoToGallery(localPhoto);
    finishUploadSuccess();
  }

  function finishUploadSuccess() {
    uploadSubmitBtn.disabled = false;
    uploadSubmitBtn.textContent = 'Publish to Live Gallery →';
    photoUploadForm.reset();
    previewContainer.style.display = 'none';
    fileDropzone.style.display = 'block';
    selectedDataUrl = '';
    closeUploadModal();
    applyFilter('community');
    window.scrollTo({ top: galleryGrid.offsetTop - 100, behavior: 'smooth' });
    alert('🎉 Success! Your photo has been uploaded directly to the gallery for everyone to see.');
  }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>

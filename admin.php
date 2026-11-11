<?php
/**
 * Imolites in Diaspora Social Club USA
 * Admin Control Portal - Gallery & Community Management
 */
session_start();

$page_title = "Admin Portal | Imolites in Diaspora Social Club USA";
$meta_description = "Administrative portal to manage and moderate Imolites gallery photos, sections, and community uploads.";
$active_page = 'admin';
$is_logged_in = !empty($_SESSION['imolites_admin_logged_in']);
$admin_email = $_SESSION['imolites_admin_email'] ?? 'imolitesusa2@gmail';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width,initial-scale=1" name="viewport"/>
  <meta content="#032417" name="theme-color"/>
  <title><?= $page_title ?></title>
  <link rel="icon" href="assets/crest-logo.png?v=2" type="image/png"/>
  <link href="style.css?v=20261111c" rel="stylesheet"/>
  <style>
    /* Admin Dashboard Specific Styles */
    .admin-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 30px 20px 80px;
    }
    .admin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 16px;
      padding: 24px;
      background: linear-gradient(135deg, #072e1e 0%, #0d4630 100%);
      border: 1.5px solid var(--gold);
      border-radius: 14px;
      color: #ffffff;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .admin-title-wrap h1 {
      font-family: 'Playfair Display', serif;
      font-size: 26px;
      color: #f3e0aa;
      margin: 0 0 6px;
    }
    .admin-title-wrap p {
      margin: 0;
      font-size: 13.5px;
      color: #c9dbd1;
    }
    .admin-nav-actions {
      display: flex;
      gap: 12px;
      align-items: center;
      flex-wrap: wrap;
    }
    .btn-admin-logout {
      background: #dc3545;
      color: #fff;
      border: none;
      padding: 9px 18px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-admin-logout:hover { background: #b02a37; }
    
    /* Login Screen */
    .login-card {
      max-width: 440px;
      margin: 60px auto;
      background: #ffffff;
      border-radius: 14px;
      border: 1px solid #d4dfd7;
      box-shadow: 0 16px 40px rgba(7, 46, 30, 0.12);
      padding: 36px 30px;
      text-align: center;
    }
    .login-card img {
      width: 80px;
      height: 80px;
      object-fit: contain;
      margin-bottom: 16px;
    }
    .login-card h2 {
      font-family: 'Playfair Display', serif;
      color: #072e1e;
      font-size: 24px;
      margin: 0 0 8px;
    }
    .login-card p {
      color: #55695e;
      font-size: 14px;
      margin-bottom: 24px;
    }
    .form-group {
      text-align: left;
      margin-bottom: 18px;
    }
    .form-group label {
      display: block;
      font-weight: 700;
      font-size: 13px;
      color: #072e1e;
      margin-bottom: 6px;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #c9d8ce;
      border-radius: 8px;
      font-size: 14px;
      background: #fdfdfd;
      box-sizing: border-box;
      transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus {
      outline: none;
      border-color: #072e1e;
      box-shadow: 0 0 0 3px rgba(7, 46, 30, 0.1);
    }
    .login-btn {
      width: 100%;
      padding: 13px;
      background: #072e1e;
      color: #f3e0aa;
      border: 1px solid var(--gold);
      border-radius: 8px;
      font-weight: 800;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .login-btn:hover {
      background: #0d4630;
      transform: translateY(-2px);
    }

    /* Admin Dashboard Panels */
    .admin-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 30px;
    }
    .stats-bar {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }
    .stat-card {
      background: #ffffff;
      border: 1px solid #d5e0d8;
      border-radius: 10px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.04);
    }
    .stat-icon {
      font-size: 32px;
      line-height: 1;
    }
    .stat-info b {
      display: block;
      font-size: 24px;
      color: #072e1e;
      font-family: 'Playfair Display', serif;
    }
    .stat-info span {
      font-size: 13px;
      color: #63776b;
      font-weight: 600;
    }

    /* Upload Box */
    .admin-upload-box {
      background: #ffffff;
      border: 1.5px solid #d3ded6;
      border-radius: 12px;
      padding: 28px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
      margin-bottom: 36px;
    }
    .admin-upload-box h3 {
      font-family: 'Playfair Display', serif;
      font-size: 21px;
      color: #072e1e;
      margin: 0 0 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .admin-upload-box p {
      color: #556a5e;
      font-size: 14px;
      margin: 0 0 20px;
    }
    .upload-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    @media (max-width: 768px) {
      .upload-row { grid-template-columns: 1fr; }
    }
    .security-notice {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: #eef7f2;
      border: 1px solid #94cbb0;
      color: #0d4630;
      padding: 8px 14px;
      border-radius: 6px;
      font-size: 12.5px;
      font-weight: 600;
      margin-bottom: 18px;
    }

    /* Photos Management Grid */
    .admin-filter-bar {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 20px;
      align-items: center;
    }
    .admin-filter {
      padding: 8px 16px;
      border-radius: 30px;
      border: 1.5px solid #c7d7c9;
      background: #ffffff;
      color: #1a4230;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .admin-filter.active, .admin-filter:hover {
      background: #072e1e;
      color: #ffffff;
      border-color: #072e1e;
    }
    .admin-photos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
    }
    .admin-photo-card {
      background: #ffffff;
      border: 1px solid #d5e0d8;
      border-radius: 10px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      position: relative;
    }
    .admin-photo-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      background: #f1f5f2;
    }
    .admin-card-body {
      padding: 14px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    .admin-card-badge {
      display: inline-block;
      font-size: 10.5px;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 12px;
      text-transform: uppercase;
      margin-bottom: 8px;
      align-self: flex-start;
    }
    .badge-exec-admin {
      background: #042115;
      color: #ffffff;
      border: 1px solid var(--gold);
    }
    .badge-comm-admin {
      background: #0d4630;
      color: #ffffff;
      border: 1px solid #94cbb0;
    }
    .badge-event-admin {
      background: #8b0000;
      color: #ffffff;
      border: 1px solid #ffd700;
    }
    .badge-other-admin {
      background: #4a5c52;
      color: #ffffff;
    }
    .admin-card-body h4 {
      margin: 0 0 4px;
      font-size: 16px;
      color: #072e1e;
      font-family: 'Playfair Display', serif;
    }
    .admin-card-body small {
      color: #63776b;
      font-size: 12px;
      margin-bottom: 12px;
      display: block;
    }
    .admin-card-actions {
      margin-top: auto;
      display: flex;
      gap: 8px;
    }
    .btn-delete-photo {
      background: #fee2e2;
      color: #b91c1c;
      border: 1px solid #f87171;
      border-radius: 6px;
      padding: 7px 12px;
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      width: 100%;
      transition: all 0.2s;
    }
    .btn-delete-photo:hover {
      background: #dc2626;
      color: #ffffff;
    }
  </style>
</head>
<body>
<div class="topline">Imolites in Diaspora Social Club USA · Official Admin Portal</div>

<div class="admin-container">
  <?php if (!$is_logged_in): ?>
    <!-- LOGIN VIEW -->
    <div class="login-card">
      <img src="assets/crest-logo.png?v=2" alt="Imolites Club Crest"/>
      <h2>Admin Login</h2>
      <p>Sign in to manage gallery photos, sections, and community uploads.</p>
      
      <div id="login-alert" style="display:none; padding:10px 14px; margin-bottom:16px; border-radius:6px; font-size:13px; font-weight:600; background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;"></div>

      <form id="admin-login-form">
        <div class="form-group">
          <label for="admin-email">Admin Email</label>
          <input type="text" id="admin-email" name="email" placeholder="imolitesusa2@gmail" required/>
        </div>
        <div class="form-group">
          <label for="admin-password">Password</label>
          <input type="password" id="admin-password" name="password" placeholder="••••••••••••" required/>
        </div>
        <button type="submit" class="login-btn" id="login-btn">Log In to Admin Portal →</button>
      </form>
      <div style="margin-top:20px; font-size:13px;">
        <a href="gallery.php" style="color:#072e1e; font-weight:600;">← Back to Cultural Gallery</a>
      </div>
    </div>
  <?php else: ?>
    <!-- LOGGED IN DASHBOARD VIEW -->
    <div class="admin-header">
      <div class="admin-title-wrap">
        <h1>Gallery &amp; Sections Management</h1>
        <p>Logged in as <strong><?= htmlspecialchars($admin_email, ENT_QUOTES, 'UTF-8') ?></strong> · Full gallery control and photo moderation</p>
      </div>
      <div class="admin-nav-actions">
        <a class="btn btn-outline" href="gallery.php" style="border-color:var(--gold); color:#ffffff; font-size:13px; padding:9px 16px;">View Live Gallery ↗</a>
        <button type="button" class="btn-admin-logout" id="logout-btn">Log Out</button>
      </div>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stat-card">
        <div class="stat-icon">🖼️</div>
        <div class="stat-info">
          <b id="stat-total">--</b>
          <span>Total Gallery Photos</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👔</div>
        <div class="stat-info">
          <b id="stat-exec">--</b>
          <span>Executive Committee</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🎖️</div>
        <div class="stat-info">
          <b id="stat-induction">--</b>
          <span>2026 Induction Honorees</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📸</div>
        <div class="stat-info">
          <b id="stat-comm">--</b>
          <span>Community Snaps</span>
        </div>
      </div>
    </div>

    <!-- Antivirus Shield Banner -->
    <div class="security-notice">
      <span>🛡️</span>
      <span><strong>Antivirus &amp; Upload Sanitizer Active:</strong> All uploads undergo deep magic-byte inspection, executable code scanning, and GD pixel re-encoding to block viruses, web shells, and malicious payloads.</span>
    </div>

    <!-- Admin Photo Upload Form -->
    <section class="admin-upload-box">
      <h3><span>📤</span> Add Photo to Any Gallery Section</h3>
      <p>Upload a photo and assign it directly to a gallery section (Executive Committee, 2026 Induction Honorees, Community, etc.). It will appear live immediately with matching club design.</p>
      
      <div id="upload-status-alert" style="display:none; padding:12px 16px; margin-bottom:18px; border-radius:8px; font-size:14px; font-weight:600;"></div>

      <form id="admin-upload-form">
        <div class="upload-row">
          <div>
            <div class="form-group">
              <label for="photo-category">Gallery Section / Folder *</label>
              <select id="photo-category" name="category" required>
                <option value="executive">👔 Executive Committee (Executive Council Folder)</option>
                <option value="events">🎖️ 2026 Induction Honorees &amp; Committee</option>
                <option value="community">📸 Community Snaps &amp; Family</option>
                <option value="heritage">🏛️ Imo State Artwork &amp; Landmarks</option>
                <option value="branding">🦅 Official Crest &amp; Swag</option>
              </select>
            </div>

            <div class="form-group">
              <label for="photo-title">Photo Title / Person's Name *</label>
              <input type="text" id="photo-title" name="title" placeholder="e.g. Chief Emeka Okoro, Gala Celebration" required/>
            </div>

            <div class="form-group">
              <label for="photo-subtitle">Role / Subtitle (Optional)</label>
              <input type="text" id="photo-subtitle" name="subtitle" placeholder="e.g. Committee Member, Honoree, Dallas TX"/>
            </div>
          </div>

          <div>
            <div class="form-group">
              <label>Select Photo *</label>
              <input type="file" id="admin-file-input" accept="image/*" style="display:none;"/>
              <div class="file-dropzone" id="admin-dropzone" tabindex="0" role="button" style="padding:26px 16px; text-align:center; border:2px dashed #97b5a2; border-radius:10px; background:#f9fbf9; cursor:pointer;">
                <span style="font-size:36px; display:block; margin-bottom:6px;">📸</span>
                <b style="color:#072e1e;">Click to choose photo or drag &amp; drop</b>
                <small style="display:block; color:#6b786e; margin-top:4px;">JPG, PNG, WEBP (Max 15MB · Auto-scanned &amp; Sanitized)</small>
              </div>
              <div id="admin-preview-wrap" style="display:none; margin-top:12px; text-align:center;">
                <img id="admin-image-preview" src="" alt="Selected photo preview" style="max-height:160px; border-radius:8px; border:2px solid var(--gold);"/>
                <div style="margin-top:6px;">
                  <button type="button" id="admin-change-file-btn" style="background:#e2ebe5; border:none; padding:5px 12px; border-radius:4px; font-size:12px; cursor:pointer;">Change Photo</button>
                </div>
              </div>
            </div>

            <button type="submit" class="btn" id="admin-submit-btn" style="width:100%; padding:14px; font-size:15px; margin-top:10px;">
              🚀 Scan &amp; Publish Live to Gallery →
            </button>
          </div>
        </div>
      </form>
    </section>

    <!-- Manage Existing Photos Section -->
    <section>
      <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
        <h2 style="font-family:'Playfair Display',serif; color:#072e1e; font-size:24px; margin:0;">
          Manage Gallery Photos &amp; Moderation
        </h2>
        <small style="color:#63776b;">Click "Delete Photo" to immediately remove any unwanted or inappropriate photo.</small>
      </div>

      <!-- Filters -->
      <div class="admin-filter-bar">
        <button class="admin-filter active" data-filter="all">All Photos</button>
        <button class="admin-filter" data-filter="community">📸 Community Uploads</button>
        <button class="admin-filter" data-filter="executive">👔 Executive Committee</button>
        <button class="admin-filter" data-filter="events">🎖️ 2026 Induction</button>
        <button class="admin-filter" data-filter="heritage">🏛️ Heritage Artwork</button>
        <button class="admin-filter" data-filter="branding">🦅 Official Crest</button>
      </div>

      <!-- Grid -->
      <div class="admin-photos-grid" id="admin-photos-grid">
        <div style="padding:40px; text-align:center; color:#576d60; grid-column:1/-1;">
          Loading gallery photos...
        </div>
      </div>
    </section>
  <?php endif; ?>
</div>

<footer class="footer">
  <div class="wrap" style="text-align:center; padding:20px 0;">
    <div class="copyright">© <span id="yr">2026</span> Imolites in Diaspora Social Club USA · Powered by <strong>Digimadi media</strong></div>
  </div>
</footer>

<script>
// Cloud Edge KV Sync Endpoint
const CLOUD_KV_ENDPOINT = 'https://kvs.ix.workers.dev/imolites/community-photos';
const STORAGE_KEY = 'imolites_community_photos_v6';

<?php if (!$is_logged_in): ?>
// Login Form Controller
const loginForm = document.getElementById('admin-login-form');
const loginAlert = document.getElementById('login-alert');
const loginBtn = document.getElementById('login-btn');

loginForm?.addEventListener('submit', async function(e) {
  e.preventDefault();
  loginAlert.style.display = 'none';
  loginBtn.disabled = true;
  loginBtn.textContent = 'Verifying credentials...';

  const email = document.getElementById('admin-email').value.trim();
  const password = document.getElementById('admin-password').value.trim();

  try {
    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('email', email);
    formData.append('password', password);

    const res = await fetch('admin-api.php?action=login', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (data.success) {
      // Store local token for GitHub Pages fallback
      localStorage.setItem('imolites_admin_token', 'logged_in');
      localStorage.setItem('imolites_admin_email', email);
      window.location.reload();
    } else {
      loginAlert.textContent = data.error || 'Invalid credentials.';
      loginAlert.style.display = 'block';
      loginBtn.disabled = false;
      loginBtn.textContent = 'Log In to Admin Portal →';
    }
  } catch (err) {
    // Client-side fallback if server-side is running in static environment
    if ((email.toLowerCase() === 'imolitesusa2@gmail' || email.toLowerCase() === 'imolitesusa2@gmail.com') && password === 'imolitesusa450') {
      localStorage.setItem('imolites_admin_token', 'logged_in');
      localStorage.setItem('imolites_admin_email', email);
      window.location.href = 'admin.html';
    } else {
      loginAlert.textContent = 'Authentication failed. Please check your credentials.';
      loginAlert.style.display = 'block';
      loginBtn.disabled = false;
      loginBtn.textContent = 'Log In to Admin Portal →';
    }
  }
});
<?php else: ?>
// Logged in Dashboard Controller
let allPhotosList = [];
let activeAdminFilter = 'all';
let selectedAdminDataUrl = '';

const logoutBtn = document.getElementById('logout-btn');
const adminDropzone = document.getElementById('admin-dropzone');
const adminFileInput = document.getElementById('admin-file-input');
const adminPreviewWrap = document.getElementById('admin-preview-wrap');
const adminImagePreview = document.getElementById('admin-image-preview');
const adminChangeFileBtn = document.getElementById('admin-change-file-btn');
const adminUploadForm = document.getElementById('admin-upload-form');
const adminSubmitBtn = document.getElementById('admin-submit-btn');
const uploadStatusAlert = document.getElementById('upload-status-alert');
const adminPhotosGrid = document.getElementById('admin-photos-grid');

// Stat elements
const statTotal = document.getElementById('stat-total');
const statExec = document.getElementById('stat-exec');
const statInduction = document.getElementById('stat-induction');
const statComm = document.getElementById('stat-comm');

// Logout
logoutBtn?.addEventListener('click', async function() {
  localStorage.removeItem('imolites_admin_token');
  try {
    await fetch('admin-api.php?action=logout', { method: 'POST' });
  } catch(e) {}
  window.location.reload();
});

// File picker trigger
adminDropzone?.addEventListener('click', () => adminFileInput.click());
adminChangeFileBtn?.addEventListener('click', () => adminFileInput.click());

// Image compression and sanitization preview
adminFileInput?.addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;

  // Check file size (max 15MB)
  if (file.size > 15 * 1024 * 1024) {
    alert('File is too large. Please select a photo under 15MB.');
    return;
  }

  const reader = new FileReader();
  reader.onload = function(e) {
    const img = new Image();
    img.onload = function() {
      // Re-encode via canvas for client sanitization and fast transmission
      let w = img.width;
      let h = img.height;
      const maxDim = 1400;
      if (w > maxDim || h > maxDim) {
        if (w > h) { h = Math.round((h * maxDim) / w); w = maxDim; }
        else { w = Math.round((w * maxDim) / h); h = maxDim; }
      }
      const canvas = document.createElement('canvas');
      canvas.width = w;
      canvas.height = h;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, w, h);
      selectedAdminDataUrl = canvas.toDataURL('image/jpeg', 0.85);

      adminImagePreview.src = selectedAdminDataUrl;
      adminPreviewWrap.style.display = 'block';
      adminDropzone.style.display = 'none';
    };
    img.src = e.target.result;
  };
  reader.readAsDataURL(file);
});

// Fetch all photos
async function fetchAdminPhotos() {
  try {
    const res = await fetch('admin-api.php?action=list_all');
    if (!res.ok) throw new Error('API request failed');
    const data = await res.json();

    const comm = data.community_photos || [];
    const adm = data.admin_photos || [];
    const base = data.base_photos || [];

    allPhotosList = [...comm, ...adm, ...base];
    updateStats(allPhotosList);
    renderAdminGrid();
  } catch (err) {
    // Fallback: fetch from public endpoints
    console.warn('Falling back to direct photo read:', err);
    try {
      const pRes = await fetch('upload.php');
      const pData = await pRes.json();
      allPhotosList = pData.photos || [];
      updateStats(allPhotosList);
      renderAdminGrid();
    } catch(e) {
      adminPhotosGrid.innerHTML = '<div style="padding:30px; text-align:center; color:#c53030;">Could not load photos list.</div>';
    }
  }
}

function updateStats(photos) {
  if (statTotal) statTotal.textContent = photos.length;
  if (statExec) statExec.textContent = photos.filter(p => p.category === 'executive').length;
  if (statInduction) statInduction.textContent = photos.filter(p => p.category === 'events').length;
  if (statComm) statComm.textContent = photos.filter(p => p.category === 'community').length;
}

function renderAdminGrid() {
  if (!adminPhotosGrid) return;
  adminPhotosGrid.innerHTML = '';

  const filtered = activeAdminFilter === 'all' 
    ? allPhotosList 
    : allPhotosList.filter(p => p.category === activeAdminFilter);

  if (filtered.length === 0) {
    adminPhotosGrid.innerHTML = '<div style="padding:40px; text-align:center; color:#6b786e; grid-column:1/-1;">No photos found in this category.</div>';
    return;
  }

  filtered.forEach(photo => {
    const card = document.createElement('div');
    card.className = 'admin-photo-card';

    let badgeClass = 'badge-other-admin';
    let badgeLabel = photo.category || 'Photo';
    if (photo.category === 'executive') { badgeClass = 'badge-exec-admin'; badgeLabel = 'Executive'; }
    else if (photo.category === 'community') { badgeClass = 'badge-comm-admin'; badgeLabel = 'Community Snap'; }
    else if (photo.category === 'events') { badgeClass = 'badge-event-admin'; badgeLabel = '2026 Induction'; }

    const photoId = photo.id || photo.image;
    const photoTitle = photo.title || photo.alt || 'Photo';
    const photoSubtitle = photo.subtitle || (photo.uploader ? ('Shared by ' + photo.uploader) : '');

    card.innerHTML = `
      <img src="${photo.image}" alt="${photoTitle}" loading="lazy"/>
      <div class="admin-card-body">
        <span class="admin-card-badge ${badgeClass}">${badgeLabel}</span>
        <h4>${photoTitle}</h4>
        <small>${photoSubtitle}</small>
        <div class="admin-card-actions">
          <button type="button" class="btn-delete-photo" data-id="${photoId}" data-image="${photo.image}">
            🗑️ Delete Photo
          </button>
        </div>
      </div>
    `;

    adminPhotosGrid.appendChild(card);
  });

  // Attach delete handlers
  document.querySelectorAll('.btn-delete-photo').forEach(btn => {
    btn.addEventListener('click', async function() {
      const id = this.dataset.id;
      const img = this.dataset.image;
      if (!confirm('Are you sure you want to permanently delete this photo? It will be removed from the live website immediately.')) {
        return;
      }

      this.disabled = true;
      this.textContent = 'Deleting...';

      try {
        const formData = new FormData();
        formData.append('action', 'delete_photo');
        formData.append('id', id);
        formData.append('image', img);

        const res = await fetch('admin-api.php?action=delete_photo', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        // Also delete from Cloudflare edge KV
        try {
          const kvRes = await fetch(CLOUD_KV_ENDPOINT);
          if (kvRes.ok) {
            const kvData = await kvRes.json();
            if (kvData && Array.isArray(kvData.photos)) {
              const updated = kvData.photos.filter(p => (p.id !== id && p.image !== img));
              await fetch(CLOUD_KV_ENDPOINT, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ photos: updated })
              });
            }
          }
        } catch(e) {}

        // Remove from local array and re-render
        allPhotosList = allPhotosList.filter(p => p.id !== id && p.image !== img);
        updateStats(allPhotosList);
        renderAdminGrid();
        alert('Photo has been successfully deleted from the gallery!');
      } catch (err) {
        alert('Error deleting photo: ' + err.message);
      }
    });
  });
}

// Category filter tabs
document.querySelectorAll('.admin-filter').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.admin-filter').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    activeAdminFilter = this.dataset.filter;
    renderAdminGrid();
  });
});

// Upload form submission
adminUploadForm?.addEventListener('submit', async function(e) {
  e.preventDefault();

  if (!selectedAdminDataUrl) {
    alert('Please select a photo to upload.');
    return;
  }

  adminSubmitBtn.disabled = true;
  adminSubmitBtn.textContent = '🛡️ Scanning for Viruses & Publishing...';
  uploadStatusAlert.style.display = 'none';

  const category = document.getElementById('photo-category').value;
  const title = document.getElementById('photo-title').value.trim();
  const subtitle = document.getElementById('photo-subtitle').value.trim();

  try {
    const formData = new FormData();
    formData.append('action', 'upload_photo');
    formData.append('category', category);
    formData.append('title', title);
    formData.append('subtitle', subtitle);
    formData.append('image_data', selectedAdminDataUrl);

    const res = await fetch('admin-api.php?action=upload_photo', {
      method: 'POST',
      body: formData
    });
    const data = await res.json();

    if (!data.success) {
      throw new Error(data.error || 'Upload failed');
    }

    // Sync to Cloudflare Edge KV so everyone across the world sees it instantly
    const newPhoto = data.photo || {
      id: 'admin_' + Date.now(),
      category: category,
      title: title,
      subtitle: subtitle,
      image: selectedAdminDataUrl,
      timestamp: Date.now()
    };

    try {
      let cloudPhotos = [];
      const getRes = await fetch(CLOUD_KV_ENDPOINT);
      if (getRes.ok) {
        const getData = await getRes.json();
        if (getData && Array.isArray(getData.photos)) {
          cloudPhotos = getData.photos;
        }
      }
      cloudPhotos.unshift(newPhoto);
      await fetch(CLOUD_KV_ENDPOINT, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ photos: cloudPhotos.slice(0, 70) })
      });
    } catch(e) {}

    // Show success message
    uploadStatusAlert.style.background = '#e6fffa';
    uploadStatusAlert.style.color = '#047857';
    uploadStatusAlert.style.border = '1px solid #10b981';
    uploadStatusAlert.textContent = '🎉 ' + data.message;
    uploadStatusAlert.style.display = 'block';

    // Reset form
    adminUploadForm.reset();
    selectedAdminDataUrl = '';
    adminPreviewWrap.style.display = 'none';
    adminDropzone.style.display = 'block';

    // Refresh photos list
    allPhotosList.unshift(newPhoto);
    updateStats(allPhotosList);
    renderAdminGrid();

  } catch (err) {
    uploadStatusAlert.style.background = '#fee2e2';
    uploadStatusAlert.style.color = '#b91c1c';
    uploadStatusAlert.style.border = '1px solid #f87171';
    uploadStatusAlert.textContent = '❌ ' + err.message;
    uploadStatusAlert.style.display = 'block';
  } finally {
    adminSubmitBtn.disabled = false;
    adminSubmitBtn.textContent = '🚀 Scan & Publish Live to Gallery →';
  }
});

// Initial load
fetchAdminPhotos();
<?php endif; ?>
</script>
</body>
</html>

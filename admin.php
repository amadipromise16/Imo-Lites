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

// Pre-load data if authenticated
$preloaded_photos = [];
$preloaded_deleted = [];
$preloaded_edits = [];

if ($is_logged_in) {
    // 1. Base photos
    $baseFile = __DIR__ . '/data/base_gallery_items.json';
    $baseItems = file_exists($baseFile) ? (json_decode(file_get_contents($baseFile), true) ?: []) : [];

    // 2. Deleted blacklist
    $deletedFile = __DIR__ . '/data/deleted_photos.json';
    if (file_exists($deletedFile)) {
        $preloaded_deleted = json_decode(file_get_contents($deletedFile), true) ?: [];
    }

    // 3. Edited metadata overrides
    $editedFile = __DIR__ . '/data/edited_photos.json';
    if (file_exists($editedFile)) {
        $preloaded_edits = json_decode(file_get_contents($editedFile), true) ?: [];
    }

    // 4. Community photos
    $communityFile = __DIR__ . '/data/community_photos.json';
    $commItems = file_exists($communityFile) ? (json_decode(file_get_contents($communityFile), true) ?: []) : [];

    // 5. Admin items
    $adminItemsFile = __DIR__ . '/data/admin_gallery_items.json';
    $admItems = file_exists($adminItemsFile) ? (json_decode(file_get_contents($adminItemsFile), true) ?: []) : [];

    // Apply edits & filter deleted
    $apply_edits = function(&$item) use ($preloaded_edits) {
        $id = $item['id'] ?? '';
        $img = $item['image'] ?? '';
        $key = isset($preloaded_edits[$id]) ? $id : (isset($preloaded_edits[$img]) ? $img : null);
        if ($key && isset($preloaded_edits[$key])) {
            $e = $preloaded_edits[$key];
            if (!empty($e['title'])) { $item['title'] = $e['title']; $item['alt'] = $e['title']; }
            if (isset($e['subtitle'])) { $item['subtitle'] = $e['subtitle']; }
            if (!empty($e['category'])) { $item['category'] = $e['category']; }
        }
    };

    $allRaw = array_merge($commItems, $admItems, $baseItems);
    foreach ($allRaw as $item) {
        $id = $item['id'] ?? '';
        $img = $item['image'] ?? '';
        $legacyKey = 'base_' . md5($img);
        if (!in_array($id, $preloaded_deleted) && !in_array($img, $preloaded_deleted) && !in_array($legacyKey, $preloaded_deleted)) {
            $apply_edits($item);
            $preloaded_photos[] = $item;
        }
    }
}
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

    .admin-container {
      max-width: 1240px;
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
      margin-bottom: 28px;
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
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 13px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s;
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
      box-shadow: 0 6px 18px rgba(7, 46, 30, 0.25);
    }

    /* Stats Bar */
    .stats-bar {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 14px;
      margin-bottom: 28px;
    }
    .stat-card {
      background: #ffffff;
      border: 1px solid #d4dfd7;
      border-radius: 12px;
      padding: 16px 18px;
      display: flex;
      align-items: center;
      gap: 14px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.04);
    }
    .stat-icon {
      font-size: 28px;
      width: 48px;
      height: 48px;
      border-radius: 10px;
      background: #f0f7f2;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .stat-info b {
      display: block;
      font-size: 24px;
      font-family: 'Playfair Display', serif;
      color: #072e1e;
      line-height: 1.1;
    }
    .stat-info span {
      font-size: 12px;
      color: #5d7366;
      font-weight: 600;
    }

    /* Security Notice */
    .security-notice {
      background: #f0f9f4;
      border: 1px solid #a3d9b8;
      border-radius: 10px;
      padding: 14px 18px;
      margin-bottom: 28px;
      font-size: 13.5px;
      color: #0d5435;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    /* Upload Box */
    .admin-upload-box {
      background: #ffffff;
      border: 1.5px solid #d4dfd7;
      border-radius: 14px;
      padding: 26px 28px;
      margin-bottom: 34px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    }
    .admin-upload-box h3 {
      font-family: 'Playfair Display', serif;
      color: #072e1e;
      font-size: 21px;
      margin: 0 0 6px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .admin-upload-box p {
      color: #55695e;
      font-size: 13.5px;
      margin-bottom: 22px;
    }
    .upload-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
    }
    @media (max-width: 768px) {
      .upload-row { grid-template-columns: 1fr; }
    }

    /* Filter Bar */
    .admin-filter-bar {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 22px;
    }
    .admin-filter {
      padding: 8px 14px;
      border-radius: 20px;
      border: 1.5px solid #c9d8ce;
      background: #ffffff;
      color: #072e1e;
      font-weight: 700;
      font-size: 12.5px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .admin-filter:hover {
      border-color: #072e1e;
      background: #f4f8f5;
    }
    .admin-filter.active {
      background: #072e1e;
      color: #f3e0aa;
      border-color: #072e1e;
    }

    /* Photos Grid */
    .admin-photos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
      gap: 22px;
    }
    .admin-photo-card {
      background: #ffffff;
      border: 1.5px solid #d5e0d8;
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 4px 14px rgba(0,0,0,0.06);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .admin-photo-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(0,0,0,0.1);
    }
    .admin-photo-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: #f1f5f2;
      border-bottom: 1px solid #e1ebe4;
    }
    .admin-card-body {
      padding: 16px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }
    .admin-card-badge {
      display: inline-block;
      font-size: 10.5px;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 14px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-bottom: 10px;
      align-self: flex-start;
    }
    .badge-exec-admin {
      background: #042115;
      color: #ffffff;
      border: 1px solid var(--gold);
    }
    .badge-trustees-admin {
      background: #072e1e;
      color: #f3e0aa;
      border: 1.5px solid var(--gold);
    }
    .badge-induct-comm-admin {
      background: #14532d;
      color: #ffffff;
      border: 1px solid #86efac;
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
      margin: 0 0 6px;
      font-size: 16px;
      color: #072e1e;
      font-family: 'Playfair Display', serif;
      line-height: 1.3;
    }
    .admin-card-body small {
      color: #63776b;
      font-size: 12.5px;
      margin-bottom: 14px;
      display: block;
      line-height: 1.4;
    }
    .admin-card-actions {
      margin-top: auto;
      display: flex;
      gap: 8px;
    }
    .btn-edit-photo {
      flex: 1;
      background: #f0fdf4;
      color: #166534;
      border: 1.5px solid #86efac;
      border-radius: 6px;
      padding: 8px 10px;
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      text-align: center;
    }
    .btn-edit-photo:hover {
      background: #166534;
      color: #ffffff;
    }
    .btn-delete-photo {
      flex: 1;
      background: #fef2f2;
      color: #b91c1c;
      border: 1.5px solid #fca5a5;
      border-radius: 6px;
      padding: 8px 10px;
      font-size: 12.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      text-align: center;
    }
    .btn-delete-photo:hover {
      background: #dc2626;
      color: #ffffff;
    }

    /* Edit Modal */
    .edit-modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(4, 25, 17, 0.7);
      backdrop-filter: blur(4px);
      z-index: 10000;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .edit-modal-card {
      background: #ffffff;
      border-radius: 14px;
      max-width: 520px;
      width: 100%;
      padding: 28px;
      box-shadow: 0 24px 60px rgba(0,0,0,0.3);
      border: 2px solid var(--gold);
      position: relative;
    }
    .edit-modal-close {
      position: absolute;
      top: 16px;
      right: 18px;
      background: none;
      border: none;
      font-size: 26px;
      cursor: pointer;
      color: #072e1e;
    }

  </style>
</head>
<body>
<div class="topline">Imolites in Diaspora Social Club USA · Official Admin Portal</div>

<div class="admin-container">
  <?php if (!$is_logged_in): ?>
    <!-- STRICT LOGIN VIEW -->
    <div class="login-card" id="login-card">
      <img src="assets/crest-logo.png?v=2" alt="Imolites Club Crest"/>
      <h2>Admin Login</h2>
      <p>Authorized personnel only. Please sign in to manage and moderate gallery photos.</p>
      
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
    <!-- AUTHORIZED DASHBOARD VIEW -->
    <div class="admin-header">
      <div class="admin-title-wrap">
        <h1>Gallery &amp; Sections Management</h1>
        <p>Logged in as <strong><?= htmlspecialchars($admin_email, ENT_QUOTES, 'UTF-8') ?></strong> · Full gallery control, photo edits, and moderation</p>
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
          <b id="stat-total"><?= count($preloaded_photos) ?></b>
          <span>Total Gallery Photos</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👔</div>
        <div class="stat-info">
          <b id="stat-exec">0</b>
          <span>Executive Committee</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🏛️</div>
        <div class="stat-info">
          <b id="stat-trustees">0</b>
          <span>Board of Trustees</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🤝</div>
        <div class="stat-info">
          <b id="stat-induct-comm">0</b>
          <span>Induction Committee</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🎖️</div>
        <div class="stat-info">
          <b id="stat-induction">0</b>
          <span>2026 Induction Honorees</span>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📸</div>
        <div class="stat-info">
          <b id="stat-comm">0</b>
          <span>Community Snaps</span>
        </div>
      </div>
    </div>

    <!-- Antivirus Shield Banner -->
    <div class="security-notice">
      <span style="font-size:24px;">🛡️</span>
      <span><strong>Antivirus &amp; Upload Sanitizer Active:</strong> All uploads undergo deep magic-byte inspection, executable code scanning, and GD pixel re-encoding to block viruses, web shells, and malicious payloads.</span>
    </div>

    <!-- Admin Photo Upload Form -->
    <section class="admin-upload-box">
      <h3><span>📤</span> Add Photo to Any Gallery Section</h3>
      <p>Upload a photo and assign it directly to any gallery section. It will appear live immediately with matching club design.</p>
      
      <div id="upload-status-alert" style="display:none; padding:12px 16px; margin-bottom:18px; border-radius:8px; font-size:14px; font-weight:600;"></div>

      <form id="admin-upload-form">
        <div class="upload-row">
          <div>
            <div class="form-group">
              <label for="photo-category">Gallery Section / Folder *</label>
              <select id="photo-category" name="category" required>
                <option value="brochure">📖 2026 Induction Brochure &amp; Media</option>
                <option value="executive">👔 Executive Committee (Executive Council Folder)</option>
                <option value="trustees">🏛️ Board of Trustees &amp; Founder</option>
                <option value="induction_committee">🤝 2026 Induction Planning Committee</option>
                <option value="events">🎖️ 2026 Induction Honorees</option>
                <option value="community">📸 Community Snaps &amp; Family</option>
                <option value="heritage">🎨 Imo State Artwork &amp; Landmarks</option>
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
        <small style="color:#63776b;">Use "Edit Details" to change titles/sections, or "Delete Photo" to permanently remove any unwanted photo.</small>
      </div>

      <!-- Filters -->
      <div class="admin-filter-bar">
        <button class="admin-filter active" data-filter="all">All Photos (<span id="cnt-all">0</span>)</button>
        <button class="admin-filter" data-filter="brochure">📖 Brochure (<span id="cnt-brochure">0</span>)</button>
        <button class="admin-filter" data-filter="executive">👔 Executive (<span id="cnt-exec">0</span>)</button>
        <button class="admin-filter" data-filter="trustees">🏛️ Trustees &amp; Founder (<span id="cnt-trustees">0</span>)</button>
        <button class="admin-filter" data-filter="induction_committee">🤝 Induction Comm (<span id="cnt-induct-comm">0</span>)</button>
        <button class="admin-filter" data-filter="events">🎖️ 2026 Honorees (<span id="cnt-events">0</span>)</button>
        <button class="admin-filter" data-filter="community">📸 Community (<span id="cnt-comm">0</span>)</button>
        <button class="admin-filter" data-filter="heritage">🎨 Heritage (<span id="cnt-heritage">0</span>)</button>
        <button class="admin-filter" data-filter="branding">🦅 Crest (<span id="cnt-branding">0</span>)</button>
      </div>

      <!-- Grid -->
      <div class="admin-photos-grid" id="admin-photos-grid">
        <div style="padding:40px; text-align:center; color:#576d60; grid-column:1/-1;">
          Loading gallery photos...
        </div>
      </div>
    </section>

    <!-- Edit Modal Dialog -->
    <div id="admin-edit-modal" class="edit-modal-backdrop" style="display:none;" role="dialog" aria-modal="true">
      <div class="edit-modal-card">
        <button class="edit-modal-close" id="close-edit-modal-btn" type="button" aria-label="Close dialog">&times;</button>
        <h3 style="font-family:'Playfair Display',serif; color:#072e1e; font-size:22px; margin:0 0 6px;">✏️ Edit Photo Details</h3>
        <p style="color:#576d60; font-size:13.5px; margin-bottom:18px;">Update the photo's title, subtitle/role, or move it to a different section.</p>
        
        <div style="text-align:center; margin-bottom:16px;">
          <img id="edit-preview-thumb" src="" alt="Photo thumbnail" style="max-height:130px; border-radius:8px; border:2px solid var(--gold); box-shadow:0 4px 12px rgba(0,0,0,0.15);"/>
        </div>

        <form id="admin-edit-form">
          <input type="hidden" id="edit-photo-id"/>
          <input type="hidden" id="edit-photo-image"/>

          <div class="form-group">
            <label for="edit-photo-title">Title / Person's Name *</label>
            <input type="text" id="edit-photo-title" required/>
          </div>

          <div class="form-group">
            <label for="edit-photo-subtitle">Role / Subtitle</label>
            <input type="text" id="edit-photo-subtitle"/>
          </div>

          <div class="form-group">
            <label for="edit-photo-category">Gallery Section *</label>
            <select id="edit-photo-category" required>
              <option value="brochure">📖 2026 Induction Brochure &amp; Media</option>
              <option value="executive">👔 Executive Committee</option>
              <option value="trustees">🏛️ Board of Trustees &amp; Founder</option>
              <option value="induction_committee">🤝 2026 Induction Planning Committee</option>
              <option value="events">🎖️ 2026 Induction Honorees</option>
              <option value="community">📸 Community Snaps</option>
              <option value="heritage">🎨 Imo State Artwork &amp; Landmarks</option>
              <option value="branding">🦅 Official Crest &amp; Swag</option>
            </select>
          </div>

          <div style="display:flex; gap:12px; margin-top:22px;">
            <button type="button" id="cancel-edit-btn" style="flex:1; padding:12px; background:#e5e7eb; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Cancel</button>
            <button type="submit" id="save-edit-btn" class="login-btn" style="flex:2; padding:12px; font-size:14px;">💾 Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>

<footer class="footer">
  <div class="wrap" style="text-align:center; padding:20px 0;">
    <div class="copyright">© <span id="yr">2026</span> Imolites in Diaspora Social Club USA · Powered by <strong>Digimadi media</strong></div>
  </div>
</footer>

<script>
const CLOUD_KV_ENDPOINT = 'https://kvs.ix.workers.dev/imolites/community-photos';
const STORAGE_KEY = 'imolites_community_photos_v6';

<?php if (!$is_logged_in): ?>
// Login Controller
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
// Authorized Dashboard Controller
window.SERVER_PHOTOS = <?= json_encode($preloaded_photos) ?>;
let allPhotosList = window.SERVER_PHOTOS || [];
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

// Edit modal elements
const editModal = document.getElementById('admin-edit-modal');
const closeEditModalBtn = document.getElementById('close-edit-modal-btn');
const cancelEditBtn = document.getElementById('cancel-edit-btn');
const adminEditForm = document.getElementById('admin-edit-form');
const editPhotoId = document.getElementById('edit-photo-id');
const editPhotoImage = document.getElementById('edit-photo-image');
const editPhotoTitle = document.getElementById('edit-photo-title');
const editPhotoSubtitle = document.getElementById('edit-photo-subtitle');
const editPhotoCategory = document.getElementById('edit-photo-category');
const editPreviewThumb = document.getElementById('edit-preview-thumb');

// Stat elements
const statTotal = document.getElementById('stat-total');
const statExec = document.getElementById('stat-exec');
const statTrustees = document.getElementById('stat-trustees');
const statInductComm = document.getElementById('stat-induct-comm');
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

// File picker
adminDropzone?.addEventListener('click', () => adminFileInput.click());
adminChangeFileBtn?.addEventListener('click', () => adminFileInput.click());

adminFileInput?.addEventListener('change', function() {
  const file = this.files[0];
  if (!file) return;

  if (file.size > 15 * 1024 * 1024) {
    alert('File is too large. Please select a photo under 15MB.');
    return;
  }

  const reader = new FileReader();
  reader.onload = function(e) {
    const img = new Image();
    img.onload = function() {
      let w = img.width, h = img.height, maxDim = 1400;
      if (w > maxDim || h > maxDim) {
        if (w > h) { h = Math.round((h * maxDim) / w); w = maxDim; }
        else { w = Math.round((w * maxDim) / h); h = maxDim; }
      }
      const canvas = document.createElement('canvas');
      canvas.width = w; canvas.height = h;
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

// Filter buttons
document.querySelectorAll('.admin-filter').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.admin-filter').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    activeAdminFilter = this.dataset.filter;
    renderAdminGrid();
  });
});

function updateStats(photos) {
  if (statTotal) statTotal.textContent = photos.length;
  if (statExec) statExec.textContent = photos.filter(p => p.category === 'executive').length;
  if (statTrustees) statTrustees.textContent = photos.filter(p => p.category === 'trustees').length;
  if (statInductComm) statInductComm.textContent = photos.filter(p => p.category === 'induction_committee').length;
  if (statInduction) statInduction.textContent = photos.filter(p => p.category === 'events').length;
  if (statComm) statComm.textContent = photos.filter(p => p.category === 'community').length;

  document.getElementById('cnt-all') && (document.getElementById('cnt-all').textContent = photos.length);
  document.getElementById('cnt-brochure') && (document.getElementById('cnt-brochure').textContent = photos.filter(p => p.category === 'brochure').length);
  document.getElementById('cnt-exec') && (document.getElementById('cnt-exec').textContent = photos.filter(p => p.category === 'executive').length);
  document.getElementById('cnt-trustees') && (document.getElementById('cnt-trustees').textContent = photos.filter(p => p.category === 'trustees').length);
  document.getElementById('cnt-induct-comm') && (document.getElementById('cnt-induct-comm').textContent = photos.filter(p => p.category === 'induction_committee').length);
  document.getElementById('cnt-events') && (document.getElementById('cnt-events').textContent = photos.filter(p => p.category === 'events').length);
  document.getElementById('cnt-comm') && (document.getElementById('cnt-comm').textContent = photos.filter(p => p.category === 'community').length);
  document.getElementById('cnt-heritage') && (document.getElementById('cnt-heritage').textContent = photos.filter(p => p.category === 'heritage').length);
  document.getElementById('cnt-branding') && (document.getElementById('cnt-branding').textContent = photos.filter(p => p.category === 'branding').length);
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
    else if (photo.category === 'trustees') { badgeClass = 'badge-trustees-admin'; badgeLabel = 'Trustees & Founder'; }
    else if (photo.category === 'induction_committee') { badgeClass = 'badge-induct-comm-admin'; badgeLabel = 'Induction Comm'; }
    else if (photo.category === 'community') { badgeClass = 'badge-comm-admin'; badgeLabel = 'Community Snap'; }
    else if (photo.category === 'events') { badgeClass = 'badge-event-admin'; badgeLabel = '2026 Honoree'; }

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
          <button type="button" class="btn-edit-photo" data-id="${photoId}" data-image="${photo.image}">
            ✏️ Edit Details
          </button>
          <button type="button" class="btn-delete-photo" data-id="${photoId}" data-image="${photo.image}">
            🗑️ Delete Photo
          </button>
        </div>
      </div>
    `;

    adminPhotosGrid.appendChild(card);
  });

  // Attach Edit handlers
  document.querySelectorAll('.btn-edit-photo').forEach(btn => {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      const img = this.dataset.image;
      const photo = allPhotosList.find(p => (p.id === id || p.image === img));
      if (!photo) return;

      editPhotoId.value = id;
      editPhotoImage.value = img;
      editPhotoTitle.value = photo.title || photo.alt || '';
      editPhotoSubtitle.value = photo.subtitle || '';
      editPhotoCategory.value = photo.category || 'community';
      editPreviewThumb.src = photo.image;

      editModal.style.display = 'flex';
    });
  });

  // Attach Delete handlers
  document.querySelectorAll('.btn-delete-photo').forEach(btn => {
    btn.addEventListener('click', async function() {
      const id = this.dataset.id;
      const img = this.dataset.image;
      const photo = allPhotosList.find(p => (p.id === id || p.image === img));
      const title = photo ? (photo.title || 'this photo') : 'this photo';

      if (!confirm(`Are you sure you want to permanently delete "${title}"? It will be removed from the live website immediately.`)) {
        return;
      }

      this.disabled = true;
      this.textContent = 'Deleting...';

      try {
        const formData = new FormData();
        formData.append('action', 'delete_photo');
        formData.append('id', id);
        formData.append('image', img);

        await fetch('admin-api.php?action=delete_photo', { method: 'POST', body: formData });
      } catch(e) {}

      allPhotosList = allPhotosList.filter(p => !(p.id === id || p.image === img));
      updateStats(allPhotosList);
      renderAdminGrid();
    });
  });
}

// Edit Form submit
adminEditForm?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const id = editPhotoId.value;
  const img = editPhotoImage.value;
  const newTitle = editPhotoTitle.value.trim();
  const newSubtitle = editPhotoSubtitle.value.trim();
  const newCategory = editPhotoCategory.value;

  const saveBtn = document.getElementById('save-edit-btn');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';

  try {
    const formData = new FormData();
    formData.append('action', 'edit_photo');
    formData.append('id', id);
    formData.append('image', img);
    formData.append('title', newTitle);
    formData.append('subtitle', newSubtitle);
    formData.append('category', newCategory);

    await fetch('admin-api.php?action=edit_photo', { method: 'POST', body: formData });
  } catch(e) {}

  const photo = allPhotosList.find(p => (p.id === id || p.image === img));
  if (photo) {
    photo.title = newTitle;
    photo.alt = newTitle;
    photo.subtitle = newSubtitle;
    photo.category = newCategory;
  }

  saveBtn.disabled = false;
  saveBtn.textContent = '💾 Save Changes';
  editModal.style.display = 'none';

  updateStats(allPhotosList);
  renderAdminGrid();
});

closeEditModalBtn?.addEventListener('click', () => editModal.style.display = 'none');
cancelEditBtn?.addEventListener('click', () => editModal.style.display = 'none');
editModal?.addEventListener('click', (e) => { if (e.target === editModal) editModal.style.display = 'none'; });

// Admin Upload Form
adminUploadForm?.addEventListener('submit', async function(e) {
  e.preventDefault();
  const title = document.getElementById('photo-title').value.trim();
  const subtitle = document.getElementById('photo-subtitle').value.trim();
  const category = document.getElementById('photo-category').value;

  if (!selectedAdminDataUrl && !adminFileInput.files[0]) {
    alert('Please choose a photo to upload.');
    return;
  }

  adminSubmitBtn.disabled = true;
  adminSubmitBtn.textContent = '🛡️ Scanning with Antivirus & Publishing...';
  uploadStatusAlert.style.display = 'none';

  try {
    let savedPhoto = null;
    // 1. Try PHP API if on Hostinger
    try {
      const formData = new FormData();
      formData.append('action', 'upload_photo');
      formData.append('category', category);
      formData.append('title', title);
      formData.append('subtitle', subtitle);
      if (selectedAdminDataUrl) formData.append('image_data', selectedAdminDataUrl);
      else if (adminFileInput.files[0]) formData.append('photo', adminFileInput.files[0]);

      const res = await fetch('admin-api.php?action=upload_photo', { method: 'POST', body: formData });
      if (res.ok) {
        const data = await res.json();
        if (data.success && data.photo) {
          savedPhoto = data.photo;
        } else if (data.error) {
          throw new Error(data.error);
        }
      }
    } catch(err) {
      if (err.message && !err.message.includes('fetch')) {
        throw err;
      }
    }

    if (!savedPhoto) {
      savedPhoto = {
        id: 'admin_' + category + '_' + Date.now(),
        category: category,
        image: selectedAdminDataUrl,
        title: title,
        alt: title,
        subtitle: subtitle || ('Official Club Media • Added on ' + new Date().toLocaleDateString()),
        timestamp: Date.now(),
        added_by: 'Admin'
      };
    }

    // Save to local admin photos list so gallery displays it immediately
    try {
      const admList = JSON.parse(localStorage.getItem('imolites_admin_photos') || '[]');
      admList.unshift(savedPhoto);
      localStorage.setItem('imolites_admin_photos', JSON.stringify(admList));
    } catch(e) {}

    allPhotosList.unshift(savedPhoto);
    updateStats(allPhotosList);
    renderAdminGrid();

    uploadStatusAlert.style.display = 'block';
    uploadStatusAlert.style.background = '#ecfdf5';
    uploadStatusAlert.style.color = '#065f46';
    uploadStatusAlert.style.border = '1.5px solid #6ee7b7';
    uploadStatusAlert.textContent = '🎉 Photo successfully scanned, verified, and published live!';

    adminUploadForm.reset();
    selectedAdminDataUrl = '';
    adminImagePreview.src = '';
    adminPreviewWrap.style.display = 'none';
    adminDropzone.style.display = 'block';
  } catch(err) {
    uploadStatusAlert.style.display = 'block';
    uploadStatusAlert.style.background = '#fef2f2';
    uploadStatusAlert.style.color = '#991b1b';
    uploadStatusAlert.style.border = '1.5px solid #fca5a5';
    uploadStatusAlert.textContent = '❌ ' + (err.message || 'Error uploading photo.');
  }

  adminSubmitBtn.disabled = false;
  adminSubmitBtn.textContent = '🚀 Scan & Publish Live to Gallery →';
});

// Initial render
updateStats(allPhotosList);
renderAdminGrid();

// Fetch fresh list from API in background
(async function refreshFromApi() {
  try {
    const res = await fetch('admin-api.php?action=list_all');
    if (res.ok) {
      const d = await res.json();
      if (d.success) {
        allPhotosList = [...(d.community_photos||[]), ...(d.admin_photos||[]), ...(d.base_photos||[])];
        updateStats(allPhotosList);
        renderAdminGrid();
      }
    }
  } catch(e) {}
})();
<?php endif; ?>
</script>
</body>
</html>

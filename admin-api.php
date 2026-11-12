<?php
/**
 * Imolites in Diaspora Social Club USA
 * Admin Management API
 * Controls Gallery Photos, Section Categorization, Community Moderation, and Antivirus Uploads
 */

session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Admin Credentials
define('ADMIN_EMAIL_1', 'imolitesusa2@gmail');
define('ADMIN_EMAIL_2', 'imolitesusa2@gmail.com');
define('ADMIN_PASSWORD', 'imolitesusa450');

$communityFile = __DIR__ . '/data/community_photos.json';
$adminItemsFile = __DIR__ . '/data/admin_gallery_items.json';
$deletedItemsFile = __DIR__ . '/data/deleted_photos.json';
$uploadDir = __DIR__ . '/uploads/';

if (!file_exists(__DIR__ . '/data')) {
    @mkdir(__DIR__ . '/data', 0755, true);
}
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Antivirus signature scanner
function scan_admin_file_for_malware($binary) {
    if (strlen($binary) === 0) return 'Empty file payload';
    $patterns = [
        '/<\?php/i', '/<\?=/i', '/<script/i', '/eval\s*\(/i',
        '/base64_decode\s*\(/i', '/gzinflate\s*\(/i', '/system\s*\(/i',
        '/shell_exec\s*\(/i', '/passthru\s*\(/i', '/exec\s*\(/i',
        '/\/bin\/sh/i', '/\/bin\/bash/i', '/cmd\.exe/i', '/powershell/i',
        '/<svg/i', '/<html/i', '/document\.cookie/i'
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $binary)) {
            return 'Disallowed executable code signature detected';
        }
    }
    return null;
}

// Verify magic bytes
function verify_admin_magic_bytes($binary) {
    if (strlen($binary) < 12) return false;
    if (substr($binary, 0, 3) === "\xFF\xD8\xFF") return 'jpg';
    if (substr($binary, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") return 'png';
    if (substr($binary, 0, 4) === "RIFF" && substr($binary, 8, 4) === "WEBP") return 'webp';
    if (substr($binary, 0, 6) === "GIF87a" || substr($binary, 0, 6) === "GIF89a") return 'gif';
    return false;
}

// Re-encode with GD
function sanitize_admin_image($binary, $destinationPath, $detectedExt) {
    $imgInfo = @getimagesizefromstring($binary);
    if ($imgInfo === false || empty($imgInfo[0]) || empty($imgInfo[1])) return false;
    if ($imgInfo[0] > 6000 || $imgInfo[1] > 6000) return false;

    if (function_exists('imagecreatefromstring')) {
        $srcImg = @imagecreatefromstring($binary);
        if ($srcImg !== false) {
            $saved = false;
            if ($detectedExt === 'png') {
                imagealphablending($srcImg, false);
                imagesavealpha($srcImg, true);
                $saved = @imagepng($srcImg, $destinationPath, 8);
            } else {
                $saved = @imagejpeg($srcImg, $destinationPath, 85);
            }
            @imagedestroy($srcImg);
            if ($saved) return true;
        }
    }
    return (@file_put_contents($destinationPath, $binary) !== false);
}

function is_admin_logged_in() {
    return !empty($_SESSION['imolites_admin_logged_in']);
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// 1. ACTION: LOGIN
if ($action === 'login') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    $validEmail = ($email === ADMIN_EMAIL_1 || $email === ADMIN_EMAIL_2);
    $validPass  = ($password === ADMIN_PASSWORD);

    if ($validEmail && $validPass) {
        $_SESSION['imolites_admin_logged_in'] = true;
        $_SESSION['imolites_admin_email'] = $email;
        $_SESSION['imolites_admin_login_time'] = time();

        echo json_encode([
            'success' => true,
            'message' => 'Admin authentication successful.',
            'user'    => ['email' => $email]
        ]);
        exit;
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error'   => 'Invalid admin credentials. Please check your email and password.'
        ]);
        exit;
    }
}

// 2. ACTION: LOGOUT
if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
    exit;
}

// 3. ACTION: CHECK AUTH
if ($action === 'check') {
    echo json_encode([
        'authenticated' => is_admin_logged_in(),
        'email'         => $_SESSION['imolites_admin_email'] ?? null
    ]);
    exit;
}

// PROTECTED ACTIONS REQUIRE LOGIN
if (!is_admin_logged_in()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized. Please log in first.']);
    exit;
}

// 4. ACTION: LIST ALL PHOTOS FOR ADMIN DASHBOARD
if ($action === 'list_all') {
    // Base gallery items from gallery_data.php
    require_once __DIR__ . '/data/gallery_data.php';
    
    // Deleted items list
    $deletedIds = [];
    if (file_exists($deletedItemsFile)) {
        $deletedIds = json_decode(file_get_contents($deletedItemsFile), true) ?: [];
    }

    // Community photos
    $communityPhotos = [];
    if (file_exists($communityFile)) {
        $communityPhotos = json_decode(file_get_contents($communityFile), true) ?: [];
    }

    // Admin custom photos
    $adminPhotos = [];
    if (file_exists($adminItemsFile)) {
        $adminPhotos = json_decode(file_get_contents($adminItemsFile), true) ?: [];
    }

    // Edited items metadata overrides
    $editedFile = __DIR__ . '/data/edited_photos.json';
    $edits = [];
    if (file_exists($editedFile)) {
        $edits = json_decode(file_get_contents($editedFile), true) ?: [];
    }

    // Apply edits helper
    $apply_edits = function(&$item) use ($edits) {
        $id = $item['id'] ?? '';
        $img = $item['image'] ?? '';
        $key = isset($edits[$id]) ? $id : (isset($edits[$img]) ? $img : null);
        if ($key && isset($edits[$key])) {
            $e = $edits[$key];
            if (!empty($e['title'])) { $item['title'] = $e['title']; $item['alt'] = $e['title']; }
            if (isset($e['subtitle'])) { $item['subtitle'] = $e['subtitle']; }
            if (!empty($e['category'])) { $item['category'] = $e['category']; }
        }
    };

    // Filter out deleted items and apply edits
    $communityPhotos = array_values(array_filter($communityPhotos, function($p) use ($deletedIds) {
        $id = $p['id'] ?? '';
        $img = $p['image'] ?? '';
        return !in_array($id, $deletedIds) && !in_array($img, $deletedIds);
    }));
    foreach ($communityPhotos as &$cp) { $apply_edits($cp); }

    $adminPhotos = array_values(array_filter($adminPhotos, function($p) use ($deletedIds) {
        $id = $p['id'] ?? '';
        $img = $p['image'] ?? '';
        return !in_array($id, $deletedIds) && !in_array($img, $deletedIds);
    }));
    foreach ($adminPhotos as &$ap) { $apply_edits($ap); }

    // Load base items from data/base_gallery_items.json
    $baseItems = [];
    $baseFile = __DIR__ . '/data/base_gallery_items.json';
    if (file_exists($baseFile)) {
        $baseItems = json_decode(file_get_contents($baseFile), true) ?: [];
    }
    if (empty($baseItems)) {
        foreach ($gallery_items as $item) {
            $slug = preg_replace('/[^a-z0-9_-]/i', '_', basename($item['image']));
            $item['id'] = 'base_' . $slug;
            $baseItems[] = $item;
        }
    }

    $filteredBaseItems = [];
    foreach ($baseItems as $item) {
        $id = $item['id'] ?? '';
        $img = $item['image'] ?? '';
        $legacyKey = 'base_' . md5($img);
        if (!in_array($id, $deletedIds) && !in_array($img, $deletedIds) && !in_array($legacyKey, $deletedIds)) {
            $apply_edits($item);
            $filteredBaseItems[] = $item;
        }
    }

    echo json_encode([
        'success'           => true,
        'community_photos'  => $communityPhotos,
        'admin_photos'      => $adminPhotos,
        'base_photos'       => $filteredBaseItems,
        'deleted_count'     => count($deletedIds)
    ]);
    exit;
}

// 5. ACTION: ADMIN UPLOAD PHOTO TO ANY SECTION
if ($action === 'upload_photo') {
    $category = trim($_POST['category'] ?? 'executive');
    $allowedCategories = ['executive', 'trustees', 'induction_committee', 'events', 'community', 'heritage', 'branding'];
    if (!in_array($category, $allowedCategories)) {
        $category = 'executive';
    }

    $title = trim($_POST['title'] ?? '');
    if ($title === '') {
        echo json_encode(['success' => false, 'error' => 'Please provide a title or name for the photo.']);
        exit;
    }

    $subtitle = trim($_POST['subtitle'] ?? '');
    $dateStr = date('M j, Y');

    // Default subtitles per section if empty
    if ($subtitle === '') {
        if ($category === 'executive') {
            $subtitle = 'Executive Committee • Added by Admin on ' . $dateStr;
        } elseif ($category === 'trustees') {
            $subtitle = 'Board of Trustees & Founder • Added by Admin on ' . $dateStr;
        } elseif ($category === 'induction_committee') {
            $subtitle = '2026 Induction Committee • Added by Admin on ' . $dateStr;
        } elseif ($category === 'events') {
            $subtitle = '2026 Induction Honorees • Added by Admin on ' . $dateStr;
        } elseif ($category === 'community') {
            $subtitle = 'Club Family Photo • Added by Admin on ' . $dateStr;
        } else {
            $subtitle = 'Official Club Media • Added on ' . $dateStr;
        }
    }

    $id = 'admin_' . $category . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
    $binaryData = null;
    $detectedExt = 'jpg';

    if (!empty($_POST['image_data'])) {
        $dataUrl = $_POST['image_data'];
        if (preg_match('/^data:image\/(jpeg|jpg|png|webp|gif);base64,(.+)$/i', $dataUrl, $matches)) {
            $binaryData = base64_decode($matches[2]);
        }
    }

    if (!$binaryData && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $binaryData = @file_get_contents($_FILES['photo']['tmp_name']);
    }

    if (!$binaryData) {
        echo json_encode(['success' => false, 'error' => 'No image file uploaded.']);
        exit;
    }

    // Antivirus checks
    if (strlen($binaryData) > 15 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Security: Image file is too large (max 15MB).']);
        exit;
    }

    $malwareScanResult = scan_admin_file_for_malware($binaryData);
    if ($malwareScanResult !== null) {
        echo json_encode(['success' => false, 'error' => 'Security Alert: File failed antivirus scan and was rejected.']);
        exit;
    }

    $verifiedFormat = verify_admin_magic_bytes($binaryData);
    if ($verifiedFormat === false) {
        echo json_encode(['success' => false, 'error' => 'Invalid image format. Only JPG, PNG, and WEBP are allowed.']);
        exit;
    }
    $detectedExt = $verifiedFormat;

    $cleanFileName = $id . '.' . ($detectedExt === 'png' ? 'png' : 'jpg');
    $targetPath = $uploadDir . $cleanFileName;

    $sanitized = sanitize_admin_image($binaryData, $targetPath, $detectedExt);
    $savedPath = ($sanitized && file_exists($targetPath)) ? ('uploads/' . $cleanFileName) : ($_POST['image_data'] ?? null);

    if (!$savedPath) {
        echo json_encode(['success' => false, 'error' => 'Could not save photo to server.']);
        exit;
    }

    $newPhotoEntry = [
        'id'        => $id,
        'category'  => $category,
        'image'     => $savedPath,
        'alt'       => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'title'     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'subtitle'  => htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'),
        'timestamp' => time() * 1000,
        'added_by'  => 'Admin (' . ($_SESSION['imolites_admin_email'] ?? 'imolitesusa2@gmail') . ')'
    ];

    // Read and save to admin_gallery_items.json
    $adminPhotos = [];
    if (file_exists($adminItemsFile)) {
        $adminPhotos = json_decode(file_get_contents($adminItemsFile), true) ?: [];
    }
    array_unshift($adminPhotos, $newPhotoEntry);
    @file_put_contents($adminItemsFile, json_encode($adminPhotos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode([
        'success' => true,
        'message' => 'Photo successfully scanned, verified, and published to ' . ucfirst($category) . ' section!',
        'photo'   => $newPhotoEntry
    ]);
    exit;
}

// 6. ACTION: DELETE PHOTO (COMMUNITY OR ADMIN OR BASE)
if ($action === 'delete_photo') {
    $photoId = trim($_POST['id'] ?? '');
    $imagePath = trim($_POST['image'] ?? '');

    if ($photoId === '' && $imagePath === '') {
        echo json_encode(['success' => false, 'error' => 'No photo ID or path specified for deletion.']);
        exit;
    }

    // 1. Delete from community_photos.json
    if (file_exists($communityFile)) {
        $comm = json_decode(file_get_contents($communityFile), true) ?: [];
        $comm = array_values(array_filter($comm, function($p) use ($photoId, $imagePath) {
            return ($p['id'] ?? '') !== $photoId && ($p['image'] ?? '') !== $imagePath;
        }));
        @file_put_contents($communityFile, json_encode($comm, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    // 2. Delete from admin_gallery_items.json
    if (file_exists($adminItemsFile)) {
        $adm = json_decode(file_get_contents($adminItemsFile), true) ?: [];
        $adm = array_values(array_filter($adm, function($p) use ($photoId, $imagePath) {
            return ($p['id'] ?? '') !== $photoId && ($p['image'] ?? '') !== $imagePath;
        }));
        @file_put_contents($adminItemsFile, json_encode($adm, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    // 3. Add to deleted_photos.json blacklist
    $deletedIds = [];
    if (file_exists($deletedItemsFile)) {
        $deletedIds = json_decode(file_get_contents($deletedItemsFile), true) ?: [];
    }
    if ($photoId && !in_array($photoId, $deletedIds)) {
        $deletedIds[] = $photoId;
    }
    if ($imagePath && !in_array($imagePath, $deletedIds)) {
        $deletedIds[] = $imagePath;
    }
    @file_put_contents($deletedItemsFile, json_encode($deletedIds, JSON_PRETTY_PRINT));

    // 4. If image exists in uploads directory, remove the physical file
    if ($imagePath && strpos($imagePath, 'uploads/') === 0) {
        $localFile = __DIR__ . '/' . $imagePath;
        if (file_exists($localFile) && is_file($localFile)) {
            @unlink($localFile);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Photo deleted successfully from live gallery and server storage.',
        'deleted_id' => $photoId
    ]);
    exit;
}

// 7. ACTION: EDIT PHOTO DETAILS
if ($action === 'edit_photo') {
    $photoId = trim($_POST['id'] ?? '');
    $imagePath = trim($_POST['image'] ?? '');
    $newTitle = trim($_POST['title'] ?? '');
    $newSubtitle = trim($_POST['subtitle'] ?? '');
    $newCategory = trim($_POST['category'] ?? '');

    if ($photoId === '' && $imagePath === '') {
        echo json_encode(['success' => false, 'error' => 'No photo ID or image specified for editing.']);
        exit;
    }

    $editedFile = __DIR__ . '/data/edited_photos.json';
    $edits = [];
    if (file_exists($editedFile)) {
        $edits = json_decode(file_get_contents($editedFile), true) ?: [];
    }

    $editKey = $photoId ?: $imagePath;
    $updatedEntry = [
        'id'        => $photoId,
        'image'     => $imagePath,
        'title'     => htmlspecialchars($newTitle, ENT_QUOTES, 'UTF-8'),
        'alt'       => htmlspecialchars($newTitle, ENT_QUOTES, 'UTF-8'),
        'subtitle'  => htmlspecialchars($newSubtitle, ENT_QUOTES, 'UTF-8'),
        'category'  => $newCategory,
        'edited_at' => time()
    ];
    $edits[$editKey] = $updatedEntry;
    @file_put_contents($editedFile, json_encode($edits, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    // Also update community_photos.json if exists
    if (file_exists($communityFile)) {
        $comm = json_decode(file_get_contents($communityFile), true) ?: [];
        $changed = false;
        foreach ($comm as &$p) {
            if (($p['id'] ?? '') === $photoId || ($p['image'] ?? '') === $imagePath) {
                if ($newTitle !== '') { $p['title'] = $newTitle; $p['alt'] = $newTitle; }
                if ($newSubtitle !== '') { $p['subtitle'] = $newSubtitle; }
                if ($newCategory !== '') { $p['category'] = $newCategory; }
                $changed = true;
            }
        }
        if ($changed) {
            @file_put_contents($communityFile, json_encode($comm, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    // Also update admin_gallery_items.json if exists
    if (file_exists($adminItemsFile)) {
        $adm = json_decode(file_get_contents($adminItemsFile), true) ?: [];
        $changed = false;
        foreach ($adm as &$p) {
            if (($p['id'] ?? '') === $photoId || ($p['image'] ?? '') === $imagePath) {
                if ($newTitle !== '') { $p['title'] = $newTitle; $p['alt'] = $newTitle; }
                if ($newSubtitle !== '') { $p['subtitle'] = $newSubtitle; }
                if ($newCategory !== '') { $p['category'] = $newCategory; }
                $changed = true;
            }
        }
        if ($changed) {
            @file_put_contents($adminItemsFile, json_encode($adm, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Photo details updated successfully.',
        'updated' => $updatedEntry
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);


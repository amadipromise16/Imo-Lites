<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/data/community_photos.json';
$uploadDir = __DIR__ . '/uploads/';

// Ensure directories exist with writable permissions
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}
if (!file_exists(__DIR__ . '/data')) {
    @mkdir(__DIR__ . '/data', 0777, true);
}

// Filter out legacy test photos
function is_test_entry($p) {
    $sub = strtolower($p['subtitle'] ?? '');
    $tit = strtolower($p['title'] ?? '');
    return (strpos($sub, '2:59 pm') !== false || (strpos($sub, 'nov 10, 2026') !== false && strpos($tit, 'community celebration snap') !== false));
}

// Handle GET: return all photos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $photos = [];
    if (file_exists($dataFile)) {
        $json = @file_get_contents($dataFile);
        $photos = json_decode($json, true) ?: [];
        $photos = array_values(array_filter($photos, function($p) { return !is_test_entry($p); }));
    }
    echo json_encode(['success' => true, 'photos' => $photos]);
    exit;
}

// Handle POST: upload new photo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? 'Community Celebration Snap');
    if ($title === '') $title = 'Community Celebration Snap';
    
    $uploader = trim($_POST['uploader'] ?? 'Club Member');
    if ($uploader === '') $uploader = 'Club Member';

    $dateStr = date('M j, Y · g:i A');
    $savedRelativePath = null;
    $id = 'snap_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));

    // Priority 1: Base64 / Compressed image data (optimized on client, guarantees upload under PHP limit)
    if (!empty($_POST['image_data'])) {
        $dataUrl = $_POST['image_data'];
        if (preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', $dataUrl, $matches)) {
            $ext = strtolower($matches[1]);
            if ($ext === 'jpeg') $ext = 'jpg';
            $decoded = base64_decode($matches[2]);
            if ($decoded !== false && strlen($decoded) > 0) {
                $fileName = $id . '.' . $ext;
                $target = $uploadDir . $fileName;
                if (@file_put_contents($target, $decoded) !== false) {
                    $savedRelativePath = 'uploads/' . $fileName;
                }
            }
        }
    }

    // Priority 2: Standard multipart file upload
    if (!$savedRelativePath && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? @finfo_file($finfo, $file['tmp_name']) : ($file['type'] ?? 'image/jpeg');
        if ($finfo) @finfo_close($finfo);

        if (in_array($mimeType, $allowedTypes)) {
            $extMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif'
            ];
            $ext = $extMap[$mimeType] ?? 'jpg';
            $fileName = $id . '.' . $ext;
            $target = $uploadDir . $fileName;
            if (@move_uploaded_file($file['tmp_name'], $target)) {
                $savedRelativePath = 'uploads/' . $fileName;
            }
        }
    }

    // If file write was blocked by host filesystem permissions, fallback to inline dataUrl
    $imageSource = $savedRelativePath ?: ($_POST['image_data'] ?? null);

    if (!$imageSource) {
        $errorCode = $_FILES['photo']['error'] ?? 'no_file';
        echo json_encode([
            'success' => false,
            'error'   => 'Could not process uploaded photo. (Code: ' . $errorCode . '). Please try again.'
        ]);
        exit;
    }

    $newEntry = [
        'id'        => $id,
        'category'  => 'community',
        'image'     => $imageSource,
        'alt'       => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'title'     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'subtitle'  => 'Shared by ' . htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8') . ' on ' . $dateStr,
        'uploader'  => htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8'),
        'timestamp' => time()
    ];

    $existing = [];
    if (file_exists($dataFile)) {
        $json = @file_get_contents($dataFile);
        $existing = json_decode($json, true) ?: [];
    }

    // Filter out duplicates and keep latest 100 entries
    $existing = array_values(array_filter($existing, function($p) use ($newEntry) {
        return $p['id'] !== $newEntry['id'] && $p['image'] !== $newEntry['image'];
    }));
    array_unshift($existing, $newEntry);
    if (count($existing) > 100) {
        $existing = array_slice($existing, 0, 100);
    }

    @file_put_contents($dataFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode([
        'success' => true,
        'message' => 'Your photo has been uploaded directly to the gallery!',
        'photo'   => $newEntry
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Method not allowed.']);

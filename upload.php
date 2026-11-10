<?php
header('Content-Type: application/json; charset=utf-8');

$dataFile = __DIR__ . '/data/community_photos.json';
$uploadDir = __DIR__ . '/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle GET: return all photos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $photos = [];
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $photos = json_decode($json, true) ?: [];
    }
    echo json_encode(['success' => true, 'photos' => $photos]);
    exit;
}

// Handle POST: upload new photo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['photo']['error'] ?? 'no_file';
        echo json_encode(['success' => false, 'error' => 'Please select a photo to upload. (Error: ' . $errorCode . ')']);
        exit;
    }

    $file = $_FILES['photo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'error' => 'Invalid file format. Only JPG, PNG, WEBP, and GIF are allowed.']);
        exit;
    }

    // Limit size to 15MB
    if ($file['size'] > 15 * 1024 * 1024) {
        echo json_encode(['success' => false, 'error' => 'Photo size is too large (maximum 15MB).']);
        exit;
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];
    $ext = $extMap[$mimeType] ?? 'jpg';
    $newFileName = 'snap_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'error' => 'Failed to save the uploaded photo to server storage.']);
        exit;
    }

    $title = trim($_POST['title'] ?? 'Community Celebration Snap');
    if ($title === '') $title = 'Community Celebration Snap';
    
    $uploader = trim($_POST['uploader'] ?? 'Club Member');
    if ($uploader === '') $uploader = 'Club Member';

    $dateStr = date('M j, Y · g:i A');

    $newEntry = [
        'id'        => basename($newFileName, '.' . $ext),
        'category'  => 'community',
        'image'     => 'uploads/' . $newFileName,
        'alt'       => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'title'     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'subtitle'  => 'Shared by ' . htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8') . ' on ' . $dateStr,
        'uploader'  => htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8'),
        'timestamp' => time()
    ];

    $existing = [];
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $existing = json_decode($json, true) ?: [];
    }

    array_unshift($existing, $newEntry);
    file_put_contents($dataFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode([
        'success' => true,
        'message' => 'Your photo has been uploaded directly to the gallery!',
        'photo'   => $newEntry
    ]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Method not allowed.']);

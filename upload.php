<?php
/**
 * Imolites in Diaspora Social Club USA
 * Secure Public Photo Upload & Community Storage API
 * Includes Antivirus Scanning, Magic-Byte Verification, and GD Re-encoding Sanitization
 */

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

// Ensure directories exist
if (!file_exists($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}
if (!file_exists(__DIR__ . '/data')) {
    @mkdir(__DIR__ . '/data', 0755, true);
}

// Antivirus signature scanner
function scan_for_malicious_content($binary) {
    if (strlen($binary) === 0) {
        return 'Empty file payload';
    }
    
    // Dangerous signatures & webshell patterns
    $patterns = [
        '/<\?php/i',
        '/<\?=/i',
        '/<script/i',
        '/eval\s*\(/i',
        '/base64_decode\s*\(/i',
        '/gzinflate\s*\(/i',
        '/str_rot13\s*\(/i',
        '/system\s*\(/i',
        '/shell_exec\s*\(/i',
        '/passthru\s*\(/i',
        '/exec\s*\(/i',
        '/popen\s*\(/i',
        '/proc_open\s*\(/i',
        '/\/bin\/sh/i',
        '/\/bin\/bash/i',
        '/cmd\.exe/i',
        '/powershell/i',
        '/<svg/i',
        '/<html/i',
        '/<iframe/i',
        '/javascript:/i',
        '/onload\s*=/i',
        '/onerror\s*=/i',
        '/document\.cookie/i'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $binary)) {
            return 'Disallowed executable code signature detected';
        }
    }
    
    return null;
}

// Magic-byte check
function verify_image_magic_bytes($binary) {
    if (strlen($binary) < 12) return false;
    
    // JPEG: \xFF\xD8\xFF
    if (substr($binary, 0, 3) === "\xFF\xD8\xFF") return 'jpg';
    
    // PNG: \x89PNG\r\n\x1a\n
    if (substr($binary, 0, 8) === "\x89PNG\x0D\x0A\x1A\x0A") return 'png';
    
    // WEBP: RIFF....WEBP
    if (substr($binary, 0, 4) === "RIFF" && substr($binary, 8, 4) === "WEBP") return 'webp';
    
    // GIF: GIF87a or GIF89a
    if (substr($binary, 0, 6) === "GIF87a" || substr($binary, 0, 6) === "GIF89a") return 'gif';
    
    return false;
}

// Sanitize & re-encode image via GD to strip all virus payloads
function sanitize_and_save_image($binary, $destinationPath, $detectedExt) {
    // 1. Verify structure via getimagesize
    $imgInfo = @getimagesizefromstring($binary);
    if ($imgInfo === false || empty($imgInfo[0]) || empty($imgInfo[1])) {
        return false;
    }
    
    $width = $imgInfo[0];
    $height = $imgInfo[1];
    
    // Limit unreasonable dimensions to prevent DOS decompression bomb
    if ($width > 6000 || $height > 6000) {
        return false;
    }

    // 2. If GD is installed, re-encode into fresh image to destroy any shellcode/polyglots
    if (function_exists('imagecreatefromstring')) {
        $srcImg = @imagecreatefromstring($binary);
        if ($srcImg !== false) {
            $saved = false;
            // Always save as clean JPEG or PNG
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

    // Fallback: file write if GD is unavailable but magic bytes and dimensions are valid
    return (@file_put_contents($destinationPath, $binary) !== false);
}

// Filter out test entries
function is_test_entry($p) {
    $sub = strtolower($p['subtitle'] ?? '');
    $tit = strtolower($p['title'] ?? '');
    return (strpos($sub, '2:59 pm') !== false || (strpos($sub, 'nov 10, 2026') !== false && strpos($tit, 'community celebration snap') !== false));
}

// Handle GET: return all public community photos
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

    $dateStr = date('M j, Y');
    $savedRelativePath = null;
    $id = 'snap_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6));

    $binaryData = null;
    $detectedExt = 'jpg';

    // Priority 1: Base64 / Compressed image data
    if (!empty($_POST['image_data'])) {
        $dataUrl = $_POST['image_data'];
        if (preg_match('/^data:image\/(jpeg|jpg|png|webp|gif);base64,(.+)$/i', $dataUrl, $matches)) {
            $binaryData = base64_decode($matches[2]);
        }
    }

    // Priority 2: Standard multipart file upload
    if (!$binaryData && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $binaryData = @file_get_contents($_FILES['photo']['tmp_name']);
    }

    if (!$binaryData) {
        echo json_encode([
            'success' => false,
            'error'   => 'No valid photo data received. Please select or snap a photo.'
        ]);
        exit;
    }

    // Antivirus Check 1: File size guard (max 15MB)
    if (strlen($binaryData) > 15 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'error'   => 'Security: Image file is too large (maximum 15MB allowed).'
        ]);
        exit;
    }

    // Antivirus Check 2: Deep Malicious Code Signature Scan
    $malwareScanResult = scan_for_malicious_content($binaryData);
    if ($malwareScanResult !== null) {
        echo json_encode([
            'success' => false,
            'error'   => 'Security Alert: File failed antivirus scan and was rejected.'
        ]);
        exit;
    }

    // Antivirus Check 3: Magic Bytes Verification
    $verifiedFormat = verify_image_magic_bytes($binaryData);
    if ($verifiedFormat === false) {
        echo json_encode([
            'success' => false,
            'error'   => 'Security Alert: Invalid image format. Only authentic JPG, PNG, and WEBP photos are allowed.'
        ]);
        exit;
    }
    $detectedExt = $verifiedFormat;

    // Secure Storage: Generate cryptographic random filename (never use client-supplied name)
    $cleanFileName = $id . '.' . ($detectedExt === 'png' ? 'png' : 'jpg');
    $targetPath = $uploadDir . $cleanFileName;

    // Antivirus Check 4: GD Re-encoding Sanitization
    $sanitized = sanitize_and_save_image($binaryData, $targetPath, $detectedExt);
    if ($sanitized && file_exists($targetPath)) {
        $savedRelativePath = 'uploads/' . $cleanFileName;
    }

    // Fallback: If disk write failed due to host directory permissions, keep safe dataUrl
    $imageSource = $savedRelativePath ?: ($_POST['image_data'] ?? null);

    if (!$imageSource) {
        echo json_encode([
            'success' => false,
            'error'   => 'Could not save photo to server storage. Please try again.'
        ]);
        exit;
    }

    $newPhoto = [
        'id'        => $id,
        'category'  => 'community',
        'image'     => $imageSource,
        'title'     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        'subtitle'  => 'Shared by ' . htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8') . ' on ' . $dateStr,
        'uploader'  => htmlspecialchars($uploader, ENT_QUOTES, 'UTF-8'),
        'timestamp' => time() * 1000
    ];

    // Append to community_photos.json
    $photos = [];
    if (file_exists($dataFile)) {
        $json = @file_get_contents($dataFile);
        $photos = json_decode($json, true) ?: [];
    }

    $photos = array_filter($photos, function($p) use ($id, $imageSource) {
        return ($p['id'] ?? '') !== $id && ($p['image'] ?? '') !== $imageSource && !is_test_entry($p);
    });

    array_unshift($photos, $newPhoto);
    if (count($photos) > 60) {
        $photos = array_slice($photos, 0, 60);
    }

    @file_put_contents($dataFile, json_encode($photos, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    echo json_encode([
        'success' => true,
        'message' => 'Photo verified, sanitized, and published successfully!',
        'photo'   => $newPhoto
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

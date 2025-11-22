<?php
// upload.php
ini_set('display_errors', 0); 
error_reporting(E_ALL);
require __DIR__ . '/config.php';

// Large file check
if (empty($_FILES) && empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    json(['ok' => false, 'error' => "File too big! Check post_max_size in php.ini"], 413);
}

$room = isset($_POST['room']) ? trim($_POST['room']) : 'default';
if ($room === '') $room = 'default';

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['video'])) {
    json(['ok' => false, 'error' => 'No file received'], 400);
}

$file = $_FILES['video'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    json(['ok' => false, 'error' => 'Upload failed with code ' . $file['error']], 400);
}

$allowedExt = ['mp4', 'webm', 'ogg', 'mkv'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt)) {
    json(['ok' => false, 'error' => 'Invalid file type'], 415);
}

$name = uniqid('vid_', true) . '.' . $ext;
$path = $uploadDir . '/' . $name;

if (!@move_uploaded_file($file['tmp_name'], $path)) {
    $e = error_get_last();
    json(['ok' => false, 'error' => 'Save failed: ' . ($e['message'] ?? '')], 500);
}

$publicUrl = 'uploads/' . $name;

try {
    $pdo = db();
    // FIX: Added backticks around `current_time`
    $stmt = $pdo->prepare("INSERT INTO sync_state (room, video_url, status, `current_time`)
                           VALUES (?, ?, 'pause', 0)
                           ON DUPLICATE KEY UPDATE video_url = VALUES(video_url), status='pause', `current_time`=0");
    $stmt->execute([$room, $publicUrl]);
} catch (Exception $e) {
    json(['ok' => false, 'error' => 'DB Error: ' . $e->getMessage()], 500);
}

json(['ok' => true, 'url' => $publicUrl, 'room' => $room]);
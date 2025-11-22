<?php
// set_url.php
require __DIR__ . '/config.php';

$room = $_POST['room'] ?? '';
$url  = $_POST['url'] ?? '';

if (!$room || !$url) {
    json(['ok' => false, 'error' => 'Missing data'], 400);
}

$pdo = db();
// Upload nahi, bas Link save karo
$stmt = $pdo->prepare("INSERT INTO sync_state (room, video_url, status, `current_time`)
                       VALUES (?, ?, 'pause', 0)
                       ON DUPLICATE KEY UPDATE video_url = VALUES(video_url), status='pause', `current_time`=0");
$stmt->execute([$room, $url]);

json(['ok' => true]);
// यहां ?> टैग को छोड़ देना सबसे सुरक्षित तरीका है।
// अगर आप ?> लगाते भी हैं, तो उसके बाद कोई स्पेस या खाली लाइन नहीं होनी चाहिए।
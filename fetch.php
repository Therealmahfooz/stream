<?php
// fetch.php
require __DIR__ . '/config.php';

$room = isset($_GET['room']) ? trim($_GET['room']) : 'default';
$userId = isset($_GET['user_id']) ? trim($_GET['user_id']) : ''; // Naya: User ID

if ($room === '') $room = 'default';

$pdo = db();

// 1. ATTENDANCE LAGAO (Heartbeat)
if ($userId) {
    // Is user ka time update karo
    $track = $pdo->prepare("INSERT INTO room_users (room, user_id, last_seen) VALUES (?, ?, NOW()) 
                            ON DUPLICATE KEY UPDATE last_seen = NOW()");
    $track->execute([$room, $userId]);
}

// 2. PURANE LOGO KO HATAO (Jo 10 second se gayab hain)
// Ye garbage collection hai, taki count galat na ho
$cleanup = $pdo->prepare("DELETE FROM room_users WHERE last_seen < (NOW() - INTERVAL 10 SECOND)");
$cleanup->execute();

// 3. LIVE COUNT NIKALO
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM room_users WHERE room = ?");
$countStmt->execute([$room]);
$userCount = $countStmt->fetchColumn();

// 4. VIDEO STATE LAO (Purana kaam)
$stmt = $pdo->prepare("SELECT room, video_url, status, `current_time` FROM sync_state WHERE room=?");
$stmt->execute([$room]);
$row = $stmt->fetch();

if (!$row) {
    $init = $pdo->prepare("INSERT INTO sync_state (room, video_url, status, `current_time`)
                           VALUES (?, NULL, 'pause', 0)");
    $init->execute([$room]);
    $row = ['room' => $room, 'video_url' => null, 'status' => 'pause', 'current_time' => 0];
}

// Count ko response me jod do
$row['online_users'] = $userCount;

json(['ok' => true, 'data' => $row]);
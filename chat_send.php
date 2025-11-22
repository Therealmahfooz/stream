<?php
require __DIR__ . '/config.php';

$room = $_POST['room'] ?? '';
$user = $_POST['user'] ?? 'Anonymous';
$msg  = $_POST['message'] ?? '';

if (!$room || !$msg) json(['ok' => false], 400);

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO chats (room, username, message) VALUES (?, ?, ?)");
$stmt->execute([$room, $user, $msg]);

json(['ok' => true]);
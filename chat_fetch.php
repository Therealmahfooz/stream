<?php
require __DIR__ . '/config.php';

$room = $_GET['room'] ?? '';
$lastId = $_GET['lastId'] ?? 0;

if (!$room) json(['ok' => false], 400);

$pdo = db();
// Sirf naye messages lao (Jo Last ID se bade hain)
$stmt = $pdo->prepare("SELECT * FROM chats WHERE room = ? AND id > ? ORDER BY id ASC");
$stmt->execute([$room, $lastId]);
$messages = $stmt->fetchAll();

json(['ok' => true, 'messages' => $messages]);
<?php
// update.php
require __DIR__ . '/config.php';

$room   = $_POST['room'] ?? '';
$status = $_POST['status'] ?? '';
$time   = $_POST['time'] ?? 0;

if (!$room || !$status) json(['ok' => false, 'error' => 'Missing data'], 400);

// Status check (Optional but good practice)
if (!in_array($status, ['play', 'pause', 'seek'])) {
    json(['ok' => false, 'error' => 'Invalid status'], 400);
}

$pdo = db();

// Video State Update karo
// `current_time` कॉलम पर बैकफिक्स (backticks) लगाए गए हैं ताकि SQL में error न आए
$stmt = $pdo->prepare("UPDATE sync_state SET status = ?, `current_time` = ?, updated_at = NOW() WHERE room = ?");
$stmt->execute([$status, (float)$time, $room]);

json(['ok' => true]);
// यहां ?> टैग को छोड़ देना सबसे सुरक्षित तरीका है।
<?php
header('Content-Type: text/plain');
echo "=== SERVER SETTINGS CHECK ===\n";
echo "1. Upload Limit (upload_max_filesize): " . ini_get('upload_max_filesize') . "\n";
echo "2. Post Limit (post_max_size): " . ini_get('post_max_size') . "\n";
echo "3. Uploads Folder Exists? " . (is_dir(__DIR__ . '/uploads') ? "YES" : "NO") . "\n";
echo "4. Uploads Folder Writable? " . (is_writable(__DIR__ . '/uploads') ? "YES" : "NO") . "\n";
?>
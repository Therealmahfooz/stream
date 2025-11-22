<?php
$host = 'localhost';
$db   = 'syncwatch';
$user = 'root';
$pass = ''; // Agar koi password hai to yahan dalein

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "<h1 style='color:green'>✅ Connection SUCCESS!</h1>";
    echo "Database sahi chal raha hai.";
} catch (PDOException $e) {
    echo "<h1 style='color:red'>❌ Connection FAILED</h1>";
    echo "<h3>Asli Error Ye Hai:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
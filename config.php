<?php
// config.php (UPDATED FOR RENDER POSTGRESQL)

// Render automatically provides connection info via environment variables
// We'll use these variables later in the Render console
$DB_HOST = getenv('PGHOST') ?: 'localhost'; 
$DB_NAME = getenv('PGDATABASE') ?: 'syncwatch';
$DB_USER = getenv('PGUSER') ?: 'user';
$DB_PASS = getenv('PGPASSWORD') ?: 'password';
$DB_PORT = getenv('PGPORT') ?: '5432'; // PostgreSQL default port

function db() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT;
    static $pdo;
    if (!$pdo) {
        try {
            // NOTE: Changing 'mysql' to 'pgsql'
            $dsn = "pgsql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;user=$DB_USER;password=$DB_PASS";
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            // Log error message instead of showing database password/details
            error_log("Database connection failed: " . $e->getMessage()); 
            die(json_encode(['ok' => false, 'error' => 'Database connection failed']));
        }
    }
    return $pdo;
}
// ... rest of the functions (json) remain the same
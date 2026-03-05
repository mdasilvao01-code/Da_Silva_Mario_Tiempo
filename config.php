<?php
define('API_KEY', 'dba490d12225e0529cd634126e99af50');
define('DB_HOST', getenv('DB_HOST')     ?: 'db');
define('DB_NAME', getenv('DB_NAME')     ?: 'weather_db');
define('DB_USER', getenv('DB_USER')     ?: 'weatheruser');
define('DB_PASS', getenv('DB_PASS')     ?: 'weatherpass');

function getDB() {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

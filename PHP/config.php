<?php
define('API_KEY', 'dba490d12225e0529cd634126e99af50');
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'mariodb');
define('DB_USER', getenv('DB_USER') ?: 'mariobd');
define('DB_PASS', getenv('DB_PASS') ?: 'abcd');

function getDB() {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE TABLE IF NOT EXISTS consultas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ciudad VARCHAR(100),
        pais VARCHAR(10),
        lat DECIMAL(9,6),
        lon DECIMAL(9,6),
        tipo ENUM('actual','horas','semana'),
        fecha DATETIME DEFAULT NOW()
    )");
    return $pdo;
}

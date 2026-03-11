<?php
require_once __DIR__ . '/config.php';

class Model {

    private $pdo;

    public function __construct() {
        $this->pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS consultas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ciudad VARCHAR(100), pais VARCHAR(10),
            lat DECIMAL(9,6), lon DECIMAL(9,6),
            tipo ENUM('actual','horas','semana'),
            fecha DATETIME DEFAULT NOW()
        )");
    }

    public function guardar($ciudad, $pais, $lat, $lon, $tipo) {
        $this->pdo->prepare("INSERT INTO consultas (ciudad,pais,lat,lon,tipo) VALUES (?,?,?,?,?)")
            ->execute([$ciudad, $pais, $lat, $lon, $tipo]);
    }

    public function historial() {
        return $this->pdo->query("SELECT * FROM consultas ORDER BY fecha DESC LIMIT 100")->fetchAll();
    }

    public function total() {
        return $this->pdo->query("SELECT COUNT(*) FROM consultas")->fetchColumn();
    }

    public function porTipo() {
        return $this->pdo->query("SELECT tipo, COUNT(*) as n FROM consultas GROUP BY tipo")->fetchAll();
    }
}

CREATE DATABASE IF NOT EXISTS weather_db;
USE weather_db;

CREATE TABLE IF NOT EXISTS consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudad VARCHAR(100),
    pais VARCHAR(10),
    lat DECIMAL(9,6),
    lon DECIMAL(9,6),
    tipo ENUM('actual','horas','semana'),
    fecha DATETIME DEFAULT NOW()
);

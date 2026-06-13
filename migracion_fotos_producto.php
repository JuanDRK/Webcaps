<?php
/**
 * Script de migración: crea tabla producto_fotos para múltiples fotos por producto
 */
require_once __DIR__ . '/vendor/autoload.php';

use PDO;

$config = parse_ini_file(__DIR__ . '/config/config.ini');
$cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

try {
    $sql = "CREATE TABLE IF NOT EXISTS producto_fotos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        producto_id INT NOT NULL,
        foto VARCHAR(255) NOT NULL,
        orden INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
    )";
    $cn->exec($sql);
    echo "✅ Tabla 'producto_fotos' creada con éxito.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

<?php
/**
 * Script de migración: crea tabla cupones para descuentos
 */
require_once __DIR__ . '/vendor/autoload.php';

use PDO;

$config = parse_ini_file(__DIR__ . '/config.ini');
$cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

try {
    $sql = "CREATE TABLE IF NOT EXISTS cupones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        codigo VARCHAR(50) NOT NULL UNIQUE,
        tipo ENUM('porcentaje','fijo') DEFAULT 'porcentaje',
        valor DECIMAL(10,2) NOT NULL,
        minimo_compra DECIMAL(10,2) DEFAULT 0,
        usos_maximos INT DEFAULT NULL,
        usos_actuales INT DEFAULT 0,
        fecha_inicio DATE DEFAULT NULL,
        fecha_fin DATE DEFAULT NULL,
        activo TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $cn->exec($sql);
    echo "✅ Tabla 'cupones' creada con éxito.\n";
    
    // Insertar cupón de ejemplo
    $cn->exec("INSERT IGNORE INTO cupones (codigo, tipo, valor, minimo_compra, usos_maximos) VALUES 
        ('BIENVENIDO', 'porcentaje', 10.00, 50000, 100),
        ('DESCUENTO20', 'porcentaje', 20.00, 100000, 50)");
    echo "✅ Cupones de ejemplo creados.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

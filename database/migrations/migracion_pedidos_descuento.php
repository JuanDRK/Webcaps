<?php
/**
 * Script de migración: agrega columnas de descuento a pedidos
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;

$config = parse_ini_file(__DIR__ . '/../../config/config.ini');
$cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

try {
    $cn->exec("ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS descuento DECIMAL(10,2) DEFAULT 0");
    $cn->exec("ALTER TABLE pedidos ADD COLUMN IF NOT EXISTS cupon_id INT DEFAULT NULL");
    echo "✅ Columnas 'descuento' y 'cupon_id' agregadas a 'pedidos'.\n";
} catch (PDOException $e) {
    echo "ℹ️ " . $e->getMessage() . "\n";
}

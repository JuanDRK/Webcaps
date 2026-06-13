<?php
/**
 * Script de migración: cambia columna 'estado' de INT a VARCHAR(20)
 * Ejecutar una sola vez si el tipo anterior era incorrecto.
 */
require_once __DIR__ . '/../../vendor/autoload.php';

use PDO;

$config = parse_ini_file(__DIR__ . '/../../config/config.ini');
$cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

try {
    $cn->exec("ALTER TABLE pedidos MODIFY COLUMN estado VARCHAR(20) DEFAULT 'Pendiente'");
    echo "✅ Columna 'estado' modificada a VARCHAR(20) con éxito.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

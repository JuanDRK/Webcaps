<?php
/**
 * Script de migración: agrega columna 'estado' a tabla pedidos
 * Ejecutar una sola vez desde el navegador o CLI.
 */
require_once __DIR__ . '/vendor/autoload.php';

use PDO;

$config = parse_ini_file(__DIR__ . '/config.ini');
$cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
]);

try {
    $cn->exec("ALTER TABLE pedidos ADD COLUMN estado VARCHAR(20) DEFAULT 'Pendiente'");
    echo "✅ Columna 'estado' agregada a 'pedidos' con éxito.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false || strpos($e->getMessage(), 'already exists') !== false) {
        echo "ℹ️ La columna 'estado' ya existe. No se realizaron cambios.\n";
    } else {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

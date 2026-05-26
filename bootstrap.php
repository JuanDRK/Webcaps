<?php
/**
 * Bootstrap de la aplicación Webcaps.
 * Carga Composer, define constantes de rutas y configura el entorno.
 */

// Evitar acceso directo
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__FILE__));
}

// Autoload de Composer
require_once BASE_PATH . '/vendor/autoload.php';

// Helpers de rutas
require_once BASE_PATH . '/src/Helpers/Paths.php';

// Cargar variables de entorno si existe .env
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Zona horaria
date_default_timezone_set('America/Bogota');

// Reporte de errores en desarrollo
if (!empty($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

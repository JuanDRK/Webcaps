<?php

namespace capsweb\Helpers;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = parse_ini_file(self::configPath('config.ini'));
            if (!$config) {
                throw new \RuntimeException('No se pudo cargar config.ini');
            }
            try {
                self::$instance = new PDO($config['dns'], $config['usuario'], $config['clave'], [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log('DB Connection failed: ' . $e->getMessage());
                throw new \RuntimeException('Error de conexión a la base de datos.');
            }
        }
        return self::$instance;
    }

    private static function configPath(string $filename): string
    {
        return dirname(__DIR__, 2) . '/config/' . $filename;
    }
}

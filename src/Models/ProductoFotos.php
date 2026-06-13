<?php
namespace capsweb;
use PDO;

require_once __DIR__ . '/../Helpers/Paths.php';

class ProductoFotos
{
    private $cn;

    public function __construct()
    {
        $config = parse_ini_file(config_path('config.ini'));
        $this->cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]);
    }

    public function guardar(int $productoId, string $foto, int $orden = 0): bool
    {
        $sql = "INSERT INTO producto_fotos (producto_id, foto, orden) VALUES (:producto_id, :foto, :orden)";
        $resultado = $this->cn->prepare($sql);
        return $resultado->execute([
            ':producto_id' => $productoId,
            ':foto' => $foto,
            ':orden' => $orden
        ]);
    }

    public function obtenerPorProducto(int $productoId): array
    {
        $sql = "SELECT * FROM producto_fotos WHERE producto_id = :producto_id ORDER BY orden ASC";
        $resultado = $this->cn->prepare($sql);
        $resultado->execute([':producto_id' => $productoId]);
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM producto_fotos WHERE id = :id";
        $resultado = $this->cn->prepare($sql);
        return $resultado->execute([':id' => $id]);
    }
}

<?php
namespace capsweb;
use PDO;

require_once __DIR__ . '/../Helpers/Paths.php';

class Cupones
{
    private $cn;

    public function __construct()
    {
        $config = parse_ini_file(config_path('config.ini'));
        $this->cn = new PDO($config['dns'], $config['usuario'], $config['clave'], [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ]);
    }

    public function validar(string $codigo, float $totalCompra): ?array
    {
        $sql = "SELECT * FROM cupones 
                WHERE codigo = :codigo 
                AND activo = 1 
                AND (usos_maximos IS NULL OR usos_actuales < usos_maximos)
                AND (fecha_inicio IS NULL OR fecha_inicio <= CURDATE())
                AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())
                AND (minimo_compra = 0 OR minimo_compra <= :total)
                LIMIT 1";
        
        $resultado = $this->cn->prepare($sql);
        $resultado->execute([
            ':codigo' => $codigo,
            ':total' => $totalCompra
        ]);
        
        $cupon = $resultado->fetch(PDO::FETCH_ASSOC);
        return $cupon ?: null;
    }

    public function calcularDescuento(array $cupon, float $total): float
    {
        if ($cupon['tipo'] === 'porcentaje') {
            return $total * ($cupon['valor'] / 100);
        } else {
            return min($cupon['valor'], $total);
        }
    }

    public function usarCupon(int $id): bool
    {
        $sql = "UPDATE cupones SET usos_actuales = usos_actuales + 1 WHERE id = :id";
        $resultado = $this->cn->prepare($sql);
        return $resultado->execute([':id' => $id]);
    }

    public function mostrar(): array
    {
        $sql = "SELECT * FROM cupones ORDER BY id DESC";
        $resultado = $this->cn->prepare($sql);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar(array $params): bool
    {
        $sql = "INSERT INTO cupones (codigo, tipo, valor, minimo_compra, usos_maximos, fecha_inicio, fecha_fin) 
                VALUES (:codigo, :tipo, :valor, :minimo_compra, :usos_maximos, :fecha_inicio, :fecha_fin)";
        $resultado = $this->cn->prepare($sql);
        return $resultado->execute([
            ':codigo' => $params['codigo'],
            ':tipo' => $params['tipo'],
            ':valor' => $params['valor'],
            ':minimo_compra' => $params['minimo_compra'] ?? 0,
            ':usos_maximos' => $params['usos_maximos'] ?? null,
            ':fecha_inicio' => $params['fecha_inicio'] ?: null,
            ':fecha_fin' => $params['fecha_fin'] ?: null
        ]);
    }

    public function cambiarEstado(int $id, bool $activo): bool
    {
        $sql = "UPDATE cupones SET activo = :activo WHERE id = :id";
        $resultado = $this->cn->prepare($sql);
        return $resultado->execute([':id' => $id, ':activo' => $activo ? 1 : 0]);
    }
}

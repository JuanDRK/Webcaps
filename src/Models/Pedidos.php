<?php

namespace capsweb;

use PHPMailer\PHPMailer\PHPMailer;

use PDO;

require_once __DIR__ . '/../Helpers/Paths.php';

class pedidos
{
    private $config;
    private $cn = null;

    public function __construct()
    {
        $this->config = parse_ini_file(config_path('config.ini'));

        $this->cn = new PDO($this->config['dns'], $this->config['usuario'], $this->config['clave'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ));
    }

    public function registrar(array $_params): string|false
    {
        $sql = "INSERT INTO `pedidos`(`cliente_id`, `total`, `fecha`, `estado`) 
        VALUES (:cliente_id,:total,:fecha,:estado)";

        $resultado = $this->cn->prepare($sql);

        $_array = array(
            ":cliente_id" => $_params['cliente_id'],
            ":total" => $_params['total'],
            ":fecha" => $_params['fecha'],
            ":estado" => $_params['estado'] ?? 'Pendiente',
        );

        if ($resultado->execute($_array))
            return $this->cn->lastInsertId();

        return false;
    }

    //esta funcion muestra los datos del cliente y el producto
    public function mostrar(): array|false
    {
        $sql = "SELECT p.id, nombre, apellidos, email, total, fecha, estado FROM pedidos p 
        INNER JOIN clientes c ON p.cliente_id = c.id ORDER BY p.id DESC";

        $resultado = $this->cn->prepare($sql);

        if ($resultado->execute())
            return  $resultado->fetchAll();

        return false;
    }

    //esta funcion muestra los ultimos pedidos realizados con un limite de 10
    public function mostrarUltimos(): array|false
    {
        $sql = "SELECT p.id, nombre, apellidos, email, total, fecha, estado FROM pedidos p 
        INNER JOIN clientes c ON p.cliente_id = c.id ORDER BY p.id DESC LIMIT 20";

        $resultado = $this->cn->prepare($sql);

        if ($resultado->execute())
            return  $resultado->fetchAll();

        return false;
    }

    //muestra los datos del pedido y del respectivo cliente segun el id del pedido
    public function mostrarPorId(int $id): array|false
    {
        $sql = "SELECT p.id, nombre, apellidos, email, total, fecha, direccion, estado FROM pedidos p 
        INNER JOIN clientes c ON p.cliente_id = c.id WHERE p.id = :id";

        $resultado = $this->cn->prepare($sql);

        $_array = array(
            ':id' => $id
        );

        if ($resultado->execute($_array))
            return  $resultado->fetch();

        return false;
    }

    public function mostrarDetallePorIdPedido(int $id): array|false
    {
        $sql = "SELECT 
                dp.id,
                pe.referencia,
                dp.precio,
                dp.cantidad,
                pe.foto,
                dp.talla               
                FROM detalle_pedidos dp
                INNER JOIN productos pe ON pe.id= dp.producto_id
                WHERE dp.pedido_id = :id";

        $resultado = $this->cn->prepare($sql);

        $_array = array(
            ':id' => $id
        );

        if ($resultado->execute($_array))
            return  $resultado->fetchAll();

        return false;
    }

    //aqui se enviara a la base de datos todos los detalles del pedidio hecho por un cliente
    public function cambiarEstado(int $id, string $estado): bool
    {
        $sql = "UPDATE `pedidos` SET `estado` = :estado WHERE `id` = :id";
        $resultado = $this->cn->prepare($sql);
        $_array = array(
            ":id" => $id,
            ":estado" => $estado
        );
        return $resultado->execute($_array);
    }

    public function registrarDetalle(array $_params): bool
    {
        $sql = "INSERT INTO `detalle_pedidos`(`pedido_id`, `producto_id`, `precio`, `cantidad`, `talla`) 
            VALUES (:pedido_id, :producto_id, :precio, :cantidad, :talla)";

        $resultado = $this->cn->prepare($sql);

        $_array = array(
            ":pedido_id" => $_params['pedido_id'],
            ":producto_id" => $_params['producto_id'],
            ":precio" => $_params['precio'],
            ":cantidad" => $_params['cantidad'],
            ":talla" => isset($_params['talla']) ? $_params['talla'] : null,
        );

        if ($resultado->execute($_array)) {
            $id = $_params['pedido_id'];
            return true;
        }
        return false;
    }
}

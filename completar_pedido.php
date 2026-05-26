<?php

use PHPMailer\PHPMailer\PHPMailer;

session_start();
require 'carro/funcionesCarrito.php';
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validarCsrf($_POST['csrf_token'])) {
        header('Location: index.php');
        exit;
    }

    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        header('Location: index.php');
        exit;
    }

    require 'src/Services/EmailService.php';

    try {
        $cliente = new capsweb\clientes;
        $pedido = new capsweb\pedidos;
        $producto = new capsweb\Productos;

        $clienteParams = array(
            'nombre' => $_POST['nombre'],
            'apellidos' => $_POST['apellidos'],
            'email' => $_POST['email'],
            'telefono' => $_POST['telefono'],
            'direccion' => $_POST['direccion']
        );

        $cliente_id = $cliente->registrar($clienteParams);
        if (!$cliente_id) {
            throw new Exception('No se pudo registrar el cliente.');
        }

        $pedidoParams = array(
            'cliente_id' => $cliente_id,
            'total' => calcularTotal(),
            'fecha' => date('Y-m-d')
        );

        $pedido_id = $pedido->registrar($pedidoParams);
        if (!$pedido_id) {
            throw new Exception('No se pudo registrar el pedido.');
        }

        foreach ($_SESSION['carrito'] as $value) {
            $detalleParams = array(
                "pedido_id" => $pedido_id,
                "producto_id" => $value['id'],
                "precio" => $value['precio'],
                "cantidad" => $value['cantidad'],
                "talla" => $value['talla'],
            );
            if (!$pedido->registrarDetalle($detalleParams)) {
                throw new Exception('Error al registrar el detalle del pedido.');
            }

            if (!$producto->actualizarStock($value['id'], $value['cantidad'])) {
                throw new Exception('Stock insuficiente para el producto ID: ' . (int) $value['id']);
            }
        }

        sendEmailPedido($pedido_id);

        $_SESSION['carrito'] = [];
        $_SESSION['pedido_ok'] = true;
        header('Location: /index.php?pedido=ok');
        exit;

    } catch (Exception $e) {
        error_log('[Webcaps Error] completar_pedido.php: ' . $e->getMessage());
        header('Location: index.php?error=pedido');
        exit;
    }
}

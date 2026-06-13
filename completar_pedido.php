<?php

use PHPMailer\PHPMailer\PHPMailer;
use capsweb\Services\Notificaciones;

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

        // Calcular total con descuento si hay cupón
        $subtotal = calcularTotal();
        $descuento = 0;
        $cuponId = null;
        
        if (!empty($_POST['cupon'])) {
            $cupones = new capsweb\Cupones();
            $cupon = $cupones->validar($_POST['cupon'], $subtotal);
            if ($cupon) {
                $descuento = $cupones->calcularDescuento($cupon, $subtotal);
                $cuponId = $cupon['id'];
                $cupones->usarCupon($cupon['id']);
            }
        }
        
        $totalFinal = $subtotal - $descuento;
        
        $pedidoParams = array(
            'cliente_id' => $cliente_id,
            'total' => $totalFinal,
            'fecha' => date('Y-m-d'),
            'descuento' => $descuento,
            'cupon_id' => $cuponId
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

        // Enviar notificaciones
        $notif = new Notificaciones();
        $infoPedido = $pedido->mostrarPorId($pedido_id);
        $itemsPedido = $pedido->mostrarDetallePorIdPedido($pedido_id);
        $infoCliente = $cliente->mostrarPorId($cliente_id);
        
        $notif->notificarNuevoPedido(
            ['id' => $pedido_id, 'total' => $pedidoParams['total']],
            $infoCliente,
            $itemsPedido
        );
        $notif->notificarClientePedido(
            ['id' => $pedido_id, 'total' => $pedidoParams['total']],
            $infoCliente
        );

        // Mantener compatibilidad con EmailService existente
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

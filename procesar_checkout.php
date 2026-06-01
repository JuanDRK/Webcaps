<?php
session_start();
require 'carro/funcionesCarrito.php';
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: finalizar.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !validarCsrf($_POST['csrf_token'])) {
    header('Location: index.php');
    exit;
}

// Guardar datos del cliente en sesión
$_SESSION['cliente_pendiente'] = [
    'nombre' => $_POST['nombre'] ?? '',
    'apellidos' => $_POST['apellidos'] ?? '',
    'email' => $_POST['email'] ?? '',
    'telefono' => $_POST['telefono'] ?? '',
    'direccion' => $_POST['direccion'] ?? ''
];

// Guardar descuento si hay cupón
$_SESSION['descuento_pendiente'] = (float)($_POST['descuento'] ?? 0);

// Si hay cupón, guardar el código para usarlo después
if (!empty($_POST['cupon'])) {
    $_SESSION['cupon_pendiente'] = $_POST['cupon'];
    // Validar y obtener el ID del cupón
    $cupones = new capsweb\Cupones();
    $cupon = $cupones->validar($_POST['cupon'], calcularTotal());
    if ($cupon) {
        $_SESSION['cupon_pendiente'] = $cupon['id'];
    }
}

// Determinar método de pago
$metodoPago = $_POST['metodo_pago'] ?? 'contraentrega';

if ($metodoPago === 'online') {
    // Redirigir a página de pago online
    header('Location: pago_online.php');
    exit;
} else {
    // Procesar pedido contra entrega (usar completar_pedido.php)
    header('Location: completar_pedido.php');
    exit;
}

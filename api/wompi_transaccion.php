<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['carrito'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Carrito vacío']);
    exit;
}

require __DIR__ . '/../carro/funcionesCarrito.php';

$data = json_decode(file_get_contents('php://input'), true);

// Guardar datos del cliente en sesión si no existen
if (empty($_SESSION['cliente_pendiente'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos de cliente requeridos. Complete el formulario de envío primero.']);
    exit;
}

try {
    $wompi = new capsweb\Services\WompiService();
    
    // Crear cliente y pedido primero
    $cliente = new capsweb\clientes;
    $pedido = new capsweb\pedidos;
    $producto = new capsweb\Productos;
    
    $clienteData = $_SESSION['cliente_pendiente'];
    $cliente_id = $cliente->registrar($clienteData);
    
    if (!$cliente_id) {
        throw new Exception('Error al registrar cliente');
    }
    
    $subtotal = calcularTotal();
    $descuento = $_SESSION['descuento_pendiente'] ?? 0;
    $total = $subtotal - $descuento;
    
    // Procesar pago con Wompi
    $resultadoPago = $wompi->crearTransaccion([
        'acceptance_token' => $data['acceptance_token'],
        'amount' => $total,
        'email' => $clienteData['email'],
        'card_token' => $data['token'],
        'installments' => $data['installments'] ?? 1,
        'reference' => 'PED-' . time() . '-' . $cliente_id,
        'phone' => $clienteData['telefono'],
        'name' => $clienteData['nombre'] . ' ' . $clienteData['apellidos']
    ]);
    
    // Verificar si el pago fue exitoso
    if (isset($resultadoPago['data']['status']) && $resultadoPago['data']['status'] === 'APPROVED') {
        // Registrar pedido
        $pedido_id = $pedido->registrar([
            'cliente_id' => $cliente_id,
            'total' => $total,
            'fecha' => date('Y-m-d'),
            'estado' => 'Pagado',
            'descuento' => $descuento,
            'cupon_id' => $_SESSION['cupon_pendiente'] ?? null,
            'wompi_transaction_id' => $resultadoPago['data']['id'] ?? null
        ]);
        
        // Registrar detalles y actualizar stock
        foreach ($_SESSION['carrito'] as $value) {
            $pedido->registrarDetalle([
                'pedido_id' => $pedido_id,
                'producto_id' => $value['id'],
                'precio' => $value['precio'],
                'cantidad' => $value['cantidad'],
                'talla' => $value['talla']
            ]);
            $producto->actualizarStock($value['id'], $value['cantidad']);
        }
        
        // Limpiar carrito y datos pendientes
        $_SESSION['carrito'] = [];
        unset($_SESSION['cliente_pendiente'], $_SESSION['descuento_pendiente'], $_SESSION['cupon_pendiente']);
        
        echo json_encode([
            'success' => true,
            'pedido_id' => $pedido_id,
            'transaction_id' => $resultadoPago['data']['id']
        ]);
    } else {
        // Pago fallido o pendiente
        echo json_encode([
            'success' => false,
            'message' => $resultadoPago['data']['status_message'] ?? 'Pago no aprobado',
            'status' => $resultadoPago['data']['status'] ?? 'ERROR'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

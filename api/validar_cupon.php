<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

$codigo = $_GET['codigo'] ?? '';
$total = (float)($_GET['total'] ?? 0);

if (empty($codigo)) {
    echo json_encode(['valido' => false, 'mensaje' => 'Código requerido']);
    exit;
}

try {
    $cupones = new capsweb\Cupones();
    $cupon = $cupones->validar($codigo, $total);
    
    if (!$cupon) {
        echo json_encode(['valido' => false, 'mensaje' => 'Cupón inválido o no aplicable']);
        exit;
    }
    
    $descuento = $cupones->calcularDescuento($cupon, $total);
    
    echo json_encode([
        'valido' => true,
        'codigo' => $cupon['codigo'],
        'tipo' => $cupon['tipo'],
        'valor' => (float)$cupon['valor'],
        'descuento' => $descuento,
        'total_final' => $total - $descuento
    ]);
} catch (Exception $e) {
    echo json_encode(['valido' => false, 'mensaje' => 'Error del sistema']);
}

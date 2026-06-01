<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';

$pedido = new capsweb\pedidos;
$info_pedido = $pedido->mostrar();

// Headers para descarga CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="pedidos_' . date('Y-m-d_H-i-s') . '.csv"');

$output = fopen('php://output', 'w');

// BOM para Excel
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Headers
fputcsv($output, ['ID', 'Fecha', 'Cliente', 'Email', 'Teléfono', 'Dirección', 'Estado', 'Total']);

// Datos
foreach ($info_pedido as $p) {
    fputcsv($output, [
        $p['id'] ?? '',
        $p['fecha'] ?? '',
        ($p['nombre'] ?? '') . ' ' . ($p['apellidos'] ?? ''),
        $p['email'] ?? '',
        $p['telefono'] ?? '',
        $p['direccion'] ?? '',
        $p['estado'] ?? 'Pendiente',
        $p['total'] ?? 0
    ]);
}

fclose($output);

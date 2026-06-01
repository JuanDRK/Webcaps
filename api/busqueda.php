<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

$query = $_GET['q'] ?? '';
if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$producto = new capsweb\Productos;
$productos = $producto->mostrar();

$sugerencias = [];
foreach ($productos as $p) {
    $referencia = strtolower($p['referencia'] ?? '');
    $categoria = strtolower($p['categoria'] ?? '');
    $q = strtolower($query);
    
    if (strpos($referencia, $q) !== false || strpos($categoria, $q) !== false) {
        $sugerencias[] = [
            'id' => $p['id'],
            'referencia' => $p['referencia'],
            'categoria' => $p['categoria'],
            'precio' => $p['precio'],
            'foto' => $p['foto']
        ];
    }
    
    if (count($sugerencias) >= 8) break;
}

echo json_encode($sugerencias);

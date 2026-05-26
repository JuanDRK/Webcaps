<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../vendor/autoload.php';

$producto = new capsweb\Productos;
$categoria = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$busqueda = isset($_GET['q']) ? trim($_GET['q']) : '';

try {
    $resultados = $producto->mostrar();

    // Filtrar por categoría
    if ($categoria > 0) {
        $resultados = array_filter($resultados, fn($p) => (int)$p['categoria_id'] === $categoria);
    }

    // Búsqueda full-text simple
    if ($busqueda !== '') {
        $q = strtolower($busqueda);
        $resultados = array_filter($resultados, fn($p) =>
            str_contains(strtolower($p['referencia']), $q) ||
            str_contains(strtolower($p['descripcion'] ?? ''), $q)
        );
    }

    $productos = array_values(array_map(fn($p) => [
        'id' => (int)$p['id'],
        'referencia' => $p['referencia'],
        'precio' => (float)$p['precio'],
        'stock' => (int)$p['stock'],
        'foto' => $p['foto'],
        'tallas' => explode(',', $p['tallas']),
        'categoria_id' => (int)$p['categoria_id'],
        'estado' => (int)$p['estado'],
        'fecha' => $p['fecha'],
    ], $resultados));

    echo json_encode(['status' => 'ok', 'count' => count($productos), 'data' => $productos], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

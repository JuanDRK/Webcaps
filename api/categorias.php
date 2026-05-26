<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../vendor/autoload.php';

$categoria = new capsweb\Categorias;

try {
    $resultados = $categoria->mostrar();
    $cats = array_values(array_map(fn($c) => [
        'id' => (int)$c['id'],
        'nombre' => $c['categoria'],
    ], $resultados));

    echo json_encode(['status' => 'ok', 'count' => count($cats), 'data' => $cats], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

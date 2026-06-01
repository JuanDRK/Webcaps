<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $wompi = new capsweb\Services\WompiService();
    $token = $wompi->tokenizarTarjeta($data);
    
    if ($token) {
        echo json_encode(['token' => $token]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No se pudo tokenizar la tarjeta']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

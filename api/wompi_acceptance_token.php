<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

try {
    $wompi = new capsweb\Services\WompiService();
    $token = $wompi->obtenerAcceptanceToken();
    echo json_encode(['token' => $token]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

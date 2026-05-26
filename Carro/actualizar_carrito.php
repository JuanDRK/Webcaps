<?php
session_start();
require __DIR__ . '/funcionesCarrito.php';
require __DIR__ . '/../vendor/autoload.php';

use capsweb\Helpers\Csrf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !Csrf::validate($_POST['csrf_token'])) {
        header('Location: index.php');
        exit;
    }

    //obtengo el id del producto segun el carrito
    $id = $_POST['id'];
    //obtengo la cantidad del producto segun el carrito
    $cantidad = $_POST['cantidad'];
    // Obtén la talla del carrito que selecciono el usuario
    $talla = $_POST['tallaTomada'];

    if (is_numeric($cantidad)) {
        if (array_key_exists($id, $_SESSION['carrito'])) {
            // Actualiza la cantidad del producto en el carrito
            actualizarProducto($id, $cantidad);
            // Actualiza la talla del producto en el carrito
            $_SESSION['carrito'][$id]['talla'] = $talla;
        }
    }
    header('Location: index.php');
    exit;
}

<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use capsweb\Helpers\Csrf;

// Si viene POST para vaciar todo el carrito, procesar primero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminartodo') {
    if (!isset($_POST['csrf_token']) || !Csrf::validate($_POST['csrf_token'])) {
        header('Location: index.php');
        exit;
    }
    if (isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }
    header('Location: index.php');
    exit;
}

// Eliminación individual por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int) $_GET['id'];

if (isset($_SESSION['carrito']) && array_key_exists($id, $_SESSION['carrito'])) {
    unset($_SESSION['carrito'][$id]);
    header('Location: index.php');
    exit;
}

header('Location: ../index.php');
exit;

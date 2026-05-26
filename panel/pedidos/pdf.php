<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { exit('ID inválido'); }

$pedido = new capsweb\pedidos;
$info_pedido = $pedido->mostrarPorId($id);
$info_detalle_pedido = $pedido->mostrarDetallePorIdPedido($id);

if (empty($info_pedido)) { exit('Pedido no encontrado'); }

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$html = '<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
.header { text-align: center; margin-bottom: 20px; }
.header h1 { margin: 0; font-size: 22px; color: #111; }
.header p { margin: 4px 0 0; color: #666; font-size: 11px; }
.section { margin-bottom: 20px; }
.section h2 { font-size: 14px; color: #111; border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 8px; }
.info-grid { display: table; width: 100%; }
.info-row { display: table-row; }
.info-cell { display: table-cell; padding: 4px 0; width: 50%; }
.label { color: #666; font-size: 10px; text-transform: uppercase; }
.value { font-weight: bold; font-size: 12px; }
table { width: 100%; border-collapse: collapse; margin-top: 8px; }
th, td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
th { background: #f9f9f9; font-size: 11px; color: #555; text-transform: uppercase; }
.total { text-align: right; margin-top: 12px; font-size: 16px; font-weight: bold; }
</style>
</head>
<body>
<div class="header">
  <h1>CAPS</h1>
  <p>Orden de pedido #' . $id . '</p>
</div>

<div class="section">
  <h2>Información del cliente</h2>
  <div class="info-grid">
    <div class="info-row">
      <div class="info-cell"><span class="label">Nombre</span><br><span class="value">' . htmlspecialchars($info_pedido['nombre'] . ' ' . $info_pedido['apellidos']) . '</span></div>
      <div class="info-cell"><span class="label">Email</span><br><span class="value">' . htmlspecialchars($info_pedido['email']) . '</span></div>
    </div>
    <div class="info-row">
      <div class="info-cell"><span class="label">Teléfono</span><br><span class="value">' . htmlspecialchars($info_pedido['telefono'] ?? '') . '</span></div>
      <div class="info-cell"><span class="label">Dirección</span><br><span class="value">' . htmlspecialchars($info_pedido['direccion']) . '</span></div>
    </div>
    <div class="info-row">
      <div class="info-cell"><span class="label">Fecha</span><br><span class="value">' . htmlspecialchars($info_pedido['fecha']) . '</span></div>
    </div>
  </div>
</div>

<div class="section">
  <h2>Productos comprados</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Referencia</th>
        <th>Precio unitario</th>
        <th>Talla</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>';

$c = 0;
foreach ($info_detalle_pedido as $item) {
    $c++;
    $subtotal = $item['precio'] * $item['cantidad'];
    $html .= '<tr>
        <td>' . $c . '</td>
        <td>' . htmlspecialchars($item['referencia']) . '</td>
        <td>$' . number_format($item['precio'], 0, ',', '.') . '</td>
        <td>' . htmlspecialchars($item['talla']) . '</td>
        <td>' . (int)$item['cantidad'] . '</td>
        <td>$' . number_format($subtotal, 0, ',', '.') . '</td>
    </tr>';
}

$html .= '</tbody>
  </table>
  <div class="total">Total: $' . number_format($info_pedido['total'], 0, ',', '.') . ' COP</div>
</div>

<div style="text-align:center; margin-top:40px; color:#999; font-size:10px;">
  Generado el ' . date('Y-m-d H:i') . ' | CAPS Palmira
</div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('pedido_' . $id . '.pdf', ['Attachment' => true]);

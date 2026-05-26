<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pedido = new capsweb\pedidos;

// Cambiar estado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['estado'])) {
    $pedido->cambiarEstado($id, $_POST['estado']);
    header('Location: ver.php?id=' . $id);
    exit;
}

$info_pedido = $pedido->mostrarPorId($id);
$info_detalle_pedido = $pedido->mostrarDetallePorIdPedido($id);

$estadoBadge = [
    'Pendiente'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'Enviado'    => 'bg-blue-50 text-blue-700 border-blue-200',
    'Completado' => 'bg-green-50 text-green-700 border-green-200',
    'Cancelado'  => 'bg-red-50 text-red-700 border-red-200',
];
$estados = ['Pendiente','Enviado','Completado','Cancelado'];
$est = $info_pedido['estado'] ?? 'Pendiente';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedido #<?= $id ?> | CAPS Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>tailwind.config={theme:{extend:{colors:{brand:{900:'#0f0f0f',accent:'#e11d48'}}}}}</script>
  <style>@media print { nav, .no-print { display: none !important; } }</style>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">
<?php $activePage = 'pedidos'; include __DIR__ . '/../templates/sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-8 overflow-y-auto">
  <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8 no-print">
    <a href="index.php" class="inline-flex items-center text-gray-500 hover:text-brand-accent transition text-sm"><i class="fa-solid fa-arrow-left mr-2"></i>Volver a pedidos</a>
    <h1 class="text-2xl font-black text-brand-900 sm:ml-4">Pedido #<?= $id ?></h1>
    <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold border <?= $estadoBadge[$est] ?? 'bg-gray-50 text-gray-700 border-gray-200' ?>"><?= $est ?></span>
    <div class="sm:ml-auto flex gap-2">
      <a href="pdf.php?id=<?= $id ?>" class="bg-brand-accent text-white px-4 py-2 rounded-lg text-sm hover:bg-rose-700 transition no-print flex items-center gap-2" download><i class="fa-solid fa-file-pdf"></i>Descargar PDF</a>
      <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition no-print flex items-center gap-2"><i class="fa-solid fa-print"></i>Imprimir</button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cliente -->
    <div class="bg-white rounded-xl border border-gray-100 p-6 h-fit">
      <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-full bg-brand-900 text-white flex items-center justify-center font-bold text-sm">
          <?= strtoupper(substr($info_pedido['nombre'] ?? 'U', 0, 1)) ?>
        </div>
        <div>
          <h2 class="font-bold text-brand-900"><?= htmlspecialchars(($info_pedido['nombre'] ?? '') . ' ' . ($info_pedido['apellidos'] ?? '')) ?></h2>
          <p class="text-xs text-gray-500">Cliente</p>
        </div>
      </div>
      <div class="space-y-4 text-sm">
        <div class="flex items-start gap-3">
          <i class="fa-regular fa-envelope text-gray-400 mt-0.5"></i>
          <div><p class="text-gray-500 text-xs">Email</p><p class="font-medium text-brand-900"><?= htmlspecialchars($info_pedido['email'] ?? '') ?></p></div>
        </div>
        <div class="flex items-start gap-3">
          <i class="fa-solid fa-phone text-gray-400 mt-0.5"></i>
          <div><p class="text-gray-500 text-xs">Teléfono</p><p class="font-medium text-brand-900"><?= htmlspecialchars($info_pedido['telefono'] ?? '—') ?></p></div>
        </div>
        <div class="flex items-start gap-3">
          <i class="fa-solid fa-location-dot text-gray-400 mt-0.5"></i>
          <div><p class="text-gray-500 text-xs">Dirección</p><p class="font-medium text-brand-900"><?= htmlspecialchars($info_pedido['direccion'] ?? '') ?></p></div>
        </div>
        <div class="flex items-start gap-3">
          <i class="fa-regular fa-calendar text-gray-400 mt-0.5"></i>
          <div><p class="text-gray-500 text-xs">Fecha del pedido</p><p class="font-medium text-brand-900"><?= htmlspecialchars($info_pedido['fecha'] ?? '') ?></p></div>
        </div>
      </div>
      <div class="border-t border-gray-100 mt-5 pt-5">
        <p class="text-gray-500 text-xs mb-1">Total del pedido</p>
        <p class="text-3xl font-black text-brand-accent">$<?= number_format($info_pedido['total'] ?? 0, 0, ',', '.') ?> <span class="text-sm text-gray-400 font-normal">COP</span></p>
      </div>
      <div class="border-t border-gray-100 mt-4 pt-4 no-print">
        <form method="POST" action="" class="flex gap-2">
          <select name="estado" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
            <?php foreach ($estados as $e) { ?>
              <option value="<?= $e ?>" <?= $est === $e ? 'selected' : '' ?>><?= $e ?></option>
            <?php } ?>
          </select>
          <button type="submit" class="bg-brand-900 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition">Cambiar</button>
        </form>
      </div>
    </div>

    <!-- Productos -->
    <div class="lg:col-span-2">
      <h2 class="font-bold text-brand-900 mb-4">Productos del pedido</h2>
      <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <?php if (count($info_detalle_pedido) > 0) { $c = 0; foreach ($info_detalle_pedido as $item) { $c++; $total = $item['precio'] * $item['cantidad']; ?>
          <div class="flex items-center gap-4 p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
            <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold flex-shrink-0"><?= $c ?></span>
            <div class="w-12 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
              <?php $fotoPath = __DIR__ . '/../../public/upload/' . $item['foto']; if (file_exists($fotoPath)) { ?>
                <img src="../../upload/<?= htmlspecialchars($item['foto']) ?>" class="w-full h-full object-cover">
              <?php } else { ?><div class="w-full h-full flex items-center justify-center text-gray-400 text-[10px]">SIN FOTO</div><?php } ?>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-brand-900 text-sm truncate"><?= htmlspecialchars($item['referencia']) ?></p>
              <div class="flex items-center gap-2 mt-1">
                <span class="inline-block px-2 py-0.5 rounded bg-brand-900 text-white text-[10px] font-bold">Talla <?= htmlspecialchars($item['talla'] ?? '—') ?></span>
                <span class="text-xs text-gray-500">Cant: <?= (int)$item['cantidad'] ?></span>
              </div>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="font-bold text-brand-900">$<?= number_format($total, 0, ',', '.') ?></p>
              <p class="text-xs text-gray-400">$<?= number_format($item['precio'], 0, ',', '.') ?> c/u</p>
            </div>
          </div>
        <?php }} else { ?>
          <div class="p-8 text-center text-gray-400">No hay productos en este pedido</div>
        <?php } ?>
      </div>
    </div>
  </div>
</main>
</div>
</body>
</html>
<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';
$pedido = new capsweb\pedidos;
$info_pedido = $pedido->mostrar();

$estadosDisponibles = ['Pendiente','Enviado','Completado','Cancelado'];
$estadoFiltro = $_GET['estado'] ?? '';

// Filtros
$cliente = $_GET['cliente'] ?? '';
$email   = $_GET['email'] ?? '';
$fecha   = $_GET['fecha'] ?? '';
$minTotal = $_GET['min'] ?? '';
$maxTotal = $_GET['max'] ?? '';

$resultadosFiltrados = [];
foreach ($info_pedido as $item) {
  $nombreCompleto = ($item['nombre'] ?? '') . ' ' . ($item['apellidos'] ?? '');
  if ((empty($cliente) || stripos($nombreCompleto, $cliente) !== false)
    && (empty($email) || stripos($item['email'] ?? '', $email) !== false)
    && (empty($fecha) || stripos($item['fecha'] ?? '', $fecha) !== false)
    && (empty($minTotal) || (float)($item['total'] ?? 0) >= (float)$minTotal)
    && (empty($maxTotal) || (float)($item['total'] ?? 0) <= (float)$maxTotal)
    && (empty($estadoFiltro) || ($item['estado'] ?? 'Pendiente') === $estadoFiltro)) {
    $resultadosFiltrados[] = $item;
  }
}

$porPagina = 8;
$total = count($resultadosFiltrados);
$paginas = ceil($total / $porPagina);
$pagina = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$inicio = ($pagina - 1) * $porPagina;
$lista = array_slice($resultadosFiltrados, $inicio, $porPagina);

function estadoBadge(string $estado): string {
  $map = [
    'Pendiente'  => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'Enviado'    => 'bg-blue-50 text-blue-700 border-blue-200',
    'Completado' => 'bg-green-50 text-green-700 border-green-200',
    'Cancelado'  => 'bg-red-50 text-red-700 border-red-200',
  ];
  return $map[$estado] ?? 'bg-gray-50 text-gray-700 border-gray-200';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin — Pedidos | CAPS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>tailwind.config={theme:{extend:{colors:{brand:{900:'#0f0f0f',accent:'#e11d48'}}}}}</script>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">
<?php $activePage = 'pedidos'; include __DIR__ . '/../templates/sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-8 overflow-y-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-black text-brand-900">Pedidos</h1>
      <p class="text-gray-500 text-sm mt-1"><?= $total ?> pedido(s) en total</p>
    </div>
  </div>

  <!-- Estado Tabs -->
  <div class="flex flex-wrap gap-2 mb-4">
    <a href="index.php" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= empty($estadoFiltro) ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>">Todos</a>
    <?php foreach ($estadosDisponibles as $e) { ?>
      <a href="?estado=<?= urlencode($e) ?>" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= $estadoFiltro === $e ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>"><?= $e ?></a>
    <?php } ?>
  </div>

  <!-- Filters compact -->
  <form class="flex flex-wrap gap-2 mb-4">
    <input type="hidden" name="estado" value="<?= htmlspecialchars($estadoFiltro) ?>">
    <input type="text" name="cliente" value="<?= htmlspecialchars($cliente) ?>" placeholder="Cliente" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-brand-accent w-40">
    <input type="text" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-brand-accent w-40">
    <input type="date" name="fecha" value="<?= htmlspecialchars($fecha) ?>" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-brand-accent">
    <input type="number" name="min" value="<?= htmlspecialchars($minTotal) ?>" placeholder="Min $" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-brand-accent w-24">
    <input type="number" name="max" value="<?= htmlspecialchars($maxTotal) ?>" placeholder="Max $" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm outline-none focus:border-brand-accent w-24">
    <button type="submit" class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-gray-700 transition">Filtrar</button>
    <a href="index.php" class="bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-200 transition">Limpiar</a>
  </form>

  <?php if (count($lista) > 0) { ?>
    <div class="grid grid-cols-1 gap-3">
      <?php foreach ($lista as $item) { $est = $item['estado'] ?? 'Pendiente'; ?>
        <a href="ver.php?id=<?= (int)$item['id'] ?>" class="group bg-white rounded-lg border border-gray-100 p-3 flex items-center gap-3 hover:shadow-md hover:border-brand-accent/30 transition">
          <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-brand-900 text-white flex items-center justify-center font-black text-sm">#<?= (int)$item['id'] ?></div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-0.5">
              <h3 class="font-bold text-brand-900 text-sm truncate"><?= htmlspecialchars(($item['nombre'] ?? '') . ' ' . ($item['apellidos'] ?? '')) ?></h3>
              <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold border <?= estadoBadge($est) ?>"><?= $est ?></span>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
              <span><?= htmlspecialchars($item['email'] ?? '') ?></span>
              <span class="text-gray-300">|</span>
              <span><?= htmlspecialchars($item['fecha'] ?? '') ?></span>
            </div>
          </div>
          <div class="text-right flex-shrink-0">
            <p class="text-lg font-black text-brand-accent">$<?= number_format($item['total'] ?? 0, 0, ',', '.') ?></p>
          </div>
          <div class="hidden sm:flex w-8 h-8 rounded-full bg-gray-100 items-center justify-center text-gray-400 group-hover:bg-brand-accent group-hover:text-white transition flex-shrink-0">
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </div>
        </a>
      <?php } ?>
    </div>

    <?php if ($paginas > 1) { ?>
      <div class="flex justify-center gap-2 mt-5">
        <?php if ($pagina > 1) { ?>
          <a href="?page=<?= $pagina - 1 ?>&estado=<?= urlencode($estadoFiltro) ?>&cliente=<?= urlencode($cliente) ?>&email=<?= urlencode($email) ?>&fecha=<?= urlencode($fecha) ?>&min=<?= urlencode($minTotal) ?>&max=<?= urlencode($maxTotal) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition text-sm">Anterior</a>
        <?php } ?>
        <?php for ($i = 1; $i <= $paginas; $i++) { ?>
          <a href="?page=<?= $i ?>&estado=<?= urlencode($estadoFiltro) ?>&cliente=<?= urlencode($cliente) ?>&email=<?= urlencode($email) ?>&fecha=<?= urlencode($fecha) ?>&min=<?= urlencode($minTotal) ?>&max=<?= urlencode($maxTotal) ?>" class="px-3 py-1.5 rounded-lg border text-sm transition <?= $i == $pagina ? 'bg-brand-accent text-white border-brand-accent' : 'border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent' ?>"><?= $i ?></a>
        <?php } ?>
        <?php if ($pagina < $paginas) { ?>
          <a href="?page=<?= $pagina + 1 ?>&estado=<?= urlencode($estadoFiltro) ?>&cliente=<?= urlencode($cliente) ?>&email=<?= urlencode($email) ?>&fecha=<?= urlencode($fecha) ?>&min=<?= urlencode($minTotal) ?>&max=<?= urlencode($maxTotal) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition text-sm">Siguiente</a>
        <?php } ?>
      </div>
    <?php } ?>

  <?php } else { ?>
    <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
      <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-shopping-bag text-2xl text-gray-400"></i>
      </div>
      <h3 class="text-lg font-bold text-brand-900 mb-1">No hay pedidos</h3>
      <p class="text-gray-500 text-sm">No se encontraron pedidos con los filtros seleccionados.</p>
    </div>
  <?php } ?>
</main>
</div>
</body>
</html>
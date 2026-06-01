<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';
$pedido = new capsweb\pedidos;
$info_pedido = $pedido->mostrar();

$estadosDisponibles = ['Pendiente','Enviado','Completado','Cancelado'];
$estadoFiltro = $_GET['estado'] ?? '';
$vista = $_GET['vista'] ?? 'lista'; // lista | calendario

// Calendario data
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
$primerDiaMes = mktime(0, 0, 0, $mes, 1, $anio);
$diasEnMes = date('t', $primerDiaMes);
$diaSemanaInicio = date('w', $primerDiaMes); // 0=domingo
$meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

// Agrupar pedidos por día para el calendario
$pedidosPorDia = [];
foreach ($info_pedido as $p) {
  $dia = (int)date('d', strtotime($p['fecha'] ?? 'now'));
  $mesPedido = (int)date('m', strtotime($p['fecha'] ?? 'now'));
  $anioPedido = (int)date('Y', strtotime($p['fecha'] ?? 'now'));
  if ($mesPedido === $mes && $anioPedido === $anio) {
    $pedidosPorDia[$dia][] = $p;
  }
}

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

  <!-- Vista Tabs -->
  <div class="flex items-center justify-between mb-4">
    <div class="flex gap-2">
      <a href="?vista=lista" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= $vista === 'lista' ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>"><i class="fa-solid fa-list mr-1"></i>Lista</a>
      <a href="?vista=calendario" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= $vista === 'calendario' ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>"><i class="fa-regular fa-calendar mr-1"></i>Calendario</a>
      <a href="exportar.php" class="px-3 py-1.5 rounded-lg text-sm font-semibold border bg-green-600 text-white border-green-600 hover:bg-green-700 transition"><i class="fa-solid fa-file-excel mr-1"></i>Exportar</a>
    </div>
  </div>

  <?php if ($vista === 'lista') { ?>
  <!-- Estado Tabs -->
  <div class="flex flex-wrap gap-2 mb-4">
    <a href="?vista=lista" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= empty($estadoFiltro) ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>">Todos</a>
    <?php foreach ($estadosDisponibles as $e) { ?>
      <a href="?vista=lista&estado=<?= urlencode($e) ?>" class="px-3 py-1.5 rounded-lg text-sm font-semibold border transition <?= $estadoFiltro === $e ? 'bg-brand-900 text-white border-brand-900' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-accent hover:text-brand-accent' ?>"><?= $e ?></a>
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
  <?php } // end lista ?>

  <?php if ($vista === 'calendario') { ?>
  <!-- Calendario -->
  <div class="bg-white rounded-xl border border-gray-100 p-6">
    <!-- Header calendario -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-brand-900"><?= $meses[$mes-1] ?> <?= $anio ?></h2>
      <div class="flex gap-2">
        <?php
        $prevMes = $mes === 1 ? 12 : $mes - 1;
        $prevAnio = $mes === 1 ? $anio - 1 : $anio;
        $nextMes = $mes === 12 ? 1 : $mes + 1;
        $nextAnio = $mes === 12 ? $anio + 1 : $anio;
        ?>
        <a href="?vista=calendario&mes=<?= $prevMes ?>&anio=<?= $prevAnio ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition"><i class="fa-solid fa-chevron-left"></i></a>
        <a href="?vista=calendario&mes=<?= date('m') ?>&anio=<?= date('Y') ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition text-sm">Hoy</a>
        <a href="?vista=calendario&mes=<?= $nextMes ?>&anio=<?= $nextAnio ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition"><i class="fa-solid fa-chevron-right"></i></a>
      </div>
    </div>

    <!-- Grid calendario -->
    <div class="grid grid-cols-7 gap-1 mb-2">
      <?php $diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; ?>
      <?php foreach ($diasSemana as $dia) { ?>
        <div class="text-center text-xs font-bold text-gray-400 py-2"><?= $dia ?></div>
      <?php } ?>
    </div>
    <div class="grid grid-cols-7 gap-1">
      <?php
      // Espacios vacíos antes del primer día
      for ($i = 0; $i < $diaSemanaInicio; $i++) {
        echo '<div class="min-h-[80px]"></div>';
      }
      // Días del mes
      $hoy = (int)date('d');
      $mesActual = (int)date('m');
      $anioActual = (int)date('Y');
      for ($dia = 1; $dia <= $diasEnMes; $dia++) {
        $esHoy = ($dia === $hoy && $mes === $mesActual && $anio === $anioActual);
        $pedidosDia = $pedidosPorDia[$dia] ?? [];
        $totalDia = count($pedidosDia);
        $ventasDia = array_sum(array_column($pedidosDia, 'total'));
      ?>
        <div class="min-h-[80px] border border-gray-100 rounded-lg p-2 <?= $esHoy ? 'bg-brand-50 border-brand-200' : 'hover:bg-gray-50' ?>">
          <div class="flex justify-between items-start mb-1">
            <span class="text-sm font-bold <?= $esHoy ? 'text-brand-accent' : 'text-gray-700' ?>"><?= $dia ?></span>
            <?php if ($totalDia > 0) { ?>
              <span class="text-[10px] bg-brand-accent text-white px-1.5 py-0.5 rounded font-bold"><?= $totalDia ?></span>
            <?php } ?>
          </div>
          <?php if ($totalDia > 0) { ?>
            <div class="text-[10px] text-gray-500 truncate">$<?= number_format($ventasDia, 0, ',', '.') ?></div>
            <div class="mt-1 space-y-0.5">
              <?php foreach (array_slice($pedidosDia, 0, 2) as $p) { ?>
                <a href="ver.php?id=<?= (int)$p['id'] ?>" class="block text-[9px] truncate text-gray-600 hover:text-brand-accent" title="#<?= $p['id'] ?> - <?= htmlspecialchars(($p['nombre'] ?? '') . ' ' . ($p['apellidos'] ?? '')) ?>">
                  #<?= $p['id'] ?> <?= htmlspecialchars($p['nombre'] ?? '') ?>
                </a>
              <?php } ?>
              <?php if ($totalDia > 2) { ?>
                <div class="text-[9px] text-gray-400">+<?= $totalDia - 2 ?> más</div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
  <?php } // end calendario ?>

</main>
</div>
</body>
</html>
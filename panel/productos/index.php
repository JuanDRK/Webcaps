<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }

require_once __DIR__ . '/../../vendor/autoload.php';
$producto = new capsweb\Productos;
$info_producto = $producto->mostrar();

// Dashboard data
$pedido = new capsweb\pedidos;
$totalPedidos = count($pedido->mostrar());
$totalProductos = count($info_producto);
$productosBajoStock = count(array_filter($info_producto, fn($p) => (int)$p['stock'] < 5));

// Ventas por mes (últimos 6 meses)
$ventasPorMes = [];
$pedidosTodos = $pedido->mostrar();
foreach ($pedidosTodos as $p) {
    $mes = date('Y-m', strtotime($p['fecha']));
    $ventasPorMes[$mes] = ($ventasPorMes[$mes] ?? 0) + (float)$p['total'];
}
ksort($ventasPorMes);
$ventasLabels = array_keys(array_slice($ventasPorMes, -6, 6, true));
$ventasValues = array_values(array_slice($ventasPorMes, -6, 6, true));

$referencia = $_GET['referencia'] ?? '';
$categoria  = $_GET['categoria'] ?? '';
$precio     = $_GET['precio'] ?? '';
$stock      = $_GET['stock'] ?? '';
$tab        = $_GET['tab'] ?? 'dashboard';

$resultadosFiltrados = [];
foreach ($info_producto as $item) {
  if ((empty($referencia) || stripos($item['referencia'], $referencia) !== false)
    && (empty($categoria) || $item['categoria'] == $categoria)
    && (empty($precio) || $item['precio'] == $precio)
    && (empty($stock) || $item['stock'] == $stock)) {
    $resultadosFiltrados[] = $item;
  }
}

$elementosPorPagina = 10;
$totalElementos = count($resultadosFiltrados);
$totalPaginas = ceil($totalElementos / $elementosPorPagina);
$paginaActual = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$inicio = ($paginaActual - 1) * $elementosPorPagina;
$listaProductosPaginada = array_slice($resultadosFiltrados, $inicio, $elementosPorPagina);

$categoriaModel = new capsweb\Categorias;
$info_categoria = $categoriaModel->mostrar();

$csrfToken = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin — Productos | CAPS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>tailwind.config={theme:{extend:{colors:{brand:{900:'#0f0f0f',accent:'#e11d48'}}}}}</script>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">
<?php $activePage = 'productos'; include __DIR__ . '/../templates/sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-8 overflow-y-auto">

<!-- Tabs -->
<div class="mb-6 border-b border-gray-200">
  <div class="flex gap-6">
    <a href="?tab=dashboard" id="tab-dashboard" class="pb-3 text-sm font-bold border-b-2 transition <?= $tab === 'dashboard' ? 'text-brand-accent border-brand-accent' : 'text-gray-500 hover:text-brand-900 border-transparent' ?>">Dashboard</a>
    <a href="?tab=productos" id="tab-productos" class="pb-3 text-sm font-bold border-b-2 transition <?= $tab === 'productos' ? 'text-brand-accent border-brand-accent' : 'text-gray-500 hover:text-brand-900 border-transparent' ?>">Productos</a>
  </div>
</div>

<!-- Dashboard Section -->
<div id="section-dashboard" <?= $tab === 'dashboard' ? '' : 'class="hidden"' ?>>
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xl"><i class="fa-solid fa-box"></i></div>
      <div><p class="text-xs text-gray-500 uppercase">Productos</p><p class="text-2xl font-black text-brand-900"><?= $totalProductos ?></p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl"><i class="fa-solid fa-shopping-bag"></i></div>
      <div><p class="text-xs text-gray-500 uppercase">Pedidos</p><p class="text-2xl font-black text-brand-900"><?= $totalPedidos ?></p></div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-5 flex items-center gap-4">
      <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
      <div><p class="text-xs text-gray-500 uppercase">Bajo stock</p><p class="text-2xl font-black text-brand-900"><?= $productosBajoStock ?></p></div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-gray-100 p-6">
    <h3 class="font-bold text-brand-900 mb-4">Ventas por mes</h3>
    <canvas id="ventasChart" height="80"></canvas>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('ventasChart');
if (ctx) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode($ventasLabels) ?>,
      datasets: [{
        label: 'Ventas (COP)',
        data: <?= json_encode($ventasValues) ?>,
        borderColor: '#e11d48',
        backgroundColor: 'rgba(225,29,72,0.1)',
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#e11d48',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: { beginAtZero: true, ticks: { callback: v => '$' + Number(v).toLocaleString('es-CO') } }
      }
    }
  });
}

/* Tabs se manejan via PHP con ?tab= para que paginación/filtros funcionen */
function switchTab(tab) { window.location.href = '?tab=' + tab; }
</script>

<!-- Productos Section -->
<div id="section-productos" <?= $tab === 'productos' ? '' : 'class="hidden"' ?>>

  <!-- Toolbar -->
  <div class="flex flex-wrap gap-3 mb-6">
    <button onclick="openModal('registerModal')" class="bg-brand-accent text-white px-4 py-2 rounded-lg font-semibold hover:bg-rose-700 transition text-sm">
      <i class="fa-solid fa-plus mr-1"></i>Nuevo producto
    </button>
    <button onclick="openModal('registerCategoriaModal')" class="bg-gray-800 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-700 transition text-sm">
      <i class="fa-solid fa-tag mr-1"></i>Añadir categoría
    </button>
    <button onclick="openModal('cambiarNewsModal')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition text-sm">
      <i class="fa-solid fa-bullhorn mr-1"></i>Cambiar noticia
    </button>
  </div>

  <!-- Filters -->
  <form class="flex flex-wrap gap-2 mb-6" method="GET" action="">
    <input type="hidden" name="tab" value="productos">
    <input type="text" name="referencia" value="<?= htmlspecialchars($referencia) ?>" placeholder="Buscar referencia" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent w-48">
    <select name="categoria" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent bg-white">
      <option value="">Todas las categorías</option>
      <?php foreach ($info_categoria as $cat) { ?>
        <option value="<?= htmlspecialchars($cat['categoria']) ?>" <?= $categoria === $cat['categoria'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['categoria']) ?></option>
      <?php } ?>
    </select>
    <input type="number" name="precio" value="<?= htmlspecialchars($precio) ?>" placeholder="Precio" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent w-28">
    <input type="number" name="stock" value="<?= htmlspecialchars($stock) ?>" placeholder="Stock" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent w-28">
    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition">Filtrar</button>
    <a href="?tab=productos" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">Limpiar</a>
  </form>

  <!-- Table -->
  <div class="bg-white rounded-xl border border-gray-100 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">#</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Producto</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Tallas</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Cat</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Precio</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Stock</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Vend</th>
          <th class="text-left px-3 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Acc</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($listaProductosPaginada) > 0) { foreach ($listaProductosPaginada as $item) { ?>
          <tr class="border-b border-gray-50 hover:bg-gray-50 transition group">
            <td class="px-3 py-2 text-xs text-gray-400"><?= $item['id'] ?></td>
            <td class="px-3 py-2">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                  <?php $fotoPath = __DIR__ . '/../../public/upload/' . $item['foto']; if (file_exists($fotoPath)) { ?>
                    <img src="../../upload/<?= htmlspecialchars($item['foto']) ?>" class="w-full h-full object-cover">
                  <?php } else { ?><div class="w-full h-full flex items-center justify-center text-gray-400 text-[9px]">SIN FOTO</div><?php } ?>
                </div>
                <span class="font-semibold text-brand-900 text-sm"><?= htmlspecialchars($item['referencia']) ?></span>
              </div>
            </td>
            <td class="px-3 py-2 text-xs text-gray-500"><?= htmlspecialchars($item['tallas']) ?></td>
            <td class="px-3 py-2"><span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide"><?= htmlspecialchars($item['categoria']) ?></span></td>
            <td class="px-3 py-2 text-sm font-bold text-brand-accent">$<?= number_format($item['precio'], 0, ',', '.') ?></td>
            <td class="px-3 py-2 text-sm text-gray-700"><?= (int)$item['stock'] ?></td>
            <td class="px-3 py-2"><span class="bg-green-50 text-green-700 px-2 py-0.5 rounded text-[10px] font-bold"><?= (int)$item['cantidad_vendida'] ?></span></td>
            <td class="px-3 py-2">
              <div class="flex gap-1">
                <button onclick="openModal('editModal-<?= $item['id'] ?>')" class="w-7 h-7 rounded bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition" title="Editar"><i class="fa-regular fa-pen-to-square text-xs"></i></button>
                <button onclick="openModal('deleteModal-<?= $item['id'] ?>')" class="w-7 h-7 rounded bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition" title="Eliminar"><i class="fa-solid fa-trash text-xs"></i></button>
              </div>
            </td>
          </tr>
        <?php }} else { ?>
          <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400 text-sm">NO HAY REGISTROS</td></tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($totalPaginas > 1) { ?>
    <div class="flex justify-center gap-2 mt-6">
      <?php if ($paginaActual > 1) { ?>
        <a href="?tab=productos&page=<?= $paginaActual - 1 ?>&referencia=<?= urlencode($referencia) ?>&categoria=<?= urlencode($categoria) ?>&precio=<?= urlencode($precio) ?>&stock=<?= urlencode($stock) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition">Anterior</a>
      <?php } ?>
      <?php for ($i = 1; $i <= $totalPaginas; $i++) { ?>
        <a href="?tab=productos&page=<?= $i ?>&referencia=<?= urlencode($referencia) ?>&categoria=<?= urlencode($categoria) ?>&precio=<?= urlencode($precio) ?>&stock=<?= urlencode($stock) ?>" class="px-3 py-2 rounded-lg border <?= $i == $paginaActual ? 'bg-brand-accent text-white border-brand-accent' : 'border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent' ?> transition"><?= $i ?></a>
      <?php } ?>
      <?php if ($paginaActual < $totalPaginas) { ?>
        <a href="?tab=productos&page=<?= $paginaActual + 1 ?>&referencia=<?= urlencode($referencia) ?>&categoria=<?= urlencode($categoria) ?>&precio=<?= urlencode($precio) ?>&stock=<?= urlencode($stock) ?>" class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:border-brand-accent hover:text-brand-accent transition">Siguiente</a>
      <?php } ?>
    </div>
  <?php } ?>
</div>

<!-- Edit Modals -->
<?php foreach ($listaProductosPaginada as $info) { ?>
<div id="editModal-<?= $info['id'] ?>" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeModal('editModal-<?= $info['id'] ?>')">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-black text-brand-900">Editar Producto</h3>
        <button onclick="closeModal('editModal-<?= $info['id'] ?>')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
      </div>
      <form method="POST" action="../acciones.php" enctype="multipart/form-data" class="space-y-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= (int)$info['id'] ?>">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Referencia</label><input value="<?= htmlspecialchars($info['referencia']) ?>" type="text" name="referencia" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad</label><input value="<?= (int)$info['stock'] ?>" type="number" name="stock" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Precio</label><input value="<?= (int)$info['precio'] ?>" type="number" name="precio" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Categoría</label>
            <select name="categoria_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent">
              <option value="">SELECCIONE</option>
              <?php foreach ($info_categoria as $cat) { ?>
                <option value="<?= (int)$cat['id'] ?>" <?= $info['categoria_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['categoria']) ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Tallas</label><input value="<?= htmlspecialchars($info['tallas']) ?>" type="text" name="tallasNuevas" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Foto</label><input type="file" name="foto" class="w-full text-sm"><input type="hidden" name="foto_temp" value="<?= htmlspecialchars($info['foto']) ?>"></div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('editModal-<?= $info['id'] ?>')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm hover:bg-gray-50">Cancelar</button>
          <input type="submit" name="accion" value="Actualizar" class="px-4 py-2 rounded-lg bg-brand-accent text-white text-sm font-bold hover:bg-rose-700 cursor-pointer">
        </div>
      </form>
    </div>
  </div>
</div>
<?php } ?>

<!-- Delete Modals -->
<?php foreach ($listaProductosPaginada as $item) { ?>
<div id="deleteModal-<?= $item['id'] ?>" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeModal('deleteModal-<?= $item['id'] ?>')">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
      <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fa-solid fa-trash text-red-500 text-xl"></i></div>
      <h3 class="text-lg font-black text-brand-900 mb-2">¿Eliminar producto?</h3>
      <p class="text-gray-500 text-sm mb-6">Esta acción no se puede deshacer.</p>
      <form action="../acciones.php" method="GET" class="flex gap-2 justify-center">
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
        <button type="button" onclick="closeModal('deleteModal-<?= $item['id'] ?>')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm">Cancelar</button>
        <input type="submit" name="accion" value="Eliminar" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-bold hover:bg-red-700 cursor-pointer">
      </form>
    </div>
  </div>
</div>
<?php } ?>

<!-- Register Modal -->
<div id="registerModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeModal('registerModal')">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-black text-brand-900">Nuevo producto</h3>
        <button onclick="closeModal('registerModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
      </div>
      <form method="POST" action="../acciones.php" enctype="multipart/form-data" class="space-y-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Referencia</label><input type="text" name="referencia" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Cantidad</label><input type="number" name="stock" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Precio</label><input type="number" name="precio" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
          <div><label class="block text-xs font-semibold text-gray-600 mb-1">Categoría</label>
            <select name="categoria_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent">
              <option value="">SELECCIONE</option>
              <?php foreach ($info_categoria as $cat) { ?>
                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['categoria']) ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Tallas</label>
          <div class="flex flex-wrap gap-2">
            <?php $tallasOpc = ['S','M','L','XL','XXL','28','30','32','34','36']; foreach ($tallasOpc as $t) { ?>
              <label class="cursor-pointer"><input type="checkbox" name="tallas[]" value="<?= $t ?>" class="peer hidden"><span class="inline-block px-3 py-1 rounded-lg border border-gray-200 text-xs font-semibold text-gray-600 peer-checked:bg-brand-900 peer-checked:text-white peer-checked:border-brand-900 transition"><?= $t ?></span></label>
            <?php } ?>
          </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Foto</label><input type="file" name="foto[]" required multiple class="w-full text-sm"></div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('registerModal')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm hover:bg-gray-50">Cancelar</button>
          <input type="submit" name="accion" value="Registrar" class="px-4 py-2 rounded-lg bg-brand-accent text-white text-sm font-bold hover:bg-rose-700 cursor-pointer">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Change News Modal -->
<div id="cambiarNewsModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeModal('cambiarNewsModal')">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-black text-brand-900">Cambiar Noticia</h3>
        <button onclick="closeModal('cambiarNewsModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
      </div>
      <form method="POST" action="../acciones.php" class="space-y-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Nuevas Noticias (separar por coma)</label><input type="text" name="lema" required placeholder="Ej: Envío gratis, Nuevos drops..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent"></div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('cambiarNewsModal')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm">Cancelar</button>
          <input type="submit" name="accion" value="Cambiar" class="px-4 py-2 rounded-lg bg-brand-accent text-white text-sm font-bold hover:bg-rose-700 cursor-pointer">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Category Modal -->
<div id="registerCategoriaModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeModal('registerCategoriaModal')">
  <div class="absolute inset-0 bg-black/50"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-black text-brand-900">Añadir Categoría</h3>
        <button onclick="closeModal('registerCategoriaModal')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
      </div>
      <form method="POST" action="../acciones.php" class="space-y-3">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">Categoría</label><input type="text" name="categoria" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent uppercase"></div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('registerCategoriaModal')" class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 text-sm">Cancelar</button>
          <input type="submit" name="accion" value="Añadir" class="px-4 py-2 rounded-lg bg-brand-accent text-white text-sm font-bold hover:bg-rose-700 cursor-pointer">
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
</script>
</div>
</main>
</div>
</body>
</html>
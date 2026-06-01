<?php
session_start();
if (empty($_SESSION['id'])) { header('Location: ../../acceso.php'); exit; }
require_once __DIR__ . '/../../vendor/autoload.php';

$cupones = new capsweb\Cupones();

// Registrar nuevo cupón
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $params = [
        'codigo' => strtoupper(trim($_POST['codigo'])),
        'tipo' => $_POST['tipo'],
        'valor' => (float)$_POST['valor'],
        'minimo_compra' => (float)($_POST['minimo_compra'] ?? 0),
        'usos_maximos' => !empty($_POST['usos_maximos']) ? (int)$_POST['usos_maximos'] : null,
        'fecha_inicio' => $_POST['fecha_inicio'] ?: null,
        'fecha_fin' => $_POST['fecha_fin'] ?: null
    ];
    $cupones->registrar($params);
    header('Location: index.php');
    exit;
}

// Cambiar estado
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $activo = (int)$_GET['activo'];
    $cupones->cambiarEstado($id, !$activo);
    header('Location: index.php');
    exit;
}

$lista = $cupones->mostrar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin — Cupones | CAPS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script>tailwind.config={theme:{extend:{colors:{brand:{900:'#0f0f0f',accent:'#e11d48'}}}}}</script>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="flex min-h-screen">
<?php $activePage = 'cupones'; include __DIR__ . '/../templates/sidebar.php'; ?>
<main class="flex-1 p-6 lg:p-8 overflow-y-auto">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-black text-brand-900">Cupones</h1>
      <p class="text-gray-500 text-sm mt-1"><?= count($lista) ?> cupón(es)</p>
    </div>
    <button onclick="openModal()" class="bg-brand-accent text-white px-4 py-2 rounded-lg font-semibold hover:bg-rose-700 transition">
      <i class="fa-solid fa-plus mr-1"></i>Nuevo cupón
    </button>
  </div>

  <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Código</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Tipo</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Valor</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Mínimo</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Usos</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Válido</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Estado</th>
          <th class="text-left px-4 py-3 font-semibold text-gray-600">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($lista as $c) { ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($c['codigo']) ?></td>
            <td class="px-4 py-3"><?= $c['tipo'] === 'porcentaje' ? 'Porcentaje' : 'Fijo' ?></td>
            <td class="px-4 py-3"><?= $c['tipo'] === 'porcentaje' ? $c['valor'] . '%' : '$' . number_format($c['valor'], 0, ',', '.') ?></td>
            <td class="px-4 py-3">$<?= number_format($c['minimo_compra'], 0, ',', '.') ?></td>
            <td class="px-4 py-3"><?= $c['usos_actuales'] ?>/<?= $c['usos_maximos'] ?? '∞' ?></td>
            <td class="px-4 py-3 text-xs text-gray-500">
              <?php if ($c['fecha_inicio'] || $c['fecha_fin']) { ?>
                <?= $c['fecha_inicio'] ? date('d/m/Y', strtotime($c['fecha_inicio'])) : '-' ?> 
                - 
                <?= $c['fecha_fin'] ? date('d/m/Y', strtotime($c['fecha_fin'])) : '-' ?>
              <?php } else { ?>Sin límite<?php } ?>
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded text-xs font-bold <?= $c['activo'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                <?= $c['activo'] ? 'Activo' : 'Inactivo' ?>
              </span>
            </td>
            <td class="px-4 py-3">
              <a href="?toggle=<?= $c['id'] ?>&activo=<?= $c['activo'] ?>" class="text-sm <?= $c['activo'] ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' ?>">
                <?= $c['activo'] ? 'Desactivar' : 'Activar' ?>
              </a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</main>
</div>

<!-- Modal -->
<div id="modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
      <h3 class="text-xl font-bold text-brand-900 mb-4">Nuevo cupón</h3>
      <form method="POST">
        <input type="hidden" name="accion" value="crear">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Código</label>
            <input type="text" name="codigo" required class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent uppercase">
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>
              <select name="tipo" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
                <option value="porcentaje">Porcentaje</option>
                <option value="fijo">Monto fijo</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Valor</label>
              <input type="number" name="valor" required min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Compra mínima</label>
              <input type="number" name="minimo_compra" value="0" min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Usos máximos</label>
              <input type="number" name="usos_maximos" placeholder="∞" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Inicio</label>
              <input type="date" name="fecha_inicio" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Fin</label>
              <input type="date" name="fecha_fin" class="w-full border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-brand-accent">
            </div>
          </div>
        </div>
        <div class="flex gap-3 mt-6">
          <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Cancelar</button>
          <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-brand-accent text-white font-semibold hover:bg-rose-700 transition">Crear</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openModal() { document.getElementById('modal').classList.remove('hidden'); }
function closeModal() { document.getElementById('modal').classList.add('hidden'); }
</script>
</body>
</html>

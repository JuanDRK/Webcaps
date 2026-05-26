<?php
session_start();
include(__DIR__ . '/../templates/cabecera.php');
require __DIR__ . '/funcionesCarrito.php';
require __DIR__ . '/../vendor/autoload.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = (int)$_GET['id'];
  $producto = new capsweb\Productos;
  $resultado = $producto->mostrarPorId($id);
  if (!$resultado) { header('Location: ../index.php'); exit; }

  $cantidad = isset($_GET['cantidad']) && is_numeric($_GET['cantidad']) ? max(1, (int)$_GET['cantidad']) : 1;
  $talla = isset($_GET['talla']) ? trim($_GET['talla']) : null;

  if (isset($_SESSION['carrito'])) {
    if (array_key_exists($id, $_SESSION['carrito'])) {
      // Si ya existe, sumamos la cantidad solicitada y actualizamos talla
      $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
      if ($talla) $_SESSION['carrito'][$id]['talla'] = $talla;
    } else {
      agregarProducto($resultado, $id, $cantidad, $talla);
    }
  } else {
    agregarProducto($resultado, $id, $cantidad, $talla);
  }
}

$carritoVacio = empty($_SESSION['carrito']);
?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
      <h1 class="text-3xl font-black text-brand-900">Tu Carrito</h1>
      <p class="text-gray-500 text-sm mt-1"><?= $carritoVacio ? 'Tu carrito está vacío' : count($_SESSION['carrito']) . ' producto(s)' ?></p>
    </div>
    <div class="flex gap-3">
      <a href="../index.php" class="inline-flex items-center px-4 py-2 border-2 border-gray-200 rounded-lg text-sm font-semibold text-gray-600 hover:border-brand-accent hover:text-brand-accent transition">
        <i class="fa-solid fa-arrow-left mr-2"></i>Seguir comprando
      </a>
      <?php if (!$carritoVacio) { ?>
        <button type="button" onclick="openClearModal()" class="inline-flex items-center px-4 py-2 border-2 border-red-200 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 transition">
          <i class="fa-solid fa-trash mr-2"></i>Vaciar
        </button>
      <?php } ?>
    </div>
  </div>

  <?php if (isset($_GET['id']) && is_numeric($_GET['id'])) { ?>
<script>
  window.addEventListener('DOMContentLoaded', () => {
    if (typeof showToast === 'function') showToast('Producto agregado al carrito', 'success');
  });
</script>
<?php } ?>

<?php if (!$carritoVacio) { ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Items -->
      <div class="lg:col-span-2 space-y-4">
        <?php foreach ($_SESSION['carrito'] as $value) {
          $idProducto = $value['id'];
          $producto = new capsweb\Productos;
          $tallasProducto = $producto->mostrarTallas($idProducto);
          $fotoUrl  = '/upload/' . htmlspecialchars($value['foto'], ENT_QUOTES, 'UTF-8');
          $fotoPath = 'public/upload/' . htmlspecialchars($value['foto'], ENT_QUOTES, 'UTF-8');
        ?>
          <div class="bg-white rounded-xl border border-gray-100 p-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center hover:shadow-md transition">
            <!-- Foto -->
            <div class="w-20 h-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
              <?php if (file_exists($fotoPath)) { ?>
                <img src="<?= $fotoUrl ?>" class="w-full h-full object-cover" alt="">
              <?php } else { ?>
                <img src="../assets/images/sinfoto.jpg" class="w-full h-full object-cover" alt="">
              <?php } ?>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-brand-900 truncate"><?= htmlspecialchars($value['referencia'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-xs text-gray-500 uppercase"><?= htmlspecialchars($value['categoria'], ENT_QUOTES, 'UTF-8') ?></p>
              <p class="text-brand-accent font-black mt-1">$<?= number_format($value['precio'], 0, ',', '.') ?> <span class="text-gray-400 text-xs font-normal">COP</span></p>
            </div>

            <!-- Form actualizar -->
            <form action="actualizar_carrito.php" method="post" class="flex flex-wrap items-center gap-3">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="id" value="<?= (int)$value['id'] ?>">

              <!-- Cantidad -->
              <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                <input type="number" name="cantidad" value="<?= (int)$value['cantidad'] ?>" min="1" class="w-14 py-2 text-center text-sm font-bold outline-none border-none">
              </div>

              <!-- Talla -->
              <select name="tallaTomada" class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-brand-accent">
                <?php foreach ($tallasProducto as $talla) {
                  $tallaEsc = htmlspecialchars($talla, ENT_QUOTES, 'UTF-8');
                  $selected = ($talla == $value['talla']) ? 'selected' : '';
                  echo '<option value="' . $tallaEsc . '" ' . $selected . '>' . $tallaEsc . '</option>';
                } ?>
              </select>

              <button type="submit" class="w-9 h-9 rounded-lg bg-brand-900 text-white flex items-center justify-center hover:bg-brand-accent transition" title="Actualizar">
                <i class="fa-solid fa-rotate-right text-xs"></i>
              </button>
            </form>

            <!-- Subtotal + Eliminar -->
            <div class="text-right flex flex-col items-end gap-2">
              <span class="font-black text-brand-900">$<?= number_format($value['precio'] * $value['cantidad'], 0, ',', '.') ?></span>
              <button type="button" onclick="openDeleteModal(<?= (int)$value['id'] ?>)" class="text-red-500 hover:text-red-700 text-xs transition">
                <i class="fa-solid fa-trash mr-1"></i>Eliminar
              </button>
            </div>
          </div>
        <?php } ?>
      </div>

      <!-- Resumen -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl border border-gray-100 p-6 sticky top-24">
          <h2 class="font-bold text-lg text-brand-900 mb-4">Resumen del pedido</h2>
          <div class="space-y-2 text-sm">
            <div class="flex justify-between text-gray-600">
              <span>Subtotal</span>
              <span>$<?= number_format(calcularTotal(), 0, ',', '.') ?> COP</span>
            </div>
            <div class="flex justify-between text-gray-600">
              <span>Envío</span>
              <span class="text-green-600 font-medium">Gratis</span>
            </div>
          </div>
          <div class="border-t border-gray-100 my-4"></div>
          <div class="flex justify-between items-center mb-6">
            <span class="font-bold text-brand-900">Total</span>
            <span class="text-2xl font-black text-brand-accent">$<?= number_format(calcularTotal(), 0, ',', '.') ?> <span class="text-sm text-gray-400 font-normal">COP</span></span>
          </div>

          <a href="../finalizar.php" class="block w-full bg-brand-accent text-white text-center py-3 rounded-xl font-bold hover:bg-rose-700 transition shadow-lg shadow-rose-900/20">
            <i class="fa-solid fa-credit-card mr-2"></i>Finalizar compra
          </a>
          <p class="text-center text-xs text-gray-400 mt-3"><i class="fa-solid fa-lock mr-1"></i>Pago seguro contra entrega</p>
        </div>
      </div>
    </div>

  <?php } else { ?>
    <!-- Carrito vacío -->
    <div class="text-center py-20">
      <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fa-solid fa-cart-shopping text-4xl text-gray-300"></i>
      </div>
      <h2 class="text-2xl font-bold text-brand-900 mb-2">Tu carrito está vacío</h2>
      <p class="text-gray-500 mb-6">Explora nuestra colección y encuentra algo que te encante.</p>
      <a href="../index.php#productos" class="inline-block bg-brand-accent text-white px-8 py-3 rounded-xl font-bold hover:bg-rose-700 transition">
        <i class="fa-solid fa-bag-shopping mr-2"></i>Ver productos
      </a>
    </div>
  <?php } ?>
</section>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeDeleteModal()">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
      <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-trash text-xl"></i>
      </div>
      <h3 class="text-lg font-black text-brand-900 mb-2">¿Eliminar este producto?</h3>
      <p class="text-gray-500 text-sm mb-6">Esta acción no se puede deshacer.</p>
      <div class="flex gap-3 justify-center">
        <button onclick="closeDeleteModal()" class="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition">Cancelar</button>
        <a id="confirm-delete-btn" href="#" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-bold hover:bg-red-600 transition">Eliminar</a>
      </div>
    </div>
  </div>
</div>

<!-- Clear Cart Modal -->
<div id="clearModal" class="fixed inset-0 z-50 hidden" onclick="if(event.target===this)closeClearModal()">
  <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
      <div class="w-14 h-14 rounded-full bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-4">
        <i class="fa-solid fa-trash-can text-xl"></i>
      </div>
      <h3 class="text-lg font-black text-brand-900 mb-2">¿Vaciar todo el carrito?</h3>
      <p class="text-gray-500 text-sm mb-6">Se eliminarán todos los productos. Esta acción no se puede deshacer.</p>
      <div class="flex gap-3 justify-center">
        <button onclick="closeClearModal()" class="px-5 py-2.5 rounded-lg border border-gray-200 text-gray-600 font-semibold hover:bg-gray-50 transition">Cancelar</button>
        <form method="POST" action="eliminar_carrito.php" class="inline">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
          <button type="submit" name="accion" value="eliminartodo" class="px-5 py-2.5 rounded-lg bg-red-500 text-white font-bold hover:bg-red-600 transition">Vaciar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function openDeleteModal(id) {
  document.getElementById('confirm-delete-btn').href = 'eliminar_carrito.php?id=' + id;
  document.getElementById('deleteModal').classList.remove('hidden');
}
function closeDeleteModal() {
  document.getElementById('deleteModal').classList.add('hidden');
}
function openClearModal() {
  document.getElementById('clearModal').classList.remove('hidden');
}
function closeClearModal() {
  document.getElementById('clearModal').classList.add('hidden');
}
</script>

<?php include(__DIR__ . '/../templates/footer.php'); ?>
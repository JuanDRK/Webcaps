<?php
include('templates/cabecera.php');
require 'vendor/autoload.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $id = (int)$_GET['id'];
  $producto = new capsweb\Productos;
  $prendaEscogida = $producto->mostrarPorId($id);
  if (!$prendaEscogida) { header('Location: index.php'); exit; }
} else { header('Location: index.php'); exit; }

$tallasDisponibles = $producto->mostrarTallas($id);
$fotoPath = 'public/upload/' . $prendaEscogida['foto'];
$fotoUrl  = 'upload/' . $prendaEscogida['foto'];
$fotoExiste = file_exists($fotoPath);

// Productos relacionados (misma categoria, excluyendo el actual)
$todos = $producto->mostrar();
$relacionados = array_filter($todos, fn($p) => $p['categoria_id'] == $prendaEscogida['categoria_id'] && $p['id'] != $id);
$relacionados = array_slice($relacionados, 0, 4);
?>

<!-- BREADCRUMB -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
  <nav class="flex text-sm text-gray-500">
    <a href="index.php" class="hover:text-brand-accent transition">Inicio</a>
    <span class="mx-2">/</span>
    <span class="text-brand-900 font-medium"><?= htmlspecialchars($prendaEscogida['referencia'], ENT_QUOTES, 'UTF-8') ?></span>
  </nav>
</div>

<!-- PRODUCT DETAIL -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
    <!-- IMAGE -->
    <div class="relative">
      <div class="bg-gray-100 rounded-2xl overflow-hidden aspect-[3/4]">
        <?php if ($fotoExiste) { ?>
          <img src="<?= $fotoUrl ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($prendaEscogida['referencia'], ENT_QUOTES, 'UTF-8') ?>">
        <?php } else { ?>
          <img src="assets/images/sinfoto.jpg" class="w-full h-full object-cover" alt="sin foto">
        <?php } ?>
      </div>
    </div>

    <!-- INFO -->
    <div class="flex flex-col justify-center">
      <p class="text-brand-accent font-semibold text-sm uppercase tracking-wider mb-2"><?= htmlspecialchars($prendaEscogida['categoria'] ?? 'CAPS', ENT_QUOTES, 'UTF-8') ?></p>
      <h1 class="text-3xl md:text-4xl font-black text-brand-900 mb-4"><?= htmlspecialchars($prendaEscogida['referencia'], ENT_QUOTES, 'UTF-8') ?></h1>
      <div class="flex items-baseline gap-3 mb-6">
        <span class="text-3xl font-black text-brand-accent">$<?= number_format($prendaEscogida['precio'], 0, ',', '.') ?></span>
        <span class="text-gray-400">COP</span>
      </div>

      <div class="w-16 h-1 bg-brand-accent rounded-full mb-6"></div>

      <form id="product-form" action="carro/index.php" method="GET" class="space-y-6">
        <input type="hidden" name="id" value="<?= (int)$prendaEscogida['id'] ?>">

        <!-- Tallas -->
        <div>
          <h3 class="font-bold text-brand-900 mb-3">Selecciona tu talla</h3>
          <div class="flex flex-wrap gap-2" id="talla-selector">
            <?php foreach ($tallasDisponibles as $idx => $talla) { ?>
              <label class="talla-btn cursor-pointer">
                <input type="radio" name="talla" value="<?= htmlspecialchars($talla, ENT_QUOTES, 'UTF-8') ?>" <?= $idx === 0 ? 'checked' : '' ?> class="peer hidden">
                <span class="px-5 py-2 border-2 border-gray-200 rounded-lg text-sm font-bold text-gray-700 peer-checked:border-brand-accent peer-checked:bg-brand-accent peer-checked:text-white hover:border-brand-accent transition select-none inline-block"><?= htmlspecialchars($talla, ENT_QUOTES, 'UTF-8') ?></span>
              </label>
            <?php } ?>
          </div>
        </div>

        <!-- Cantidad -->
        <div>
          <h3 class="font-bold text-brand-900 mb-3">Cantidad</h3>
          <div class="inline-flex items-center border border-gray-200 rounded-xl overflow-hidden">
            <button type="button" onclick="stepQty(-1)" class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition text-lg font-bold">-</button>
            <input type="number" id="qty-input" name="cantidad" value="1" min="1" max="<?= (int)$prendaEscogida['stock'] ?>" class="w-16 h-12 text-center font-bold text-brand-900 border-x border-gray-200 outline-none">
            <button type="button" onclick="stepQty(1)" class="w-12 h-12 flex items-center justify-center text-gray-500 hover:bg-gray-100 transition text-lg font-bold">+</button>
          </div>
          <span class="text-sm text-gray-500 ml-3"><?= (int)$prendaEscogida['stock'] ?> disponibles</span>
        </div>

        <!-- CTA -->
        <button type="submit" class="w-full bg-brand-accent text-white text-center py-4 rounded-xl font-bold text-lg hover:bg-rose-700 transition shadow-lg shadow-rose-900/20">
          <i class="fa-solid fa-cart-plus mr-2"></i>Agregar al carrito
        </button>
        <a href="index.php#productos" class="block text-center text-gray-500 hover:text-brand-accent transition text-sm">
          <i class="fa-solid fa-arrow-left mr-1"></i>Seguir comprando
        </a>
      </form>
    </div>
  </div>
</section>

<!-- RELATED PRODUCTS -->
<?php if (count($relacionados) > 0) { ?>
<section class="bg-gray-50 py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-black text-brand-900 mb-8 text-center">También te puede gustar</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php foreach ($relacionados as $r) {
        $rfotoPath = 'public/upload/' . $r['foto'];
        $rfotoUrl  = 'upload/' . $r['foto'];
      ?>
        <a href="infoPrenda.php?id=<?= (int)$r['id'] ?>" class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
          <div class="relative overflow-hidden aspect-[3/4]">
            <?php if (file_exists($rfotoPath)) { ?>
              <img src="<?= $rfotoUrl ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="<?= htmlspecialchars($r['referencia'], ENT_QUOTES, 'UTF-8') ?>">
            <?php } else { ?>
              <img src="assets/images/sinfoto.jpg" class="w-full h-full object-cover" alt="sin foto">
            <?php } ?>
          </div>
          <div class="p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1"><?= htmlspecialchars($r['categoria'] ?? 'CAPS', ENT_QUOTES, 'UTF-8') ?></p>
            <h3 class="font-bold text-brand-900 text-lg mb-2 truncate"><?= htmlspecialchars($r['referencia'], ENT_QUOTES, 'UTF-8') ?></h3>
            <div class="flex items-center justify-between">
              <span class="text-brand-accent font-black text-xl">$<?= number_format($r['precio'], 0, ',', '.') ?></span>
              <span class="text-gray-400 text-xs">COP</span>
            </div>
          </div>
        </a>
      <?php } ?>
    </div>
  </div>
</section>
<?php } ?>

<script>
function stepQty(delta) {
  const input = document.getElementById('qty-input');
  let v = parseInt(input.value) + delta;
  const min = parseInt(input.min);
  const max = parseInt(input.max);
  if (v < min) v = min;
  if (v > max) v = max;
  input.value = v;
}

// Agregar al carrito vía AJAX
document.getElementById('product-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = this;
  const data = new URLSearchParams(new FormData(form));
  const ref = '<?= addslashes($prendaEscogida['referencia']) ?>';
  try {
    await fetch('/carro/index.php?' + data.toString());
    if (typeof window.refreshMiniCart === 'function') window.refreshMiniCart();
    if (typeof showToast === 'function') showToast('Agregado: ' + ref, 'success');
  } catch (err) {
    form.submit();
  }
});
</script>

<?php include('templates/footer.php'); ?>